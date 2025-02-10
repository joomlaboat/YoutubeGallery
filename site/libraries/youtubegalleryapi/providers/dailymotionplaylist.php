<?php
/**
 * YouTubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use CustomTables\CTMiscHelper;
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

class YGAPI_VideoSource_DailymotionPlaylist
{
	/**
	 * @throws Exception
	 */
	public static function getVideoIDList($URL, &$playlistId, &$dataLink): array
	{
		//https://api.dailymotion.com/playlist/xy4h8/videos

		$videoList = [];

		$playlistId = YGAPI_VideoSource_DailymotionPlaylist::extractDailymotionPlayListID($URL);
		if ($playlistId == '')
			return $videoList; //playlist id not found

		$dataLink = 'https://api.dailymotion.com/playlist/' . $playlistId . '/videos';
		$j = CTMiscHelper::getJSONUrlResponse($dataLink, false);

		if (isset($j->error)) {
			$videoListItem['es_status'] = -1;
			$videoListItem['es_error'] = $j->error->message;
			$videoListItem['es_rawdata'] = null;
			Factory::getApplication()->enqueueMessage($videoListItem['es_error'], 'error');

			$videoList[] = $videoListItem;
			return $videoList;
		} elseif ($j === null) {
			Factory::getApplication()->enqueueMessage('YGAPI_VideoSource_DailymotionPlaylist - Response is empty', 'error');
			return [];
		}

		if (isset($j->list)) {
			foreach ($j->list as $entry)
				$videoList[] = 'https://www.dailymotion.com/playlist/' . $entry->id;
		}

		return $videoList;
	}

	public static function extractDailymotionPlayListID($URL): string
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
