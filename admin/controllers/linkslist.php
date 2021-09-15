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

