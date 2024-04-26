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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();

/** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');


$document->addCustomTag('<link rel="stylesheet" href="' . Uri::root() . 'components/com_youtubegallery/css/wizard.css" type="text/css" />');
$document->addCustomTag('<script src="' . Uri::root() . 'components/com_youtubegallery/js/wizard.js"></script>');

$input = Factory::getApplication()->input;

$link = Route::_('index.php?option=com_youtubegallery');
$simple_mode = $input->getCmd('tmpl') == 'component';

if ($simple_mode)
    $link .= (!str_contains($link, '?') ? '?' : '&') . 'tmpl=component&ygrefreshparent=1';//this is for modal form - edit article youtube gallery button

$id = (int)$input->getInt('id');
$link .= '&id=' . $id;

echo '
	<script>
			' . ($simple_mode ? 'simple_mode=true;' : '') . '
			' . ($input->getInt('ygrefreshparent') == 1 ? 'parent.postMessage("YGRefreshMainFrame", "*");' : '') . '
	</script>
';

$textarea_box = $this->form->getInput('es_videolist');
?>

<form id="adminForm" name="adminForm" action="<?php echo $link; ?>" method="post" class="form-validate">
    <div id="hideModalAddVideoFormMessage" style="display:none;"></div>
    <div id="hideModalAddVideoForm" style="display:block;">
        <fieldset class="adminform">
            <?php echo $this->form->getInput('id'); ?>

            <?php if ($simple_mode): ?>

                <?php if ($input->getInt('ygrefreshparent') == 1): ?>
                <script>
                    parent.postMessage('YGRefreshMainFrame', '*');
                </script>
            <?php endif; ?>

            <?php if ($id == 0): ?>
                <div style="text-align:center;">
                    <?php echo $this->form->getLabel('es_listname'); ?><br/>
                    <?php echo $this->form->getInput('es_listname'); ?>
                    <br/>
                    <button onclick="submitSimpleForm(false);" class="btn btn-small button-save">
                        <span class="icon-save" aria-hidden="true"></span>Create Video List
                    </button>
                </div>
            <?php endif; ?>

            <?php else: ?>


                <div class="form-horizontal">
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('es_listname'); ?></div>
                        <div class="controls radio btn-group"><?php echo $this->form->getInput('es_listname'); ?></div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Modal Form -->
            <div id="layouteditor_Modal" class="layouteditor_modal">

                <!-- Modal content -->
                <div class="layouteditor_modal-content" id="layouteditor_modalbox">
                    <span class="layouteditor_close">&times;</span>
                    <div id="layouteditor_modal_content_box">
                        <p>Some text in the Modal..</p>
                    </div>
                </div>

            </div>
            <!-- end of the modal form -->

            <div class="row-fluid" style="width:100%;">
                <!-- Begin Content -->
                <div class="span10 form-horizontal" style="width:100%;">
                    <?php if ($simple_mode): ?>
                        <?php
                        if ($id != 0)
                            include('links.php');
                        ?>
                        <div style="display:none;"><?php echo $textarea_box; ?></div>
                    <?php else: ?>

                        <?php echo HTMLHelper::_('uitab.startTabSet', 'linksformTab', ['active' => 'videolinks', 'recall' => true, 'breakpoint' => 768]); ?>

                        <?php echo HTMLHelper::_('uitab.addTab', 'linksformTab', 'videolinks', Text::_('COM_YOUTUBEGALLERY_VIDEO_LINKS')); ?>
                        <?php include('links.php'); ?>
                        <?php echo HTMLHelper::_('uitab.endTab'); ?>

                        <?php echo HTMLHelper::_('uitab.addTab', 'linksformTab', 'source', Text::_('COM_YOUTUBEGALLERY_SOURCE')); ?>
                        <?php include('doc.php'); ?>
                        <?php echo HTMLHelper::_('uitab.endTab'); ?>

                        <?php echo HTMLHelper::_('uitab.addTab', 'linksformTab', 'settings', Text::_('COM_YOUTUBEGALLERY_SETTINGS')); ?>
                        <?php include('settings.php'); ?>
                        <?php echo HTMLHelper::_('uitab.endTab'); ?>

                        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

                    <?php endif; ?>

                </div>
            </div>

            <script>
                YGSetVLTA('jform_es_videolist');
                YGUpdatelinksTable();
            </script>

        </fieldset>
    </div>
    <input type="hidden" name="task" value="linksform.edit"/>
    <?php echo HTMLHelper::_('form.token'); ?>

    <input type="hidden" name="jform[id]" value="<?php echo (int)$this->item->id; ?>"/>
</form>
