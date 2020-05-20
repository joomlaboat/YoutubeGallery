<?php
/**
 * YoutubeGallery
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');
//not finished
class VideoSource_DailyMotion
{
	public static function extractDailyMotionID($theLink)
	{

		if(strpos($theLink,'dailymotion.com/video/')===false)
		{
			//http://www.dailymotion.com/au/relevance/search/camp+fire+1/1#video=x16ckln
			$l=explode('#',$theLink);

			if(count($l)>1)
			{
				$a=explode('=',$l[1]);

				if(count($a)>1)
					return $a[1];
			}
		}
		else
		{
			//http://www.dailymotion.com/video/xrcy5b
			$l=explode('/',$theLink);
			if(count($l)>4)
			{
				$a=explode('_',$l[4]);
				$b=explode('#',$a[0]);
				return $b[0];
			}
		}

		return '';
		
	}
	
	public static function renderDailyMotionPlayer($options, $width, $height, &$videolist_row, &$theme_row)
	{		
		$videoidkeyword='****youtubegallery-video-id****';
		
		$title='';
		if(isset($options['title']))
			$title=$options['title'];

		$playerid='youtubegalleryplayerid_'.$videolist_row->id;
		
		$settings=array();
		$settings[]=array('autoplay',(int)$options['autoplay']);
		$settings[]=array('related',$options['relatedvideos']);
		$settings[]=array('controls',$options['controls']);
		if($theme_row->logocover)
			$settings[]=array('logo','0');
		else
			$settings[]=array('logo','1');
			
		if($options['color1']!='')
			$settings[]=array('foreground',$options['color1']);
			
		if($options['color2']!='')
			$settings[]=array('highlight',$options['color2']);
			
		$settings[]=array('info',$options['showinfo']);
		
		YouTubeGalleryMisc::ApplyPlayerParameters($settings,$options['youtubeparams']);
		$settingline=YouTubeGalleryMisc::CreateParamLine($settings);
		
		$result='';
		
		$result.='<iframe '
			.' id="'.$playerid.'"'
			.' alt="'.$title.'"'
			.' frameborder="0" width="'.$width.'" height="'.$height.'" src="http://www.dailymotion.com/embed/video/'.$videoidkeyword.'?'.$settingline.'"'
			.($theme_row->responsive==1 ? ' onLoad="YoutubeGalleryAutoResizePlayer'.$videolist_row->id.'();"' : '')
			.'></iframe>';
		
		return $result;
	}
}
