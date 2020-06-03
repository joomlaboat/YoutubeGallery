<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class com_YoutubeGalleryInstallerScript
{
    function postflight($route, $adapter)
    {
        com_YoutubeGalleryInstallerScript::enableButtonPlugin();
    }
    
    protected static function enableButtonPlugin()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $fields = array(
            $db->quoteName('enabled') . ' = 1',
            $db->quoteName('ordering') . ' = 9999'
        );

        $conditions = array(
            $db->quoteName('name') . ' = ' . $db->quote('plg_editors-xtd_youtubegallerybutton'), 
            $db->quoteName('type') . ' = ' . $db->quote('plugin'),
            $db->quoteName('ordering') . ' != ' . $db->quote('9999')// We only need to perform this if the extension is being installed, not updated
        );

        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);

        $db->setQuery($query);   
        $db->execute();     
    }
}
