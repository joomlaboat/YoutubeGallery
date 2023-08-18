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

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * YoutubeGallery - Settings Model
 */
class YoutubeGalleryModelSettings extends JModelAdmin
{
    public function getTable($type = 'Settings', $prefix = 'YoutubeGalleryTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @return      mixed   A JForm object on success, false on failure
     */

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.

        $form = $this->loadForm('com_youtubegallery.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    function store()
    {
        $jform = Factory::getApplication()->input->getVar('jform');
        $allowsef = trim(preg_replace("/[^0-9]/", "", $jform['allowsef']));

        $joomlaboat_api_host = trim(Factory::getApplication()->input->getVar('joomlaboat_api_host'));
        $joomlaboat_api_key = trim(preg_replace("/[^a-zA-Z0-9~_-]/", "", Factory::getApplication()->input->getVar('joomlaboat_api_key')));
        $youtubedataapi_key = trim(preg_replace("/[^a-zA-Z0-9~_-]/", "", Factory::getApplication()->input->getVar('youtubedataapi_key')));

        $db = Factory::getDBO();
        $query = array();
        $query[] = YoutubeGalleryModelSettings::makeQueryLine('allowsef', $allowsef);
        $query[] = YoutubeGalleryModelSettings::makeQueryLine('joomlaboat_api_host', $joomlaboat_api_host);
        $query[] = YoutubeGalleryModelSettings::makeQueryLine('joomlaboat_api_key', $joomlaboat_api_key);
        $query[] = YoutubeGalleryModelSettings::makeQueryLine('youtubedataapi_key', $youtubedataapi_key);

        foreach ($query as $q) {
            $db->setQuery($q);
            $db->execute();
        }
        return true;
    }

    static protected function makeQueryLine($field, $value)
    {
        $db = Factory::getDBO();
        /*
        return 'INSERT INTO #__customtables_table_youtubegallerysettings (es_option, es_value)
        VALUES ('.$db->quote($field).', '.$db->quote($value).')
        ON DUPLICATE KEY UPDATE es_option='.$db->quote($field).', es_value='.$db->quote($value);
        */

        $id = YoutubeGalleryModelSettings::getRecordID($field);
        if ($id == null)
            return 'INSERT INTO #__customtables_table_youtubegallerysettings (es_option, es_value)'
                . ' VALUES (' . $db->quote($field) . ', ' . $db->quote($value) . ')';
        else
            return 'UPDATE #__customtables_table_youtubegallerysettings SET es_value=' . $db->quote($value) . ' WHERE es_option=' . $db->quote($field);
    }

    static protected function getRecordID($field)
    {
        $db = Factory::getDBO();
        $query = 'SELECT id FROM #__customtables_table_youtubegallerysettings WHERE es_option=' . $db->quote($field) . ' LIMIT 1';
        $db->setQuery($query);
        $records = $db->loadAssocList();
        if (count($records) == 0)
            return null;

        return $records[0]['id'];
    }
}
