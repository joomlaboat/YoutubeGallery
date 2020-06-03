<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

$input      = JFactory::getApplication()->input;

if($input->getCmd('task')=='enableplugin')
{
	$this->EnablePlugin();
	$msg= '<p>Content - Youtube Gallery Plugin enabled.</p>';
	JFactory::getApplication()->enqueueMessage(JText::_($msg), 'notice');
}

//Check Plugin
$plugin=$this->checkIfPluginIsEnabled();
	
if($plugin==null)
{
	$msg= '<p>Youtube Gallery Plugin is not installed.<br/>'
		.'You can get it free here: <a href="https://github.com/joomlaboat/plg_youtubegallery" target="_blank">GitHub</a><br/>'
		.'<a href="https://github.com/joomlaboat/plg_youtubegallery/archive/master.zip" traget="_blank">Download YoutubeGallery Plugin</a></p>';
		
	JFactory::getApplication()->enqueueMessage(JText::_($msg), 'warning');
}
elseif($plugin['enabled']==0)
{
	$onClick='location.href="/administrator/index.php?option=com_youtubegallery&view=listandthemeselection&tmpl=component&task=enableplugin"';
	$msg= '<p>Youtube Gallery Plugin is not enabled. <button class="btn btn-success button-save-selected" type="button" onclick=\''.$onClick.'\'>'.JText::_('Enable Plugin').'</button></p>';
	JFactory::getApplication()->enqueueMessage(JText::_($msg), 'warning');
}
else
{
	
	$videolist=$input->getInt('videolist');
	if($input->getCmd('task')=='preview')
		include('_preview.php');
	else
		include('_selection.php');
	
	
	//include('_selection.php');
	//include('_preview.php');
}
