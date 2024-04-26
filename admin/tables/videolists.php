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
 * Youtube Gallery - Video Lists Table class
 */
class YoutubeGalleryTableVideolists extends Table
{
    /**
     * Constructor
     *
     * @param object Database connector object
     */

    var $id = null;
    var $es_listname = null;
    var $es_videolist = null;
    var $es_catid = null;
    var $es_updateperiod = null;
    var $es_lastplaylistupdate = null;
    var $es_description = null;
    /*var $es_author = null;*/
    var $es_watchusergroup = null;
    var $es_authorurl = null;
    var $es_image = null;
    var $es_note = null;

    function __construct(&$db)
    {
        parent::__construct('#__customtables_table_youtubegalleryvideolists', 'id', $db);
    }
}
