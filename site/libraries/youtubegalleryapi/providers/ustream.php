<?php
/**
 * YoutubeGallery API
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use YouTubeGallery\Helper;

defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

class YGAPI_VideoSource_Ustream
{
    public static function extractUstreamID($theLink)
    {
        //Recorded
        //https://www.ustream.tv/channel/nasa-tv-wallops
        //https://www.ustream.tv/recorded/40925310 - recorded

        //Live
        //https://www.ustream.tv/channel/live-iss-stream
        //https://www.ustream.tv/channel/95.0.02
        $l = explode('/', $theLink);
        if (count($l) > 4)
            return $l[4];

        return '';
    }

    public static function getVideoData($videoid, &$blankArray)
    {
        $HTML_SOURCE = Helper::getURLData('https://www.ustream.tv/recorded/' . $videoid);

        if ($HTML_SOURCE == '' or $HTML_SOURCE[0] != '<') {
            $blankArray['es_status'] = -1;
            $blankArray['es_error'] = 'Return data is empty or not HTML.';
            $blankArray['es_rawdata'] = null;//$HTML_SOURCE;
            return false;
        }

        $blankArray['es_title'] = YouTubeGalleryAPIMisc::getValueByAlmostTag($HTML_SOURCE, '<meta property="og:title" content="');
        $blankArray['es_description'] = YouTubeGalleryAPIMisc::getValueByAlmostTag($HTML_SOURCE, '<meta property="og:description" content="');

        $theImage = YouTubeGalleryAPIMisc::getValueByAlmostTag($HTML_SOURCE, '<meta property="og:image" content="');
        $theImage = str_replace(',', '%2C', $theImage) . ',640,360';
        $blankArray['es_imageurl'] = $theImage;
        $blankArray['es_publisheddate'] = YouTubeGalleryAPIMisc::getValueByAlmostTag($HTML_SOURCE, '<span data-dateformat="%F %j at %g:%i%a" data-timestamp="');
        return true;

    }
}
