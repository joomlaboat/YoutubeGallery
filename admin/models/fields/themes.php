<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Version;

$versionObject = new Version;
$version = (int)$versionObject->getShortVersion();

if ($version < 4) {
// import the list field type
    jimport('joomla.form.helper');
    JFormHelper::loadFieldClass('list');


    /**
     * YouTubeGallery Form Field class for the Youtube Gallery component
     */
    class JFormFieldThemes extends JFormFieldList
    {
        protected $type = 'themes';

        protected function getOptions()
        {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select(array('id', 'es_themename'));
            $query->from('#__customtables_table_youtubegallerythemes');
            $db->setQuery((string)$query);
            $messages = $db->loadObjectList();
            $options = array();
            if ($messages) {
                foreach ($messages as $message)
                    $options[] = JHtml::_('select.option', $message->id, $message->es_themename);
            }

            $options = array_merge(parent::getOptions(), $options);
            return $options;
        }
    }
} else {
    class JFormFieldThemes extends FormField
    {
        protected $type = 'themes';
        protected $layout = 'joomla.form.field.list'; //Needed for Joomla 5

        protected function getInput()
        {
            $data = $this->getLayoutData();
            $data['options'] = $this->getOptions();
            return $this->getRenderer($this->layout)->render($data);
        }

        protected function getOptions()
        {
            $db = Factory::getDBO();
            $query = $db->getQuery(true);
            $query->select(array('id', 'es_themename'));
            $query->from('#__customtables_table_youtubegallerythemes');
            $db->setQuery((string)$query);
            $messages = $db->loadObjectList();
            $options = array();
            if ($messages) {
                foreach ($messages as $message)
                    $options[] = HTMLHelper::_('select.option', $message->id, $message->es_themename);
            }
            return $options;
        }
    }
}