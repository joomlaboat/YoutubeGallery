<?php
/**
 * YoutubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

class YGAPI_VideoSource_YoutubeUserUploads
{
    public static function extractYouTubeUserVideosID($youtubeURL): string
    {
        //https://www.youtube.com/c/albasiranet/featured
        //return '/c/albasiranet/videos';
        //link example: https://www.youtube.com/user/ivankomlev
        //or
        ////link example: https://www.youtube.com/user/acharnesnews/favorites
        $matches = explode('/', $youtubeURL);

        if (count($matches) > 5) {
            $userid = $matches[4];
            $pair = explode('?', $userid);
            return $pair[0];
        }
        return '';
    }

    public static function extractYouTubeUserFeaturedID($youtubeURL): string
    {
        //link example: https://www.youtube.com/user/ivankomlev
        ////link example: https://www.youtube.com/user/acharnesnews/favorites
        $matches = explode('/', $youtubeURL);

        if (count($matches) > 3) {
            $userid = $matches[4];
            $pair = explode('?', $userid);
            return $pair[0];
        }

        return '';
    }

    public static function getVideoIDList($youtubeURL, $vsn, $show_what, bool $active_key, $youtube_data_api_key = ''): array
    {
        $videoList = array();

        $base_url = 'https://www.googleapis.com/youtube/v3';
        $userid = YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserID($youtubeURL);

        $videoListItem = YouTubeGalleryAPIMisc::getBlankArray();
        $videoListItem['es_videosource'] = $vsn;
        $videoListItem['es_link'] = $youtubeURL;
        $videoListItem['es_isvideo'] = 0;
        $videoListItem['es_videoid'] = $userid;

        if ($userid == '') {
            $videoListItem['es_status'] = -1;
            $videoListItem['es_error'] = 'User ID not set.';
            $videoList[] = $videoListItem;
            return $videoList;
        }

        //if ($userid == 'Whataboutit')
        //$userid = 'wQnaBGUeq2qQHHD04dcfi4bSBwY';

        $part = 'contentDetails';

        if ($youtube_data_api_key == '')
            return [];

        $key = $youtube_data_api_key;

        $url = $base_url . '/channels?forUsername=' . $userid . '&key=' . $key . '&part=' . $part;
        $HTMLCode = Helper::getURLData($url);

        if ($HTMLCode == '') {
            $videoListItem['es_status'] = -1;
            $videoListItem['es_error'] = $url . 'Server response is empty.';
            $videoList[] = $videoListItem;
            return $videoList;
        }

        $j = json_decode($HTMLCode);
        if (!$j) {
            $videoListItem['es_status'] = -1;
            $videoListItem['es_error'] = 'Server response is not JSON.';
            $videoListItem['es_rawdata'] = null;//$HTMLCode;
            $videoList[] = $videoListItem;
            return $videoList;
        }

        if (isset($j->error)) {
            $videoListItem['es_status'] = -1;
            $videoListItem['es_error'] = $j->error->message;
            $videoListItem['es_rawdata'] = null;//$HTMLCode;
            $videoList[] = $videoListItem;
            return $videoList;
        }

        if (!isset($j->items)) {
            $videoListItem['es_status'] = -1;
            $videoListItem['es_error'] = 'Items object not found.';
            $videoListItem['es_rawdata'] = null;//$HTMLCode;
            $videoList[] = $videoListItem;
            return $videoList;
        }

        $items = $j->items;

        $playListId = '';
        if (isset($items[0]) and isset($items[0]->contentDetails) and isset($items[0]->contentDetails->relatedPlaylists)) {
            if ($show_what == 'uploads' and isset($items[0]->contentDetails->relatedPlaylists->uploads)) {
                $playListId = $items[0]->contentDetails->relatedPlaylists->uploads;
            } elseif ($show_what == 'favorites' and isset($items[0]->contentDetails->relatedPlaylists->favorites))
                $playListId = $items[0]->contentDetails->relatedPlaylists->favorites;
            else {
                $videoListItem['es_status'] = -1;
                $videoListItem['es_error'] = 'Show what not set.';
                $videoListItem['es_rawdata'] = null;//$HTMLCode;
                $videoList[] = $videoListItem;
                return $videoList;
            }

            if ($playListId == '') {
                $videoListItem['es_status'] = -1;
                $videoListItem['es_error'] = 'User not found or no files uploaded';
                $videoListItem['es_rawdata'] = null;//$HTMLCode;
                $videoList[] = $videoListItem;
                return $videoList;
            }
        }

        $part = 'id,snippet';
        $dataLink = $base_url . '/playlistItems?playlistId=' . $playListId . '&part=' . $part . '&key=' . $key;
        $videoListItem['es_datalink'] = $dataLink;

        $newList = YGAPI_VideoSource_YoutubePlaylist::getPlaylistVideos($dataLink, $videoListItem, $active_key);
        $videoList[] = $videoListItem;
        return array_merge($videoList, $newList);
    }

    public static function extractYouTubeUserID($youtubeURL): string
    {
        //link example: https://www.youtube.com/user/ivankomlev
        //or
        ////link example: https://www.youtube.com/user/acharnesnews/favorites
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
