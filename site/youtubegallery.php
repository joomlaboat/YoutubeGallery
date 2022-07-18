<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

// import joomla controller library
jimport('joomla.application.component.controller');

// Get an instance of the controller prefixed by Youtube Gallery

$path = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR;
require_once($path . 'loader.php');
YGLoadClasses();

$controller = JControllerLegacy::getInstance('YoutubeGallery');


// Perform the Request task
$controller->execute(Factory::getApplication()->input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();
