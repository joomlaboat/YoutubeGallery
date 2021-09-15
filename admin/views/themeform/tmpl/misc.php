<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$pro='';

?>

<div class="form-horizontal">

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_openinnewwindow'); ?></div><div class="controls"><?php echo $this->form->getInput('es_openinnewwindow').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_rel'); ?></div><div class="controls"><?php echo $this->form->getInput('es_rel').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_hrefaddon'); ?></div><div class="controls"><?php echo $this->form->getInput('es_hrefaddon').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_useglass'); ?></div><div class="controls"><?php echo $this->form->getInput('es_useglass').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_logocover'); ?></div><div class="controls"><?php echo $this->form->getInput('es_logocover').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_prepareheadtags'); ?></div><div class="controls"><?php echo $this->form->getInput('es_prepareheadtags').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_changepagetitle'); ?></div><div class="controls"><?php echo $this->form->getInput('es_changepagetitle').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_responsive'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('es_responsive').$pro; ?></div>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_nocookie'); ?></div>
		<div class="controls"><?php echo $this->form->getInput('es_nocookie'); ?></div><?php echo $pro; ?>
	</div>

	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_mediafolder'); ?></div><div class="controls">images/<?php echo $this->form->getInput('es_mediafolder').$pro; ?></div>
	</div>
	<hr/>
	<div class="control-group">
		<div class="control-label"><?php echo $this->form->getLabel('es_themedescription'); ?></div><div class="controls"><?php echo $this->form->getInput('es_themedescription').$pro; ?></div>
	</div>

</div>
