<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * YoutubeGallery - Settings Model
 */
class YoutubeGalleryModelSettings extends JModelAdmin
{
        /**
         * Returns a reference to the a Table object, always creating it.
         *
         * @param       type    The table type to instantiate
         * @param       string  A prefix for the table class name. Optional.
         * @param       array   Configuration array for model. Optional.
         * @return      JTable  A database object

         */
		public $id;


        public function getTable($type = 'Settings', $prefix = 'YoutubeGalleryTable', $config = array())
        {
                return JTable::getInstance($type, $prefix, $config);
        }
        /**
         * Method to get the record form.
         *
         * @param       array   $data           Data for the form.
         * @param       boolean $loadData       True if the form is to load its own data (default case), false if not.
         * @return      mixed   A JForm object on success, false on failure

         */

        public function getForm($data = array(), $loadData = true)
        {
                // Get the form.

                $form = $this->loadForm('com_youtubegallery.settings', 'settings', array('control' => 'jform', 'load_data' => $loadData));
                if (empty($form))
                {
                        return false;
                }

                return $form;
        }


	static protected function makeQueryLine($field,$value)
	{
		$db = JFactory::getDBO();
		
		return 'INSERT INTO #__youtubegallery_settings (`option`, `value`)
		VALUES ('.$db->quote($field).', '.$db->quote($value).')
		ON DUPLICATE KEY UPDATE `option`='.$db->quote($field).', `value`='.$db->quote($value);
	}

    function store()
    {
		$jform=JFactory::getApplication()->input->getVar('jform');
		$allowsef=trim(preg_replace("/[^0-9]/", "", $jform['allowsef']));

		$joomlaboat_api_host=trim(preg_replace("/[^^a-zA-Z0-9~\-#@:_(),.!@\/]/", "", JFactory::getApplication()->input->getVar('joomlaboat_api_host')));
		$joomlaboat_api_key=trim(preg_replace("/[^a-zA-Z0-9~_-]/", "", JFactory::getApplication()->input->getVar('joomlaboat_api_key')));

		$db = JFactory::getDBO();
		$query=array();
		$query[] = YoutubeGalleryModelSettings::makeQueryLine('allowsef',$allowsef);
		$query[] = YoutubeGalleryModelSettings::makeQueryLine('joomlaboat_api_host',$joomlaboat_api_host);
		$query[] = YoutubeGalleryModelSettings::makeQueryLine('joomlaboat_api_key',$joomlaboat_api_key);
				
		foreach($query as $q)
		{
			$db->setQuery($q);
			if (!$db->query())    die ( $db->stderr());
		}
		return true;
    }
}
