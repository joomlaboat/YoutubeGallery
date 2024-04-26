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

// import Joomla controllerform library
//jimport('joomla.application.component.controllerform');

/**
 * YoutubeGallery - themeform Controller
 */
class YoutubeGalleryControllerThemeForm extends FormController
{

    function display($cachable = false, $urlparams = array())
    {
        $jinput = Factory::getApplication()->input;
        $task = $jinput->post->get('task', '');

        if ($task == 'themeform.add' or $task == 'add') {
            $this->setRedirect('index.php?option=com_youtubegallery&view=themeform&layout=edit');
            return true;
        }

        if ($task == 'themeform.edit' or $task == 'edit') {
            $cid = Factory::getApplication()->input->getVar('cid', array(), 'post', 'array');

            if (!count($cid)) {
                $this->setRedirect('index.php?option=com_youtubegallery&view=themelist', Text::_('COM_YOUTUBEGALLERY_NO_THEME_SELECTED'), 'error');
                return false;
            }

            $this->setRedirect('index.php?option=com_youtubegallery&view=themeform&layout=edit&id=' . $cid[0]);
            return true;
        }

        $jinput->set('hidemainmenu', true);

        Factory::getApplication()->input->setVar('view', 'themeform');
        Factory::getApplication()->input->setVar('layout', 'edit');

        switch ($task) {
            case 'apply':
                $this->save();
                break;
            case 'themeform.apply':
                $this->save();
                break;
            case 'save':
                $this->save();
                break;
            case 'themeform.save':
                $this->save();
                break;
            case 'cancel':
                $this->cancel();
                break;
            case 'themeform.cancel':
                $this->cancel();
                break;
        }

        parent::display();
    }

    function save($key = NULL, $urlVar = NULL)
    {
        $task = Factory::getApplication()->input->getVar('task');

        // get our model
        $model = $this->getModel('themeform');
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
    function cancel($key = NULL)
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=themelist');
    }

    /**
     * Cancels an edit operation
     */
    function cancelItem()
    {


        $model = $this->getModel('item');
        $model->checkin();


    }

    /**
     * Form for copying item(s) to a specific option
     */
}
