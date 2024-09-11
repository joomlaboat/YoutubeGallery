<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

namespace YouTubeGallery;

defined('_JEXEC') or die('Restricted access');

use \Joomla\CMS\Factory;

//use \YouTubeGalleryRenderer;

class RendererCSS// extends YouTubeGalleryRenderer
{
    public static function renderCSS(&$theme_row, $instance_id)
    {
        $headscript_css = '
<style>';

        if ($theme_row->es_useglass) {
            $headscript_css .= '
.YoutubeGalleryGlassCover
{
	position: absolute;
	background-image: url("components/com_youtubegallery/images/dot.png");
	top:0px;
	left:0px;
	width:100%;
	height:100%;
	margin-top:0px;
	margin-left:0px;
	padding:0px;
}
';
        }

        if ($theme_row->es_responsive == 2)
            $headscript_css .= RendererCSS::getResponsiveCode_CSS($instance_id);

        $headscript_css .= '
</style>';

        $document = Factory::getDocument();
        $document->addCustomTag($headscript_css);
    }

    protected static function getResponsiveCode_CSS($instance_id)
    {
        //CSS for making YouTubeGallery player responsive (without javascript):
        $result = '

div#YoutubeGalleryMainContainer' . $instance_id . ' {
width: 100% !important;
}
div#YoutubeGallerySecondaryContainer' . $instance_id . ', .YoutubeGalleryLogoCover' . $instance_id . '{
position: relative !important;
width: 100% !important;
height: 0 !important; padding-bottom: 56.25% !important; /* 16:9 */

}


div#YoutubeGallerySecondaryContainer' . $instance_id . ' iframe {
position: absolute !important;
top: 0 !important;
left: 0 !important;
width: 100% !important;
height: 100% !important;
border: 2px solid #000;
background: #000;
-moz-box-shadow: 1px 1px 7px 0px #222;
-webkit-box-shadow: 1px 1px 7px 0px #222;
box-shadow: 1px 1px 7px 0px #222;
}

div#YoutubeGallerySecondaryContainer' . $instance_id . ' object{
width: 100% !important;
height: 100% !important;

position: absolute !important;
top: 0 !important;
left: 0 !important;
width: 100% !important;
height: 100% !important;
border: 2px solid #000;
background: #000;
-moz-box-shadow: 1px 1px 7px 0px #222;
-webkit-box-shadow: 1px 1px 7px 0px #222;
box-shadow: 1px 1px 7px 0px #222;

}
	';
        return $result;
    }
}
