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


class JHTMLVideoList
{
    public static function render($control_name, $value, $attribute)
    {
        $value = (int)$value;
        $db = Factory::getDBO();

        $query = 'SELECT id, es_listname FROM #__customtables_table_youtubegalleryvideolists ORDER BY es_listname';

        $db->setQuery($query);
        $videoLists = $db->loadAssocList();
        if (!$videoLists) $videoLists = array();

        $input = Factory::getApplication()->input;
        if ($input->getInt('showlatestvideolist') == 1) {
            if ($value == 0 and count($videoLists) > 0) {
                //find the latest Video List
                $value = 0;
                foreach ($videoLists as $v) {
                    $id = (int)$v['id'];
                    if ($id > $value)
                        $value = $id;
                }
            }
        }

        $videoLists = array_merge(array(array('id' => '', 'es_listname' => '- ' . JText::_('COM_YOUTUBEGALLERY_VIDEOLIST_ADD'))), $videoLists);

        return JHTML::_('select.genericlist', $videoLists, $control_name, 'class="inputbox"' . $attribute, 'id', 'es_listname', $value);
    }
}
