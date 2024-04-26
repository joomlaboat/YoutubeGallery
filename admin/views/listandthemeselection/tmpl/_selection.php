<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

// Load chosen for select elements
\Joomla\CMS\HTML\HTMLHelper::_('script', 'system/fields/fieldsrenderer.js', array('version' => 'auto', 'relative' => true));
\Joomla\CMS\HTML\HTMLHelper::_('script', 'vendor/chosen/chosen.jquery.min.js', array('version' => 'auto', 'relative' => true));
\Joomla\CMS\HTML\HTMLHelper::_('stylesheet', 'vendor/chosen/chosen.css', array('version' => 'auto', 'relative' => true));

// Load tooltip instance without HTML support
//\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.renderTooltip', '.noHtmlTip', array('html' => false));

// Include jQuery
\Joomla\CMS\HTML\HTMLHelper::_('behavior.core');
\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.framework');

$document = Factory::getDocument();
$document->addScript(Uri::root() . "components/com_youtubegallery/js/modal.js");

if (!empty($fieldInput)) // Media Form Field
{
    $onClick = "window.parent.jInsertFieldValue(document.getElementById('f_url').value, '" . $fieldInput . "');window.parent.jModalClose();window.parent.jQuery('.modal.in').modal('hide');";
} else // XTD Image plugin
{
    $onClick = 'YG.onok();window.parent.jModalClose();';
}
$input = Factory::getApplication()->input;
$videolistid = (int)$input->getInt('videolistid');
$themeid = (int)$input->getInt('themeid');


//JHTML::addIncludePath(JPATH_SITE . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'helpers');
\Joomla\CMS\HTML\HTMLHelper::addIncludePath(JPATH_SITE . '/administrator/components/com_youtubegallery/helpers');

?>


<script>
    ygSiteBase = '<?php echo Uri::root(); ?>';
</script>
<div class="container-popup">

    <form action="" class="form-horizontal" id="imageForm" method="post" enctype="multipart/form-data">

        <div id="messages" style="display: none;">
            <span id="message"></span><?php echo HTMLHelper::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true); ?>
        </div>

        <div class="well">
            <div class="row-fluid">
                <div class="span8">
                    <div class="control-group">
                        <div class="control-label">
                            <label for="folder"><?php echo Text::_('COM_YOUTUBEGALLERY_FIELD_VIDEOLIST_LABEL'); ?></label>
                        </div>

                        <div class="controls">
                            <?php echo HTMLHelper::_('videolist.render', 'vidoelistselector', $videolistid, ' onChange="YG.updatePreview();"'); ?>
                        </div>
                    </div>
                    <div class="control-group">

                        <div class="control-label">
                            <label for="folder"><?php echo Text::_('COM_YOUTUBEGALLERY_THEME'); ?></label>
                        </div>
                        <div class="controls">
                            <?php echo HTMLHelper::_('theme.render', 'themeselector', $themeid, ' onChange="YG.updatePreview();"'); ?>
                        </div>
                    </div>

                </div>

                <div class="span4 control-group">
                    <div class="pull-right">
                        <button id="yginsertbutton" class="btn btn-success button-save-selected" type="button"
                                <?php if (!empty($onClick)) :
                                // This is for Mootools compatibility               ?>onclick="<?php echo $onClick; ?>"<?php endif; ?>
                                data-dismiss="modal"><?php echo Text::_('COM_YOUTUBEGALLERY_INSERT'); ?></button>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="span10 form-horizontal" style="width:calc(100% - 30px);">
    <ul class="nav nav-tabs">
        <li class="active"><a href="#videolinks"
                              data-toggle="tab"><?php echo Text::_('COM_YOUTUBEGALLERY_FIELD_VIDEOLIST_LABEL'); ?></a>
        </li>
        <li><a href="#preview" data-toggle="tab"><?php echo Text::_('COM_YOUTUBEGALLERY_PREVIEW'); ?></a></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane active" id="videolinks">
            <div id="YGPreviewMessageBox" style="color:#aaaaaa;"></div>
            <div id="YGVideoLinksDiv">
                <iframe id="YGVideoLinks" src="" width="100%"
                        style="width:100%;height:250px;border:1px solid lightgrey;"></iframe>
            </div>
        </div>

        <div class="tab-pane" id="preview">
            <div id="YGPreviewDiv">
                <iframe id="YGPreview" src="" width="100%"
                        style="width:100%;height:250px;border:1px solid lightgrey;"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener("message", function (event) {
        if (event.data == 'YGRefreshMainFrame') {
            YG.refreshFrame();
        }
    });

    YG.updatePreview();
</script>
