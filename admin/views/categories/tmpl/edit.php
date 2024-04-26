<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die('Restricted access');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
?>
<p style="text-align:left;">Upgrade to <a href="https://joomlaboat.com/youtube-gallery#pro-version" target="_blank">PRO
        version</a> to get more features</p>
<form action="<?php echo Route::_('index.php?option=com_youtubegallery&layout=edit&id=' . (int)$this->item->id); ?>"
      method="post" name="adminForm" id="youtubegallery-form" class="form-validate">
    <fieldset class="adminform">
        <legend><?php echo Text::_('COM_YOUTUBEGALLERY_CATEGORY_FORM_DETAILS'); ?></legend>

        <ul class="adminformlist">
            <?php foreach ($this->form->getFieldset() as $field): ?>
                <li><?php echo $field->label;
                    echo $field->input; ?></li>
            <?php endforeach; ?>
        </ul>
    </fieldset>
    <div>
        <input type="hidden" name="task" value="categoryform.edit"/>
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
