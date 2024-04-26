<?php
/**
 * YoutubeGallery Joomla! 3.0/4.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerScript;

class YoutubeGalleryTheme_'.$themeName.'InstallerScript
{
	function preflight($route, $adapter)
	{
		YoutubeGalleryTheme_'.$themeName.'InstallerScript::deleteIfExists();
		
		$parent = $adapter->getParent();
		$source = $parent->getPath("source");
		
		if(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'themeimport.php'))
		{
		    $this->addTheme($source);
		}
		else
		{
		    echo '<h1 style="text-align:center;">Youtube Gallery not found. Please install it first.<br/>
		    <a href="https://joomlaboat.com/youtube-gallery">Youtube Gallery Home Page.</a>
		    </h1><br/><br/>';
		    return false;
		}
	}
	
	function postflight($route, $adapter)
    {
		YoutubeGalleryTheme_'.$themeName.'InstallerScript::deleteIfExists();
	}

	function deleteIfExists()
	{
		//delete extension - type="file"
		$db = Factory::getDbo();
		$db->setQuery('DELETE FROM #__extensions WHERE name = "YoutubeGalleryTheme_'.$themeName.'" ');
		$db->execute();
	}

	function addTheme($path)
	{
	    if(file_exists($path.DIRECTORY_SEPARATOR.'theme.txt'))
	    {
			require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'models'.DIRECTORY_SEPARATOR.'themeimport.php');
			$ygmti= new YoutubeGalleryModelThemeImport;

			//Ok archive is fine, looks like it is really YG theme.
			$filedata=file_get_contents ($path.DIRECTORY_SEPARATOR.'theme.txt');
			if($filedata=='')
			{
				//Archive doesn't containe Theme Data
				$msg='Theme Data file is empty';
				echo '<h1>'.$msg.'</h1>';
				return;
			}

			$theme_row=unserialize($filedata);
			
			//Add record to database
			$theme_row->themename=$ygmti->getThemeName(str_replace('"','',$theme_row->es_themename));
			if($theme_row->es_themename!='')
			{
				if(file_exists($path.DIRECTORY_SEPARATOR.'about.txt'))
					$theme_row->es_themedescription=file_get_contents ($path.DIRECTORY_SEPARATOR.'about.txt');
				else
					$theme_row->es_themedescription="";


				if($theme_row->es_mediafolder!='')
				{
					//prepare media folder
					$theme_row->es_mediafolder=$ygmti->prepareFolder($theme_row->es_mediafolder,JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR);
					echo '<p>Media Folder "'.$theme_row->es_mediafolder.'" created.</p>';
					//move files
					$ygmti->moveFiles($path,JPATH_SITE.DIRECTORY_SEPARATOR.'images'.DIRECTORY_SEPARATOR.$theme_row->es_mediafolder);
				}

				echo '<p>New Theme Name: '.$theme_row->es_themename.'</p>';

				$ygmti->saveTheme($theme_row);
				echo '<p>Theme Imported</p>';
			}
			else
				return false;
	    }
	    else
	    {
			echo '<h1>File "theme.txt" not found.</h1>';
			$msg='Archive doesn\'t contain Gallery Data.';
			return false;
	    }
	}
}
