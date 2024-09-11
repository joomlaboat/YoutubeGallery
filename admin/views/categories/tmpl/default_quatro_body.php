<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file access');
use Joomla\CMS\HTML\HTMLHelper;

defined('_JEXEC') or die('Restricted access');

?>
<?php foreach ($this->items as $i => $item): ?>

    <?php

    $link2edit = 'index.php?option=com_youtubegallery&view=categoryform&layout=edit&id=' . $item->id;

    //$canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
    //$userChkOut = Factory::getUser($item->checked_out);
    ?>
    <tr class="row<?php echo $i % 2; ?>">

        <?php if ($this->canState or $this->canDelete): ?>
            <td class="text-center">
                <?php /* if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
					<?php else: ?>
						&#9633;
					<?php endif; ?>
				<?php else: */ ?>
                <?php echo HTMLHelper::_('grid.id', $i, $item->id); ?>
                <?php /* endif; */ ?>
            </td>
        <?php endif; ?>

        <td scope="row">
            <div class="name">
                <?php if ($this->canEdit): ?>
                    <a href="<?php echo $link2edit; ?>"><?php echo $this->escape($item->es_categoryname); ?></a>
                    <?php /* if ($item->checked_out): ?>
						<?php echo HTMLHelper::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'categories.', $canCheckin); ?>
					<?php endif; */ ?>
                <?php else: ?>
                    <?php echo $this->escape($item->es_categoryname); ?>
                <?php endif; ?>
            </div>
        </td>

        <td class="text-center btns d-none d-md-table-cell">
            <?php if ($this->canState) : ?>
                <?php /* if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'categories.', true, 'cb'); ?>
					<?php else: ?>
						<?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'categories.', false, 'cb'); ?>
					<?php endif; ?>
				<?php else: */ ?>
                <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'categories.', true, 'cb'); ?>
                <?php /* endif; */ ?>
            <?php else: ?>
                <?php echo HTMLHelper::_('jgrid.published', $item->published, $i, 'categories.', false, 'cb'); ?>
            <?php endif; ?>

        </td>
        <td class="d-none d-md-table-cell">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php endforeach; ?>
