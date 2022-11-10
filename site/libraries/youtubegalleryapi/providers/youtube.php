<?php
/**
 * YoutubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class YGAPI_VideoSource_YouTube
{
    public static function extractYouTubeID($youtubeURL)
    {
        if (!(strpos($youtubeURL, '/embed/') === false)) {
            //Convert Embed links to Address bar version
            $youtubeURL = str_replace('www.youtube.com/embed/', 'youtu.be/', $youtubeURL);
            $youtubeURL = str_replace('youtube.com/embed/', 'youtu.be/', $youtubeURL);
        }

        if (!(strpos($youtubeURL, '://youtu.be') === false) or !(strpos($youtubeURL, '://www.youtu.be') === false)) {
            //youtu.be
            $list = explode('/', $youtubeURL);
            if (isset($list[3]))
                return $list[3];
            else
                return '';
        } else {
            //youtube.com
            $arr = YouTubeGalleryAPIMisc::parse_query($youtubeURL);
            if (isset($arr['v']))
                return $arr['v'];
            else
                return '';
        }

    }

    public static function copyVideoData($j, &$blankArray)
    {
        try {
            if (isset($j->error)) {

                if (isset($j->error->errors)) {

                    $e = $j->error->errors[0];

                    if (isset($e->code))
                        $blankArray['es_status'] = $e->code;
                    else
                        $blankArray['es_status'] = 5;//5 - Youtube API error

                    $blankArray['es_error'] = $e->message;
                    $blankArray['es_rawdata'] = null;//json_encode($j);

                    return false;
                }

            }

            $items = $j->items;

            if (!is_array($items)) {
                $blankArray['es_status'] = -1;
                $blankArray['es_error'] = 'YoutubeGalleryAPI: Unexpected Youtube response.';
                $blankArray['es_rawdata'] = null;//$htmlcode;
                return false;
            }

            if (count($items) > 0) {
                $item = $items[0];
                if ($item->kind == 'youtube#video' and $item->id == $blankArray['es_videoid'])
                    return YGAPI_VideoSource_YouTube::copyVideoDataItem($item, $blankArray);
            } else {
                $blankArray['es_status'] = 17;//17 - Private Video or not found
                $blankArray['es_error'] = 'Video not found or private';
                $blankArray['es_rawdata'] = null;//json_encode($j);
            }


        } catch (Exception $e) {
            $blankArray['es_status'] = -2;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Error catched.';
            $blankArray['es_rawdata'] = null;//$htmlcode;
            return false;
        }
        return true;
    }

    public static function copyVideoDataItem($item, &$blankArray, $debug = false)
    {
        try {
            $snippet = $item->snippet;

            $videoid = '';

            if ($item->kind == 'youtube#video')
                $videoid = $item->id;
            elseif ($item->kind == 'youtube#playlistItem') {
                $s = $snippet->resourceId;
                if ($s->kind == 'youtube#video') {
                    $videoid = $s->videoId;
                    $blankArray['es_videoid'] = $s->videoId;
                    $blankArray['es_link'] = 'https://www.youtube.com/watch?v=' . $s->videoId;//for playlists
                }
            } elseif ($item->kind == 'youtube#searchResult') {
                $s = $item->id;
                if ($s->kind == 'youtube#video') {
                    $videoid = $s->videoId;
                    $blankArray['es_videoid'] = $s->videoId;
                    $blankArray['es_link'] = 'https://www.youtube.com/watch?v=' . $s->videoId;//for playlists
                }

            } else {
                $blankArray['es_videoid'] = '';
                return false;
            }

            if ($videoid == $blankArray['es_videoid']) {
                $blankArray['es_title'] = $snippet->title;
                $blankArray['es_description'] = $snippet->description;
                $blankArray['es_publisheddate'] = $snippet->publishedAt;

                $t = $snippet->thumbnails;

                $images = array();

                if (isset($t->default))
                    $images[] = $t->default->url . (isset($t->default->width) ? ',' . $t->default->width : '') . (isset($t->default->height) ? ',' . $t->default->height : '');

                if (isset($t->medium) and isset($t->medium->width) and isset($t->medium->height))
                    $images[] = $t->medium->url . ',' . $t->medium->width . ',' . $t->medium->height;

                if (isset($t->high) and isset($t->high->width) and isset($t->high->height))
                    $images[] = $t->high->url . ',' . $t->high->width . ',' . $t->high->height;

                if (isset($t->standard) and isset($t->standard->width) and isset($t->standard->height))
                    $images[] = $t->standard->url . ',' . $t->standard->width . ',' . $t->standard->height;

                if (isset($t->maxres) and isset($t->maxres->width) and isset($t->maxres->height))
                    $images[] = $t->maxres->url . ',' . $t->maxres->width . ',' . $t->maxres->height;

                $blankArray['es_imageurl'] = implode(';', $images);

                $blankArray['es_channeltitle'] = $snippet->channelTitle;

                if (isset($item->contentDetails)) {
                    $d = $item->contentDetails->duration;
                    $blankArray['es_duration'] = YGAPI_VideoSource_YouTube::covtime_apiv3($d);
                    $blankArray['es_lastupdate'] = date('Y-m-d H:i:s');//Individual video updated
                }

                if (isset($item->statistics)) {
                    $blankArray['es_statisticsfavoritecount'] = $item->statistics->favoriteCount;
                    if (isset($item->statistics->viewCount))
                        $blankArray['es_statisticsviewcount'] = $item->statistics->viewCount;

                    if (isset($item->statistics->likeCount))
                        $blankArray['es_likes'] = $item->statistics->likeCount;

                    if (isset($item->statistics->dislikeCount))
                        $blankArray['es_dislikes'] = $item->statistics->dislikeCount;

                    if (isset($item->statistics->commentCount))
                        $blankArray['es_commentcount'] = $item->statistics->commentCount;
                    else
                        $blankArray['es_commentcount'] = 0;
                }

                if (isset($snippet->tags))
                    $blankArray['es_keywords'] = $snippet->tags;

                if (isset($snippet->channelTitle))
                    $blankArray['es_channeltitle'] = $snippet->channelTitle;

                if (isset($item->recordingDetails) and isset($item->recordingDetails->location)) {
                    $location = $item->recordingDetails->location;
                    $blankArray['es_latitude'] = $location->latitude;
                    $blankArray['es_longitude'] = $location->longitude;
                    $blankArray['es_altitude'] = $location->altitude;
                }

                return true;
            }
        } catch (Exception $e) {
            $blankArray['es_status'] = -2;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Error catched.';
            $blankArray['es_rawdata'] = null;//$htmlcode;
            return false;
        }
        return true;
    }

    protected static function covtime_apiv3($youtube_time)
    {
        $start = new DateTime('@0'); // Unix epoch
        $start->add(new DateInterval($youtube_time));

        $d = $start->format('H:i:s');

        $parts = explode(':', $d);
        $hours = intval($parts[0]);
        $minutes = intval($parts[1]);
        $seconds = intval($parts[2]);

        return $seconds + $minutes * 60 + $hours * 3600;
    }


    protected static function convert_duration($youtube_time)
    {
        $parts = null;
        preg_match_all('/(\d+)/', $youtube_time, $parts);

        $hours = floor($parts[0][0] / 60);
        $minutes = $parts[0][0] % 60;
        if (isset($parts[0][1]))
            $seconds = $parts[0][1];
        else
            $seconds = 0;

        return $seconds + $minutes * 60 + $hours * 3600;
    }

}
