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
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Version;

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
        $version = new Version;
        $this->version = (int)$version->getShortVersion();

        // Assign the variables
        $this->form = $this->get('Form');
        $this->item = $this->get('Item');
        $this->script = $this->get('Script');
        $this->state = $this->get('State');
        // get action permissions

        $this->canDo = ContentHelper::getActions('com_youtubegallery', 'linkslist', $this->item->id);

        $this->canCreate = $this->canDo->get('linkslist.create');
        $this->canEdit = $this->canDo->get('linkslist.edit');
        $this->canState = $this->canDo->get('linkslist.edit.state');
        $this->canDelete = $this->canDo->get('linkslist.delete');

        // get input
        $jinput = Factory::getApplication()->input;
        $this->ref = Factory::getApplication()->input->get('ref', 0, 'word');
        $this->refid = Factory::getApplication()->input->get('refid', 0, 'int');
        $this->referral = '';
        if ($this->refid) {
            // return to the item that refered to this item
            $this->referral = '&ref=' . (string)$this->ref . '&refid=' . (int)$this->refid;
        } elseif ($this->ref) {
            // return to the list view that refered to this item
            $this->referral = '&ref=' . (string)$this->ref;
        }

        // Set the toolbar
        $this->addToolBar();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->setDocument();
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        $jinput = Factory::getApplication()->input;
        $jinput->get->set('hidemainmenu', true);

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
        $document = Factory::getDocument();
        $document->setTitle($isNew ? JText::_('COM_YOUTUBEGALLERY_LINKSFORM_NEW') : JText::_('COM_YOUTUBEGALLERY_LINKSFORM_EDIT'));
        $document->addScript(JURI::root() . "components/com_youtubegallery/js/submitbutton.js");

        JText::script('COM_YOUTUBEGALLERY_FORMEDIT_ERROR_UNACCEPTABLE');
    }
}//class
