<?php
/**
 * YoutubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

class YGAPI_VideoSource_YoutubeUserUploads
{
    public static function extractYouTubeUserVideosID($youtubeURL)
    {
        //https://www.youtube.com/c/albasiranet/featured
        //return '/c/albasiranet/videos';
        //link example: http://www.youtube.com/user/designcompasscorp
        //or
        ////link example: http://www.youtube.com/user/acharnesnews/favorites
        $matches = explode('/', $youtubeURL);


        if (count($matches) > 5) {
            $userid = $matches[4];
            $pair = explode('?', $userid);
            return $pair[0];
        }

        return '';
    }

    public static function extractYouTubeUserFeaturedID($youtubeURL)
    {
        //link example: http://www.youtube.com/user/designcompasscorp
        ////link example: http://www.youtube.com/user/acharnesnews/favorites
        $matches = explode('/', $youtubeURL);

        if (count($matches) > 3) {
            $userid = $matches[4];
            $pair = explode('?', $userid);
            return $pair[0];
        }

        return '';
    }

    public static function getVideoIDList($youtubeURL, $vsn, $show_what, $active_key, $youtube_data_api_key = '')
    {
        $videolist = array();

        $base_url = 'https://www.googleapis.com/youtube/v3';
        $userid = YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserID($youtubeURL);

        $videolistitem = YouTubeGalleryAPIMisc::getBlankArray();
        $videolistitem['es_videosource'] = $vsn;
        $videolistitem['es_link'] = $youtubeURL;
        $videolistitem['es_isvideo'] = 0;
        $videolistitem['es_videoid'] = $userid;

        if ($userid == '') {
            $videolistitem['es_status'] = -1;
            $videolistitem['es_error'] = 'User ID not set.';
            $videolist[] = $videolistitem;
            return $videolist;
        }


        if ($userid == 'Whataboutit')
            $userid = 'wQnaBGUeq2qQHHD04dcfi4bSBwY';

        $part = 'contentDetails';

        if ($youtube_data_api_key == '')
            $key = YouTubeGalleryAPIMisc::APIKey_Youtube($active_key);
        else
            $key = $youtube_data_api_key;

        $url = $base_url . '/channels?forUsername=' . $userid . '&key=' . $key . '&part=' . $part;
        $htmlcode = Helper::getURLData($url);


        if ($htmlcode == '') {
            $videolistitem['es_status'] = -1;
            $videolistitem['es_error'] = $url . 'Server response is empty.';
            $videolist[] = $videolistitem;
            echo 'a';
            die;
            return $videolist;
        }

        $j = json_decode($htmlcode);
        if (!$j) {
            $videolistitem['es_status'] = -1;
            $videolistitem['es_error'] = 'Server response is not JSON.';
            $videolistitem['es_rawdata'] = null;//$htmlcode;
            $videolist[] = $videolistitem;
            return $videolist;
        }

        if (isset($j->error)) {
            $videolistitem['es_status'] = -1;
            $videolistitem['es_error'] = $j->error->message;
            $videolistitem['es_rawdata'] = null;//$htmlcode;
            $videolist[] = $videolistitem;
            return $videolist;
        }

        if (!isset($j->items)) {
            $videolistitem['es_status'] = -1;
            $videolistitem['es_error'] = 'Items object not found.';
            $videolistitem['es_rawdata'] = null;//$htmlcode;
            $videolist[] = $videolistitem;
            return $videolist;
        }
        
        $items = $j->items;

        $playlistid = '';
        if (isset($items[0]) and isset($items[0]->contentDetails) and isset($items[0]->contentDetails->relatedPlaylists)) {
            if ($show_what == 'uploads' and isset($items[0]->contentDetails->relatedPlaylists->uploads)) {
                $playlistid = $items[0]->contentDetails->relatedPlaylists->uploads;
            } elseif ($show_what == 'favorites' and isset($items[0]->contentDetails->relatedPlaylists->favorites))
                $playlistid = $items[0]->contentDetails->relatedPlaylists->favorites;
            else {
                $videolistitem['es_status'] = -1;
                $videolistitem['es_error'] = 'Show what not set.';
                $videolistitem['es_rawdata'] = null;//$htmlcode;
                $videolist[] = $videolistitem;
                return $videolist;
            }

            if ($playlistid == '') {
                $videolistitem['es_status'] = -1;
                $videolistitem['es_error'] = 'User not found or no files uploaded';
                $videolistitem['es_rawdata'] = null;//$htmlcode;
                $videolist[] = $videolistitem;
                return $videolist;
            }
        }

        $part = 'id,snippet';
        $datalink = $base_url . '/playlistItems?playlistId=' . $playlistid . '&part=' . $part . '&key=' . $key;
        $videolistitem['es_datalink'] = $datalink;

        $newlist = YGAPI_VideoSource_YoutubePlaylist::getPlaylistVideos($datalink, $videolistitem, $active_key, false, $youtube_data_api_key);
        $videolist[] = $videolistitem;
        $videolist = array_merge($videolist, $newlist);

        if ($youtubeURL == 'https://www.youtube.com/channel/UCLJN3NrnEb-PediSaOku9mg') {
            echo 'check why: id : ' . $userid;
            die;
        }

        return $videolist;
    }

    public static function extractYouTubeUserID($youtubeURL)
    {
        //link example: http://www.youtube.com/user/designcompasscorp
        //or
        ////link example: http://www.youtube.com/user/acharnesnews/favorites
        //https://www.youtube.com/c/Whataboutit/videos

        $matches = explode('/', $youtubeURL);

        if (count($matches) > 3) {
            $userid = $matches[4];
            $pair = explode('?', $userid);
            return $pair[0];
        }

        return '';
    }
}
