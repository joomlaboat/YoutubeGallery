<?php
/**
 * YoutubeGallery Joomla! Native Component
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
 * YoutubeGallery - LinksForm Controller
 */
class YoutubeGalleryControllerLinksForm extends JControllerForm
{

    function display($cachable = false, $urlparams = array())
    {
        $jinput = Factory::getApplication()->input;
        $task = $jinput->post->get('task', '');

        if ($task == 'linksform.add' or $task == 'add') {
            $this->setRedirect('index.php?option=com_youtubegallery&view=linksform&layout=edit');
            return true;
        }

        if ($task == 'linksform.edit' or $task == 'edit') {
            $cid = $jinput->get('cid', array(), 'ARRAY');

            if (!count($cid)) {
                $this->setRedirect('index.php?option=com_youtubegallery&view=linkslist', JText::_('COM_YOUTUBEGALLERY_NO_VIDEOLISTS_SELECTED'), 'error');
                return false;
            }

            $this->setRedirect('index.php?option=com_youtubegallery&view=linksform&layout=edit&id=' . $cid[0]);
            return true;
        }

        Factory::getApplication()->input->setVar('view', 'linksform');
        Factory::getApplication()->input->setVar('layout', 'edit');

        switch ($task) {
            case 'apply':
                $this->save();
                break;
            case 'linksform.apply':
                $this->save();
                break;
            case 'save':
                $this->save();
                break;
            case 'linksform.save':
                $this->save();
                break;
            case 'cancel':
                $this->cancel();
                break;
            case 'linksform.cancel':
                $this->cancel();
                break;
            default:
                parent::display();
                break;
        }

    }


    function save($key = NULL, $urlVar = NULL)
    {
        $task = Factory::getApplication()->input->getCmd('task');

        // get our model
        $model = $this->getModel('linksform');
        // attempt to store, update user accordingly

        if ($task != 'save' and $task != 'apply' and $task != 'linksform.save' and $task != 'linksform.apply') {
            $msg = JText::_('COM_YOUTUBEGALLERY_VIDEOLIST_WAS_UNABLE_TO_SAVE');
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $this->setRedirect($link, $msg, 'error');
        }

        $input = Factory::getApplication()->input;

        if ($model->store()) {

            if ($task == 'save' or $task == 'linksform.save') {
                $link = 'index.php?option=com_youtubegallery&view=linkslist';

            } elseif ($task == 'apply' or $task == 'linksform.apply') {


                $link = 'index.php?option=com_youtubegallery&view=linksform&layout=edit&id=' . $model->id;
            }

            $msg = JText::_('COM_YOUTUBEGALLERY_VIDEOLIST_SAVED_SUCCESSFULLY');

            if ($input->getCmd('tmpl') == 'component') {
                $link .= (strpos($link, '?') === false ? '?' : '&') . 'tmpl=component';
                $link .= '&ygrefreshparent=' . ($input->getInt('ygrefreshparent') == 1 ? '1' : '0');
            }

            $this->setRedirect($link, $msg);
        } else {

            $link = 'index.php?option=com_youtubegallery&view=linksform&layout=edit&id=' . $model->id;
            if ($input->getCmd('tmpl') == 'component') {
                $link .= (strpos($link, '?') === false ? '?' : '&') . 'tmpl=component';
                $link .= '&ygrefreshparent=' . ($input->getInt('ygrefreshparent') == 1 ? '1' : '0');
            }

            $msg = JText::_('COM_YOUTUBEGALLERY_VIDEOLIST_WAS_UNABLE_TO_SAVE');
            $this->setRedirect($link, $msg, 'error');
        }

    }

    function cancel($key = NULL)
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=linkslist');
    }

}
