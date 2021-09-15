<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Database\ParameterType;

/**
 * linkslist Model
 */
class YoutubeGalleryModelLinksList extends JModelList
{
	/**
	* Method to build an SQL query to load the list data.
	*
	* @return string  An SQL query
	*/
	
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'published','a.published',
				'es_listname','a.es_listname'
			);
		}

		parent::__construct($config);
	}
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'a.id', $direction = 'asc')
	{
		// Load the parameters.
		$this->setState('params', ComponentHelper::getParams('com_youtubegallery'));

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{ 
		// load parent items
		$items = parent::getItems();  
        
		// return items
		return $items;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		//$query->select('a.*');
		$selects=[];
		$selects[]='id';
		$selects[]='published';
		$selects[]='es_listname';
		$selects[]='es_updateperiod';
		$selects[]='es_lastplaylistupdate';
		$selects[]='(SELECT es_categoryname FROM #__customtables_table_youtubegallerycategories AS c WHERE c.id = a.es_catid LIMIT 1) AS categoryname';
		$selects[]='(SELECT COUNT(v.id) FROM #__customtables_table_youtubegalleryvideos AS v WHERE v.es_videolist = a.id AND v.es_isvideo = 1 LIMIT 1) AS number_of_videos';
		
		$query->select($selects);
		
		// From the customtables_item table
		$query->from($db->quoteName('#__customtables_table_youtubegalleryvideolists', 'a'));

		// Filter by published state
		$published = $this->getState('filter.published');
		
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published == null or $published == '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.es_listname LIKE '.$search.')');
			}
		}
		
		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'asc');	
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		
		return $query;
	} 
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');

		return parent::getStoreId($id);
	}	
}
