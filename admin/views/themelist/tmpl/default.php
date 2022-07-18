<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

JHtml::_('behavior.tooltip');

?>
<form action="<?php echo JRoute::_('index.php?option=com_youtubegallery&view=themelist'); ?>" method="post" name="adminForm" id="adminForm">

<?php if(!empty( $this->sidebar)): ?>


	<div id="j-main-container" class="span10">
	
	<div id="j-sidebar-container" class="span2">
		<?php //echo $this->sidebar; ?>
	</div>
	
<?php else : ?>
	<div id="j-main-container">
<?php endif; ?>
<?php if (empty($this->items)): ?>
	<?php // echo $this->loadTemplate('toolbar');?>
    <div class="alert alert-no-items">
        <?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    </div>
<?php else : ?>
		<?php //echo $this->loadTemplate('toolbar');?>

<?php	/*$s=Factory::getApplication()->input->getVar( 'search'); ?>
	<div id="j-main-container" class="span10">
		<div id="filter-bar" class="btn-toolbar">

			<div class="filter-search btn-group pull-left">
				<label for="search" class="element-invisible">Search title.</label>
				<input type="text" name="search" placeholder="Search title." id="search" value="<?php echo $s; ?>" title="Search title." />
			</div>
			<div class="btn-group pull-left hidden-phone">
				<button class="btn tip hasTooltip" type="submit" title="Search"><i class="icon-search"></i></button>
				<button class="btn tip hasTooltip" type="button" onclick="document.id('search').value='';this.form.submit();" title="Clear"><i class="icon-remove"></i></button>

			</div>
		</div>

	</div>*/
?>



		<div class="clearfix"> </div>


        <table class="table table-striped">
                <thead><?php echo $this->loadTemplate('head');?></thead>
                <tfoot><?php echo $this->loadTemplate('foot');?></tfoot>
                <tbody><?php echo $this->loadTemplate('body');?></tbody>
        </table>

<?php endif; ?>

        <input type="hidden" id="task" name="task" value="" />
		<input type="hidden" id="view" name="view" value="themelist" />
        <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
        <?php echo JHtml::_('form.token'); ?>

</form>

<p style="text-align:left;">
<a href="http://www.joomlaboat.com/youtube-gallery/youtube-gallery-themes?view=catalog&layout=custom" target="_blank" style="color:#51A351;">Get more Themes</a>
<span style="margin-left:20px;">|</span>
<a href="https://joomlaboat.com/contact-us" target="_blank" style="margin-left:20px;">Help (Contact Tech-Support)</a>
</p>