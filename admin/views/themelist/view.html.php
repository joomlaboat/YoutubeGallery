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

class YoutubeGalleryViewThemeList extends JViewLegacy
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

                $context= 'com_youtubegallery.themelist.';
                $mainframe = JFactory::getApplication();
                $search			= $mainframe->getUserStateFromRequest($context."search",'search','',	'string' );
                $search			= JString::strtolower( $search );

                $lists['search']=$search;



                $javascript = 'onchange="document.adminForm.submit();"';


                $this->assignRef('lists', $lists);

                parent::display($tpl);
        }

        protected function addToolBar()
        {
                JToolBarHelper::title(JText::_('COM_YOUTUBEGALLERY_THEMELIST'));


                JToolBarHelper::addNew('themeform.add');
                JToolBarHelper::editList('themeform.edit');
                JToolBarHelper::custom( 'themelist.copyItem', 'copy.png', 'copy_f2.png', 'Copy', true);
				JToolBarHelper::custom( 'themelist.uploadItem', 'upload.png', 'upload_f2.png', 'Import', false);

                JToolBarHelper::deleteList('', 'themelist.delete');

        }

        function array_insert(&$array, $insert, $position = -1)
        {
                $position = ($position == -1) ? (count($array)) : $position ;
                if($position != (count($array))) {
                $ta = $array;
                for($i = $position; $i < (count($array)); $i++)
                {
                        if(!isset($array[$i])) {
                                 die("\r\nInvalid array: All keys must be numerical and in sequence.");
                        }
                        $tmp[$i+1] = $array[$i];
                        unset($ta[$i]);
                }
                $ta[$position] = $insert;
                $array = $ta + $tmp;

                } else {
                     $array[$position] = $insert;
                }
                ksort($array);
                return true;
        }

}
