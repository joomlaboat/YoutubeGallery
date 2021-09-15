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
}
