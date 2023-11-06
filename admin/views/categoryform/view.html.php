<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Version;

/**
 * Youtube Category Form
 */
class YoutubeGalleryViewCategoryForm extends JViewLegacy
{
    /**
     * display method of Youtube Gallery view
     * @return void
     */
    var $item;

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

        $this->canDo = ContentHelper::getActions('com_youtubegallery', 'categories', $this->item->id);

        $this->canCreate = $this->canDo->get('categories.create');
        $this->canEdit = $this->canDo->get('categories.edit');
        $this->canState = $this->canDo->get('categories.edit.state');
        $this->canDelete = $this->canDo->get('categories.delete');

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

        // Set the document
        $this->document = Factory::getDocument();
        $this->setDocument($this->document);

        // Display the template
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
    {
        $jinput = Factory::getApplication()->input;
        $jinput->get->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        JToolBarHelper::title($isNew ? JText::_('COM_YOUTUBEGALLERY_NEW_CATEGORY') : JText::_('COM_YOUTUBEGALLERY_EDIT_CATEGORY'));
        JToolBarHelper::apply('categoryform.apply');
        JToolBarHelper::save('categoryform.save');
        JToolBarHelper::cancel('categoryform.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
    }

    /**
     * Method to set up the document properties
     *
     * @return void
     */
    public function setDocument(Joomla\CMS\Document\Document $document): void
    {
        if (isset($this->item) and $this->item !== null) {
            $isNew = ($this->item->id < 1);
            $document->setTitle($isNew ? JText::_('COM_YOUTUBEGALLERY_NEW_CATEGORY') : JText::_('COM_YOUTUBEGALLERY_EDIT_CATEGORY'));
            $document->addScript(JURI::root() . "components/com_youtubegallery/js/submitbutton.js");
            JText::script('COM_YOUTUBEGALLERY_CATEGORYFORM_ERROR_UNACCEPTABLE');
        }
    }
}
