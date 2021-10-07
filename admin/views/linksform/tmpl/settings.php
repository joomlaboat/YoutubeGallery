<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

$options=[];
$options[]=["0.00347","Check for New Videos Every 5 Minutes"];
$options[]=["0.01025","Check for New Videos Every 15 Minutes"];
$options[]=["0.0205","Check for New Videos Every 30 Minutes"];
$options[]=["0.041","Check for New Videos Every Hour"];
$options[]=["0.125","Check for New Videos Every 3 Hours"];
$options[]=["0.25","Check for New Videos Every 6 Hours"];
$options[]=["0.33","Check for New Videos Every 8 Hours"];
$options[]=["0.5","Check for New Videos Every 12 Hours"];
$options[]=["1","Check for New Videos Every Day"];
$options[]=["3","Check for New Videos Every 3 Days"];
$options[]=["7","Check for New Videos Every Week"];
$options[]=["10","Check for New Videos Every 10 Days"];
$options[]=["30","Check for New Videos Every Month"];
$options[]=["365","Check for New Videos Every Year"];
$options[]=["-0.041","Refresh Video List Every Hour"];
$options[]=["-0.125","Refresh Video List Every 3 Hours"];
$options[]=["-0.25","Refresh Video List Every 6 Hours"];
$options[]=["-0.33","Every 8 Hours"];
$options[]=["-0.5","Refresh Video List Every 12 Hours"];
$options[]=["-1","Refresh Video List Every Day"];
$options[]=["-3","Refresh Video List Every 3 Days"];
$options[]=["-7","Refresh Video List Every Week"];
$options[]=["-10","Every 10 Days"];
$options[]=["-30","Refresh Video List Every Month"];
$options[]=["-365","Refresh Video List Every Year"];
?>
<div class="form-horizontal">
								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_catid'); ?></div>
									<div class="controls radio btn-group"><?php echo $this->form->getInput('es_catid'); ?></div>
								</div>

								<div class="control-group">
									<div class="control-label"><?php echo $this->form->getLabel('es_updateperiod'); ?></div>
									<div class="controls radio btn-group"><select id="jform_es_updateperiod" name="jform[es_updateperiod]" class="inputbox" aria-invalid="false">
									<?php 
									foreach($options as $option)
									{
										echo '<option value="'.$option[0].'"'.((float)$option[0] == (float)$this->item->es_updateperiod ? 'selected="selecetd"' : '').'>'.$option[1].'</option>';
									}
									?>
</select></div>
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