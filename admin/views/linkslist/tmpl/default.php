<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');


if (!function_exists('curl_init')) {
    echo '<div style="border-radius:5px;margin-bottom:10px;padding:5px;border: 1px solid red; ">

		<p>In order to let Youtube Gallery to request a list of videos and/or information about videos from Youtube servers, cURL should be installed or
		("allow_url_fopen" and "allow_url_include" enabled).

		';

    echo '
				<p style="color:red;font-weight:bold;">cURL is <span style="color:red">NOT installed</span> on this server. Install it.</p>
				';


    if (!ini_get('allow_url_fopen') or !ini_get('allow_url_include')) {
        echo '<p>Another option is to enable <span style="color:red;font-weight:bold;">"allow_url_fopen" and "allow_url_include"</span></p>';
        echo '
			<p>To enable it, modify your main php.ini file or create a new file named "php.ini" with lines below:<br/><br/>
<span style="color:green;">allow_url_fopen=on<br/>
allow_url_include=on</span><br/>
<br/>
Upload it to your website root and administrator folders. Or contact your hosting provider to enable this functionality.
<br/>
<i>These settings may be disabled for security reasons. If you are concern about it, enabled them temporary to get videos and disable again. </i>
			</p>';
    }

    echo '</div>';


}

if (!class_exists("DOMDocument")) {
    echo '<div style="border-radious:5px;margin-bottom:10px;padding:3px;border: 1px solid red; ">
				<p style="font-weight:bold;">DOM is NOT <span style="color:red">installed</span> on this server.</p>
				</div>';

}

//------------------ end PHP check
?>

<form action="<?php echo JRoute::_('index.php?option=com_youtubegallery'); ?>" method="post" name="adminForm"
      id="adminForm">

    <?php
    //for joomla 3.0
    //$s=Factory::getApplication()->input->getVar( 'search');
    ?>

    <?php /*
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

			<div class="filter-select hidden-phone" style="float:right;"><?php echo $this->lists['categories'].'&nbsp;'; ?></div>
</div></div>

		<div class="clearfix"> </div>
		*/ ?>


    <table class="table table-striped">
        <thead><?php echo $this->loadTemplate('head'); ?></thead>
        <tfoot><?php echo $this->loadTemplate('foot'); ?></tfoot>
        <tbody><?php echo $this->loadTemplate('body'); ?></tbody>
    </table>


    <input type="hidden" id="task" name="task" value=""/>
    <input type="hidden" id="boxchecked" name="boxchecked" value="0"/>
    <?php echo JHtml::_('form.token'); ?>
</form>

<p><a href="https://joomlaboat.com/contact-us" target="_blank" style="margin-left:20px;">Help (Contact Tech-Support)</a>
</p>