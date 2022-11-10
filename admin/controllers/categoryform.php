<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

// import Joomla controllerform library
jimport('joomla.application.component.controllerform');

/**
 * YoutubeGallery - CategoryForm Controller
 */
class YoutubeGalleryControllerCategoryForm extends JControllerForm
{

    function display($cachable = false, $urlparams = array())
    {

        $jinput = Factory::getApplication()->input;
        $task = $jinput->post->get('task', '');

        if ($task == 'categoryform.add' or $task == 'add') {
            $this->setRedirect('index.php?option=com_youtubegallery&view=categoryform&layout=edit');
            return true;
        }

        if ($task == 'categoryform.edit' or $task == 'edit') {
            $cid = $jinput->get('cid', array(), 'ARRAY');

            if (!count($cid)) {
                $this->setRedirect('index.php?option=com_youtubegallery&view=categories', JText::_('COM_YOUTUBEGALLERY_NO_CATEGORIES_SELECTED'), 'error');
                return false;
            }

            $this->setRedirect('index.php?option=com_youtubegallery&view=categoryform&layout=edit&id=' . $cid[0]);
            return true;
        }

        Factory::getApplication()->input->setVar('view', 'categoryform');
        Factory::getApplication()->input->setVar('layout', 'edit');

        switch (Factory::getApplication()->input->getVar('task')) {
            case 'apply':
                $this->save();
                break;
            case 'save':
                $this->save();
                break;
            case 'cancel':
                $this->cancel();
                break;
            case 'categoryform.apply':
                $this->save();
                break;
            case 'categoryform.save':
                $this->save();
                break;
            case 'categoryform.cancel':
                $this->cancel();
                break;
        }

        parent::display();
    }

    function save(?string $key = null, ?string $urlVar = null)
    {
        $task = Factory::getApplication()->input->getVar('task');

        // get our model
        $model = $this->getModel('categoryform');
        // attempt to store, update user accordingly

        if ($task != 'save' and $task != 'apply' and $task != 'categoryform.save' and $task != 'categoryform.apply') {
            $msg = JText::_('COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_SAVE');

            $link = 'index.php?option=com_youtubegallery&view=categories';
            $this->setRedirect($link, $msg, 'error');
        }

        if ($model->store()) {

            if ($task == 'save' or $task == 'categoryform.save')
                $link = 'index.php?option=com_youtubegallery&view=categories';
            elseif ($task == 'apply' or $task == 'categoryform.apply') {

                $link = 'index.php?option=com_youtubegallery&view=categoryform&layout=edit&id=' . $model->id;
            }

            $msg = JText::_('COM_YOUTUBEGALLERY_CATEGORY_SAVED_SUCCESSFULLY');

            $this->setRedirect($link, $msg);
        } else {

            $msg = JText::_('COM_YOUTUBEGALLERY_CATEGORY_WAS_UNABLE_TO_SAVE');
            $this->setRedirect($link, $msg, 'error');
        }

    }

    /**
     * Cancels an edit operation
     */
    function cancel(?string $key = null)
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=categories');
    }

}
