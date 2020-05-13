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

$pro='';

?>

<div class="form-horizontal">

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('openinnewwindow'); ?></div><div class="controls"><?php echo $this->form->getInput('openinnewwindow').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('rel'); ?></div><div class="controls"><?php echo $this->form->getInput('rel').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('hrefaddon'); ?></div><div class="controls"><?php echo $this->form->getInput('hrefaddon').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('useglass'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('useglass').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('logocover'); ?></div><div class="controls"><?php echo $this->form->getInput('logocover').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('prepareheadtags'); ?></div><div class="controls"><?php echo $this->form->getInput('prepareheadtags').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('changepagetitle'); ?></div><div class="controls"><?php echo $this->form->getInput('changepagetitle').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('responsive'); ?></div><div class="controls"><?php echo $this->form->getInput('responsive').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('nocookie'); ?></div>
		<div>
			<div class="controls radio btn-group"><?php echo $this->form->getInput('nocookie'); ?></div><?php echo $pro; ?>
		</div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('mediafolder'); ?></div><div class="controls">images/<?php echo $this->form->getInput('mediafolder').$pro; ?></div>
	</div>
	<hr/>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('themedescription'); ?></div><div class="controls"><?php echo $this->form->getInput('themedescription').$pro; ?></div>
	</div>

</div>
