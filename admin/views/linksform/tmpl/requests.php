<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

$jinput = Factory::getApplication()->input;
$task = $jinput->getCmd('task', '');

switch ($task) {
    case 'resolvesoundcloudlink' :
        $client_id = $jinput->getCmd('client_id', '');
        if ($client_id == '') {
            echo 'soundcloud api client id not set (go to "settings")';
            break;
        }

        $url = $jinput->getCmd('url', '');
        if ($url == '') {
            echo 'url not set';
            break;
        }

        $theUrl = 'http://api.soundcloud.com/resolve.json?url=' . urlencode($url) . '&client_id=' . $client_id;

        $html = Helper::getURLData($theUrl);

        if (!str_contains($html, '{"')) {
            echo 'please check this link: https://joomlaboat.com/youtube-gallery/f-a-q/why-i-see-allow-url-fopen-message?cbprofile=2';
            break;
        }

        $track = '';
        $j = json_decode($html);
        //echo 't='.$j->track.'<br/>';
        //echo 'l='.$j->location.'<br/>';

        if ($j->track != '') {
            $track = $j->track;

        } elseif ($j->location != '') {

            $p = explode('/', $j->location);

            if (isset($p[3]) and isset($p[4]) and $p[3] == 'tracks') {
                $a = explode('.', $p[4]);
                $track = $a[0];
            }

        }

        echo json_encode(array('id' => $track, 'kind' => 'track'));
        //die;

        break;


    case 'getyoutubeshowowner' :
        $link = $jinput->getCmd('link', '');
        if ($link != '')
            echo getYoutubeGalleryShowOwner($link);
        else
            echo 'link parameter not set';
        break;

    case 'getyoutubeshowsbyowner' :

        $username = $jinput->getCmd('owner', '');
        if ($username != '') {
            $username = trim(preg_replace("/[^a-zA-Z0-9_]/", "", $username));

            $max_results = $jinput->getInt('max-results', 10);
            $start_index = $jinput->getInt('start-index', 1);

            $arr = getYoutubeShowsByUser($username, $max_results, $start_index);
            echo json_encode($arr);
        } else
            echo 'owner parameter not set';
        break;

    case 'getyoutubeseasonsbyshowid' :

        $showid = $jinput->getCmd('showid', '');
        if ($showid != '') {

            $showid = trim(preg_replace("/[^a-zA-Z0-9_]/", "", $showid));

            $max_results = $jinput->getInt('max-results', 10);
            $start_index = $jinput->getInt('start-index', 1);


            $arr = getYoutubeSeasonsByShowID($showid, $max_results, $start_index);
            echo json_encode($arr);
        } else
            echo 'owner parameter not set';
        break;

    case 'getyoutubeshowownershows' :
        $link = $jinput->getCmd('link', '');
        if ($link) {
            $u = getYoutubeGalleryShowOwner($link);

            $max_results = $jinput->getInt('max-results', 10);
            $start_index = $jinput->getInt('start-index', 1);


            $ua = json_decode($u);
            if ($ua == false)
                echo $ua;

            $arr = getYoutubeShowsByUser($ua->username, $max_results, $start_index);
        } else
            echo 'link parameter not set';

        break;
    case '':
        echo '<html><body></body></html>';
        break;

    default:
        echo 'unknown task';
}


function getYoutubeSeasonsByShowID($showid, $max_results, $start_index)
{
    if ($max_results == 0)
        $max_results = 10;

    if ($start_index == 0)
        $start_index = 1;

    $url = 'http://gdata.youtube.com/feeds/api/shows/' . $showid . '/content?v=2&max-results=' . $max_results . '&start-index=' . $start_index;

    $a = Helper::getURLData($url);
    if ($a == '')
        return 'cannot load seasons page';

    if (!str_contains($a, '<?xml version'))
        return 'Cannot load data, no connection';


    $xml = simplexml_load_string($a);


    if ($xml) {

        $arr = array();
        foreach ($xml->entry as $entry) {
            $p = explode(':', $entry->id);
            if (count($p) == 6) {
                $id = $p[5];
                $arr[] = array('id' => $id, 'title' => $entry->title);
            }
        }

        return $arr;
    }
    return 'xml format corrupted';
}

function getYoutubeShowsByUser($username, $max_results, $start_index)
{
    if ($max_results == 0)
        $max_results = 10;

    if ($start_index == 0)
        $start_index = 1;

    $url = 'http://gdata.youtube.com/feeds/api/users/' . $username . '/shows?v=2&max-results=' . $max_results . '&start-index=' . $start_index;

    $a = Helper::getURLData($url);
    if ($a == '')
        return 'cannot load user shows page';

    if (!str_contains($a, '<?xml version'))
        return 'Cannot load data, no connection';

    $xml = simplexml_load_string($a);

    if ($xml) {
        $arr = array();
        foreach ($xml->entry as $entry) {
            $alternate_link = '';
            foreach ($entry->link as $link) {
                $l = $link->attributes();
                if ($l['rel'] == 'alternate') {
                    $alternate_link = $l['href'];
                    break;
                }
            }
            $arr[] = array('id' => $entry->id, 'title' => $entry->title, 'link' => $alternate_link);
        }
        return $arr;
    }
    return 'xml format corrupted';

}

function getYoutubeGalleryShowOwner($url)
{
    if (!str_contains($url, '://www.youtube.com/show/'))
        return 'wrong link format';

    $a = Helper::getURLData($url);
    if ($a == '')
        return 'cannot load show page';

    $gdata = getValueOfParameter($a, '<link rel="alternate" type="application/rss+xml" title="RSS" href="');

    if (!$gdata)
        return 'cannot find the owner';

    $username = getValueOfParameter($a, 'http://gdata.youtube.com/feeds/base/users/', '/');

    if (!$gdata)
        return 'cannot find username';

    $arr = array('username' => $username);

    return json_encode($arr);


}

//---------------- useful functions

function getValueOfParameter($r, $p, $f = '"')
{
    $i = strpos($r, $p);
    if ($i === false)
        return false;

    $l = strlen($p);
    $a = strpos($r, $f, $i + $l);
    if ($a === false)
        return false;

    return substr($r, $i + $l, $a - $i - $l);

}

function html2txt($document)
{
    $search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
        '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
        '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
        '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
    );
    $text = preg_replace($search, '', $document);
    return $text;
}
