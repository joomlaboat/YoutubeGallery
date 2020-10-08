<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<h4>General Theme Settings</h4>

<div class="form-horizontal">

	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('themename'); ?></div><div class="controls"><?php echo $this->form->getInput('themename'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('width'); ?></div><div class="controls"><?php echo $this->form->getInput('width'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('height'); ?></div><div class="controls"><?php echo $this->form->getInput('height'); ?></div></div>
	<?php /* <div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('pagination'); ?></div><div class="controls"><?php echo $this->form->getInput('pagination'); ?></div></div> */ ?>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('playvideo'); ?></div><div class="controls radio btn-group"><?php echo $this->form->getInput('playvideo'); ?></div></div>
</div>


<h4>Navigation Bar</h4>

<div class="form-horizontal">
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('orderby'); ?></div><div class="controls"><?php echo $this->form->getInput('orderby'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('customlimit'); ?></div><div class="controls"><?php echo $this->form->getInput('customlimit'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('navbarstyle'); ?></div><div class="controls"><?php echo $this->form->getInput('navbarstyle'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('bgcolor'); ?></div><div class="controls"><?php echo $this->form->getInput('bgcolor'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('thumbnailstyle'); ?></div><div class="controls"><?php echo $this->form->getInput('thumbnailstyle'); ?></div></div>

	<?php /* <div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('cols'); ?></div><div class="controls"><?php echo $this->form->getInput('cols'); ?> Doesn't make sence in responsive mode</div></div> */ ?>
</div>


<h4>Layout Settings</h4>

<div class="form-horizontal">
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('listnamestyle'); ?></div><div class="controls"><?php echo $this->form->getInput('listnamestyle'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('activevideotitlestyle'); ?></div><div class="controls"><?php echo $this->form->getInput('activevideotitlestyle'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('descr_style'); ?></div><div class="controls"><?php echo $this->form->getInput('descr_style'); ?></div></div>
	<div class="control-group"><div class="control-label"><?php echo $this->form->getLabel('cssstyle'); ?></div><div class="controls"><?php echo $this->form->getInput('cssstyle'); ?></div></div>
</div>
