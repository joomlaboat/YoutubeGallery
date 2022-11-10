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
<?php foreach ($this->items as $i => $item):

    $link2edit = 'index.php?option=com_youtubegallery&view=themeform&layout=edit&id=' . $item->id;
    $link2export = 'index.php?option=com_youtubegallery&view=themeexport&themeid=' . $item->id;
    ?>

    <tr class="row<?php echo $i % 2; ?>">
        <td>
            <?php if ($this->canEdit): ?>
                <a href="<?php echo $link2edit; ?>"><?php echo $item->id; ?></a>
            <?php else: ?>
                <?php echo $item->id; ?>
            <?php endif; ?>
        </td>
        <td>
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td>
            <?php if ($this->canEdit): ?>
                <a href="<?php echo $link2edit; ?>"><?php echo $item->es_themename; ?></a>
            <?php else: ?>
                <?php echo $item->es_themename; ?>
            <?php endif; ?>
        </td>

        <td>
            <?php echo($item->es_mediafolder != '' ? 'images/' . $item->es_mediafolder : ''); ?>
        </td>

        <td>
            <?php if ($this->canEdit): ?>
                <a href="<?php echo $link2export; ?>">Export Theme</a>
            <?php endif; ?>
        </td>
    </tr>
<?php endforeach; ?>
