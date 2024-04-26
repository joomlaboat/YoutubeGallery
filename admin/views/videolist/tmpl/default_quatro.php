<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$document = Factory::getDocument();

$adminpath = Uri::root(true) . '/administrator/components/com_youtubegallery/';
$document->addCustomTag('<script src="' . $adminpath . 'js/videolist.js"></script>');

?>
<script>
    const isHTTPS = <?php echo((isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") ? 'true' : 'false'); ?>
</script>

<form action="<?php echo Route::_('index.php?option=com_youtubegallery&view=videolist&listid=' . $this->listid); ?>"
      method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">

                <p><a href="https://joomlaboat.com/contact-us" target="_blank" style="margin-left:20px;">Help (Contact
                        Tech-Support)</a></p>

                <?php
                // Search tools bar
                echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));
                ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span
                                class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table" id="userList">
                        <caption class="visually-hidden">
                            <?php echo Text::_('COM_USERS_USERS_TABLE_CAPTION'); ?>,
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                        <?php include('default_quatro_head.php'); ?>
                        </thead>
                        <tbody>
                        <?php echo $this->loadTemplate('quatro_body'); ?>
                        </tbody>
                    </table>

                    <?php echo $this->pagination->getListFooter(); ?>

                <?php endif; ?>

                <input type="hidden" name="task" value="">
                <input type="hidden" name="boxchecked" value="0">
                <?php echo HTMLHelper::_('form.token'); ?>
                <input type="hidden" name="es_listid"
                       value="<?php echo Factory::getApplication()->input->getInt('es_listid'); ?>"/>
            </div>
        </div>
    </div>
</form>
