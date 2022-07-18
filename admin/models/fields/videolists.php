<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die;

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * YoutubeGallery Form Field class for the Youtube Gallery component
 */
class JFormFieldVideoLists extends JFormFieldList
{
    /**
     * The field type.
     *
     * @var         string
     */
    protected $type = 'VideoLists';

    /**
     * Method to get a list of options for a list input.
     *
     * @return      array           An array of JHtml options.
     */
    protected function getOptions()
    {
        $db = Factory::getDBO();
        $query = $db->getQuery(true);
        $query->select(array('id', 'es_listname'));
        $query->from('#__customtables_table_youtubegalleryvideolists');
        $db->setQuery((string)$query);
        $messages = $db->loadObjectList();
        $options = array();
        if ($messages) {
            foreach ($messages as $message) {
                $options[] = JHtml::_('select.option', $message->id, $message->es_listname);

            }
        }
        $options = array_merge(parent::getOptions(), $options);
        return $options;
    }
}
