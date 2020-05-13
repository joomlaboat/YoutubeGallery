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

$document = JFactory::getDocument();
$document->addCustomTag('<link rel="stylesheet" href="components/com_youtubegallery/css/wizard.css" type="text/css" />');
$document->addCustomTag('<script src="components/com_youtubegallery/js/wizard.js"></script>');

$link=JRoute::_('index.php?option=com_youtubegallery');

$input      = JFactory::getApplication()->input;
if($input->getCmd('tmpl')=='component')
	$link.=(strpos($link,'?')===false ? '?' : '&').'tmpl=component&ygrefreshparent=1';//this is for modal form - edit article youtube gallery button

?>

	<form id="adminForm" name="adminForm" action="<?php echo $link; ?>" method="post" class="form-validate">
	<div id="hideModalAddVideoFormMessage" style="display:none;"></div>
		<div id="hideModalAddVideoForm" style="display:block;">
        <fieldset class="adminform">
                <?php echo $this->form->getInput('id'); ?>

<?php

if($input->getCmd('tmpl')=='component'):

if($input->getInt('ygrefreshparent')==1):
?>
<script>
	parent.postMessage('YGRefreshMainFrame', '*');
</script>
	
<?php endif; ?>
<button onclick="hideModalAddVideoForm();Joomla.submitbutton('linksform.apply');" class="btn btn-small button-save">
	<span class="icon-save" aria-hidden="true"></span>
	Create Video List</button>
	<?php
endif;
?>
			<div class="form-horizontal">
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('listname'); ?></div>
					<div class="controls radio btn-group"><?php echo $this->form->getInput('listname'); ?></div>
				</div>
			</div>

<!-- Modal Form -->
		<div id="layouteditor_Modal" class="layouteditor_modal">

  <!-- Modal content -->
  <div class="layouteditor_modal-content" id="layouteditor_modalbox">
    <span class="layouteditor_close">&times;</span>
	<div id="layouteditor_modal_content_box">
    <p>Some text in the Modal..</p>
	</div>
  </div>

</div>
		<!-- end of the modal form -->

			<div class="row-fluid" style="width:100%;">
			<!-- Begin Content -->
				<div class="span10 form-horizontal" style="width:100%;">
					<ul class="nav nav-tabs">
						<li class="active"><a href="#videolinks" data-toggle="tab"><?php echo JText::_( 'COM_YOUTUBEGALLERY_VIDEO_LINKS' ); ?></a></li>
						<li><a href="#source" data-toggle="tab"><?php echo JText::_( 'COM_YOUTUBEGALLERY_SOURCE' ); ?></a></li>
						<li><a href="#settings" data-toggle="tab"><?php echo JText::_( 'COM_YOUTUBEGALLERY_SETTINGS' ); ?></a></li>

					</ul>

					<div class="tab-content">

						<!-- Begin Tabs -->
						<div class="tab-pane active" id="videolinks">

							<button onclick="YGAddLink();" class="btn btn-small btn-success" type="button" >
								<span class="icon-new icon-white"></span><span style="margin-left:10px;">Add Link</span>
							</button>



							<div id="ygvideolinkstable"></div>
							<div  id="ygvideolinkstablemessage" style="display:block;color:#008800"><i>Use "Order By" option in Theme settings to set the order, custom order is also available.</i></div>

						</div>
						<div class="tab-pane" id="source">

							<?php

						$textarea_box=$this->form->getInput('videolist');
						require_once('doc.php');
					?>
					<!-- ----------------------------- -->
						</div>

						<div class="tab-pane" id="settings">
							<div class="form-horizontal">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('catid'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('updateperiod'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('updateperiod'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('description'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('description'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('authorurl'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('authorurl'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('watchusergroup'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('watchusergroup'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('image'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('note'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('note'); ?></div>
								</div>
						</div>
						</div>
					</div>
				</div>
			</div>

			<script>
				YGSetVLTA('jform_videolist');
				YGUpdatelinksTable();
			</script>

        </fieldset>
				</div>
	    <input type="hidden" name="task" value="linksform.edit" />
	    <?php echo JHtml::_('form.token'); ?>

		<input type="hidden" name="jform[id]" value="<?php echo (int)$this->item->id; ?>" />
</form>
