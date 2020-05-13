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


?>
<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_youtubegallery'); ?>" method="post" class="form-inline" enctype="multipart/form-data" style="text-align: center;">

	<input type="hidden" name="MAX_FILE_SIZE" value="1000000" />

	<div style="width:600px;margin:0 auto;">

		<div style="width: 290px;margin:50px auto;font-size:18px;position: relative;">
			<?php echo JText::_('COM_YOUTUBEGALLERY_THEME_UPLOADFILE'); ?>: <input name="themefile" id="themefile" type="file" style="font-size:18px;" />
		</div>
	</div>


                <input type="hidden" name="task" value="themeimport.upload" />
				<input type="submit" class="btn btn-success" value="Upload" style="text-align: center;" />


                <?php echo JHtml::_('form.token'); ?>

</form>
