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

class YouTubeGalleryDB
{
	var $videolist_row;
	var $theme_row;

	function getVideoListTableRow($listid)
	{
		$db = JFactory::getDBO();

		//Load Video List

		$query = 'SELECT ';
		$query .= 'l.id AS id, ';
		$query .= 'l.es_listname AS es_listname, ';
		$query .= 'l.es_videolist AS es_videolist, ';
		$query .= 'l.es_catid AS es_catid, ';
		$query .= 'l.es_updateperiod AS es_updateperiod, ';
		$query .= 'l.es_lastplaylistupdate AS es_lastplaylistupdate, ';
		$query .= 'l.es_description AS es_description, ';
		//$query .= '#__youtubegallery_videolists.author AS author, ';
		$query .= 'l.es_authorurl AS es_authorurl, ';
		$query .= 'l.es_image AS es_image, ';
		$query .= 'l.es_note AS es_note, ';
		$query .= 'l.es_watchusergroup AS es_watchusergroup, ';

		$query .= '(SELECT COUNT(v.id) FROM #__customtables_table_youtubegalleryvideos AS v WHERE v.es_videolist=l.id AND v.es_isvideo LIMIT 1) AS TotalVideos ';

		$query .= 'FROM #__customtables_table_youtubegalleryvideolists AS l';
		//$query .= 'COUNT(v.es_videolist) AS TotalVideos FROM #__customtables_table_youtubegalleryvideolists AS l';
		

		//$query .= ' LEFT JOIN #__customtables_table_youtubegalleryvideos AS v ON v.es_videolist=l.id AND v.es_isvideo';
		$query .= ' WHERE l.id='.(int)$listid.' ';
		$query .= ' GROUP BY l.id';
		$query .= ' LIMIT 1';

		$db->setQuery($query);

		$videolist_rows = $db->loadObjectList();


		if(count($videolist_rows)==0)
			return false;//'<p>No video list found</p>';

		$this->videolist_row=$videolist_rows[0];
		return true;
	}

	function getThemeTableRow($themeid)
	{
		$db = JFactory::getDBO();

		//Load Theme Row
		//, es_showinfo, es_cols,es_showtitle, , es_linestyle, es_showlistname, es_showactivevideotitle,es_description, es_descr_position,, es_pagination , es_readonly,es_activevideotitlestyle,
		$query = 'SELECT id, es_themename, es_width, es_height, es_playvideo, es_repeat, es_fullscreen, es_autoplay, es_related, es_allowplaylist, es_bgcolor,
		es_cssstyle, es_navbarstyle, es_thumbnailstyle, es_listnamestyle,
		es_descrstyle, es_colorone, es_colortwo, es_border, es_openinnewwindow, es_rel, es_hrefaddon, es_customlimit,
		es_controls, es_youtubeparams, es_useglass, es_logocover, es_customlayout,  es_prepareheadtags, es_muteonplay,
		es_volume, es_orderby, es_customnavlayout, es_responsive, es_mediafolder, es_headscript, es_themedescription, es_nocookie, es_changepagetitle
		FROM #__customtables_table_youtubegallerythemes WHERE id='.(int)$themeid.' LIMIT 1';
		//es_playertype,

		$db->setQuery($query);

		$theme_rows = $db->loadObjectList();

		if(count($theme_rows)==0)
			return false;//'<p>No theme found</p>';

		$this->theme_row=$theme_rows[0];
		return true;
	}

	static public function getRawData($videoid)
	{

		$db = JFactory::getDBO();

		$query = 'SELECT es_rawdata FROM #__customtables_table_youtubegalleryvideos WHERE es_videoid='.$db->quote($videoid).' LIMIT 1';

		$db->setQuery($query);

		$values=$db->loadAssocList();

		if(count($values)==0)
			return "";

		$v=$values[0];

		return $v['es_rawdata'];
	}

	static public function setDelayedRequest($videoid,$link)
	{
		if($videoid!='')
		{
			$value='*youtubegallery_request*';//.$link;//md5(mt_rand());

			$db = JFactory::getDBO();

			$query = 'UPDATE #__customtables_table_youtubegalleryvideos SET '.$db->quoteName('es_rawdata').'='.$db->quote($value).' WHERE '.$db->quoteName('es_videoid').'='.$db->quote($videoid);

			$db->setQuery($query);
			$db->execute();
		}

	}

