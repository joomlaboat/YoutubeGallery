<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

/**
 * YouTube Gallery - Links Form View
 */
class YoutubeGalleryViewLinksForm extends HtmlView
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

        // Set the document
        $this->document = Factory::getDocument();
        $this->setDocument($this->document);

        // Display the template
        parent::display($tpl);
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar(): void
    {
        $jinput = Factory::getApplication()->input;
        $jinput->get->set('hidemainmenu', true);

        $isNew = ($this->item->id == 0);
        ToolbarHelper::title($isNew ? Text::_('COM_YOUTUBEGALLERY_LINKSFORM_NEW') : Text::_('COM_YOUTUBEGALLERY_LINKSFORM_EDIT'));
        ToolbarHelper::apply('linksform.apply');
        ToolbarHelper::save('linksform.save');
        ToolbarHelper::cancel('linksform.cancel', $isNew ? 'JTOOLBAR_CANCEL' : 'JTOOLBAR_CLOSE');
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
            $document->setTitle($isNew ? Text::_('COM_YOUTUBEGALLERY_LINKSFORM_NEW') : Text::_('COM_YOUTUBEGALLERY_LINKSFORM_EDIT'));
            $document->addScript(Uri::root() . "components/com_youtubegallery/js/submitbutton.js");
        }
    }
}
