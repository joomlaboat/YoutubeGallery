<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

// import Joomla view library
//jimport('joomla.application.component.view');

/**
 * Youtube Gallery - Links Form View
 */
class YoutubeGalleryViewLinksForm extends HtmlView
{
    /**
     * display method of Youtube Gallery view
     * @return void
     */
    public function display($tpl = null)
    {
        $version = new Version;
        $this->version = (int)$version->getShortVersion();

        $form = $this->get('Form');
        $item = $this->get('Item');

        // Check for errors.
        //if (count($errors = $this->get('Errors')))
        //if(count($this->get('Errors')))
        //{
        //Factory::getApplication()->enqueueMessage( implode('<br />', $errors), 'error');
        //return false;
        //}

        // Assign the Data
        $this->form = $form;
        $this->item = $item;

        // Set the toolbar
        $this->addToolBar();

        // Display the template
        parent::display($tpl);

        // Set the document
        $this->document = Factory::getDocument();
        $this->setDocument($this->document);//this method must be called after "display" to let validation work properly because the Form must be rendered before validation script
    }

    /**
     * Setting the toolbar
     */
    protected function addToolBar()
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
     * @param Document $document
     * @return void
     */
    public function setDocument($document): void
    {
        $isNew = empty($this->item->id);
        $document = Factory::getDocument();
        $document->setTitle($isNew ? Text::_('COM_YOUTUBEGALLERY_LINKSFORM_NEW') : Text::_('COM_YOUTUBEGALLERY_LINKSFORM_EDIT'));
        $document->addScript(Uri::root() . "/administrator/components/com_youtubegallery/js/submitbutton.js");
    }
}