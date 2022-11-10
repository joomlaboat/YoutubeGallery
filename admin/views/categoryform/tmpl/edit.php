<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

if ($this->version < 4) {
    JHtml::_('behavior.formvalidation');
    JHtml::_('behavior.tooltip');
} else {
    $wa = $this->document->getWebAssetManager();
    $wa->useScript('keepalive')
        ->useScript('form.validate');
}

$link = 'index.php?option=com_youtubegallery';

$input = Factory::getApplication()->input;
$id = (int)$input->getInt('id');
$link .= '&id=' . $id;

?>

<form id="adminForm" action="<?php echo JRoute::_($link); ?>" method="post" class="form-validate">

    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_YOUTUBEGALLERY_CATEGORY_FORM_DETAILS'); ?></legend>

        <div class="form-horizontal">
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('es_categoryname'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('es_categoryname'); ?></div>
            </div>
        </div>

        <div class="form-horizontal">
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('es_parentid'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('es_parentid'); ?></div>
            </div>
        </div>

        <div class="form-horizontal">
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('es_description'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('es_description'); ?></div>
            </div>
        </div>

        <div class="form-horizontal">
            <div class="control-group">
                <div class="control-label"><?php echo $this->form->getLabel('es_image'); ?></div>
                <div class="controls"><?php echo $this->form->getInput('es_image'); ?></div>
            </div>
        </div>

    </fieldset>

    <input type="hidden" name="jform[id]" value="<?php echo (int)$this->item->id; ?>"/>
    <input type="hidden" name="task" value="categoryform.edit"/>
    <?php echo JHtml::_('form.token'); ?>

</form>
