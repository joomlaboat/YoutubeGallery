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

jimport('joomla.application.component.view');

class YoutubeGalleryViewVideoList extends JViewLegacy
{
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

                $context= 'com_youtubegallery.videoylist.';
                $mainframe = JFactory::getApplication();
                $search			= $mainframe->getUserStateFromRequest($context."search",'search','',	'string' );
                $search			= JString::strtolower( $search );

                $lists['search']=$search;

                $this->assignRef('lists', $lists);

                // Display the template
                parent::display($tpl);
        }

        protected function addToolBar()
        {
                $jinput = JFactory::getApplication()->input;
                $jinput->get->set('hidemainmenu',true);

                JToolBarHelper::title(JText::_('COM_YOUTUBEGALLERY_VIDEO_LIST'));

		JToolBarHelper::cancel('videolist.cancel', 'JTOOLBAR_CLOSE');
        }

}//class
