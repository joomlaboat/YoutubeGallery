<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once('misc.php');
require_once('data.php');
require_once('players.php');

class YoutubeGalleryLayoutRenderer
{
	function getValue($fld, $params, &$videolist_row, &$theme_row, $gallery_list, $width, $height, $videoid, $AllowPagination, $total_number_of_rows,$custom_itemid=0)//,$title
	{
		$videodescription_params=array();

		$fields_theme=array('bgcolor','cssstyle','navbarstyle','thumbnailstyle','linestyle','listnamestyle','activevideotitlestyle',
							'color1','color2','descr_style','rel','hrefaddon');
		if(in_array($fld,$fields_theme))
		{
			$theme_row_array = get_object_vars($theme_row);
			return $theme_row_array[$fld];
		}

		switch($fld)
		{
			case 'mediafolder':
				if($theme_row->mediafolder=='')
					return '';
				else
					return 'images/'.$theme_row->mediafolder;
			break;

			case 'videolist':
				if($params!='')
				{
					$pair=explode(',',$params);
					switch($pair[0])
					{
						case 'title':
							return $videolist_row->listname;
							break;

						case 'description':
							return YouTubeGalleryMisc::html2txt($videolist_row->description);
							break;

						case 'author':
							return $videolist_row->author;
							break;

						case 'playlist':
							$pl=YoutubeGalleryLayoutRenderer::getPlaylistIdsOnly($gallery_list);
							$vlu=implode(',',$pl);
							break;

						case 'watchgroup':
							return $videolist_row->watchusergroup ;
							break;

						case 'authorurl':
							return $videolist_row->authorurl ;
							break;
						case 'image':
							return $videolist_row->image ;
							break;
						case 'note':
							return $videolist_row->note ;
							break;
					}
				}
				return $videolist_row->listname;
			break;

			case 'cols':
				return $theme_row->cols;
				break;

			case 'listname':
				return $videolist_row->listname;
			break;

			case 'videotitle':
				$title=str_replace('"','_quote_',YouTubeGalleryGalleryList::getTitleByVideoID($videoid,$gallery_list));

				if($params!='')
				{
					$pair=explode(',',$params);
					$words=(int)$pair[0];
					if(isset($pair[1]))
						$chars=(int)$pair[1];
					else
						$chars=0;

					$title=YouTubeGalleryMisc::html2txt($title);
					$title=YouTubeGalleryMisc::PrepareDescription_($title, $words, $chars);
				}
				$title='<div id="YoutubeGalleryVideoTitle'.$videolist_row->id.'">'.$title.'</div>';
				return $title;
			break;

			case 'videodescription':
				$description=str_replace('"','&quot;',YouTubeGalleryGalleryList::getDescriptionByVideoID($videoid,$gallery_list));
				$description=YouTubeGalleryMisc::html2txt($description);

				$videodescription_params=array();

				if($params!='')
				{
					$videodescription_params=explode(',',$params);
					$description=YouTubeGalleryMisc::PrepareDescription($description, $videodescription_params);
				}
				$description='<div id="YoutubeGalleryVideoDescription'.$videolist_row->id.'">'.$description.'</div>';
				return $description;
			break;

			case 'videoplayer':
				$pair=explode(',',$params);

				if($params!='')
					$playerwidth=(int)$pair[0];
				else
					$playerwidth=$width;


				if(isset($pair[1]))
					$playerheight=(int)$pair[1];
				else
					$playerheight=$height;

				

				$containerStyle='width:'.$playerwidth.'px;height:'.$playerheight.'px;';

				if($theme_row->playvideo==0)
					$containerStyle.='display:none;';
				else
					$containerStyle.='display:block;';

				//-------------------- prepare description
				$new_gallery_list=array();
				$videodescription_params=explode(',',$params);
				
				foreach($gallery_list as $listitem)
				{
					$description=$listitem['description'];
					$description=str_replace('&quot;','_quote_',$description);
					$description=str_replace('"','_quote_',$description);
					$description=str_replace("'",'_quote_',$description);
					$description=str_replace("@",'_email_',$description);
					
					if($params!='')
						$description=YouTubeGalleryMisc::PrepareDescription($description, $videodescription_params);
					
					$listitem['description']=$description;

					$title=$listitem['title'];
					$title=str_replace('&quot;','_quote_',$title);
					$title=str_replace('"','_quote_',$title);
					$listitem['title']=str_replace("'",'_quote_',$title);
					
					$title=$listitem['custom_title'];
					$title=str_replace('&quot;','_quote_',$title);
					$title=str_replace('"','_quote_',$title);
					$listitem['custom_title']=str_replace("'",'_quote_',$title);
					
					$new_gallery_list[]=$listitem;
				}
				$result='<div id="YoutubeGallery_VideoRecords_'.$videolist_row->id.'" style="display:none;">'.json_encode($new_gallery_list).'</div>';
				
				return $result.'<div id="YoutubeGallerySecondaryContainer'.$videolist_row->id.'" style="'.$containerStyle.'"></div>';
			break;

			case 'navigationbar': //Obselete
				
				require_once('thumbnails.php');
				return YoutubeGalleryLayoutThumbnails::NavigationList($gallery_list, $videolist_row, $theme_row, $AllowPagination, $videoid,$custom_itemid);
			break;

			case 'thumbnails':
			
				require_once('thumbnails.php');
				return YoutubeGalleryLayoutThumbnails::NavigationList($gallery_list, $videolist_row, $theme_row, $AllowPagination, $videoid,$custom_itemid);
			break;

			case 'count':
				if ($params=='all')
					return $videolist_row->TotalVideos;
				else
					return count($gallery_list);
			break;

			case 'pagination':
				require_once('pagination_render.php');
				return YouTubeGalleryPagination::Pagination($theme_row,$gallery_list,$width,$total_number_of_rows);

				break;

			case 'width':
				return $width;
			break;

			case 'height':
				return $height;
			break;

			case 'instanceid':
				return $videolist_row->id;
			break;

			case 'videoid':
				return $videoid;
			break;

			case 'link':
				return  $link=YouTubeGalleryMisc::full_url($_SERVER);
			break;

			case 'social':
				return YoutubeGallerySocialButtons::SocialButtons('window.location.href','yg',$params,$videolist_row->id,$videoid);
			break;

			case 'video':

				$pair=YouTubeGalleryMisc::csv_explode(':',$params,'"',false);

				if($pair[0]!="")
				{
					$options='';
					if(isset($pair[1]))
						$options=$pair[1];

					$tableFields=array('title','description',
					  'imageurl','videoid','videosource','publisheddate','duration',
					  'rating_average','rating_max','rating_min','rating_numRaters',
					  'keywords','commentcount','likes','dislikes','playlist');


					$listitem=YouTubeGalleryGalleryList::getVideoRowByID($videoid,$gallery_list);

					if($listitem)
						return YoutubeGalleryLayoutRenderer::getTumbnailData($pair[0], "", "", $listitem,$tableFields,$options,$theme_row,$gallery_list,$videolist_row);
					else
						return '';
				}

			break;

		}//switch($fld)

	}//function



