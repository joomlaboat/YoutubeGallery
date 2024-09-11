<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die('Restricted access');

$WebsiteRoot = Uri::root();
if ($WebsiteRoot[strlen($WebsiteRoot) - 1] != '/') //Root must have slash / in the end
    $WebsiteRoot .= '/';

?>
<form id="adminForm" action="<?php echo Route::_('index.php?option=com_youtubegallery'); ?>" method="post"
      class="form-inline">
    <div style="position:absolute;top:0;left:0;width:100%;">
        <div style="position:relative;width:400px;border:2px solid #cccccc;background-color: #fefefe;margin:0 auto;text-align:center;padding:5px;">
            <p style="font-size:12px;font-weight:bold;">Download Theme File:</p>
            <p style="font-size:17px;font-weight:bold;"><a
                        href="<?php echo $WebsiteRoot . 'tmp/youtubegallery/' . $this->theme_zip_file . '">' . $this->theme_zip_file; ?></a></p>
		<p style=" color:grey;font-size:10px;">Clean "tmp" folder to delete file.</p>
        </div>

    </div>
    <input type="hidden" name="task" value="themeexport.edit"/>
    <?php echo HTMLHelper::_('form.token'); ?>
</form>
