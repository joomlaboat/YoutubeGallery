<?php
/**
 * YoutubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

class YGAPI_VideoSource_YoutubePlaylist
{
    public static function extractYoutubeChannelID($youtubeURL)
    {
        //https://www.youtube.com/channel/UCRND2QLhATXcgrTgFfsZHyg/videos
        $matches = explode('/', $youtubeURL);

        if (count($matches) > 4) {

            $channelid = $matches[4];
            $pair = explode('?', $channelid);
            return $pair[0];
        }
        return '';
    }

    public static function extractYouTubeSearchKeywords($youtubeURL)
    {
        //https://www.youtube.com/results?search_query=%22dogs+101%22&oq=%22dogs+101%22&gs_l=youtube.3..0l10.16119.16453.0.17975.2.2.0.0.0.0.330.649.3-2.2.0...0.0...1ac.1.GQ5tbo9Q0Cg
        $arr = YouTubeGalleryAPIMisc::parse_query($youtubeURL);

        $p = urldecode($arr['search_query']);
        if (!isset($p) or $p == '')
            return ''; //incorrect Link

        $keywords = str_replace('"', '', $p);
        $keywords = str_replace('\'', '', $p);
        $keywords = str_replace('&', '', $p);
        $keywords = str_replace('?', '', $p);
        $keywords = str_replace('%', '', $p);
        $keywords = str_replace('+', ' ', $keywords);
        $keywords = str_replace(' ', ',', $keywords);

        return $keywords;
    }

    public static function YoutubeLists($theLink, $vsn, $query, $listid, $active_key, $youtube_data_api_key = '')
    {
        $videoitems = array();

        $videolistitem = YouTubeGalleryAPIMisc::getBlankArray();
        $videolistitem['es_videosource'] = $vsn;
        $videolistitem['es_link'] = $theLink;
        $videolistitem['es_isvideo'] = 0;
        $videolistitem['es_videoid'] = $listid;
        $videolistitem['es_videoids'] = '-';

        require_once('youtube.php');

        $part = 'id,snippet';
        $base_url = 'https://www.googleapis.com/youtube/v3';

        if ($youtube_data_api_key == '')
            $key = YouTubeGalleryAPIMisc::APIKey_Youtube($active_key);
        else
            $key = $youtube_data_api_key;

        if ($key == '') {
            Factory::getApplication()->enqueueMessage('Youtube Data API key is required.', 'error');
            return $videoitems;
        }

        $datalink = $base_url . '/' . $query . '&part=' . $part . '&key=' . $key;
        $videolistitem['es_datalink'] = $datalink;


        $debug = false;
        if ($theLink == 'https://www.youtube.com/channel/UCLJN3NrnEb-PediSaOku9mg') {
            $debug = true;
            echo '$datalink=' . $datalink . '<br/>';
        }


        $newlist = YGAPI_VideoSource_YoutubePlaylist::getPlaylistVideos($datalink, $videolistitem, $active_key, $debug, $youtube_data_api_key);//$theLink=='https://www.youtube.com/channel/UCLJN3NrnEb-PediSaOku9mg');


        if ($theLink == 'https://www.youtube.com/channel/UCLJN3NrnEb-PediSaOku9mg') {
            print_r($newlist);
            die;
        }

        $videoitems[] = $videolistitem;
        $videoitems = array_merge($videoitems, $newlist);

        return $videoitems;
    }

    public static function getPlaylistVideos($datalink, &$videolistitem, $active_key, $debug = false, $youtube_data_api_key = '')
    {
        //If its on JoomlaBoat.com - limit the number of videos to 100
        $limitNumberOfVideos = false;
        if (strpos(JURI::root(false), 'joomlaboat.com'))
            $limitNumberOfVideos = true;

        $videolist = array();
        $url = $datalink;

        $videos_found = 0;
        $nextPageToken = '';
        $count = -1;
        $videos = array();

        while ($videos_found < $count or $count == -1) {
            if ($nextPageToken != '')
                $url = $datalink . '&pageToken=' . $nextPageToken;

            $htmlcode = Helper::getURLData($url);
            if ($debug)
                print_r($htmlcode);


            if ($htmlcode == '') {
                $videolistitem['es_status'] = -1;
                $videolistitem['es_error'] = 'Response is empty';
                $videolistitem['es_rawdata'] = null;//$htmlcode;
                return $videolist;
            }

            $j = json_decode($htmlcode);
            if (!$j) {
                $videolistitem['es_status'] = -1;
                $videolistitem['es_error'] = 'Response is not JSON';
                $videolistitem['es_rawdata'] = null;//$htmlcode;
                return $videolist;
            }

            if (isset($j->error)) {
                if (isset($j->error->errors)) {
                    $e = $j->error->errors[0];
                    $videolistitem['es_status'] = -2;
                    $videolistitem['es_error'] = $e->message;
                    $videolistitem['es_rawdata'] = null;//$htmlcode;
                    return $videolist;
                }
            }

            if (isset($j->nextPageToken))
                $nextPageToken = $j->nextPageToken;
            else
                $nextPageToken = '';

            $pageinfo = $j->pageInfo;
            if ($pageinfo->totalResults > $count)
                $count = $pageinfo->totalResults;

            $items = $j->items;
            if (count($items) == 0)
                break;//return $videolist;


            foreach ($items as $item) {
                $videoitem = YouTubeGalleryAPIMisc::getBlankArray();
                $videoitem['es_videosource'] = 'youtube';
                $videoitem['es_isvideo'] = 1;

                YGAPI_VideoSource_YouTube::copyVideoDataItem($item, $videoitem, $debug);

                if ($videoitem['es_videoid'] != '') {
                    $videolist[] = $videoitem;
                    $videos[] = $videoitem['es_videoid'];

                    //Update Channel title
                    if ($videolistitem['es_title'] == '')
                        $videolistitem['es_title'] = $videoitem['es_channeltitle'];

                    $videos_found++;
                }
            }

            if (!$active_key and $videos_found >= 5) //break if not paid
                break;

            if ($limitNumberOfVideos and $videos_found > 100) //break if more than 100
                break;
        }

        $videolistitem['es_videoids'] = ',' . implode(',', $videos) . ',';
        return $videolist;
    }

    public static function getVideoIDList($youtubeURL, &$playlistid, &$datalink)
    {
        $playlistid = YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($youtubeURL);
        $videolist = YGAPI_VideoSource_YoutubePlaylist::getPlaylistVideos($playlistid, $datalink);

        return $videolist;
    }

    public static function extractYouTubePlayListID($youtubeURL)
    {
        $arr = YouTubeGalleryAPIMisc::parse_query($youtubeURL);
        $p = $arr['list'];

        if (strlen($p) < 3)
            return '';

        return $p;
    }
}