	public static function isEmpty($fld, &$videolist_row, &$theme_row, $gallery_list, $videoid, $AllowPagination, $total_number_of_rows)
	{

		$fields_theme=array('bgcolor','cssstyle','navbarstyle','thumbnailstyle','linestyle','listnamestyle','activevideotitlestyle','color1','color2','descr_style','rel','hrefaddon');
		if(in_array($fld,$fields_theme))
		{
			$theme_row_array = get_object_vars($theme_row);
			if($theme_row_array[$fld]=='')
				return true;
			else
				return false;
		}


		switch($fld)
		{
			case 'cols':
				return false;
			case 'social':
				return false;
			break;
			case 'link':
				return false;
			case 'video':
				return false;
			break;


			case 'videolist':
			//	if($videolist_row->listname=='')
				//	return true;
				//else
					return false;
			break;

			case 'listname':
				//if($videolist_row->listname=='')
					//return true;
				//else
					return false;
			break;

			case 'videotitle':
				//$title=YouTubeGalleryGalleryList::getTitleByVideoID($videoid,$gallery_list);
				//if($title=='')
					//return true;
				//else
					return false;
			break;

			case 'videodescription':
				//$description=YouTubeGalleryGalleryList::getDescriptionByVideoID($videoid,$gallery_list);
				//if($description=='')
					//return true;
				//else
					return false;
			break;

			case 'videoplayer':
				return false;//!$videoid;
			break;

			case 'navigationbar':
				if($total_number_of_rows==0)
					return true; //hide nav bar
				elseif($total_number_of_rows>0)
					return false;
			break;

			case 'thumbnails':
				if($total_number_of_rows==0)
					return true; //hide nav bar
				elseif($total_number_of_rows>0)
					return false;
			break;

			case 'mediafolder':
				if($theme_row->mediafolder=='')
					return true;
				else
					return false;
			break;

			case 'count':
				return ($total_number_of_rows>0 ? false : true);
			break;

			case 'pagination':
				return ($total_number_of_rows>5 and $AllowPagination ? false : true);
			break;

			case 'width':
				return false;
			break;

			case 'height':
				return false;
			break;

			case 'instanceid':
				return false;

			case 'videoid':
				return false;

			break;

		}
		return true;
	}

