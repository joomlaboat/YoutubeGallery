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
use Joomla\CMS\MVC\Controller\FormController;

/**
 * YouTubeGallery - VideoList Controller
 */
class YoutubeGalleryControllerVideoList extends FormController//JControllerAdmin
{
    function display($cachable = false, $urlparams = array()): void
    {
        switch (Factory::getApplication()->input->getCmd('task')) {
            case 'cancel':
                $this->cancel();
                break;
            default:
                Factory::getApplication()->input->set('view', 'videoylist');
                parent::display();
                break;
        }
    }

    function cancel($key = null): void
    {
        $this->setRedirect('index.php?option=com_youtubegallery');
    }
}
