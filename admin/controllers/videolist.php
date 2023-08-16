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
jimport('joomla.application.component.controlleradmin');


/**
 * YoutubeGallery - VideoList Controller
 */
class YoutubeGalleryControllerVideoList extends JControllerAdmin
{
    function display($cachable = false, $urlparams = array())
    {
        switch (Factory::getApplication()->input->getVar('task')) {
            case 'cancel':
                $this->cancel();
                break;
            default:
                Factory::getApplication()->input->setVar('view', 'videoylist');
                parent::display();
                break;
        }

    }

    function cancel()
    {
        $this->setRedirect('index.php?option=com_youtubegallery');
    }
}
