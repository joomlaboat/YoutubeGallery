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
