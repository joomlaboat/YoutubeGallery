<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Document\Factory;

defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');


/**
 * YoutubeGallery - LinksForm Model
 */
class YoutubeGalleryModelLinksForm extends JModelAdmin
{
    public $id;

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

        $form = $this->loadForm('com_youtubegallery.linksform', 'linksform', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string       Script files
     */
    public function getScript()
    {
        return 'administrator/components/com_youtubegallery/models/forms/linksform.js';
    }

    function RefreshPlayist($cids, $update_videolist = true)
    {
        $where = array();

        foreach ($cids as $cid)
            $where[] = 'id=' . $cid;


        require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php');

        // Create a new query object.

        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        // Select some fields
        $query->select(array('*'));
        // From the Youtube Gallery table
        $query->from('#__youtubegallery_videolists');

        if (count($where) > 0)
            $query->where(implode(' OR ', $where));

        $db->setQuery($query);

        $linksform_rows = $db->loadObjectList();
        if (count($linksform_rows) < 1)
            return false;

        $ygDB = new YouTubeGalleryDB;

        foreach ($linksform_rows as $linksform_row) {

            $ygDB->videolist_row = $linksform_row;
            YouTubeGalleryDB::update_cache_table($linksform_row, $update_videolist); //false - refresh

            if (!$update_videolist) {
                $query = 'UPDATE #__youtubegallery_videolists SET lastplaylistupdate="' . date('Y-m-d H:i:s') . '" WHERE id=' . (int)$linksform_row->id;
                $db->setQuery($query);
                $db->execute();

                $query = 'UPDATE #__youtubegallery_videos SET lastupdate=NULL WHERE isvideo AND listid=' . (int)$linksform_row->id;//to force the update

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

    function store()
    {


        $linksform_row = $this->getTable('videolists');

        $jinput = Factory::getApplication()->input;
        $data = $jinput->get('jform', array(), 'ARRAY');

        $post = array();

        $listname = trim(preg_replace("/[^a-zA-Z0-9_]/", "", $data['listname']));

        $data['jform']['listname'] = $listname;


        if (!$linksform_row->bind($data)) {
            echo 'Cannot bind.';
            return false;
        }

        // Make sure the  record is valid
        if (!$linksform_row->check()) {
            echo 'Cannot check.';
            return false;
        }

        // Store
        if (!$linksform_row->store()) {

            echo '<p>Cannot store.</p>
				<p>There is some fields missing.</p>
				';
            return false;
        }

        require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'db.php');
        $ygDB = new YouTubeGalleryDB;
        $ygDB->videolist_row = $linksform_row;
        YouTubeGalleryDB::update_cache_table($linksform_row, false);
        $linksform_row->lastplaylistupdate = date('Y-m-d H:i:s');

        $this->id = $linksform_row->id;

        return true;
    }

    public function getTable($type = 'VideoLists', $prefix = 'YoutubeGalleryTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    function deleteVideoList($cids)
    {

        $linksform_row = $this->getTable('videolists');

        $db = Factory::getDBO();

        if (count($cids)) {
            foreach ($cids as $cid) {
                $query = 'DELETE FROM #__youtubegallery_videos WHERE listid=' . (int)$cid;

                $db->setQuery($query);
                $db->execute();

                if (!$linksform_row->delete($cid))
                    return false;
            }
        }


        return true;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return      mixed   The data for the form.
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
}
