<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');

class YouTubeGalleryDB
{
	var $videolist_row;
	var $theme_row;

	//
	function getVideoListTableRow($listid)
	{
		$db = JFactory::getDBO();

		//Load Video List

		$query = 'SELECT ';
		$query .= '#__youtubegallery_videolists.id AS id, ';
		$query .= '#__youtubegallery_videolists.listname AS listname, ';
		$query .= '#__youtubegallery_videolists.videolist AS videolist, ';
		$query .= '#__youtubegallery_videolists.catid AS catid, ';
		$query .= '#__youtubegallery_videolists.updateperiod AS updateperiod, ';
		$query .= '#__youtubegallery_videolists.lastplaylistupdate AS lastplaylistupdate, ';
		$query .= '#__youtubegallery_videolists.description AS description, ';
		$query .= '#__youtubegallery_videolists.author AS author, ';
		$query .= '#__youtubegallery_videolists.authorurl AS authorurl, ';
		$query .= '#__youtubegallery_videolists.image AS image, ';
		$query .= '#__youtubegallery_videolists.note AS note, ';
		$query .= '#__youtubegallery_videolists.watchusergroup AS watchusergroup, ';

		$query .= 'COUNT(#__youtubegallery_videos.listid) AS TotalVideos FROM #__youtubegallery_videolists';

		$query .= ' LEFT JOIN #__youtubegallery_videos ON #__youtubegallery_videos.listid=#__youtubegallery_videolists.id AND #__youtubegallery_videos.isvideo';
		$query .= ' WHERE #__youtubegallery_videolists.id='.(int)$listid.' ';
		$query .= ' GROUP BY id';
		$query .= ' LIMIT 1';

		$db->setQuery($query);
		if (!$db->query())    die ( $db->stderr());

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
		$query = 'SELECT id, themename, width, height, playvideo, `repeat`, fullscreen, autoplay, related, allowplaylist, showinfo, bgcolor, cols,
		showtitle, cssstyle, navbarstyle, thumbnailstyle, linestyle, showlistname, listnamestyle, showactivevideotitle, activevideotitlestyle,
		description, descr_position, descr_style, color1, color2, border, openinnewwindow, rel, hrefaddon, pagination, customlimit,
		controls, youtubeparams, playertype, useglass, logocover, customlayout,  prepareheadtags, muteonplay,
		volume, orderby, customnavlayout, responsive, mediafolder, readonly, headscript, themedescription, nocookie, changepagetitle
		FROM #__youtubegallery_themes WHERE id='.(int)$themeid.' LIMIT 1';


		$db->setQuery($query);
		if (!$db->query())    die ( $db->stderr());

		$theme_rows = $db->loadObjectList();

		if(count($theme_rows)==0)
			return false;//'<p>No theme found</p>';

		$this->theme_row=$theme_rows[0];
		return true;
	}


	static public function getRawData($videoid)
	{

		$db = JFactory::getDBO();

		$query = 'SELECT rawdata FROM #__youtubegallery_videos WHERE videoid='.$db->quote($videoid).' LIMIT 1';

		$db->setQuery($query);
		if (!$db->query())    die( $db->stderr());

		$values=$db->loadAssocList();

		if(count($values)==0)
			return "";

		$v=$values[0];

		return $v['rawdata'];
	}

	static public function setDelayedRequest($videoid,$link)
	{
		if($videoid!='')
		{
			$value='*youtubegallery_request*';//.$link;//md5(mt_rand());

			$db = JFactory::getDBO();

			$query = 'UPDATE #__youtubegallery_videos SET '.$db->quoteName('rawdata').'='.$db->quote($value).' WHERE '.$db->quoteName('videoid').'='.$db->quote($videoid);

			$db->setQuery($query);
			if (!$db->query())    die ( $db->stderr());


		}

	}

	static public function setRawData($videoid,$videoData)
	{
		if($videoid!='')
		{
			$db = JFactory::getDBO();
			$query = 'UPDATE #__youtubegallery_videos SET '.$db->quoteName('rawdata').'='.$db->quote($videoData).' WHERE '.$db->quoteName('videoid').'='.$db->quote($videoid);
			$db->setQuery($query);
			if (!$db->query())    die ( $db->stderr());
		}

	}


	protected static function isVideo_record_exist($videosource,$videoid)//,$listid)
	{
				$db = JFactory::getDBO();

				$query = 'SELECT id, allowupdates FROM #__youtubegallery_videos WHERE '.$db->quoteName('videosource').'='.$db->quote($videosource).' AND '.$db->quoteName('videoid').'='.$db->quote($videoid).' LIMIT 1';
				//.' AND '.$db->quoteName('listid').'='.$listid.' LIMIT 1';

				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());

