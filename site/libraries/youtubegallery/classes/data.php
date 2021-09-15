<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use YouTubeGallery\Helper;

class YouTubeGalleryData
{
	public static function formVideoList(&$videolist_row,$rawList,&$firstvideo,$thumbnailstyle,$force=false)
	{
		$gallery_list=array();
		$ordering=0;
		
		foreach($rawList as $b)
		{
			$datalink='';
			$playlistid='';

			$b=str_replace("\n",'',$b);
			$b=trim(str_replace("\r",'',$b));

			$listitem=Helper::csv_explode(',', $b, '"', false);

			$theLink=trim($listitem[0]);
			if($theLink!='')
			{
				$item=array();
				if(isset($listitem[1]))
					$item['es_customtitle']=$listitem[1];
				
				if(isset($listitem[2]))
					$item['es_customdescription']=$listitem[2];
				
				if(isset($listitem[3]))
					$item['es_customimageurl']=$listitem[3];
				
				if(isset($listitem[4]))
					$item['es_specialparams']=$listitem[4];
				
				if(isset($listitem[5]))
					$item['es_startsecond']=$listitem[5];
				
				if(isset($listitem[6]))
					$item['es_endsecond']=$listitem[6];
				
				if(isset($listitem[7]))
					$item['es_watchgroup']=$listitem[7];
				
				YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI($theLink,$gallery_list,$item,$ordering,$videolist_row,$force);
			}
		}
		
		return $gallery_list;
	}
	
	public static function updateSingleVideo($listitem,&$videolist_row)
	{
		$videolist_id=$videolist_row->id;
		
		if($listitem['es_lastupdate']!='' and $listitem['es_lastupdate']!='0000-00-00 00:00:00' and ($listitem['es_isvideo']==1 and $listitem['es_duration']!=0))
			return $listitem; //no need to update. But this should count the update period. In future version
		
		$theLink=trim($listitem['es_link']);
		if($theLink=='')
			return $listitem;
			
		$item=array();//where to save
		
		//Check if YouTubeGallery API installed
		$file = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegalleryapi' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php';
		if (file_exists($file))
		{
			require_once ($file);
			
			$y = new YouTubeGalleryAPIMisc;
            
			$isnew = 0;
			$active_key = true;
			
			$results = $y->checkLink($theLink, $isnew, $active_key, $force_update = true, $videolist_id = $videolist_row->id);
			
			if(count($results) == 1)
				return $results[0];
				
			return true;
		}
		else
		{
			echo 'query the server';
			YouTubeGalleryData::queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($theLink,$item,$listitem,$force=true);//force the update
		}
		
		if((int)$item['status']==0)
		{
			$parent_id=null;
			
			YouTubeGalleryDB::updateDBSingleItem($item,$videolist_id,$parent_id);//,$parent_details,$this_is_a_list,$list_count_left);
			
			if($listitem['es_customtitle'])
				$item['es_title']=$listitem['es_customtitle'];
			
			if($listitem['es_customdescription'])
				$item['es_description']=$listitem['es_customdescription'];
		}
		else
			return $listitem;
	}
	
	public static function queryTheAPIServer($theLink,$host='',$force=false)
	{
		if($host=='')
			$host=YouTubeGalleryDB::getSettingValue('joomlaboat_api_host');
	
		$key=YouTubeGalleryDB::getSettingValue('joomlaboat_api_key');
		
		//its very important to encode the youtube link.
		if(strpos($host,'?')===false)
			$url=$host.'?';
		else
			$url=$host.'&';
	
		$url .= 'key='.$key.'&v=5.3.2&query='.base64_encode($theLink);
		
		if($force)
			$url.='&force=1';//to force the update
		
		$urldata=Helper::getURLData($url);
		return $urldata;
	}
	