	static public function setRawData($videoid,$videoData)
	{
		if($videoid!='')
		{
			$db = JFactory::getDBO();
			$query = 'UPDATE #__customtables_table_youtubegalleryvideos SET '.$db->quoteName('es_rawdata').'='.$db->quote($videoData).' WHERE '.$db->quoteName('es_videoid').'='.$db->quote($videoid);
			$db->setQuery($query);
			$db->execute();
		}
	}

	protected static function isVideo_record_exist($videosource,$listid,$videoid)
	{
		$db = JFactory::getDBO();

		$query = 'SELECT id, es_allowupdates FROM #__customtables_table_youtubegalleryvideos WHERE es_videolist='.(int)$listid.' AND '.$db->quoteName('es_videosource').'='.$db->quote($videosource).' AND '.$db->quoteName('es_videoid').'='.$db->quote($videoid).' LIMIT 1';

		$db->setQuery($query);
		$db->execute();

		$videos_rows=$db->loadAssocList();

		if(count($videos_rows)==0)
			return 0;

		$videos_row=$videos_rows[0];

		if($videos_row['es_allowupdates']!=1)
			return -1; //Updates disable

		return $videos_row['id'];
	}

	public static function Playlist_lastupdate($theLink)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT es_lastupdate FROM #__customtables_table_youtubegalleryvideos WHERE es_link='.$db->quote($theLink).' LIMIT 1';

		$db->setQuery($query);

		$videos_rows=$db->loadAssocList();
				
		if(count($videos_rows))
			return $videos_rows[0]['es_lastupdate'];
				
