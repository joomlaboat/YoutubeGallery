<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

$input = Factory::getApplication()->input;

if ($input->getCmd('task') == 'enableplugin') {
    $this->EnablePlugin();
    $msg = '<p>Content - Youtube Gallery Plugin enabled.</p>';
    Factory::getApplication()->enqueueMessage(Text::_($msg), 'notice');
}

//Check Plugin
$plugin = $this->checkIfPluginIsEnabled();

if ($plugin == null) {
    $msg = '<p>Youtube Gallery Plugin is not installed.<br/>'
        . 'You can get it free here: <a href="https://github.com/joomlaboat/plg_youtubegallery" target="_blank">GitHub</a><br/>'
        . '<a href="https://github.com/joomlaboat/plg_youtubegallery/archive/master.zip">Download YoutubeGallery Plugin</a></p>';

    Factory::getApplication()->enqueueMessage(Text::_($msg), 'warning');
} elseif ($plugin['enabled'] == 0) {
    $onClick = 'location.href="/administrator/index.php?option=com_youtubegallery&view=listandthemeselection&tmpl=component&task=enableplugin"';
    $msg = '<p>Youtube Gallery Plugin is not enabled. <button class="btn btn-success button-save-selected" type="button" onclick=\'' . $onClick . '\'>' . Text::_('Enable Plugin') . '</button></p>';
    Factory::getApplication()->enqueueMessage(Text::_($msg), 'warning');
} else {

    $videolist = $input->getInt('videolist');
    if ($input->getCmd('task') == 'preview') {
        //include('_preview.php');
    } else {
        include('_selection.php');
    }
}
