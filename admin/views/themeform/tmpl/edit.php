<?php
/**
 * YoutubeGallery Joomla! Native Component
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

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

$componentpath = JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR;
$htmlscriptpath = $componentpath . 'views' . DIRECTORY_SEPARATOR . 'themeform' . DIRECTORY_SEPARATOR . 'tmpl' . DIRECTORY_SEPARATOR;

require_once($componentpath . 'libraries' . DIRECTORY_SEPARATOR . 'layouteditor.php');
$onPageLoads = array();

/*
?>

<script type="text/javascript">
	ExtensionName="com_youtubegallery";
	// waiting spinner
	var outerDiv = jQuery('body');
	jQuery('<div id="loading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('components/com_customtables/assets/images/import.gif') 50% 15% no-repeat")
		.css("top", outerDiv.position().top - jQuery(window).scrollTop())
		.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
		.css("width", outerDiv.width())
		.css("height", outerDiv.height())
		.css("position", "fixed")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.css("display", "none")
		.appendTo(outerDiv);
	jQuery('#loading').show();
	// when page is ready remove and show
	jQuery(window).load(function() {
		jQuery('#customtables_loader').fadeIn('fast');
		jQuery('#loading').hide();
	});
</script>
*/
?>
<style>
    #jform_headscript, #jform_themedescription {
        width: 420px;
    }
</style>
<!--<div id="customtables_loader" style="display: none;"></div>-->
<form id="adminForm" name="adminForm" action="<?php echo JRoute::_('index.php?option=com_youtubegallery'); ?>"
      method="post" class="form-validate">
    <?php echo $this->form->getInput('id'); ?>

    <div class="row-fluid" style="width:100%;">
        <!-- Begin Content -->
        <div class="span10 form-horizontal" style="width:100%;">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#general" data-toggle="tab">General</a></li>
                <li><a href="#playersettings" data-toggle="tab">Player Settings</a></li>
                <li><a href="#customlayout" data-toggle="tab">Custom Layout</a></li>
                <li><a href="#customnavlayout" data-toggle="tab">Thumbnail Layout</a></li>
                <li><a href="#misc" data-toggle="tab">Miscellaneous</a></li>
                <li><a href="#headscript" data-toggle="tab">Head Script</a></li>
                <li><a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-themes?view=catalog&layout=custom"
                       target="_blank" style="color:#51A351;">Get more Themes</a></li>

            </ul>

            <div class="tab-content">

                <!-- Begin Tabs -->
                <div class="tab-pane active" id="general">

                    <fieldset class="adminform">
                        <?php include('layoutwizard.php'); ?>
                    </fieldset>

                </div>


                <div class="tab-pane" id="customlayout">
                    <?php
                    if ($this->item->id == 0)
                        $this->item->es_customlayout = file_get_contents($htmlscriptpath . 'customlayout.html');


                    $textareacode = '<textarea name="jform[es_customlayout]" id="jform_es_customlayout" filter="raw" style="width:100%" rows="30">' . $this->item->es_customlayout . '</textarea>';
                    echo renderEditor($textareacode, 'customlayout', $onPageLoads);
                    ?>
                </div>

                <div class="tab-pane" id="customnavlayout">
                    <?php
                    if ($this->item->id == 0)
                        $this->item->es_customnavlayout = file_get_contents($htmlscriptpath . 'customnavlayout.html');


                    $textareacode = '<textarea name="jform[es_customnavlayout]" id="jform_es_customnavlayout" filter="raw" style="width:100%" rows="30">' . $this->item->es_customnavlayout . '</textarea>';
                    echo renderEditor($textareacode, 'customnavlayout', $onPageLoads);
                    ?>
                </div>


                <div class="tab-pane" id="playersettings">
                    <fieldset class="adminform">
                        <?php include('playersettings.php'); ?>
                    </fieldset>
                </div>

                <div class="tab-pane" id="headscript">
                    <?php
                    if ($this->item->id == 0)
                        $this->item->es_headscript = file_get_contents($htmlscriptpath . 'headscript.html');

                    $textareacode = '<textarea name="jform[es_headscript]" id="jform_es_headscript" filter="raw" style="width:100%" rows="30">' . $this->item->es_headscript . '</textarea>';
                    echo renderEditor($textareacode, 'headscript', $onPageLoads);
                    ?>
                </div>

                <div class="tab-pane" id="misc">
                    <fieldset class="adminform">
                        <?php include('misc.php'); ?>
                    </fieldset>
                </div>


            </div>
        </div>
    </div>

    <div id="fieldWizardBox"></div>
    <div id="ct_processMessageBox"></div>

    <input type="hidden" name="task" value="themeform.edit"/>
    <?php
    echo JHtml::_('form.token');
    echo render_onPageLoads($onPageLoads, 0);
    ?>

</form>
