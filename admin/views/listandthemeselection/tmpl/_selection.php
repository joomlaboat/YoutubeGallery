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

//$doc = Factory::getDocument();
//$doc->addScript(Uri::root(true) . '/media/vendor/jquery/jquery.min.js');
//\Joomla\CMS\HTML\HTMLHelper::_('script', Uri::root(true) . '/media/vendor/jquery/jquery.min.js', array('version' => 'auto', 'relative' => true));

// Load chosen for select elements
//\Joomla\CMS\HTML\HTMLHelper::_('script', 'system/fields/fieldsrenderer.js', array('version' => 'auto', 'relative' => true));
//\Joomla\CMS\HTML\HTMLHelper::_('script', Uri::root() . 'vendor/chosen/chosen.jquery.min.js', array('version' => 'auto', 'relative' => true));
//\Joomla\CMS\HTML\HTMLHelper::_('stylesheet', 'vendor/chosen/chosen.css', array('version' => 'auto', 'relative' => true));

// Load tooltip instance without HTML support
//\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.renderTooltip', '.noHtmlTip', array('html' => false));

// Include jQuery
//\Joomla\CMS\HTML\HTMLHelper::_('behavior.core');
//\Joomla\CMS\HTML\HTMLHelper::_('bootstrap.framework');

$document = Factory::getDocument();

if (!empty($fieldInput)) // Media Form Field
{
    $onClick = "window.parent.jInsertFieldValue(document.getElementById('f_url').value, '" . $fieldInput . "');window.parent.jModalClose();window.parent.jQuery('.modal.in').modal('hide');";
} else {
    $onClick = "YoutubeGalleryButtonInsert()";
}
$input = Factory::getApplication()->input;
$videoListId = $input->getInt('videolistid');
$themeId = $input->getInt('themeid');

HTMLHelper::addIncludePath(JPATH_SITE . '/administrator/components/com_youtubegallery/helpers');
?>


<script>
    ygSiteBase = '<?php echo Uri::root(); ?>';

    function YoutubeGalleryButtonInsert() {
        const videoList = document.getElementById("videolistselector").value;
        if (videoList === "") {
            alert("Video List not selected");
            return false;
        }
        const theme = document.getElementById("themeselector").value;
        if (theme === "") {
            alert("Theme not selected");
            return false;
        }
        window.parent.YG.insert(videoList, theme);
    }

    function YoutubeGalleryUpdatePreview() {
        const videoList = document.getElementById("videolistselector").value;
        const theme = document.getElementById("themeselector").value;
        const html_string = '<span style="color:#aaaaaa;">Loading...</span>';
        const videoList_url = ygSiteBase + 'administrator/index.php?option=com_youtubegallery&view=linksform&layout=edit&tmpl=component&id=' + videoList;

        document.getElementById("YGVideoLinks").src = "data:text/html;charset=utf-8," + escape(html_string);
        document.getElementById("YGPreview").src = "data:text/html;charset=utf-8," + escape(html_string);
        document.getElementById('yginsertbutton').disabled = videoList === 0;

        setTimeout(function () {
            document.getElementById("YGVideoLinks").src = videoList_url;
        }, 200);

        const preview_url = ygSiteBase + 'administrator/index.php?option=com_youtubegallery&view=listandthemeselection&tmpl=component&task=preview&videolist=' + videoList + '&theme=' + theme;

        setTimeout(function () {
            document.getElementById("YGPreview").src = preview_url;
        }, 200);
    }

    function YoutubeGalleryRefreshFrame() {
        const videoList = document.getElementById("videolistselector").value;
        const themeId = document.getElementById("themeselector").value;

        document.getElementById('YGPreviewMessageBox').innerHTML = "Loading videos...";
        document.getElementById('YGVideoLinksDiv').style.display = "none";
        document.getElementById('YGPreviewDiv').style.display = "none";

        let url = location.href;

        if (url.indexOf("?") === -1)
            url += '?';
        else
            url += '&';

        if (videoList === "" && url.indexOf('showlatestvideolist') === -1)
            url += 'showlatestvideolist=1';
        else if (url.indexOf('videolistid') === -1)
            url += 'videolistid=' + videoList;

        url += '&themeid=' + themeId;

        location.href = url;
    }

</script>
<div class="container-popup container">
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
                            <?php

                            echo HTMLHelper::_('videolist.render',
                                'videolistselector', $videoListId, 'onChange="YoutubeGalleryUpdatePreview();"'); ?>
                        </div>
                    </div>
                    <div class="control-group">

                        <div class="control-label">
                            <label for="folder"><?php echo Text::_('COM_YOUTUBEGALLERY_THEME'); ?></label>
                        </div>
                        <div class="controls">
                            <?php echo HTMLHelper::_('theme.render', 'themeselector', $themeId, ' onChange="YoutubeGalleryUpdatePreview();"'); ?>
                        </div>
                    </div>
                </div>

                <div class="span4 control-group">
                    <div class="pull-right">
                        <button id="yginsertbutton" class="btn btn-success button-save-selected" type="button"
                                <?php if (!empty($onClick)) :
                                // This is for Mootools compatibility                                                                                                  ?>onclick="<?php echo $onClick; ?>"<?php endif; ?>
                                data-dismiss="modal"><?php echo Text::_('COM_YOUTUBEGALLERY_INSERT'); ?></button>

                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="span10 form-horizontal container border light-gray-border"><!-- style="width:calc(100% - 30px);" -->

    <?php echo HTMLHelper::_('uitab.startTabSet', 'YGTabs',
        ['active' => 'videolinks', 'recall' => true, 'breakpoint' => 768]); ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'YGTabs', 'videolinks', Text::_('COM_YOUTUBEGALLERY_VIDEO_LINKS')); ?>
    <div id="YGPreviewMessageBox" style="color:#aaaaaa;"></div>
    <div id="YGVideoLinksDiv">
        <iframe id="YGVideoLinks" src="" width="100%"
                style="width:100%;height:250px;"></iframe>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php echo HTMLHelper::_('uitab.addTab', 'YGTabs', 'preview', Text::_('COM_YOUTUBEGALLERY_PREVIEW')); ?>
    <div id="YGPreviewDiv">
        <iframe id="YGPreview" src="" width="100%"
                style="width:100%;height:250px;"></iframe>
    </div>
    <?php echo HTMLHelper::_('uitab.endTab'); ?>
    <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
</div>

<script>
    window.addEventListener("message", function (event) {
        if (event.data == 'YGRefreshMainFrame') {
            YoutubeGalleryRefreshFrame();
        }
    });

    YoutubeGalleryUpdatePreview();
</script>
