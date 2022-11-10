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
        if (!(strpos($link, '://youtube.com') === false) or !(strpos($link, '://www.youtube.com') === false)) {
            if (!(strpos($link, '/playlist') === false))
                return 'youtubeplaylist';
            if (strpos($link, '&list=PL') !== false) {
                return 'youtubeplaylist';
                //https://www.youtube.com/watch?v=cNw8A5pwbVI&list=PLMaV6BfupUm-xIMRGKfjj-fP0BLq7b6SJ
            } elseif (!(strpos($link, '/favorites') === false))
                return 'youtubeuserfavorites';
            elseif (!(strpos($link, '/user') === false))
                return 'youtubeuseruploads';
            elseif (!(strpos($link, '/results') === false))
                return 'youtubesearch';
            elseif (!(strpos($link, 'youtube.com/show/') === false))
                return 'youtubeshow';
            elseif (!(strpos($link, 'youtube.com/channel/') === false))
                return 'youtubechannel';
            elseif (strpos($link, 'youtube.com/c/') !== false and strpos($link, '/videos') !== false) {
                return 'youtubeuservideos';
            } elseif (strpos($link, 'youtube.com/') !== false and strpos($link, '/featured') !== false)
                return 'youtubeuserfeatured';
            else
                return 'youtube';
        }

        if (!(strpos($link, '://youtu.be') === false) or !(strpos($link, '://www.youtu.be') === false))
            return 'youtube';

        if (!(strpos($link, 'youtubestandard:') === false))
            return 'youtubestandard';

        if (!(strpos($link, 'videolist:') === false))
            return 'videolist';


        if (!(strpos($link, '://vimeo.com/user') === false) or !(strpos($link, '://www.vimeo.com/user') === false))
            return 'vimeouservideos';
        elseif (!(strpos($link, '://vimeo.com/channels/') === false) or !(strpos($link, '://www.vimeo.com/channels/') === false))
            return 'vimeochannel';
        elseif (!(strpos($link, '://vimeo.com/album/') === false) or !(strpos($link, '://www.vimeo.com/album/') === false))
            return 'vimeoalbum';
        elseif (!(strpos($link, '://vimeo.com') === false) or !(strpos($link, '://www.vimeo.com') === false)) {
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
        if (!(strpos($link, '://dailymotion.com/playlist/') === false) or !(strpos($link, '://www.dailymotion.com/playlist/') === false))
            return 'dailymotionplaylist';

        if (!(strpos($link, '://dailymotion.com') === false) or !(strpos($link, '://www.dailymotion.com') === false))
            return 'dailymotion';

        if (!(strpos($link, '://tiktok.com/') === false) or !(strpos($link, '://www.tiktok.com/') === false))
            return 'tiktok';

        if (!(strpos($link, '://ustream.tv/recorded') === false) or !(strpos($link, '://www.ustream.tv/recorded') === false))
            return 'ustream';

        if (!(strpos($link, '://ustream.tv/channel') === false) or !(strpos($link, '://www.ustream.tv/channel') === false))
            return 'ustreamlive';


        //http://api.soundcloud.com/tracks/49931.json  - accepts only resolved links
        //if(!(strpos($link,'://api.soundcloud.com/tracks/')===false) )
        if (strpos($link, 'soundcloud.com/') !== false)
            return 'soundcloud';

        return '';
    }

    public static function isVideoList($vsn)
    {
        $channels_youtube = array('youtubeuseruploads', 'youtubestandard', 'youtubeplaylist', 'youtubeshow', 'youtubeuserfavorites', 'youtubesearch', 'youtubechannel', 'youtubeuservideos',
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
        $videoitems = array();

        if ($vsn == 'youtubeplaylist') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');

            $playlistid = YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($theLink);
            $videoitems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'playlistItems?playlistId=' . $playlistid, $playlistid, $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubechannel') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');


            $channelid = YGAPI_VideoSource_YoutubePlaylist::extractYoutubeChannelID($theLink);

            if ($theLink == 'https://www.youtube.com/channel/UCLJN3NrnEb-PediSaOku9mg') {
                echo $channelid . '<br/>
';
            }

            $videoitems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'search?channelId=' . $channelid, $channelid, $active_key, $youtube_data_api_key);

        } elseif ($vsn == 'youtubeshow') {
            //require_once('providers'.DIRECTORY_SEPARATOR.'youtube.php');
            //require_once('providers'.DIRECTORY_SEPARATOR.'youtubeshow.php');
            $videoitems = array();
        } elseif ($vsn == 'youtubeuserfavorites') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
            $videoitems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'favorites', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubeuservideos') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');

            $videoitems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'videos', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubeuserfeatured') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
            $videoitems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'featured', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubeuseruploads') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
            $videoitems = YGAPI_VideoSource_YoutubeUserUploads::getVideoIDList($theLink, $vsn, 'uploads', $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'youtubestandard') {
            //require_once('providers'.DIRECTORY_SEPARATOR.'youtubestandard.php');
            $videoitems = array();//YGAPI_VideoSource_YoutubeStandard::getVideoIDList($theLink, $specialparams, $playlistid,$datalink);
        } elseif ($vsn == 'youtubesearch') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
            require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');

            $keywords = YGAPI_VideoSource_YoutubePlaylist::extractYouTubeSearchKeywords($theLink);
            $videoitems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'search?q=' . urlencode($keywords), urlencode($keywords), $active_key, $youtube_data_api_key);
        } elseif ($vsn == 'vimeouservideos') {
            $specialparams = '';
            require_once('providers' . DIRECTORY_SEPARATOR . 'vimeouservideos.php');
            $videoitems = YGAPI_VideoSource_VimeoUserVideos::getVideoIDList($theLink, $specialparams, $playlistid);
        } elseif ($vsn == 'vimeochannel') {
            $specialparams = '';
            require_once('providers' . DIRECTORY_SEPARATOR . 'vimeochannel.php');
            $videoitems = YGAPI_VideoSource_VimeoChannel::getVideoIDList($theLink, $specialparams);
        } elseif ($vsn == 'vimeoalbum') {
            $specialparams = '';
            require_once('providers' . DIRECTORY_SEPARATOR . 'vimeoalbum.php');
            $videoitems = YGAPI_VideoSource_VimeoAlbum::getVideoIDList($theLink, $specialparams);
        } elseif ($vsn == 'dailymotionplaylist') {
            require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotionplaylist.php');
            $videoitems = YGAPI_VideoSource_DailymotionPlaylist::getVideoIDList($theLink, $playlistid, $datalink);
        }

        return $videoitems;
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
                        $key = YouTubeGalleryAPIMisc::APIKey_Youtube($active_key);
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
                case 'youtubeplaylist':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');

                    return YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($theLink);
                    break;

                case 'youtubechannel':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
                    return YGAPI_VideoSource_YoutubePlaylist::extractYoutubeChannelID($theLink);
                    break;

                case 'youtubeshow':
                    //require_once('providers'.DIRECTORY_SEPARATOR.'youtubeshow.php');
                    //$newlist='';//YGAPI_VideoSource_YoutubeShow::getVideoIDList($theLink, $specialparams, $playlistid,$datalink);
                    return '';
                    break;

                case 'youtubeuserfavorites':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserID($theLink);
                    break;

                case 'youtubeuseruploads':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserID($theLink);
                    break;

                case 'youtubeuservideos':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserVideosID($theLink);
                    break;

                case 'youtubeuserfeatured':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeuseruploads.php');
                    return YGAPI_VideoSource_YoutubeUserUploads::extractYouTubeUserFeaturedID($theLink);
                    break;

                case 'youtubestandard':
                    $linkPair = explode(':', $theLink);
                    if (!isset($linkPair[1]))
                        return '';

                    return $linkPair[1];
                    break;

                case 'youtubesearch':
                    $arr = YouTubeGalleryAPIMisc::parse_query($theLink);

                    $p = urldecode($arr['search_query']);
                    if (!isset($p) or $p == '')
                        return ''; //incorrect Link

                    $keywords = str_replace('"', '', $p);
                    $keywords = str_replace('+', ' ', $keywords);
                    $keywords = str_replace(' ', ',', $keywords);

                    return $keywords;
                    break;

                case 'vimeouservideos':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeouservideos.php');
                    return YGAPI_VideoSource_VimeoUserVideos::extractVimeoUserID($theLink);
                    break;

                case 'vimeochannel':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeochannel.php');
                    return YGAPI_VideoSource_VimeoChannel::extractVimeoUserID($theLink);
                    break;

                case 'vimeoalbum':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeoalbum.php');
                    return YGAPI_VideoSource_VimeoAlbum::extractVimeoAlbumID($theLink);
                    break;

                case 'dailymotionplaylist':
                    require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotionplaylist.php');
                    return YGAPI_VideoSource_DailymotionPlaylist::extractDailymotionPlayListID($theLink);
                    break;
            }
        } else {
            switch ($vsn) {
                case 'vimeo' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'vimeo.php');
                    return YGAPI_VideoSource_Vimeo::extractVimeoID($theLink);
                    break;

                case 'own3dtvlive' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'own3dtvlive.php');
                    return YGAPI_VideoSource_Own3DTvLive::extractOwn3DTvLiveID($theLink);
                    break;

                case 'own3dtvvideo' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'own3dtvvideo.php');
                    return YGAPI_VideoSource_Own3DTvVideo::extractOwn3DTvVideoID($theLink);
                    break;

                case 'youtube' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
                    return YGAPI_VideoSource_Youtube::extractYouTubeID($theLink);
                    break;

                    break;

                case 'dailymotion' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotion.php');
                    return YGAPI_VideoSource_DailyMotion::extractDailyMotionID($theLink);
                    break;

                case 'ustream' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'ustream.php');
                    return YGAPI_VideoSource_Ustream::extractUstreamID($theLink);
                    break;

                case 'ustreamlive' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'ustream.php');
                    return YGAPI_VideoSource_Ustream::extractUstreamID($theLink);
                    break;

                case 'tiktok' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'tiktok.php');
                    return YGAPI_VideoSource_TikTok::extractTikTokID($theLink);
                    break;

                case 'soundcloud' :
                    require_once('providers' . DIRECTORY_SEPARATOR . 'soundcloud.php');
                    return YGAPI_VideoSource_soundcloud::extractID($theLink);
                    break;

            }//switch($vsn)
        }
        return '';
    }
}
