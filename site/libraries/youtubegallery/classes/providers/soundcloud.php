<?php
/**
 * YoutubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use YouTubeGallery\Helper;

class VideoSource_SoundCloud
{
	public static function extractID($theLink)
	{
		// http://api.soundcloud.com/tracks/49931.json

		$l=explode('/',$theLink);

		if(count($l)>4)
		{
			$a=explode('.',$l[4]);
			return $a[0];
		}

		return '';


	}

	public static function renderPlayer($options, $width, $height, &$videolist_row, &$theme_row)
	{
		$videoidkeyword='****youtubegallery-video-id****';

		$playerid='youtubegalleryplayerid_'.$videolist_row->id;

		$settings=array();

		$settings[]=array('auto_play',((int)$options['es_autoplay']) ? 'true' : 'false');
		$settings[]=array('hide_related',((int)$options['es_relatedvideos']) ? 'false' : 'true');

		//if($options['es_showinfo']==0)
		//{
			//$settings[]=array('show_artwork',false);
			//$settings[]=array('visual',false);
		//}
		//else
		//{
			$settings[]=array('show_artwork',true);
			$settings[]=array('visual',true);
		//}

		Helper::ApplyPlayerParameters($settings,$options['es_youtubeparams']);

		$settingline=Helper::CreateParamLine($settings);



		$result='';

		$title='';
		if(isset($options['es_title']))
			$title=$options['es_title'];


		if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
			$http='https://';
		else
			$http='http://';

		$data=$http.'w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/'.$videoidkeyword.'&amp;'.$settingline;


		//<iframe width="100%" height="450" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/22890406&amp;auto_play=false&amp;hide_related=false&amp;visual=true"></iframe>
		//<iframe width="100%" height="166" scrolling="no" frameborder="no" src="https://w.soundcloud.com/player/?url=https%3A//api.soundcloud.com/tracks/22890406&amp;color=ff5500&amp;auto_play=false&amp;hide_related=false&amp;show_artwork=true"></iframe>

		$result.=

		'<iframe src="'.$data.'"'
			.' id="'.$playerid.'"'
			.' width="'.$width.'"'
			.' height="'.$height.'"'
			.' alt="'.$title.'"'
			.' frameborder="'.((int)$options['es_border']==1 ? 'yes' : 'no').'"'
			.($theme_row->responsive==1 ? ' onLoad="YoutubeGalleryAutoResizePlayer'.$videolist_row->id.'();"' : '')
			.'>'
		.'</iframe>';



		return $result;
	}
}
