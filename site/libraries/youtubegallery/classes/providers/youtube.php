<?php
/**
 * YoutubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

class VideoSource_YouTube
{
    public static function extractYouTubeID($youtubeURL)
    {
        if (!(strpos($youtubeURL, '://youtu.be') === false) or !(strpos($youtubeURL, '://www.youtu.be') === false)) {
            //youtu.be
            $list = explode('/', $youtubeURL);
            return $list[3] ?? '';
        } else {
            //youtube.com
            $arr = JoomlaBasicMisc::parse_query($youtubeURL);
            return $arr['v'] ?? '';
        }
    }

    public static function renderYouTubePlayer($options, $width, $height, &$videolist_row, &$theme_row)//,$startsecond,$endsecond)
    {
        $videoidkeyword = '****youtubegallery-video-id****';

        //VideoSource_YouTube::ygPlayerTypeController($options, $theme_row);

        $playerapiid = 'ygplayerapiid_' . $videolist_row->id;
        $playerid = 'youtubegalleryplayerid_' . $videolist_row->id;

        $settings = VideoSource_YouTube::ygPlayerPrepareSettings($options, $theme_row, $playerapiid);//,$startsecond,$endsecond);

        $initial_volume = (int)$theme_row->es_volume;

        $full_playlist = '';
        $youtubeparams = $options['es_youtubeparams'];
        $p = explode(';', $youtubeparams);


        if ($options['es_allowplaylist'] == 1) {
            foreach ($p as $v) {
                $pair = explode('=', $v);
                if ($pair[0] == 'playlist')
                    $playlist = $pair[1];

                if ($pair[0] == 'fullplaylist')
                    $full_playlist = $pair[1];
            }
        }

        if ($options['es_allowplaylist'] != 1)// or $options['playertype']==5 or $options['playertype']==2)
        {
            $p_new = array();
            foreach ($p as $v) {
                $pair = explode('=', $v);
                if ($pair[0] != 'playlist')
                    $p_new[] = $v;
            }
            $youtubeparams = implode(';', $p_new);
        }

        Helper::ApplyPlayerParameters($settings, $youtubeparams);
        $settingline = Helper::CreateParamLine($settings);

        if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
            $http = 'https://';
        else
            $http = 'http://';

        if ($theme_row->es_nocookie)
            $youtubeserver = $http . 'www.youtube-nocookie.com/';
        else
            $youtubeserver = $http . 'www.youtube.com/';

        $return = VideoSource_YouTube::ygHTML5PlayerAPI($width, $height, $youtubeserver, $videoidkeyword, $settingline,
            $options, $videolist_row->id, $playerid, $theme_row,
            $full_playlist, $initial_volume, $playerapiid, false);
        return $return;
    }

    protected static function ygPlayerPrepareSettings(&$options, &$theme_row, $playerapiid)//,$startsecond,$endsecond)
    {
        $settings = array();
        $settings[] = array('autoplay', (int)$options['es_autoplay']);

        $settings[] = array('hl', 'en');

        if ($options['es_fullscreen'] != 0)
            $settings[] = array('fs', '1');
        else
            $settings[] = array('fs', '0');


        //$settings[]=array('showinfo',$options['es_showinfo']);
        $settings[] = array('iv_load_policy', '3');
        $settings[] = array('rel', $options['es_relatedvideos']);
        $settings[] = array('loop', (int)$options['es_repeat']);
        $settings[] = array('border', (int)$options['es_border']);

        if ($options['es_colorone'] != '')
            $settings[] = array('color1', $options['es_colorone']);

        if ($options['es_colortwo'] != '')
            $settings[] = array('color2', $options['es_colortwo']);

        if ($options['es_controls'] != '') {
            $settings[] = array('controls', $options['es_controls']);
            if ($options['es_controls'] == 0)
                $settings[] = array('version', 3);
        }

        /*
        if($options['playertype']==2)
        {
            //Player with Flash availability check
            $settings[]=array('playerapiid','ygplayerapiid_'.$playerapiid);
            $settings[]=array('enablejsapi','1');
        }
        */
        return $settings;
    }

    protected static function ygHTML5PlayerAPI($width, $height, $youtubeserver, $videoidkeyword, $settingline,
                                               &$options, $vlid, $playerid, &$theme_row, &$full_playlist, $initial_volume, $playerapiid, $withFlash = false)
    {
        $result = '<div id="' . $playerapiid . 'api" data-marker="DYNAMIC PLAYER"></div>';

        $showHeadScript = true;

        if ($showHeadScript)
            $result .= VideoSource_YouTube::ygHTML5PlayerAPIHead($width, $height, $youtubeserver, $videoidkeyword,
                $settingline, $options, $vlid, $playerid,
                $theme_row, $full_playlist, $initial_volume, $playerapiid, $withFlash);

        return $result;
    }

    protected static function ygHTML5PlayerAPIHead($width, $height, $youtubeserver, $videoidkeyword, $settingline,
                                                   &$options, $vlid, $playerid, &$theme_row, &$full_playlist,
                                                   $initial_volume, $playerapiid, $withFlash = false)
    {

        $AdoptedPlayerVars = str_replace('&amp;', '", "', $settingline);
        $AdoptedPlayerVars = '"' . str_replace('=', '":"', $AdoptedPlayerVars) . '", "enablejsapi":"1"';

        if ($full_playlist != '')
            $pl = '"' . $full_playlist . '".split(",");';
        else
            $pl = '[];';


        $autoplay = ((int)$options['es_autoplay'] == 1 ? 'true' : 'false');
        $result_head = '
			var tag = document.createElement("script");
			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName("script")[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			youtubeplayer' . $vlid . '.youtubeplayer_options={' . $AdoptedPlayerVars . '};
			//window.YTConfig = {  host: "https://www.youtube.com"}
		';

        $document = Factory::getDocument();
        $document->addScriptDeclaration($result_head);
    }
}
