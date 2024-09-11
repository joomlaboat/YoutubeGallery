<?php
//use Vimeo\Vimeo;

/**
 * YouTubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');


class YGAPI_VideoSource_Vimeo
{
    public static function extractVimeoID($theLink)
    {

        preg_match('/http:\/\/vimeo.com\/(\d+)$/', $theLink, $matches);
        if (count($matches) != 0) {
            $vimeo_id = $matches[1];

            return $vimeo_id;
        } else {
            preg_match('/https:\/\/vimeo.com\/(\d+)$/', $theLink, $matches);
            if (count($matches) != 0) {
                $vimeo_id = $matches[1];
                return $vimeo_id;
            }
        }

        return '';
    }

    public static function getVideoData($videoid, &$blankArray)
    {
        $consumer_key = YouTubeGalleryAPIMisc::APIKey_Vimeo_Consumer_Key();
        $consumer_secret = YouTubeGalleryAPIMisc::APIKey_Vimeo_Consumer_Secret();
        $oauth_access_token = YouTubeGalleryAPIMisc::APIKey_Vimeo_Oauth_Access_Token();

        require_once('Vimeo' . DIRECTORY_SEPARATOR . 'Vimeo.php');

        $session = Factory::getSession();
        if (!isset($session))
            session_start();

        if ($oauth_access_token == '') {
            if ($session->get('oauth_access_token') != '')
                $oauth_access_token = $session->get('oauth_access_token');
        }

        if ($session->get('oauth_access_token_secret') != '')
            $oauth_access_token_secret = $session->get('oauth_access_token_secret');
        else
            $oauth_access_token_secret = '';

        $vimeo = new Vimeo($consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret);

        $fields_desired = implode(',', array(
            'name',
            'description',
            'pictures',
            'stats',
            'tags',
            'metadata',
            'created_time',
            'duration'
        ));

        $a = array('fields' => $fields_desired,
            'sort' => 'date',
            'filter' => 'embeddable',
            'filter_embeddable' => 'true');

        $video_info = $vimeo->request('/videos/' . $videoid, $a, 'GET', true);
        $blankArray['es_datalink'] = '/videos/' . $videoid;

        if (!isset($video_info['body'])) {
            $blankArray['es_status'] = -2;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Video not Found or Permission Denied.';
            $blankArray['es_rawdata'] = null;

            return false;
        }

        if (isset($video_body['error']) and $video_body['error'] != "") {
            $blankArray['es_status'] = -1;
            $blankArray['es_error'] = $video_body['error'];
            $blankArray['es_rawdata'] = null;
        }

        $video_body = $video_info['body'];

        //-------------
        $blankArray['es_title'] = $video_body['name'];
        $blankArray['es_description'] = $video_body['description'];
        $blankArray['es_publisheddate'] = $video_body['created_time'];
        $blankArray['es_duration'] = $video_body['duration'];
        $blankArray['es_statisticsfavoritecount'] = $video_body['metadata']['connections']['likes']['total'];
        $blankArray['es_statisticsviewcount'] = $video_body['stats']['plays'];

        $images = array();
        foreach ($video_body['pictures']['sizes'] as $image)
            $images[] = $image['link'] . ',' . $image['width'] . ',' . $image['height'];

        $blankArray['es_imageurl'] = implode(';', $images);

        $keywords = array();

        if (isset($video_body['tags'])) {
            foreach ($video_body['tags'] as $tag)
                $keywords[] = $tag['tag'];
        }

        $blankArray['es_keywords'] = implode(',', $keywords);

        return true;
    }
}
