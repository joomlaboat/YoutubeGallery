<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

namespace YouTubeGallery;

use Exception;
use Joomla\CMS\Factory;
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
                $uri = Helper::deleteURLQueryOption($uri, 'fb_action_ids');
                $uri = Helper::deleteURLQueryOption($uri, 'fb_action_types');
                $uri = Helper::deleteURLQueryOption($uri, 'fb_source');
                $uri = Helper::deleteURLQueryOption($uri, 'action_object_map');
                $uri = Helper::deleteURLQueryOption($uri, 'action_type_map');
                $uri = Helper::deleteURLQueryOption($uri, 'action_ref_map');
            }
            $pageURL .= $uri;
        }

        return $pageURL;
    }

    public static function deleteURLQueryOption($URLString, $opt): string
    {
        $url_first_part = '';
        $p = strpos($URLString, '?');
        if (!($p === false)) {
            $url_first_part = substr($URLString, 0, $p);
            $URLString = substr($URLString, $p + 1);
        }

        $URLString = str_replace('&amp;', '&', $URLString);
        $query = explode('&', $URLString);
        $newQuery = array();

        for ($q = 0; $q < count($query); $q++) {
            $p = stripos($query[$q], $opt . '=');
            if ($p === false or ($p != 0 and $p === false))
                $newQuery[] = $query[$q];
        }

        if ($url_first_part != '' and count($newQuery) > 0)
            $URLString = $url_first_part . '?' . implode('&', $newQuery);
        elseif ($url_first_part != '' and count($newQuery) == 0)
            $URLString = $url_first_part;
        else
            $URLString = implode('&', $newQuery);

        return $URLString;
    }

    public static function getURLData(string $url): string
    {
        if (function_exists('curl_init')) {
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
            curl_setopt($ch, CURLOPT_URL, $url);

            try {
                $htmlCode = @curl_exec($ch);
            } catch (\Exception $e) {
                $application = Factory::getApplication();
                $application->enqueueMessage(curl_error($e->getMessage()), 'error');
                return '';
            }

            if ($htmlCode === FALSE) {
                $application = Factory::getApplication();
                $application->enqueueMessage(curl_error($ch), 'error');
                return '';
            }

            curl_close($ch);
            return $htmlCode;
        } elseif (ini_get('allow_url_fopen')) {

            try {
                return @file_get_contents($url);
            } catch (Exception $e) {
                return '';
            }
        } else {
            $application = Factory::getApplication();
            $application->enqueueMessage('Cannot load data, enable "allow_url_fopen" or install cURL<br/>'
                . '<a href="https://joomlaboat.com/youtube-gallery/f-a-q/why-i-see-allow-url-fopen-message" target="_blank">Here</a> is what to do.', 'error');

            return '';
        }
    }

    public static function object_to_array($data)
    {
        if (is_array($data) || is_object($data)) {
            $result = array();
            foreach ($data as $key => $value) {
                $result[$key] = YoutubeGalleryLayoutRenderer::object_to_array($value);
            }
            return $result;
        }
        return $data;
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
    public static function CreateParamLine(&$settings): string
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

    public static function PrepareDescription($description, $videoDescriptionParams)
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
        $desc = trim($desc);
        return $desc;
    }
}


