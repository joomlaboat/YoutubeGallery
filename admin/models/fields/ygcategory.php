<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

// import the list field type
//jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * YoutubeGallery Form Field class for the Youtube Gallery component
 */
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

        $options[] = JHtml::_('select.option', 0, JText::_('COM_YOUTUBEGALLERY_SELECT_CATEGORY'));

        if ($records) {
            foreach ($records as $record) {
                $options[] = JHtml::_('select.option', $record->id, $record->es_categoryname);

            }
        }
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
