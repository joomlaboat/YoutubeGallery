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
 * Youtube Gallery - Categories Controller
 */

class YoutubeGalleryControllerCategories extends JControllerForm
{
		/**
		 * Proxy for getModel.
		 */
		function display($cachable = false, $urlparams = array())
		{
				switch(JFactory::getApplication()->input->getVar( 'task'))
				{
						case 'delete':
								$this->delete();
								break;
						case 'copyItem':
								$this->copyItem();
								break;
						case 'remove_confirmed':
								$this->remove_confirmed();
								break;
						case 'categories.delete':
								$this->delete();
								break;
						case 'categories.copyItem':
								$this->copyItem();
								break;
						case 'categories.remove_confirmed':
								$this->remove_confirmed();
								break;
						default:
								JFactory::getApplication()->input->setVar( 'view', 'categories');
								parent::display();
								
								break;
				}
		
		}

		public function getModel($name = 'Categories', $prefix = 'YoutubeGalleryModel', $config = array()) 
		{
			    $model = parent::getModel($name, $prefix, array('ignore_request' => true));
		        return $model;
		}
        
 
		public function delete()
		{
			$canDoThemeList = YoutubeGalleryHelper::getActions('categories');
			$canViewThemeList = $canDoThemeList->get('categories.view');
		
			if(!$canViewThemeList)
			{
				$link='index.php?option=com_youtubegallery&view=linkslist';
				$msg = JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED');
				$this->setRedirect($link, $msg, 'error');
				return false;
			}
			
				JFactory::getApplication()->input->setVar( 'view', 'categories');

				// Check for request forgeries
				JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
				        	
				$cid = JFactory::getApplication()->input->getVar( 'cid', array(), 'post', 'array' );

				if (!count($cid)) {
						$this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', JText::_('COM_YOUTUBEGALLERY_NO_CATEGORIES_SELECTED'),'error' );
						return false;
				}
			
				$model = $this->getModel();
				$model->ConfirmRemove();
	        }
	
		public function remove_confirmed()
		{
			$canDoThemeList = YoutubeGalleryHelper::getActions('categories');
			$canViewThemeList = $canDoThemeList->get('categories.view');
		
			if(!$canViewThemeList)
			{
				$link='index.php?option=com_youtubegallery&view=linkslist';
				$msg = JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED');
				$this->setRedirect($link, $msg, 'error');
				return false;
			}
		
				// Get some variables from the request
				$cid	= JFactory::getApplication()->input->getVar( 'cid', array(), 'post', 'array' );
		
		        	if (!count($cid))
				{
						$this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', JText::_('COM_YOUTUBEGALLERY_NO_CATEGORIES_SELECTED'),'error' );
						
						return false;
				}

				$model = $this->getModel('categoryform');
				if ($n = $model->deleteCategory($cid))
				{
					$msg = JText::sprintf( 'COM_YOUTUBEGALLERY_CATEGORY_S_DELETED', $n );
					$this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', $msg );
				} else
				{
						$msg = $model->getError();
						$this->setRedirect( 'index.php?option=com_youtubegallery&view=categories', $msg,'error' );
				}
		}
		
		public function copyItem()
		{
			$canDoCategories = YoutubeGalleryHelper::getActions('categories');
			$canViewCategories = $canDoCategories->get('categories.view');
		
			if(!$canViewCategories)
			{
				$link='index.php?option=com_youtubegallery&view=linkslist';
				$msg = JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED');
				$this->setRedirect($link, $msg, 'error');
				return false;
			}
				
				$cid = JFactory::getApplication()->input->getVar( 'cid', array(), 'post', 'array' );
	    
				$model = $this->getModel('categories');
	    
	    
				if($model->copyItem($cid))
				{
						$msg = JText::_( 'COM_YOUTUBEGALLERY_CATEGORY_COPIED_SUCCESSFULLY' );
				}
				else
				{
						$msg = JText::_( 'COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_COPY' );
				}
	    
				$link 	= 'index.php?option=com_youtubegallery&view=categories';
				$this->setRedirect($link, $msg);
		}
}
