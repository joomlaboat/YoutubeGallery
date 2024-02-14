<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use CustomTables\CT;
use CustomTables\Fields;
use CustomTables\ImportTables;
use CustomTables\IntegrityChecks;

//use CustomTables\ImportTables;
use CustomTables\TableHelper;
use Joomla\CMS\Factory;


if (!function_exists('str_contains')) {
	function str_contains($haystack, $needle): bool
	{
		return $needle !== '' && mb_strpos($haystack, $needle) !== false;
	}
}

class com_YoutubeGalleryInstallerScript
{
	function postflight($type, $parent)
	{
		if ($type == 'uninstall') {
			return true; //No need to do anything
		}

		com_YoutubeGalleryInstallerScript::enableButtonPlugin();
		$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'customtables' . DIRECTORY_SEPARATOR;
		$loader_file = $path . 'loader.php';

		if (!file_exists($loader_file)) {
			Factory::getApplication()->enqueueMessage('Youtube Gallery is corrupted, please contact the developer.', 'error');
			return false;
		}

		$component_name = 'com_youtubegallery';

		// PHP 7.4 + code
		require_once($loader_file);
		CustomTablesLoader($include_utilities = true, false, null, $component_name);
		$ct = new CT;

		//Check Custom Tables, create if necessary
		$result = IntegrityChecks::check($ct, $check_core_tables = true, $check_custom_tables = false);

		$filename = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $component_name . DIRECTORY_SEPARATOR
			. 'importfiles' . DIRECTORY_SEPARATOR . 'youtubegallery_tables.txt';

		$msg = '';

		$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR
			. 'customtables' . DIRECTORY_SEPARATOR;
		$path_utilities = $path . 'utilities' . DIRECTORY_SEPARATOR;

		require_once($path_utilities . 'importtables.php');
		$status = ImportTables::processFile($filename, 'YoutubeGallery', $msg);

		if ($msg != '') {
			Factory::getApplication()->enqueueMessage($msg, 'error');
			return false;
		}

		com_YoutubeGalleryInstallerScript::updateYGv3tov4();
		return true;
	}

