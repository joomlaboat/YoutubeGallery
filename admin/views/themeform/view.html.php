<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Version;

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
				
		$version = new Version;
		$this->version = (int)$version->getShortVersion();
		
		// Assign the variables
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');
		$this->state = $this->get('State');
		// get action permissions

		$this->canDo = ContentHelper::getActions('com_youtubegallery', 'themelist',$this->item->id);
		
		$this->canCreate = $this->canDo->get('themelist.create');
		$this->canEdit = $this->canDo->get('themelist.edit');
		$this->canState = $this->canDo->get('themelist.edit.state');
		$this->canDelete = $this->canDo->get('themelist.delete');
		
		// get input
		$jinput = JFactory::getApplication()->input;
		$this->ref = JFactory::getApplication()->input->get('ref', 0, 'word');
		$this->refid = JFactory::getApplication()->input->get('refid', 0, 'int');
		$this->referral = '';
		if ($this->refid)
		{
			// return to the item that refered to this item
			$this->referral = '&ref='.(string)$this->ref.'&refid='.(int)$this->refid;
		}
		elseif($this->ref)
		{
			// return to the list view that refered to this item
			$this->referral = '&ref='.(string)$this->ref;
		}

		// Set the toolbar
		$this->addToolBar();
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		if($this->version < 4)
			parent::display($tpl);
		else
			parent::display('quatro');

		// Set the document
		$this->setDocument();
	}
	
		 function getTags()
	{
		$file=JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'xml'.DIRECTORY_SEPARATOR.'tags506.xml';

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
		$document->addScript(JURI::root()."components/com_youtubegallery/js/submitbutton.js");
		JText::script('COM_YOUTUBEGALLERY_FORMEDIT_ERROR_UNACCEPTABLE');
	}
}
