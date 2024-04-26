<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Table\Table;

// import Joomla table library
//jimport('joomla.database.table');

/**
 * Youtube Gallery - Categories Table class
 */
class YoutubeGalleryTableCategories extends Table
{
    /**
     * Constructor
     *
     * @param object Database connector object
     */

    var $id = null;
    var $es_categoryname = null;
    var $es_description = null;
    var $es_image = null;
    var $es_parentid = null;

    function __construct(&$db)
    {
        parent::__construct('#__customtables_table_youtubegallerycategories', 'id', $db);
    }
}
