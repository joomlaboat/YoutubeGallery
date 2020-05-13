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
	
	public static function getVideoData($theLink,$videoid,$customimage,$customtitle,$customdescription)
	{
		if (!function_exists('curl_init') and !function_exists('file_get_contents'))
			return "enable php functions: curl_init or file_get_contents";

		if (function_exists('phpversion') and phpversion()<5)
				return "Update to PHP 5+";
		
		$blankArray=array(
				'videosource'=>'tiktok',
				'videoid'=>$videoid,
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
				'dislikes'=>0,
				'commentcount'=>0,
				'channel_username'=>'',
				'channel_title'=>'',
				'channel_subscribers'=>0,
				'channel_subscribed'=>0,
				'channel_location'=>'',
				'channel_commentcount'=>0,
				'channel_viewcount'=>0,
				'channel_videocount'=>0,
				'channel_description'=>''
				);
				
		try
		{
			$url = 'https://www.tiktok.com/oembed?url='.$theLink;//https://www.tiktok.com/@scout2015/video/'.$videoid;
			$blankArray['datalink']=$url;
			$htmlcode=YouTubeGalleryMisc::getURLData($url);
	
			$j=json_decode($htmlcode);

			if(!$j)
				return 'Connection Error';
			
			if(isset($j->error))
			{
				if(isset($j->error->errors))
				{
					$e=$j->error->errors[0];
					return strip_tags($e->message);
				}
				
			}
			
			$pos=strpos($j->title,'#');
			
			if($pos!==false)
				$blankArray['title']=substr($j->title,0,$pos);
			else
				$blankArray['title']=$j->title;
						
			$blankArray['imageurl']=$j->thumbnail_url;
			$blankArray['channel_title']=$j->author_name;
			
			if($pos!==false)
			{
				$tags=array();
				$tArray=explode('#',substr($j->title,$pos));
				foreach($tArray as $t_)
				{
					$t=trim($t_);
					if($t!='')
						$tags[]=$t;
				}
				$blankArray['keywords']=implode(',',$tags);
			}
			return $blankArray;
		}
		catch(Exception $e)
		{
			return array(
					'videosource'=>'collegehumor',
					'videoid'=>$videoid,
					'imageurl'=>$theImage,
					'title'=>'***Video not found***',
					'description'=>$Description
					);
		}
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
