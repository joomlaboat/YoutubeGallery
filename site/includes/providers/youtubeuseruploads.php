<?php
/**
 * YoutubeGallery
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');
require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'youtubeplaylist.php');

class VideoSource_YoutubeUserUploads
{
	public static function extractYouTubeUserID($youtubeURL)
	{
		//link example: http://www.youtube.com/user/designcompasscorp
		$matches=explode('/',$youtubeURL);

		if (count($matches) >3)
		{

			$userid = $matches[4];
			$pair=explode('?',$userid);
			return $pair[0];
		}

	    return '';
	}

	public static function getVideoIDList($youtubeURL,$optionalparameters,&$userid,&$datalink)
	{
		$videolist=array();
		$base_url='https://www.googleapis.com/youtube/v3';
		$api_key = YouTubeGalleryMisc::getSettingValue('youtube_api_key');

		if($api_key=='')
			return $videolist;

		$userid=VideoSource_YoutubeUserUploads::extractYouTubeUserID($youtubeURL);

		if($userid=='')
			return $videolist; //user id not found

		//------------- first step:  get user playlist id
		$part='contentDetails';
		$url=$base_url.'/channels?forUsername='.$userid.'&key='.$api_key.'&part='.$part;

		$htmlcode=YouTubeGalleryMisc::getURLData($url);

		if($htmlcode=='')
			return $videolist;

		$j=json_decode($htmlcode);
		if(!$j)
			return 'Connection Error';

		$items=$j->items;

		$playlistid='';
		if(isset($items[0]->contentDetails->relatedPlaylists->uploads))
		{
			$playlistid=$items[0]->contentDetails->relatedPlaylists->uploads;
			if($playlistid=='')
				return $videolist; //user not found or no files uploaded
		}

		//--------------- second step: get videos

		$videolist=VideoSource_YoutubePlaylist::getPlaylistVideos($playlistid,$datalink,$api_key,$optionalparameters);

		return $videolist;
	}

	public static function getUserInfo($youtubeURL,&$item)
	{
				
		$userid=VideoSource_YoutubeUserUploads::extractYouTubeUserID($youtubeURL);
		
		if($userid=='')
			return 'user id not found';
		
		//$url = 'http://gdata.youtube.com/feeds/api/users/'.$userid;
		$api_key = YouTubeGalleryMisc::getSettingValue('youtube_api_key');
		$url='https://www.googleapis.com/youtube/v3/channels?part=statistics,snippet&key='.$api_key.'&forUsername='.$userid;
		$item['datalink']=$url;
		
		
		
		$xml=false;
		$htmlcode=YouTubeGalleryMisc::getURLData($url);
		
		$j=json_decode($htmlcode);

		if(!$j)
			return 'Connection Error';
		
		if(!empty($j->error))
			return 'Error: '.$j->error->message;
		
		if(!isset($j->items) or count($j->items)==0)
			return 'Error: Channel not found.';
	
		$blankArray['datalink']=$url;
		
		$statistics=$j->items[0]->statistics;
		$snippet=$j->items[0]->snippet;
		

		if(isset($snippet->customUrl))
			$item['channel_username']=$snippet->customUrl;
		
		if(isset($snippet->title))
			$item['channel_title']=$snippet->title;
		
		if(isset($snippet->description))
			$item['channel_description']=$snippet->description;
		
		$item['channel_location']='';
		
		$item['channel_subscribed']=0;
						
		if(isset($statistics->commentCount))
			$item['channel_commentcount']=$statistics->commentCount;
						
		if(isset($statistics->videoCount))
			$item['channel_videocount']=$statistics->videoCount;
			
		if(isset($statistics->subscriberCount))
			$item['channel_subscribers']=$statistics->subscriberCount;
		
		if(isset($statistics->viewCount))
			$item['channel_viewcount']=$statistics->viewCount;
		
		if(isset($statistics->viewCount))
			$item['channel_totaluploadviews']=$statistics->viewCount; 
			
		return '';
		
	}


}
