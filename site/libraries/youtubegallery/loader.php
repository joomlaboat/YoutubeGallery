<?php

use Joomla\CMS\Component\ComponentHelper;

defined('JPATH_PLATFORM') or die;

function YGLoadClasses(): void
{
	$CustomTables_path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customtables' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'customtables' . DIRECTORY_SEPARATOR;
	$loader_file = $CustomTables_path . 'loader.php';
	$component_name = 'com_customtables';

	if (!file_exists($loader_file)) {
		$CustomTables_path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'customtables' . DIRECTORY_SEPARATOR;
		$loader_file = $CustomTables_path . 'loader.php';
		$component_name = 'com_youtubegallery';
	}

	require_once($loader_file);

	$params = ComponentHelper::getParams($component_name);
	$loadTwig = $params->get('loadTwig') ?? true;
	CustomTablesLoader($include_utilities = true, false, null, $component_name, $loadTwig);

	$path = 'classes' . DIRECTORY_SEPARATOR;

	require_once($path . 'data.php');
	require_once($path . 'db.php');
	require_once($path . 'misc.php');
	require_once($path . 'gallery_list.php');
	require_once($path . 'hotplayer.php');
	require_once($path . 'layoutrenderer.php');
	require_once($path . 'misc.php');
	require_once($path . 'players.php');
	require_once($path . 'render.php');
	require_once($path . 'render_css.php');
	require_once($path . 'responsive_js.php');
	require_once($path . 'socialbuttons.php');
	require_once($path . 'thumbnails.php');

	require_once($path . 'providers' . DIRECTORY_SEPARATOR . 'youtube.php');
}