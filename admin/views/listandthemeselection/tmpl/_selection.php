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


JHtml::_('formbehavior.chosen', 'select');

// Load tooltip instance without HTML support because we have a HTML tag in the tip
JHtml::_('bootstrap.tooltip', '.noHtmlTip', array('html' => false));

// Include jQuery
JHtml::_('behavior.core');
JHtml::_('jquery.framework');

$document = JFactory::getDocument();
$document->addScript(JURI::root() . "/administrator/components/com_youtubegallery/js/modal.js");

if (!empty($fieldInput)) // Media Form Field
{
	if ($isMoo)
	{
		$onClick = "window.parent.jInsertFieldValue(document.getElementById('f_url').value, '" . $fieldInput . "');window.parent.jModalClose();window.parent.jQuery('.modal.in').modal('hide');";
	}
}
else // XTD Image plugin
{
	$onClick = 'YG.onok();window.parent.jModalClose();';
}

JHTML::addIncludePath(JPATH_SITE.DIRECTORY_SEPARATOR.'administrator'.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'helpers');
//index.php?option=com_media&amp;asset=<?php echo $asset; &amp;author=<?php echo $author; 
?>

<div class="container-popup">

	<form action="" class="form-horizontal" id="imageForm" method="post" enctype="multipart/form-data">

		<div id="messages" style="display: none;">
			<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true); ?>
		</div>

		<div class="well">
			<div class="row-fluid">
				<div class="span8 control-group">
					<div class="control-label">
						<label for="folder"><?php echo JText::_('COM_YOUTUBEGALLERY_FIELD_VIDEOLIST_LABEL'); ?></label>
					</div>
					<div class="controls">
						<?php echo JHTML::_('videolist.render','vidoelistselctor',0,' onChange="YG.updatePreview();"');?>
					</div>
					
					<div class="control-label">
						<label for="folder"><?php echo JText::_('COM_YOUTUBEGALLERY_THEME'); ?></label>
					</div>
					<div class="controls">
						<?php echo JHTML::_('theme.render','themeselctor',0,' onChange="YG.updatePreview();"');?>
					</div>
					
				</div>
				
				<div class="span4 control-group">
					<div class="pull-right">
						<button id="yginsertbutton" class="btn btn-success button-save-selected" type="button" <?php if (!empty($onClick)) :
							// This is for Mootools compatibility ?>onclick="<?php echo $onClick; ?>"<?php endif; ?> data-dismiss="modal"><?php echo JText::_('COM_YOUTUBEGALLERY_INSERT'); ?></button>
						
					</div>
				</div>
			</div>
		</div>


		
		
	</form>

	
</div>

<?php if($plugin_enabled): ?>
<div id="YGPreviewMessageBox"></div>
<div id="YGPreviewDiv"><iframe id="YGPreview" src="" width="100%" style="width:100%;height:300px;border:1px solid lightgrey;">Preview...</iframe></div>

<script>
	
	window.addEventListener("message", function(event)
	{
		if(event.data=='YGRefreshMainFrame')
		{
			YG.refreshFrame();
		}
	});
	
	var vl_obj=document.getElementById('vidoelistselctor');
	if(vl_obj.options.length==0 || vl_obj.value==0)
	{
		document.getElementById('yginsertbutton').disabled=true;
		
		var url='/administrator/index.php?option=com_youtubegallery&view=linksform&layout=edit&tmpl=component';
		document.getElementById("YGPreview").src=url;
	}
	else
		YG.updatePreview();
		
</script>


<?php endif; ?>
