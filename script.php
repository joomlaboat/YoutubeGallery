<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use CustomTables\IntegrityChecks;
use CustomTables\ImportTables;

class com_YoutubeGalleryInstallerScript
{
    function postflight($route, $adapter)
    {
        com_YoutubeGalleryInstallerScript::enableButtonPlugin();
		
		$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'customtables' . DIRECTORY_SEPARATOR;

		$esfile = $path.'loader.php';
			
		if(!file_exists($esfile))
		{
			JFactory::getApplication()->enqueueMessage('Youtube Gallery is corrupted, please contact the developer.','error');

			return false;
		}
		
		require_once($path.'loader.php');
		CTLoader();
		
		//Check Custom Tables, create if nessesary
		$result = IntegrityChecks::check($check_core_tables = true, $check_custom_tables = false);

		$component_name='com_youtubegallery';
		
		$filename = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . $component_name . DIRECTORY_SEPARATOR
			. 'importfiles' . DIRECTORY_SEPARATOR . 'youtubegallery_tables.txt';
		
		$msg='';
		
		$status=ImportTables::processFile($filename,$menutype='YoutubeGallery',$msg);

		if($msg!='')
		{
			JFactory::getApplication()->enqueueMessage($msg,'error');
			return false;
		}
		
		com_YoutubeGalleryInstallerScript::updateYGv3tov4();

		return true;
    }
	
    protected static function enableButtonPlugin()
    {
        $db = JFactory::getDbo();
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
		
		$map = ['option','value'];
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_settings', '#__customtables_table_youtubegallerysettings',$map);
		
		$map = ['categoryname','parentid','description','image'];
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_categories', '#__customtables_table_youtubegallerycategories',$map);
		
		$map = ['themename','playvideo','width','height','repeat','fullscreen','autoplay','related','bgcolor','cssstyle','navbarstyle','thumbnailstyle',
			'listnamestyle','descr_style'=>'es_descrstyle','color1'=>'es_colorone','color2'=>'es_colortwo',
			'border','openinnewwindow','rel','hrefaddon',
			'customlimit','controls','youtubeparams','useglass','logocover','customlayout','prepareheadtags','muteonplay','lastplaylistupdate',
			'volume','orderby','customnavlayout','responsive','mediafolder','headscript','themedescription','nocookie','changepagetitle','allowplaylist'];
			

		$ignore_map = ['showtitle','showinfo','cols','linestyle','showlistname','showactivevideotitle',
			'activevideotitlestyle','description','pagination','playertype','readonly','cache','enablecache','randomization'];
			
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_themes', '#__customtables_table_youtubegallerythemes',$map,$ignore_map);

		//'listname'
		$map = ['videolist','catid','updateperiod','lastplaylistupdate','datetime','description','watchusergroup','authorurl','image','note'];
		$ignore_map = ['author'];
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_videolists', '#__customtables_table_youtubegalleryvideolists',$map,$ignore_map);

	
		$map = ['custom_imageurl'=>'es_customimageurl','custom_title'=>'es_customtitle','custom_description'=>'es_customdescription',
		'rating_average'=>'es_ratingaverage','rating_max'=>'es_ratingmax','rating_min'=>'es_ratingmin','rating_numRaters'=>'es_ratingnumberofraters',
		'statistics_favoriteCount'=>'es_statisticsfavoritecount','statistics_viewCount'=>'es_statisticsviewcount',
		'channel_username'=>'es_channelusername','channel_title'=>'es_channeltitle','channel_subscribers'=>'es_channelsubscribers',
		'channel_subscribed'=>'es_channelsubscribed','channel_location'=>'es_channellocation','channel_commentcount'=>'es_channelcommentcount',
		'channel_viewcount'=>'es_channelviewcount','channel_videocount'=>'es_channelvideocount','channel_description'=>'es_channeldescription',
		'channel_totaluploadviews'=>'es_channel_totaluploadviews','listid'=>'es_videolist',
		'latitude'=>['name'=>'es_latitude','type'=>'float'],
		'longitude'=>['name'=>'es_longitude','type'=>'float'],
		'altitude'=>['name'=>'es_altitude','type'=>'int']
		
		];
		
		$ignore_map = ['volume'];
		
		com_YoutubeGalleryInstallerScript::updateYGv3table('#__youtubegallery_videos', '#__customtables_table_youtubegalleryvideos',$map,$ignore_map);
	}
	
	function updateYGv3table($old_table,$new_table,$map,$exceptions=array())
	{
		if(!ESTables::checkIfTableExists($old_table))
			return false;
	
		$db = JFactory::getDBO();	
		$query = 'SELECT COUNT(*) AS c FROM '.$new_table.' LIMIT 1';
		$db->setQuery( $query );
		$records = $db->loadAssocList();
		
		if((int)$records[0]['c'] > 0)
			return false;
		
		$query = 'SELECT * FROM '.$old_table.' LIMIT 100';
		$db->setQuery( $query );

		$records = $db->loadAssocList();
		
		foreach($records as $record)
		{
			$id=ImportTables::insertRecords($new_table,$record,false,$exceptions,true,'es_',$map);//insert single new record
		}

		return false;
	}
}
