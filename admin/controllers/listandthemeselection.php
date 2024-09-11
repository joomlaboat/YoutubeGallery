<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\Utilities\ArrayHelper;

/**
 * YoutubeGallery - themeform Controller
 */
class YoutubeGalleryControllerThemeForm extends FormController
{
    function display($cachable = false, $urlparams = array()): void
    {
        $jInput = Factory::getApplication()->input;
        $task = $jInput->post->get('task', '');

        if ($task == 'themeform.add' or $task == 'add') {
            $this->setRedirect('index.php?option=com_youtubegallery&view=themeform&layout=edit');
            return;
        }

        if ($task == 'themeform.edit' or $task == 'edit') {

            $cid = Factory::getApplication()->input->post->get('cid', array(), 'array');
            $cid = ArrayHelper::toInteger($cid);

            if (!count($cid)) {
                $this->setRedirect('index.php?option=com_youtubegallery&view=themelist', Text::_('COM_YOUTUBEGALLERY_NO_THEME_SELECTED'), 'error');
                return;
            }

            $this->setRedirect('index.php?option=com_youtubegallery&view=themeform&layout=edit&id=' . $cid[0]);
            return;
        }

        $jInput->set('hidemainmenu', true);

        Factory::getApplication()->input->set('view', 'themeform');
        Factory::getApplication()->input->set('layout', 'edit');

        switch ($task) {
            case 'themeform.apply':
            case 'save':
            case 'themeform.save':
            case 'apply':
                $this->save();
                break;
            case 'themeform.cancel':
            case 'cancel':
                $this->cancel();
                break;
        }

        parent::display();
    }

    /**
     * @throws Exception
     */
    function save(?string $key = null, ?string $urlVar = null): void
    {
        $task = Factory::getApplication()->input->getCmd('task');

        // get our model
        $model = $this->getModel('themeform');

        $link = '';

        // attempt to store, update user accordingly
        if ($task != 'save' and $task != 'apply' and $task != 'themeform.save' and $task != 'themeform.apply') {
            $msg = Text::_('COM_YOUTUBEGALLERY_THEME_WAS_UNABLE_TO_SAVE');
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $this->setRedirect($link, $msg, 'error');
        }

        if ($model->store()) {

            if ($task == 'save' or $task == 'themeform.save') {
                $link = 'index.php?option=com_youtubegallery&view=themelist';
            } elseif ($task == 'apply' or $task == 'themeform.apply') {
                $link = 'index.php?option=com_youtubegallery&view=themeform&layout=edit&id=' . $model->id;
            }

            $msg = Text::_('COM_YOUTUBEGALLERY_THEME_SAVED_SUCCESSFULLY');

            $this->setRedirect($link, $msg);
        } else {

            $link = 'index.php?option=com_youtubegallery&view=themeform&layout=edit&id=' . $model->id;
            $msg = Text::_('COM_YOUTUBEGALLERY_THEME_WAS_UNABLE_TO_SAVE');
            $this->setRedirect($link, $msg, 'error');
        }

    }

    /**
     * Cancels an edit operation
     */
    function cancel(?string $key = null): void
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=themelist');
    }

    /**
     * Cancels an edit operation
     */
    function cancelItem(): void
    {
        $model = $this->getModel('item');
        $model->checkin();

    }
}
