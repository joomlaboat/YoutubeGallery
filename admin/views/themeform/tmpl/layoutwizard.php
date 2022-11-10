<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<h4>General Theme Settings</h4>

<div class="form-horizontal">
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_themename'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_themename'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_width'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_width'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_height'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_height'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_playvideo'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_playvideo'); ?></div>
    </div>
</div>


<h4>Navigation Bar</h4>

<div class="form-horizontal">
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_orderby'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_orderby'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_customlimit'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_customlimit'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_navbarstyle'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_navbarstyle'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_bgcolor'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_bgcolor'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_thumbnailstyle'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_thumbnailstyle'); ?></div>
    </div>
</div>


<h4>Layout Settings</h4>

<div class="form-horizontal">
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_listnamestyle'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_listnamestyle'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_activevideotitlestyle'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_activevideotitlestyle'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_descr_style'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_descr_style'); ?></div>
    </div>
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_cssstyle'); ?></div>
        <div class="controls"><?php echo $this->form->getInput('es_cssstyle'); ?></div>
    </div>
</div>
