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
use Joomla\CMS\MVC\View\HtmlView;

class YoutubegalleryViewListandthemeselection extends HtmlView
{
    /**
     * display method of Youtube Gallery view
     * @return void
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $this->session = Factory::getSession();
        parent::display($tpl);
    }

    function checkIfPluginIsEnabled()
    {
        $db = Factory::getDBO();
        $query = 'SELECT extension_id, enabled FROM #__extensions WHERE ' . $db->quoteName('type') . '="plugin" AND folder="content" AND ' . $db->quoteName('element') . '="youtubegallery" LIMIT 1';
        $db->setQuery($query);
        $plugins = $db->loadAssocList();

        if (count($plugins) == 0)
            return null;

        return $plugins[0];
    }

    function EnablePlugin(): void
    {
        $db = Factory::getDBO();
        $query = 'UPDATE #__extensions SET enabled=1 WHERE ' . $db->quoteName('type') . '="plugin" AND folder="content" AND ' . $db->quoteName('element') . '="youtubegallery"';
        $db->setQuery($query);
        $db->execute();
    }
}
