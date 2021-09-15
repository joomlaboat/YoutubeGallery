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

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->getCmd('task'));

// Redirect if set by the controller
$controller->redirect();


/* 
// import joomla controller library
jimport('joomla.application.component.controller');
 
$jinput=JFactory::getApplication()->input;
 
$task=$jinput->getVar( 'task');
$view=$jinput->getCmd( 'view');
$t='categories.delete';

if($view=='' and $task==$t)
{
	$controllerName = 'categories';
	$jinput->set('view', 'categories');
}
else
	$controllerName = $jinput->getCmd( 'view', 'linkslist' );
 
require_once(JPATH_ADMINISTRATOR.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'youtubegallery.php');
 
$canDoThemeList = YoutubeGalleryHelper::getActions('themelist');
$canViewThemeList = $canDoThemeList->get('themelist.view');
if($controllerName=='themelist' and !$canViewThemeList)
{
	$task=$jinput->set( 'task', '');
	$view=$jinput->set( 'view', 'linkslist');
	JFactory::getApplication()->enqueueMessage(JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED'), 'error');
}

$canDoCategories = YoutubeGalleryHelper::getActions('categories');
$canViewCategories = $canDoCategories->get('categories.view');
if($controllerName=='categories' and !$canViewCategories)
{
	$task=$jinput->set( 'task', '');
	$view=$jinput->set( 'view', 'linkslist');
	JFactory::getApplication()->enqueueMessage(JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED'), 'error');
}

$canDoSettings = YoutubeGalleryHelper::getActions('settings');
$canViewSettings = $canDoSettings->get('settings.view');
if($controllerName=='settings' and !$canViewSettings)
{
	$task=$jinput->set( 'task', '');
	$view=$jinput->set( 'view', 'linkslist');
	JFactory::getApplication()->enqueueMessage(JText::_( 'JGLOBAL_AUTH_ACCESS_DENIED'), 'error');
}

switch($controllerName)
{
	case 'linkslist':
	
		JSubMenuHelper::addEntry(JText::_('Video Lists'), 'index.php?option=com_youtubegallery&view=linkslist', true);
		
		if($canViewThemeList)
			JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_youtubegallery&view=themelist', false);
		
		if($canViewCategories)
			JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_youtubegallery&view=categories', false);
		
		if($canViewSettings)
			JSubMenuHelper::addEntry(JText::_('Settings'), 'index.php?option=com_youtubegallery&view=settings&layout=edit', false);
	break;

	case 'themelist':
		
		JSubMenuHelper::addEntry(JText::_('Video Lists'), 'index.php?option=com_youtubegallery&view=linkslist', false);
		
		if($canViewThemeList)
			JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_youtubegallery&view=themelist', true);
		
		if($canViewCategories)
			JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_youtubegallery&view=categories', false);
		
		if($canViewSettings)
			JSubMenuHelper::addEntry(JText::_('Settings'), 'index.php?option=com_youtubegallery&view=settings&layout=edit', false);
	break;

	case 'categories':
		
		JSubMenuHelper::addEntry(JText::_('Video Lists'), 'index.php?option=com_youtubegallery&view=linkslist', false);
		
		if($canViewThemeList)
			JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_youtubegallery&view=themelist', false);
		
		if($canViewCategories)
			JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_youtubegallery&view=categories', true);
		
		if($canViewSettings)
			JSubMenuHelper::addEntry(JText::_('Settings'), 'index.php?option=com_youtubegallery&view=settings&layout=edit', false);
	break;

	case 'settings':
		
		JSubMenuHelper::addEntry(JText::_('Video Lists'), 'index.php?option=com_youtubegallery&view=linkslist', false);
		
		if($canViewThemeList)
			JSubMenuHelper::addEntry(JText::_('Themes'), 'index.php?option=com_youtubegallery&view=themelist', false);
		
		if($canViewCategories)
			JSubMenuHelper::addEntry(JText::_('Categories'), 'index.php?option=com_youtubegallery&view=categories', false);
		
		if($canViewSettings)
			JSubMenuHelper::addEntry(JText::_('Settings'), 'index.php?option=com_youtubegallery&view=settings&layout=edit', true);
	break;

}
 
$controller = JControllerLegacy::getInstance('YoutubeGallery');
	
// Perform the Request task
$controller->execute($jinput->getCmd('task'));
$controller->redirect();

*/