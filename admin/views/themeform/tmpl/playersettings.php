<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


?>
<!--<div class="form-horizontal">-->
<div class="row-fluid form-horizontal-desktop">
    <div class="span12">

        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_autoplay'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_autoplay'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_repeat'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_repeat'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_allowplaylist'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_allowplaylist'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_fullscreen'); ?></div>
            <div>
                <div class="controls"><?php echo $this->form->getInput('es_fullscreen'); ?></div>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_related'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_related'); ?></div>
        </div>

        <?php /*
	<div class="control-group">
		<div class="control-label"><?php //echo $this->form->getLabel('es_showinfo'); ?></div><div class="controls radio btn-group">
		<?php //echo $this->form->getInput('es_showinfo'); ?></div>
	</div>
	*/ ?>

        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_controls'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_controls'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_border'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_border'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_color1'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_color1'); ?></div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_color2'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_color2'); ?></div>
        </div>

        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_muteonplay'); ?></div>
            <div>
                <div class="controls"><?php echo $this->form->getInput('es_muteonplay'); ?></div>
            </div>
        </div>
        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_volume'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_volume'); ?></div>
        </div>

        <div class="control-group">
            <div class="control-label"><?php echo $this->form->getLabel('es_youtubeparams'); ?></div>
            <div class="controls"><?php echo $this->form->getInput('es_youtubeparams'); ?></div>
        </div>

    </div>
</div>
