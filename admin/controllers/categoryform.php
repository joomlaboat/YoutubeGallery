<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;

/**
 * YouTubeGallery - CategoryForm Controller
 */
class YoutubeGalleryControllerCategoryForm extends FormController
{
    function display($cachable = false, $urlparams = array()): void
    {
        $jInput = Factory::getApplication()->input;
        $task = $jInput->post->get('task', '');

        if ($task == 'categoryform.add' or $task == 'add') {
            $this->setRedirect('index.php?option=com_youtubegallery&view=categoryform&layout=edit');
            return;
        }

        if ($task == 'categoryform.edit' or $task == 'edit') {
            $cid = $jInput->get('cid', array(), 'ARRAY');

            if (!count($cid)) {
                $this->setRedirect('index.php?option=com_youtubegallery&view=categories', Text::_('COM_YOUTUBEGALLERY_NO_CATEGORIES_SELECTED'), 'error');
                return;
            }

            $this->setRedirect('index.php?option=com_youtubegallery&view=categoryform&layout=edit&id=' . $cid[0]);
            return;
        }

        Factory::getApplication()->input->set('view', 'categoryform');
        Factory::getApplication()->input->set('layout', 'edit');

        switch (Factory::getApplication()->input->get('task')) {
            case 'save':
            case 'categoryform.apply':
            case 'categoryform.save':
            case 'apply':
                $this->save();
                break;
            case 'categoryform.cancel':
            case 'cancel':
                $this->cancel();
                break;
        }

        parent::display();
    }

    /**
     * @throws Exception
     */
    function save($key = null, $urlVar = null): void
    {
        $task = Factory::getApplication()->input->get('task');

        // get our model
        $model = $this->getModel('categoryform');
        $link = '';

        // attempt to store, update user accordingly
        if ($task != 'save' and $task != 'apply' and $task != 'categoryform.save' and $task != 'categoryform.apply') {
            $msg = Text::_('COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_SAVE');

            $link = 'index.php?option=com_youtubegallery&view=categories';
            $this->setRedirect($link, $msg, 'error');
        }

        if ($model->store()) {

            if ($task == 'save' or $task == 'categoryform.save')
                $link = 'index.php?option=com_youtubegallery&view=categories';
            elseif ($task == 'apply' or $task == 'categoryform.apply') {

                $link = 'index.php?option=com_youtubegallery&view=categoryform&layout=edit&id=' . $model->id;
            }

            $msg = Text::_('COM_YOUTUBEGALLERY_CATEGORY_SAVED_SUCCESSFULLY');

            $this->setRedirect($link, $msg);
        } else {
            $msg = Text::_('COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_SAVE');
            $this->setRedirect($link, $msg, 'error');
        }

    }

    /**
     * Cancels an edit operation
     */
    function cancel($key = null): void
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=categories');
    }
}
