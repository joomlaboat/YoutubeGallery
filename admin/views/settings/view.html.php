<?php
/**
 * YoutubeGallery Joomla! Native Component
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
 * Youtube Gallery Theme Export View
 */
class YoutubeGalleryViewSettings extends JViewLegacy
{
    /**
     * display method of Youtube Gallery view
     * @return void
     * @throws Exception
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

        $this->canDo = ContentHelper::getActions('com_youtubegallery', 'settings');

        $this->canView = $this->canDo->get('settings.view');
        $this->canEdit = $this->canDo->get('settings.edit');

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
        if ($this->canView and $this->canEdit)
            $this->addToolBar();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors), 500);
        }

        // Display the template
        if ($this->canView) {
            if ($this->version < 4)
                parent::display($tpl);
            else
                parent::display('quatro');
        } else
            Factory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');

    }

    protected function addToolBar()
    {
        $jinput = Factory::getApplication()->input;
        $jinput->get->set('hidemainmenu', true);
        JToolBarHelper::title(JText::_('Settings'));
        JToolBarHelper::apply('settings.apply');
        JToolBarHelper::cancel('settings.cancel', 'JTOOLBAR_CLOSE');
    }
}
