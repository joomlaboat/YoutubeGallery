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

class VideoSource_TikTok
{
	public static function extractTikTokID($theLink)
	{
		//https://www.tiktok.com/@user/video/6793525112617454853
		$l=explode('/',$theLink);
		if(count($l)>5)
			return $l[5];

		return '';

	}
	
	public static function renderPlayer($options, $width, $height, &$videolist_row, &$theme_row)
	{
		
		$data='<blockquote class="tiktok-embed" cite="****youtubegallery-video-link****" data-video-id="****youtubegallery-video-id****" style="max-width: '.$width.'px;min-width: '.$height.'px;" ></blockquote>';
		$data.='****scriptbegin**** async src="https://www.tiktok.com/embed.js">****scriptend****';
		
		$playerid='youtubegalleryplayerid_'.$videolist_row->id;
		
		$result='<div>
		<iframe id="'.$playerid.'" frameborder="0" width="'.$width.'" height="'.$height.'" srcdoc=\''.$data.'\' scrolling="no" style="border: 0px none transparent;"></iframe>';

		

		return $result;
	}
}
