<?php

defined('JPATH_PLATFORM') or die;

function YGLoadClasses()
{
	//$component_path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR;
	
	//$path = $component_path .'classes' . DIRECTORY_SEPARATOR;
	$path = 'classes' . DIRECTORY_SEPARATOR;
	
	require_once($path.'data.php');
	require_once($path.'db.php');
	require_once($path.'misc.php');
	require_once($path.'gallery_list.php');
	require_once($path.'hotplayer.php');
	require_once($path.'layoutrenderer.php');
	require_once($path.'misc.php');
	require_once($path.'pagination.php');
	require_once($path.'pagination_render.php');
	require_once($path.'players.php');
	require_once($path.'render.php');
	require_once($path.'render_css.php');
	require_once($path.'responsive_js.php');
	require_once($path.'socialbuttons.php');
	require_once($path.'thumbnails.php');
	
	$path = 'classes' . DIRECTORY_SEPARATOR . 'providers' . DIRECTORY_SEPARATOR;
	
	require_once($path.'dailymotion.php');
	require_once($path.'soundcloud.php');
	require_once($path.'ustream.php');
	require_once($path.'ustreamlive.php');
	require_once($path.'vimeo.php');
	require_once($path.'youtube.php');
}