<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

/**
 * YouTubeGallery Form Field class for the Youtube Gallery component
 */

$versionObject = new Version;
$version = (int)$versionObject->getShortVersion();

if ($version < 4) {
    JFormHelper::loadFieldClass('list');

    class JFormFieldYGCategory extends JFormFieldList
    {
        /**
         * The field type.
         *
         * @var         string
         */
        public $type = 'YGCategory';

        /**
         * Method to get a list of options for a list input.
         *
         * @return      array           An array of JHtml options.
         */
        protected function getOptions()
        {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select(array('id', 'es_categoryname'));
            $query->from('#__customtables_table_youtubegallerycategories');
            $db->setQuery((string)$query);
            $records = $db->loadObjectList();

            $options = array();

            $options[] = JHtml::_('select.option', 0, Text::_('COM_YOUTUBEGALLERY_SELECT_CATEGORY'));

            if ($records) {
                foreach ($records as $record) {
                    $options[] = JHtml::_('select.option', $record->id, $record->es_categoryname);

                }
            }
            return array_merge(parent::getOptions(), $options);
        }
    }

} else {
    class JFormFieldYGCategory extends FormField
    {
        /**
         * The field type.
         *
         * @var         string
         */
        public $type = 'YGCategory';

        protected $layout = 'joomla.form.field.list'; //Needed for Joomla 5


        protected function getInput()
        {
            $data = $this->getLayoutData();
            $data['options'] = $this->getOptions();
            return $this->getRenderer($this->layout)->render($data);
        }

        /**
         * Method to get a list of options for a list input.
         *
         * @return      array           An array of JHtml options.
         */
        public function getOptions()
        {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select(array('id', 'es_categoryname'));
            $query->from('#__customtables_table_youtubegallerycategories');
            $db->setQuery((string)$query);
            $records = $db->loadObjectList();

            $options = array();

            $options[] = HTMLHelper::_('select.option', 0, Text::_('COM_YOUTUBEGALLERY_SELECT_CATEGORY'));

            if ($records) {
                foreach ($records as $record) {
                    $options[] = HTMLHelper::_('select.option', $record->id, $record->es_categoryname);

                }
            }
            return $options;
        }
    }
}