<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controlleradmin');

class YoutubeGalleryControllerThemeList extends JControllerAdmin
{
	/**
	* Proxy for getModel.
	*/
	protected $text_prefix = 'COM_YOUTUBEGALLERY_THEMELIST';

	public function getModel($name = 'LinksForm', $prefix = 'YoutubeGalleryModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		
		return $model;
	}
	
	public function publish()
	{
		YoutubeGalleryHelper::setRecordStatus($this->task,'THEMELIST','youtubegallerythemes');
		
		$redirect = 'index.php?option=' . $this->option;
		$redirect.='&view=themelist';

		// Redirect to the item screen.
		$this->setRedirect(
			JRoute::_(
				$redirect, false
			)
		);
	}
	
	public function delete()
	{
		YoutubeGalleryHelper::deleteRecord('THEMELIST','youtubegallerythemes');
		
		$redirect = 'index.php?option=' . $this->option;
		$redirect.='&view=themelist';

		// Redirect to the item screen.
		$this->setRedirect(
			JRoute::_(
				$redirect, false
			)
		);
	}
	
	public function uploadItem()
	{
		//$canDoThemeList = YoutubeGalleryHelper::getActions('themelist');
		//$canViewThemeList = $canDoThemeList->get('themelist.view');
		
		//if(!$canViewThemeList)
		//{
			//$link='index.php?option=com_youtubegallery&view=linkslist';
			//$msg = JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED');
			//$this->setRedirect($link, $msg, 'error');
			//return false;
		//}
		
		$link 	= 'index.php?option=com_youtubegallery&view=themeimport';
		$this->setRedirect($link, '');
	}
}
