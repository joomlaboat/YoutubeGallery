<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\DatabaseQuery;
use Joomla\Database\QueryInterface;

/**
 * Categories Model
 */
class YoutubeGalleryModelCategories extends ListModel
{
    /**
     * Method to build an SQL query to load the list data.
     *
     * @return void  An SQL query
     * @throws Exception
     */

    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'published', 'a.published',
                'es_categoryname', 'a.es_categoryname'
            );
        }

        parent::__construct($config);
    }

    /**
     * Method to get an array of data items.
     *
     * @return  mixed  An array of data items on success, false on failure.
     */
    public function getItems(): mixed
    {
        // load parent items
        return parent::getItems();
    }

    /**
     * Method to autopopulate the model state.
     *
     * @param string $ordering
     * @param string $direction
     * @return  void
     */
    protected function populateState($ordering = 'a.id', $direction = 'asc'): void
    {
        // Load the parameters.
        $this->setState('params', ComponentHelper::getParams('com_youtubegallery'));

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Method to build an SQL query to load the list data.
     *
     * @return    DatabaseQuery|QueryInterface    An SQL query
     */
    protected function getListQuery()
    {
        // Create a new query object.
        $db = Factory::getDBO();
        $query = $db->getQuery(true);

        // Select some fields
        //$query->select('a.*');
        $selects = [];
        $selects[] = 'id';
        $selects[] = 'published';
        $selects[] = 'es_categoryname';

        $query->select($selects);

        // From the customtables_item table
        $query->from($db->quoteName('#__customtables_table_youtubegallerycategories', 'a'));

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = ' . (int)$published);
        } elseif ($published === '') {
            $query->where('(a.published = 0 OR a.published = 1)');
        }
        // Filter by search.
        $search = $this->getState('filter.search');
        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = ' . (int)substr($search, 3));
            } else {
                $search = $db->quote('%' . $db->escape($search) . '%');
                $query->where('(a.es_categoryname LIKE ' . $search . ')');
            }
        }

        // Add the list ordering clause.
        $orderCol = $this->state->get('list.ordering', 'a.id');
        $orderDirection = $this->state->get('list.direction', 'asc');
        if ($orderCol != '') {
            $query->order($db->escape($orderCol . ' ' . $orderDirection));
        }

        return $query;
    }

    /**
     * Method to get a store id based on model configuration state.
     *
     * @return  string  A store id.
     *
     */

    protected function getStoreId($id = ''): string
    {
        // Compile the store id.
        $id .= ':' . $this->getState('filter.search');
        $id .= ':' . $this->getState('filter.published');

        return parent::getStoreId($id);
    }
}
