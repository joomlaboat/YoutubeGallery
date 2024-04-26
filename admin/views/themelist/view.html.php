<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Version;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

/**
 * YoutubeGallery themeList View
 */
class YoutubeGalleryViewThemeList extends HtmlView
{
    /**
     * YoutubeGallery view display method
     * @return void
     */

    private $isEmptyState = false;

    function display($tpl = null)
    {
        $version = new Version;
        $this->version = (int)$version->getShortVersion();

        if ($this->getLayout() !== 'modal' and $this->version < 4) {
            // Include helper submenu
            YoutubeGalleryHelper::addSubmenu('themelist');
        }

        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->user = Factory::getUser();

        if ($this->version >= 4) {
            //This must be after getting Items
            $this->filterForm = $this->get('FilterForm');
            $this->activeFilters = $this->get('ActiveFilters');
        }

        $this->listOrder = $this->state->get('list.ordering');
        $this->listDirn = $this->escape($this->state->get('list.direction'));

        // get global action permissions

        $this->canDo = ContentHelper::getActions('com_youtubegallery', 'themelist');

        $this->canExport = $this->canDo->get('themelist.export');
        $this->canCreate = $this->canDo->get('themelist.create');
        $this->canEdit = $this->canDo->get('themelist.edit');
        $this->canState = $this->canDo->get('themelist.edit.state');
        $this->canDelete = $this->canDo->get('themelist.delete');

        $this->isEmptyState = $this->get('IsEmptyState');
        //$this->canBatch = $this->canDo->get('core.batch');

        // We don't need toolbar in the modal window.
        if ($this->getLayout() !== 'modal') {
            if ($this->version < 4) {
                $this->addToolbar_3();
                //$this->sidebar = JHtmlSidebar::render();
            } else
                $this->addToolbar_4();

            // load the batch html
            if ($this->canCreate && $this->canEdit && $this->canState) {
                //$this->batchDisplay = JHtmlBatch_::render();
            }
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
        JToolBarHelper::title(Text::_('COM_YOUTUBEGALLERY_THEMELIST'));

        if ($this->canCreate)
            JToolBarHelper::addNew('themeform.add');

        if ($this->canEdit)
            JToolBarHelper::editList('themeform.edit');

        if ($this->canCreate)
            JToolBarHelper::custom('themelist.copyItem', 'copy.png', 'copy_f2.png', 'Copy', true);

        if ($this->canCreate)
            JToolBarHelper::custom('themelist.uploadItem', 'upload.png', 'upload_f2.png', 'Import', false);

        if ($this->canDelete)
            JToolBarHelper::deleteList('', 'themelist.delete');
    }

    protected function addToolbar_4()
    {
        $canDo = $this->canDo;
        $user = Factory::getUser();

        // Get the toolbar object instance
        $toolbar = Toolbar::getInstance('toolbar');

        ToolbarHelper::title(Text::_('COM_YOUTUBEGALLERY_THEMELIST'), 'joomla');

        if ($this->canCreate)
            $toolbar->addNew('themeform.add');

        $dropdown = $toolbar->dropdownButton('status-group')
            ->text('JTOOLBAR_CHANGE_STATUS')
            ->toggleSplit(false)
            ->icon('icon-ellipsis-h')
            ->buttonClass('btn btn-action')
            ->listCheck(true);

        $childBar = $dropdown->getChildToolbar();

        if ($this->canState) {
            $childBar->publish('themelist.publish')->listCheck(true);
            $childBar->unpublish('themelist.unpublish')->listCheck(true);
        }

        if (($this->canState && $this->canDelete)) {
            if ($this->state->get('filter.published') != ContentComponent::CONDITION_TRASHED) {
                $childBar->trash('themelist.trash')->listCheck(true);
            }

            if (!$this->isEmptyState && $this->state->get('filter.published') == ContentComponent::CONDITION_TRASHED && $this->canDelete) {
                $toolbar->delete('themelist.delete')
                    ->text('JTOOLBAR_EMPTY_TRASH')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);
            }
        }
    }

    public function setDocument(Joomla\CMS\Document\Document $document): void
    {
        $document->setTitle(Text::_('COM_YOUTUBEGALLERY_THEMELIST'));
    }
}