		return 0;
	}

	function getVideoList_FromCache_From_Table(&$videoid,&$total_number_of_rows,$get_the_first_one=false)
	{
		$listIDs=array();
		$listIDs[]=$this->videolist_row->id;

		$db = JFactory::getDBO();

		$where=array();

		$where[]='!INSTR(es_title,"***Video not found***")';
		$where[]=$db->quoteName('es_videolist').'='.$db->quote($this->videolist_row->id);
		$where[]='es_isvideo=0';
		$where[]='es_videosource="videolist"';


		$query = 'SELECT es_videoid FROM #__customtables_table_youtubegalleryvideos WHERE '.implode(' AND ', $where);

		$db->setQuery($query);

		$videos_lists=$db->loadAssocList();
		
		if(count($videos_lists)>0)
		{
			foreach($videos_lists as $v)
			{
				if($v['es_videoid']==-1)
				{
					//all videos
					$listIDs=array();
					break;
				}
				elseif(!(strpos($v['es_videoid'],'catid=')===false))
				{
					//Video Lists of selected category by id

					$catid=intval(str_replace('catid=','',$v['es_videoid']));

					$query = 'SELECT id FROM #__customtables_table_youtubegalleryvideolists WHERE es_catid='.$catid;
					$db->setQuery($query);

					$videos_lists_=$db->loadAssocList();

					foreach($videos_lists_ as $vl)
						$listIDs[]=$vl['id'];
				}
				elseif(!(strpos($v['es_videoid'],'category=')===false))
				{
					//Video Lists of selected category by id

					$categoryname=str_replace('category=','',$v['es_videoid']);

					$query = 'SELECT l.id AS computedcatid FROM #__customtables_table_youtubegalleryvideolists AS l
					INNER JOIN #__customtables_table_youtubegallerycategories AS c ON c.id=l.es_catid
					WHERE c.es_categoryname='.$db->quote($categoryname);

					$db->setQuery($query);
					$videos_lists_=$db->loadAssocList();

					foreach($videos_lists_ as $vl)
						$listIDs[]=$vl['computedcatid'];
				}
				else
					$listIDs[]=$v['es_videoid'];
			}
		}
		return $this->getVideoList_FromCacheFromTable($videoid,$total_number_of_rows,$listIDs,$get_the_first_one);
	}

	function addSearchQuery()
	{
		//Input value sanitazed below
		$search_fields=JFactory::getApplication()->input->getVar('ygsearchfields');
		if($search_fields!='')
		{
			$search_query=str_replace('"','',JFactory::getApplication()->input->getVar('ygsearchquery'));
			$search_query=str_replace(' ',',',$search_query);
			$search_query_array=explode(',',$search_query);

			$search_fields_array=explode(',',$search_fields);
			$possible_fields=array('es_videoid','es_title','es_description','es_publisheddate','es_keywords','es_channelusername'
				,'es_channeltitle','es_channeldescription');

			$q_where=array();
			foreach($search_query_array as $q)
			{
				if($q!='')
				{
					$f_where=array();
					foreach($search_fields_array as $f)
					{
						if(in_array($f,$possible_fields))
							$f_where[]='INSTR('.$f.',"'.$q.'")';
					}//f

					if(count($f_where)==1)
						$q_where[]=implode(' OR ',$f_where);
					elseif(count($f_where)>1)
						$q_where[]='('.implode(' OR ',$f_where).')';
				}
			}//q

			if(count($q_where)==1)
				return implode(' AND ',$q_where);
			elseif(count($q_where)>1)
				return '('.implode(' AND ',$q_where).')';
			else
				return '';
		}
	}
	
	protected static function checkIfLatLongAltFieldsExists()
	{
		$db = JFactory::getDBO();
		$query = "SHOW COLUMNS FROM #__customtables_table_youtubegalleryvideos";//SELECT * FROM #__youtubegallery_videos LIMIT 1";
		$db->setQuery( $query );
		$all = $db->loadAssocList();
		
		$fields=array();
		foreach($all as $key){
			if(!in_array($key['Field'],$fields))
				$fields[]=$key['Field'];
		}
		
		if (!in_array("es_latitude", $fields)) {
			$db->setQuery("ALTER TABLE #__customtables_table_youtubegalleryvideos ADD es_latitude decimal(20,7) NULL DEFAULT NULL");
			$db->execute();
		}
		
		if (!in_array("es_longitude", $fields)) {
				$db->setQuery("ALTER TABLE #__customtables_table_youtubegalleryvideos ADD es_longitude decimal(20,7) NULL DEFAULT NULL");
				$db->execute();
			}
		
		if (!in_array("es_altitude", $fields)) {
			$db->setQuery("ALTER TABLE #__customtables_table_youtubegalleryvideos ADD es_altitude int NULL DEFAULT NULL");
			$db->execute();
		}
	}
	
	function getVideoList_FromCacheFromTable(&$videoid,&$total_number_of_rows,&$listIDs,$get_the_first_one=false)
	{
		$jinput=JFactory::getApplication()->input;
		
		$db = JFactory::getDBO();
		$where=array();

		//Only for search module
		$wq=$this->addSearchQuery();
		if($wq!='')
			$where[]=$wq;

		if(count($listIDs)>0)
		{
			$w=array();
			foreach($listIDs as $l)
			{
				$w[]=$db->quoteName('es_videolist').'='.(int)$l;//$db->quote($l);
			}
			$where[]='('.implode(' OR ',$w).')';
		}

		$where[]='es_isvideo=1';

		if($this->theme_row->es_rel!='' and JFactory::getApplication()->input->getCmd('tmpl')=='component')
		{
			// Get only one video - current video. and shadow box
			$where[]=$db->quoteName('es_videoid').'='.$db->quote($videoid);
			$limitstart=0;
			$limit=1;
		}
		
		if($this->theme_row->es_orderby!='')
		{
			if($this->theme_row->es_orderby=='randomization')
				$orderby='RAND()';
			else
				$orderby='es_'.$this->theme_row->es_orderby;
		}
		else
			$orderby='es_ordering';
		
		if($get_the_first_one)
		{
			// Get only one video - the first video.
			$limitstart=0;
			$limit=1;
		}
		else
		{
			if($jinput->getInt('yg_api')==1 and $orderby=='RAND()')
			{
				$limit=0; // UNLIMITED
				$limitstart = 0;
			}
			else
			{
				if(((int)$this->theme_row->es_customlimit)==0)
					$limit=0; // UNLIMITED
				else
					$limit = (int)$this->theme_row->es_customlimit;

				$limitstart = $jinput->getInt('ygstart', 0);
			}
		}

		$query = 'SELECT *,IF(es_customtitle!="", es_customtitle, es_title) AS es_title, IF(es_customdescription!="", es_customdescription, es_description) AS es_description'
			.' FROM #__customtables_table_youtubegalleryvideos WHERE '.implode(' AND ', $where).' ORDER BY '.$orderby;// GROUP BY videoid 
		
		$db->setQuery($query);
		$db->execute();
		$total_number_of_rows = $db->getNumRows();

		if($limitstart>$total_number_of_rows)
			$limitstart=0;

		if($limit==0)
			$db->setQuery($query);
		else
			$db->setQuery($query, $limitstart, $limit);

		$videos_rows=$db->loadAssocList();
		
		$firstvideo='';

		if(count($videos_rows)>0)//$firstvideo=='' and 
		{
			$videos_row=$videos_rows[0];
			$firstvideo=$videos_row['es_videoid'];
		}
		
		if($videoid == '')
		{
			if($firstvideo!='')
				$videoid=$firstvideo;
		}

		return $videos_rows;
	}

	function update_playlist($force_update = false)
	{
		$start  = strtotime( $this->videolist_row->es_lastplaylistupdate );
		$end    = strtotime( date( 'Y-m-d H:i:s') );
		$days_diff = ($end-$start)/86400;

		$updateperiod=(float)$this->videolist_row->es_updateperiod;
		if($updateperiod==0)
			$updateperiod=1;

		if($days_diff>abs($updateperiod) or $force_update)
		{
			YouTubeGalleryDB::update_cache_table($this->videolist_row,true);
			$this->videolist_row->es_lastplaylistupdate =date( 'Y-m-d H:i:s');

			$db = JFactory::getDBO();
			$query = 'UPDATE #__customtables_table_youtubegalleryvideolists SET '.$db->quoteName('es_lastplaylistupdate').'='.$db->quote($this->videolist_row->es_lastplaylistupdate)
				.' WHERE id='.(int)$this->videolist_row->id;
				
			$db->setQuery($query);
			$db->execute();
		}
	}

	public static function update_cache_table(&$videolist_row,$update_videolist=false)
	{
		$videolist_array=Helper::csv_explode("\n", $videolist_row->es_videolist, '"', true);

		$firstvideo='';
		$videolist=YouTubeGalleryData::formVideoList($videolist_row,$videolist_array, $firstvideo, '',$update_videolist);//$this->theme_row->thumbnailstyle);

		$db = JFactory::getDBO();
		$parent_id=null;

		$ListOfVideosNotToDelete=array();
		
		foreach($videolist as $g)
		{
			if(isset($g['es_videoid']))
			{
				$ListOfVideosNotToDelete[]='!(es_videoid='.$db->quote($g['es_videoid']).' AND es_videosource='.$db->quote($g['es_videosource']).')';
				YouTubeGalleryDB::updateDBSingleItem($g,(int)$videolist_row->id,$parent_id);
			}
		}
		
		//Delete All videos of this video list that has been deleted form the list and allowed for updates.

		$query='DELETE FROM #__customtables_table_youtubegalleryvideos WHERE es_videolist='.(int)$videolist_row->id;
		if(count($ListOfVideosNotToDelete)>0)
			$query.=' AND '.implode(' AND ',$ListOfVideosNotToDelete);
					
		$db->setQuery($query);
		$db->execute();
	}
	
	public static function updateDBSingleItem($g,$videolist_id,&$parent_id)
	{
		YouTubeGalleryDB::checkIfLatLongAltFieldsExists();
		
		$db = JFactory::getDBO();
		$fields=YouTubeGalleryDB::prepareQuerySets($g,$videolist_id,$parent_id);
		$record_id=YouTubeGalleryDB::isVideo_record_exist($g['es_videosource'],$videolist_id,$g['es_videoid']);

		$query='';

		if($record_id==0)
		{
			$query='INSERT #__customtables_table_youtubegalleryvideos SET '.implode(', ', $fields).', es_allowupdates=1';

			$db->setQuery($query);
			$db->execute();
								
			$record_id_new = $db->insertid();

			$ListOfVideos[]=$record_id_new;

			if((int)$g['es_isvideo']==0)
				$parent_id=$record_id_new;
		}
		elseif($record_id>0)
		{
			$query="UPDATE #__customtables_table_youtubegalleryvideos SET ".implode(', ', $fields).' WHERE id='.$record_id;

			$db->setQuery($query);
			$db->execute();

			$ListOfVideos[]=$record_id;

			if((int)$g['es_isvideo']==0)
				$parent_id = $record_id; //set the parent ID for video records
		}
	}

	protected static function prepareQuerySets($g,$videolist_id,&$parent_id)
	{
		$db = JFactory::getDBO();
		
		$g_title=str_replace('"','&quot;',$g['es_title']);
		$g_description=str_replace('"','&quot;',$g['es_description']);

		if(isset($g['es_customtitle']))
			$custom_g_title=str_replace('"','&quot;',$g['es_customtitle']);
		else
			$custom_g_title='';

		if(isset($g['es_customdescription']))
			$custom_g_description=str_replace('"','&quot;',$g['es_customdescription']);
		else
			$custom_g_description='';
		
		$fields=array();

		if($videolist_id!=0)
			$fields[]=$db->quoteName('es_videolist').'='.$db->quote($videolist_id);

		if((int)$g['es_isvideo']==0)
			$parent_id=null;
						
		if($parent_id!=null)
			$fields[]=$db->quoteName('es_parentid').'='.(int)$parent_id;

		$fields[]=$db->quoteName('es_videosource').'='.$db->quote($g['es_videosource']);

		$fields[]=$db->quoteName('es_videoid').'='.$db->quote($g['es_videoid']);

		if(isset($g['es_datalink']))
			$fields[]=$db->quoteName('es_datalink').'='.$db->quote($g['es_datalink']);

		if($g['es_imageurl']!='')
			$fields[]=$db->quoteName('es_imageurl').'='.$db->quote($g['es_imageurl']);

		if($g['es_title']!='')
			$fields[]=$db->quoteName('es_title').'='.$db->quote($g_title);

		if($g['es_description']!='')
			$fields[]=$db->quoteName('es_description').'='.$db->quote($g_description);

		if(isset($g['es_customimageurl']))
			$fields[]=$db->quoteName('es_customimageurl').'='.$db->quote($g['es_customimageurl']);
		else
			$fields[]=$db->quoteName('es_customimageurl').'=""';

		if($g['es_title']!='')
			$fields[]=$db->quoteName('es_alias').'='.$db->quote(YouTubeGalleryDB::get_alias($g_title,$g['es_videoid']));

		$fields[]=$db->quoteName('es_customtitle').'='.$db->quote($custom_g_title);
		$fields[]=$db->quoteName('es_customdescription').'='.$db->quote($custom_g_description);

		if(isset($g['es_specialparams']))
			$fields[]=$db->quoteName('es_specialparams').'='.$db->quote($g['es_specialparams']);
		else
			$fields[]=$db->quoteName('es_specialparams').'=""';

		if(isset($g['es_startsecond']))
			$fields[]=$db->quoteName('es_startsecond').'='.(int)$g['es_startsecond'];
		else
			$fields[]=$db->quoteName('es_startsecond').'=0';

		if(isset($g['es_endsecond']))
			$fields[]=$db->quoteName('es_endsecond').'='.(int)$g['es_endsecond'];
		else
			$fields[]=$db->quoteName('es_endsecond').'=0';

		$fields[]=$db->quoteName('es_link').'='.$db->quote($g['es_link']);

		$fields[]=$db->quoteName('es_isvideo').'='.(int)$g['es_isvideo'];//$db->quote(($this_is_a_list ? '0' : '1'));

		if(isset($g['es_publisheddate']) and $g['es_publisheddate']!='')
		{
			$publisheddate = date('Y-m-d H:i:s', strtotime($g['es_publisheddate']));
			$fields[]=$db->quoteName('es_publisheddate').'='.$db->quote($publisheddate);
		}

		if(isset($g['es_duration']))
		{
			$fields[]=$db->quoteName('es_duration').'='.(int)$g['es_duration'];
			$fields[]=$db->quoteName('es_lastupdate').'=NOW()';
		}

		if(isset($g['es_ratingaverage']))
			$fields[]=$db->quoteName('es_ratingaverage').'='.(float)$g['es_ratingaverage'];

		if(isset($g['es_ratingmax']))
			$fields[]=$db->quoteName('es_ratingmax').'='.(int)$g['es_ratingmax'];

		if(isset($g['es_ratingmin']))
			$fields[]=$db->quoteName('es_ratingmin').'='.(int)$g['es_ratingmin'];

		if(isset($g['es_ratingnumberofraters']))
			$fields[]=$db->quoteName('es_ratingnumberofraters').'='.(int)$g['es_ratingnumberofraters'];

		if(isset($g['es_statisticsfavoritecount']))
			$fields[]=$db->quoteName('es_statisticsfavoritecount').'='.(int)$g['es_statisticsfavoritecount'];

		if(isset($g['es_statisticsviewcount']))
			$fields[]=$db->quoteName('es_statisticsviewcount').'='.(int)$g['es_statisticsviewcount'];

		if(isset($g['es_keywords']))
		{
			if(is_array($g['es_keywords']))
			{
				$key_words=implode(',',$g['es_keywords']);
				$fields[]=$db->quoteName('es_keywords').'='.$db->quote($key_words);
			}
			else
				$key_words='';
		}

		if(isset($g['es_likes']))
			$fields[]=$db->quoteName('es_likes').'='.(int)$g['es_likes'];

		if(isset($g['es_dislikes']))
			$fields[]=$db->quoteName('es_dislikes').'='.(int)$g['es_dislikes'];

		if(isset($g['es_channelusername']))
			$fields[]=$db->quoteName('es_channelusername').'='.$db->quote($g['es_channelusername']);

		if(isset($g['es_channeltitle']))
			$fields[]=$db->quoteName('es_channeltitle').'='.$db->quote($g['es_channeltitle']);

		if(isset($g['es_channelsubscribers']))
			$fields[]=$db->quoteName('es_channelsubscribers').'='.(int)$g['es_channelsubscribers'];

		if(isset($g['es_channelsubscribed']))
			$fields[]=$db->quoteName('es_channelsubscribed').'='.(int)$g['es_channelsubscribed'];

		if(isset($g['es_channellocation']))
			$fields[]=$db->quoteName('es_channellocation').'='.$db->quote($g['es_channellocation']);

		if(isset($g['es_channelcommentcount']))
			$fields[]=$db->quoteName('es_channelcommentcount').'='.(int)$g['es_channelcommentcount'];

		if(isset($g['es_channelviewcount']))
			$fields[]=$db->quoteName('es_channelviewcount').'='.(int)$g['es_channelviewcount'];

		if(isset($g['es_channelvideocount']))
			$fields[]=$db->quoteName('es_channelvideocount').'='.(int)$g['es_channelvideocount'];

		if(isset($g['es_channeldescription']))
			$fields[]=$db->quoteName('es_channeldescription').'='.$db->quote($g['es_channeldescription']);
						
		if(isset($g['es_latitude']))
			$fields[]=$db->quoteName('es_latitude').'='.(float)$g['es_latitude'];

		if(isset($g['es_longitude']))
			$fields[]=$db->quoteName('es_longitude').'='.(float)$g['es_longitude'];

		if(isset($g['es_altitude']))
			$fields[]=$db->quoteName('es_altitude').'='.(int)$g['es_altitude'];
						
		if(isset($g['es_ordering']))
			$fields[]=$db->quoteName('es_ordering').'='.(int)$g['es_ordering'];

		return $fields;
	}

	public static function getSettingValue($option)
	{
		$db = JFactory::getDBO();

		$query = 'SELECT '.$db->quoteName('es_value').' FROM #__customtables_table_youtubegallerysettings WHERE '.$db->quoteName('es_option').'='.$db->quote($option).' LIMIT 1';

		$db->setQuery($query);
		
		$values=$db->loadAssocList();

		$vlu="";
		if(count($values)>0)
		{
			$v=$values[0];
			$vlu=$v['es_value'];				
		}

		if($option=='joomlaboat_api_host' and $vlu=='')
			$vlu='https://joomlaboat.com/youtubegallery-api';

		return $vlu;
	}

	public static function get_alias($title,$videoid)
	{
		if($videoid!='')
		{
			$alias=Helper::slugify($title);

			if($alias!="")
			{
				$db = JFactory::getDBO();
				$db->setQuery('SELECT '.$db->quoteName('es_alias').' FROM #__customtables_table_youtubegalleryvideos WHERE '.$db->quoteName('es_alias').'='.$db->quote($alias));

				$rows = $db->loadObjectList();

			  	if(count($rows)>1)
					$alias.="_".$videoid;
			}
			else
				return $videoid;

			if($alias=='')
				return 'x-'.$videoid;
			else
				return $alias;
		}
		else
			return '-wrong video id-';
	}

	public static function getVideoIDbyAlias($alias)
	{
		$db = JFactory::getDBO();

		$db->setQuery('SELECT '.$db->quoteName('es_videoid').' FROM #__customtables_table_youtubegalleryvideos WHERE '.$db->quoteName('es_alias').'='.$db->quote($alias).' LIMIT 1');

		$rows = $db->loadObjectList();

		if(count($rows)==0)
			return '';
		else
		{
			$row=$rows[0];
			return $row->videoid;
		}
	}

	public static function getVideoRowByID($videoid)
	{
		if($videoid=='' or $videoid=='****youtubegallery-video-id****')
			return false;


		//Check DB
		$db = JFactory::getDBO();

		$query = 'SELECT *,IF(es_customtitle!="", es_customtitle, es_title) AS es_title,IF(es_customdescription!="", es_customdescription, es_description) AS es_description'
			.' FROM #__customtables_table_youtubegalleryvideos'
			.' WHERE es_videoid='.$db->quote($videoid).' LIMIT 1';

		$db->setQuery($query);

		$values=$db->loadAssocList();
		
		if(count($values)==0)
			return false;

		return $values[0];
	}
}
