<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

// import Joomla view library
//jimport('joomla.application.component.view');

/**
 * Youtube Gallery Theme Export View
 */
class YoutubeGalleryViewThemeImport extends HtmlView
{
    /**
     * display method of Youtube Gallery view
     * @return void
     */

    public function display($tpl = null)
    {
        // Set the toolbar
        $this->addToolBar();
        parent::display($tpl);
    }

    protected function addToolBar()
    {
        $jinput = Factory::getApplication()->input;
        $jinput->get->set('hidemainmenu', true);
        ToolbarHelper::title(Text::_('COM_YOUTUBEGALLERY_THEME_IMPORT'));
        ToolbarHelper::cancel('themeimport.cancel', 'JTOOLBAR_CLOSE');
    }
}
