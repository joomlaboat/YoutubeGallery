<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
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


                JToolBarHelper::addNew('categoryform.add');
                JToolBarHelper::editList('categoryform.edit');
                JToolBarHelper::custom( 'categories.copyItem', 'copy.png', 'copy_f2.png', 'Copy', true);
                JToolBarHelper::deleteList('', 'categories.delete');

        }
}     //class
