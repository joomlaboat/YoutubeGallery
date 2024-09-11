<?php
/**
 * YoutubeGallery Joomla!  Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

/**
 * YoutubeGallery - Category Model
 */
class YoutubeGalleryModelCategoryForm extends AdminModel
{
    public $id;

    /**
     * Method to get the record form.
     *
     * @param array $data Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     * @throws Exception
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_youtubegallery.categoryform', 'categoryform', array('control' => 'jform', 'load_data' => $loadData));

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
    public function getScript(): string
    {
        return 'administrator/components/com_youtubegallery/models/forms/categoryform.js';
    }

    /**
     * @throws Exception
     */
    function store(): bool
    {
        $category_row = $this->getTable('categories');

        $jInput = Factory::getApplication()->input;
        $data = $jInput->get('jform', array(), 'ARRAY');
        $categoryname = trim(preg_replace("/[^a-zA-Z0-9_]/", "", $data['jform']['categoryname']));
        $data['jform']['categoryname'] = $categoryname;

        if (!$category_row->bind($data)) {
            return false;
        }

        // Make sure the  record is valid
        if (!$category_row->check()) {
            return false;
        }

        // Store
        if (!$category_row->store()) {
            return false;
        }

        $this->id = $category_row->id;
        return true;
    }

    public function getTable($name = 'Categories', $prefix = 'YoutubeGalleryTable', $config = array())
    {
        return Table::getInstance($name, $prefix, $config);
    }

    /**
     * @throws Exception
     */
    function deleteCategory(array $cids): bool
    {
        $category_row = $this->getTable('categories');

        if (count($cids)) {
            foreach ($cids as $cid) {
                if (!$category_row->delete($cid)) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return      mixed   The data for the form.
     * @throws Exception
     */
    protected function loadFormData(): mixed
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_youtubegallery.edit.categoryform.data', array());
        if (empty($data)) {
            $data = $this->getItem();
        }
        return $data;
    }
}
