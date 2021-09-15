<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
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
		<?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_LISTNAME', 'a.es_listname', $this->listDirn, $this->listOrder); ?>
	</th>
	
	<th scope="col">
        <?php echo JText::_('COM_YOUTUBEGALLERY_CATEGORY'); ?>
    </th>

    <th scope="col">
        <?php echo JText::_('COM_YOUTUBEGALLERY_UPDATE'); ?>
    </th>
    
	<th scope="col" style="text-align:center;">
        <?php echo JText::_('COM_YOUTUBEGALLERY_NUMBER_OF_VIDEOS'); ?>
    </th>

	<th scope="col" class="text-center d-none d-md-table-cell" >
		<?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_STATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
	</th>
	
	<th scope="col" class="w-12 d-none d-xl-table-cell" >
		<?php echo HTMLHelper::_('searchtools.sort', 'COM_YOUTUBEGALLERY_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
	</th>
</tr>
