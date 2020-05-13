<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class YouTubeGalleryData
{
	public static function formVideoList($rawList,&$firstvideo,$thumbnailstyle)
	{
		$gallery_list=array();
		
		foreach($rawList as $b)
		{
			$datalink='';
			$playlistid='';

			$b=str_replace("\n",'',$b);
			$b=trim(str_replace("\r",'',$b));

			$listitem=YouTubeGalleryMisc::csv_explode(',', $b, '"', false);

			$theLink=trim($listitem[0]);
			if($theLink!='')
				YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI($theLink,$gallery_list);
		}
		
		return $gallery_list;
		
	}
	
	public static function queryJoomlaBoatYoutubeGalleryAPI($theLink,&$gallery_list)
	{
		if (!function_exists('curl_init') and !function_exists('file_get_contents'))
			return "enable php functions: curl_init or file_get_contents";

		if (function_exists('phpversion'))
		{
			if(phpversion()<5)
				return "Update to PHP 5+";
		}

		try
		{
			$url = 'http://api.joomlaboat.com/youtube-gallery?key=666&v=5.0.0&query='.base64_encode($theLink);
			$htmlcode=YouTubeGalleryMisc::getURLData($url);

			$j=json_decode($htmlcode);

			if(!$j)
			{
				$item=array('es_error'=>'Connection Error');
				$gallery_list[]=YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($item);
				return;
			}
			
			if(isset($j['es_error']))
			{
				$item=array('es_error'=>$j['es_error']);
				$gallery_list[]=YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($item);
				return;
			}
			
			foreach($j as $item)
			{
				$gallery_list[]=YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI_SingleVideo((array)$item);
			}
		}
		catch(Exception $e)
		{
			$item=array('error'=>'Cannot get youtube video data.');
			$gallery_list[]=YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($item);
			return;
		}
	}
	
	public static function queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($item)
	{
		$blankArray=array(
				'videosource'=>'',
				'videoid'=>'',
				'alias'=>'',
				'imageurl'=>'',
				'title'=>'',
				'description'=>'',
				'publisheddate'=>'',
				'duration'=>0,
				'rating_average'=>0,
				'rating_max'=>0,
				'rating_min'=>0,
				'rating_numRaters'=>0,
				'statistics_favoriteCount'=>0,
				'statistics_viewCount'=>0,
				'keywords'=>'',
				'likes'=>0,
				'dislikes'=>'',
				'commentcount'=>'',
				'channel_username'=>'',
				'channel_title'=>'',
				'channel_subscribers'=>0,
				'channel_subscribed'=>0,
				'channel_location'=>'',
				'channel_commentcount'=>0,
				'channel_viewcount'=>0,
				'channel_videocount'=>0,
				'channel_description'=>'',
				'status'=>0,
				'error'=>''
				);
		
		if(isset($item['es_error']) and $item['es_error']!='')
		{
			$blankArray['status']=$item['es_status'];
			$blankArray['error']=$item['es_error'];
			return $blankArray;
		}
	
		$blankArray['videosource']=$item['es_videosource'];
		$blankArray['videoid']=$item['es_videoid'];
		$blankArray['link']=$item['es_link'];
		$blankArray['isvideo']=$item['es_isvideo'];
		
		$blankArray['title']=$item['es_title'];
		$blankArray['description']=$item['es_description'];
		$blankArray['publisheddate']=$item['es_publisheddate'];
		$blankArray['imageurl']=$item['es_imageurl'];
		$blankArray['channel_title']=$item['es_channeltitle'];
		$blankArray['duration']=$item['es_duration'];
		$blankArray['statistics_favoriteCount']=$item['es_statisticsfavoritecount'];
		$blankArray['statistics_viewCount']=$item['es_statisticsviewcount'];
		$blankArray['likes']=$item['es_likes'];
		$blankArray['dislikes']=$item['es_dislikes'];
		$blankArray['commentcount']=$item['es_commentcount'];
		$blankArray['keywords']=$item['es_keywords'];
		
		$blankArray['rating_average']=$item['es_ratingaverage'];
		$blankArray['rating_max']=$item['es_ratingmax'];
		$blankArray['rating_min']=$item['es_ratingmin'];
		$blankArray['rating_numRaters']=$item['es_ratingnumerofraters'];
		
		$blankArray['statistics_favoriteCount']=$item['es_statisticsfavoritecount'];
		$blankArray['statistics_viewCount']=$item['es_statisticsviewcount'];
		
		$blankArray['channel_username']=$item['es_channelusername'];
		$blankArray['channel_title']=$item['es_channeltitle'];
		$blankArray['channel_subscribers']=$item['es_channelsubscribers'];
		$blankArray['channel_subscribed']=$item['es_channelsubscribed'];
		$blankArray['channel_location']=$item['es_channellocation'];
		$blankArray['channel_commentcount']=$item['es_channelcommentcount'];
		$blankArray['channel_viewcount']=$item['es_channelviewcount'];
		$blankArray['channel_videocount']=$item['es_channelvideocount'];
		$blankArray['channel_description']=$item['es_channeldescription'];
		//$blankArray['']=$item['es_channeltotaluploadviews'];
		$blankArray['']=$item['es_alias'];
		
		return $blankArray;
	}
	
	public static function getVideoSourceName($link)
	{
		if(!(strpos($link,'://youtube.com')===false) or !(strpos($link,'://www.youtube.com')===false))
		{
			if(!(strpos($link,'/playlist')===false))
				return 'youtubeplaylist';
			if(strpos($link,'&list=PL')!==false)
			{
				return 'youtubeplaylist';
				//https://www.youtube.com/watch?v=cNw8A5pwbVI&list=PLMaV6BfupUm-xIMRGKfjj-fP0BLq7b6SJ
			}
			elseif(!(strpos($link,'/favorites')===false))
				return 'youtubeuserfavorites';
			elseif(!(strpos($link,'/user')===false))
				return 'youtubeuseruploads';
			elseif(!(strpos($link,'/results')===false))
				return 'youtubesearch';
			elseif(!(strpos($link,'youtube.com/show/')===false))
				return 'youtubeshow';
			elseif(!(strpos($link,'youtube.com/channel/')===false))
				return 'youtubechannel';
			else
				return 'youtube';
		}

		if(!(strpos($link,'://youtu.be')===false) or !(strpos($link,'://www.youtu.be')===false))
			return 'youtube';

		if(!(strpos($link,'youtubestandard:')===false))
			return 'youtubestandard';

		if(!(strpos($link,'videolist:')===false))
			return 'videolist';


		if(!(strpos($link,'://vimeo.com/user')===false) or !(strpos($link,'://www.vimeo.com/user')===false))
			return 'vimeouservideos';
		elseif(!(strpos($link,'://vimeo.com/channels/')===false) or !(strpos($link,'://www.vimeo.com/channels/')===false))
			return 'vimeochannel';
		elseif(!(strpos($link,'://vimeo.com/album/')===false) or !(strpos($link,'://www.vimeo.com/album/')===false))
			return 'vimeoalbum';
		elseif(!(strpos($link,'://vimeo.com')===false) or !(strpos($link,'://www.vimeo.com')===false))
		{
			preg_match('/http:\/\/vimeo.com\/(\d+)$/', $link, $matches);
			if (count($matches) != 0)
			{
				//single video
				return 'vimeo';
			}
			else
			{
				preg_match('/https:\/\/vimeo.com\/(\d+)$/', $link, $matches);
				if (count($matches) != 0)
				{
					//single video
					return 'vimeo';
				}
				else
				{
					preg_match('/http:\/\/vimeo.com\/(\d+)$/', $link, $matches);
					return 'vimeouservideos'; //or anything else
				}
			}


			return '';
		}


		if(!(strpos($link,'://own3d.tv/l/')===false) or !(strpos($link,'://www.own3d.tv/l/')===false))
			return 'own3dtvlive';

		if(!(strpos($link,'://own3d.tv/v/')===false) or !(strpos($link,'://www.own3d.tv/v/')===false))
			return 'own3dtvvideo';


		if(!(strpos($link,'video.google.com')===false))
			return 'google';

		if(!(strpos($link,'video.yahoo.com')===false))
			return 'yahoo';

		if(!(strpos($link,'://break.com')===false) or !(strpos($link,'://www.break.com')===false))
			return 'break';


		if(!(strpos($link,'://collegehumor.com')===false) or !(strpos($link,'://www.collegehumor.com')===false))
			return 'collegehumor';

		//http://www.dailymotion.com/playlist/x1crql_BigCatRescue_funny-action-big-cats/1#video=x7k9rx
		if(!(strpos($link,'://dailymotion.com/playlist/')===false) or !(strpos($link,'://www.dailymotion.com/playlist/')===false))
			return 'dailymotionplaylist';

		if(!(strpos($link,'://dailymotion.com')===false) or !(strpos($link,'://www.dailymotion.com')===false))
			return 'dailymotion';

		if(!(strpos($link,'://present.me')===false) or !(strpos($link,'://www.present.me')===false))
			return 'presentme';

		if(!(strpos($link,'://tiktok.com/')===false) or !(strpos($link,'://www.tiktok.com/')===false))
			return 'tiktok';

		if(!(strpos($link,'://ustream.tv/recorded')===false) or !(strpos($link,'://www.ustream.tv/recorded')===false))
			return 'ustream';

		if(!(strpos($link,'://ustream.tv/channel')===false) or !(strpos($link,'://www.ustream.tv/channel')===false))
			return 'ustreamlive';


		//http://api.soundcloud.com/tracks/49931.json  - accepts only resolved links
		if(!(strpos($link,'://api.soundcloud.com/tracks/')===false) )
			return 'soundcloud';

		if(!(strpos(strtolower($link),'.flv')===false))
			return '.flv';

		return '';
	}
}
