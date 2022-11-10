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
 * YoutubeGallery - themeexport Controller
 */
class YoutubeGalleryControllerThemeExport extends JControllerForm
{
    function display($cachable = false, $urlparams = array())
    {
        switch (Factory::getApplication()->input->getVar('task')) {
            case 'cancel':
                $this->cancel();
                break;
            default:
                Factory::getApplication()->input->setVar('view', 'themeexport');
                parent::display();
                break;
        }
    }

    /**
     * Cancels an edit operation
     */
    function cancel(?string $key = null)
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=themelist');
    }


}
