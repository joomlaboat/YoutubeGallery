<?php
/**
 * YoutubeGallery API for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

class YouTubeGalleryAPIData
{
    public static function formVideoList($theLink, $active_key, $youtube_data_api_key = '')
    {
        //return one or multiple video rows
        $gallery_list = array();
        $vsn = YouTubeGalleryAPIData::getVideoSourceName($theLink);

        if (YouTubeGalleryAPIData::isVideoList($vsn)) {
            $gallery_list = YouTubeGalleryAPIData::GrabVideoListData($theLink, $vsn, $active_key, $youtube_data_api_key);
        } else {
            $item = YouTubeGalleryAPIData::GrabVideoData($theLink, $vsn, $active_key, $youtube_data_api_key);

            if (isset($item['es_videoid']) and $item['es_videoid'] != '')
                $gallery_list[] = $item;
        }

        return $gallery_list;
    }

    public static function getVideoSourceName($link)
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
            elseif (str_contains($link, 'youtube.com/c/') and str_contains($link, '/videos')) {
                return 'youtubeuservideos';
            } elseif (str_contains($link, 'youtube.com/') and str_contains($link, '/featured'))
                return 'youtubeuserfeatured';
            else
                return 'youtube';
        }

        if (str_contains($link, '://youtu.be') or str_contains($link, '://www.youtu.be'))
            return 'youtube';

        if (str_contains($link, 'youtubestandard:'))
            return 'youtubestandard';

        if (str_contains($link, 'videolist:'))
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
            return '';
        }

        //https://www.dailymotion.com/playlist/x1crql_BigCatRescue_funny-action-big-cats/1#video=x7k9rx
        if (str_contains($link, '://dailymotion.com/playlist/') or str_contains($link, '://www.dailymotion.com/playlist/'))
            return 'dailymotionplaylist';

        if (str_contains($link, '://dailymotion.com') or str_contains($link, '://www.dailymotion.com'))
            return 'dailymotion';

        if (str_contains($link, '://tiktok.com/') or str_contains($link, '://www.tiktok.com/'))
            return 'tiktok';

        if (str_contains($link, '://ustream.tv/recorded') or str_contains($link, '://www.ustream.tv/recorded'))
            return 'ustream';

        if (str_contains($link, '://ustream.tv/channel') or str_contains($link, '://www.ustream.tv/channel'))
            return 'ustreamlive';

        //http://api.soundcloud.com/tracks/49931.json  - accepts only resolved links
        if (str_contains($link, 'soundcloud.com/'))
            return 'soundcloud';

        return '';
    }

    public static function isVideoList($vsn)
    {
        $channels_youtube = array('youtubeuseruploads', 'youtubestandard', 'youtubehandle', 'youtubeplaylist', 'youtubeshow', 'youtubeuserfavorites', 'youtubesearch', 'youtubechannel', 'youtubeuservideos',
            'youtubeuserfeatured');
        $channels_other = array('vimeouservideos', 'vimeochannel', 'vimeoalbum', 'dailymotionplaylist');
        $channels_vimeo = array('vimeouservideos', 'vimeochannel', 'vimeoalbum');

        if (in_array($vsn, $channels_youtube) or in_array($vsn, $channels_other)) {
            return true;
        } else
            return false;
    }

    public static function GrabVideoListData($theLink, $vsn, $active_key, $youtube_data_api_key = '')
    {
        $videoItems = array();

        if ($vsn == 'youtubehandle') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            $playlistId = YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($theLink);
            $videoItems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'search?q=' . str_replace('@', '%40', $playlistId), $playlistId, $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubeplaylist') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            $playlistId = YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($theLink);
            $videoItems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, $query, $playlistId, $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubechannel') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            $channelId = YGAPI_VideoSource_YoutubePlaylist::extractYoutubeChannelID($theLink);
            $videoItems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'search?channelId=' . $channelId, $channelId, $active_key, $youtube_data_api_key);

        } elseif ($vsn == 'youtubeshow') {
            //require_once('providers'.DIRECTORY_SEPARATOR.'youtube.php');
            //require_once('providers'.DIRECTORY_SEPARATOR.'youtubeshow.php');
            $videoItems = array();
        } elseif ($vsn == 'youtubeuserfavorites') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
            $videoItems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'favorites', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubeuservideos') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
            $videoItems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'videos', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubeuserfeatured') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
            $videoItems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'featured', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubeuseruploads') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
            $videoItems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'uploads', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubestandard') {
            //require_once('providers'.DIRECTORY_SEPARATOR.'youtubestandard.php');
            $videoItems = array();//YGAPI_VideoSource_YoutubeStandard::getVideoIDList($theLink, $specialparams, $playlistid,$datalink);
        } elseif ($vsn == 'youtubesearch') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            $keywords = YGAPI_VideoSource_YoutubePlaylist::extractYouTubeSearchKeywords($theLink);
            $videoItems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'search?q=' . urlencode($keywords), urlencode($keywords), $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'vimeouservideos') {
            $specialparams = '';
            require_once('providers' . DIRECTORY_SEPARATOR . 'vimeouservideos.php');
            $videoItems = YGAPI_VideoSource_VimeoUserVideos::getVideoIDList($theLink, $specialparams, $playlistId);
        } elseif ($vsn == 'vimeochannel') {
            $specialparams = '';
            require_once('providers' . DIRECTORY_SEPARATOR . 'vimeochannel.php');
            $videoItems = YGAPI_VideoSource_VimeoChannel::getVideoIDList($theLink, $specialparams);
        } elseif ($vsn == 'vimeoalbum') {
            $specialparams = '';
            require_once('providers' . DIRECTORY_SEPARATOR . 'vimeoalbum.php');
            $videoItems = YGAPI_VideoSource_VimeoAlbum::getVideoIDList($theLink, $specialparams);
        } elseif ($vsn == 'dailymotionplaylist') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotionplaylist.php');
            $videoItems = YGAPI_VideoSource_DailymotionPlaylist::getVideoIDList($theLink, $playlistId, $datalink);
        }
        return $videoItems;
    }

    public static function GrabVideoData($theLink, $vsn, $active_key, $youtube_data_api_key = '')
    {
        $query_video_host = true;
        $videoitem = YouTubeGalleryAPIMisc::getBlankArray();
        $videoitem['es_videosource'] = $vsn;
        $videoitem['es_link'] = $theLink;
        $videoitem['es_isvideo'] = 1;
        $videoitem['es_parentid'] = null;
        $videoitem['es_customimageurl'] = null;
        $videoitem['es_customtitle'] = null;
        $videoitem['es_customdescription'] = null;

        switch ($vsn) {
            case 'vimeo' :

                require_once('providers' . DIRECTORY_SEPARATOR . 'vimeo.php');
                $videoid = YGAPI_VideoSource_Vimeo::extractVimeoID($theLink);

                if ($videoid != '') {
                    $videoitem['es_videoid'] = $videoid;
                    YGAPI_VideoSource_Vimeo::getVideoData($videoid, $videoitem);
                }

                break;

            case 'youtube' :

                require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
                $videoid = YGAPI_VideoSource_Youtube::extractYouTubeID($theLink);

                if ($videoid != '') {
                    $videoitem['es_videoid'] = $videoid;

                    if ($active_key)
                        $part = 'recordingDetails,id,snippet,contentDetails,statistics';//,status
                    else
                        $part = 'id,snippet,contentDetails,statistics';//,status

                    if ($youtube_data_api_key == '')
                        $key = YouTubeGalleryDB::getSettingValue('youtubedataapi_key');
                    else
                        $key = $youtube_data_api_key;

                    if ($key == '') {
                        Factory::getApplication()->enqueueMessage('Youtube Data API key is required.', 'error');
                        return $videoitem;
                    }

                    $link = 'https://www.googleapis.com/youtube/v3/videos?id=' . $videoid . '&part=' . $part . '&key=' . $key;

                    $videoitem['es_datalink'] = $link;//this link won't be visible in output, its for internal use.

                    $json = YouTubeGalleryAPIData::getVideoData($link, $videoitem);

                    if ($json != false)
                        YGAPI_VideoSource_Youtube::copyVideoData($json, $videoitem);
                }
                break;

            case 'dailymotion' :

                require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotion.php');
                $videoid = YGAPI_VideoSource_DailyMotion::extractDailyMotionID($theLink);

                if ($videoid != '') {
                    $videoitem['es_videoid'] = $videoid;
                    $fields = 'created_time,description,duration,rating,ratings_total,thumbnail_small_url,thumbnail_medium_url,title,views_total';
                    $link = 'https://api.dailymotion.com/video/' . $videoid . '?fields=' . $fields;
                    $blankArray['es_datalink'] = $link;
                    $json = YouTubeGalleryAPIData::getVideoData($link, $videoitem);
                    if ($json != false)
                        YGAPI_VideoSource_DailyMotion::copyVideoData($json, $videoitem);
                }

                break;

            case 'ustream' :

                require_once('providers' . DIRECTORY_SEPARATOR . 'ustream.php');

                $videoid = YGAPI_VideoSource_Ustream::extractUstreamID($theLink);

                if ($videoid != '') {
                    $videoitem['es_videoid'] = $videoid;
                    YGAPI_VideoSource_Ustream::getVideoData($videoid, $videoitem);
                }

                break;

            case 'ustreamlive' :

                require_once('providers' . DIRECTORY_SEPARATOR . 'ustream.php');

                $videoid = YGAPI_VideoSource_Ustream::extractUstreamID($theLink);

                if ($videoid != '') {
                    $videoitem['es_videoid'] = $videoid;
                    YGAPI_VideoSource_Ustream::getVideoData($videoid, $videoitem);
                }

                break;

            case 'tiktok' :

                require_once('providers' . DIRECTORY_SEPARATOR . 'tiktok.php');
                $videoid = YGAPI_VideoSource_TikTok::extractTikTokID($theLink);

                if ($videoid != '') {
                    $videoitem['es_videoid'] = $videoid;
                    $link = 'https://www.tiktok.com/oembed?url=' . $theLink;
                    $blankArray['es_datalink'] = $link;
                    $json = YouTubeGalleryAPIData::getVideoData($link, $videoitem);
                    if ($json != false)
                        YGAPI_VideoSource_TikTok::copyVideoData($json, $videoitem);
                }

                break;

            case 'soundcloud' :

                //https://soundcloud.com/sunny2point0/hellokitty
                require_once('providers' . DIRECTORY_SEPARATOR . 'soundcloud.php');

                $videoid = YGAPI_VideoSource_soundcloud::extractID($theLink);

                if ($videoid != '') {
                    $url = 'http://api.soundcloud.com/resolve.json?url=' . urlencode($theLink) . '&client_id=' . YouTubeGalleryAPIMisc::APIKey_SoundCloud_ClientID();
                    $json = YouTubeGalleryAPIData::getVideoData($url, $videoitem);

                    if ($json != false) {
                        if ((int)$json->status == 302) {
                            $videoitem['es_datalink'] = $json->location;
                            $videoitem['es_videoid'] = $videoid;
                            $videoitem['es_trackid'] = YGAPI_VideoSource_soundcloud::extractTrackID($json->location);

                            $j = YouTubeGalleryAPIData::getVideoData($json->location, $videoitem);
                            if ($j != false)
                                YGAPI_VideoSource_SoundCloud::copyVideoData($j, $videoitem);
                        } else {
                            $videoitem['es_status'] = -(int)$json->status;
                            $videoitem['es_error'] = $json->status;
                        }
                    }
                }
                break;

        }//switch($vsn)


        return $videoitem;
    }

    protected static function getVideoData($link, &$blankArray)
    {
        //Returns JSON
        if (!function_exists('curl_init') and !function_exists('file_get_contents')) {
            $blankArray['es_status'] = -1;
            $blankArray['es_error'] = 'Enable php functions: curl_init or file_get_contents.';
            return false;

        }

        if (function_exists('phpversion')) {
            if (phpversion() < 5) {
                $blankArray['es_status'] = -1;
                $blankArray['es_error'] = 'Update to PHP 5+';
                return false;
            }
        }

        try {
            $htmlcode = Helper::getURLData($link);

            $j = json_decode($htmlcode);

            if (!$j) {
                //$e=$j->error->errors[0];
                $blankArray['es_status'] = -1;
                $blankArray['es_error'] = 'Response is not JSON';
                $blankArray['es_rawdata'] = null;//$htmlcode;

                return false;
            } else
                return $j;
        } catch (Exception $e) {
            $blankArray['es_status'] = -2;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Error catched while requesting data.';
            $blankArray['es_rawdata'] = null;//$htmlcode;
            return false;
        }
        return false;
    }

    public static function getVideoID($theLink, $vsn)
    {
        if (YouTubeGalleryAPIData::isVideoList($vsn)) {

            switch ($vsn) {
                case 'youtubehandle':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
                    return YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($theLink);
                case 'youtubeplaylist':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
                    return YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($theLink);
                case 'youtubechannel':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
                    return YGAPI_VideoSource_YoutubePlaylist::extractYoutubeChannelID($theLink);
                case 'youtubeshow':
                    //require_once('providers'.DIRECTORY_SEPARATOR.'youtubeshow.php');
                    //$newlist='';//YGAPI_VideoSource_YoutubeShow::getVideoIDList($theLink, $specialparams, $playlistid,$datalink);
                    return '';
                case 'youtubeuserfavorites':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserID($theLink);
                case 'youtubeuseruploads':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserID($theLink);
                case 'youtubeuservideos':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserVideosID($theLink);
                case 'youtubeuserfeatured':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserFeaturedID($theLink);
                case 'youtubestandard':
                    $linkPair = explode(':', $theLink);
                    if (!isset($linkPair[1]))
                        return '';

                    return $linkPair[1];
                case 'youtubesearch':
                    $arr = JoomlaBasicMisc::parse_query($theLink);

                    $p = urldecode($arr['search_query']);
                    if (!isset($p) or $p == '')
                        return ''; //incorrect Link

                    $keywords = str_replace('"', '', $p);
                    $keywords = str_replace('+', ' ', $keywords);
                    $keywords = str_replace(' ', ',', $keywords);

                    return $keywords;
                case 'vimeouservideos':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeouservideos.php');
                    return YGAPI_VideoSource_VimeoUserVideos::extractVimeoUserID($theLink);
                case 'vimeochannel':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeochannel.php');
                    return YGAPI_VideoSource_VimeoChannel::extractVimeoUserID($theLink);
                case 'vimeoalbum':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeoalbum.php');
                    return YGAPI_VideoSource_VimeoAlbum::extractVimeoAlbumID($theLink);
                case 'dailymotionplaylist':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotionplaylist.php');
                    return YGAPI_VideoSource_DailymotionPlaylist::extractDailymotionPlayListID($theLink);
            }
        } else {
            switch ($vsn) {
                case 'vimeo' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeo.php');
                    return YGAPI_VideoSource_Vimeo::extractVimeoID($theLink);
                case 'own3dtvlive' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'own3dtvlive.php');
                    return YGAPI_VideoSource_Own3DTvLive::extractOwn3DTvLiveID($theLink);
                case 'own3dtvvideo' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'own3dtvvideo.php');
                    return YGAPI_VideoSource_Own3DTvVideo::extractOwn3DTvVideoID($theLink);
                case 'youtube' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
                    return YGAPI_VideoSource_Youtube::extractYouTubeID($theLink);
                case 'dailymotion' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotion.php');
                    return YGAPI_VideoSource_DailyMotion::extractDailyMotionID($theLink);
                case 'ustream' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'ustream.php');
                    return YGAPI_VideoSource_Ustream::extractUstreamID($theLink);
                case 'ustreamlive' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'ustream.php');
                    return YGAPI_VideoSource_Ustream::extractUstreamID($theLink);
                case 'tiktok' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'tiktok.php');
                    return YGAPI_VideoSource_TikTok::extractTikTokID($theLink);
                case 'soundcloud' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'soundcloud.php');
                    return YGAPI_VideoSource_soundcloud::extractID($theLink);
            }//switch($vsn)
        }
        return '';
    }
}
