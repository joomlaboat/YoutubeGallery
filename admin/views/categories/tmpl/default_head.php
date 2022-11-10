<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<tr>
    <th width="5">
        <?php echo JText::_('COM_YOUTUBEGALLERY_CATEGORY_ID'); ?>
    </th>
    <th width="20">
        <input type="checkbox" name="checkall-toggle" value="" title="Check All" onclick="Joomla.checkAll(this)"/>
    </th>
    <th align="left" style="text-align:left;">
        <?php echo JText::_('COM_YOUTUBEGALLERY_CATEGORYNAME'); ?>
    </th>

</tr>
