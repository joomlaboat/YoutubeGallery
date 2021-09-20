<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Helper\ContentHelper;

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * YoutubeGallery - LinksForm Controller
 */
class YoutubeGalleryControllerSettings extends JControllerForm
{
    function display($cachable = false, $urlparams = array())
	{
		$jinput = JFactory::getApplication()->input;
		
		$this->canDo = ContentHelper::getActions('com_youtubegallery', 'settings');
		$this->canView = $this->canDo->get('settings.view');
		$this->canEdit = $this->canDo->get('settings.edit');
		
		if(!$this->canView)
		{
			$link='index.php?option=com_youtubegallery&view=linkslist';
			$msg = JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED');
			$this->setRedirect($link, $msg, 'error');
			return true;
		}
	}
       
	function save($key = NULL, $urlVar = NULL)
	{
		$this->canDo = ContentHelper::getActions('com_youtubegallery', 'settings');
		
		$this->canView = $this->canDo->get('settings.view');
		$this->canEdit = $this->canDo->get('settings.edit');
		
		if(!$this->canView or !$this->canEdit)
		{
			$link='index.php?option=com_youtubegallery&view=linkslist';
			$msg = JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED');
			$this->setRedirect($link, $msg, 'error');
			return false;
		}


		$task = JFactory::getApplication()->input->getVar( 'task');
		
		// get our model
		$model = $this->getModel('settings');
		// attempt to store, update user accordingly
		
		if($task != 'save' and $task != 'apply' and $task != 'settings.save' and $task != 'settings.apply' )
		{
			$msg = JText::_( 'COM_YOUTUBEGALLERY_SETTINGS_WAS_UNABLE_TO_SAVE');
			$this->setRedirect($link, $msg, 'error');
			return false;
		}
		
		if ($model->store())
		{
			$link 	= 'index.php?option=com_youtubegallery&view=settings&layout=edit';
			$msg = JText::_( 'COM_YOUTUBEGALLERY_SETTINGS_SAVED_SUCCESSFULLY' );
			
			$this->setRedirect($link, $msg);
		}
		else
		{
			$link 	= 'index.php?option=com_youtubegallery&view=settings&layout=edit';
			$msg = JText::_( 'COM_YOUTUBEGALLERY_SETTINGS_WAS_UNABLE_TO_SAVE');
			$this->setRedirect($link, $msg, 'error');
		}
	}
	
	function cancel($key = NULL)
	{
		$this->setRedirect( 'index.php?option=com_youtubegallery&view=linkslist');
	}
}
