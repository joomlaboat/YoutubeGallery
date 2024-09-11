<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * YouTubeGallery - LinksForm Controller
 */
class YoutubeGalleryControllerSettings extends FormController
{
    var mixed $canDo;
    var bool $canView;

    var bool $canEdit;

    function display($cachable = false, $urlparams = array()): void
    {
        $this->canDo = ContentHelper::getActions('com_youtubegallery', 'settings');
        $this->canView = $this->canDo->get('settings.view');
        $this->canEdit = $this->canDo->get('settings.edit');

        if (!$this->canView) {
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $msg = Text::_('JGLOBAL_AUTH_ACCESS_DENIED');
            $this->setRedirect($link, $msg, 'error');
        }
    }

    /**
     * @throws Exception
     */
    function save($key = NULL, $urlVar = NULL): bool
    {
        $this->canDo = ContentHelper::getActions('com_youtubegallery', 'settings');

        $this->canView = $this->canDo->get('settings.view');
        $this->canEdit = $this->canDo->get('settings.edit');

        if (!$this->canView or !$this->canEdit) {
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $msg = Text::_('JGLOBAL_AUTH_ACCESS_DENIED');
            $this->setRedirect($link, $msg, 'error');
            return false;
        }

        $task = Factory::getApplication()->input->getCmd('task');

        // get our model
        $model = $this->getModel('settings');
        // attempt to store, update user accordingly

        if ($task != 'save' and $task != 'apply' and $task != 'settings.save' and $task != 'settings.apply') {
            $msg = Text::_('COM_YOUTUBEGALLERY_SETTINGS_WAS_UNABLE_TO_SAVE');
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $this->setRedirect($link, $msg, 'error');
            return false;
        }

        $link = 'index.php?option=com_youtubegallery&view=settings&layout=edit';
        if ($model->store()) {
            $msg = Text::_('COM_YOUTUBEGALLERY_SETTINGS_SAVED_SUCCESSFULLY');

            $this->setRedirect($link, $msg);
        } else {
            $msg = Text::_('COM_YOUTUBEGALLERY_SETTINGS_WAS_UNABLE_TO_SAVE');
            $this->setRedirect($link, $msg, 'error');
        }
        return true;
    }

    function cancel($key = NULL): void
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=linkslist');
    }
}
