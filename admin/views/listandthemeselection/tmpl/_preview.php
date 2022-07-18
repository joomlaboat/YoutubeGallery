<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

//Show preview

$jinput = Factory::getApplication()->input;
$videolist = $jinput->getInt('videolist');
$theme = $jinput->getInt('theme');

if ($videolist == 0) {
    echo 'Video list not selected.';
} elseif ($theme == 0) {
    echo 'Theme not selected.';
} else {
    $htmlresult = '{youtubegalleryid=' . $videolist . ',' . $theme . '}';

    require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR . 'youtubegallery.php');

    plgContentYoutubeGallery::plgYoutubeGallery($htmlresult, true);


    echo '
		<div style="width:100%;vertical-align:top;transform-origin: center top;
	padding:0;margin:0;transform: scale(0.5);   -moz-transform: scale(0.5);">';
    echo $htmlresult . '</div>';
}
