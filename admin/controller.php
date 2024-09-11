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
use Joomla\CMS\MVC\Controller\BaseController;

class YoutubeGalleryController extends BaseController
{
    /**
     * display task
     *
     * @return void
     * @throws Exception
     */
    function display($cachable = false, $urlparams = null)
    {
        // set default view if not set
        $jinput = Factory::getApplication()->input;
        $view = $jinput->getCmd('view', 'linkslist');

        $jinput->set('view', $view);

        // call parent behavior
        parent::display($cachable);
    }
}
