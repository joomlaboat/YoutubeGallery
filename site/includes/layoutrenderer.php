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
	var $videodescription_params;


	function getValue($fld, $params, &$videolist_row, &$theme_row, $gallery_list, $width, $height, $videoid, $AllowPagination, $total_number_of_rows,$custom_itemid=0)//,$title
	{

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
				$title=str_replace('"','_quote_',YoutubeGalleryLayoutRenderer::getTitleByVideoID($videoid,$gallery_list));

				if($params!='')
				{
					$pair=explode(',',$params);
					$words=(int)$pair[0];
					if(isset($pair[1]))
						$chars=(int)$pair[1];
					else
						$chars=0;

					$title=YouTubeGalleryMisc::html2txt($title);
					$title=YoutubeGalleryLayoutRenderer::PrepareDescription_($title, $words, $chars);
				}
				$title='<div id="YoutubeGalleryVideoTitle'.$videolist_row->id.'">'.$title.'</div>';
				return $title;
			break;

			case 'videodescription':
				$description=str_replace('"','&quot;',YoutubeGalleryLayoutRenderer::getDescriptionByVideoID($videoid,$gallery_list));
				$description=YouTubeGalleryMisc::html2txt($description);

				$this->videodescription_params=array();

				if($params!='')
				{
					$this->videodescription_params=explode(',',$params);
					$description=YoutubeGalleryLayoutRenderer::PrepareDescription($description, $this->videodescription_params);
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

				YoutubeGalleryLayoutRenderer::addHotReloadScript($gallery_list,$playerwidth,$playerheight,$videolist_row, $theme_row);

				$containerStyle='width:'.$playerwidth.'px;height:'.$playerheight.'px;';

				if($theme_row->playvideo==0)
					$containerStyle.='display:none;';
				else
					$containerStyle.='display:block;';

				//-------------------- prepare description
				$new_gallery_list=array();
				$this->videodescription_params=explode(',',$params);
				
				foreach($gallery_list as $listitem)
				{
					$description=$listitem['description'];
					$description=str_replace('&quot;','_quote_',$description);
					$description=str_replace('"','_quote_',$description);
					$description=str_replace("'",'_quote_',$description);
					$description=str_replace("@",'_email_',$description);
					
					if($params!='')
						$description=YoutubeGalleryLayoutRenderer::PrepareDescription($description, $this->videodescription_params);
					
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

			case 'navigationbar':

				$pair=explode(',',$params);

				if((int)$pair[0]>0)
					$number_of_columns=(int)$pair[0];
				else
					$number_of_columns=(int)$theme_row->cols;


				if($number_of_columns<1)
					$number_of_columns=3;

				if($number_of_columns>10)
					$number_of_columns=10;


				if(isset($pair[1]))
					$navbarwidth=(int)$pair[1];
				else
					$navbarwidth=$width;

				return $this->ClassicNavTable($gallery_list, $navbarwidth, $number_of_columns, $videolist_row, $theme_row, $AllowPagination, $videoid,$custom_itemid);
			break;

			case 'thumbnails':
				//simple list
				return $this->NavigationList($gallery_list, $videolist_row, $theme_row, $AllowPagination, $videoid,$custom_itemid);
			break;

			case 'count':
				if ($params=='all')
					return $videolist_row->TotalVideos;
				else
					return count($gallery_list);
			break;

			case 'pagination':
				return YoutubeGalleryLayoutRenderer::Pagination($theme_row,$gallery_list,$width,$total_number_of_rows);

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
				return YoutubeGalleryLayoutRenderer::SocialButtons('window.location.href','yg',$params,$videolist_row->id,$videoid);
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


					$listitem=YoutubeGalleryLayoutRenderer::getVideoRowByID($videoid,$gallery_list,true);//YoutubeGalleryLayoutRenderer::object_to_array($videolist_row);


					return YoutubeGalleryLayoutRenderer::getTumbnailData($pair[0], "", "", $listitem,$tableFields,$options,$theme_row,$gallery_list,$videolist_row);
				}

			break;

		}//switch($fld)

	}//function

	public static function object_to_array($data)
	{
	  if (is_array($data) || is_object($data))
	   {
        $result = array();
        foreach ($data as $key => $value)
        {
            $result[$key] = YoutubeGalleryLayoutRenderer::object_to_array($value);
        }
        return $result;
    }
    return $data;
	}

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
				if($videolist_row->listname=='')
					return true;
				else
					return false;
			break;

			case 'listname':
				if($videolist_row->listname=='')
					return true;
				else
					return false;
			break;

			case 'videotitle':
				$title=YoutubeGalleryLayoutRenderer::getTitleByVideoID($videoid,$gallery_list);
				if($title=='')
					return true;
				else
					return false;
			break;

			case 'videodescription':
				$description=YoutubeGalleryLayoutRenderer::getDescriptionByVideoID($videoid,$gallery_list);
				if($description=='')
					return true;
				else
					return false;
			break;

			case 'videoplayer':
				return !$videoid;
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
		if(!isset($theme_row))
			return 'Theme not selected';

		if(!isset($videolist_row))
			return 'Video List not selected';

		if(strpos($htmlresult,'[videoplayer')===false and $theme_row->rel=='')
		{
			//[videoplayer] tag
			$htmlresult='[videoplayer]'.$htmlresult;
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
			$ValueList=YoutubeGalleryLayoutRenderer::getListToReplace($fld,$ValueOptions,$htmlresult,'[]');

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

	public static function getListToReplace($par,&$options,&$text,$qtype,$separator=':',$quote_char='"')
	{
		$fList=array();
		$l=strlen($par)+2;

		$offset=0;
		do{
			if($offset>=strlen($text))
				break;

			$ps=strpos($text, $qtype[0].$par.$separator, $offset);
			if($ps===false)
				break;

			if($ps+$l>=strlen($text))
				break;

			$quote_open=false;

			$ps1=$ps+$l;
			$count=0;
			do{

				$count++;
				if($count>100)
					die;

				if($quote_char=='')
					$peq=false;
				else
				{
					do
					{
						$peq=strpos($text, $quote_char, $ps1);

						if($peq>0 and $text[$peq-1]=='\\')
						{
							// ignore quote in this case
							$ps1++;

						}
						else
							break;

					}while(1==1);
				}

				$pe=strpos($text, $qtype[1], $ps1);

				if($pe===false)
					break;

				if($peq!==false and $peq<$pe)
				{
					//quote before the end character

					if(!$quote_open)
						$quote_open=true;
					else
						$quote_open=false;

					$ps1=$peq+1;
				}
				else
				{
					if(!$quote_open)
						break;

					$ps1=$pe+1;

				}
			}while(1==1);



		if($pe===false)
			break;

		$notestr=substr($text,$ps,$pe-$ps+1);

			$options[]=trim(substr($text,$ps+$l,$pe-$ps-$l));
			$fList[]=$notestr;


		$offset=$ps+$l;


		}while(!($pe===false));

		//for these with no parameters
		$ps=strpos($text, $qtype[0].$par.$qtype[1]);
		if(!($ps===false))
		{
			$options[]='';
			$fList[]=$qtype[0].$par.$qtype[1];
		}

		return $fList;
	}

	public static function getPagination($num,$limitstart,$limit,&$theme_row)
	{
				$AddAnchor=false;
				if($theme_row->openinnewwindow==2 or $theme_row->openinnewwindow==3)
				{
					$AddAnchor=true;
				}
				//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'pagination.php');
				require_once('pagination.php');
				$thispagination = new YGPagination($num, $limitstart, $limit, '', $AddAnchor );

				return $thispagination;
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


			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'videoid');

			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'onclick');
			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'onmouseover');
			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'onmouseout');
			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'onmouseeenter');
			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'onmousemove');
			$URLQuery=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($URLQuery, 'onmouseleave');

			$aLink=$URLPath.$URLPathSecondPart;



			$aLink.=($URLQuery!='' ? '?'.$URLQuery : '' );



			if(strpos($aLink,'?')===false)
				$aLink.='?';
			else
				$aLink.='&';


			$allowsef=YouTubeGalleryMisc::getSettingValue('allowsef');
			if($allowsef==1)
			{
				$aLink=YoutubeGalleryLayoutRenderer::deleteURLQueryOption($aLink, 'video');
				$aLink.='video='.$listitem['alias'];
			}
			else
				$aLink.='videoid='.$videoid;




			if(strpos($aLink,'ygstart')===false and JFactory::getApplication()->input->getInt('ygstart')!=0)
				$aLink.='&ygstart='.JFactory::getApplication()->input->getInt('ygstart');

			return JRoute::_($aLink);


	}//function

	public static function deleteURLQueryOption($urlstr, $opt)
	{
		$url_first_part='';
		$p=strpos($urlstr,'?');
		if(!($p===false))
		{
			$url_first_part	= substr($urlstr,0,$p);
			$urlstr	= substr($urlstr,$p+1);
		}

		$params = array();

		$urlstr=str_replace('&amp;','&',$urlstr);

		$query=explode('&',$urlstr);

		$newquery=array();

		for($q=0;$q<count($query);$q++)
		{
			$p=stripos($query[$q],$opt.'=');
			if($p===false or ($p!=0 and $p===false))
				$newquery[]=$query[$q];
		}

		if($url_first_part!='' and count($newquery)>0)
			$urlstr=$url_first_part.'?'.implode('&',$newquery);
		elseif($url_first_part!='' and count($newquery)==0)
			$urlstr=$url_first_part;
		else
			$urlstr=implode('&',$newquery);

		return $urlstr;
	}

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

	public static function curPageURL($add_REQUEST_URI=true)
	{
		$pageURL = '';

			$pageURL .= 'http';

			if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}

			$pageURL .= "://";

			if (isset($_SERVER["HTTPS"]))
			{
				if (isset($_SERVER["SERVER_PORT"]) and $_SERVER["SERVER_PORT"] != "80") {
					$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
				} else {
					$pageURL .= $_SERVER["SERVER_NAME"];
				}
			}
			else
				$pageURL .= $_SERVER["SERVER_NAME"];

			if($add_REQUEST_URI)
			{
				//clean Facebook staff
				$uri=$_SERVER["REQUEST_URI"];
				if(!(strpos($uri,'fb_action_ids=')===false))
				{
					$uri= YoutubeGalleryLayoutRenderer::deleteURLQueryOption($uri, 'fb_action_ids');
					$uri= YoutubeGalleryLayoutRenderer::deleteURLQueryOption($uri, 'fb_action_types');
					$uri= YoutubeGalleryLayoutRenderer::deleteURLQueryOption($uri, 'fb_source');
					$uri= YoutubeGalleryLayoutRenderer::deleteURLQueryOption($uri, 'action_object_map');
					$uri= YoutubeGalleryLayoutRenderer::deleteURLQueryOption($uri, 'action_type_map');
					$uri= YoutubeGalleryLayoutRenderer::deleteURLQueryOption($uri, 'action_ref_map');
				}
				$pageURL .=$uri;
			}

		return $pageURL;
	}

	public static function Pagination(&$theme_row,$the_gallery_list,$width,$total_number_of_rows)
	{
		$mainframe = JFactory::getApplication();

		if(((int)$theme_row->customlimit)==0)
		{
			//limit=0; // UNLIMITED
			//No pagination - all items shown
			return '';
		}
		else
			$limit = (int)$theme_row->customlimit;

		$limitstart = JFactory::getApplication()->input->getInt('ygstart', 0);

		$pagination=YoutubeGalleryLayoutRenderer::getPagination($total_number_of_rows,$limitstart,$limit,$theme_row);

		$paginationcode='';

		if($limit==0)
		{
			$paginationcode.='
				<table cellspacing="0" style="padding:0px;width:'.$width.'px;border-style: none;"  border="0" >
				<tr style="height:30px;border-style: none;border-width:0px;">
				<td style="text-align:left;width:140px;vertical-align:middle;border: none;">'.JText::_( 'SHOW' ).': '.$pagination->getLimitBox("").'</td>
				<td style="text-align:right;vertical-align:middle;border: none;"><div class="pagination">'.$pagination->getPagesLinks().'</div></td>
				</tr>
				</table>
				';
		}
		else
		{
			$paginationcode.='<div class="pagination">'.$pagination->getPagesLinks().'</div>';

		}

		return $paginationcode;
	}

	function NavigationList($the_gallery_list, &$videolist_row, &$theme_row, $AllowPagination, $videoid,$custom_itemid=0)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');
		$misc=new YouTubeGalleryMisc;

		$misc->videolist_row =$videolist_row;
		$misc->theme_row =$theme_row;

		if($theme_row->prepareheadtags>0)
		{
			$curPageUrl=YoutubeGalleryLayoutRenderer::curPageURL();
			$document = JFactory::getDocument();

		}
		$catalogresult='';
		$paginationcode='';
		$gallery_list=$the_gallery_list;
		$getinfomethod=YouTubeGalleryMisc::getSettingValue('getinfomethod');

		//$misc->RefreshVideoData($gallery_list,$getinfomethod,false,$this->videodescription_params);

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


        foreach($gallery_list as $listitem)
        {
			if(strpos($listitem['title'],'***Video not found***')===false)
			{



				$aLinkURL='';



				if(!$isForShadowBox and ($theme_row->openinnewwindow==4 or $theme_row->openinnewwindow==5))
					$aLink='javascript:youtubeplayer'.$videolist_row->id.'.HotVideoSwitch(\''.$videolist_row->id.'\',\''.$listitem['videoid'].'\',\''.$listitem['videosource'].'\','.$listitem['id'].')';
				else
					$aLink=YoutubeGalleryLayoutRenderer::makeLink($listitem, $theme_row->rel, $aLinkURL, $videolist_row->id, $theme_row->id,$custom_itemid);



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

					$aHrefLink='<a href="'.$aLink.'"'
						.($theme_row->rel!='' ? ' rel="'.$theme_row->rel.'"' : '')
						.(($theme_row->openinnewwindow==1 OR $theme_row->openinnewwindow==3) ? ' target="_blank"' : '')
						.'>';

				$thumbnail_item=YoutubeGalleryLayoutRenderer::renderThumbnailForNavBar($aHrefLink,$aLink,$videolist_row, $theme_row,$listitem, $videoid,$item_index,$gallery_list);

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

	function ClassicNavTable($the_gallery_list,$width,$number_of_columns, &$videolist_row, &$theme_row, $AllowPagination, $videoid,$custom_itemid=0)
	{
		require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');
		$misc=new YouTubeGalleryMisc;
		$misc->videolist_row =$videolist_row;
		$misc->theme_row =$theme_row;

		if($theme_row->prepareheadtags>0)
		{
			$curPageUrl=YoutubeGalleryLayoutRenderer::curPageURL();
			$document = JFactory::getDocument();
		}

		$catalogresult='';
		$paginationcode='';
		$w_str='width:'.$width.(strpos($width,'%')===false ? 'px' : '').';';
		$catalogresult.='<table cellspacing="0" '.($theme_row->navbarstyle!='' ? 'style="'.$w_str.'padding:0;border:none;'.$theme_row->navbarstyle.'" ' : 'style="'.$w_str.'padding:0;border:none;margin:0 auto;"').'>
		<tbody>';

		$column_width=floor(100/$number_of_columns).'%';

		$gallery_list=$the_gallery_list;

		$getinfomethod=YouTubeGalleryMisc::getSettingValue('getinfomethod');

		///$misc->RefreshVideoData($gallery_list,$getinfomethod,false,$this->videodescription_params);

		$tr=0;
		$count=0;
		$bgcolor=$theme_row->bgcolor;

		$item_index=1;
        foreach($gallery_list as $listitem)
        {
			if(strpos($listitem['title'],'***Video not found***')===false)
			{

				if($getinfomethod=='js')
				{
					$thumbnail_item='updater';

					if($tr==0)
						$catalogresult.='<tr style="border:none;" >';

					$catalogresult.=
						'<td style="width:'.$column_width.';vertical-align:top;text-align:center;border:none;'.($bgcolor!='' ? ' background-color: #'.$bgcolor.';' : '').'">'
						.$thumbnail_item.'</td>';

					$tr++;
					if($tr==$number_of_columns)
					{
						$catalogresult.='
								</tr>
					';

					if($count+1<count($gallery_list))
					{
						$catalogresult.='
						<tr style="border:none;"><td colspan="'.$number_of_columns.'" style="border:none;" ><hr'.($theme_row->linestyle!='' ? ' style="'.$theme_row->linestyle.'" ' : '').' /></td></tr>';
					}

					$tr	=0;
				}
				$count++;
			}
			else
			{
				$aLinkURL='';

				if($theme_row->openinnewwindow==4 or $theme_row->openinnewwindow==5)
				{
					$aLink='javascript:youtubeplayer'.$videolist_row->id.'.HotVideoSwitch(\''.$videolist_row->id.'\',\''.$listitem['videoid'].'\',\''.$listitem['videosource'].'\','.$listitem['id'].')';
				}
				else
					$aLink=YoutubeGalleryLayoutRenderer::makeLink($listitem, $theme_row->rel, $aLinkURL, $videolist_row->id, $theme_row->id,$custom_itemid);

				$isForShadowBox=false;

				if(isset($theme_row))
				{
					if($theme_row->rel!='')
						$isForShadowBox=true;
				}

				if($isForShadowBox and $theme_row->rel!='' and $theme_row->openinnewwindow!=4 and $theme_row->openinnewwindow!=5)
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

					$aHrefLink='<a href="'.$aLink.'"'
						.($theme_row->rel!='' ? ' rel="'.$theme_row->rel.'"' : '')
						.(($theme_row->openinnewwindow==1 OR $theme_row->openinnewwindow==3) ? ' target="_blank"' : '')
						.'>';


				$thumbnail_item=YoutubeGalleryLayoutRenderer::renderThumbnailForNavBar($aHrefLink,$aLink,$videolist_row, $theme_row,$listitem, $videoid,$item_index,$gallery_list);


				if($thumbnail_item!='')
				{
					if($tr==0)
						$catalogresult.='<tr style="border:none;" >';

					$catalogresult.=
					'<td style="width:'.$column_width.';vertical-align:top;text-align:center;border:none;'.($bgcolor!='' ? ' background-color: #'.$bgcolor.';' : '').'">'
					.$thumbnail_item.'</td>';


					$tr++;
					if($tr==$number_of_columns)
					{
						$catalogresult.='
							</tr>
						';
						if($count+1<count($gallery_list))
							$catalogresult.='
							<tr style="border:none;"><td colspan="'.$number_of_columns.'" style="border:none;" ><hr'.($theme_row->linestyle!='' ? ' style="'.$theme_row->linestyle.'" ' : '').' /></td></tr>';

						$tr	=0;
					}
					$count++;
				}


			}
			$item_index++;
		}


	}

		if($tr>0)
				$catalogresult.='<td style="border:none;" colspan="'.($number_of_columns-$tr).'">&nbsp;</td></tr>';


       $catalogresult.='</tbody>

    </table>

	';
		return $catalogresult;
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
			$result=YoutubeGalleryLayoutRenderer::renderThumbnailLayout($theme_row->customnavlayout,$listitem,$aHrefLink,$aLink, $videoid,$theme_row,$item_index,$gallery_list,$videolist_row);
		}
		else
		{
			$thumbnail_layout='[a][image][/a]'; //with link

			if($theme_row->showtitle)
			{
				if($thumbtitle!='')
					$thumbnail_layout.='<br/>'.($theme_row->thumbnailstyle=='' ? '<span style="font-size: 8pt;" >[title]</span>' : '<div style="'.$theme_row->thumbnailstyle.'">[title]</div>');
			}
			$result=YoutubeGalleryLayoutRenderer::renderThumbnailLayout($thumbnail_layout,		$listitem,$aHrefLink,$aLink, $videoid,$theme_row,$item_index,$gallery_list,$videolist_row);
		}

/*
			$result.='<div id="YoutubeGalleryThumbTitle'.$videolist_row->id.'_'.$listitem['id'].'" style="display:none;visibility:hidden;">'.$listitem['title'].'</div>';
			$result.='<div id="YoutubeGalleryThumbDescription'.$videolist_row->id.'_'.$listitem['id'].'" style="display:none;visibility:hidden;">'.$listitem['description'].'</div>';
			$result.='<div id="YoutubeGalleryThumbLink'.$videolist_row->id.'_'.$listitem['id'].'" style="display:none;visibility:hidden;">'.$listitem['link'].'</div>';
			$result.='<div id="YoutubeGalleryThumbStartSecond'.$videolist_row->id.'_'.$listitem['id'].'" style="display:none;visibility:hidden;">'.$listitem['startsecond'].'</div>';
			$result.='<div id="YoutubeGalleryThumbEndSecond'.$videolist_row->id.'_'.$listitem['id'].'" style="display:none;visibility:hidden;">'.$listitem['endsecond'].'</div>';

			if($listitem['custom_imageurl']!='' and strpos($listitem['custom_imageurl'],'#')===false)
				$result.='<div id="YoutubeGalleryThumbCustomImage'.$videolist_row->id.'_'.$listitem['id'].'" style="display:none;visibility:hidden;">'.$listitem['custom_imageurl'].'</div>';
				*/
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
			if($listitem['imageurl']=='flvthumbnail' and $listitem['custom_imageurl']=='')
			{
				if($as_tag)
				{
					require_once('flv.php');
					$linkTarget=(($theme_row->openinnewwindow==1 OR $theme_row->openinnewwindow==3) ? '_blank' : '_self');
					$imagetag=VideoSource_FLV::getThumbnailCode($listitem['link'], $theme_row->thumbnailstyle,$aLink,$linkTarget);
				}
				else
					$imagetag='';
			}
			else
			{
				if($listitem['imageurl']=='flvthumbnail' and $listitem['custom_imageurl']!='')
				{
					$imagelink = $listitem['custom_imageurl'];
				}
				else
				{
					$images=explode(',',$listitem['imageurl']);
					$index=0;
					if($options!='')
					{
						$index=(int)$options;
						if($index<0)
							$index=0;
						if($index>=count($images))
							$index=count($images)-1;
						$imagelink= $images[$index];
					}
					else
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
					$imagelink= $images[$index];
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
					$curPageUrl=YoutubeGalleryLayoutRenderer::curPageURL();

					$imagelink=(strpos($imagelink,'http://')===false and strpos($imagelink,'https://')===false  ? $curPageUrl.'/' : '').$imagelink;

					$document->addCustomTag('<link rel="image_src" href="'.$imagelink.'" />'); //all thumbnails
				}

			}
		}

		return $imagetag;
	}

	public static function renderThumbnailLayout($thumbnail_layout,$listitem,$aHrefLink,$aLink, $videoid,&$theme_row,$item_index,&$gallery_list,&$videolist_row)
	{
		$listitem=YouTubeGalleryData::updateSingleVideo($listitem);
		
		$fields=array('width','height','image','link','a','/a','link','title','description',
					  'imageurl','videoid','videosource','publisheddate','duration',
					  'rating_average','rating_max','rating_min','rating_numRaters',
					  'statistics_favoriteCount','viewcount','favcount','keywords','isactive','commentcount','likes','dislikes','channel','social',
					  'odd','even','videolist','inwatchgroup'
					  );


		$tableFields=array('title','description',
					  'imageurl','videoid','videosource','publisheddate','duration',
					  'rating_average','rating_max','rating_min','rating_numRaters',
					  'keywords','commentcount','likes','dislikes');


		foreach($fields as $fld)
		{

			$imageFound=(strlen($listitem['imageurl'])>0);// or strlen($listitem['custom_imageurl'])>0);

			$isEmpty=YoutubeGalleryLayoutRenderer::isThumbnailDataEmpty($fld,$listitem,$tableFields,$imageFound, $videoid, $item_index,$videolist_row);

			$ValueOptions=array();
			$ValueList=YoutubeGalleryLayoutRenderer::getListToReplace($fld,$ValueOptions,$thumbnail_layout,'[]');

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
					$vlu=YoutubeGalleryLayoutRenderer::getTumbnailData($fld, $aHrefLink, $aLink, $listitem, $tableFields,$options,$theme_row,$gallery_list,$videolist_row); //NEW
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

			case 'image':
				$vlu=YoutubeGalleryLayoutRenderer::PrepareImageTag($listitem,$options,$theme_row,true);
			break;

			case 'imageurl':
				$vlu=YoutubeGalleryLayoutRenderer::PrepareImageTag($listitem,$options,$theme_row,false);
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
						$vlu=YoutubeGalleryLayoutRenderer::PrepareDescription_($vlu, $words, $chars);
				}

			break;

			case 'description':


				$description= str_replace('"','&quot;',$listitem['description']);
				$description=YouTubeGalleryMisc::html2txt($description);

				//if($options!='')
				//{
					//$options=explode(',',$options);
					//$description=YoutubeGalleryLayoutRenderer::PrepareDescription($description, $options);
				//}
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
						$vlu= YoutubeGalleryLayoutRenderer::curPageURL(false).$aLink; //NEW
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
					$a=YoutubeGalleryLayoutRenderer::curPageURL(false);
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
				if($listitem[$tf]=='')
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

	public static function SetHeaderTags($videolist_row, $theme_row,$pl)
	{
		if(count($pl)==0)
			return;

		$parts=explode('*',$pl[0]);
		$videoid=$parts[0];
		$videosource=$parts[2];

		$VideoRow=YoutubeGalleryLayoutRenderer::getVideoRowByID($videoid,$gallery_list);
		if(!$VideoRow)
			return;

		$mydoc = JFactory::getDocument();

		if($theme_row->changepagetitle!=3)
		{
			$mainframe = JFactory::getApplication();
			$sitename =$mainframe->getCfg('sitename');


			$title=$VideoRow['title'];

			if($theme_row->changepagetitle==0)
				$mydoc->setTitle($title.' - '.$sitename);
			elseif($theme_row->changepagetitle==1)
				$mydoc->setTitle($sitename.' - '.$title);
			elseif($theme_row->changepagetitle==2)
				$mydoc->setTitle($title);
		}

		//add meta keywords
		
		if((int)$theme_row->prepareheadtags!=0 and $videosource=='youtube')
		{
			$mydoc->setMetaData( 'keywords', YouTubeGalleryMisc::html2txt($VideoRow['keywords']));//tags
			$description_=str_replace('*email*','@',YouTubeGalleryMisc::html2txt($VideoRow['description']));
			$mydoc->setMetaData( 'description', $description_);

		}

						//if(!$VideoRow)
					///return '';

				//if($videosource!='')
				//	$vs=$videosource;
			//	else
		//			$vs=$row['videosource'];

		$image_link=$VideoRow['imageurl'];
		//$startsecond=$VideoRow['startsecond'];
		//$endsecond=$VideoRow['endsecond'];


		if($theme_row->prepareheadtags==2 or $theme_row->prepareheadtags==3)
		{

				if($image_link!='' and strpos($image_link,'#')===false)
				{

					$curPageUrl=YoutubeGalleryLayoutRenderer::curPageURL();

					$image_link_array=explode(',',$image_link);
					if(count($image_link_array)>=3)
						$imagelink=$image_link_array[3];
					else
						$imagelink=$image_link_array[0];


					$imagelink=(strpos($imagelink,'http://')===false and strpos($image_link,'https://')===false  ? $curPageUrl.'/' : '').$imagelink;

					if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
						$imagelink=str_replace('http://','https://',$imagelink);

					$mydoc->addCustomTag('<link rel="image_src" href="'.$imagelink.'" /><!-- active -->');
				}


		}

	}

	
	public static function addHotReloadScript(&$gallery_list,$width,$height,&$videolist_row, &$theme_row)
	{

			$vs=array();//'youtube','vimeo','break','own3dtvlive','own3dtvvideo','google','yahoo','collegehumor','dailymotion','.flv','presentme');
			foreach($gallery_list as $g)
			{

				$v=$g['videosource'];


				if(!in_array($v,$vs))
					$vs[]=$v;
			}

			$document = JFactory::getDocument();
			$document->addScript(JURI::root(true).'/components/com_youtubegallery/js/player.js');
						
			$autoplay=((int)$theme_row->autoplay==1 ? 'true' : 'false');
			
			$allowplaylist=((int)$theme_row->allowplaylist==1 or $theme_row->repeat==1 ? 'true' : 'false'); //to loop video or to play the next one


			$playerapiid='ygplayerapiid_'.$videolist_row->id;
			$initial_volume=(int)$theme_row->volume;




		$pl=YoutubeGalleryLayoutRenderer::getPlaylistIdsOnly($gallery_list,'','',true,true);//(bool)$theme_row->allowplaylist

			$hotrefreshscript='


var youtubeplayer'.$videolist_row->id.' = new YoutubeGalleryPlayerObject('
			.$width.','
			.$height.','
			.'"'.$playerapiid.'",'
			.$initial_volume.','
			.$theme_row->muteonplay.','
			.$autoplay.','
			.$allowplaylist.');


	function onYouTubeIframeAPIReady () {
		
		youtubeplayer'.$videolist_row->id.'.iframeAPIloaded=true;
	}

	youtubeplayer'.$videolist_row->id.'.videolistid="'.$videolist_row->id.'";


	youtubeplayer'.$videolist_row->id.'.VideoSources=["'.implode('", "',$vs).'"];

	youtubeplayer'.$videolist_row->id.'.openinnewwindow="'.$theme_row->openinnewwindow.'";
	youtubeplayer'.$videolist_row->id.'.PlayList="'.implode(',',$pl).'".split(",");
';


		YoutubeGalleryLayoutRenderer::SetHeaderTags($videolist_row, $theme_row,$pl);

			$document->addScriptDeclaration($hotrefreshscript);
			$hotrefreshscript='';
			$i=0;

			foreach($vs as $v)
			{
				$player_code='<!-- '.$v.' player -->'.YouTubeGalleryPlayers::ShowActiveVideo($gallery_list,$width,$height,'****youtubegallery-video-id****', $videolist_row, $theme_row,$v);
				$hotrefreshscript.='
	youtubeplayer'.$videolist_row->id.'.Player['.$i.']=\''.$player_code.'\';';
				$i++;
			}

			$hotrefreshscript.='

	for (var i=0;i<youtubeplayer'.$videolist_row->id.'.Player.length;i++)
	{
		var player_code=youtubeplayer'.$videolist_row->id.'.Player[i];
		';
		$hotrefreshscript.='
		player_code=player_code.replace(\'_quote_\',\'\\\'\');
		youtubeplayer'.$videolist_row->id.'.Player[i]=player_code;
	}
';//</script>

		if($theme_row->playvideo==1)
		{
			$videoid=JFactory::getApplication()->input->getCmd('videoid');


			$hotrefreshscript.='
			youtubeplayer'.$videolist_row->id.'.CurrentVideoID="'.$videoid.'";
			window.addEventListener( "load", function( event ) {

';
		if($videoid=='')
		{
			$hotrefreshscript.='
			
			setTimeout(youtubeplayer'.$videolist_row->id.'.FindNextVideo(), 500);
';
		}
		else
		{
			$hotrefreshscript.='
			setTimeout(youtubeplayer'.$videolist_row->id.'.FindCurrentVideo(), 500);
';
		}

$hotrefreshscript.='
		});
';
		}

		$document->addScriptDeclaration($hotrefreshscript);
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

	public static function getVideoRowByID($videoid,&$gallery_list,$asArray=false)
	{
		if($videoid=='' or $videoid=='****youtubegallery-video-id****')
		{
			if($asArray)
				return array();
			else
				return false;
		}

		if(isset($gallery_list) and count($gallery_list)>0)
		{

			foreach($gallery_list as $gl_row)
			{
				if($gl_row['videoid']==$videoid)
					return $gl_row;
			}
		}

		//Check DB
		$db = JFactory::getDBO();

		$query = 'SELECT * FROM #__youtubegallery_videos WHERE videoid="'.$videoid.'" LIMIT 1';

		$db->setQuery($query);
		if (!$db->query())    die( $db->stderr());
		$values=$db->loadAssocList();



		if(count($values)==0)
		{
			if($asArray)
				return array();
			else
				return false;
		}
		else
			return $values[0];


	}

	public static function getTitleByVideoID($videoid,&$gallery_list)
	{
		$gl_row=YoutubeGalleryLayoutRenderer::getVideoRowByID($videoid,$gallery_list);
		if($gl_row)
			return $gl_row['title'];

		return '';

	}

	public static function getThumbnailByID($videoid,&$gallery_list)
	{
		$gl_row=YoutubeGalleryLayoutRenderer::getVideoRowByID($videoid,$gallery_list);
		if($gl_row)
			return $gl_row['imageurl'];

		return '';
	}

	public static function getVideoSourceByID($videoid,&$gallery_list)
	{
		$gl_row=YoutubeGalleryLayoutRenderer::getVideoRowByID($videoid,$gallery_list);
		if($gl_row)
			return $gl_row['videosource'];

		return '';
	}

	public static function PrepareDescription($description, $videodescription_params)
	{
		if(count($videodescription_params)>0)
		{
					$words=(int)$videodescription_params[0];
					if(isset($videodescription_params[1]))
						$chars=(int)$videodescription_params[1];
					else
						$chars=0;

					if($words!=0 or $chars!=0)
						$description=YoutubeGalleryLayoutRenderer::PrepareDescription_($description, $words, $chars);

					if(isset($videodescription_params[2]) and $videodescription_params[2]=='addlinebreaks')
					{
						$description=nl2br($description);
						$description=str_replace('<br />','_thelinebreak_',$description);
					}
		}

		$description=str_replace('&quot;','_quote_',$description);
		$description=str_replace('@','_email_',$description);


		return $description;
	}

	protected static function PrepareDescription_($desc, $words, $chars)
	{
		if($chars==0 and $words>0)
		{
			preg_match('/([^\\s]*(?>\\s+|$)){0,'.$words.'}/', $desc, $matches);
			$desc=trim($matches[0]);
		}
		else
		{
			if(strlen($desc)>$chars)
			$desc=substr($desc,0,$chars);
		}

		$desc=str_replace("/n"," ",$desc);
		$desc=str_replace("/r"," ",$desc);

		$desc=trim(preg_replace('/\s\s+/', ' ', $desc));

		$desc=trim($desc);

		return $desc;
	}


	public static function SocialButtons($link,$prefix,$params,$videolist_row_id,$videoid)
	{
		$pair=explode(',',$params);

		$w=80;
		if(isset($pair[2]))
			$w=(int)$pair[2];

		switch($pair[0])
		{
			case 'facebook_comments':

						$head_result='

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=6245995.0.07869";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));

document.write(\'<div id="fb-root"></div>\');
</script>
';

	$document = JFactory::getDocument();
	$document->addCustomTag($head_result);


						$numposts='3';
						if(isset($pair[1]))
							$numposts=(int)$pair[1];

						$width='';//style="width:auto !important;"';
						if(isset($pair[2]))
							$width='data-width="'.(int)$pair[2].'px"';

						$colorscheme='light';
						if(isset($pair[3]))
							$colorscheme=$pair[3];

						if($link=='' or $link='window.location.href')
							$link=YouTubeGalleryMisc::full_url($_SERVER);

						$result='<div class="fb-comments" data-href="'.$link.'" data-num-posts="'.$numposts.'" '.$width.' data-colorscheme="'.$colorscheme.'"></div>';



						return $result;
			break;
			//------------------------------------------------------------------------------------------------------------
			case 'facebook_share':

						$bName='Share Link';
						if(isset($pair[1]))
							$bName=$pair[1];



					$dName=$prefix.'fbshare_'.$videolist_row_id.'x'.$videoid;
					$tStyle='width:'.$w.'px;height:20px;border: 1px #29447e solid;background-color:#5972a7;color:white;font-size:12px;font-weight:bold;text-align:center;position:relative;';
					$tStyle2='border-top:#8a9cc2 1px solid;width:'.($w-2).'px;height:18px;padding:0px;font-decoration:none;';
					$result ='
	<div id="'.$dName.'"></div>
	<script>
		var theURL=escape('.$link.');

		var fbobj=document.getElementById("'.$dName.'");
		var sBody=\'<a href="https://www.facebook.com/sharer/sharer.php?u=\'+theURL+\'" target="_blank" style="color:white;"><div style="'.$tStyle.'"><div style="'.$tStyle2.'">'.$bName.'</div>\';
		sBody+=\'<div style="position:absolute;bottom:0;left:0;margin-bottom:-2px;width:'.$w.'px;height:1px;border-bottom:1px solid #e5e5e5;"></div>\';
		sBody+=\'</div></a>\';
	        fbobj.innerHTML = sBody;
	</script>
	';
			return $result;
			break;
			//------------------------------------------------------------------------------------------------------------
			case 'facebook_like':

						$FBLanguage='';
						if(isset($pair[1]))
							$FBLanguage=$pair[1];

					$dName=$prefix.'fblike_'.$videolist_row_id.'x'.$videoid;
					$result ='
	<div id="'.$dName.'" style="width:'.$w.'px;"></div>
	<script>
		var theURL=escape('.$link.');
		var fbobj=document.getElementById("'.$dName.'");
		var sBody=\'<iframe src="http://www.facebook.com/plugins/like.php?href=\';
		sBody+=theURL;
		sBody+=\'&layout=button_count&locale='.$FBLanguage.'&show_faces=false&action=like&font=tahoma&colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; height:20px" ></iframe>\';
	        fbobj.innerHTML = sBody;
	</script>
	';
					return $result;
			break;
			//------------------------------------------------------------------------------------------------------------
			case 'twitter':

					$TwitterAccount='';//"YoutubeGallery";
					if(isset($pair[1]))
						$TwitterAccount=$pair[1];
					else
						return '<p style="color:white;background-color:red;">Set Twitter Account.<br/>Example: [social:twitter,JoomlaBoat]</p>';

					$dName=$prefix.'witter_'.$videolist_row_id.'x'.$videoid;
					$result ='
	<div id="'.$dName.'" style="width:'.$w.'px;"></div>
	<script>
		var theURL=escape('.$link.');
		var twobj=document.getElementById("'.$dName.'");
		var TwBody=\'<a href="https://twitter.com/share" class="twitter-share-button" data-url="\'+theURL+\'" data-via="'.$TwitterAccount.'" data-hashtags="\'+theURL+\'">Tweet</a>\';
		twobj.innerHTML = TwBody;
		!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
	</script>
	';
					return $result;
			break;
		}
	}
}
