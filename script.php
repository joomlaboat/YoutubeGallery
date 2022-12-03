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
use Joomla\CMS\Factory;


if (!function_exists('str_contains')) {
    function str_contains($haystack, $needle): bool
    {
        return $needle !== '' && mb_strpos($haystack, $needle) !== false;
    }
}

class com_YoutubeGalleryInstallerScript
{
    function postflight($route, $adapter)
    {
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
        CTLoader($include_utilities = true, false, null, $component_name);
        $ct = new CT;

        //Check Custom Tables, create if necessary
        $result = IntegrityChecks::check($ct, $check_core_tables = true, $check_custom_tables = false);

        //* PHP 7.3 - code
        //self::createTablesPHP73();

        $filename = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $component_name . DIRECTORY_SEPARATOR
            . 'importfiles' . DIRECTORY_SEPARATOR . 'youtubegallery_tables.txt';

        $msg = '';

        $path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR
            . 'customtables' . DIRECTORY_SEPARATOR;
        $path_utilities = $path . 'utilities' . DIRECTORY_SEPARATOR;

        require_once($path_utilities . 'importtables.php');
        $status = ImportTables::processFile($filename, $menutype = 'YoutubeGallery', $msg);

        //echo 'Status: ' . $status . '<br/>';
        //die;
        if ($msg != '') {
            Factory::getApplication()->enqueueMessage($msg, 'error');
            return false;
        }

        com_YoutubeGalleryInstallerScript::updateYGv3tov4();
        //die;
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
        if (!ESTables::checkIfTableExists($old_table))
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

    function createTablesPHP73()
    {
        $db = Factory::getDBO();

        $queries = array();
        $queries[] = "CREATE TABLE #__customtables_table_youtubegallerycategories (
    id int NOT NULL,
  published tinyint NOT NULL DEFAULT '1',
  es_categoryname varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Category Name',
  es_parentid int DEFAULT NULL COMMENT 'Parent',
  es_description text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Description',
  es_image varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Image'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Youtube Gallery Categories';";

        $queries[] = "CREATE TABLE #__customtables_table_youtubegallerykeytypes (
    id int UNSIGNED NOT NULL,
  published tinyint(1) NOT NULL DEFAULT '1',
  es_name varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  es_youtuvedataapikey varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='YoutubeGallery Key Types';";

        $queries[] = "CREATE TABLE #__customtables_table_youtubegalleryrequests (
    id int UNSIGNED NOT NULL,
  published tinyint(1) NOT NULL DEFAULT '1',
  es_link varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  es_videoitems varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  es_key varchar(128) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  es_serveraddress varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  es_created datetime DEFAULT NULL,
  es_status int DEFAULT NULL,
  es_count int DEFAULT NULL,
  es_isnew tinyint NOT NULL DEFAULT '0',
  es_rawdata text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  es_keytype int DEFAULT NULL,
  es_youtuvedataapikey varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='YoutubeGallery Requests';";

        $queries[] = "CREATE TABLE #__customtables_table_youtubegallerysettings (
    id int NOT NULL,
  published tinyint NOT NULL DEFAULT '1',
  es_option varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Option',
  es_value varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Value'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Youtube Gallery Settings';";

        $queries[] = "CREATE TABLE #__customtables_table_youtubegallerythemes (
    id int NOT NULL,
  published tinyint NOT NULL DEFAULT '1',
  es_themename varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Theme name',
  es_width int DEFAULT NULL COMMENT 'Player Area Width',
  es_height int DEFAULT NULL COMMENT 'Player Area Height',
  es_playvideo tinyint NOT NULL DEFAULT '0' COMMENT 'Show First Video',
  es_orderby varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Order By',
  es_customlimit int DEFAULT NULL COMMENT 'Pagination Limit',
  es_navbarstyle varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Navigation bar CSS Style',
  es_bgcolor varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thumbnail Background color',
  es_thumbnailstyle varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Thumbnail CSS Style',
  es_listnamestyle varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Video List Name CSS Style',
  es_activevideotitlestyle varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Active Video Title CSS Style',
  es_descrstyle varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Active Video Description CSS Style',
  es_cssstyle varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'CSS Style',
  es_autoplay tinyint NOT NULL DEFAULT '0' COMMENT 'Autoplay',
  es_repeat tinyint NOT NULL DEFAULT '0' COMMENT ' Repeat (loop single video)',
  es_fullscreen tinyint NOT NULL DEFAULT '0' COMMENT 'Fullscreen',
  es_allowplaylist tinyint NOT NULL DEFAULT '0' COMMENT 'Allow Playlist',
  es_related tinyint NOT NULL DEFAULT '0' COMMENT 'Related Videos',
  es_controls tinyint NOT NULL DEFAULT '0' COMMENT 'Controls',
  es_border tinyint NOT NULL DEFAULT '0' COMMENT 'Show Border',
  es_colorone varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Primary Border Color',
  es_colortwo varchar(8) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT ' Mute Player Yes No Initial Volume (0-100), -1 for system default Youtube Parameters',
  es_muteonplay tinyint NOT NULL DEFAULT '0' COMMENT 'Mute Player',
  es_volume int DEFAULT NULL COMMENT 'Initial Volume (0-100), -1 for system default',
  es_youtubeparams varchar(450) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Youtube Parameters',
  es_customlayout text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Custom Layout',
  es_customnavlayout text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Thumbnail Layout',
  es_headscript text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'HTML document head script',
  es_openinnewwindow int DEFAULT NULL COMMENT 'Switch Video As',
  es_rel varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Rel option to apply any shadow/lightbox',
  es_hrefaddon varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'HREF addon',
  es_useglass tinyint NOT NULL DEFAULT '0' COMMENT 'Use glass cover',
  es_logocover varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Logo Cover',
  es_prepareheadtags int DEFAULT NULL COMMENT 'Prepare Head Tags',
  es_changepagetitle int DEFAULT NULL COMMENT 'Change Page Title',
  es_responsive int DEFAULT NULL COMMENT 'Responsive',
  es_nocookie tinyint NOT NULL DEFAULT '0' COMMENT 'No Cookie',
  es_mediafolder varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Media Folder',
  es_themedescription text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Theme Description'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Youtube Gallery Themes';";

        $queries[] = "CREATE TABLE #__customtables_table_youtubegalleryvideolists (
    id int NOT NULL,
  published tinyint NOT NULL DEFAULT '1',
  es_listname varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'List Name',
  es_videolist text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Video Links (Source)',
  es_catid int DEFAULT NULL COMMENT 'Category',
  es_updateperiod decimal(20,2) DEFAULT NULL COMMENT 'Cache Update Period',
  es_description text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'Video List Description',
  es_authorurl varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Link to the author page',
  es_watchusergroup int UNSIGNED DEFAULT NULL COMMENT ' Who may watch the Videos',
  es_image varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Image',
  es_note varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Additional Note',
  es_lastplaylistupdate datetime DEFAULT NULL COMMENT 'Last playlist update time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Youtube Gallery Video Lists ';";

        $queries[] = "CREATE TABLE #__customtables_table_youtubegalleryvideos (
    id int NOT NULL,
  published tinyint NOT NULL DEFAULT '1',
  es_videosource varchar(30) DEFAULT NULL COMMENT 'Video Source',
  es_videoid varchar(128) DEFAULT NULL COMMENT 'Video ID',
  es_imageurl varchar(1024) DEFAULT NULL COMMENT 'Image URL',
  es_description text COMMENT 'Description',
  es_customimageurl varchar(1024) DEFAULT NULL COMMENT 'Custom Image URL',
  es_customtitle varchar(1024) DEFAULT NULL COMMENT 'Custom Title',
  es_customdescription text COMMENT 'Custom Description',
  es_specialparams varchar(255) DEFAULT NULL COMMENT 'Special Params',
  es_lastupdate datetime DEFAULT NULL COMMENT 'Last Update ',
  es_allowupdates tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Allow Updates',
  es_status int DEFAULT NULL COMMENT 'Status',
  es_isvideo tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Is Video',
  es_link varchar(1024) DEFAULT NULL COMMENT 'Link',
  es_ordering int DEFAULT NULL COMMENT 'Ordering',
  es_publisheddate datetime DEFAULT NULL COMMENT 'Published Date',
  es_duration int DEFAULT NULL COMMENT 'Duration',
  es_ratingaverage decimal(20,2) DEFAULT NULL COMMENT 'Rating Average',
  es_ratingmax int DEFAULT NULL COMMENT 'Rating Max',
  es_ratingmin int DEFAULT NULL COMMENT 'Rating Min',
  es_ratingnumberofraters int DEFAULT NULL COMMENT 'Rating Number of Raters',
  es_statisticsfavoritecount int DEFAULT NULL COMMENT 'Statistics Favorite Count',
  es_statisticsviewcount int DEFAULT NULL COMMENT 'StatisticsView Count',
  es_keywords varchar(1024) DEFAULT NULL COMMENT 'Keywords',
  es_startsecond int DEFAULT NULL COMMENT 'Start Second ',
  es_endsecond int DEFAULT NULL COMMENT 'End Second',
  es_likes int DEFAULT NULL COMMENT 'Likes',
  es_dislikes int DEFAULT NULL COMMENT 'Dislikes',
  es_commentcount int DEFAULT NULL COMMENT 'Comment Count ',
  es_channelusername varchar(255) DEFAULT NULL COMMENT 'Channel Username',
  es_channeltitle varchar(255) DEFAULT NULL COMMENT 'Channel Title',
  es_channelsubscribers int DEFAULT NULL COMMENT 'Channel Subscribers',
  es_channelsubscribed int DEFAULT NULL COMMENT 'Channel Subscribed',
  es_channellocation varchar(5) DEFAULT NULL COMMENT 'Channel Location',
  es_channelcommentcount int DEFAULT NULL COMMENT 'Channel Comment Count',
  es_channelviewcount int DEFAULT NULL COMMENT 'Channel Viewcount',
  es_channelvideocount int DEFAULT NULL COMMENT 'Channel Videocount',
  es_channeldescription text COMMENT 'Channel Description',
  es_channeltotaluploadviews int DEFAULT NULL COMMENT 'Channel Total Upload Views',
  es_alias varchar(255) DEFAULT NULL COMMENT 'Alias',
  es_rawdata text COMMENT 'Rawdata',
  es_datalink varchar(1024) DEFAULT NULL COMMENT 'Data Link',
  es_error varchar(1024) DEFAULT NULL COMMENT 'Error',
  es_title varchar(1024) DEFAULT NULL COMMENT 'Title',
  es_trackid varchar(128) DEFAULT NULL COMMENT 'Track ID',
  es_videoids text COMMENT 'Video IDs',
  es_latitude decimal(20,7) DEFAULT NULL,
  es_longitude decimal(20,7) DEFAULT NULL,
  es_altitude int DEFAULT NULL COMMENT 'Altitude',
  es_videolist int DEFAULT NULL COMMENT 'listid',
  es_parentid int DEFAULT NULL COMMENT 'Parent (Playlist or Channel)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Youtube Gallery Videos';";

        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerycategories ADD PRIMARY KEY (id);";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerykeytypes ADD PRIMARY KEY (id);";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegalleryrequests ADD PRIMARY KEY (id);";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerysettings ADD PRIMARY KEY (id);";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerythemes ADD PRIMARY KEY (id);";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegalleryvideolists ADD PRIMARY KEY (id);";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegalleryvideos ADD PRIMARY KEY (id);";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerycategories MODIFY id int NOT NULL AUTO_INCREMENT;";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerykeytypes MODIFY id int UNSIGNED NOT NULL AUTO_INCREMENT;";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegalleryrequests MODIFY id int UNSIGNED NOT NULL AUTO_INCREMENT;";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerysettings MODIFY id int NOT NULL AUTO_INCREMENT;";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegallerythemes MODIFY id int NOT NULL AUTO_INCREMENT;";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegalleryvideolists MODIFY id int NOT NULL AUTO_INCREMENT;";
        $queries[] = "ALTER TABLE #__customtables_table_youtubegalleryvideos MODIFY id int NOT NULL AUTO_INCREMENT;";

        foreach ($queries as $query) {
            $db->setQuery($query);
            $db->execute();
        }

    }
}
