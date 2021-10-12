<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

namespace YouTubeGallery;

defined('_JEXEC') or die('Restricted access');

class Helper
{
	//Text Functions
	
	public static function csv_explode($delim=',', $str, $enclose='"', $preserve=false)
	{
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
						$description=Helper::PrepareDescription_($description, $words, $chars);

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
	
	public static function PrepareDescription_($desc, $words, $chars)
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
	
	//URL/Network Functions
	
	public static function full_url($s, $use_forwarded_host=false)
	{
	    return Helper::url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
	}

	/*
	protected static function _is_curl_installed()
	{
		if  (in_array  ('curl', get_loaded_extensions())) {
			return true;
		}
		else {
			return false;
		}
	}
	*/
	
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
	
	
	public static function parse_query($var)
	{
		$arr  = array();

		 $var  = parse_url($var);
		 $varquery=$var['query'];


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
					$uri= Helper::deleteURLQueryOption($uri, 'fb_action_ids');
					$uri= Helper::deleteURLQueryOption($uri, 'fb_action_types');
					$uri= Helper::deleteURLQueryOption($uri, 'fb_source');
					$uri= Helper::deleteURLQueryOption($uri, 'action_object_map');
					$uri= Helper::deleteURLQueryOption($uri, 'action_type_map');
					$uri= Helper::deleteURLQueryOption($uri, 'action_ref_map');
				}
				$pageURL .=$uri;
			}

		return $pageURL;
	}
	
	public static function getURLData($url,$format='json')
	{
			if (function_exists('curl_init'))
			{
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_VERBOSE, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_ACCEPT_ENCODING, 'gzip, deflate, br');
				
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9',
				'accept-language:en-US,en;q=0.9',
				'cache-control: max-age=0',
				'sec-fetch-dest: document',
				'sec-fetch-mode: navigate',
				'sec-fetch-site: none',
				'sec-fetch-user: ?1',
				'upgrade-insecure-requests: 1'));
				
				curl_setopt($ch, CURLOPT_USERAGENT, 'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.88 Safari/537.36');
				curl_setopt($ch, CURLOPT_URL,$url);
				
				//if($format=='json')
				//{
					//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
				//}
				
				$htmlcode = curl_exec($ch);
				if($htmlcode === FALSE) {
					$application = JFactory::getApplication();
					$application->enqueueMessage(curl_error($ch), 'error');

					return '';
				}
				
				curl_close($ch);
				return $htmlcode;
			}
			elseif (ini_get('allow_url_fopen') == true)
			{
				return file_get_contents($url);
			}
			else
			{
			    $application = JFactory::getApplication();
				$application->enqueueMessage('Cannot load data, enable "allow_url_fopen" or install cURL<br/>'
				.'<a href="https://joomlaboat.com/youtube-gallery/f-a-q/why-i-see-allow-url-fopen-message" target="_blank">Here</a> is what to do.', 'error');

				return '';
			}
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
		$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
                if ( preg_match ( "/iphone|itouch|ipod|ipad/", $user_agent ) ) {
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

	//Convert Functions
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
	
	//param Functions (Menu Item)
	public static function ApplyPlayerParameters(&$settings,$youtubeparams)
	{
		if($youtubeparams=='')
			return;

		$a=str_replace("\n",'',$youtubeparams);
		$a=trim(str_replace("\r",'',$a));
		$l=explode(';',$a);

		foreach($l as $o)
		{
			if($o!='')
			{
				$pair=explode('=',$o);
				if(count($pair)==2)
				{
					$option=trim(strtolower($pair[0]));

					$found=false;

					for($i=0;$i<count($settings);$i++)
					{

						if($settings[$i][0]==$option)
						{
							$settings[$i][1]=$pair[1];
							$found=true;
							break;
						}
					}

					if(!$found)
						$settings[]=array($option,$pair[1]);
				}//if(count($pair)==2)
			}//if($o!='')
		}

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
	
	
	public static function prepareDescriptions($gallery_list)
	{
			//-------------------- prepare description
				$params='';
				$new_gallery_list=array();
				$videodescription_params=explode(',',$params);
				
				foreach($gallery_list as $listitem)
				{
					$description=$listitem['es_description'];
					$description=str_replace('&quot;','_quote_',$description);
					$description=str_replace('"','_quote_',$description);
					$description=str_replace("'",'_quote_',$description);
					$description=str_replace("@",'_email_',$description);
					
					if($params!='')
						$description=Helper::PrepareDescription($description, $videodescription_params);
					
					$listitem['es_description']=$description;

					$title=$listitem['es_title'];
					$title=str_replace('&quot;','_quote_',$title);
					$title=str_replace('"','_quote_',$title);
					$listitem['title']=str_replace("'",'_quote_',$title);
					
					$title=$listitem['es_customtitle'];
					$title=str_replace('&quot;','_quote_',$title);
					$title=str_replace('"','_quote_',$title);
					$listitem['es_customtitle']=str_replace("'",'_quote_',$title);
					
					$new_gallery_list[]=$listitem;
				}
			return $new_gallery_list;
	}

}


