<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


?>
<div class="form-horizontal">


	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('autoplay'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('autoplay'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('repeat'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('repeat'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('allowplaylist'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('allowplaylist'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('fullscreen'); ?></div>
		<div>
			<div class="controls radio btn-group"><?php echo $this->form->getInput('fullscreen'); ?></div>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('related'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('related'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('showinfo'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('showinfo'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('controls'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('controls'); ?></div>
	</div>
		<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('border'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('border'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('color1'); ?></div><div class="controls"><?php echo $this->form->getInput('color1'); ?></div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('color2'); ?></div><div class="controls"><?php echo $this->form->getInput('color2'); ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('muteonplay'); ?></div>
		<div>
			<div class="controls radio btn-group"><?php echo $this->form->getInput('muteonplay'); ?></div>
		</div>
	</div>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('volume'); ?></div><div class="controls"><?php echo $this->form->getInput('volume'); ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('youtubeparams'); ?></div><div class="controls"><?php echo $this->form->getInput('youtubeparams'); ?></div>
	</div>
</div>
