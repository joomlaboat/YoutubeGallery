<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');
?>
    <!--
    <link href="<?php echo Uri::root(true); ?>administrator//media/jui/css/chosen.css" rel="stylesheet"/>
    <link href="<?php echo Uri::root(true); ?>administrator/templates/isis/css/template.css" rel="stylesheet"/>

    <script src="/media/jui/js/jquery.min.js"></script>
    <script src="/media/jui/js/jquery-noconflict.js"></script>
    <script src="/media/jui/js/jquery-migrate.min.js"></script>
    <script src="/media/jui/js/chosen.jquery.min.js"></script>
    <script src="/media/jui/js/bootstrap.min.js"></script>
    <script src="/media/system/js/core.js"></script>
-->
<?php /*
    <!--[if lte IE 9]>
    <script src="/media/system/js/polyfill.filter.js"></script><![endif]-->
    <script src="/administrator/templates/isis/js/template.js"></script>
    <!--[if lt IE 9]>
    <script src="/media/jui/js/html5.js"></script><![endif]-->
 */ ?>


<?php
require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery'
    . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'listandthemeselection' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR . '_modal.php');