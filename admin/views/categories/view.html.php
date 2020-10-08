<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * YoutubeGallery Categories View
 */

        //joomla 3.x
class YoutubeGalleryViewCategories extends JViewLegacy
{
        /**
         * YoutubeGallery view display method
         * @return void
         */
        function display($tpl = null)
        {
                // Get data from the model
                $items = $this->get('Items');
                $pagination = $this->get('Pagination');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
					    JFactory::getApplication()->enqueueMessage( implode('<br />', $errors), 'error');
                        return false;
                }
                // Assign data to the view
                $this->items = $items;
                $this->pagination = $pagination;

				$this->canDo = YoutubeGalleryHelper::getActions('categories');
				
				$this->canCreate = $this->canDo->get('categories.create');
				$this->canDelete = $this->canDo->get('categories.delete');
				$this->canEdit = $this->canDo->get('categories.edit');
				$this->canUpdate = $this->canDo->get('categories.update');


                // Set the toolbar
                $this->addToolBar();

                // Display the template
                parent::display($tpl);
        }

        /**
         * Setting the toolbar
        */
        protected function addToolBar()
        {
                JToolBarHelper::title(JText::_('COM_YOUTUBEGALLERY_CATEGORIES'));


				if($this->canCreate)
					JToolBarHelper::addNew('categoryform.add');
				
				if($this->canEdit)
					JToolBarHelper::editList('categoryform.edit');
				
				if($this->canCreate)
					JToolBarHelper::custom( 'categories.copyItem', 'copy.png', 'copy_f2.png', 'Copy', true);
				
				if($this->canDelete)
					JToolBarHelper::deleteList('', 'categories.delete');

        }
}     //class
