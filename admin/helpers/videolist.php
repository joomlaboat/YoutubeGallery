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
        $videolists = $db->loadAssocList();
        if (!$videolists) $videolists = array();

        $input = Factory::getApplication()->input;
        if ($input->getInt('showlatestvideolist') == 1) {
            if ($value == 0 and count($videolists) > 0) {
                //find the latest Video List
                $value = 0;
                foreach ($videolists as $v) {
                    $id = (int)$v['id'];
                    if ($id > $value)
                        $value = $id;
                }
            }
        }

        $videolists = array_merge(array(array('id' => '', 'es_listname' => '- ' . JText::_('COM_YOUTUBEGALLERY_VIDEOLIST_ADD'))), $videolists);

        return JHTML::_('select.genericlist', $videolists, $control_name, 'class="inputbox"' . $attribute, 'id', 'es_listname', $value);


    }

}
