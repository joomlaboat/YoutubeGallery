<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

class YouTubeGalleryData
{
    public static function formVideoList(object $videoListRow, array $rawList, bool $force = false): array
    {
        $gallery_list = array();
        $ordering = 0;

        foreach ($rawList as $b) {
            $b = str_replace("\n", '', $b);
            $b = trim(str_replace("\r", '', $b));
            $listItem = JoomlaBasicMisc::csv_explode(',', $b, '"', false);

            $theLink = trim($listItem[0]);
            if ($theLink != '') {
                $item = array();
                if (isset($listItem[1]))
                    $item['es_customtitle'] = $listItem[1];

                if (isset($listItem[2]))
                    $item['es_customdescription'] = $listItem[2];

                if (isset($listItem[3]))
                    $item['es_customimageurl'] = $listItem[3];

                if (isset($listItem[4]))
                    $item['es_specialparams'] = $listItem[4];

                if (isset($listItem[5]))
                    $item['es_startsecond'] = $listItem[5];

                if (isset($listItem[6]))
                    $item['es_endsecond'] = $listItem[6];

                if (isset($listItem[7]))
                    $item['es_watchgroup'] = $listItem[7];

                YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI($theLink, $gallery_list, $videoListRow, $force);
            }
        }
        return $gallery_list;
    }

    public static function queryJoomlaBoatYoutubeGalleryAPI(string $theLink, array &$gallery_list, object $videoListRow, bool $force = false): bool
    {
        $active_key = true;

        $updatePeriod = 60 * 24 * ($videoListRow->es_updateperiod) * 60;
        $PlaylistLastUpdate = YouTubeGalleryDB::Playlist_LastUpdate($theLink);
        $diff = strtotime(date('Y-m-d H:i:s')) - strtotime($PlaylistLastUpdate);

        $force = ($diff > $updatePeriod or $force);
        $youtubeDataAPIKey = YouTubeGalleryDB::getSettingValue('youtubedataapi_key');

        //Check if YouTubeGallery API installed
        $file = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR
            . 'libraries' . DIRECTORY_SEPARATOR . 'youtubegalleryapi' . DIRECTORY_SEPARATOR . 'misc.php';

        if (file_exists($file) and $youtubeDataAPIKey != '') {
            require_once($file);

            $y = new YouTubeGalleryAPIMisc;
            $isNew = 0;
            $results = $y->checkLink($active_key, $theLink, $isNew, $force, $videoListRow->id, $youtubeDataAPIKey);

            foreach ($results as $result)
                $gallery_list[] = $result;

            return true;
        }

        $msg = JText::_('COM_YOUTUBEGALLERY_YOUTUBE_API_REGISTER_PROJECT')
            . ' <a href="https://console.developers.google.com/" target="_blank">link</a> '
            . JText::_('COM_YOUTUBEGALLERY_YOUTUBE_API_GET_THE_KEY');

        Factory::getApplication()->enqueueMessage($msg, 'error');
        return false;

        /*
                $item = array();
                if (!function_exists('curl_init') and !function_exists('file_get_contents')) {
                    $item['es_error'] = 'Enable php functions: curl_init or file_get_contents.';
                    $item['es_status'] = -1;

                    $gallery_list[] = YouTubeGalleryData::parse_SingleVideo($item);
                    return false;
                }

                if (function_exists('phpversion')) {
                    if (phpversion() < 5) {
                        $item['es_error'] = 'Update to PHP 5+';
                        $item['es_status'] = -1;
                        $gallery_list[] = YouTubeGalleryData::parse_SingleVideo($item);
                        return false;
                    }
                }

                //try
                //{
                $htmlCode = YouTubeGalleryData::queryTheAPIServer($theLink, '', $force);

                $j_ = json_decode($htmlCode);

                if (!$j_) {
                    $item['es_error'] = 'Connection Error';
                    $item['es_status'] = -1;

                    $gallery_list[] = YouTubeGalleryData::parse_SingleVideo($item);
                    return false;
                }

                $j = (array)$j_;

                if (isset($j['es_error'])) {
                    $item['es_error'] = $j['es_error'];
                    $item['es_status'] = -1;

                    $gallery_list[] = YouTubeGalleryData::parse_SingleVideo($item);
                    return false;
                }

                foreach ($j as $item) {
                    $original_item['es_ordering'] = $ordering;
                    $gallery_list[] = YouTubeGalleryData::parse_SingleVideo((array)$item, $original_item);
                    $ordering++;
                }
                */
        /*
        }
        catch(Exception $e)
        {
            $item['es_error']='Cannot get youtube video data.';
            $item['es_status']=-1;

            $gallery_list[]=YouTubeGalleryData::parse_SingleVideo($item);
            return false;
        }
        */
        return true;
    }

