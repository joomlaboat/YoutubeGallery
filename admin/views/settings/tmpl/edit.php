<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');

$document = Factory::getDocument();
$document->addCustomTag('<link rel="stylesheet" href="components/com_youtubegallery/css/specialbutton.css" type="text/css" />');

?>

<form id="adminForm" action="<?php echo JRoute::_('index.php?option=com_youtubegallery'); ?>" method="post"
      class="form-inline">
    <div class="row-fluid" style="width:100%;">

        <!-- Begin Content -->
        <div class="span10 form-horizontal" style="width:100%;">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#api" data-toggle="tab">Activation</a></li>
                <li><a href="#sef" data-toggle="tab">SEF</a></li>
            </ul>

            <div class="tab-content">

                <!-- Begin Tabs -->

                <div class="tab-pane active" id="api">
                    <?php include('api.php'); ?>
                </div>

                <div class="tab-pane" id="sef">
                    <?php include('sef.php'); ?>
                </div>
            </div>


            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </div>
</form>