	function render($htmlresult, &$videolist_row, &$theme_row, $gallery_list, $width, $height, $videoid, $total_number_of_rows,$custom_itemid=0)
	{
		if($theme_row->rel=='jce')
			echo JHTML::_('behavior.modal'); //Modal Loader
		
		
		if(!isset($theme_row))
			return 'Theme not selected';

		if(!isset($videolist_row))
			return 'Video List not selected';

		if(strpos($htmlresult,'[videoplayer')===false and $theme_row->rel=='')
		{
			//[videoplayer] tag
			$htmlresult='[videoplayer]'.$htmlresult;// if [videoplayer] tag forgotten
		}

		if(strpos($htmlresult,'[pagination')===false)
			$AllowPagination=false;
		else
			$AllowPagination=true;

		$fields_generated=array('link','cols','width','height','video', 'videolist', 'listname','videotitle','videodescription','videoplayer','navigationbar','thumbnails','count','pagination','instanceid','videoid','mediafolder','social');
		$fields_theme=array('bgcolor','cssstyle','navbarstyle','thumbnailstyle','linestyle','listnamestyle','activevideotitlestyle','color1','color2','descr_style','rel','hrefaddon');

		$fields_all=array_merge($fields_generated, $fields_theme);

		foreach($fields_all as $fld)
		{
			$isEmpty=YoutubeGalleryLayoutRenderer::isEmpty($fld,$videolist_row,$theme_row,$gallery_list,$videoid,$AllowPagination,$total_number_of_rows);

			$ValueOptions=array();
			$ValueList=YouTubeGalleryMisc::getListToReplace($fld,$ValueOptions,$htmlresult,'[]');

			$ifname='[if:'.$fld.']';
			$endifname='[endif:'.$fld.']';

			if($isEmpty)
			{
				foreach($ValueList as $ValueListItem)
					$htmlresult=str_replace($ValueListItem,'',$htmlresult);

				do{
					$textlength=strlen($htmlresult);

					$startif_=strpos($htmlresult,$ifname);
					if($startif_===false)
						break;

					if(!($startif_===false))
					{

						$endif_=strpos($htmlresult,$endifname);
						if(!($endif_===false))
						{
							$p=$endif_+strlen($endifname);
							$htmlresult=substr($htmlresult,0,$startif_).substr($htmlresult,$p);
						}
					}

				}while(1==1);
			}
			else
			{
				$htmlresult=str_replace($ifname,'',$htmlresult);
				$htmlresult=str_replace($endifname,'',$htmlresult);

				$i=0;
				foreach($ValueOptions as $ValueOption)
				{
					$vlu= $this->getValue($fld,$ValueOption,$videolist_row, $theme_row,$gallery_list,$width,$height,$videoid,$AllowPagination,$total_number_of_rows,$custom_itemid);
					$htmlresult=str_replace($ValueList[$i],$vlu,$htmlresult);
					$i++;
				}
			}// IF NOT

			$ifname='[ifnot:'.$fld.']';
			$endifname='[endifnot:'.$fld.']';

			if(!$isEmpty)
			{
				foreach($ValueList as $ValueListItem)
					$htmlresult=str_replace($ValueListItem,'',$htmlresult);

				do{
					$textlength=strlen($htmlresult);

					$startif_=strpos($htmlresult,$ifname);
					if($startif_===false)
						break;

					if(!($startif_===false))
					{
						$endif_=strpos($htmlresult,$endifname);
						if(!($endif_===false))
						{
							$p=$endif_+strlen($endifname);
							$htmlresult=substr($htmlresult,0,$startif_).substr($htmlresult,$p);
						}
					}

				}while(1==1);

			}
			else
			{
				$htmlresult=str_replace($ifname,'',$htmlresult);
				$htmlresult=str_replace($endifname,'',$htmlresult);
				$vlu='';
				$i=0;
				foreach($ValueOptions as $ValueOption)
				{

					$htmlresult=str_replace($ValueList[$i],$vlu,$htmlresult);
					$i++;
				}
			}

		}//foreach($fields as $fld)

		return $htmlresult;

	}

	



	

	
	
	

	

	
	

	
}
