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
use Joomla\CMS\Router\Route;

/**
 * YouTube Gallery - LinksList Controller
 */
class YoutubeGalleryControllerLinksList extends FormController
{
    /**
     * Proxy for getModel.
     */

    protected $text_prefix = 'COM_YOUTUBEGALLERY_LINKSLIST';

    public function updateItem()
    {
        $model = $this->getModel('linksform');
        $cid = Factory::getApplication()->input->post->get('cid', array(), 'array');

        if (count($cid) < 1) {
            $this->setRedirect('index.php?option=com_youtubegallery&view=linkslist', Text::_('COM_YOUTUBEGALLERY_NO_ITEMS_SELECTED'), 'error');
            return false;
        }

        if ($model->RefreshPlayist($cid, true)) {
            $msg = Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_UPDATED_SUCCESSFULLY');
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $this->setRedirect($link, $msg);
        } else {
            $msg = Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_WAS_UNABLE_TO_UPDATE');
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $this->setRedirect($link, $msg, 'error');
        }
    }

    public function getModel($name = 'LinksForm', $prefix = 'YoutubeGalleryModel', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    public function refreshItem()
    {
        $model = $this->getModel('linksform');
        $cid = Factory::getApplication()->input->post->get('cid', array(), 'array');

        if (count($cid) < 1) {
            $this->setRedirect('index.php?option=com_youtubegallery&view=linkslist', Text::_('COM_YOUTUBEGALLERY_NO_ITEMS_SELECTED'), 'error');

            return false;
        }

        if ($model->RefreshPlayist($cid, false)) {
            $msg = Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESHED_SUCCESSFULLY');
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $this->setRedirect($link, $msg);
        } else {
            $msg = Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_WAS_UNABLE_TO_REFRESH');
            $link = 'index.php?option=com_youtubegallery&view=linkslist';
            $this->setRedirect($link, $msg, 'error');
        }
    }

    public function unpublish()
    {
        $this->update_status();
    }

    public function update_status(): void
    {
        YoutubeGalleryHelper::setRecordStatus($this->task, 'LINKSLIST', 'youtubegalleryvideolists');

        $redirect = 'index.php?option=' . $this->option;
        $redirect .= '&view=linkslist';

        // Redirect to the item screen.
        $this->setRedirect(
            Route::_(
                $redirect, false
            )
        );
    }

    public function trash()
    {
        $this->update_status();
    }

    public function publish()
    {
        $this->update_status();
    }

    public function delete()
    {
        YoutubeGalleryHelper::deleteRecord('LINKSLIST', 'youtubegalleryvideolists');

        $redirect = 'index.php?option=' . $this->option;
        $redirect .= '&view=linkslist';

        // Redirect to the item screen.
        $this->setRedirect(
            Route::_(
                $redirect, false
            )
        );
    }
}

