<?php
/**
 * YoutubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use YouTubeGallery\Helper;

class VideoSource_UstreamLive
{
    public static function extractUstreamLiveID($theLink)
    {
        //https://www.ustream.tv/channel/live-iss-stream
        //https://www.ustream.tv/channel/95.0.02
        $l = explode('/', $theLink);
        if (count($l) > 4)
            return $l[4];

        return '';
    }

    public static function renderUstreamLivePlayer($options, $width, $height, &$videoListRow, &$theme_row)
    {
        //https://www.dailymotion.com/doc/api/player.html

        $videoidkeyword = '****youtubegallery-video-id****';

        $playerid = 'youtubegalleryplayerid_' . $videoListRow->id;

        $settings = array();

        if ($options['es_colorone'] != '') {
            $settings[] = array('ub', $options['es_colorone']);
            $settings[] = array('lc', $options['es_colorone']);
        }

        if ($options['es_colortwo'] != '') {
            $settings[] = array('oc', $options['es_colortwo']);
            $settings[] = array('uc', $options['es_colortwo']);
        }

        //$settings[]=array('info',$options['es_showinfo']);
        $settings[] = array('wmode', 'direct');

        Helper::ApplyPlayerParameters($settings, $options['es_youtubeparams']);
        $settingline = Helper::CreateParamLine($settings);

        $result = '';


        $result .= '<iframe '
            . ' id="' . $playerid . '"';

        if (isset($options['es_title']))
            $result .= ' alt="' . $options['es_title'] . '"';

        $result .= ' frameborder="0" width="' . $width . '" height="' . $height . '" src="https://www.ustream.tv/embed/' . $videoidkeyword . '?v=3&amp;wmode=direct&' . $settingline . '"'
            . ($theme_row->es_responsive == 1 ? ' onLoad="YoutubeGalleryAutoResizePlayer' . $videoListRow->id . '();"' : '')
            . ' scrolling="no" style="border: 0px none transparent;"></iframe>';

        return $result;
    }
}
