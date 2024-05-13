<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

$options = [];
$options[] = ["0.041", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_0041')];
$options[] = ["0.125", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_0125')];
$options[] = ["0.25", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_025')];
$options[] = ["0.33", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_033')];
$options[] = ["0.5", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_05')];
$options[] = ["1", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_1')];
$options[] = ["3", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_3')];
$options[] = ["7", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_7')];
$options[] = ["10", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_10')];
$options[] = ["30", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_30')];
$options[] = ["365", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_CHECK_365')];
$options[] = ["-0.041", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_0041')];
$options[] = ["-0.125", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_0125')];
$options[] = ["-0.33", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_033')];
$options[] = ["-0.5", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_05')];
$options[] = ["-1", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_1')];
$options[] = ["-3", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_3')];
$options[] = ["-7", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_7')];
$options[] = ["-10", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_10')];
$options[] = ["-30", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_30')];
$options[] = ["-365", Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_REFRESH_365')];
?>
<div class="form-horizontal">
    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_catid'); ?></div>
        <div class="controls radio btn-group"><?php echo $this->form->getInput('es_catid'); ?></div>
    </div>

    <div class="control-group">
        <div class="control-label"><?php echo $this->form->getLabel('es_updateperiod'); ?></div>
        <div class="controls radio btn-group"><select id="jform_es_updateperiod" name="jform[es_updateperiod]"
                                                      class="inputbox" aria-invalid="false">
                <?php
                foreach ($options as $option) {
                    echo '<option value="' . $option[0] . '"' . ((float)$option[0] == (float)$this->item->es_updateperiod ? 'selected="selecetd"' : '') . '>' . $option[1] . '</option>';
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