    public static function updateSingleVideo(array $listItem, &$videoListRow)
    {
        $active_key = true;
        $videoListId = $videoListRow->id;

        if ($listItem['es_lastupdate'] != '' and $listItem['es_lastupdate'] != '0000-00-00 00:00:00' and ($listItem['es_isvideo'] == 1 and $listItem['es_duration'] != 0))
            return $listItem; //no need to update. But this should count the update period. In future version

        $theLink = trim($listItem['es_link']);
        if ($theLink == '')
            return $listItem;

        $item = array();//where to save

        //Check if YouTubeGallery API installed
        $file = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'youtubegalleryapi' . DIRECTORY_SEPARATOR . 'misc.php';
        if (file_exists($file)) {
            require_once($file);

            $y = new YouTubeGalleryAPIMisc;
            $isNew = 0;
            $results = $y->checkLink($active_key, $theLink, $isNew, true, $videoListRow->id);

            if (count($results) == 1) {
                if ($results[0]['es_title'] == '')
                    return $listItem;

                return $results[0];
            }
        } else {
            YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($theLink, $item, $listItem, true);//force the update
        }

        if (!isset($item['status']) or (int)$item['status'] == 0) {
            $parent_id = null;

            YouTubeGalleryDB::updateDBSingleItem($item, $videoListId, $parent_id);

            if ($listItem['es_customtitle'])
                $item['es_title'] = $listItem['es_customtitle'];

            if ($listItem['es_customdescription'])
                $item['es_description'] = $listItem['es_customdescription'];
        }
        return $listItem;
    }

    protected static function queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($theLink, &$item, &$original_item, $force = false): bool
    {
        if (!function_exists('curl_init') and !function_exists('file_get_contents')) {
            $es_item = array('es_error' => 'Enable php functions: curl_init or file_get_contents.', 'es_status' => -1);
            $item = YouTubeGalleryData::parse_SingleVideo($es_item);
            return false;
        }

        if (function_exists('phpversion')) {
            if (phpversion() < 5) {
                $es_item = array('es_error' => 'Update to PHP 5+', 'es_status' => -1);
                $item = YouTubeGalleryData::parse_SingleVideo($es_item);
                return false;
            }
        }

        try {
            $htmlCode = YouTubeGalleryData::queryTheAPIServer($theLink, '', $force);

            $j = json_decode($htmlCode);

            if (!$j) {
                $es_item = array('es_error' => 'Connection Error', 'es_status' => -1);
                $item = YouTubeGalleryData::parse_SingleVideo($es_item);
                return false;
            }

            if (isset($j['es_error'])) {
                $es_item = array('es_error' => $j['es_error'], 'es_status' => -1);
                $item = YouTubeGalleryData::parse_SingleVideo($es_item);
                return false;
            }

            if (count($j) == 0) {
                $es_item = array('es_error' => 'Cannot get youtube video data. Video not found.', 'es_status' => -1);
                $item = YouTubeGalleryData::parse_SingleVideo($es_item);
                return false;
            }

            $new_es_item = $j[0];
            $item = YouTubeGalleryData::parse_SingleVideo((array)$new_es_item, $original_item);
        } catch (Exception $e) {
            $es_item = array('es_error' => 'Cannot get youtube video data.', 'es_status' => -1);
            $item = YouTubeGalleryData::parse_SingleVideo($es_item);
            return false;
        }
        return true;
    }

