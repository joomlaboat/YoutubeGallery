<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery'
    . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . 'linksform' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR;

if ($this->version < 4)
    require_once($path . '_modal.php');
else
    require_once($path . '_modal_quatro.php');