<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

/**
 * Youtube Gallery - Links Form View
 */
class YoutubeGalleryViewLinksForm extends JViewLegacy
{
        /**
         * display method of Youtube Gallery view
         * @return void
         */
        public function display($tpl = null)
        {
                $form = $this->get('Form');
                $item = $this->get('Item');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JFactory::getApplication()->enqueueMessage( implode('<br />', $errors), 'error');
                        return false;
                }

                // Assign the Data
                $this->form = $form;
                $this->item = $item;

                // Set the toolbar
                $this->addToolBar();

                // Display the template
                parent::display($tpl);
                
                // Set the document
                $this->setDocument();//this method must be called after "display" to let validation work properly because the Form must be rendered before validation script
        }

        /**
         * Setting the toolbar
         */
        protected function addToolBar()
        {
                $jinput = JFactory::getApplication()->input;
                $jinput->get->set('hidemainmenu',true);

                $isNew = ($this->item->id == 0);
                JToolBarHelper::title($isNew ? JText::_('COM_YOUTUBEGALLERY_LINKSFORM_NEW') : JText::_('COM_YOUTUBEGALLERY_LINKSFORM_EDIT'));
                JToolBarHelper::apply('linksform.apply');
                JToolBarHelper::save('linksform.save');
                JToolBarHelper::cancel('linksform.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
        }

        /**
        * Method to set up the document properties
        *
        * @return void
        */
        protected function setDocument()
        {
                $isNew = ($this->item->id < 1);
                $document = JFactory::getDocument();
                $document->setTitle($isNew ? JText::_('COM_YOUTUBEGALLERY_LINKSFORM_NEW') : JText::_('COM_YOUTUBEGALLERY_LINKSFORM_EDIT'));
                $document->addScript(JURI::root(). "components/com_youtubegallery/js/submitbutton.js");
                
                JText::script('COM_YOUTUBEGALLERY_FORMEDIT_ERROR_UNACCEPTABLE');
        }
}//class
