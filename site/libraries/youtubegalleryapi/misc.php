<?php
/**
 * YoutubeGallery API for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once('data.php');

class YouTubeGalleryAPIMisc
{
	static public function APIKeys_Youtube()
	{
		//https://console.developers.google.com/apis/api/youtube/overview?project=ygforjoomla&folder=&organizationId=
		
		$db = JFactory::getDBO();
		$query = 'SELECT es_youtuvedataapikey FROM #__customtables_table_youtubegallerykeytypes'
			.' WHERE es_youtuvedataapikey IS NOT NULL AND es_youtuvedataapikey != "" GROUP BY es_youtuvedataapikey';
		$db->setQuery($query);
		$rows=$db->loadAssocList();

		if(count($rows)==0)
			return [];
		$column = $db->loadColumn(0);

		return $column;
	}
	
	static public function APIKey_Youtube()
	{
		$keys = YouTubeGalleryAPIMisc::APIKeys_Youtube();
		if(count($keys) == 0)
			return '';
		
		$index = rand(0, count($keys) - 1);
		
		return $keys[$index];
	}
	
	static public function APIKey_Vimeo_Consumer_Key()
	{
		/*
		Vimeo API

		In order to allow Youtube Gallery to fetch metadata (title,description etc) of the Vimeo Video you have to register your own instance of Youtube Gallery.
		https://developer.vimeo.com/apps/new

		Type 'YoutubeGallery Your Site/Name' into 'App Name' field during registration.
		*/
		return '30ee2d9b75c95e4d1457402a8ef6be9f5bea209e';//Client ID (Also known as Consumer Key or API Key)
	}
	
	static public function APIKey_Vimeo_Consumer_Secret()
	{
		return '7d028cf274f1e0f644267bcae524be455bce4556';
	}
	
	static public function APIKey_Vimeo_Oauth_Access_Token()
	{
		return '';
	}
	
	static public function APIKey_SoundCloud_ClientID()
	{
		return '15b62a25f18d50104a0860bb62ed4b8f';
	}
	
	static public function APIKey_SoundCloud_ClientSecret()
	{
		return '272360cb476e9761227cac686d30d41b';
	}
	
	static public function UpdatePeriod()
	{
		return 3600;
	}
	
	static public function getBlankArray($isPublic=false)
	{
		$blankArray=array(
				'id'=>0,
				'es_videosource'=>'',
				'es_videoid'=>'',
				'es_videoids'=>'',
				'es_trackid'=>'',
				'es_isvideo'=>0,
				'es_link'=>'',
				'es_lastupdate'=>null,
				
				'es_imageurl'=>'',
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
				'es_commentcount'=>0,

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
				'es_rawdata'=>null,
				'es_latitude' => null,
				'es_longitude' => null,
				'es_altitude' => null
				
				);
				
		if(!$isPublic)
		{
			$blankArray['es_datalink']='';
			$blankArray['es_rawdata']=null;
		}
		
		//http://api.joomlaboat.com/youtube-gallery?query=aHR0cHM6Ly93d3cueW91dHViZS5jb20vd2F0Y2g/dj0wSEwtTjlvT2pjcyZsaXN0PVJETU11YllHRERJdXVoMCZpbmRleD0yNw==
		return $blankArray;
	}
	
	function isVideo_record_exist($videosource,$videoid, $videolist_id = null)
	{
		$db = JFactory::getDBO();

		$wheres=[];
		
		$wheres[] = $db->quoteName('es_videosource').'='.$db->quote($videosource);
		$wheres[] = $db->quoteName('es_videoid').'='.$db->quote($videoid);
		
		if($videolist_id != null)
			$wheres[] = $db->quoteName('es_videolist').'='.(int)$videolist_id;

		$query = 'SELECT id FROM #__customtables_table_youtubegalleryvideos WHERE '.implode(' AND ', $wheres).' LIMIT 1';
		
		$db->setQuery($query);

		$videos_rows=$db->loadAssocList();

		if(count($videos_rows)==0)
			return 0;

		$videos_row=$videos_rows[0];

		return $videos_row['id'];
	}
	
	function checkLink($theLink, &$isnew, $active_key, $force_update = false, $videolist_id = null, $youtube_data_api_key = '')
	{
		$blankArray=array();
		
		$vsn=YouTubeGalleryAPIData::getVideoSourceName($theLink);//For link valdation
		
		if($vsn!='')
		{
			$videoid=YouTubeGalleryAPIData::getVideoID($theLink,$vsn);//For linkvalidation again
			if($videoid!='')
			{
				if(YouTubeGalleryAPIData::isVideoList($vsn))
				{
					$videos_rows=$this->getVideoRecords($vsn,$videoid,true);
					if(count($videos_rows)==0)
					{
						$isnew=1;
						return $this->update_cache_table($theLink,$active_key, $videolist_id, $youtube_data_api_key);//new
					}
					else
					{
						$isnew=0;
						
						if((bool)$force_update)
							return $this->update_cache_table($theLink,$active_key, $videolist_id, $youtube_data_api_key);//not new
						else
							return $videos_rows;//not new
					}
				}
				else
				{
					$videos_rows=$this->getVideoRecords($vsn,$videoid,true);
					//Think about updating videos
					if(count($videos_rows)==0)
					{
						//Video not found in database, try to grab it from the provider
						$isnew=1;
						return $this->update_cache_table($theLink,$active_key, $videolist_id, $youtube_data_api_key);//new
					}
					else
					{
						$isnew=0;
						
						if((bool)$force_update)
						{
							$recs=$this->update_cache_table($theLink,$active_key, $videolist_id, $youtube_data_api_key);//not new
							return $recs;
						}
						else
							return $videos_rows;//not new
					}
				}
			}
			else
			{
				$blankArray['es_status'] = -4;
				$blankArray['es_error'] = 'YoutubeGalleryAPI ('.$vsn.'): Not supported video link.';
				
				if($videolist_id != null)
					$blankArray['es_videolist'] = (int)$videolist_id;
			}
		}
		else
		{
			$blankArray['es_status']=-3;
			$blankArray['es_error']='YoutubeGalleryAPI: Not supported video source.';
			
			if($videolist_id != null)
				$blankArray['es_videolist'] = (int)$videolist_id;
		}
		return $blankArray;
	}
	
	function getVideoRecords($videosource,$videoid,$noNullDate=false)
	{
		$db = JFactory::getDBO();
		
		$item=YouTubeGalleryAPIMisc::getBlankArray(true);
		$keys=array_keys($item);
		$selects=implode(',',$keys);
		
		$wheres=array();
		$wheres[]='(es_videosource='.$db->quote($videosource).' AND es_videoid='.$db->quote($videoid).')';
		if($noNullDate)
		{
			$wheres[]='es_lastupdate IS NOT NULL';
			$wheres[]='DATEDIFF(NOW(), es_lastupdate)<1';
		}

		$query = 'SELECT '.$selects.' FROM #__customtables_table_youtubegalleryvideos AS l WHERE '.implode(' AND ',$wheres);

		$db->setQuery($query);
		$recs=$db->loadAssocList();
		
		if(count($recs)==1)
		{
			$rec=$recs[0];
			if((int)$rec['es_isvideo']==0)
			{
				$where='INSTR('.$db->quote($rec['es_videoids']).',CONCAT(",",es_videoid,","))';
				$query = 'SELECT '.$selects.' FROM #__customtables_table_youtubegalleryvideos WHERE '.$where;
				
				$db->setQuery($query);
				$recs_videos=$db->loadAssocList();
				return array_merge($recs,$recs_videos);
			}
				
		}
		return $recs;
	}
	
	function update_cache_table($theLink,$active_key, $videolist_id = null, $youtube_data_api_key = '')
	{
		$videolist=YouTubeGalleryAPIData::formVideoList($theLink,$active_key, $youtube_data_api_key);

		$parent_id = null;

		$db = JFactory::getDBO();

		for($i=0;$i<count($videolist);$i++)
		{
			$g=$videolist[$i];
					
			if($videolist_id != null)
				$g['es_videolist'] = (int)$videolist_id;
								
			$fields=YouTubeGalleryAPIMisc::makeSetList($g,$parent_id);
			
			$record_id=$this->isVideo_record_exist($g['es_videosource'],$g['es_videoid'], $videolist_id);

			if($record_id==0)
			{
				$fields[]=$db->quoteName('es_allowupdates').'=1';
							
				if($videolist_id != null)
					$fields[]=$db->quoteName('es_videolist').'='.(int)$videolist_id;
									
				$query = 'INSERT #__customtables_table_youtubegalleryvideos SET '.implode(', ', $fields);
						
				$db->setQuery($query);
				$db->execute();

				$record_id_new=$db->insertid();
				$g['id']=$record_id_new;
				
				if((int)$g['es_isvideo']==0)
					$parent_id = $record_id_new;
			}
			else
			{
				$query='UPDATE #__customtables_table_youtubegalleryvideos SET '.implode(', ', $fields).' WHERE id='.$record_id;
				$g['id']=$record_id;
				$db->setQuery($query);
				$db->execute();
			}
					
			//To return clean and secure record
			$g['es_datalink']='';
			$g['es_rawdata']=null;
			$videolist[$i]=$g;
		}
		
		return $videolist;
	}
	
	protected static function makeSetList($g,&$parent_id)
	{
		$db = JFactory::getDBO();
		
		$g_title=str_replace('"','&quot;',$g['es_title']);
		$g_description=str_replace('"','&quot;',$g['es_description']);

		$fields=array();
		
		if((int)$g['es_isvideo']==0)
			$parent_id=null;
			
		if($parent_id!=null)
			$fields[]=$db->quoteName('es_parentid').'='.(int)$parent_id;

		if((int)$g['es_isvideo']==0 and isset($g['es_videoids']))//Video List - Playlist etc
			$fields[]=$db->quoteName('es_videoids').'='.$db->quote($g['es_videoids']);

		$fields[]=$db->quoteName('es_videosource').'='.$db->quote($g['es_videosource']);

		$fields[]=$db->quoteName('es_videoid').'='.$db->quote($g['es_videoid']);
						
		if(isset($g['es_trackid']))
			$fields[]=$db->quoteName('es_trackid').'='.$db->quote($g['es_trackid']);

		if(isset($g['es_datalink']))
			$fields[]=$db->quoteName('es_datalink').'='.$db->quote($g['es_datalink']);
						
		$fields[]=$db->quoteName('es_lastupdate').'=NOW()';//.$db->quote($g['es_lastupdate']);
		
		if(isset($g['es_rawdata']))
			$fields[]=$db->quoteName('es_rawdata').'=NULL';//.$db->quote($g['es_rawdata']);

		if($g['es_imageurl']!='')
			$fields[]=$db->quoteName('es_imageurl').'='.$db->quote($g['es_imageurl']);

		if($g['es_title']!='')
			$fields[]=$db->quoteName('es_title').'='.$db->quote($g_title);

		if($g['es_description']!='')
			$fields[]=$db->quoteName('es_description').'='.$db->quote($g_description);

		$fields[]=$db->quoteName('es_link').'='.$db->quote($g['es_link']);
						
		$fields[]=$db->quoteName('es_isvideo').'='.$g['es_isvideo'];

		if(isset($g['es_publisheddate']))
		{
			$publisheddate = date('Y-m-d H:i:s', strtotime($g['es_publisheddate']));
			$fields[]=$db->quoteName('es_publisheddate').'='.$db->quote($publisheddate);
		}

		if(isset($g['es_duration']))
			$fields[]=$db->quoteName('es_duration').'='.(int)$g['es_duration'];

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

		return $fields; 
	}

	public static function parse_query($var)
	{
		$arr  = array();
		
		$var  = parse_url($var);
		if(isset($var['query']))
			$varquery=$var['query'];
		else
			$varquery='';


		 if($varquery=='')
			return $arr;

		 $var  = html_entity_decode($varquery);
		 $var  = explode('&', $var);


		foreach($var as $val)
		{
			$x          = explode('=', $val);
			$arr[$x[0]] = $x[1];
		}
		unset($val, $x, $var);
		return $arr;
	}

	public static function csv_explode(string $delim, $str, $enclose='"', $preserve=false)
	{
		//$delim=','
		$resArr = array();
		$n = 0;
		$expEncArr = explode($enclose, $str);
		foreach($expEncArr as $EncItem)
		{
			if($n++%2){
				array_push($resArr, array_pop($resArr) . ($preserve?$enclose:'') . $EncItem.($preserve?$enclose:''));
			}else{
				$expDelArr = explode($delim, $EncItem);
				array_push($resArr, array_pop($resArr) . array_shift($expDelArr));
			    $resArr = array_merge($resArr, $expDelArr);
			}
		}
		return $resArr;
	}

	public static function getURLData($url,$format='json')
	{
		$htmlcode='';

		if (function_exists('curl_init'))
		{
			$ch = curl_init();
			$timeout = 150;
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

			if($format=='json')
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));

			$htmlcode = curl_exec($ch);
			curl_close($ch);
		}
		elseif (ini_get('allow_url_fopen') == true)
		{
			$htmlcode = file_get_contents($url);
		}
		else
		{
		    $application = JFactory::getApplication();
			$application->enqueueMessage('Cannot load data, enable "allow_url_fopen" or install cURL<br/>'
			.'<a href="https://joomlaboat.com/youtube-gallery/f-a-q/why-i-see-allow-url-fopen-message" target="_blank">Here</a> is what to do.', 'error');
				return '';
		}
		return $htmlcode;
	}

	public static function CreateParamLine(&$settings)
	{
		$a=array();

		foreach($settings as $s)
		{
			if(isset($s[1]))
				$a[]=$s[0].'='.$s[1];
		}

		return implode('&amp;',$a);
	}

	public static function slugify($text)
	{
		//or use

		// replace non letter or digits by -
		$text = preg_replace('~[^\\pL\d]+~u', '-', $text);

		if(function_exists('iconv'))
			$text = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $text);

		$text = trim($text, '-');
		$text = strtolower($text);

		// remove unwanted characters
		$text = preg_replace('~[^-\w]+~', '', $text);

		if (empty($text))
			return '';

		return $text;
	}

	/* USER-AGENTS ================================================== */
	//http://stackoverflow.com/questions/6524301/detect-mobile-browser
	public static function check_user_agent ( $type = NULL )
	{
        $user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
        if ( $type == 'bot' ) {
                // matches popular bots
                if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $user_agent ) ) {
                        return true;
                        // watchmouse|pingdom\.com are "uptime services"
                }
        } else if ( $type == 'browser' ) {
                // matches core browser types
                if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
                        return true;
                }
        } else if ( $type == 'mobile' ) {
                // matches popular mobile devices that have small screens and/or touch inputs
                // mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
                // detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
                if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) {
                        // these are the most common
                        return true;
                } else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
                        // these are less common, and might not be worth checking
                        return true;
                }
        }
        return false;
	}

	public static function check_user_agent_for_apple ()
	{
		$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT']);
		if(preg_match ( "/iphone|itouch|ipod|ipad/", $user_agent ) )
		{
			// these are the most common
			return true;
		}
		return false;
	}

	public static function check_user_agent_for_ie ()
	{
		$u=$_SERVER['HTTP_USER_AGENT'];
		if(strpos($u, 'MSIE') !== FALSE)
			return true;
		elseif(strpos($u, 'Trident') !== FALSE)
			return true;

		return false;
	}

	public static function html2txt($document)
	{
		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
               '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
		);
		$text = preg_replace($search, '', $document);
		return $text;
	}

	protected static function url_origin($s, $use_forwarded_host=false)
	{
		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true:false;
		$sp = strtolower($s['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
		$port = $s['SERVER_PORT'];
		$port = ((!$ssl && $port=='80') || ($ssl && $port=='443')) ? '' : ':'.$port;
		$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME']);
		return $protocol . '://' . $host . $port;
	}

	public static function full_url($s, $use_forwarded_host=false)
	{
	    return YouTubeGalleryAPIMisc::url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
	}

	public static function getMaxResults($spq,&$option)
	{
		$count=0;
		$pair=explode('&',$spq);
		foreach($pair as $p)
		{
			$opt=explode('=',$p);
			if($opt[0]=='maxResults')
			{
				$option=$opt[0].'='.$opt[1];
				$count=(int)$opt[1];
			}
		}

		if($count==0)
			$count=50;

		return $count;
	}

	function _is_curl_installed()
	{
	    if  (in_array  ('curl', get_loaded_extensions())) {
	        return true;
	    }
	    else {
	        return false;
	    }
	}
	
	public static function getValueByAlmostTag($HTML_SOURCE,$AlmostTagStart,$AlmostTagEnd='"')
	{
		$vlu='';

		$strPartLength=strlen($AlmostTagStart);
		$p1=strpos($HTML_SOURCE,$AlmostTagStart);
		if($p1>0)
		{
			$p2=strpos($HTML_SOURCE,$AlmostTagEnd,$p1+$strPartLength);
			$vlu=substr($HTML_SOURCE,$p1+$strPartLength,$p2-$p1-$strPartLength);
		}
		return $vlu;
	}
	
	public static function getNumberOfPayments()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(id) AS c FROM #__customtables_table_payments';
		
		try
        {
			$db->setQuery($query);
			$recs=$db->loadAssocList();
		}
        catch (RuntimeException $e)
        {
			return 0;
		}
			
		if(count($recs)==0)
			return 0;
		
		return (int)$recs[0]['c'];
	}
}
