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
use CustomTables\database;

$versionObject = new Version;
$version = (int)$versionObject->getShortVersion();

if(!class_exists('database')){
	$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR;
	require_once($path . 'loader.php');
	YGLoadClasses();
}

if ($version < 4) {

// import the list field type
	jimport('joomla.form.helper');
	JFormHelper::loadFieldClass('list');

	/**
	 * YouTubeGallery Form Field class for the Youtube Gallery component
	 */
	class JFormFieldVideoLists extends JFormFieldList
	{
		protected $type = 'VideoLists';

		/**
		 * Method to get a list of options for a list input.
		 *
		 * @return array An array of JHtml options.
		 */

		protected function getOptions()
		{
			$db = database::getDB();

			$query = 'SELECT id,es_listname FROM #__customtables_table_youtubegalleryvideolists WHERE published=1';
			$db->setQuery($query);

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

} else {
	class JFormFieldVideoLists extends FormField
	{
		protected $layout = 'joomla.form.field.list'; //Needed for Joomla 5
		/**
		 * The field type.
		 *
		 * @var         string
		 */
		protected $type = 'VideoLists';

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
			$db = database::getDB();
			$query = $db->getQuery(true);
			$query->select(array('id', 'es_listname'));
			$query->from('#__customtables_table_youtubegalleryvideolists');
			$query->where('published=1');
			$db->setQuery((string)$query);
			$messages = $db->loadObjectList();
			$options = array();
			if ($messages) {
				foreach ($messages as $message) {
					$options[] = HTMLHelper::_('select.option', $message->id, $message->es_listname);

				}
			}
			return $options;
		}
	}
}