	public static function queryJoomlaBoatYoutubeGalleryAPI($theLink,&$gallery_list,&$original_item,&$ordering,$videolist_row,$force=false)
	{
		$updateperiod=60*24*($videolist_row->es_updateperiod)*60;
		$Playlist_lastupdate=YouTubeGalleryDB::Playlist_lastupdate($theLink);
		$diff = strtotime(date('Y-m-d H:i:s')) - strtotime($Playlist_lastupdate);
		
		$force=$force or $diff>$updateperiod;
		
		//Check if YouTubeGallery API installed
		$file = JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegalleryapi' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php';
		if (file_exists($file))
		{
			require_once ($file);
			
			$y = new YouTubeGalleryAPIMisc;
            
			$isnew = 0;
			$active_key = true;
			
			$results = $y->checkLink($theLink, $isnew, $active_key, $force_update = $force, $videolist_id = $videolist_row->id);
			
			foreach($results as $result)
				$gallery_list[] = $result;
				
			return true;
		}
		
		
		
		
		$item=array();
		if (!function_exists('curl_init') and !function_exists('file_get_contents'))
		{
			$item['es_error']='Enable php functions: curl_init or file_get_contents.';
			$item['es_status']=-1;
			
			$gallery_list[]=YouTubeGalleryData::parse_SingleVideo($item);
			return false;
		}			

		if (function_exists('phpversion'))
		{
			if(phpversion()<5)
			{
				$item['es_error']='Update to PHP 5+';
				$item['es_status']=-1;
				$gallery_list[]=YouTubeGalleryData::parse_SingleVideo($item);
				return false;
			}
		}

		//try
		//{
			$htmlcode=YouTubeGalleryData::queryTheAPIServer($theLink,'',$force);

			$j_=json_decode($htmlcode);
			
			if(!$j_)
			{
				$item['es_error']='Connection Error';
				$item['es_status']=-1;
			
				$gallery_list[]=YouTubeGalleryData::parse_SingleVideo($item);
				return false;
			}
			
			$j=(array)$j_;
			
			if(isset($j['es_error']))
			{
				$item['es_error']=$j['es_error'];
				$item['es_status']=-1;
			
				$gallery_list[]=YouTubeGalleryData::parse_SingleVideo($item);
				return false;
			}
			
			foreach($j as $item)
			{
				$original_item['es_ordering']=$ordering;
				$gallery_list[]=YouTubeGalleryData::parse_SingleVideo((array)$item,$original_item);
				$ordering++;
			}
		/*
		}
		catch(Exception $e)
		{
			$item['es_error']='Cannot get youtube video data.';
			$item['es_status']=-1;
				
			$gallery_list[]=YouTubeGalleryData::parse_SingleVideo($item);
			return false;
		}
		*/
		
	}
	
	protected static function queryJoomlaBoatYoutubeGalleryAPI_SingleVideo($theLink,&$item,&$original_item,$force=false)
	{
		if (!function_exists('curl_init') and !function_exists('file_get_contents'))
		{
			$es_item=array('es_error'=>'Enable php functions: curl_init or file_get_contents.','es_status'=>-1);
			$item=YouTubeGalleryData::parse_SingleVideo($es_item);
			return false;
		}			

		if (function_exists('phpversion'))
		{
			if(phpversion()<5)
			{
				$es_item=array('es_error'=>'Update to PHP 5+','es_status'=>-1);
				$item=YouTubeGalleryData::parse_SingleVideo($es_item);
				return false;
			}
		}

		try
		{
			$htmlcode=YouTubeGalleryData::queryTheAPIServer($theLink,'',$force);
			
			$j=json_decode($htmlcode);

			if(!$j)
			{
				$es_item=array('es_error'=>'Connection Error','es_status'=>-1);
				$item=YouTubeGalleryData::parse_SingleVideo($es_item);
				return false;
			}
			
			if(isset($j['es_error']))
			{
				$es_item=array('es_error'=>$j['es_error'],'es_status'=>-1);
				$item=YouTubeGalleryData::parse_SingleVideo($es_item);
				return false;
			}
			
			if(count($j)==0)
			{
				$es_item=array('es_error'=>'Cannot get youtube video data. Video not found.','es_status'=>-1);
				$item=YouTubeGalleryData::parse_SingleVideo($es_item);
				return false;
			}
		
			$new_es_item=$j[0];
			$item=YouTubeGalleryData::parse_SingleVideo((array)$new_es_item,$original_item);
		}
		catch(Exception $e)
		{
			$es_item=array('es_error'=>'Cannot get youtube video data.','es_status'=>-1);
			$item=YouTubeGalleryData::parse_SingleVideo($es_item);
			return false;
		}
	}
	
