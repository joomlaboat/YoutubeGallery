<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

class YoutubeGalleryControllerThemeList extends FormController
{
    /**
     * Proxy for getModel.
     */
    protected $text_prefix = 'COM_YOUTUBEGALLERY_THEMELIST';

    public function getModel($name = 'LinksForm', $prefix = 'YoutubeGalleryModel', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    /**
     * @throws Exception
     */
    public function publish(): void
    {
        $this->update_status();
    }

    /**
     * @throws Exception
     */
    public function update_status(): void
    {
        YoutubeGalleryHelper::setRecordStatus($this->task, 'THEMELIST', 'youtubegallerythemes');

        $redirect = 'index.php?option=' . $this->option;
        $redirect .= '&view=themelist';

        // Redirect to the item screen.
        $this->setRedirect(
            Route::_(
                $redirect, false
            )
        );
    }

    /**
     * @throws Exception
     */
    public function unpublish(): void
    {
        $this->update_status();
    }

    /**
     * @throws Exception
     */
    public function trash(): void
    {
        $this->update_status();
    }

    public function delete(): void
    {
        YoutubeGalleryHelper::deleteRecord('THEMELIST', 'youtubegallerythemes');

        $redirect = 'index.php?option=' . $this->option;
        $redirect .= '&view=themelist';

        // Redirect to the item screen.
        $this->setRedirect(
            JRoute::_(
                $redirect, false
            )
        );
    }

    public function uploadItem(): void
    {
        $link = 'index.php?option=com_youtubegallery&view=themeimport';
        $this->setRedirect($link, '');
    }
}
