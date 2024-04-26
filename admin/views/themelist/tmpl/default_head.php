<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted Access');

?>
<tr>
    <th width="5">
        <?php echo Text::_('COM_YOUTUBEGALLERY_ID'); ?>
    </th>
    <th width="20">
        <input type="checkbox" name="checkall-toggle" value="" title="Check All" onclick="Joomla.checkAll(this)"/>
    </th>
    <th align="left" style="text-align:left;">
        <?php echo Text::_('COM_YOUTUBEGALLERY_THEMENAME'); ?>
    </th>

    <th style="text-align:left;">
        Media Folder
    </th>

    <th style="text-align:left;">
        Export
    </th>
</tr>
