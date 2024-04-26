<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file access');
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

?>
<tr>
    <?php if ($this->canState or $this->canDelete): ?>
        <th class="w-1 text-center">
            <?php echo HTMLHelper::_('grid.checkall'); ?>
        </th>
    <?php endif; ?>

    <th scope="col">
        <?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_THEMENAME', 'a.es_themename', $this->listDirn, $this->listOrder); ?>
    </th>

    <th scope="col" class="text-center d-none d-md-table-cell">
        <?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
    </th>

    <th scope="col" class="w-12 d-none d-xl-table-cell">
        <?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>

    <th scope="col">
        Media Folder
    </th>

    <th scope="col">
        Export
    </th>
</tr>
