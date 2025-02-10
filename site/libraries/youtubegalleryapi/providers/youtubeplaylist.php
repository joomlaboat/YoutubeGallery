<?php
/**
 * YouTubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use CustomTables\CTMiscHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

class YGAPI_VideoSource_YoutubePlaylist
{
	public static function extractYoutubeChannelID($youtubeURL): string
	{
		//https://www.youtube.com/channel/UCRND2QLhATXcgrTgFfsZHyg/videos
		$matches = explode('/', $youtubeURL);

		if (count($matches) > 4) {
			$channelId = $matches[4];
			$pair = explode('?', $channelId);
			return $pair[0];
		}
		return '';
	}

	public static function extractYouTubeSearchKeywords($youtubeURL): string
	{
		//https://www.youtube.com/results?search_query=%22dogs+101%22&oq=%22dogs+101%22&gs_l=youtube.3..0l10.16119.16453.0.17975.2.2.0.0.0.0.330.649.3-2.2.0...0.0...1ac.1.GQ5tbo9Q0Cg
		$arr = CTMiscHelper::parse_query($youtubeURL);

		$p = urldecode($arr['search_query']);
		if (!isset($p) or $p == '')
			return ''; //incorrect Link

		$keywords = str_replace('"', '', $p);
		$keywords = str_replace('\'', '', $keywords);
		$keywords = str_replace('&', '', $keywords);
		$keywords = str_replace('?', '', $keywords);
		$keywords = str_replace('%', '', $keywords);
		$keywords = str_replace('+', ' ', $keywords);
		return str_replace(' ', ',', $keywords);
	}

	public static function YoutubeLists($theLink, $vsn, $query, $listId, bool $active_key, $youtube_data_api_key = ''): array
	{
		$videoItems = array();

		$videoListItem = YouTubeGalleryAPIMisc::getBlankArray();
		$videoListItem['es_videosource'] = $vsn;
		$videoListItem['es_link'] = $theLink;
		$videoListItem['es_isvideo'] = 0;
		$videoListItem['es_videoid'] = $listId;
		$videoListItem['es_videoids'] = '-';

		require_once('youtube.php');

		$part = 'id,snippet';
		$base_url = 'https://www.googleapis.com/youtube/v3';

		if ($youtube_data_api_key == '') {
			Factory::getApplication()->enqueueMessage('Youtube Data API key is required.', 'error');
			return $videoItems;
		}

		$dataLink = $base_url . '/' . $query . '&part=' . $part . '&key=' . $youtube_data_api_key;
		$videoListItem['es_datalink'] = $dataLink;
		$debug = false;

		if ($theLink == 'https://www.youtube.com/channel/UCLJN3NrnEb-PediSaOku9mg') {
			$debug = true;
		}

		$newList = YGAPI_VideoSource_YoutubePlaylist::getPlaylistVideos($dataLink, $videoListItem, $active_key, $debug);

		$videoItems[] = $videoListItem;
		return array_merge($videoItems, $newList);
	}

	public static function getPlaylistVideos(string $dataLink, &$videoListItem, bool $active_key, $debug = false): array
	{
		//If its on JoomlaBoat.com - limit the number of videos to 100
		$limitNumberOfVideos = false;
		if (str_contains(Uri::root(false), 'joomlaboat.com'))
			$limitNumberOfVideos = true;

		$videoList = array();
		$url = $dataLink;

		$videos_found = 0;
		$nextPageToken = '';
		$count = -1;
		$videos = array();

		while ($videos_found < $count or $count == -1) {
			if ($nextPageToken != '')
				$url = $dataLink . '&pageToken=' . $nextPageToken;

			$j = CTMiscHelper::getJSONUrlResponse($url, false);

			if (isset($j->error)) {
				$videoListItem['es_status'] = -1;
				$videoListItem['es_error'] = $j->error->message;
				$videoListItem['es_rawdata'] = null;
				Factory::getApplication()->enqueueMessage($videoListItem['es_error'], 'error');

				$videoList[] = $videoListItem;
				return $videoList;
			} elseif ($j === null) {
				Factory::getApplication()->enqueueMessage('Youtube Gallery: getVideoIDList - Response is empty', 'error');
				return [];
			}

			$nextPageToken = $j->nextPageToken ?? '';

			$pageInfo = $j->pageInfo;
			if ($pageInfo->totalResults > $count)
				$count = $pageInfo->totalResults;

			$items = $j->items;
			if (count($items) == 0)
				break;

			foreach ($items as $item) {
				$videoItem = YouTubeGalleryAPIMisc::getBlankArray();
				$videoItem['es_videosource'] = 'youtube';
				$videoItem['es_isvideo'] = 1;

				YGAPI_VideoSource_YouTube::copyVideoDataItem($item, $videoItem, $debug);

				if ($videoItem['es_videoid'] != '') {
					$videoList[] = $videoItem;
					$videos[] = $videoItem['es_videoid'];

					//Update Channel title
					if ($videoListItem['es_title'] == '')
						$videoListItem['es_title'] = $videoItem['es_channeltitle'];

					$videos_found++;

					if (!$active_key and $videos_found >= 5) //break if not paid
						break;
				}
			}

			if (!$active_key and $videos_found >= 5) //break if not paid
				break;

			if ($limitNumberOfVideos and $videos_found > 100) //break if more than 100
				break;
		}

		$videoListItem['es_videoids'] = ',' . implode(',', $videos) . ',';
		return $videoList;
	}

	/*
	public static function getVideoIDList(string $youtubeURL, string &$playListId, string &$dataLink): array
	{
		$playListId = YGAPI_VideoSource_YoutubePlaylist::extractYouTubePlayListID($youtubeURL);

		$videoListItem = YouTubeGalleryAPIMisc::getBlankArray();
		$videoListItem['es_videosource'] = 'youtubeplaylist';
		$videoListItem['es_link'] = $youtubeURL;
		$videoListItem['es_isvideo'] = 0;
		$videoListItem['es_videoid'] = $playListId;
		$videoListItem['es_videoids'] = '-';
		$videoListItem['es_datalink'] = $dataLink;

		return YGAPI_VideoSource_YoutubePlaylist::getPlaylistVideos($dataLink, $videoListItem);
	}
	*/

	public static function extractYouTubePlayListID($youtubeURL): ?string
	{
		if (str_contains($youtubeURL, 'youtube.com/@')) {
			$parts = explode('youtube.com/@', $youtubeURL);
			if (count($parts) < 2)
				return null;

			return '@' . $parts[1];
		} else {
			$arr = CTMiscHelper::parse_query($youtubeURL);
			$p = $arr['list'];

			if (strlen($p) < 3)
				return '';

			return $p;
		}
	}
}
