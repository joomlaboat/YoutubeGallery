<?php
/**
 * YoutubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');


require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

class YGAPI_VideoSource_VimeoChannel
{
    public static function getVideoIDList($vimeo_user_link, $optionalparameters)
    {

        $videolist = array();
        $optionalparameters_arr = explode(',', $optionalparameters);

        $channel_id = YGAPI_VideoSource_VimeoChannel::extractVimeoUserID($vimeo_user_link);


        //-------------- prepare our Consumer Key and Secret
        require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

        $consumer_key = Helper::getSettingValue('vimeo_api_client_id');
        $consumer_secret = Helper::getSettingValue('vimeo_api_client_secret');

        if ($consumer_key == '' or $consumer_secret == '') {
            $application = Factory::getApplication();
            $application->enqueueMessage('Consumer_key or consumer_secret not set.', 'error');

            return $videolist;
        }
        //--------------

        //require_once('vimeo_api.php');
        require_once('Vimeo/Vimeo.php');

        $session = Factory::getSession();
        if (!isset($session))
            session_start();


        if (null !== $session->get('oauth_access_token'))//if(isset($session->get('oauth_access_token')))
            $oauth_access_token = $session->get('oauth_access_token');
        else
            $oauth_access_token = '';

        if (null !== $session->get('oauth_access_token_secret'))//if(isset($session->get('oauth_access_token_secret')))
            $oauth_access_token_secret = $session->get('oauth_access_token_secret');
        else
            $oauth_access_token_secret = '';

        $vimeo = new Vimeo($consumer_key, $consumer_secret, $oauth_access_token, $oauth_access_token_secret);

        $params = array();
        foreach ($optionalparameters_arr as $p) {
            $pair = explode('=', $p);
            if ($pair[0] == 'page')
                $params['page'] = (int)$pair[1];

            if ($pair[0] == 'per_page')
                $params['per_page'] = (int)$pair[1];
        }


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


        $video_info = $video_info = $vimeo->request('/channels/' . $channel_id . '/videos', $a, 'GET', true);

        $video_body = $video_info['body'];

        if (isset($video_body)) {
            if (!$video_body)
                return $videolist;

            foreach ($video_body['data'] as $video) {
                $uri = $video['uri'];

                if (str_contains($uri, '/videos/') and !str_contains($uri, '/channels/')) {
                    $video_id = str_replace('/videos/', '', $uri);
                    $videolist[] = 'https://vimeo.com/' . $video_id;
                }
            }
        }

        return $videolist;

    }

    public static function extractVimeoUserID($vimeo_user_link)
    {
        //http://vimeo.com/channels/431663
        //http://vimeo.com/channels/489067
        //http://vimeo.com/channels/ahrcpitssatsplitscreen
        $matches = explode('/', $vimeo_user_link);

        if (count($matches) > 4) {
            if ($matches[3] != 'channels')
                return ''; //not a channel link

            return $matches[4];

        }

        return '';
    }


}
