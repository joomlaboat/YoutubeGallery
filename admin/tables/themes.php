<?php
/**
 * YouTubeGallery Joomla! Native Component
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
 * YouTube Gallery - Themes Table class
 */
class YoutubeGalleryTableThemes extends Table
{
    var $id = null;
    var $es_themename = null;

    var $es_width = null;
    var $es_height = null;
    var $es_playvideo = null;
    var $es_repeat = null;
    var $es_fullscreen = null;
    var $es_autoplay = null;
    var $es_related = null;
//	var $es_showinfo = null;
    var $es_bgcolor = null;
    var $es_cols = null;
//	var $es_showtitle = null;

    var $es_cssstyle = null;
    var $es_navbarstyle = null;
    var $es_thumbnailstyle = null;
    //var $es_linestyle = null;

//	var $es_showlistname = null;
    var $es_listnamestyle = null;

    //var $es_showactivevideotitle = null;
    //var $es_activevideotitlestyle = null;

    //var $es_description = null;
    //var $es_descr_position = null;
    var $es_descrstyle = null;


    var $es_colorone = null;
    var $es_colortwo = null;

    var $es_border = null;

    var $es_openinnewwindow = null;
    var $es_rel = null;
    var $es_hrefaddon = null;

//	var $es_pagination = null;
    var $es_customlimit = null;


    var $es_controls = null;
    var $es_youtubeparams = null;
    var $es_playertype = null;
    var $es_useglass = null;
    var $es_logocover = null;

    var $es_customlayout = null;
    var $es_prepareheadtags = null;

    var $es_muteonplay = null;
    var $es_lastplaylistupdate = null;
    var $es_volume = null;

    var $es_orderby = null;
    var $es_customnavlayout = null;
    var $es_responsive = null;

    var $es_mediafolder = null;
    var $es_readonly = null;
    var $es_headscript = null;
    var $es_themedescription = null;
    var $es_nocookie = null;
    var $es_changepagetitle = null;

    var $es_allowplaylist = null;

    function __construct(&$db)
    {
        parent::__construct('#__customtables_table_youtubegallerythemes', 'id', $db);
    }
}
