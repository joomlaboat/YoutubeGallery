<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class YouTubeGalleryGalleryList
{
	public static function getDescriptionByVideoID($videoid,&$gallery_list)
	{
		if(isset($gallery_list) and count($gallery_list)>0)
		{
				foreach($gallery_list as $g)
				{
						if($g['videoid']==$videoid)
								return $g['description'];
				}
		}

		return '';
	}
	
	public static function getPlaylistIdsOnly(&$gallery_list,$current_videoid='',$exclude_source='',$full=false,$allowplaylist=true)
	{
		//set $current_videoid to '' to do not rearrange video list
		$theList1=array();

		$theList2=array();


		$current_videoid_found=false;

		foreach($gallery_list as $gl_row)
		{
			if($gl_row['videoid']==$current_videoid)
			{
				$current_videoid_found=true;
			}
			else
			{
					if($exclude_source=='' or $gl_row['videosource']==$exclude_source)
					{
						$a='';
						if($current_videoid_found)
							$a=$gl_row['videoid'];
						else
							$a=$gl_row['videoid'];

						if($full)
							$theList2[]=$a.'*'.$gl_row['id'].'*'.$gl_row['videosource'];
						else
							$theList2[]=$a;
					}
			}

			if(!$allowplaylist)
				break;


		}//foreach

		return array_merge($theList1,$theList2);


	}


	public static function getListIndexByVideoID($videoid,&$gallery_list)
	{

		$i=0;
		foreach($gallery_list as $gl_row)
		{
			if($gl_row['videoid']==$videoid)
				return $i;
			$i++;
		}
		return -1;
	}
	
	public static function getTitleByVideoID($videoid,&$gallery_list)
	{
		$gl_row=YouTubeGalleryGalleryList::getVideoRowByID($videoid,$gallery_list);
		if($gl_row)
			return $gl_row['title'];

		return '';

	}

	public static function getThumbnailByID($videoid,&$gallery_list)
	{
		$gl_row=YouTubeGalleryGalleryList::getVideoRowByID($videoid,$gallery_list);
		if($gl_row)
			return $gl_row['imageurl'];

		return '';
	}

	public static function getVideoSourceByID($videoid,&$gallery_list)
	{
		$gl_row=YouTubeGalleryGalleryList::getVideoRowByID($videoid,$gallery_list);
		if($gl_row)
			return $gl_row['videosource'];

		return '';
	}
	
	public static function getVideoRowByID($videoid,&$gallery_list)
	{
		if($videoid=='' or $videoid=='****youtubegallery-video-id****')
			return false;

		if(isset($gallery_list) and count($gallery_list)>0)
		{

			foreach($gallery_list as $gl_row)
			{
				if($gl_row['videoid']==$videoid)
					return $gl_row;
			}
		}
		
		return false;
	}
}