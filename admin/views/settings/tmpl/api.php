<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
defined('_JEXEC') or die('Restricted access');

$key = YouTubeGalleryDB::getSettingValue('joomlaboat_api_key');
$host = YouTubeGalleryDB::getSettingValue('joomlaboat_api_host');

$youtubedataapi_key = YouTubeGalleryDB::getSettingValue('youtubedataapi_key');
$activated = false;

try {
    $htmlcode = YouTubeGalleryData::queryTheAPIServer('connection-test', $host);

    $j = json_decode($htmlcode);

    if (!$j) {
        if (str_contains($key, '-development')) {
            echo 'Server response: "' . $htmlcode . '"<br/>';
        }
        echo '<p style="color:red;">' . JText::_('COM_YOUTUBEGALLERY_SERVER_DOWN') . ' (' . $host . ').<br/>'
            . JText::_('COM_YOUTUBEGALLERY_PLEASE_CONTACT_SUPPORT') . ' support@joomlaboat.com</p>';
    }


    echo '<hr/>
			
			<div class="control-group">
				<div class="control-label">' . JText::_('COM_YOUTUBEGALLERY_YOUTUBE_API_KEY') . '</div>
				<div class="controls"><input type="text" name="youtubedataapi_key" style="min-width:370px;width:100%;" value="' . $youtubedataapi_key . '" /></div>
			</div>
			<p>' . JText::_('COM_YOUTUBEGALLERY_YOUTUBE_API_REGISTER_PROJECT')
        . ' <a href="https://console.developers.google.com/" target="_blank">link</a> '
        . JText::_('COM_YOUTUBEGALLERY_YOUTUBE_API_GET_THE_KEY') . '</p>
			<hr/>';

    $j = (array)$j;
    if (isset($j['connection'])) {
        if ((int)$j['connection'] == 1)
            echo '<p style="color:darkgreen;">' . JText::_('COM_YOUTUBEGALLERY_CONNECTION_OK') . '</p>';

        if ((int)$j['keytype'] == 1) {
            echo JText::_('COM_YOUTUBEGALLERY_ACTVATION');
            echo '<div class="specialbutton">
        <a href="https://joomlaboat.com/youtube-gallery#buy-extension" target="_blank" class="button">' . JText::_('COM_YOUTUBEGALLERY_BUYNOW') . '</a>
</div>';
        } elseif ((int)$j['keytype'] == 2) {
            $date = null;
            if ($j['date'])
                $date = $j['date'];

            echo '<p style="color:darkgreen;">' . JText::_('COM_YOUTUBEGALLERY_ACTVATED') . ' ' . $date . '</p>';
            $activated = true;


        } elseif ((int)$j['keytype'] == 3)
            echo '<p style="color:red;">' . JText::_('COM_YOUTUBEGALLERY_WRONG_KEY') . '</p>';
        elseif ((int)$j['keytype'] == 4)
            echo '<p style="color:red;">' . JText::_('COM_YOUTUBEGALLERY_KEY_EXPIRED') . '</p>';
    }
} catch (Exception $e) {
    echo '<p style="color:red;">' . JText::_('COM_YOUTUBEGALLERY_NO_CONNECTION') . '</p>';
}
?>
<div class="control-group">
    <div class="control-label"><?php echo JText::_('COM_YOUTUBEGALLERY_SERVER_ADDRESS'); ?></div>
    <div class="controls"><input type="text" readonly style="min-width:370px;width:100%;"
                                 value="<?php echo $_SERVER['SERVER_ADDR'] ?? ''; ?>"/></div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo JText::_('COM_YOUTUBEGALLERY_JOOMLABOAT_YOUTUBE_HOST'); ?></div>
    <div class="controls"><input type="text" name="joomlaboat_api_host" style="min-width:370px;width:100%;"
                                 value="<?php echo $host; ?>"<?php echo((!$activated and $host == 'https://joomlaboat.com/youtubegallery-api') ? ' readonly="READONLY"' : ''); ?> />
    </div>
</div>

<div class="control-group">
    <div class="control-label"><?php echo JText::_('COM_YOUTUBEGALLERY_JOOMLABOAT_YOUTUBE_KEY'); ?></div>
    <div class="controls"><input type="text" name="joomlaboat_api_key" style="min-width:370px;width:100%;"
                                 value="<?php echo $key; ?>"/></div>
</div>