				$videos_rows=$db->loadAssocList();

				if(count($videos_rows)==0)
						return 0;

				$videos_row=$videos_rows[0];

				if($videos_row['allowupdates']!=1)
						return -1; //Updates disable

				return $videos_row['id'];
	}


	function getVideoList_FromCache_From_Table(&$videoid,&$total_number_of_rows,$get_the_first_one=false)
	{
		$listIDs=array();
		$listIDs[]=$this->videolist_row->id;

		$db = JFactory::getDBO();

		$where=array();

		$where[]='!INSTR(title,"***Video not found***")';
		$where[]=$db->quoteName('listid').'='.$db->quote($this->videolist_row->id);
		$where[]='isvideo=0';
		$where[]='videosource="videolist"';


		$query = 'SELECT videoid FROM #__youtubegallery_videos WHERE '.implode(' AND ', $where);

		$db->setQuery($query);
		if (!$db->query())    die( $db->stderr());
		$videos_lists=$db->loadAssocList();

		if(count($videos_lists)>0)
		{
			foreach($videos_lists as $v)
			{

				if($v['videoid']==-1)
				{
					//all videos
					$listIDs=array();
					break;
				}
				elseif(!(strpos($v['videoid'],'catid=')===false))
				{
					//Video Lists of selected category by id

					$catid=intval(str_replace('catid=','',$v['videoid']));

					$query = 'SELECT id FROM #__youtubegallery_videolists WHERE catid='.$catid;
					$db->setQuery($query);
					if (!$db->query())    die( $db->stderr());
					$videos_lists_=$db->loadAssocList();

					foreach($videos_lists_ as $vl)
					{
						$listIDs[]=$vl['id'];
					}
				}
				elseif(!(strpos($v['videoid'],'category=')===false))
				{
					//Video Lists of selected category by id

					$categoryname=str_replace('category=','',$v['videoid']);

					$query = 'SELECT #__youtubegallery_videolists.id AS computedcatid FROM #__youtubegallery_videolists
					INNER JOIN #__youtubegallery_categories ON #__youtubegallery_categories.id=#__youtubegallery_videolists.catid
					WHERE #__youtubegallery_categories.categoryname='.$db->quote($categoryname);


					$db->setQuery($query);
					if (!$db->query())    die( $db->stderr());
					$videos_lists_=$db->loadAssocList();

					foreach($videos_lists_ as $vl)
					{
						$listIDs[]=$vl['computedcatid'];
					}

				}
				else
					$listIDs[]=$v['videoid'];
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
			$possible_fields=array('videoid','title','description','publisheddate','keywords','channel_username','channel_title','channel_description');//,'channel_totaluploadviews');


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
		$query = "SHOW COLUMNS FROM #__youtubegallery_videos";//SELECT * FROM #__youtubegallery_videos LIMIT 1";
		$db->setQuery( $query );
		$all = $db->loadAssocList();
		
		$fields=array();
		foreach($all as $key){
			if(!in_array($key['Field'],$fields))
				$fields[]=$key['Field'];
		}
		
		if (!in_array("latitude", $fields)) {
			$db->setQuery("ALTER TABLE `#__youtubegallery_videos` ADD `latitude` decimal(20,7) NULL DEFAULT NULL");
			if (!$db->query())    die( $db->stderr());
		}
		
		if (!in_array("longitude", $fields)) {
				$db->setQuery("ALTER TABLE `#__youtubegallery_videos` ADD `longitude` decimal(20,7) NULL DEFAULT NULL");
				if (!$db->query())    die( $db->stderr());
			}
		
		if (!in_array("altitude", $fields)) {
			$db->setQuery("ALTER TABLE `#__youtubegallery_videos` ADD `altitude` int NULL DEFAULT NULL");
			if (!$db->query())    die( $db->stderr());
		}
	}
	
	function getVideoList_FromCacheFromTable(&$videoid,&$total_number_of_rows,&$listIDs,$get_the_first_one=false)
	{

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
				$w[]=$db->quoteName('listid').'='.$db->quote($l);
			}
			$where[]='('.implode(' OR ',$w).')';
		}


		$where[]='isvideo=1';



		if($this->theme_row->rel!='' and JFactory::getApplication()->input->getCmd('tmpl')=='component')
		{
			// Get only one video - current video. and shadow box
			$where[]=$db->quoteName('videoid').'='.$db->quote($videoid);
			$limitstart=0;
			$limit=1;
		}
		if($get_the_first_one)
		{
			// Get only one video - the first video.
			$limitstart=0;
			$limit=1;
		}
		else
		{
			if(((int)$this->theme_row->customlimit)==0)
				$limit=0; // UNLIMITED
			else
				$limit = (int)$this->theme_row->customlimit;

			$limitstart = JFactory::getApplication()->input->getInt('ygstart', 0);
		}

		if($this->theme_row->orderby!='')
		{
			if($this->theme_row->orderby=='randomization')
				$orderby='RAND()';
			else
				$orderby=$this->theme_row->orderby;
		}
		else
			$orderby='ordering';

		$query = 'SELECT * FROM #__youtubegallery_videos WHERE '.implode(' AND ', $where).' GROUP BY videoid ORDER BY '.$orderby;

		$db->setQuery($query);
		if (!$db->query())    die( $db->stderr());

		$total_number_of_rows = $db->getNumRows();

		if($limit==0)
			$db->setQuery($query);
		else
			$db->setQuery($query, $limitstart, $limit);

		if (!$db->query())    die( $db->stderr());

		$videos_rows=$db->loadAssocList();
		
		
		

		$firstvideo='';

		if($firstvideo=='' and count($videos_rows)>0)
		{
			$videos_row=$videos_rows[0];
			$firstvideo=$videos_row['videoid'];


		}
		if($videoid!='')
		{

		}
		else
		{
			if($firstvideo!='')
				$videoid=$firstvideo;
		}

		return $videos_rows;

	}



	function update_playlist($force_update = false)
	{

			$start  = strtotime( $this->videolist_row->lastplaylistupdate );
			$end    = strtotime( date( 'Y-m-d H:i:s') );
			$days_diff = ($end-$start)/86400;

			$updateperiod=(float)$this->videolist_row->updateperiod;
			if($updateperiod==0)
				$updateperiod=1;

			if($days_diff>abs($updateperiod) or $force_update)
			{

				YouTubeGalleryDB::update_cache_table($this->videolist_row);//,$updateperiod>0); //updateperiod>0 ? refresh : get new videos
				$this->videolist_row->lastplaylistupdate =date( 'Y-m-d H:i:s');

				$db = JFactory::getDBO();
				$query = 'UPDATE #__youtubegallery_videolists SET '.$db->quoteName('lastplaylistupdate').'='.$db->quote($this->videolist_row->lastplaylistupdate).' WHERE id='.(int)$this->videolist_row->id;
				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
			}
	}

	public static function update_cache_table(&$videolist_row,$update_videolist=true)
	{
				$videolist_array=YouTubeGalleryMisc::csv_explode("\n", $videolist_row->videolist, '"', true);
				$firstvideo='';
				$videolist=YouTubeGalleryData::formVideoList($videolist_array, $firstvideo, '');//$this->theme_row->thumbnailstyle);
				
				$db = JFactory::getDBO();
				$parent_id=0;
				//$parent_details=array();

				$ListOfVideosNotToDelete=array();
				
				foreach($videolist as $g)
				{
					$ListOfVideosNotToDelete[]='!(videoid='.$db->quote($g['videoid']).' AND videosource='.$db->quote($g['videosource']).')';
					YouTubeGalleryDB::updateDBSingleItem($g,(int)$videolist_row->id,$parent_id);//,$parent_details);//,$this_is_a_list);//,$list_count_left);
				}
		
				//Delete All videos of this video list that has been deleted form the list and allowed for updates.

				$query='DELETE FROM #__youtubegallery_videos WHERE listid='.(int)$videolist_row->id;
				if(count($ListOfVideosNotToDelete)>0)
					$query.=' AND '.implode(' AND ',$ListOfVideosNotToDelete);
					
				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
	}
	
	public static function updateDBSingleItem($g,$videolist_id,&$parent_id)//,&$parent_details)//,&$this_is_a_list)//,&$list_count_left)
	{
		YouTubeGalleryDB::checkIfLatLongAltFieldsExists();
		
		
		$db = JFactory::getDBO();

		$fields=YouTubeGalleryDB::prepareQuerySets($g,$videolist_id,$parent_id);//,$parent_details);//,$this_is_a_list);//,$list_count_left);
		
		$record_id=YouTubeGalleryDB::isVideo_record_exist($g['videosource'],$g['videoid']);//,$videolist_row->id);


		$query='';

		if($record_id==0)
						{
								$query='INSERT #__youtubegallery_videos SET '.implode(', ', $fields).', allowupdates="1"';

								$db->setQuery($query);
								if (!$db->query())    die( $db->stderr());

								$record_id_new=YouTubeGalleryDB::isVideo_record_exist($g['videosource'],$g['videoid']);//,$videolist_row->id);

								$ListOfVideos[]=$record_id_new;

								if((int)$g['isvideo']==0)
								{
									$parent_id=$record_id_new;
									//$parent_details=$g;
								}
						}
						elseif($record_id>0)
						{
								$query="UPDATE #__youtubegallery_videos SET ".implode(', ', $fields).' WHERE id='.$record_id;

								$db->setQuery($query);
								if (!$db->query())    die( $db->stderr());

								$ListOfVideos[]=$record_id;

								if((int)$g['isvideo']==0)
								{
									$parent_id=$record_id;
									//$parent_details=$g;
								}
						}
	}

	protected static function prepareQuerySets($g,$videolist_id,&$parent_id)//,&$parent_details)//,&$this_is_a_list)//,&$list_count_left)
	{
		$db = JFactory::getDBO();
		
		
		$g_title=str_replace('"','&quot;',$g['title']);
		$g_description=str_replace('"','&quot;',$g['description']);

		if(isset($g['custom_title']))
			$custom_g_title=str_replace('"','&quot;',$g['custom_title']);
		else
			$custom_g_title='';

		if(isset($g['custom_description']))
			$custom_g_description=str_replace('"','&quot;',$g['custom_description']);
		else
			$custom_g_description='';
		
		$fields=array();

						if((int)$g['isvideo']==0)
							$parent_id=0;

						if($videolist_id!=0)
							$fields[]=$db->quoteName('listid').'='.$db->quote($videolist_id);
						
						
						if($parent_id!=0)
							$fields[]=$db->quoteName('parentid').'='.$db->quote($parent_id);
						
						$fields[]=$db->quoteName('videosource').'='.$db->quote($g['videosource']);

						$fields[]=$db->quoteName('videoid').'='.$db->quote($g['videoid']);

						
						
						if(isset($g['datalink']))
							$fields[]=$db->quoteName('datalink').'='.$db->quote($g['datalink']);


						if($g['imageurl']!='')
							$fields[]=$db->quoteName('imageurl').'='.$db->quote($g['imageurl']);

						if($g['title']!='')
							$fields[]=$db->quoteName('title').'='.$db->quote($g_title);

						if($g['description']!='')
							$fields[]=$db->quoteName('description').'='.$db->quote($g_description);

						if(isset($g['custom_imageurl']))
							$fields[]=$db->quoteName('custom_imageurl').'='.$db->quote($g['custom_imageurl']);
						else
							$fields[]=$db->quoteName('custom_imageurl').'=""';


						if($g['title']!='')
							$fields[]=$db->quoteName('alias').'='.$db->quote(YouTubeGalleryDB::get_alias($g_title,$g['videoid']));


						$fields[]=$db->quoteName('custom_title').'='.$db->quote($custom_g_title);
						$fields[]=$db->quoteName('custom_description').'='.$db->quote($custom_g_description);

						if(isset($g['specialparams']))
							$fields[]=$db->quoteName('specialparams').'='.$db->quote($g['specialparams']);
						else
							$fields[]=$db->quoteName('specialparams').'=""';

						if(isset($g['startsecond']))
							$fields[]=$db->quoteName('startsecond').'='.$db->quote($g['startsecond']);
						else
							$fields[]=$db->quoteName('startsecond').'="0"';

						if(isset($g['endsecond']))
							$fields[]=$db->quoteName('endsecond').'='.$db->quote($g['endsecond']);
						else
							$fields[]=$db->quoteName('endsecond').'="0"';

						$fields[]=$db->quoteName('link').'='.$db->quote($g['link']);

						
						$fields[]=$db->quoteName('isvideo').'='.$db->quote($g['isvideo']);//$db->quote(($this_is_a_list ? '0' : '1'));

						if(isset($g['publisheddate']))
							$fields[]=$db->quoteName('publisheddate').'='.$db->quote($g['publisheddate']);

						if(isset($g['duration']))
						{
							$fields[]=$db->quoteName('duration').'='.$db->quote($g['duration']);
							
							if(isset($g['lastupdate']) and $g['lastupdate']!="" and $g['lastupdate']!="0000-00-00 00:00:00")
								$fields[]=$db->quoteName('lastupdate').'='.$db->quote($g['lastupdate']);
						}

						if(isset($g['rating_average']))
							$fields[]=$db->quoteName('rating_average').'='.$db->quote($g['rating_average']);

						if(isset($g['rating_max']))
							$fields[]=$db->quoteName('rating_max').'='.$db->quote($g['rating_max']);

						if(isset($g['rating_min']))
							$fields[]=$db->quoteName('rating_min').'='.$db->quote($g['rating_min']);

						if(isset($g['rating_numRaters']))
							$fields[]=$db->quoteName('rating_numRaters').'='.$db->quote($g['rating_numRaters']);

						if(isset($g['statistics_favoriteCount']))
							$fields[]=$db->quoteName('statistics_favoriteCount').'='.$db->quote($g['statistics_favoriteCount']);

						if(isset($g['statistics_viewCount']))
							$fields[]=$db->quoteName('statistics_viewCount').'='.$db->quote($g['statistics_viewCount']);


						if(isset($g['keywords']))
						{
							if(is_array($g['keywords']))
							{
								$key_words=implode(',',$g['keywords']);
								$fields[]=$db->quoteName('keywords').'='.$db->quote($key_words);
							}
							else
								$key_words='';

						}
						if(isset($g['likes']))
							$fields[]=$db->quoteName('likes').'='.$db->quote($g['likes']);

						if(isset($g['dislikes']))
							$fields[]=$db->quoteName('dislikes').'='.$db->quote($g['dislikes']);

						//if((int)$g['isvideo']==0)
							//$parent_details=$g;

						if(isset($g['channel_username']))
							$fields[]=$db->quoteName('channel_username').'='.$db->quote($g['channel_username']);

						if(isset($g['channel_title']))
							$fields[]=$db->quoteName('channel_title').'='.$db->quote($g['channel_title']);

						if(isset($g['channel_subscribers']))
							$fields[]=$db->quoteName('channel_subscribers').'='.$db->quote($g['channel_subscribers']);

						if(isset($g['channel_subscribed']))
							$fields[]=$db->quoteName('channel_subscribed').'='.$db->quote($g['channel_subscribed']);

						if(isset($g['channel_location']))
							$fields[]=$db->quoteName('channel_location').'='.$db->quote($g['channel_location']);

						if(isset($g['channel_commentcount']))
							$fields[]=$db->quoteName('channel_commentcount').'='.$db->quote($g['channel_commentcount']);

						if(isset($g['channel_viewcount']))
							$fields[]=$db->quoteName('channel_viewcount').'='.$db->quote($g['channel_viewcount']);

						if(isset($g['channel_videocount']))
							$fields[]=$db->quoteName('channel_videocount').'='.$db->quote($g['channel_videocount']);

						if(isset($g['channel_description']))
							$fields[]=$db->quoteName('channel_description').'='.$db->quote($g['channel_description']);
						
						
						if(isset($g['latitude']))
							$fields[]=$db->quoteName('latitude').'='.$db->quote($g['latitude']);

						if(isset($g['longitude']))
							$fields[]=$db->quoteName('longitude').'='.$db->quote($g['longitude']);

						if(isset($g['altitude']))
							$fields[]=$db->quoteName('altitude').'='.$db->quote($g['altitude']);
							
		return $fields;
	}


	public static function getSettingValue($option)
	{
				$db = JFactory::getDBO();

				$query = 'SELECT '.$db->quoteName('value').' FROM #__youtubegallery_settings WHERE '.$db->quoteName('option').'='.$db->quote($option).' LIMIT 1';

				$db->setQuery($query);
				if (!$db->query())    die( $db->stderr());
					$values=$db->loadAssocList();

				$vlu="";
				if(count($values)>0)
				{
					$v=$values[0];
					$vlu=$v['value'];				
				}
				
				return $vlu;
	}


	public static function get_alias($title,$videoid)
	{
		if($videoid!='')
		{
			$alias=YouTubeGalleryMisc::slugify($title);

			if($alias!="")
			{
				$db = JFactory::getDBO();

				$db->setQuery('SELECT '.$db->quoteName('alias').' FROM #__youtubegallery_videos WHERE '.$db->quoteName('alias').'='.$db->quote($alias));

				if (!$db->query())    die ('yg get_alias err:'. $db->stderr());
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

		$db->setQuery('SELECT '.$db->quoteName('videoid').' FROM #__youtubegallery_videos WHERE '.$db->quoteName('alias').'='.$db->quote($alias).' LIMIT 1');
		if (!$db->query())    die ('yg router.php 2 err:'. $db->stderr());
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

		$query = 'SELECT * FROM #__youtubegallery_videos WHERE videoid="'.$videoid.'" LIMIT 1';

		$db->setQuery($query);
		if (!$db->query())    die( $db->stderr());
		$values=$db->loadAssocList();

		if(count($values)==0)
			return false;

		return $values[0];
	}
	
	
}