<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

$allowsef = YouTubeGalleryMisc::getSettingValue('allowsef');
if ($allowsef != 1)
	$allowsef = 0;
    
    
    
?>
<div class="span10 form-horizontal" style="width:100%;">
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