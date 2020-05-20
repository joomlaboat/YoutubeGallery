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

class VideoSource_YouTube
{
	public static function extractYouTubeID($youtubeURL)
	{
		if(!(strpos($youtubeURL,'://youtu.be')===false) or !(strpos($youtubeURL,'://www.youtu.be')===false))
		{
			//youtu.be
			$list=explode('/',$youtubeURL);
			if(isset($list[3]))
				return $list[3];
			else
				return '';
		}
		else
		{
			//youtube.com
			$arr=YouTubeGalleryMisc::parse_query($youtubeURL);
			if(isset($arr['v']))
				return $arr['v'];
			else
				return '';
		}

	}

	public static function renderYouTubePlayer($options, $width, $height, &$videolist_row, &$theme_row)//,$startsecond,$endsecond)
	{

		$videoidkeyword='****youtubegallery-video-id****';

		VideoSource_YouTube::ygPlayerTypeController($options, $theme_row);

		$playerapiid='ygplayerapiid_'.$videolist_row->id;
		$playerid='youtubegalleryplayerid_'.$videolist_row->id;

		$settings=VideoSource_YouTube::ygPlayerPrepareSettings($options, $theme_row,$playerapiid);//,$startsecond,$endsecond);

		$initial_volume=(int)$theme_row->volume;

		$playlist='';
		$full_playlist='';
		$youtubeparams=$options['youtubeparams'];
		$p=explode(';',$youtubeparams);


		if($options['allowplaylist']==1)
		{
			foreach($p as $v)
			{
				$pair=explode('=',$v);
				if($pair[0]=='playlist')
					$playlist=$pair[1];

				if($pair[0]=='fullplaylist')
					$full_playlist=$pair[1];
			}
		}

		if($options['allowplaylist']!=1 or $options['playertype']==5 or $options['playertype']==2)
		{
			$p_new=array();
			foreach($p as $v)
			{
				$pair=explode('=',$v);
				if($pair[0]!='playlist')
					$p_new[]=$v;
			}
			$youtubeparams=implode(';',$p_new);
		}

		YouTubeGalleryMisc::ApplyPlayerParameters($settings,$youtubeparams);

		$settingline=YouTubeGalleryMisc::CreateParamLine($settings);



		if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
			$http='https://';
		else
			$http='http://';

		if($theme_row->nocookie)
			$youtubeserver=$http.'www.youtube-nocookie.com/';
		else
			$youtubeserver=$http.'www.youtube.com/';

		$return=VideoSource_YouTube::ygHTML5PlayerAPI($width,$height,$youtubeserver,$videoidkeyword,$settingline,
															  $options,$videolist_row->id,$playerid,$theme_row,
															  $full_playlist,$initial_volume,$playerapiid,false);
		return $return;
	}

	protected static function ygPlayerPrepareSettings(&$options, &$theme_row, $playerapiid)//,$startsecond,$endsecond)
	{
		$settings=array();
		$settings[]=array('autoplay',(int)$options['autoplay']);

		$settings[]=array('hl','en');


		if($options['fullscreen']!=0)
			$settings[]=array('fs','1');
		else
			$settings[]=array('fs','0');


		$settings[]=array('showinfo',$options['showinfo']);
		$settings[]=array('iv_load_policy','3');
		$settings[]=array('rel',$options['relatedvideos']);
		$settings[]=array('loop',(int)$options['repeat']);
		$settings[]=array('border',(int)$options['border']);

		if($options['color1']!='')
			$settings[]=array('color1',$options['color1']);

		if($options['color2']!='')
			$settings[]=array('color2',$options['color2']);

		if($options['controls']!='')
		{
			$settings[]=array('controls',$options['controls']);
			if($options['controls']==0)
				$settings[]=array('version',3);

		}
		//--------------
		//if($options['playertype']!=2)
		//{
			//$settings[]=array('start',((int)$startsecond));
			//$settings[]=array('end',((int)$endsecond));
		//}


		if($options['playertype']==2)
		{
			//Player with Flash availability check
			$settings[]=array('playerapiid','ygplayerapiid_'.$playerapiid);
			$settings[]=array('enablejsapi','1');
		}


		return $settings;
	}

