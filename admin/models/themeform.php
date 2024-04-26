<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

// import Joomla modelform library
//jimport('joomla.application.component.modeladmin');

/**
 * YoutubeGallery - Theme Form Model
 */
class YoutubeGalleryModelthemeForm extends AdminModel
{
    public $id;

    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_youtubegallery.themeform', 'themeform', array('control' => 'jform', 'load_data' => true));
        if (empty($form))
            return false;

        return $form;
    }

    /**
     * Method to get the script that have to be included on the form
     *
     * @return string       Script files
     */
    public function getScript()
    {
        return 'administrator/components/com_youtubegallery/models/forms/themeform.js';
    }

    function store()
    {
        $themeform_row = $this->getTable('themes');
        $jinput = Factory::getApplication()->input;
        $data = $jinput->get('jform', array(), 'ARRAY');
        $themename = trim(preg_replace("/[^a-zA-Z0-9_]/", "", $data['themename']));
        $data['themename'] = $themename;

        if (!$themeform_row->bind($data)) {
            echo 'Cannot bind.';
            return false;
        }

        // Make sure the  record is valid
        if (!$themeform_row->check()) {
            echo 'Cannot check.';
            return false;
        }

        // Store
        if (!$themeform_row->store()) {

            echo '<p>Cannot store.</p>
				<p>There is some fields missing.</p>
				';
            return false;
        }

        $this->id = $themeform_row->id;

        return true;
    }

    public function getTable($type = 'Themes', $prefix = 'YoutubeGalleryTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    function deleteTheme($cids)
    {
        $themeform_row = $this->getTable('themes');

        if (count($cids)) {
            foreach ($cids as $cid) {
                if (!$themeform_row->delete($cid))
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
        $data = Factory::getApplication()->getUserState('com_youtubegallery.edit.themeform.data', array());
        if (empty($data))
            $data = $this->getItem();

        return $data;
    }
}
