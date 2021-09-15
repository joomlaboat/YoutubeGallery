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

class VideoSource_Vimeo
{
	public static function extractVimeoID($theLink)
	{
		preg_match('/http:\/\/vimeo.com\/(\d+)$/', $theLink, $matches);
		if (count($matches) != 0)
		{
			$vimeo_id = $matches[1];
			
			return $vimeo_id;
		}
		else
		{
			preg_match('/https:\/\/vimeo.com\/(\d+)$/', $theLink, $matches);
			if (count($matches) != 0)
			{
				$vimeo_id = $matches[1];
				return $vimeo_id;
			}
		}
		
		return '';
	}

	public static function renderVimeoPlayer($options, $width, $height, &$videolist_row, &$theme_row)
	{
		$videoidkeyword='****youtubegallery-video-id****';

		$playerid='youtubegalleryplayerid_'.$videolist_row->id;
		
		$settings=array();

		$settings[]=array('loop',(int)$options['es_repeat']);//Whether to restart the video automatically after reaching the end.
		
		$settings[]=array('autoplay',(int)$options['es_autoplay']);//Whether to start playback of the video automatically. This feature might not work on all devices.
		
		$settings[]=array('muted',(int)$theme_row->muteonplay);//Whether the video is muted upon loading.
		//The true value is required for the autoplay behavior in some browsers.
		
		/*
		if($options['es_showinfo']==0)
		{
			$settings[]=array('portrait',0);//Whether to display the video owner's portrait.
			$settings[]=array('title',0);//	Whether the player displays the title overlay.
			$settings[]=array('byline',0);//	Whether to display the video owner's name.
		}
		else
		{
			*/
			$settings[]=array('portrait',1);
			$settings[]=array('title',1);
			$settings[]=array('byline',1);
		//}
		
		if($options['es_controls']==0)
			$settings[]=array('background',1);//Whether the player is in background mode, which hides
		else
			$settings[]=array('background',0);//the playback controls, enables autoplay, and loops the video.
			
		
		if($options['es_colorone']!='')
			$settings[]=array('color',$options['es_colorone']);//The hexadecimal color value of the playback controls, which is normally 00ADEF.
			//The embed settings of the video might override this value.

		Helper::ApplyPlayerParameters($settings,$options['es_youtubeparams']);
		
		$settingline=Helper::CreateParamLine($settings);
		
		
		$border_width=3;
		
		if((int)$options['es_border']==1 and $options['es_colorone']!='')
		{
			$width=((int)$width)-($border_width*2);
			$height=((int)$height)-($border_width*2);
		}
		
		if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
			$http='https://';
		else
			$http='http://';
				
		$vimeoserver=$http.'vimeo.com/';

		$playerapiid='ygplayerapiid_'.$videolist_row->id;
		$result=VideoSource_Vimeo::ygHTML5VimeoPlayerAPI($settingline,$videolist_row->id,$playerapiid);
		
		
		return $result;
	}
	
	
	protected static function ygHTML5VimeoPlayerAPI($settingline,$vlid,$playerapiid)
	{

			$result='<div id="'.$playerapiid.'api"></div><!--DYNAMIC PLAYER-->';

			$result_head='<script src="https://player.vimeo.com/api/player.js"></script>';
			$document = JFactory::getDocument();	
			$document->addCustomTag($result_head);
						
			$AdoptedPlayerVars=str_replace('&amp;','", "',$settingline);
			$AdoptedPlayerVars='"'.str_replace('=','":"',$AdoptedPlayerVars).'", "enablejsapi":"1"';
		
			$result_head='
			youtubeplayer'.$vlid.'.vimeoplayer_options={'.$AdoptedPlayerVars.'};
';
		
			$document = JFactory::getDocument();	
			$document->addScriptDeclaration($result_head);
			
			return $result;
	}
	

}
