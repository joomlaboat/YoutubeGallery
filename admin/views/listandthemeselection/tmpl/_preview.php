<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

	//Show preview
	
	$videolist=$input->getInt('videolist');
	$theme=$input->getInt('theme');
	
	$htmlresult='{youtubegalleryid='.$videolist.','.$theme.'}';
	
	require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'plugins'.DIRECTORY_SEPARATOR.'content'.DIRECTORY_SEPARATOR.'youtubegallery'.DIRECTORY_SEPARATOR.'youtubegallery.php');
	
	plgContentYoutubeGallery::plgYoutubeGallery($htmlresult, true);
	
	
//width:100%;zoom: 0.5; 
	echo '
		<div style="width:100%;vertical-align:top;transform-origin: center top;
	padding:0;margin:0;transform: scale(0.5);   -moz-transform: scale(0.5);">';
	echo $htmlresult.'</div>';
