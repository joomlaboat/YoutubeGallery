<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');
 
// import Joomla table library
jimport('joomla.database.table');
 
/**
 * Youtube Gallery - Video Lists Table class
 */
class YoutubeGalleryTableSettings extends JTable
{
	/**
         * Constructor
         *
         * @param object Database connector object
         */

	var $id = null;
	var $es_option = null;
	var $es_value = null;

        function __construct(&$db) 
        {
                parent::__construct('#__customtables_table_youtubegallerysettings', 'id', $db);
        }
}
