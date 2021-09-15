<?php
/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * YoutubeGallery component helper.
 */
abstract class YoutubeGalleryHelper
{
	public static function addSubmenu($submenu)
	{
		// load user for access menus
		$user = JFactory::getUser();
		// load the submenus to sidebar

		JSubMenuHelper::addEntry(JText::_('Video Lists'), 'index.php?option=com_youtubegallery&view=linkslist', $submenu === 'linkslist');
		JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_youtubegallery&view=themelist', $submenu === 'themelist');
		JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_youtubegallery&view=categories', $submenu === 'categories');
		JSubMenuHelper::addEntry(JText::_('Settings'), 'index.php?option=com_youtubegallery&view=settings&layout=edit', $submenu === 'settingst');
	
	}
	
	public function setRecordStatus($task, $viewLabel,$tableShortName)
	{
		if($task == 'publish')
			$status = 1;
		elseif($task == 'unpublish')
			$status = 0;
		elseif($task == 'trash')
			$status = -2;
		else
			return;
				
		$cid	= JFactory::getApplication()->input->post->get('cid',array(),'array');
		$cid = ArrayHelper::toInteger($cid);
		
		$ok=true;
		
		foreach($cid as $id)
		{
			if((int)$id!=0)
			{
				$id=(int)$id;
				$isok=YoutubeGalleryHelper::setPublishStatusSingleRecord($id,$status,$tableShortName);
				if(!$isok)
				{
					$ok=false;
					break;
				}
			}
		}
		
		if($task == 'trash')
			$msg = 'COM_YOUTUBEGALLERY_'.$viewLabel.'_N_ITEMS_TRASHED';
		elseif($task == 'publish')
			$msg = 'COM_YOUTUBEGALLERY_'.$viewLabel.'_N_ITEMS_PUBLISHED';
		else
			$msg = 'COM_YOUTUBEGALLERY_'.$viewLabel.'_N_ITEMS_UNPUBLISHED';
			
		if(count($cid) == 1)
			$msg.='_1';
		
		JFactory::getApplication()->enqueueMessage(JText::sprintf($msg,count($cid)),'success');
	}
	
	protected static function setPublishStatusSingleRecord($id,$status,$tableShortName)
	{
		$db = JFactory::getDBO();

		$query = 'UPDATE #__customtables_table_'.$tableShortName.' SET published='.(int)$status.' WHERE id='.(int)$id;

	 	$db->setQuery($query);
		$db->execute();	

		return true;
	}
	
	public static function deleteRecord($viewLabel,$tableShortName)
	{
		$cid	= JFactory::getApplication()->input->post->get('cid',array(),'array');
		$cid = ArrayHelper::toInteger($cid);
		
		$ok=true;
		
		foreach($cid as $id)
		{
			if((int)$id!=0)
			{
				$id=(int)$id;
				$isok=YoutubeGalleryHelper::deleteSingleRecord($id,$tableShortName);
				if(!$isok)
				{
					$ok=false;
					break;
				}
			}
		}
		
		$msg ='COM_YOUTUBEGALLERY_'.$viewLabel.'_N_ITEMS_DELETED';
		if(count($cid) == 1)
			$msg.='_1';
		
		JFactory::getApplication()->enqueueMessage(JText::sprintf($msg,count($cid)),'success');
	}
	
	protected static function deleteSingleRecord($id,$tableShortName)
	{
		$db = JFactory::getDBO();

		$query = 'DELETE FROM #__customtables_table_'.$tableShortName.' WHERE id='.(int)$id;

	 	$db->setQuery($query);
		$db->execute();	

		return true;
	}
	
	/**
	*	Check if have an array with a length
	*
	*	@input	array   The array to check
	*
	*	@returns bool true on success
	**/
	public static function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && count((array)$array) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return self::checkArray($array, false);
			}
			return true;
		}
		return false;
	}
	
	/**
	*	Check if have a string with a length
	*
	*	@input	string   The string to check
	*
	*	@returns bool true on success
	**/
	public static function checkString($string)
	{
		if (isset($string) && is_string($string) && strlen($string) > 0)
		{
			return true;
		}
		return false;
	}
	
	
	/**
	*	Check if have an object with a length
	*
	*	@input	object   The object to check
	*
	*	@returns bool true on success
	**/
	public static function checkObject($object)
	{
		if (isset($object) && is_object($object))
		{
			return count((array)$object) > 0;
		}
		return false;
	}
}
