<?php
/**
 * YoutubeGallery Joomla! Native Component
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
<form id="adminForm" action="<?php echo Route::_('index.php?option=com_youtubegallery'); ?>" method="post"
      class="form-inline" enctype="multipart/form-data" style="text-align: center;">

    <input type="hidden" name="MAX_FILE_SIZE" value="1000000"/>

    <div style="width:600px;margin:0 auto;">

        <div style="width: 290px;margin:50px auto;font-size:18px;position: relative;">
            <?php echo Text::_('COM_YOUTUBEGALLERY_THEME_UPLOADFILE'); ?>: <input name="themefile" id="themefile"
                                                                                  type="file" style="font-size:18px;"/>
        </div>
    </div>


    <input type="hidden" name="task" value="themeimport.upload"/>
    <input type="submit" class="btn btn-success" value="Upload" style="text-align: center;"/>


    <?php echo HTMLHelper::_('form.token'); ?>

</form>
