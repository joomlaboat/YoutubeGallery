<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use YoutubeGallery\Plugin\Content\YoutubeGallery\Extension\YoutubeGallery;

//Show preview
$jInput = Factory::getApplication()->input;
$videolist = $jInput->getInt('videolist');
$theme = $jInput->getInt('theme');

if ($videolist == 0) {
    echo Text::_('COM_YOUTUBEGALLERY_VIDEO_LIST_NOT_SELECTED');
} elseif ($theme == 0) {
    echo Text::_('COM_YOUTUBEGALLERY_THEME_NOT_SELECTED');
} else {
    $htmlResult = '{youtubegalleryid=' . $videolist . ',' . $theme . '}';

    require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR . 'content' . DIRECTORY_SEPARATOR
        . 'youtubegallery' . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Extension' . DIRECTORY_SEPARATOR . 'youtubegallery.php');

    YoutubeGallery::plgYoutubeGallery($htmlResult, true);

    echo '
		<div style="width:100%;vertical-align:top;transform-origin: center top;
	padding:0;margin:0;transform: scale(0.5);-moz-transform: scale(0.5);">';
    echo $htmlResult . '</div>';
}
