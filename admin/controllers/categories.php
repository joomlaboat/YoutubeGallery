<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Router\Route;

/**
 * YouTube Gallery - Categories Controller
 */
class YoutubeGalleryControllerCategories extends FormController
{
    /**
     * Proxy for getModel.
     */

    protected $text_prefix = 'COM_YOUTUBEGALLERY_CATEGORIES';

    public function getModel($name = 'CategoryForm', $prefix = 'YoutubeGalleryModel', $config = array())
    {
        return parent::getModel($name, $prefix, array('ignore_request' => true));
    }

    /**
     * @throws Exception
     */
    public function unpublish(): void
    {
        $this->setStatus($this->task);
    }

    /**
     * @throws Exception
     */
    protected function setStatus($task): void
    {
        YoutubeGalleryHelper::setRecordStatus($task, 'CATEGORIES', 'youtubegallerycategories');

        $redirect = 'index.php?option=' . $this->option;
        $redirect .= '&view=categories';

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
    public function publish(): void
    {
        $this->setStatus($this->task);
    }

    /**
     * @throws Exception
     */
    public function trash(): void
    {
        $this->setStatus($this->task);
    }

    public function delete(): void
    {
        YoutubeGalleryHelper::deleteRecord('CATEGORIES', 'youtubegallerycategories');

        $redirect = 'index.php?option=' . $this->option;
        $redirect .= '&view=categories';

        // Redirect to the item screen.
        $this->setRedirect(
            Route::_(
                $redirect, false
            )
        );
    }
}
