<?php
/**
 * YouTubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class YGAPI_VideoSource_SoundCloud
{
    public static function extractID($theLink)
    {
        //https://soundcloud.com/sunny2point0/hellokitty

        $l = explode('/', $theLink);
        return $l[3] . '/' . $l[4];

    }

    public static function extractTrackID($theLink)
    {
        // http://api.soundcloud.com/tracks/49931.json

        $l = explode('/', $theLink);

        if (count($l) > 4) {
            $a = explode('.', $l[4]);
            return $a[0];
        }

        return '';


    }

    public static function copyVideoData($j, &$blankArray)
    {
        try {
            $blankArray['es_title'] = $j->title;
            $blankArray['es_description'] = $j->description;

            $blankArray['es_publisheddate'] = $j->created_at;
            $blankArray['es_duration'] = floor($j->duration / 1000);
            $blankArray['es_keywords'] = $j->tag_list;
            $blankArray['es_statisticsviewcount'] = $j->playback_count;
            $blankArray['es_statisticsfavoritecount'] = $j->favoritings_count;
            $blankArray['es_commentcount'] = $j->comment_count;
            $blankArray['es_imageurl'] = $j->artwork_url;

            $u = $j->user;

            $blankArray['es_channelusername'] = $u->username;
            $blankArray['es_channeltitle'] = $u->username;
        } catch (Exception $e) {
            $blankArray['es_status'] = -2;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Error catched.';
            return false;
        }
        return true;
    }
}
