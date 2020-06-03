<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

?>

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