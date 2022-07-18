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
class JFormFieldCategoryParent extends JFormFieldList
{
	protected $type = 'CategoryParent';
	
	/**
	* Method to get a list of options for a list input.
	*
	* @return array An array of JHtml options.
	*/
	
	protected function getOptions()
	{
		$current_category_id = Factory::getApplication()->input->getInt('id', '0');

		$db = Factory::getDBO();

		$query='SELECT id,es_categoryname,es_parentid FROM #__customtables_table_youtubegallerycategories';
		$db->setQuery((string)$query);

		$messages = $db->loadObjectList();

		$options = array();

		$options[] = JHtml::_('select.option', 0, JText::_( 'COM_YOUTUBEGALLERY_SELECT_CATEGORYROOT' ));

		$children=$this->getAllChildren($current_category_id);

		if ($messages)
		{
			foreach($messages as $message)
			{
				if($current_category_id==0)
				    $options[] = JHtml::_('select.option', $message->id, $message->es_categoryname);
				else
				{
					if($message->id!=$current_category_id and $message->es_parentid!=$current_category_id and !in_array($message->id,$children))
						$options[] = JHtml::_('select.option', $message->id, $message->es_categoryname);
				}
			}
		}
		
		$options = array_merge(parent::getOptions(), $options);
		return $options;
	}

	protected function getAllChildren($parentid)
	{
		$children=array();
		if($parentid==0)
			return $children;

		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select(array('id', 'es_parentid'));
		$query->from('#__customtables_table_youtubegallerycategories');
		$query->where('es_parentid='.$parentid);
		$db->setQuery((string)$query);

		$rows = $db->loadObjectList();
		foreach($rows as $row)
		{
			$children[]=$row->id;
			$grand_children=$this->getAllChildren($row->id);
			if(count($grand_children)>0)
						     $children=array_merge($children,$grand_children);
		}
		return $children;
	}
}
