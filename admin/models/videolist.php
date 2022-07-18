<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');
// import the Joomla modellist library
jimport('joomla.application.component.modellist');

/**
 * VideoList Model
 */
class YoutubeGalleryModelVideoList extends JModelList
{
    public function getTable($type = 'VideoList', $prefix = 'YoutubeGalleryTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return string  An SQL query
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = Factory::getDBO();

        $where = array();

        $context = 'com_youtubegallery.videolist.';
        $mainframe = Factory::getApplication();
        //$search	= $mainframe->getUserStateFromRequest($context."search",'search','',	'string' );
        //$search	= strtolower(trim(preg_replace("/[^a-zA-Z0-9 ]/", "", $search)));

        $where[] = 'es_videolist=' . (int)Factory::getApplication()->input->getInt('listid');

        $search = $this->getState('filter.search');
        $search = strtolower(trim(preg_replace("/[^a-zA-Z0-9 ]/", "", $search)));

        if ($search != '')
            $where[] = '(
					INSTR(es_link,' . $db->quote($search) . ') OR 
					INSTR(es_title,' . $db->quote($search) . ') OR 
					INSTR(es_description,' . $db->quote($search) . ')
				)';

        $query = $db->getQuery(true);
        // Select some fields
        $query->select(array('*'));
        // From the Youtube Gallery Videos table
        $query->from('#__customtables_table_youtubegalleryvideos');

        if (count($where) > 0)
            $query->where(implode(' AND ', $where));

        return $query;
    }
}
