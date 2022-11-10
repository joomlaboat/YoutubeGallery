<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file access');
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Access\Access;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\String\PunycodeHelper;

?>
<tr>
    <?php if ($this->canState or $this->canDelete): ?>
        <th class="w-1 text-center">
            <?php echo JHtml::_('grid.checkall'); ?>
        </th>
    <?php endif; ?>

    <th scope="col">
        <?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_CATEGORYNAME', 'a.es_categoryname', $this->listDirn, $this->listOrder); ?>
    </th>

    <th scope="col" class="text-center d-none d-md-table-cell">
        <?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
    </th>

    <th scope="col" class="w-12 d-none d-xl-table-cell">
        <?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
