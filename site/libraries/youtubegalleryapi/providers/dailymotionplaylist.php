<?php
/**
 * YouTubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

class YGAPI_VideoSource_DailymotionPlaylist
{
    public static function getVideoIDList($URL, &$playlistid, &$datalink)
    {
        //https://api.dailymotion.com/playlist/xy4h8/videos

        $videolist = array();

        $playlistid = YGAPI_VideoSource_DailymotionPlaylist::extractDailymotionPlayListID($URL);
        if ($playlistid == '')
            return $videolist; //playlist id not found


        $apiurl = 'https://api.dailymotion.com/playlist/' . $playlistid . '/videos';
        $datalink = $apiurl;

        $htmlcode = Helper::getURLData($apiurl);

        if ($htmlcode == '')
            return $videolist;


        if (!isset($htmlcode) or $htmlcode == '' or $htmlcode[0] != '{') {
            return 'Cannot load data, no connection or access denied';
        }
        $streamData = json_decode($htmlcode);


        foreach ($streamData->list as $entry) {
            $videolist[] = 'https://www.dailymotion.com/playlist/' . $entry->id;
            //https://www.dailymotion.com/playlist/x1crql_BigCatRescue_funny-action-big-cats/1#video=x986zk

        }//foreach ($xml->entry as $entry)

        return $videolist;
    }

    public static function extractDailymotionPlayListID($URL)
    {
        //https://www.dailymotion.com/playlist/x1crql_BigCatRescue_funny-action-big-cats/1#video=x7k9rx
        $p = explode('/', $URL);

        if (count($p) < 4)
            return '';

        $p2 = explode('_', $p[4]);
        if (count($p2) < 1)
            return ''; //incorrect playlist ID

        return $p2[0]; //return without everything after _
    }
}
