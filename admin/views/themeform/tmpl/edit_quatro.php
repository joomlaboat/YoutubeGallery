<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$componentpath = JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR;
$htmlscriptpath = $componentpath . 'views' . DIRECTORY_SEPARATOR . 'themeform' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR;

require_once($componentpath . 'libraries' . DIRECTORY_SEPARATOR . 'layouteditor.php');
$onPageLoads = array();

?>

<script type="text/javascript">
    ExtensionName = "com_youtubegallery";
</script>
<style>
    #jform_headscript, #jform_themedescription {
        width: 420px;
    }
</style>

<form id="adminForm" name="adminForm" action="<?php echo Route::_('index.php?option=com_youtubegallery'); ?>"
      method="post" class="form-validate">
    <?php echo $this->form->getInput('id'); ?>

    <div class="row-fluid" style="width:100%;">
        <!-- Begin Content -->
        <div class="span10 form-horizontal" style="width:100%;">

            <?php echo HTMLHelper::_('uitab.startTabSet', 'themeformTab', ['active' => 'general', 'recall' => true, 'breakpoint' => 768]); ?>


            <?php echo HTMLHelper::_('uitab.addTab', 'themeformTab', 'general', Text::_('COM_YOUTUBEGALLERY_THEME_TAB_GENERAL')); ?>
            <fieldset class="adminform">
                <?php include('layoutwizard.php'); ?>
            </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'themeformTab', 'playersettings', Text::_('COM_YOUTUBEGALLERY_THEME_TAB_PLAYER')); ?>
            <fieldset class="adminform">
                <?php include('playersettings.php'); ?>
            </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php
            $element_name = 'customlayout';
            echo HTMLHelper::_('uitab.addTab', 'themeformTab', $element_name . '-tab', Text::_('COM_YOUTUBEGALLERY_THEME_TAB_CUSTOMLAYOUT')); ?>
            <div id="<?php echo $element_name; ?>">
                <?php
                if ($this->item->id == 0)
                    $this->item->es_customlayout = file_get_contents($htmlscriptpath . 'customlayout.html');

                $textareacode = '<textarea name="jform[es_' . $element_name . ']" id="jform_es_' . $element_name . '" filter="raw" style="width:100%" rows="30">' . $this->item->es_customlayout . '</textarea>';
                echo renderEditor($textareacode, $element_name, $onPageLoads);
                ?>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php
            $element_name = 'customnavlayout';
            echo HTMLHelper::_('uitab.addTab', 'themeformTab', $element_name . '-tab', Text::_('COM_YOUTUBEGALLERY_THEME_TAB_THUMBNAILLAYOUT')); ?>
            <div id="<?php echo $element_name; ?>">
                <?php
                if ($this->item->id == 0)
                    $this->item->es_customnavlayout = file_get_contents($htmlscriptpath . 'customnavlayout.html');

                $textareacode = '<textarea name="jform[es_' . $element_name . ']" id="jform_es_' . $element_name . '" filter="raw" style="width:100%" rows="30">' . $this->item->es_customnavlayout . '</textarea>';
                echo renderEditor($textareacode, $element_name, $onPageLoads);
                ?>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'themeformTab', 'misc', Text::_('COM_YOUTUBEGALLERY_THEME_TAB_MISC')); ?>
            <fieldset class="adminform">
                <?php include('misc.php'); ?>
            </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php
            $element_name = 'headscript';
            echo HTMLHelper::_('uitab.addTab', 'themeformTab', $element_name . '-tab', Text::_('COM_YOUTUBEGALLERY_THEME_TAB_HEADSCRIPT')); ?>
            <div id="<?php echo $element_name; ?>">
                <?php
                if ($this->item->id == 0)
                    $this->item->es_headscript = file_get_contents($htmlscriptpath . 'headscript.html');

                $textareacode = '<textarea name="jform[es_' . $element_name . ']" id="jform_es_' . $element_name . '" filter="raw" style="width:100%" rows="30">' . $this->item->es_headscript . '</textarea>';
                echo renderEditor($textareacode, $element_name, $onPageLoads);
                ?>
            </div>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'themeformTab', 'more', Text::_('COM_YOUTUBEGALLERY_THEME_TAB_MORETHEMES')); ?>
            <fieldset class="adminform">
                <a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-themes?view=catalog&layout=custom"
                   target="_blank" style="color:#51A351;">Get more Themes</a>
            </fieldset>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>


            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>
    </div>

    <div id="fieldWizardBox"></div>
    <div id="ct_processMessageBox"></div>

    <input type="hidden" name="task" value="themeform.edit"/>
    <?php
    echo HTMLHelper::_('form.token');
    echo render_onPageLoads($onPageLoads, 0);
    ?>

</form>
