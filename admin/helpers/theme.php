<?php
/**
 * YouTube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use CustomTables\database;

class JHTMLTheme
{
    public static function render($control_name, $value, $attribute)
    {
		$db = database::getDB();
        $query = 'SELECT id, es_themename FROM #__customtables_table_youtubegallerythemes ORDER BY es_themename';
        $db->setQuery($query);
        $themes = $db->loadAssocList();
        if (!$themes) $themes = array();

        if ($value == '' and count($themes) > 0)
            $value = $themes[0]['id'];

        return HTMLHelper::_('select.genericlist', $themes, $control_name, 'class="form-select"' . $attribute, 'id', 'es_themename', $value);
    }
}
