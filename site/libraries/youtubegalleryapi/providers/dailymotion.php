<?php
/**
 * YoutubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

//not finished
class YGAPI_VideoSource_DailyMotion
{
    public static function extractDailyMotionID($theLink)
    {

        if (strpos($theLink, 'dailymotion.com/video/') === false) {
            //https://www.dailymotion.com/au/relevance/search/camp+fire+1/1#video=x16ckln
            $l = explode('#', $theLink);

            if (count($l) > 1) {
                $a = explode('=', $l[1]);

                if (count($a) > 1)
                    return $a[1];
            }
        } else {
            //https://www.dailymotion.com/video/xrcy5b
            $l = explode('/', $theLink);
            if (count($l) > 4) {
                $a = explode('_', $l[4]);
                $b = explode('#', $a[0]);
                return $b[0];
            }
        }

        return '';

    }

    public static function copyVideoData($j, &$blankArray)
    {
        try {
            if (isset($j->error)) {
                if (isset($j->error->errors)) {
                    $e = $j->error->errors[0];
                    $blankArray['es_status'] = $e->code;
                    $blankArray['es_error'] = $e->message;

                    return false;
                }

            }

            $blankArray['es_imageurl'] = $j->thumbnail_small_url . ',80,60;' . $j->thumbnail_medium_url . '160,120';
            $blankArray['es_title'] = $j->title;
            $blankArray['es_description'] = $j->description;
            $blankArray['es_publisheddate'] = date('Y-m-d H:i:s', $j->created_time);
            $blankArray['es_duration'] = $j->duration;

            $blankArray['es_ratingaverage'] = $j->rating;
            $blankArray['es_ratingmax'] = $j->ratings_total;
            $blankArray['es_statisticsviewcount'] = $j->views_total;
        } catch (Exception $e) {
            $blankArray['es_status'] = -2;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Error catched.';
            return false;
        }
        return true;

    }
}
