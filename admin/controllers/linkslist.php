<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla controlleradmin library
jimport('joomla.application.component.controlleradmin');
/**
 * Youtube Gallery - LinksList Controller
 */

class YoutubeGalleryControllerLinksList extends JControllerAdmin
{
	/**
	* Proxy for getModel.
	*/

	protected $text_prefix = 'COM_YOUTUBEGALLERY_LINKSLIST';

	public function getModel($name = 'LinksForm', $prefix = 'YoutubeGalleryModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
	}
	
	public function updateItem()
	{
		$model = $this->getModel('linksform');
		$cid = JFactory::getApplication()->input->post->get('cid',array(),'array');

		if (count($cid)<1)
		{
			$this->setRedirect( 'index.php?option=com_youtubegallery&view=linkslist', JText::_('COM_YOUTUBEGALLERY_NO_ITEMS_SELECTED'),'error' );
			return false;
		}
					    	    
		if($model->RefreshPlayist($cid,true))
		{
			$msg = JText::_( 'COM_YOUTUBEGALLERY_VIDEOLIST_UPDATED_SUCCESSFULLY' );
			$link 	= 'index.php?option=com_youtubegallery&view=linkslist';
			$this->setRedirect($link, $msg);
		}
		else
		{
			$msg = JText::_( 'COM_YOUTUBEGALLERY_VIDEOLIST_WAS_UNABLE_TO_UPDATE' );
			$link 	= 'index.php?option=com_youtubegallery&view=linkslist';
			$this->setRedirect($link, $msg,'error');
		}
		
		die;
	}
	
	public function refreshItem()
	{
		$model = $this->getModel('linksform');
		$cid = JFactory::getApplication()->input->post->get('cid',array(),'array');
    
		if (count($cid)<1)
		{
			$this->setRedirect( 'index.php?option=com_youtubegallery&view=linkslist', JText::_('COM_YOUTUBEGALLERY_NO_ITEMS_SELECTED'),'error' );
                
			return false;
		}
					    	    
		if($model->RefreshPlayist($cid,false))
		{
			$msg = JText::_( 'COM_YOUTUBEGALLERY_VIDEOLIST_REFRESHED_SUCCESSFULLY' );
			$link 	= 'index.php?option=com_youtubegallery&view=linkslist';
			$this->setRedirect($link, $msg);
		}
		else
		{
			$msg = JText::_( 'COM_YOUTUBEGALLERY_VIDEOLIST_WAS_UNABLE_TO_REFRESH' );
			$link 	= 'index.php?option=com_youtubegallery&view=linkslist';
			$this->setRedirect($link, $msg,'error');
		}
	}
	
	public function publish()
	{
		YoutubeGalleryHelper::setRecordStatus($this->task,'LINKSLIST','youtubegalleryvideolists');
		
		$redirect = 'index.php?option=' . $this->option;
		$redirect.='&view=linkslist';

		// Redirect to the item screen.
		$this->setRedirect(
			JRoute::_(
				$redirect, false
			)
		);
	}
	
	public function delete()
	{
		YoutubeGalleryHelper::deleteRecord('LINKSLIST','youtubegalleryvideolists');
		
		$redirect = 'index.php?option=' . $this->option;
		$redirect.='&view=linkslist';

		// Redirect to the item screen.
		$this->setRedirect(
			JRoute::_(
				$redirect, false
			)
		);
	}
}

