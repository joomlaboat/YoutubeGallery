<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
?>
<?php foreach($this->items as $i => $item):

        $link2edit='index.php?option=com_youtubegallery&view=categoryform&layout=edit&id='.$item->id;
?>

        <tr class="row<?php echo $i % 2; ?>">
                <td>
					<?php if($this->canEdit): ?>
                        <a href="<?php echo $link2edit; ?>"><?php echo $item->id; ?></a>
					<?php else: ?>
						<?php echo $item->id; ?>
					<?php endif; ?>
                </td>

                <td class="center">
						<input type="checkbox" id="cb0" name="cid[]" value="<?php echo $item->id; ?>" onclick="Joomla.isChecked(this.checked);" title="Checkbox for row <?php echo $item->id; ?>" />
                </td>
					
                <td>
					<?php if($this->canEdit): ?>
                        <a href="<?php echo $link2edit; ?>">
							<?php 
								//echo $item->treename; 
								echo $item->es_categoryname; 
							?>
						</a>
					<?php else: ?>
						<?php 
							//echo $item->treename; 
							echo $item->es_categoryname; 
						?>
					<?php endif; ?>
                </td>
        </tr>
<?php endforeach; ?>
