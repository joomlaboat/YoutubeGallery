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

class VideoSource_DailyMotion
{
    public static function extractDailyMotionID($theLink)
    {

        if (strpos($theLink, 'dailymotion.com/video/') === false) {
            //https://www.dailymotion.com/au/relevance/search/camp+fire+1/1#video=x16ckln
            $l = explode('#', $theLink);

            if (count($l) > 1) {
                $a = explode('=', $l[1]);

                if (count($a) > 1)
                    return $a[1];
            }
        } else {
            //https://www.dailymotion.com/video/xrcy5b
            $l = explode('/', $theLink);
            if (count($l) > 4) {
                $a = explode('_', $l[4]);
                $b = explode('#', $a[0]);
                return $b[0];
            }
        }

        return '';

    }

    public static function renderDailyMotionPlayer($options, $width, $height, &$videolist_row, &$theme_row)
    {
        $videoidkeyword = '****youtubegallery-video-id****';

        $title = '';
        if (isset($options['es_title']))
            $title = $options['es_title'];

        $playerid = 'youtubegalleryplayerid_' . $videolist_row->id;

        $settings = array();
        $settings[] = array('autoplay', (int)$options['es_autoplay']);
        $settings[] = array('related', $options['es_relatedvideos']);
        $settings[] = array('controls', $options['es_controls']);
        if ($theme_row->logocover)
            $settings[] = array('logo', '0');
        else
            $settings[] = array('logo', '1');

        if ($options['es_colorone'] != '')
            $settings[] = array('foreground', $options['es_colorone']);

        if ($options['color2'] != '')
            $settings[] = array('highlight', $options['es_colortwo']);

        //$settings[]=array('info',$options['es_showinfo']);

        Helper::ApplyPlayerParameters($settings, $options['es_youtubeparams']);
        $settingline = Helper::CreateParamLine($settings);

        $result = '';

        $result .= '<iframe '
            . ' id="' . $playerid . '"'
            . ' alt="' . $title . '"'
            . ' frameborder="0" width="' . $width . '" height="' . $height . '" src="https://www.dailymotion.com/embed/video/' . $videoidkeyword . '?' . $settingline . '"'
            . ($theme_row->responsive == 1 ? ' onLoad="YoutubeGalleryAutoResizePlayer' . $videolist_row->id . '();"' : '')
            . '></iframe>';

        return $result;
    }
}
