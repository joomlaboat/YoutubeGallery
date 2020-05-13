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
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');

?>

<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_youtubegallery'); ?>" method="post" class="form-inline">

<div class="row-fluid" style="width:100%;">
		<!-- Begin Content -->
		<div class="span10 form-horizontal" style="width:100%;">
				<ul class="nav nav-tabs">
						
						<li class="active"><a href="#youtube" data-toggle="tab">Youtube</a></li>
						<li><a href="#vimeo" data-toggle="tab">Vimeo</a></li>
						<li><a href="#soundcloud" data-toggle="tab">SoundCloud</a></li>
						<li><a href="#general" data-toggle="tab"><?php echo JText::_( 'COM_YOUTUBEGALLERY_SETTINGS_SEF' ); ?></a></li>
				</ul>

				<div class="tab-content">

						<!-- Begin Tabs -->
						

						<div class="tab-pane active" id="youtube">
																<?php echo JText::_('COM_YOUTUBEGALLERY_YOUTUBE_DESC'); ?>
																<!--<hr/>
																<p>
								<?php echo JText::_('COM_YOUTUBEGALLERY_YOUTUBE_APIKEY'); ?>:<br/>
								<input name="youtube_api_key" style="width:400px;" value="<?php /*
								$key=YouTubeGalleryMisc::getSettingValue('youtube_api_key');
								echo $key;
								*/ ?>" />
																</p>-->
						</div>

						<div class="tab-pane" id="vimeo">
																<?php echo JText::_('COM_YOUTUBEGALLERY_VIMEO_DESC'); ?>

																<p><?php echo JText::_('COM_YOUTUBEGALLERY_VIMEO_CLIENT_ID'); ?><br/>
																<input name="vimeo_api_client_id" style="width:400px;" value="<?php echo YouTubeGalleryMisc::getSettingValue('vimeo_api_client_id'); ?>" />
																</p>

																<p><?php echo JText::_('COM_YOUTUBEGALLERY_VIMEO_CLIENT_SECRET'); ?><br/>
																																<input name="vimeo_api_client_secret" style="width:400px;" value="<?php echo YouTubeGalleryMisc::getSettingValue('vimeo_api_client_secret'); ?>" />
																</p>
																<p><?php echo JText::_('COM_YOUTUBEGALLERY_VIMEO_CLIENT_TOKEN'); ?><br/>
																																<input name="vimeo_api_access_token" style="width:400px;" value="<?php echo YouTubeGalleryMisc::getSettingValue('vimeo_api_access_token'); ?>" />
																</p>
						</div>

						<div class="tab-pane" id="soundcloud">

																<?php echo JText::_('COM_YOUTUBEGALLERY_SOUNDCLOUD_DESC'); ?>

																<p>
																<?php echo JText::_('COM_YOUTUBEGALLERY_CLIENT_ID'); ?>:<br/>
																																<input name="soundcloud_api_client_id" style="width:400px;" value="<?php echo YouTubeGalleryMisc::getSettingValue('soundcloud_api_client_id'); ?>" />
																</p>

																<p>
																<?php echo JText::_('COM_YOUTUBEGALLERY_CLIENT_SECRET'); ?>:<br/>
																																<input name="soundcloud_api_client_secret" style="width:400px;" value="<?php echo YouTubeGalleryMisc::getSettingValue('soundcloud_api_client_secret'); ?>" />
																</p>

						</div>
						
						
						<div class="tab-pane" id="general">

																<?php

																$allowsef=YouTubeGalleryMisc::getSettingValue('allowsef');
																if($allowsef!=1)
																																$allowsef=0;

																?>


																<div class="control-group">
																																<div class="control-label"><?php echo JText::_( 'COM_YOUTUBEGALLERY_ALLOW_SEF' ); ?></div>
																																<div class="controls">
																																																<fieldset id="jform_attribs_link_titles" class="radio btn-group">
																																																<fieldset id="jform_allowsef" class="radio inputbox">
																																																																<input type="radio" id="jform_allowsef1" name="jform[allowsef]" value="1"<?php echo ($allowsef=='1' ? 'checked="checked"' : ''); ?> />
																																																																<label for="jform_allowsef1">Yes</label>
																																																																<input type="radio" id="jform_allowsef0" name="jform[allowsef]" value="0"<?php echo ($allowsef=='0' ? 'checked="checked"' : ''); ?> />
																																																																<label for="jform_allowsef0">No</label>
																																																</fieldset>
																																																</fieldset>
																																</div>
																</div>


						</div>


				</div>
				<input type="hidden" name="task" value="" />
				<?php echo JHtml::_('form.token'); ?>
</form>
