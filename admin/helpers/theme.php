<?php
/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// Check to ensure this file is included in Joomla!
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');


class JHTMLTheme
{
    public static function render($control_name, $value, $attribute)
    {
        $db = Factory::getDBO();
        $query = 'SELECT id, es_themename FROM #__customtables_table_youtubegallerythemes ORDER BY es_themename';
        $db->setQuery($query);
        $themes = $db->loadAssocList();
        if (!$themes) $themes = array();

        if ($value == '' and count($themes) > 0)
            $value = $themes[0]['id'];

        return JHTML::_('select.genericlist', $themes, $control_name, 'class="inputbox"' . $attribute, 'id', 'es_themename', $value);

    }
}
