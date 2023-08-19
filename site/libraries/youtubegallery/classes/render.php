<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

jimport('joomla.application.component.view');

class YouTubeGalleryRenderer
{
    public static function SetHeaderTags(&$theme_row, $pl): void
    {
        if (count($pl) == 0)
            return;

        $parts = explode('*', $pl[0]);
        $videoId = $parts[0];
        $videoSource = $parts[2];

        $VideoRow = YoutubeGalleryDB::getVideoRowByID($videoId);
        if (!$VideoRow)
            return;

        $myDocument = Factory::getDocument();

        if ($theme_row->es_changepagetitle != 3) {
            $mainframe = Factory::getApplication();
            $sitename = $mainframe->getCfg('sitename');

            $title = $VideoRow['es_title'];

            if ($theme_row->es_changepagetitle == 0)
                $myDocument->setTitle($title . ' - ' . $sitename);
            elseif ($theme_row->es_changepagetitle == 1)
                $myDocument->setTitle($sitename . ' - ' . $title);
            elseif ($theme_row->es_changepagetitle == 2)
                $myDocument->setTitle($title);
        }

        //add meta keywords

        if ((int)$theme_row->es_prepareheadtags != 0 and $videoSource == 'youtube') {
            $myDocument->setMetaData('keywords', Helper::html2txt($VideoRow['es_keywords']));//tags
            $description_ = str_replace('*email*', '@', Helper::html2txt($VideoRow['es_description']));
            $myDocument->setMetaData('description', $description_);
        }

        $image_link = $VideoRow['es_imageurl'];

        if ($theme_row->es_prepareheadtags == 2 or $theme_row->es_prepareheadtags == 3) {
            if ($image_link != '' and !str_contains($image_link, '#')) {
                $curPageUrl = Helper::curPageURL();

                $image_link_array = explode(',', $image_link);
                if (count($image_link_array) >= 3)
                    $imagelink = $image_link_array[3];
                else
                    $imagelink = $image_link_array[0];

                $imagelink = (!str_contains($imagelink, 'http://') and !str_contains($image_link, 'https://') ? $curPageUrl . '/' : '') . $imagelink;

                if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
                    $imagelink = str_replace('http://', 'https://', $imagelink);

                $myDocument->addCustomTag('<link rel="image_src" href="' . $imagelink . '" /><!-- active -->');
            }
        }
    }

    function render(array &$gallery_list, object &$videoListRow, &$theme_row, $total_number_of_rows, $videoid, $custom_itemid = 0): string
    {
        $width = $theme_row->es_width;
        if ($width == 0)
            $width = 400;

        $height = $theme_row->es_height;
        if ($height == 0)
            $height = 300;

        //Head Script
        YouTubeGalleryRenderer::setHeadScript($theme_row, $videoListRow->id, $width, $height);
        YoutubeGalleryHotPlayer::addHotReloadScript($gallery_list, $width, $height, $videoListRow, $theme_row);

        $result = '
<a name="youtubegallery"></a>
<div id="YoutubeGalleryMainContainer' . $videoListRow->id . '" style="position: relative;display:block;'
            . ((int)$theme_row->es_width != 0 ? 'width:' . $width . 'px;' : '') . ($theme_row->es_cssstyle != '' ? $theme_row->es_cssstyle . ';' : '') . '">
';
        $LayoutRenderer = new YoutubeGalleryLayoutRenderer;

        $jinput = Factory::getApplication()->input;
        if ($theme_row->es_rel != '' and $jinput->getCmd('tmpl') != '')
            $layoutcode = '[videoplayer]'; // Shadow box
        else
            $layoutcode = $theme_row->es_customlayout;

        $result .= $LayoutRenderer->render($layoutcode, $videoListRow, $theme_row, $gallery_list, $width, $height, $videoid, $total_number_of_rows, $custom_itemid);

        $result .= '
</div>
';
        return $result;
    }

    protected static function setHeadScript(object &$theme_row, string $instance_id, string $width, string $height): void
    {
        if ($theme_row->es_headscript === null)
            return;

        $headScript = $theme_row->es_headscript;

        if ($headScript != '') {
            $headScript = str_replace('[instanceid]', $instance_id, $headScript);
            $headScript = str_replace('[width]', $width, $headScript);
            $headScript = str_replace('[height]', $height, $headScript);
            $headScript = str_replace('[mediafolder]', 'images/' . $theme_row->es_mediafolder, $headScript);

            $fields_theme = array('es_bgcolor', 'es_cssstyle', 'es_navbarstyle', 'es_thumbnailstyle', 'es_listnamestyle', 'es_colorone',
                'es_colortwo', 'es_descrstyle', 'es_rel', 'es_hrefaddon', 'es_mediafolder');

            $theme_row_array = get_object_vars($theme_row);

            foreach ($fields_theme as $fld) {
                if ($theme_row_array[$fld] !== null)
                    $headScript = str_replace('[' . $fld . ']', $theme_row_array[$fld], $headScript);
            }

            $document = Factory::getDocument();
            $document->addCustomTag($headScript);
        }

        YouTubeGallery\RendererCSS::renderCSS($theme_row, $instance_id);

        if ($theme_row->es_responsive == 1)
            YouTubeGalleryRendererJS::getResponsiveCode_JS($instance_id, $width, $height);
    }
}
