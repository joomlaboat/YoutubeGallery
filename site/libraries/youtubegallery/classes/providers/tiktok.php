<?php
/**
 * YouTubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class VideoSource_TikTok
{
    public static function extractTikTokID($theLink)
    {
        //https://www.tiktok.com/@user/video/6793525112617454853
        $l = explode('/', $theLink);
        if (count($l) > 5)
            return $l[5];

        return '';

    }

    public static function renderPlayer($options, $width, $height, &$videoListRow, &$theme_row)
    {

        $data = '<blockquote class="tiktok-embed" cite="****youtubegallery-video-link****" data-video-id="****youtubegallery-video-id****" style="max-width: ' . $width . 'px;min-width: ' . $height . 'px;" ></blockquote>';
        $data .= '****scriptbegin**** async src="https://www.tiktok.com/embed.js">****scriptend****';

        $playerid = 'youtubegalleryplayerid_' . $videoListRow->id;

        $result = '<div>
		<iframe id="' . $playerid . '" frameborder="0" width="' . $width . '" height="' . $height . '" srcdoc=\'' . $data . '\' scrolling="no" style="border: 0px none transparent;"></iframe>';


        return $result;
    }
}
