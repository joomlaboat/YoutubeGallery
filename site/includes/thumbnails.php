<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class YoutubeGalleryLayoutThumbnails
{
	public static function renderThumbnailLayout($thumbnail_layout,$listitem,$aHrefLink,$aLink, $videoid,&$theme_row,$item_index,&$gallery_list,&$videolist_row)
	{
		$listitem=YouTubeGalleryData::updateSingleVideo($listitem);
		
		$fields=array('width','height','imageurl','image','link','a','/a','link','title','description',
					  'videoid','videosource','publisheddate','duration',
					  'rating_average','rating_max','rating_min','rating_numRaters',
					  'statistics_favoriteCount','viewcount','favcount','keywords','isactive','commentcount','likes','dislikes','channel','social',
					  'odd','even','videolist','inwatchgroup','latitude','longitude','altitude'
					  );

		$tableFields=array('title','description',
					  'imageurl','videoid','videosource','publisheddate','duration',
					  'rating_average','rating_max','rating_min','rating_numRaters',
					  'keywords','commentcount','likes','dislikes','latitude','longitude','altitude');


		foreach($fields as $fld)
		{
			$imageFound=(strlen($listitem['imageurl'])>0);// or strlen($listitem['custom_imageurl'])>0);

			$isEmpty=YoutubeGalleryLayoutThumbnails::isThumbnailDataEmpty($fld,$listitem,$tableFields,$imageFound, $videoid, $item_index,$videolist_row);

			$ValueOptions=array();
			$ValueList=YoutubeGalleryMisc::getListToReplace($fld,$ValueOptions,$thumbnail_layout,'[]');

			
					
			$ifname='[if:'.$fld.']';
			$endifname='[endif:'.$fld.']';

			if($isEmpty)
			{

				foreach($ValueList as $ValueListItem)
					$thumbnail_layout=str_replace($ValueListItem,'',$thumbnail_layout);

				do{
					$textlength=strlen($thumbnail_layout);

					$startif_=strpos($thumbnail_layout,$ifname);
					if($startif_===false)
						break;

					if(!($startif_===false))
					{

						$endif_=strpos($thumbnail_layout,$endifname);
						if(!($endif_===false))
						{
							$p=$endif_+strlen($endifname);
							$thumbnail_layout=substr($thumbnail_layout,0,$startif_).substr($thumbnail_layout,$p);
						}
					}

				}while(1==1);
			}
			else
			{
				$thumbnail_layout=str_replace($ifname,'',$thumbnail_layout);
				$thumbnail_layout=str_replace($endifname,'',$thumbnail_layout);

				$i=0;
				foreach($ValueOptions as $ValueOption)
				{
					$options=$ValueOptions[$i];

					$vlu=YoutubeGalleryLayoutThumbnails::getTumbnailData($fld, $aHrefLink, $aLink, $listitem, $tableFields,$options,$theme_row,$gallery_list,$videolist_row); //NEW
					$thumbnail_layout=str_replace($ValueList[$i],$vlu,$thumbnail_layout);
					$i++;
				}
			}// IF NOT

			$ifname='[ifnot:'.$fld.']';
			$endifname='[endifnot:'.$fld.']';

			if(!$isEmpty)
			{
				foreach($ValueList as $ValueListItem)
					$thumbnail_layout=str_replace($ValueListItem,'',$thumbnail_layout);

				do{
					$textlength=strlen($thumbnail_layout);

					$startif_=strpos($thumbnail_layout,$ifname);
					if($startif_===false)
						break;

					if(!($startif_===false))
					{
						$endif_=strpos($thumbnail_layout,$endifname);
						if(!($endif_===false))
						{
							$p=$endif_+strlen($endifname);
							$thumbnail_layout=substr($thumbnail_layout,0,$startif_).substr($thumbnail_layout,$p);
						}
					}

				}while(1==1);

			}
			else
			{
				$thumbnail_layout=str_replace($ifname,'',$thumbnail_layout);
				$thumbnail_layout=str_replace($endifname,'',$thumbnail_layout);
				$vlu='';
				$i=0;
				foreach($ValueOptions as $ValueOption)
				{
					$thumbnail_layout=str_replace($ValueList[$i],$vlu,$thumbnail_layout);
					$i++;
				}
			}

		}//foreach($fields as $fld)

		return $thumbnail_layout;

	}

	
	public static function getTumbnailData($fld, $aHrefLink, $aLink, $listitem,&$tableFields,$options,&$theme_row,&$gallery_list,&$videolist_row) //NEW
	{
		$vlu='';

		switch($fld)
		{
			case 'width':

				$vlu=(int)$theme_row->width;
				if($vlu==0)
					$vlu=400;
			break;

			case 'height':

				$vlu=(int)$theme_row->height;
				if($vlu==0)
					$vlu=300;
			break;

			case 'imageurl':
				$vlu=YoutubeGalleryLayoutThumbnails::PrepareImageTag($listitem,$options,$theme_row,false);
			
			break;

			case 'image':
				$vlu=YoutubeGalleryLayoutThumbnails::PrepareImageTag($listitem,$options,$theme_row,true);
			break;

			case 'title':
				$vlu= str_replace('"','&quot;',$listitem['title']);
				$vlu=YouTubeGalleryMisc::html2txt($vlu);

				if($options!='')
				{
					$pair=explode(',',$options);
					$words=(int)$pair[0];
					if(isset($pair[1]))
						$chars=(int)$pair[1];
					else
						$chars=0;

					if($words!=0 or $chars!=0)
						$vlu=YouTubeGalleryMisc::PrepareDescription_($vlu, $words, $chars);
				}

			break;

			case 'description':


				$description= str_replace('"','&quot;',$listitem['description']);
				$description=YouTubeGalleryMisc::html2txt($description);

				$vlu=$description;

			break;

			case 'a':
				$vlu= $aHrefLink;
			break;

			case '/a':
				$vlu= '</a>';
			break;

			case 'link':
				if($options=='')
					$vlu= $aLink;
				elseif($options=='full')
				{
					if(strpos($aLink,'http://')!==false or strpos($aLink,'https://')!==false or strpos($aLink,'javascript:')!==false)
						$vlu= YouTubeGalleryMisc::curPageURL(false).$aLink; //NEW
				}
			break;

			case 'viewcount':
				$vlu=(int)$listitem['statistics_viewCount'];

				if($options!='')
					$vlu= number_format ( $vlu, 0, '.', $options);

			break;

			case 'likes':
				$vlu=(int)$listitem['likes'];

				if($options!='')
					$vlu= number_format ( $vlu, 0, '.', $options);

			break;

			case 'dislikes':
				$vlu=(int)$listitem['dislikes'];

				if($options!='')
					$vlu= number_format ( $vlu, 0, '.', $options);

			break;

			case 'latitude':
				$vlu=$listitem['latitude'];
			break;
			
			case 'longitude':
				$vlu=$listitem['longitude'];
			break;
			
			case 'altitude':
				$vlu=$listitem['altitude'];
			break;

			case 'channel':

				if($options!='')
				{
					$pair=explode(',',$options);
					$f='channel_'.$pair[0];

					$vlu=$listitem[$f];
					if(isset($pair[1]))
					{
						if($pair[0]=='subscribers' or $pair[0]=='subscribed' or $pair[0]=='commentcount' or $pair[0]=='viewcount' or $pair[0]=='videocount')
						{
							$vlu= number_format ( $vlu, 0, '.', $pair[1]);
						}
					}
				}
				else
					$vlu='Tag "[channel:<i>parameter</i>]" must have a parameter. Example: [channel:viewcount]';
			break;

			case 'commentcount':
				$vlu=(int)$listitem['commentcount'];

				if($options!='')
					$vlu= number_format ( $vlu, 0, '.', $options);

			break;

			case 'favcount':
				$vlu=$listitem['statistics_favoriteCount'];
			break;

			case 'duration':

				if($options=='')
					$vlu= $listitem['duration'];
				else
				{
					$parts=YouTubeGalleryMisc::csv_explode(',',$options,'"',false);

					$secs=(int)$listitem['duration'];

					$vlu=date($parts[0],mktime(0,0,$secs));
				}

			break;

			case 'publisheddate':

				if($options=='')
					$vlu= $listitem['publisheddate'];
				else
					$vlu=date($options,strtotime($listitem['publisheddate']));

			break;

			case 'social':
				$l='';
				if(strpos($aLink,'javascript:')===false)
				{
					$a=YouTubeGalleryMisc::curPageURL(false);
					if(strpos($aLink,$a)===false)
						$l='"'.$a.$aLink.'"';
					else
						$l='"'.$aLink.'"';

				}
				else
					$l='(window.location.href.indexOf("?")==-1 ?  window.location.href+"?videoid='.$listitem['videoid'].'" : window.location.href+"&videoid='.$listitem['videoid'].'" )';


				$vlu= YoutubeGalleryLayoutRenderer::SocialButtons($l,'ygt', $options,$listitem['id'],$listitem['videoid']);

			break;

			case 'videolist':

				if($options!='')
				{
					$pair=explode(',',$options);
					switch($pair[0])
					{
						case 'title':
							return $videolist_row->listname;
							break;

						case 'description':
							return $videolist_row->description;
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


				break;

			default:
				if(in_array($fld,$tableFields ))
					$vlu=$listitem[$fld];
			break;
		}

		return $vlu;
	}
	
	public static function isThumbnailDataEmpty($fld,$listitem,&$tableFields,$ImageFound, $videoid, $item_index,&$videolist_row)
	{

		foreach($tableFields as $tf)
		{
			if($fld==$tf)
			{
				if($listitem[$tf]=='' or (is_numeric($listitem[$tf]) and $listitem[$tf]==0))
					return true;
				else
					return false;
			}
		}

		switch($fld)
		{
			case 'width':
				return false;
			break;

			case 'height':
				return false;
			break;

			case 'inwatchgroup':
				$u=(int)$videolist_row->watchusergroup;

				if($videolist_row->watchusergroup==0 or $videolist_row->watchusergroup==1)
					return false; //public videos

				//check is authorized or not
				$user = JFactory::getUser();
				$usergroups = $user->get('groups');

				if(in_array($videolist_row->watchusergroup,$usergroups))
				{
					//The user group has access
					return false;
				}
				return true;

				break;
			case 'odd':
				if ($item_index % 2 == 0)
					return true; //not odd
				else
					return false; //odd

				break;

			case 'even':
				if ($item_index % 2 == 0)
					return false; //even
				else
					return true; //not even

				break;

			case 'isactive':

				if($listitem['videoid']==$videoid)
					return false;
				else
					return true;
				break;

			case 'image':
				if(!$ImageFound)
					return true;
				else
					return false;
			break;

			case 'a':
					return false;
			break;

			case '/a':
					return false;
			break;

			case 'link':
					return false;
			break;

			case 'viewcount':
					return false;
			break;

			case 'social':
					return false;
			break;

			case 'videolist':
					return false;
			break;

			case 'favcount':
				if($listitem['statistics_favoriteCount']==0)
					return true;
				else
					return false;
			break;

			case 'channel':
					if($listitem['channel_username']=='')
						return true;
					else
						return false;
			break;

		}
		return true;

	}
	
	
	public static function makeLink(&$listitem, $rel, &$aLinkURL, $videolist_row_id, $theme_row_id,$custom_itemid=0)
	{
		$videoid=$listitem['videoid'];

		$theview='youtubegallery';


		$juri=new JURI();
		$WebsiteRoot=$juri->root();

		if($WebsiteRoot[strlen($WebsiteRoot)-1]!='/') //Root must have slash / in the end
			$WebsiteRoot.='/';

		$URLPath=$_SERVER['REQUEST_URI']; // example:  /index.php'

		$pattern = '/[^\pL\pN$-_.+!*\'\(\)\,\{\}\|\\\\\^\~\[\]\<\>\#\%\"\;\/\?\:\@\&\=\.]/u';
		$URLPath = preg_replace($pattern, '', $URLPath);
		$URLPath = preg_replace('/"(\n.)+?"/m','', $URLPath);
		$URLPath = str_replace('"','', $URLPath);

		if($URLPath!='')
		{
			$p=strpos($URLPath,'?');
			if(!($p===false))
				$URLPath=substr($URLPath,0,$p);
		}


		$URLPathSecondPart='';

		if($URLPath!='')
		{
			//Path (URI) must be without leadint /
			if($URLPath!='')
			{
				if($URLPath[0]!='/')
					$URLPath=''.$URLPath;

			}


		}//if($URLPath!='')


		if($custom_itemid!=0)
		{
			//For Shadow/Light Boxes
			$aLink=$WebsiteRoot.'index.php?option=com_youtubegallery&view='.$theview;
			$aLink.='&Itemid='.$custom_itemid;
			$aLink.='&videoid='.$videoid;
			$aLink=JRoute::_($aLink);

			return $aLink;
		}
		elseif($rel!='')
		{
			//For Shadow/Light Boxes
			$aLink=$WebsiteRoot.'index.php?option=com_youtubegallery&view='.$theview;
			$aLink.='&listid='.$videolist_row_id;
			$aLink.='&themeid='.$theme_row_id;
			$aLink.='&videoid='.$videoid;

			return $aLink;

		}
		/////////////////////////////////


		if(JFactory::getApplication()->input->getCmd('option')=='com_youtubegallery' and JFactory::getApplication()->input->getCmd('view')==$theview )
		{
			//For component only

			$aLink='index.php?option=com_youtubegallery&view='.$theview.'&Itemid='.JFactory::getApplication()->input->getInt('Itemid',0);

			$aLink.='&videoid='.$videoid;

			$aLink=JRoute::_($aLink);

			if(strpos($aLink,'ygstart')===false and JFactory::getApplication()->input->getInt('ygstart')!=0)
			{
				if(strpos($aLink,'?')===false)
					$aLink.='?ygstart='.JFactory::getApplication()->input->getInt('ygstart');
				else
					$aLink.='&ygstart='.JFactory::getApplication()->input->getInt('ygstart');
			}

			return $aLink;
		}


		/////////////////////////////////

			$URLQuery= $_SERVER['QUERY_STRING'];
			$URLQuery= str_replace('"','', $URLQuery);


			$URLQuery=YouTubeGalleryMisc::deleteURLQueryOption($URLQuery, 'videoid');

			$URLQuery=YouTubeGalleryMisc::deleteURLQueryOption($URLQuery, 'onclick');
			$URLQuery=YouTubeGalleryMisc::deleteURLQueryOption($URLQuery, 'onmouseover');
			$URLQuery=YouTubeGalleryMisc::deleteURLQueryOption($URLQuery, 'onmouseout');
			$URLQuery=YouTubeGalleryMisc::deleteURLQueryOption($URLQuery, 'onmouseeenter');
			$URLQuery=YouTubeGalleryMisc::deleteURLQueryOption($URLQuery, 'onmousemove');
			$URLQuery=YouTubeGalleryMisc::deleteURLQueryOption($URLQuery, 'onmouseleave');

			$aLink=$URLPath.$URLPathSecondPart;



			$aLink.=($URLQuery!='' ? '?'.$URLQuery : '' );



			if(strpos($aLink,'?')===false)
				$aLink.='?';
			else
				$aLink.='&';


			$allowsef=YouTubeGalleryDB::getSettingValue('allowsef');
			if($allowsef==1)
			{
				$aLink=YouTubeGalleryMisc::deleteURLQueryOption($aLink, 'video');
				$aLink.='video='.$listitem['alias'];
			}
			else
				$aLink.='videoid='.$videoid;




			if(strpos($aLink,'ygstart')===false and JFactory::getApplication()->input->getInt('ygstart')!=0)
				$aLink.='&ygstart='.JFactory::getApplication()->input->getInt('ygstart');

			return JRoute::_($aLink);


	}//function
	
	public static function NavigationList($the_gallery_list, &$videolist_row, &$theme_row, $AllowPagination, $videoid,$custom_itemid=0)
	{
		if($theme_row->prepareheadtags>0)
		{
			$curPageUrl=YouTubeGalleryMisc::curPageURL();
			$document = JFactory::getDocument();

		}
		$catalogresult='';
		$paginationcode='';
		$gallery_list=$the_gallery_list;

		$tr=0;
		$count=0;
		$item_index=1;
		$isForShadowBox=false;
		if(isset($theme_row))
		{
					if($theme_row->rel!='')
						$isForShadowBox=true;
		}

		$bgcolor=$theme_row->bgcolor;
		$thumbnail_item='';

        foreach($gallery_list as $listitem)
        {
			if(strpos($listitem['title'],'***Video not found***')===false)
			{



				$aLinkURL='';



				if(!$isForShadowBox and ($theme_row->openinnewwindow==4 or $theme_row->openinnewwindow==5))
					$aLink='javascript:youtubeplayer'.$videolist_row->id.'.HotVideoSwitch(\''.$videolist_row->id.'\',\''.$listitem['videoid'].'\',\''.$listitem['videosource'].'\','.$listitem['id'].')';
				else
					$aLink=YoutubeGalleryLayoutThumbnails::makeLink($listitem, $theme_row->rel, $aLinkURL, $videolist_row->id, $theme_row->id,$custom_itemid);



				if($isForShadowBox and $theme_row->rel!='')// and $theme_row->openinnewwindow!=4 and $theme_row->openinnewwindow!=5)
						$aLink.='&tmpl=component';

				if($theme_row->hrefaddon!='' and $theme_row->openinnewwindow!=4 and $theme_row->openinnewwindow!=5)
				{
					$hrefaddon=str_replace('?','',$theme_row->hrefaddon);
					if($hrefaddon[0]=='&')
						$hrefaddon=substr($hrefaddon,1);

					if(strpos($aLink,$hrefaddon)===false)
					{

						if(strpos($aLink,'?')===false)
							$aLink.='?';
						else
							$aLink.='&';


						$aLink.=$hrefaddon;
					}
				}


				if($theme_row->openinnewwindow!=4 and $theme_row->openinnewwindow!=5)
				{
					if(strpos($aLink,'&amp;')===false)
						$aLink=str_replace('&','&amp;',$aLink);

					$aLink=$aLink.(($theme_row->openinnewwindow==2 OR $theme_row->openinnewwindow==3) ? '#youtubegallery' : '');
				}

					//to apply shadowbox
					//do not route the link
					if($theme_row->rel=='')
					{
						$aHrefLink='<a href="'.$aLink.'"'.(($theme_row->openinnewwindow==1 OR $theme_row->openinnewwindow==3) ? ' target="_blank"' : '').'>';
					}
					else
					{
						if($theme_row->rel=='jce')
							$aHrefLink='<a href="'.$aLink.'" class="modal">';
						else
							$aHrefLink='<a href="'.$aLink.'" rel="'.$theme_row->rel.'"';

					}
				

				$thumbnail_item=YoutubeGalleryLayoutThumbnails::renderThumbnailForNavBar($aHrefLink,$aLink,$videolist_row, $theme_row,$listitem, $videoid,$item_index,$gallery_list);

				if($thumbnail_item!='')
				{
					$catalogresult.='<div id="youtubegallery_thumbnail_'.$videolist_row->id.'_'.$count.'" style="display:contents;">'
					.'<div id="youtubegallery_thumbnail_box_'.$videolist_row->id.'_'.$listitem['id'].'" class="ygThumb-inactive" style="display:contents;">'
					.$thumbnail_item.'</div></div>';
					$count++;
				}
				$item_index++;
			}
			else
				$thumbnail_item='';
		}//for

		if($count<abs($theme_row->customlimit))
		{
			for($i=$count+1;$i<=$theme_row->customlimit;$i++)
			{//'.$thumbnail_item.'
				$catalogresult.='<div id="youtubegallery_thumbnail_'.$videolist_row->id.'_'.$i.'" style="display:none;">'.$thumbnail_item.'</div>'; //placeholder for thumbnail
			}
		}

		return '<div id="youtubegallery_thumbnails_'.$videolist_row->id.'">'.$catalogresult.'</div>';
	}
	
	
	public static function renderThumbnailForNavBar($aHrefLink,$aLink,&$videolist_row, &$theme_row,$listitem, $videoid,$item_index, &$gallery_list)
	{
		$result='';


		$thumbnail_layout='';


		//------------------------------- title
		$thumbtitle='';
		if($listitem['title']!='')
		{
			$thumbtitle=str_replace('"','',$listitem['title']);
			$thumbtitle=str_replace('\'','&rsquo;',$listitem['title']);

			if(strpos($thumbtitle,'&amp;')===false)
				$thumbtitle=str_replace('&','&amp;',$thumbtitle);
		}

		//------------------------------- add title and description hidden div containers if needed




		//------------------------------- end of image tag

		if($theme_row->customnavlayout!='')
		{
			$result=YoutubeGalleryLayoutThumbnails::renderThumbnailLayout($theme_row->customnavlayout,$listitem,$aHrefLink,$aLink, $videoid,$theme_row,$item_index,$gallery_list,$videolist_row);
		}
		else
		{
			$thumbnail_layout='[a][image][/a]'; //with link

			if($theme_row->showtitle)
			{
				if($thumbtitle!='')
					$thumbnail_layout.='<br/>'.($theme_row->thumbnailstyle=='' ? '<span style="font-size: 8pt;" >[title]</span>' : '<div style="'.$theme_row->thumbnailstyle.'">[title]</div>');
			}
			$result=YoutubeGalleryLayoutThumbnails::renderThumbnailLayout($thumbnail_layout,		$listitem,$aHrefLink,$aLink, $videoid,$theme_row,$item_index,$gallery_list,$videolist_row);
		}

		//}

		return $result;

	}
	
	public static function PrepareImageTag(&$listitem,$options,&$theme_row,$as_tag=true)
	{

		$imagetag='';

		//image title
		$thumbtitle=$listitem['title'];
		if($thumbtitle=='')
		{
			$mydoc = JFactory::getDocument();
			$thumbtitle=str_replace('"','',$mydoc->getTitle());
		}

		$thumbtitle=str_replace('"','',$thumbtitle);
		$thumbtitle=str_replace('\'','&rsquo;',$thumbtitle);

		if(strpos($thumbtitle,'&amp;')===false)
			$thumbtitle=str_replace('&','&amp;',$thumbtitle);

		//image src
		if($listitem['imageurl']=='')
		{
			if($as_tag)
			{
				$imagetag='<div style="';

				if($theme_row->thumbnailstyle!='')
					$imagetag.=$theme_row->thumbnailstyle;
				else
					$imagetag.='border:1px solid red;background-color:white;';

				if(strpos($theme_row->thumbnailstyle,'width')===false)
					$imagetag.='width:120px;height:90px;';

				$imagetag.='"></div>';
			}
			else
				$imagetag='';

		}
		else
		{
					$images=explode(';',$listitem['imageurl']);
					$index=0;
					if($options!='')
					{
						$index=(int)$options;
						if($index<0)
							$index=0;
						if($index>=count($images))
							$index=count($images)-1;
						
						$imagelink_array=explode(',',$images[$index]);
						$imagelink= $imagelink_array[0];
					}
					else
					{
						if($listitem['custom_imageurl']!='')
						{
							if(!(strpos($listitem['custom_imageurl'],'#')===false))
							{	
								$index=(int)(str_replace('#','',$listitem['custom_imageurl']));
								if($index<0)
									$index=0;
								if($index>=count($images))
									$index=count($images)-1;
							}
							else
								$imagelink = $listitem['custom_imageurl'];
						}
						else
						{
							$imagelink_array=explode(',',$images[$index]);
							$imagelink= $imagelink_array[0];
						}
					}
				
				if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
						$imagelink=str_replace('http://','https://',$imagelink);
					else
						$imagelink=str_replace('https://','http://',$imagelink);

				if($as_tag)
				{
					$imagetag='<img src="'.$imagelink.'"'.($theme_row->thumbnailstyle!='' ? ' style="'.$theme_row->thumbnailstyle.'"' : ' style="border:none;"');


					if(strpos($theme_row->thumbnailstyle,'width')===false)
						$imagetag.=' width="120" height="90"';

					$imagetag.=' alt="'.$thumbtitle.'" title="'.$thumbtitle.'"';
					$imagetag.=' />';
				}
				else
					$imagetag=$imagelink;


				if($theme_row->prepareheadtags==1 or $theme_row->prepareheadtags==3)//thumbnails or both
				{
					$document = JFactory::getDocument();
					$curPageUrl=YoutubeGalleryMisc::curPageURL();

					$imagelink=(strpos($imagelink,'http://')===false and strpos($imagelink,'https://')===false  ? $curPageUrl.'/' : '').$imagelink;

					$document->addCustomTag('<link rel="image_src" href="'.$imagelink.'" />'); //all thumbnails
				}

		}

		return $imagetag;
	}

}