<?php
/**
 * YoutubeGallery API for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use CustomTables\CTMiscHelper;
use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

class YouTubeGalleryAPIData
{
	public static function formVideoList($theLink, bool $active_key, $youtube_data_api_key = '')
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

	public static function isVideoList($vsn): bool
	{
		$channels_youtube = array('youtubeuseruploads', 'youtubestandard', 'youtubehandle', 'youtubeplaylist', 'youtubeshow', 'youtubeuserfavorites', 'youtubesearch', 'youtubechannel', 'youtubeuservideos',
			'youtubeuserfeatured');
		$channels_other = array('vimeouservideos', 'vimeochannel', 'vimeoalbum', 'dailymotionplaylist');
		//$channels_vimeo = array('vimeouservideos', 'vimeochannel', 'vimeoalbum');

		if (in_array($vsn, $channels_youtube) or in_array($vsn, $channels_other)) {
			return true;
		} else
			return false;
	}

	public static function GrabVideoListData($theLink, $vsn, bool $active_key, $youtube_data_api_key = '')
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
			$videoItems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'playlistItems?playlistId=' . $playlistId, $playlistId, $active_key, $youtube_data_api_key);
		} elseif ($vsn == 'youtubechannel') {
			require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
			require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
			$channelId = YGAPI_VideoSource_YoutubePlaylist::extractYoutubeChannelID($theLink);
			$videoItems = YGAPI_VideoSource_YoutubePlaylist::YoutubeLists($theLink, $vsn, 'search?channelId=' . $channelId, $channelId, $active_key, $youtube_data_api_key);
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

	public static function GrabVideoData($theLink, $vsn, bool $active_key, $youtube_data_api_key = ''): array
	{
		$query_video_host = true;
		$videoItem = YouTubeGalleryAPIMisc::getBlankArray();
		$videoItem['es_videosource'] = $vsn;
		$videoItem['es_link'] = $theLink;
		$videoItem['es_isvideo'] = 1;
		$videoItem['es_parentid'] = null;
		$videoItem['es_customimageurl'] = null;
		$videoItem['es_customtitle'] = null;
		$videoItem['es_customdescription'] = null;

		switch ($vsn) {
			case 'vimeo' :

				require_once('providers' . DIRECTORY_SEPARATOR . 'vimeo.php');
				$videoId = YGAPI_VideoSource_Vimeo::extractVimeoID($theLink);

				if ($videoId != '') {
					$videoItem['es_videoid'] = $videoId;
					YGAPI_VideoSource_Vimeo::getVideoData($videoId, $videoItem);
				}

				break;

			case 'youtube' :

				require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
				$videoId = YGAPI_VideoSource_Youtube::extractYouTubeID($theLink);

				if ($videoId != '') {
					$videoItem['es_videoid'] = $videoId;

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
						return $videoItem;
					}

					$link = 'https://www.googleapis.com/youtube/v3/videos?id=' . $videoId . '&part=' . $part . '&key=' . $key;
					$videoItem['es_datalink'] = $link;//this link won't be visible in output, its for internal use.
					$json = YouTubeGalleryAPIData::getVideoData($link, $videoItem);

					if ($json)
						YGAPI_VideoSource_Youtube::copyVideoData($json, $videoItem);
				}
				break;

			case 'dailymotion' :

				require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotion.php');
				$videoId = YGAPI_VideoSource_DailyMotion::extractDailyMotionID($theLink);

				if ($videoId != '') {
					$videoItem['es_videoid'] = $videoId;
					$fields = 'created_time,description,duration,rating,ratings_total,thumbnail_small_url,thumbnail_medium_url,title,views_total';
					$link = 'https://api.dailymotion.com/video/' . $videoId . '?fields=' . $fields;
					//$blankArray['es_datalink'] = $link;
					$json = YouTubeGalleryAPIData::getVideoData($link, $videoItem);
					if ($json)
						YGAPI_VideoSource_DailyMotion::copyVideoData($json, $videoItem);
				}

				break;

			case 'ustreamlive':
			case 'ustream' :

				require_once('providers' . DIRECTORY_SEPARATOR . 'ustream.php');

				$videoId = YGAPI_VideoSource_Ustream::extractUstreamID($theLink);

				if ($videoId != '') {
					$videoItem['es_videoid'] = $videoId;
					YGAPI_VideoSource_Ustream::getVideoData($videoId, $videoItem);
				}

				break;

			case 'tiktok' :

				require_once('providers' . DIRECTORY_SEPARATOR . 'tiktok.php');
				$videoId = YGAPI_VideoSource_TikTok::extractTikTokID($theLink);

				if ($videoId != '') {
					$videoItem['es_videoid'] = $videoId;
					$link = 'https://www.tiktok.com/oembed?url=' . $theLink;
					//$blankArray['es_datalink'] = $link;
					$json = YouTubeGalleryAPIData::getVideoData($link, $videoItem);
					if ($json)
						YGAPI_VideoSource_TikTok::copyVideoData($json, $videoItem);
				}

				break;

			case 'soundcloud' :

				//https://soundcloud.com/sunny2point0/hellokitty
				require_once('providers' . DIRECTORY_SEPARATOR . 'soundcloud.php');
				$videoId = YGAPI_VideoSource_soundcloud::extractID($theLink);

				if ($videoId != '') {
					$url = 'https://api.soundcloud.com/resolve.json?url=' . urlencode($theLink) . '&client_id=' . YouTubeGalleryAPIMisc::APIKey_SoundCloud_ClientID();
					$json = YouTubeGalleryAPIData::getVideoData($url, $videoItem);

					if ($json) {
						if ((int)$json->status == 302) {
							$videoItem['es_datalink'] = $json->location;
							$videoItem['es_videoid'] = $videoId;
							$videoItem['es_trackid'] = YGAPI_VideoSource_soundcloud::extractTrackID($json->location);

							$j = YouTubeGalleryAPIData::getVideoData($json->location, $videoItem);
							if ($j)
								YGAPI_VideoSource_SoundCloud::copyVideoData($j, $videoItem);
						} else {
							$videoItem['es_status'] = -(int)$json->status;
							$videoItem['es_error'] = $json->status;
						}
					}
				}
				break;

		}//switch($vsn)


		return $videoItem;
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
			if (phpversion() < 7.4) {
				$blankArray['es_status'] = -1;
				$blankArray['es_error'] = 'Update to PHP 7.4+';
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
	}

	public static function getVideoID($theLink, $vsn): string
	{
		if (YouTubeGalleryAPIData::isVideoList($vsn)) {

			switch ($vsn) {
				case 'youtubeplaylist':
				case 'youtubehandle':
					require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
					return YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($theLink);
				case 'youtubechannel':
					require_once('providers' . DIRECTORY_SEPARATOR . 'youtubeplaylist.php');
					return YGAPI_VideoSource_YoutubePlaylist::extractYoutubeChannelID($theLink);
				case 'youtubeshow':
					//require_once('providers'.DIRECTORY_SEPARATOR.'youtubeshow.php');
					//$newlist='';//YGAPI_VideoSource_YoutubeShow::getVideoIDList($theLink, $specialparams, $playlistid,$datalink);
					return '';
				case 'youtubeuseruploads':
				case 'youtubeuserfavorites':
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
					$arr = CTMiscHelper::parse_query($theLink);

					$p = urldecode($arr['search_query']);
					if (!isset($p) or $p == '')
						return ''; //incorrect Link

					$keywords = str_replace('"', '', $p);
					$keywords = str_replace('+', ' ', $keywords);
					return str_replace(' ', ',', $keywords);
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
				case 'youtube' :
					require_once('providers' . DIRECTORY_SEPARATOR . 'youtube.php');
					return YGAPI_VideoSource_Youtube::extractYouTubeID($theLink);
				case 'dailymotion' :
					require_once('providers' . DIRECTORY_SEPARATOR . 'dailymotion.php');
					return YGAPI_VideoSource_DailyMotion::extractDailyMotionID($theLink);
				case 'livestream':
				case 'ustream' :
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
