<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class YoutubeGalleryModelLinksForm extends AdminModel
{
    public $typeAlias = 'com_youtubegallery.linksform';
    protected $text_prefix = 'COM_YOUTUBEGALLERY';

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_youtubegallery.linksform', 'linksform', array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        // The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
        if (Factory::getApplication()->input->get('a_id')) {
            $id = Factory::getApplication()->input->get('a_id', 0, 'INT');
        } // The back end uses id so we use that the rest of the time and set it to 0 by default.
        else {
            $id = Factory::getApplication()->input->get('id', 0, 'INT');
        }

        $user = Factory::getUser();

        // Check for existing item.
        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_youtubegallery.linksform.' . (int)$id))
            or ($id == 0 && !$user->authorise('core.edit.state', 'com_youtubegallery'))) {
            // Disable fields for display.
            $form->setFieldAttribute('published', 'disabled', 'true');
            // Disable fields while saving.
            $form->setFieldAttribute('published', 'filter', 'unset');
        }

        // Only load these values if no id is found
        if (0 == $id) {
            // Set redirected field name
            $redirectedField = Factory::getApplication()->input->get('ref', null, 'STRING');
            // Set redirected field value
            $redirectedValue = Factory::getApplication()->input->get('refid', 0, 'INT');
            if (0 != $redirectedValue && $redirectedField) {
                // Now set the local-redirected field default value
                $form->setValue($redirectedField, null, $redirectedValue);
            }
        }

        return $form;
    }

    public function getScript()
    {
        return Uri::root(true) . '/administrator/components/com_youtubegallery/models/forms/linksform.js';
    }

    function saveVideoList($data): bool
    {
        $linksFormRow = $this->getTable('videolists');

        if (isset($data['es_listname'])) {
            $listName = trim(preg_replace("/[^a-zA-Z0-9_]/", "", $data['es_listname']));
            $data['jform']['es_listname'] = $listName;
        }

        if (!$linksFormRow->bind($data)) {
            echo 'Cannot bind.';
            return false;
        }

        // Make sure the  record is valid
        if (!$linksFormRow->check()) {
            echo 'Cannot check.';
            return false;
        }

        // Store
        if (!$linksFormRow->store()) {
            echo '<p>Cannot store.</p><p>There is some fields missing.</p>';
            return false;
        }

        $active_key = YouTubeGalleryData::isActivated();

        YouTubeGalleryDB::update_cache_table($active_key, $linksFormRow, true);
        $this->id = $linksFormRow->id;
        return true;
    }

    public function getTable($type = 'Videolists', $prefix = 'YoutubeGalleryTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /*
        function store(): bool
        {
            echo 'modal store.<br/>';
            return $this->saveVideoList();
        }

        public function save($data): bool
        {
            echo 'modal save.<br/>';
            return $this->saveVideoList();
        }
    */
    /**
     * @throws Exception
     */
    function RefreshPlayList($cids, $update_videolist = true): bool
    {
        $where = array();

        foreach ($cids as $cid)
            $where[] = 'id=' . $cid;

        // Create a new query object.

        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        // Select some fields
        $query->select(array('*'));
        // From the YouTube Gallery table
        $query->from('#__customtables_table_youtubegalleryvideolists');

        if (count($where) > 0)
            $query->where(implode(' OR ', $where));

        $db->setQuery($query);

        $linksFormRows = $db->loadObjectList();
        if (count($linksFormRows) < 1)
            return false;

        $active_key = YouTubeGalleryData::isActivated();

        foreach ($linksFormRows as $linksFormRow) {

            YouTubeGalleryDB::update_cache_table($active_key, $linksFormRow, $update_videolist); //false - refresh

            if (!$update_videolist) {
                $query = 'UPDATE #__customtables_table_youtubegalleryvideolists SET es_lastplaylistupdate="' . date('Y-m-d H:i:s') . '" WHERE id=' . (int)$linksFormRow->id;
                $db->setQuery($query);
                $db->execute();

                $query = 'UPDATE #__customtables_table_youtubegalleryvideos SET es_lastupdate=NULL WHERE es_isvideo AND es_videolist=' . (int)$linksFormRow->id;//to force the update

                $db->setQuery($query);
                $db->execute();
            }
        }

        //check for error messages
        $mainframe = Factory::getApplication();
        $messages = $mainframe->getMessageQueue();
        if (is_array($messages) and count($messages) == 0)
            return true;
        else
            return false;
    }

    protected function canDelete($record): bool
    {
        if (!empty($record->id)) {
            $user = Factory::getUser();
            // The record has been set. Check the record permissions.
            return $user->authorise('linkslist.delete', 'com_youtubegallery.linkslist.' . (int)$record->id);
        }
        return false;
    }

    protected function canEditState($record): bool
    {
        $user = Factory::getUser();
        $recordId = (!empty($record->id)) ? $record->id : 0;

        if ($recordId) {
            // The record has been set. Check the record permissions.
            $permission = $user->authorise('linkslist.edit.state', 'com_youtubegallery.linkslist.' . (int)$recordId);
            if (!$permission && !is_null($permission)) {
                return false;
            }
        }
        // In the absence of better information, revert to the component permissions.
        return parent::canEditState($record);
    }

    protected function allowEdit($data = array(), $key = 'id')
    {
        echo 'allowEdit';
        // Check specific edit permission then general edit permission.

        return Factory::getUser()->authorise('linkslist.edit', 'com_youtubegallery.linkslist.' . ((int)isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
    }

    /**
     * @throws Exception
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_youtubegallery.edit.linksform.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        return $data;
    }

    public function getItem($pk = null)
    {
        if ($item = parent::getItem($pk)) {
            if (!empty($item->params) && !is_array($item->params)) {
                // Convert the params field to an array.
                $registry = new Registry;
                $registry->loadString($item->params);
                $item->params = $registry->toArray();
            }

            if (!empty($item->metadata)) {
                // Convert the metadata field to an array.
                $registry = new Registry;
                $registry->loadString($item->metadata);
                $item->metadata = $registry->toArray();
            }

            //if (!empty($item->id)) {
            //$item->tags = new JHelperTags;
            //$item->tags->getTagIds($item->id, 'com_youtubegallery.linksform');
            //}
        }
        return $item;
    }
}