	protected static function ygPlayerTypeController(&$options, &$theme_row)
	{
		$initial_volume=(int)$theme_row->volume;


		if($options['playertype']==100) //auto
			$options['playertype']=2; //Flash with API by default

		//Change Flash 2 to 3
		elseif($options['playertype']==4)//Flash Version 2 is depricated (api)
			$options['playertype']=2;//Flash Version 3 (api)
		elseif($options['playertype']==3)//Flash Version 2 is depricated
			$options['playertype']=0;//Flash Version 3


		//Change to HTML5 if for Apple
		if($options['playertype']==0)
		{
			if(YouTubeGalleryMisc::check_user_agent_for_apple())
				$options['playertype']=1; //Flash Player not supported use IFrame Instead
		}

		//Change to HTML5 API if for Apple
		if($options['playertype']==2)
		{
			if(YouTubeGalleryMisc::check_user_agent_for_apple())
				$options['playertype']=5; //Flash Player not supported use IFrame API Instead
		}

		//Change to API if needed
		if($options['playertype']==0)
		{
			//Note - not available for IE
			if(($theme_row->muteonplay or $initial_volume!=-1) and $options['playertype']!=5)
					$options['playertype']=2; //because other types of player doesn't support this functionality.
		}

		//Change to API if needed
		if($options['playertype']==1)
		{
			//Note - not available for IE
			if(($theme_row->muteonplay or $initial_volume!=-1) and $options['playertype']!=5)
					$options['playertype']=5; //because other types of player doesn't support this functionality.
		}

		//Disable API for IE (Flash)
		if($options['playertype']==2)
		{
			if(YouTubeGalleryMisc::check_user_agent_for_ie())
				$options['playertype']=0; //Disable API for IE (so sad!)
		}


		//Disable API for IE (IFrame)
		if($options['playertype']==5)
		{
			if(YouTubeGalleryMisc::check_user_agent_for_ie())
				$options['playertype']=1; //Disable API for IE (so sad!)
		}

	}



	protected static function ygHTML5PlayerAPI($width,$height,$youtubeserver,$videoidkeyword,$settingline,
											   &$options,$vlid,$playerid,&$theme_row,&$full_playlist,$initial_volume,$playerapiid,$withFlash=false)
	{
			$showHeadScript=false;

			/*$result='<iframe id="'.$playerapiid.'api" type="text/html" width="640" height="390"
  src="http://www.youtube.com/embed/M7lc1UVf-VE?enablejsapi=1&origin=http://example.com"
  frameborder="0"></iframe>';*/
			$result='<div id="'.$playerapiid.'api"></div><!--DYNAMIC PLAYER-->';

			$showHeadScript=true;

			if($showHeadScript)
				$result.=VideoSource_YouTube::ygHTML5PlayerAPIHead($width,$height,$youtubeserver,$videoidkeyword,
																   $settingline,$options,$vlid,$playerid,
																   $theme_row,$full_playlist,$initial_volume,$playerapiid,$withFlash);

			return $result;
	}

	protected static function ygHTML5PlayerAPIHead($width,$height,$youtubeserver,$videoidkeyword,$settingline,
												   &$options,$vlid,$playerid,&$theme_row,&$full_playlist,
												   $initial_volume,$playerapiid,$withFlash=false)
	{

		$AdoptedPlayerVars=str_replace('&amp;','", "',$settingline);
		$AdoptedPlayerVars='"'.str_replace('=','":"',$AdoptedPlayerVars).'", "enablejsapi":"1"';

		if($full_playlist!='')
			$pl='"'.$full_playlist.'".split(",");';
		else
			$pl='[];';


		$autoplay=((int)$options['autoplay']==1 ? 'true' : 'false');
		$result_head='
			var tag = document.createElement("script");
			tag.src = "https://www.youtube.com/iframe_api";
			var firstScriptTag = document.getElementsByTagName("script")[0];
			firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
			youtubeplayer'.$vlid.'.youtubeplayer_options={'.$AdoptedPlayerVars.'};
			//window.YTConfig = {  host: "https://www.youtube.com"}
		';

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($result_head);

	}





}