	public static function parse_SingleVideo($item,$original_item=array())
	{
		//[channel_totaluploadviews] => 0 not used
		
		$blankArray = array(
			'id' => 0,
			'es_videoid' => '',
			'es_videolist' => 0,
			'es_parentid' => null,
			'es_videosource'=>'',
			'es_alias'=>'',
			'es_imageurl'=>'',
			'es_isvideo'=>1,
					
			'es_customimageurl' => (array_key_exists('es_customimageurl',$original_item) ? $original_item['es_customimageurl'] : ''),
			'es_customtitle' => (array_key_exists('es_customtitle',$original_item) ? $original_item['es_customtitle'] : ''),
			'es_customdescription' => (array_key_exists('es_customdescription',$original_item) ? $original_item['es_customdescription'] : ''),
			'es_specialparams' => (array_key_exists('es_specialparams',$original_item) ? $original_item['es_specialparams'] : ''),
			'es_lastupdate' => (array_key_exists('es_lastupdate',$original_item) and $original_item['es_lastupdate']!='0000-00-00 00:00:00' ? $original_item['es_lastupdate'] : ''),
			'es_link' => (array_key_exists('es_link',$original_item) ? $original_item['es_link'] : ''),
			'es_startsecond' => (array_key_exists('es_startsecond',$original_item) ? $original_item['es_startsecond'] : ''),
			'es_endsecond' => (array_key_exists('es_endsecond',$original_item) ? $original_item['es_endsecond'] : ''),
			'es_title'=>'',
			'es_description'=>'',
			'es_publisheddate'=>'',
			'es_duration'=>0,
			'es_ratingaverage'=>0,
			'es_ratingmax'=>0,
			'es_ratingmin'=>0,
			'es_ratingnumberofraters'=>0,
			'es_statisticsfavoritecount'=>0,
			'es_statisticsviewcount'=>0,
			'es_keywords'=>'',
			'es_likes'=>0,
			'es_dislikes'=>'',
			'es_commentcount'=>'',
			'es_channelusername'=>'',
			'es_channeltitle'=>'',
			'es_channelsubscribers'=>0,
			'es_channelsubscribed'=>0,
			'es_channellocation'=>'',
			'es_channelcommentcount'=>0,
			'es_channelviewcount'=>0,
			'es_channelvideocount'=>0,
			'es_channeldescription'=>'',
			'es_status'=>0,
			'es_error'=>'',
			'es_rawdata' =>null, 
			'es_datalink' => '',
			'es_latitude' => null,
			'es_longitude' => null,
			'es_altitude' => null,
			'es_ordering' => (array_key_exists('es_ordering',$original_item) ? $original_item['es_ordering'] : 0)
		);
				
		if(isset($item['es_error']) and $item['es_error']!='')
		{
			$blankArray['status']=$item['es_status'];
			$blankArray['error']=$item['es_error'];
			return $blankArray;
		}
		
		if(isset($item['es_parentid']))
			$blankArray['es_parentid']=$item['es_parentid'];
	
		$blankArray['es_videosource']=$item['es_videosource'];
		$blankArray['es_videoid']=$item['es_videoid'];
		$blankArray['es_link']=$item['es_link'];
		$blankArray['es_isvideo']=$item['es_isvideo'];
		$blankArray['es_lastupdate']=$item['es_lastupdate'];
		
		$blankArray['es_title']=$item['es_title'];
		$blankArray['es_description']=$item['es_description'];
		$blankArray['es_publisheddate']=$item['es_publisheddate'];
		$blankArray['es_imageurl']=$item['es_imageurl'];
		$blankArray['es_channel_title']=$item['es_channeltitle'];
		$blankArray['es_duration']=$item['es_duration'];
		$blankArray['es_statisticsfavoritecount']=$item['es_statisticsfavoritecount'];
		$blankArray['es_statisticsviewcount']=$item['es_statisticsviewcount'];
		$blankArray['es_likes']=$item['es_likes'];
		$blankArray['es_dislikes']=$item['es_dislikes'];
		$blankArray['es_commentcount']=$item['es_commentcount'];
		$blankArray['es_keywords']=$item['es_keywords'];
		
		$blankArray['es_ratingaverage']=$item['es_ratingaverage'];
		$blankArray['es_ratingmax']=$item['es_ratingmax'];
		$blankArray['es_ratingmin']=$item['es_ratingmin'];
		$blankArray['es_ratingnumberofraters']=$item['es_ratingnumberofraters'];
		
		$blankArray['es_statisticsfavoritecount']=$item['es_statisticsfavoritecount'];
		$blankArray['es_statisticsviewcount']=$item['es_statisticsviewcount'];
		
		$blankArray['es_channelusername']=$item['es_channelusername'];
		$blankArray['es_channeltitle']=$item['es_channeltitle'];
		$blankArray['es_channelsubscribers']=$item['es_channelsubscribers'];
		$blankArray['es_channelsubscribed']=$item['es_channelsubscribed'];
		$blankArray['es_channellocation']=$item['es_channellocation'];
		$blankArray['es_channelcommentcount']=$item['es_channelcommentcount'];
		$blankArray['es_channelviewcount']=$item['es_channelviewcount'];
		$blankArray['es_channelvideocount']=$item['es_channelvideocount'];
		$blankArray['es_channeldescription']=$item['es_channeldescription'];
		
		$blankArray['es_alias']=YouTubeGalleryDB::get_alias($item['es_title'],$item['es_videoid']);//$item['es_alias'];
		
		$blankArray['es_latitude']=$item['es_latitude'];
		$blankArray['es_longitude']=$item['es_longitude'];
		$blankArray['es_altitude']=$item['es_altitude'];
		
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

		return '';
	}
}
