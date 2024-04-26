<?php
/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_youtubegallery')) {
    Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
};

// require helper files
JLoader::register('YoutubeGalleryHelper', dirname(__FILE__) . '/helpers/youtubegallery.php');
JLoader::register('JHtmlBatch_', dirname(__FILE__) . '/helpers/html/batch_.php');

// import joomla controller library
jimport('joomla.application.component.controller');

$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR;
require_once($path . 'loader.php');
YGLoadClasses();

// Get an instance of the controller prefixed by Customtables
$controller = BaseController::getInstance('YoutubeGallery');
//$controller = JControllerLegacy::getInstance('YoutubeGallery');

/// Perform the Request task
$task = Factory::getApplication()->input->getCmd('task');
// Perform the Request task
try {
    $controller->execute($task);
} catch (Exception $e) {
    die($e->getMessage());
}

//$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();
