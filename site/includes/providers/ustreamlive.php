<?php
/**
 * YoutubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');

class VideoSource_UstreamLive
{
	public static function extractUstreamLiveID($theLink)
	{
		//http://www.ustream.tv/channel/live-iss-stream
		//http://www.ustream.tv/channel/95.0.02
		$l=explode('/',$theLink);
		if(count($l)>4)
			return $l[4];

		return '';
	}

	public static function renderUstreamLivePlayer($options, $width, $height, &$videolist_row, &$theme_row)
	{
		//http://www.dailymotion.com/doc/api/player.html

		$videoidkeyword='****youtubegallery-video-id****';

		$playerid='youtubegalleryplayerid_'.$videolist_row->id;

		$settings=array();

		if($options['color1']!='')
		{
			$settings[]=array('ub',$options['color1']);
			$settings[]=array('lc',$options['color1']);
		}

		if($options['color2']!='')
		{
			$settings[]=array('oc',$options['color2']);
			$settings[]=array('uc',$options['color2']);
		}

		$settings[]=array('info',$options['showinfo']);
		$settings[]=array('wmode','direct');

		YouTubeGalleryMisc::ApplyPlayerParameters($settings,$options['youtubeparams']);
		$settingline=YouTubeGalleryMisc::CreateParamLine($settings);

		$result='';


		$result.='<iframe '
			.' id="'.$playerid.'"';

		if(isset($options['title']))
			$result.=' alt="'.$options['title'].'"';

		$result.=' frameborder="0" width="'.$width.'" height="'.$height.'" src="http://www.ustream.tv/embed/'.$videoidkeyword.'?v=3&amp;wmode=direct&'.$settingline.'"'
			.($theme_row->responsive==1 ? ' onLoad="YoutubeGalleryAutoResizePlayer'.$videolist_row->id.'();"' : '')
			.' scrolling="no" style="border: 0px none transparent;"></iframe>';

		return $result;
	}
}
