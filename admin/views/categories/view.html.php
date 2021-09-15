<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Version;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\Database\DatabaseDriver;
use Joomla\Component\Content\Administrator\Extension\ContentComponent;

/**
 * YoutubeGallery Categories View
 */
class YoutubeGalleryViewCategories extends JViewLegacy
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
		
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			//YoutubeGalleryHelper::addSubmenu('categories');
		}
		
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		
		if($this->version >= 4)
		{
			//This must be after getting Items
			$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');
		}
		
		$this->listOrder = $this->state->get('list.ordering');
		$this->listDirn = $this->escape($this->state->get('list.direction'));
		
		// get global action permissions

		$this->canDo = ContentHelper::getActions('com_youtubegallery', 'categories');
		
		$this->canCreate = $this->canDo->get('categories.create');
		$this->canEdit = $this->canDo->get('categories.edit');
		$this->canState = $this->canDo->get('categories.edit.state');
		$this->canDelete = $this->canDo->get('categories.delete');
		
		$this->isEmptyState = $this->get('IsEmptyState');
		//$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			if($this->version < 4)
			{
				$this->addToolbar_3();
				$this->sidebar = JHtmlSidebar::render();
			}
			else
				$this->addToolbar_4();
			
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				//$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
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
	
	protected function addToolbar_4()
	{
		$canDo = $this->canDo;
		$user  = Factory::getUser();

		// Get the toolbar object instance
		$toolbar = Toolbar::getInstance('toolbar');

		ToolbarHelper::title(Text::_('COM_YOUTUBEGALLERY_CATEGORIES'), 'joomla');

		if ($this->canCreate)
			$toolbar->addNew('categoryform.add');

		$dropdown = $toolbar->dropdownButton('status-group')
			->text('JTOOLBAR_CHANGE_STATUS')
			->toggleSplit(false)
			->icon('icon-ellipsis-h')
			->buttonClass('btn btn-action')
			->listCheck(true);

		$childBar = $dropdown->getChildToolbar();
		
		if ($this->canState)
		{
			$childBar->publish('categories.publish')->listCheck(true);
			$childBar->unpublish('categories.unpublish')->listCheck(true);
		}
		
		/*
		if ($this->canDo->get('core.admin'))
		{
			$childBar->checkin('listoflayouts.checkin');
		}
		*/

		if(($this->canState && $this->canDelete))
		{
			if ($this->state->get('filter.published') != ContentComponent::CONDITION_TRASHED)
			{
				$childBar->trash('categories.trash')->listCheck(true);
			}

			if (!$this->isEmptyState && $this->state->get('filter.published') == ContentComponent::CONDITION_TRASHED && $this->canDelete)
			{
				$toolbar->delete('categories.delete')
					->text('JTOOLBAR_EMPTY_TRASH')
					->message('JGLOBAL_CONFIRM_DELETE')
					->listCheck(true);
			}
		}
	}
	
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_YOUTUBEGALLERY_CATEGORIES'));
	}
}
