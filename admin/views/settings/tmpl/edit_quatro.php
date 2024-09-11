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
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate');

$document = Factory::getDocument();
$document->addCustomTag('<link rel="stylesheet" href="components/com_youtubegallery/css/specialbutton.css" type="text/css" />');

?>

<form id="adminForm" action="<?php echo Route::_('index.php?option=com_youtubegallery'); ?>" method="post"
      class="form-inline">
    <div class="row-fluid" style="width:100%;">
        <div class="span10 form-horizontal" style="width:100%;">

            <?php echo HTMLHelper::_('uitab.startTabSet', 'settingsTab', ['active' => 'api', 'recall' => true, 'breakpoint' => 768]); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'settingsTab', 'api', Text::_('Activation')); ?>
            <?php include('api.php'); ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.addTab', 'settingsTab', 'sef', Text::_('SEF')); ?>
            <?php include('sef.php'); ?>
            <?php echo HTMLHelper::_('uitab.endTab'); ?>

            <?php echo HTMLHelper::_('uitab.endTabSet'); ?>
        </div>

        <input type="hidden" name="task" value=""/>
        <?php echo HTMLHelper::_('form.token'); ?>
    </div>
</form>
