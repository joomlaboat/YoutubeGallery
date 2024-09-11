<?php
/**
 * YouTubeGallery Joomla! Native Component
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
 * YouTubeGallery - themeimport Controller
 */
class YoutubeGalleryControllerThemeImport extends FormController
{
    function display($cachable = false, $urlparams = array()): void
    {
        switch (Factory::getApplication()->input->getCmd('task')) {
            case 'upload':
            case 'themeimport.upload':
                $this->upload();
                break;
            case 'cancel':
            case 'themeimport.cancel':
                $this->cancel();
                break;
            default:
                Factory::getApplication()->input->set('view', 'themeimport');
                parent::display();
                break;
        }
    }

    function upload(): void
    {
        $model = $this->getModel('themeimport');
        $msg = '';
        if ($model->upload_theme($msg)) {
            $msg = Text::_('COM_YOUTUBEGALLERY_THEME_IMPORTED_SUCCESSFULLY');
            $link = 'index.php?option=com_youtubegallery&view=themelist';
            $this->setRedirect($link, $msg);
        } else {
            if ($msg == '')
                $msg = Text::_('COM_YOUTUBEGALLERY_THEME_FILE_CORRUPTED_OR_NO_PERMISSION');

            $link = 'index.php?option=com_youtubegallery&view=themeimport';
            $this->setRedirect($link, $msg, 'error');
        }
    }

    function cancel($key = null): void
    {
        $this->setRedirect('index.php?option=com_youtubegallery&view=themelist');
    }


}
