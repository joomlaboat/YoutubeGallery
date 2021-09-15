<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

?>

<div class="form-horizontal">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_catid'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_catid'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_updateperiod'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_updateperiod'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_description'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_description'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_authorurl'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_authorurl'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_watchusergroup'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_watchusergroup'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_image'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_image'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_note'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_note'); ?></div>
								</div>
						</div>