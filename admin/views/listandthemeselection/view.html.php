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

// import Joomla view library
jimport('joomla.application.component.view');

class YoutubegalleryViewListandthemeselection extends JViewLegacy
{
    /**
     * display method of Youtube Gallery view
     * @return void
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

    function EnablePlugin()
    {
        $db = Factory::getDBO();
        $query = 'UPDATE #__extensions SET enabled=1 WHERE ' . $db->quoteName('type') . '="plugin" AND folder="content" AND ' . $db->quoteName('element') . '="youtubegallery"';
        $db->setQuery($query);
        $db->execute();
    }
}
