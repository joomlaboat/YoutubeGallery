<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file access');
defined('_JEXEC') or die('Restricted access');

?>
<?php foreach ($this->items as $i => $item): ?>

    <?php
    $link2edit = 'index.php?option=com_youtubegallery&view=themeform&layout=edit&id=' . $item->id;
    $link2export = 'index.php?option=com_youtubegallery&view=themeexport&themeid=' . $item->id;

    //$canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
    //$userChkOut = Factory::getUser($item->checked_out);
    ?>
    <tr class="row<?php echo $i % 2; ?>">

        <?php if ($this->canState or $this->canDelete): ?>
            <td class="text-center">
                <?php /* if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					<?php else: ?>
						&#9633;
					<?php endif; ?>
				<?php else: */ ?>
                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                <?php /* endif; */ ?>
            </td>
        <?php endif; ?>

        <td scope="row">
            <div class="name">
                <?php if ($this->canEdit): ?>
                    <a href="<?php echo $link2edit; ?>"><?php echo $this->escape($item->es_themename); ?></a>
                    <?php /* if ($item->checked_out): ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'themelist.', $canCheckin); ?>
					<?php endif; */ ?>
                <?php else: ?>
                    <?php echo $this->escape($item->es_themename); ?>
                <?php endif; ?>
            </div>
        </td>

        <td class="text-center btns d-none d-md-table-cell">
            <?php if ($this->canState) : ?>
                <?php /* if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'themelist.', true, 'cb'); ?>
					<?php else: ?>
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'themelist.', false, 'cb'); ?>
					<?php endif; ?>
				<?php else: */ ?>
                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'themelist.', true, 'cb'); ?>
                <?php /* endif; */ ?>
            <?php else: ?>
                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'themelist.', false, 'cb'); ?>
            <?php endif; ?>

        </td>

        <td class="d-none d-md-table-cell">
            <?php echo $item->id; ?>
        </td>

        <td scope="row">
            <?php echo($item->es_mediafolder != '' ? 'images/' . $item->es_mediafolder : ''); ?>
        </td>

        <td scope="row">
            <?php if ($this->canExport): ?>
                <a href="<?php echo $link2export; ?>">Export Theme</a>
            <?php endif; ?>
        </td>

    </tr>
<?php endforeach; ?>
