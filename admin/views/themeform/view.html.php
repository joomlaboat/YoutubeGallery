<?php
/**
 * YoutubeGallery Joomla! Native Component
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
 * Youtube Gallery Theme Form View
 */
class YoutubeGalleryViewThemeForm extends JViewLegacy
{
        /**
         * display method of Youtube Gallery view
         * @return void
         */
        public function display($tpl = null)
        {
			$jinput = JFactory::getApplication()->input;
			$task=$jinput->getCmd('task','');
			if($task=='gettags')
				$this->getTags();
			
			
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

		function getTags()
		{
			$file=JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'tags.xml';

			if(!file_exists($file))
			{
				JFactory::getApplication()->enqueueMessage(JoomlaBasicMisc::JTextExtended('COM_YOUTUBEGALLERY_FILE_NOT_FOUND'), 'error');
				return;
			}

			$content=file_get_contents($file);

			$parts=explode('/',$file);
			$filename=end($parts);

			if (ob_get_contents()) ob_end_clean();

   @header('Content-Type: text/xml');
   @header("Pragma: public");
   @header("Expires: 0");
   @header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
   @header("Cache-Control: public");
   @header("Content-Description: File Transfer");
   /*header("Content-type: application/octet-stream");*/
   @header("Content-Disposition: attachment; filename=\"".$filename."\"");
   @header("Content-Transfer-Encoding: binary");

   echo $content;

   die;
		}
		

        protected function addToolBar()
        {
                $jinput = JFactory::getApplication()->input;
		$jinput->get->set('hidemainmenu',true);

                $isNew = ($this->item->id == 0);
                JToolBarHelper::title($isNew ? JText::_('COM_YOUTUBEGALLERY_THEME_NEW') : JText::_('COM_YOUTUBEGALLERY_THEME_EDIT'));
                JToolBarHelper::apply('themeform.apply');
                JToolBarHelper::save('themeform.save');
                JToolBarHelper::cancel('themeform.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
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
                $document->setTitle($isNew ? JText::_('COM_YOUTUBEGALLERY_THEME_NEW') : JText::_('COM_YOUTUBEGALLERY_THEME_EDIT'));
                $document->addScript(JURI::root() . "/administrator/components/com_youtubegallery/js/submitbutton.js");
                
                JText::script('COM_YOUTUBEGALLERY_FORMEDIT_ERROR_UNACCEPTABLE');
        }
}
