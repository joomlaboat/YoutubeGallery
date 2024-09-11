<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

class YoutubeGalleryViewVideoList extends HtmlView
{
    var $listid;
    /**
     * Video Lists view display method
     * @return void
     */
    private $isEmptyState = false;

    function display($tpl = null)
    {
        $version = new Version;
        $this->version = (int)$version->getShortVersion();

        $app = Factory::getApplication();
        $this->listid = $app->input->getInt('listid', 0);

        if ($this->getLayout() !== 'modal' and $this->version < 4) {
            // Include helper submenu
            YoutubeGalleryHelper::addSubmenu('videolists');
        }

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->user = Factory::getUser();

        if ($this->version >= 4) {
            $this->filterForm = $this->get('FilterForm');
            $this->activeFilters = $this->get('ActiveFilters');
        }

        $this->listOrder = $this->escape($this->state->get('list.ordering'));
        $this->listDirn = $this->escape($this->state->get('list.direction'));

        $this->isEmptyState = $this->get('IsEmptyState');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            if ($this->version < 4) {
                $this->addToolbar_3();
                //$this->sidebar = JHtmlSidebar::render();
            } else
                $this->addToolbar_4();
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        // Set the document
        $this->document = Factory::getDocument();
        $this->setDocument($this->document);

        // Display the template
        if ($this->version < 4)
            parent::display($tpl);
        else
            parent::display('quatro');
    }

    protected function addToolBar_3()
    {
        $jinput = Factory::getApplication()->input;
        $jinput->get->set('hidemainmenu', true);

        JToolBarHelper::title(Text::_('COM_YOUTUBEGALLERY_VIDEO_LIST'));
        JToolBarHelper::cancel('videolist.cancel', 'JTOOLBAR_CLOSE');
    }

    protected function addToolbar_4()
    {
        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('COM_YOUTUBEGALLERY_VIDEO_LIST'), 'joomla');
    }

    public function setDocument(Joomla\CMS\Document\Document $document): void
    {
        $document->setTitle(Text::_('COM_YOUTUBEGALLERY_VIDEO_LIST'));
    }
}