	protected static function enableButtonPlugin()
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);

		$fields = array(
			$db->quoteName('enabled') . ' = 1',
			$db->quoteName('ordering') . ' = 9999'
		);

		$conditions = array(
			$db->quoteName('name') . ' = ' . $db->quote('plg_editors-xtd_youtubegallerybutton'),
			$db->quoteName('type') . ' = ' . $db->quote('plugin'),
			$db->quoteName('ordering') . ' != ' . $db->quote('9999')// We only need to perform this if the extension is being installed, not updated
		);

		$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

		$db->setQuery($query);
		$db->execute();
	}

	function updateYGv3tov4()
	{
		//Update Youtube gallery database tables to Joomla 4 model.
		//Joomla 4 Youtube Gallery database tables created using Custom Tables and can be managed by Custom Tables as well.

		$map = ['option', 'value'];
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_settings', '#__customtables_table_youtubegallerysettings', $map);

		$map = ['categoryname', 'parentid', 'description', 'image'];
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_categories', '#__customtables_table_youtubegallerycategories', $map);

		$map = ['themename', 'playvideo', 'width', 'height', 'repeat', 'fullscreen', 'autoplay', 'related', 'bgcolor', 'cssstyle', 'navbarstyle', 'thumbnailstyle',
			'listnamestyle', 'descr_style' => 'es_descrstyle', 'color1' => 'es_colorone', 'color2' => 'es_colortwo',
			'border', 'openinnewwindow', 'rel', 'hrefaddon',
			'customlimit', 'controls', 'youtubeparams', 'useglass', 'logocover', 'customlayout', 'prepareheadtags', 'muteonplay', 'lastplaylistupdate',
			'volume', 'orderby', 'customnavlayout', 'responsive', 'mediafolder', 'headscript', 'themedescription', 'nocookie', 'changepagetitle', 'allowplaylist'];


		$ignore_map = ['showtitle', 'showinfo', 'cols', 'linestyle', 'showlistname', 'showactivevideotitle',
			'activevideotitlestyle', 'description', 'pagination', 'playertype', 'readonly', 'cache', 'enablecache', 'randomization', 'descr_position'];

		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_themes', '#__customtables_table_youtubegallerythemes', $map, $ignore_map);

		//'listname'
		$map = ['videolist', 'catid', 'updateperiod', 'lastplaylistupdate', 'datetime', 'description', 'watchusergroup', 'authorurl', 'image', 'note'];
		$ignore_map = ['author'];
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_videolists', '#__customtables_table_youtubegalleryvideolists', $map, $ignore_map);


		$map = ['custom_imageurl' => 'es_customimageurl', 'custom_title' => 'es_customtitle', 'custom_description' => 'es_customdescription',
			'rating_average' => 'es_ratingaverage', 'rating_max' => 'es_ratingmax', 'rating_min' => 'es_ratingmin', 'rating_numRaters' => 'es_ratingnumberofraters',
			'statistics_favoriteCount' => 'es_statisticsfavoritecount', 'statistics_viewCount' => 'es_statisticsviewcount',
			'channel_username' => 'es_channelusername', 'channel_title' => 'es_channeltitle', 'channel_subscribers' => 'es_channelsubscribers',
			'channel_subscribed' => 'es_channelsubscribed', 'channel_location' => 'es_channellocation', 'channel_commentcount' => 'es_channelcommentcount',
			'channel_viewcount' => 'es_channelviewcount', 'channel_videocount' => 'es_channelvideocount', 'channel_description' => 'es_channeldescription',
			'listid' => 'es_videolist',
			'latitude' => ['name' => 'es_latitude', 'type' => 'float'],
			'longitude' => ['name' => 'es_longitude', 'type' => 'float'],
			'altitude' => ['name' => 'es_altitude', 'type' => 'int']
			//'channel_totaluploadviews' => 'es_channel_totaluploadviews',

		];

		$ignore_map = ['volume', 'channel_totaluploadviews'];

		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_videos', '#__customtables_table_youtubegalleryvideos', $map, $ignore_map);
	}

	function updateYGv3table($old_table, $new_table, $map, $exceptions = array())
	{
		if (!TableHelper::checkIfTableExists($old_table))
			return false;

		$db = Factory::getDBO();
		$query = 'SELECT COUNT(*) AS c FROM ' . $new_table . ' LIMIT 1';
		$db->setQuery($query);
		$records = $db->loadAssocList();

		if ((int)$records[0]['c'] > 0)
			return false;

		$query = 'SELECT * FROM ' . $old_table . ' LIMIT 100';
		$db->setQuery($query);

		$records = $db->loadAssocList();

		foreach ($records as $record) {
			$id = self::insertRecords($new_table, $record, false, $exceptions, true, 'es_', $map);//insert single new record
		}
		return false;
	}

	public static function insertRecords($table, $rows, $addprefix = true, $exceptions = array(), $force_id = false, $add_field_prefix = '', $field_conversion_map = array())
	{
		if ($addprefix)
			$mysqltablename = '#__customtables_' . $table;
		else
			$mysqltablename = $table;

		$db = Factory::getDBO();

		$inserts = array();

		$keys = array_keys($rows);

		$ignore_fields = ['asset_id', 'created_by', 'modified_by', 'checked_out',
			'checked_out_time', 'version', 'hits', 'publish_up', 'publish_down', 'checked_out_time'];

		$core_fields = ['id', 'published'];

		foreach ($keys as $key) {
			$isOk = false;
			$type = null;

			if (isset($field_conversion_map[$key])) {
				$isOk = true;
				if (is_array($field_conversion_map[$key])) {
					$fieldname = $field_conversion_map[$key]['name'];
					$type = $field_conversion_map[$key]['type'];
				} else
					$fieldname = $field_conversion_map[$key];
			} elseif (count($field_conversion_map) > 0 and in_array($key, $field_conversion_map)) {
				$isOk = true;
				if (in_array($key, $core_fields))
					$fieldname = $key;
				else
					$fieldname = $add_field_prefix . $key;
			} else {
				$fieldname = self::checkFieldName($key, $force_id, $exceptions);
				if ($fieldname != '') {
					$isOk = true;
					if (!in_array($fieldname, $core_fields))
						$fieldname = $add_field_prefix . $fieldname;
				}
			}

			if ($isOk and !in_array($fieldname, $ignore_fields)) {
				/*
				if (!Fields::checkIfFieldExists($mysqltablename, $fieldname, false)) {
					//Add field
					$isLanguageFieldName = Fields::isLanguageFieldName($fieldname);

					if ($isLanguageFieldName) {
						//Add language field
						//Get non langauge field type

						$nonLanguageFieldName = Fields::getLanguagelessFieldName($key);

						$filedtype = Fields::getFieldType($mysqltablename, $nonLanguageFieldName);

						if ($filedtype != '') {
							Fields::AddMySQLFieldNotExist($mysqltablename, $key, $filedtype, '');
							if ($rows[$key] === null) {

							}

							$inserts[] = $fieldname . '=' . ImportTables::dbQuoteByType($rows[$key], $type);
						}
					}
				} else {
					*/
				$inserts[] = $fieldname . '=' . $db->quote($rows[$key], $type);
				//}
			}
		}

		return self::insertRecords2($mysqltablename, $inserts);
	}

	public static function checkFieldName($key, $force_id, $exceptions)
	{
		$ok = true;

		if (str_contains($key, 'itemaddedtext'))
			$ok = false;

		if (!$force_id) {
			if ($key == 'id')
				$ok = false;
		}

		for ($k = 3; $k < 11; $k++) {
			if (str_contains($key, '_' . $k))
				$ok = false;
		}

		if (str_contains($key, '_1'))
			$fieldname = str_replace('_1', '', $key);
		elseif (str_contains($key, '_2'))
			$fieldname = str_replace('_2', '_es', $key);
		else
			$fieldname = $key;

		if ($ok and !in_array($key, $exceptions))
			return $fieldname;

		return '';
	}

	public static function insertRecords2($realtablename, $sets)
	{
		$db = Factory::getDBO();

		if ($db->serverType == 'postgresql') {
			$set_fieldnames = array();
			$set_values = array();
			foreach ($sets as $set) {
				$break_sets = explode('=', $set);
				$set_fieldnames[] = $break_sets[0];
				$set_values[] = $break_sets[1];
			}

			$query = 'INSERT INTO ' . $realtablename . ' (' . implode(',', $set_fieldnames) . ') VALUES (' . implode(',', $set_values) . ')';
		} else {
			$query = 'INSERT ' . $realtablename . ' SET ' . implode(', ', $sets);

		}
		$db->setQuery($query);
		$db->execute();
		return $db->insertid();
	}
}