    public static function parse_SingleVideo($item, $original_item = array()): array
    {
        //[channel_totaluploadviews] => 0 not used

        $blankArray = array(
            'id' => 0,
            'es_videoid' => '',
            'es_videolist' => 0,
            'es_parentid' => null,
            'es_videosource' => '',
            'es_alias' => '',
            'es_imageurl' => '',
            'es_isvideo' => 1,

            'es_customimageurl' => (array_key_exists('es_customimageurl', $original_item) ? $original_item['es_customimageurl'] : ''),
            'es_customtitle' => (array_key_exists('es_customtitle', $original_item) ? $original_item['es_customtitle'] : ''),
            'es_customdescription' => (array_key_exists('es_customdescription', $original_item) ? $original_item['es_customdescription'] : ''),
            'es_specialparams' => (array_key_exists('es_specialparams', $original_item) ? $original_item['es_specialparams'] : ''),
            'es_lastupdate' => (array_key_exists('es_lastupdate', $original_item) and $original_item['es_lastupdate'] != '0000-00-00 00:00:00' ? $original_item['es_lastupdate'] : ''),
            'es_link' => (array_key_exists('es_link', $original_item) ? $original_item['es_link'] : ''),
            'es_startsecond' => (array_key_exists('es_startsecond', $original_item) ? $original_item['es_startsecond'] : ''),
            'es_endsecond' => (array_key_exists('es_endsecond', $original_item) ? $original_item['es_endsecond'] : ''),
            'es_title' => '',
            'es_description' => '',
            'es_publisheddate' => '',
            'es_duration' => 0,
            'es_ratingaverage' => 0,
            'es_ratingmax' => 0,
            'es_ratingmin' => 0,
            'es_ratingnumberofraters' => 0,
            'es_statisticsfavoritecount' => 0,
            'es_statisticsviewcount' => 0,
            'es_keywords' => '',
            'es_likes' => 0,
            'es_dislikes' => '',
            'es_commentcount' => '',
            'es_channelusername' => '',
            'es_channeltitle' => '',
            'es_channelsubscribers' => 0,
            'es_channelsubscribed' => 0,
            'es_channellocation' => '',
            'es_channelcommentcount' => 0,
            'es_channelviewcount' => 0,
            'es_channelvideocount' => 0,
            'es_channeldescription' => '',
            'es_status' => 0,
            'es_error' => '',
            'es_rawdata' => null,
            'es_datalink' => '',
            'es_latitude' => null,
            'es_longitude' => null,
            'es_altitude' => null,
            'es_ordering' => (array_key_exists('es_ordering', $original_item) ? $original_item['es_ordering'] : 0)
        );

        if (isset($item['es_error']) and $item['es_error'] != '') {
            $blankArray['status'] = $item['es_status'];
            $blankArray['error'] = $item['es_error'];
            return $blankArray;
        }

        if (isset($item['es_parentid']))
            $blankArray['es_parentid'] = $item['es_parentid'];

        $blankArray['es_videosource'] = $item['es_videosource'];
        $blankArray['es_videoid'] = $item['es_videoid'];
        $blankArray['es_link'] = $item['es_link'];
        $blankArray['es_isvideo'] = $item['es_isvideo'];
        $blankArray['es_lastupdate'] = $item['es_lastupdate'];

        $blankArray['es_title'] = $item['es_title'];
        $blankArray['es_description'] = $item['es_description'];
        $blankArray['es_publisheddate'] = $item['es_publisheddate'];
        $blankArray['es_imageurl'] = $item['es_imageurl'];
        $blankArray['es_channel_title'] = $item['es_channeltitle'];
        $blankArray['es_duration'] = $item['es_duration'];

        $blankArray['es_likes'] = $item['es_likes'];
        $blankArray['es_dislikes'] = $item['es_dislikes'];
        $blankArray['es_commentcount'] = $item['es_commentcount'];
        $blankArray['es_keywords'] = $item['es_keywords'];

        $blankArray['es_ratingaverage'] = $item['es_ratingaverage'];
        $blankArray['es_ratingmax'] = $item['es_ratingmax'];
        $blankArray['es_ratingmin'] = $item['es_ratingmin'];
        $blankArray['es_ratingnumberofraters'] = $item['es_ratingnumberofraters'];

        $blankArray['es_statisticsfavoritecount'] = $item['es_statisticsfavoritecount'];
        $blankArray['es_statisticsviewcount'] = $item['es_statisticsviewcount'];

        $blankArray['es_channelusername'] = $item['es_channelusername'];
        $blankArray['es_channeltitle'] = $item['es_channeltitle'];
        $blankArray['es_channelsubscribers'] = $item['es_channelsubscribers'];
        $blankArray['es_channelsubscribed'] = $item['es_channelsubscribed'];
        $blankArray['es_channellocation'] = $item['es_channellocation'];
        $blankArray['es_channelcommentcount'] = $item['es_channelcommentcount'];
        $blankArray['es_channelviewcount'] = $item['es_channelviewcount'];
        $blankArray['es_channelvideocount'] = $item['es_channelvideocount'];
        $blankArray['es_channeldescription'] = $item['es_channeldescription'];

        $blankArray['es_alias'] = YouTubeGalleryDB::get_alias($item['es_title'], $item['es_videoid']);//$item['es_alias'];

        $blankArray['es_latitude'] = $item['es_latitude'];
        $blankArray['es_longitude'] = $item['es_longitude'];
        $blankArray['es_altitude'] = $item['es_altitude'];

        return $blankArray;
    }

