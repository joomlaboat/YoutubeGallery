<?php
/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_youtubegallery'))
{
	JFactory::getApplication()->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
};

// require helper files
JLoader::register('YoutubeGalleryHelper', dirname(__FILE__) . '/helpers/youtubegallery.php');
JLoader::register('JHtmlBatch_', dirname(__FILE__) . '/helpers/html/batch_.php');

// import joomla controller library
jimport('joomla.application.component.controller');

$path=JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'libraries'.DIRECTORY_SEPARATOR.'youtubegallery'.DIRECTORY_SEPARATOR;
require_once($path.'loader.php');
YGLoadClasses();

// Get an instance of the controller prefixed by Customtables
$controller = JControllerLegacy::getInstance('YoutubeGallery');

/// Perform the Request task
$task = JFactory::getApplication()->input->getCmd('task');

$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
