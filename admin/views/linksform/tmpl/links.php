<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted access');

?>

    <button onclick="YGAddLink();" class="btn btn-small btn-success" type="button">
        <span class="icon-new icon-white"></span><span
                style="margin-left:10px;"><?php echo Text::_('COM_YOUTUBEGALLERY_VIDEOLIST_ADD_LINK'); ?></span>
    </button>

    <div id="ygvideolinkstable"></div>
    <div id="ygvideolinkstablemessage" style="display:block;color:#008800;"></div>

<?php //<i>Use "Order By" option in Theme settings to set the order, custom order is also available.</i> ?>