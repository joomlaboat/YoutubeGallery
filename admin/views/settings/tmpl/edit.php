<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

$key=YouTubeGalleryMisc::getSettingValue('joomlaboat_api_key');

$allowsef = YouTubeGalleryMisc::getSettingValue('allowsef');
if ($allowsef != 1)
	$allowsef = 0;


?>

<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_youtubegallery'); ?>" method="post" class="form-inline">
	<div class="row-fluid" style="width:100%;">
		<div class="span10 form-horizontal" style="width:100%;">
		<p><?php echo JText::_('COM_YOUTUBEGALLERY_ACTVATION'); ?></p>
			
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_YOUTUBEGALLERY_SERVER_ADDRESS'); ?></div>
				<div class="controls"><input type="text" readonly value="<?php echo $_SERVER['SERVER_ADDR']; ?>" /></div>
			</div>
			
			<div class="control-group">
				<div class="control-label"><?php echo JText::_('COM_YOUTUBEGALLERY_JOOMLABOAT_YOUTUBE_KEY'); ?></div>
				<div class="controls"><input name="joomlaboat_api_key" style="width:300px;" value="<?php echo $key; ?>" /></div>
			</div>
			
			<div class="control-group">			
				<div class="control-label"><?php echo JText::_('COM_YOUTUBEGALLERY_ALLOW_SEF'); ?></div>
				<div class="controls radio btn-group">
					<fieldset id="jform_allowsef" class="radio inputbox">
						<input type="radio" id="jform_allowsef1" name="jform[allowsef]" value="1"<?php echo ($allowsef == '1' ? 'checked="checked"' : ''); ?> />
						<label for="jform_allowsef1">Yes</label>
						<input type="radio" id="jform_allowsef0" name="jform[allowsef]" value="0"<?php echo ($allowsef == '0' ? 'checked="checked"' : ''); ?> />
						<label for="jform_allowsef0">No</label>
					</fieldset>
				</div>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
