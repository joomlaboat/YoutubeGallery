<?php
//use Vimeo\Vimeo;

/**
 * YoutubeGallery
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


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

	public static function getVideoData($videoid,$customimage,$customtitle,$customdescription)
	{
		
		$theTitle='';
		$Description='';
		$theImage='';
				
		
		//-------------- prepare our Consumer Key and Secret
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');
		
		$consumer_key = YouTubeGalleryMisc::getSettingValue('vimeo_api_client_id');
		$consumer_secret = YouTubeGalleryMisc::getSettingValue('vimeo_api_client_secret');
		$oauth_access_token = YouTubeGalleryMisc::getSettingValue('vimeo_api_access_token');
		
		if($consumer_key=='' or $consumer_secret=='')
		{
			return array('videosource'=>'vimeo', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>'Vimeo API Key not set. (YoutubeGallery/Settings)','description'=>'It\'s important to apply for your own API key.');
		}
		//--------------
		
		
		require_once('Vimeo'.DIRECTORY_SEPARATOR.'Vimeo.php');
		//require_once('Vimeo/Exceptions/ExceptionInterface.php');
		//require_once('Vimeo/Exceptions/VimeoRequestException.php');
		//require_once('Vimeo/Exceptions/VimeoUploadException.php');
		
		$session = JFactory::getSession();
		if(!isset($session))
			session_start();
		
		if($oauth_access_token=='')
		{
			if($session->get('oauth_access_token')!='')
				$oauth_access_token=$session->get('oauth_access_token');
		}
		
		if($session->get('oauth_access_token_secret')!='')
			$oauth_access_token_secret=$session->get('oauth_access_token_secret');
		else
			$oauth_access_token_secret='';
		
		
		
		$vimeo = new Vimeo($consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret);

		$fields_desired = implode(',', array(
				'name',
              'description',
              'pictures',
              'stats',
              'tags',
              'metadata',
			  'created_time',
			  'duration'
              ));
		
		
		$a=array('fields' => $fields_desired,
                                      'sort' => 'date',
                                      'filter' => 'embeddable',
                                      'filter_embeddable' => 'true');
							 
		$video_info = $video_info = $vimeo->request('/videos/'.$videoid, $a,'GET',true);
	
		$video_body=$video_info['body'];
		
		if(isset($video_body))
		{
			if(!$video_body)
				return array('videosource'=>'vimeo', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>'***Video not found***','description'=>'Video not Found or Permission Denied.');
			
			if(isset($video_body['error']) and $video_body['error']!="")
			{
				return array('videosource'=>'vimeo', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>'***Video not found***','description'=>$video_body['error']);
			}
			
			
			
			if($customimage!='')
				$theImage=$customimage;
			else
			{
				$images=array();
				

				foreach($video_body['pictures']['sizes'] as $image)
				{
					$images[]=$image['link'];
				}
				
				$theImage=implode(',',$images);
			}
		
			if($customtitle=='')
				$theTitle=$video_body['name'];
			else
				$theTitle=$customtitle;
			
			if($customdescription=='')
				$Description=$video_body['description'];	
			else
				$Description=$customdescription;
			
			$keywords=array();
			
			if(isset($video_body['tags']))
			{
				foreach($video_body['tags'] as $tag)
				{
					$keywords[]=$tag['tag'];
				}
			}
			
			$videodata=
			array(
				'videosource'=>'vimeo',
				'videoid'=>$videoid,
				'imageurl'=>$theImage,
				'title'=>$theTitle,
				'description'=>$Description,
				'publisheddate'=>$video_body['created_time'],
				'duration'=>$video_body['duration'],
				'rating_average'=>0,
				'rating_max'=>0,
				'rating_min'=>0,
				'rating_numRaters'=>0,
				'statistics_favoriteCount'=>$video_body['metadata']['connections']['likes']['total'],
				'statistics_viewCount'=>$video_body['stats']['plays'],
				'keywords'=>implode(',',$keywords)
			);
			
			return $videodata;
			
		}
		else
			return array('videosource'=>'vimeo', 'videoid'=>$videoid, 'imageurl'=>$theImage, 'title'=>'***Video not found***','description'=>$Description);

	}
	
	public static function renderVimeoPlayer($options, $width, $height, &$videolist_row, &$theme_row)
	{
		$videoidkeyword='****youtubegallery-video-id****';

		$playerid='youtubegalleryplayerid_'.$videolist_row->id;
		
		$settings=array();

		$settings[]=array('loop',(int)$options['repeat']);//Whether to restart the video automatically after reaching the end.
		
		$settings[]=array('autoplay',(int)$options['autoplay']);//Whether to start playback of the video automatically. This feature might not work on all devices.
		
		$settings[]=array('muted',(int)$theme_row->muteonplay);//Whether the video is muted upon loading.
		//The true value is required for the autoplay behavior in some browsers.
		
		if($options['showinfo']==0)
		{
			$settings[]=array('portrait',0);//Whether to display the video owner's portrait.
			$settings[]=array('title',0);//	Whether the player displays the title overlay.
			$settings[]=array('byline',0);//	Whether to display the video owner's name.
		}
		else
		{
			$settings[]=array('portrait',1);
			$settings[]=array('title',1);
			$settings[]=array('byline',1);
		}
		
		if($options['controls']==0)
			$settings[]=array('background',1);//Whether the player is in background mode, which hides
		else
			$settings[]=array('background',0);//the playback controls, enables autoplay, and loops the video.
			
		
		if($options['color1']!='')
			$settings[]=array('color',$options['color1']);//The hexadecimal color value of the playback controls, which is normally 00ADEF.
			//The embed settings of the video might override this value.


		
		YouTubeGalleryMisc::ApplyPlayerParameters($settings,$options['youtubeparams']);
		
		$settingline=YouTubeGalleryMisc::CreateParamLine($settings);
		
		
		$border_width=3;
		
		if((int)$options['border']==1 and $options['color1']!='')
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
