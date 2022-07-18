<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');

//JHtml::_('behavior.tooltip');

$document = Factory::getDocument();
$document->addCustomTag('<link rel="stylesheet" href="' . JURI::root() . 'components/com_youtubegallery/css/wizard.css" type="text/css" />');
$document->addCustomTag('<script src="' . JURI::root() . 'components/com_youtubegallery/js/wizard.js"></script>');

$input = Factory::getApplication()->input;

$link = JRoute::_('index.php?option=com_youtubegallery');
$simple_mode = $input->getCmd('tmpl') == 'component';

if ($simple_mode)
    $link .= (strpos($link, '?') === false ? '?' : '&') . 'tmpl=component&ygrefreshparent=1';//this is for modal form - edit article youtube gallery button

$id = (int)$input->getInt('id');

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
                        <ul class="nav nav-tabs">
                            <li class="active"><a href="#videolinks"
                                                  data-toggle="tab"><?php echo JText::_('COM_YOUTUBEGALLERY_VIDEO_LINKS'); ?></a>
                            </li>
                            <li><a href="#source"
                                   data-toggle="tab"><?php echo JText::_('COM_YOUTUBEGALLERY_SOURCE'); ?></a></li>
                            <li><a href="#settings"
                                   data-toggle="tab"><?php echo JText::_('COM_YOUTUBEGALLERY_SETTINGS'); ?></a></li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane active" id="videolinks">
                                <?php include('links.php'); ?>

                            </div>

                            <div class="tab-pane" id="source">
                                <?php


                                require_once('doc.php');
                                ?>
                            </div>

                            <div class="tab-pane" id="settings">
                                <?php include('settings.php'); ?>
                            </div>
                        </div>
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
    <?php echo JHtml::_('form.token'); ?>

    <input type="hidden" name="jform[id]" value="<?php echo (int)$this->item->id; ?>"/>
</form>