    public static function queryTheAPIServer($theLink, $host = '', $force = false): ?string
    {
        if ($host == '')
            $host = YouTubeGalleryDB::getSettingValue('joomlaboat_api_host');

        $key = YouTubeGalleryDB::getSettingValue('joomlaboat_api_key');

        //It's very important to encode the YouTube link.
        if (!str_contains($host, '?'))
            $url = $host . '?';
        else
            $url = $host . '&';

        $url .= 'key=' . $key . '&v=5.3.3&query=' . base64_encode($theLink);

        if ($force)
            $url .= '&force=1';//to force the update

        return Helper::getURLData($url);
    }

    public static function getVideoSourceName($link): string
    {
        if (str_contains($link, '://youtube.com') or str_contains($link, '://www.youtube.com')) {
            if (str_contains($link, 'youtube.com/@'))
                return 'youtubehandle';
            elseif (str_contains($link, '/playlist'))
                return 'youtubeplaylist';
            elseif (str_contains($link, '&list=PL'))
                return 'youtubeplaylist';
            elseif (str_contains($link, '/favorites'))
                return 'youtubeuserfavorites';
            elseif (str_contains($link, '/user'))
                return 'youtubeuseruploads';
            elseif (str_contains($link, '/results'))
                return 'youtubesearch';
            elseif (str_contains($link, 'youtube.com/show/'))
                return 'youtubeshow';
            elseif (str_contains($link, 'youtube.com/channel/'))
                return 'youtubechannel';
            else
                return 'youtube';
        }

        if (str_contains($link, '://youtu.be') or str_contains($link, '://www.youtu.be'))
            return 'youtube';

        if (str_contains($link, 'youtubestandard:'))
            return 'youtubestandard';

        if (!str_contains($link, 'videolist:'))
            return 'videolist';


        if (str_contains($link, '://vimeo.com/user') or str_contains($link, '://www.vimeo.com/user'))
            return 'vimeouservideos';
        elseif (str_contains($link, '://vimeo.com/channels/') or str_contains($link, '://www.vimeo.com/channels/'))
            return 'vimeochannel';
        elseif (str_contains($link, '://vimeo.com/album/') or str_contains($link, '://www.vimeo.com/album/'))
            return 'vimeoalbum';
        elseif (str_contains($link, '://vimeo.com') or str_contains($link, '://www.vimeo.com')) {
            preg_match('/http:\/\/vimeo.com\/(\d+)$/', $link, $matches);
            if (count($matches) != 0) {
                //single video
                return 'vimeo';
            } else {
                preg_match('/https:\/\/vimeo.com\/(\d+)$/', $link, $matches);
                if (count($matches) != 0) {
                    //single video
                    return 'vimeo';
                } else {
                    preg_match('/http:\/\/vimeo.com\/(\d+)$/', $link, $matches);
                    return 'vimeouservideos'; //or anything else
                }
            }
        }

        if (str_contains($link, '://own3d.tv/l/') or str_contains($link, '://www.own3d.tv/l/'))
            return 'own3dtvlive';

        if (str_contains($link, '://own3d.tv/v/') or str_contains($link, '://www.own3d.tv/v/'))
            return 'own3dtvvideo';

        if (str_contains($link, 'video.google.com'))
            return 'google';

        if (str_contains($link, 'video.yahoo.com'))
            return 'yahoo';

        if (str_contains($link, '://break.com') or str_contains($link, '://www.break.com'))
            return 'break';

        if (str_contains($link, '://collegehumor.com') or str_contains($link, '://www.collegehumor.com'))
            return 'collegehumor';

        //https://www.dailymotion.com/playlist/x1crql_BigCatRescue_funny-action-big-cats/1#video=x7k9rx
        if (str_contains($link, '://dailymotion.com/playlist/') or str_contains($link, '://www.dailymotion.com/playlist/'))
            return 'dailymotionplaylist';

        if (str_contains($link, '://dailymotion.com') or str_contains($link, '://www.dailymotion.com') === false)
            return 'dailymotion';

        if (str_contains($link, '://present.me') or str_contains($link, '://www.present.me') === false)
            return 'presentme';

        if (str_contains($link, '://tiktok.com/') or str_contains($link, '://www.tiktok.com/') === false)
            return 'tiktok';

        if (str_contains($link, '://ustream.tv/recorded') or str_contains($link, '://www.ustream.tv/recorded') === false)
            return 'ustream';

        if (str_contains($link, '://ustream.tv/channel') or str_contains($link, '://www.ustream.tv/channel') === false)
            return 'ustreamlive';

        //http://api.soundcloud.com/tracks/49931.json  - accepts only resolved links
        if (str_contains($link, '://api.soundcloud.com/tracks/'))
            return 'soundcloud';

        return '';
    }
}
