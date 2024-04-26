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

// import Joomla controlleradmin library
//jimport('joomla.application.component.controlleradmin');

/**
 * Youtube Gallery - Categories Controller
 */
class YoutubeGalleryControllerCategories extends FormController
{
    /**
     * Proxy for getModel.
     */

    protected $text_prefix = 'COM_YOUTUBEGALLERY_CATEGORIES';

    public function getModel($name = 'CategoryForm', $prefix = 'YoutubeGalleryModel', $config = array())
    {
        $model = parent::getModel($name, $prefix, array('ignore_request' => true));

        return $model;
    }

    public function unpublish()
    {
        $this->setStatus($this->task);
    }

    protected function setStatus($task)
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

    public function publish()
    {
        $this->setStatus($this->task);
    }

    public function trash()
    {
        $this->setStatus($this->task);
    }

    public function delete()
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
