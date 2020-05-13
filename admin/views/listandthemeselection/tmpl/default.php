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


$user       = JFactory::getUser();
$input      = JFactory::getApplication()->input;

if($input->getCmd('task')=='enableplugin')
{
	$this->EnablePlugin();
	$msg= '<p>Content - Youtube Gallery Plugin enabled.</p>';
	JFactory::getApplication()->enqueueMessage(JText::_($msg), 'notice');
}


$plugin_enabled=false;
	
	
	

	//Check Plugin
	$plugin=$this->checkIfPluginIsEnabled();
	if($plugin==null)
	{
		$msg= '<p>Content - Youtube Gallery Plugin not installed. It\'s a part of Youtube Gallery Package. Uninstall Youtube Gallery and install it again.</p>';
		JFactory::getApplication()->enqueueMessage(JText::_($msg), 'error');
	}
	
	
	if($plugin['enabled']==0)
	{
		$onClick='location.href="/administrator/index.php?option=com_youtubegallery&view=listandthemeselection&tmpl=component&task=enableplugin"';
		$msg= '<p>Content - Youtube Gallery Plugin not enabled. <button class="btn btn-success button-save-selected" type="button" onclick=\''.$onClick.'\'>'.JText::_('Enable Plugin').'</button></p>';
		JFactory::getApplication()->enqueueMessage(JText::_($msg), 'warning');
	}
	else
		$plugin_enabled=true;


$videolist=$input->getInt('videolist');
if($input->getCmd('task')=='preview' and $videolist!=0)
{
	include('_preview.php');
}
else
{
	include('_selection.php');
}
