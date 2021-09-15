<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/
 
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Registry\Registry;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

class YoutubeGalleryModelLinksForm extends JModelAdmin
{
	protected $text_prefix = 'COM_YOUTUBEGALLERY';

	public $typeAlias = 'com_youtubegallery.linksform';

	public function getTable($type = 'Videolists', $prefix = 'YoutubeGalleryTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			if (!empty($item->params) && !is_array($item->params))
			{
				// Convert the params field to an array.
				$registry = new Registry;
				$registry->loadString($item->params);
				$item->params = $registry->toArray();
			}

			if (!empty($item->metadata))
			{
				// Convert the metadata field to an array.
				$registry = new Registry;
				$registry->loadString($item->metadata);
				$item->metadata = $registry->toArray();
			}

			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, 'com_youtubegallery.linksform');
			}
		}

		return $item;
	}

	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_youtubegallery.linksform', 'linksform', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if (JFactory::getApplication()->input->get('a_id'))
		{
			$id = JFactory::getApplication()->input->get('a_id', 0, 'INT');
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = JFactory::getApplication()->input->get('id', 0, 'INT');
		}

		$user = JFactory::getUser();

		// Check for existing item.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('core.edit.state', 'com_youtubegallery.linksform.' . (int) $id))
			|| ($id == 0 && !$user->authorise('core.edit.state', 'com_youtubegallery')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('published', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
		// If this is a new item insure the greated by is set.
		if (0 == $id)
		{
			// Set the created_by to this user
		}

		// Only load these values if no id is found
		if (0 == $id)
		{
			// Set redirected field name
			$redirectedField = JFactory::getApplication()->input->get('ref', null, 'STRING');
			// Set redirected field value
			$redirectedValue = JFactory::getApplication()->input->get('refid', 0, 'INT');
			if (0 != $redirectedValue && $redirectedField)
			{
				// Now set the local-redirected field default value
				$form->setValue($redirectedField, null, $redirectedValue);
			}
		}

		return $form;
	}

	public function getScript()
	{
		return JURI::root(true).'/administrator/components/com_youtubegallery/models/forms/linksform.js';
	}

	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			$user = JFactory::getUser();
			// The record has been set. Check the record permissions.
			return $user->authorise('linkslist.delete', 'com_youtubegallery.linkslist.' . (int) $record->id);
		}
		return false;
	}

	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		$recordId = (!empty($record->id)) ? $record->id : 0;

		if ($recordId)
		{
			// The record has been set. Check the record permissions.
			$permission = $user->authorise('linkslist.edit.state', 'com_youtubegallery.linkslist.' . (int) $recordId);
			if (!$permission && !is_null($permission))
			{
				return false;
			}
		}
		// In the absense of better information, revert to the component permissions.
		return parent::canEditState($record);
	}

	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.

		return JFactory::getUser()->authorise('linkslist.edit', 'com_youtubegallery.linkslist.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or parent::allowEdit($data, $key);
	}
	 
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_youtubegallery.edit.linksform.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	protected function getUniqeFields()
	{
		return false;
	}
	
	public function save($data)
	{
       	$linksform_row = $this->getTable('videolists');
		
		$jinput = JFactory::getApplication()->input;
        $data = $jinput->get( 'jform',array(),'ARRAY');
		
       	$post = array();

        $listname=trim(preg_replace("/[^a-zA-Z0-9_]/", "", $data['es_listname']));

        $data['jform']['es_listname']=$listname;

       	if (!$linksform_row->bind($data))
       	{
            echo 'Cannot bind.';
       		return false;
       	}

       	// Make sure the  record is valid
       	if (!$linksform_row->check())
       	{
            echo 'Cannot check.';
       		return false;
       	}

       	// Store
       	if (!$linksform_row->store())
       	{
            echo '<p>Cannot store.</p>
			<p>There is some fields missing.</p>
';
     		return false;
       	}

		YouTubeGalleryDB::update_cache_table($linksform_row,false);

       	$this->id=$linksform_row->id;

       	return true;
	}

	protected function generateUniqe($field,$value)
	{
		// set field value uniqe
		$table = $this->getTable();

		while ($table->load(array($field => $value)))
		{
			$value = JString::increment($value);
		}

		return $value;
	}
}
