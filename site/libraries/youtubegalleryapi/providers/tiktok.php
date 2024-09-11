<?php
/**
 * YouTubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

class YGAPI_VideoSource_TikTok
{
    public static function extractTikTokID($theLink)
    {
        //https://www.tiktok.com/@user/video/6793525112617454853
        //https://www.tiktok.com/@marelissahim/video/6804413992514161925
        $l = explode('/', $theLink);
        if (count($l) > 5)
            return $l[5];

        return '';

    }

    public static function copyVideoData($j, &$blankArray)
    {
        try {
            if (isset($j->error)) {
                if (isset($j->error->errors)) {
                    $e = $j->error->errors[0];

                    $blankArray['es_status'] = -1;
                    $blankArray['es_error'] = strip_tags($e->message);

                    return false;
                }

            }

            $pos = strpos($j->title, '#');

            if ($pos !== false)
                $blankArray['es_title'] = substr($j->title, 0, $pos);
            else
                $blankArray['es_title'] = $j->title;

            $blankArray['es_imageurl'] = $j->thumbnail_url . ',540,960';
            $blankArray['es_channeltitle'] = $j->author_name;

            if ($pos !== false) {
                $tags = array();
                $tArray = explode('#', substr($j->title, $pos));
                foreach ($tArray as $t_) {
                    $t = trim($t_);
                    if ($t != '')
                        $tags[] = $t;
                }
                $blankArray['keywords'] = implode(',', $tags);
            }
        } catch (Exception $e) {
            $blankArray['es_status'] = -2;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Error catched.';
            return false;
        }
        return true;
    }
}
