<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach ($this->items as $i => $item):

    $link2edit = 'index.php?option=com_youtubegallery&view=linksform&layout=edit&id=' . $item->id;
    $link2videolist = 'index.php?option=com_youtubegallery&view=videolist&listid=' . $item->id;
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
                <a href="<?php echo $link2edit; ?>"><?php echo $item->es_listname; ?></a>
            <?php else: ?>
                <?php echo $item->es_listname; ?>
            <?php endif; ?>
        </td>

        <td>
            <?php echo $item->categoryname; ?>
        </td>

        <td>
                        <span style="">
                                
                                <?php

                                if ($item->es_updateperiod >= 1)
                                    echo sprintf(Text::_('COM_YOUTUBEGALLERY_LASTUPDATE'), $item->es_lastplaylistupdate, $item->es_updateperiod);
                                else {
                                    $hours = round((24 * $item->es_updateperiod), 0);
                                    echo sprintf(Text::_('COM_YOUTUBEGALLERY_LASTUPDATE_HOURS'), $item->es_lastplaylistupdate, $hours);
                                }

                                ?>
                                
                        </span>
        </td>

        <td>
            <a href="<?php echo $link2videolist; ?>"><?php echo $item->number_of_videos; ?></a>
        </td>


    </tr>
<?php endforeach; ?>
