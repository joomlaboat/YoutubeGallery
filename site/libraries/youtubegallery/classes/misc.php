<?php
/**
 * YouTubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

namespace YouTubeGallery;

use CustomTables\CTMiscHelper;
use Throwable;
use YoutubeGalleryLayoutRenderer;

defined('_JEXEC') or die('Restricted access');

class Helper
{
	//Text Functions
	public static function html2txt($document): string
	{
		if ($document === null)
			return '';

		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
			'@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
			'@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
			'@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
		);
		return preg_replace($search, '', $document);
	}

	public static function full_url($s, $use_forwarded_host = false): string
	{
		return Helper::url_origin($s, $use_forwarded_host) . $s['REQUEST_URI'];
	}

	protected static function url_origin($s, $use_forwarded_host = false): string
	{
		$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
		$sp = strtolower($s['SERVER_PROTOCOL']);
		$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
		$port = $s['SERVER_PORT'];
		$port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
		$host = ($use_forwarded_host && isset($s['HTTP_X_FORWARDED_HOST'])) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : $s['SERVER_NAME']);
		return $protocol . '://' . $host . $port;
	}

	//URL/Network Functions
	public static function curPageURL($add_REQUEST_URI = true): string
	{
		$pageURL = '';

		$pageURL .= 'http';

		if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {
			$pageURL .= "s";
		}

		$pageURL .= "://";

		if (isset($_SERVER["HTTPS"])) {
			if (isset($_SERVER["SERVER_PORT"]) and $_SERVER["SERVER_PORT"] != "80") {
				$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"];
			} else {
				$pageURL .= $_SERVER["SERVER_NAME"];
			}
		} else
			$pageURL .= $_SERVER["SERVER_NAME"];

		if ($add_REQUEST_URI) {
			//clean Facebook staff
			$uri = $_SERVER["REQUEST_URI"];
			if (str_contains($uri, 'fb_action_ids=')) {
				$uri = CTMiscHelper::deleteURLQueryOption($uri, 'fb_action_ids');
				$uri = CTMiscHelper::deleteURLQueryOption($uri, 'fb_action_types');
				$uri = CTMiscHelper::deleteURLQueryOption($uri, 'fb_source');
				$uri = CTMiscHelper::deleteURLQueryOption($uri, 'action_object_map');
				$uri = CTMiscHelper::deleteURLQueryOption($uri, 'action_type_map');
				$uri = CTMiscHelper::deleteURLQueryOption($uri, 'action_ref_map');
			}
			$pageURL .= $uri;
		}

		return $pageURL;
	}

	public static function ApplyPlayerParameters(&$settings, $youtubeParams): void
	{
		if ($youtubeParams == '')
			return;

		$a = str_replace("\n", '', $youtubeParams);
		$a = trim(str_replace("\r", '', $a));
		$l = explode(';', $a);

		foreach ($l as $o) {
			if ($o != '') {
				$pair = explode('=', $o);
				if (count($pair) == 2) {
					$option = trim(strtolower($pair[0]));

					$found = false;

					for ($i = 0; $i < count($settings); $i++) {

						if ($settings[$i][0] == $option) {
							$settings[$i][1] = $pair[1];
							$found = true;
							break;
						}
					}

					if (!$found)
						$settings[] = array($option, $pair[1]);
				}
			}
		}
	}

	//Convert Functions
	public static function CreateParamLine($settings): string
	{
		$a = array();

		foreach ($settings as $s) {
			if (isset($s[1]))
				$a[] = $s[0] . '=' . $s[1];
		}

		return implode('&amp;', $a);
	}

	//param Functions (Menu Item)
	public static function prepareDescriptions($gallery_list): array
	{
		//-------------------- prepare description
		$params = '';
		$new_gallery_list = array();
		$videoDescription_params = explode(',', $params);

		foreach ($gallery_list as $listItem) {
			$description = $listItem['es_description'];
			$description = str_replace('&quot;', '_quote_', $description);
			$description = str_replace('"', '_quote_', $description);
			$description = str_replace("'", '_quote_', $description);
			$description = str_replace("@", '_email_', $description);

			if ($params != '')
				$description = Helper::PrepareDescription($description, $videoDescription_params);

			$listItem['es_description'] = $description;

			$title = $listItem['es_title'];
			$title = str_replace('&quot;', '_quote_', $title);
			$title = str_replace('"', '_quote_', $title);
			$listItem['title'] = str_replace("'", '_quote_', $title);

			$title = $listItem['es_customtitle'];
			$title = str_replace('&quot;', '_quote_', $title);
			$title = str_replace('"', '_quote_', $title);
			$listItem['es_customtitle'] = str_replace("'", '_quote_', $title);

			$new_gallery_list[] = $listItem;
		}
		return $new_gallery_list;
	}

	public static function PrepareDescription($description, $videoDescriptionParams): string
	{
		if (count($videoDescriptionParams) > 0) {
			$words = (int)$videoDescriptionParams[0];
			if (isset($videoDescriptionParams[1]))
				$chars = (int)$videoDescriptionParams[1];
			else
				$chars = 0;

			if ($words != 0 or $chars != 0)
				$description = Helper::PrepareDescription_($description, $words, $chars);

			if (isset($videoDescriptionParams[2]) and $videoDescriptionParams[2] == 'addlinebreaks') {
				$description = nl2br($description);
				$description = str_replace('<br />', '_thelinebreak_', $description);
			}
		}

		$description = str_replace('&quot;', '_quote_', $description);
		return str_replace('@', '_email_', $description);
	}

	public static function PrepareDescription_($desc, $words, $chars): string
	{
		if ($chars == 0 and $words > 0) {
			preg_match('/([^\\s]*(?>\\s+|$)){0,' . $words . '}/', $desc, $matches);
			$desc = trim($matches[0]);
		} else {
			if (strlen($desc) > $chars)
				$desc = substr($desc, 0, $chars);
		}

		$desc = str_replace("/n", " ", $desc);
		$desc = str_replace("/r", " ", $desc);
		$desc = trim(preg_replace('/\s\s+/', ' ', $desc));
		return trim($desc);
	}
}


