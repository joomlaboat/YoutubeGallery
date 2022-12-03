<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use YouTubeGallery\Helper;
use YouTubeGallery\Pagination;

use Joomla\CMS\Version;

use Joomla\CMS\HTML\HTMLHelper;

class YoutubeGalleryLayoutRenderer
{
    function render($htmlresult, &$videolist_row, &$theme_row, $gallery_list, $width, $height, $videoid, $total_number_of_rows, $custom_itemid = 0)
    {
        if ($theme_row->es_rel == 'jce') {
            $version_object = new Version;
            $version = (int)$version_object->getShortVersion();

            if ($version < 4)
                echo JHTML::_('behavior.modal'); //Modal Loader
            //else
            //echo JHTML::_('behavior.modal'); //Modal Loader
        }


        if (!isset($theme_row))
            return 'Theme not selected';

        if (!isset($videolist_row))
            return 'Video List not selected';

        if (strpos($htmlresult, '[videoplayer') === false and $theme_row->es_rel == '') {
            //[videoplayer] tag
            $htmlresult = '[videoplayer]' . $htmlresult;// if [videoplayer] tag forgotten
        }

        if (strpos($htmlresult, '[pagination') === false)
            $AllowPagination = false;
        else
            $AllowPagination = true;
//,'cols','linestyle','activevideotitlestyle'
        $fields_generated = array('link', 'width', 'height', 'video', 'videolist', 'listname', 'videotitle', 'videodescription', 'videoplayer', 'navigationbar', 'thumbnails', 'count', 'pagination', 'instanceid', 'videoid', 'mediafolder', 'social');
        $fields_theme = array('bgcolor', 'cssstyle', 'navbarstyle', 'thumbnailstyle', 'listnamestyle', 'color1', 'color2', 'descr_style', 'rel', 'hrefaddon');

        $fields_all = array_merge($fields_generated, $fields_theme);

        foreach ($fields_all as $fld) {
            $isEmpty = YoutubeGalleryLayoutRenderer::isEmpty($fld, $videolist_row, $theme_row, $gallery_list, $videoid, $AllowPagination, $total_number_of_rows);

            $ValueOptions = array();
            $ValueList = Helper::getListToReplace($fld, $ValueOptions, $htmlresult, '[]');

            $ifname = '[if:' . $fld . ']';
            $endifname = '[endif:' . $fld . ']';

            if ($isEmpty) {
                foreach ($ValueList as $ValueListItem)
                    $htmlresult = str_replace($ValueListItem, '', $htmlresult);

                do {
                    $textlength = strlen($htmlresult);

                    $startif_ = strpos($htmlresult, $ifname);
                    if ($startif_ === false)
                        break;

                    if (!($startif_ === false)) {

                        $endif_ = strpos($htmlresult, $endifname);
                        if (!($endif_ === false)) {
                            $p = $endif_ + strlen($endifname);
                            $htmlresult = substr($htmlresult, 0, $startif_) . substr($htmlresult, $p);
                        }
                    }

                } while (1 == 1);
            } else {
                $htmlresult = str_replace($ifname, '', $htmlresult);
                $htmlresult = str_replace($endifname, '', $htmlresult);

                $i = 0;
                foreach ($ValueOptions as $ValueOption) {
                    $vlu = $this->getValue($fld, $ValueOption, $videolist_row, $theme_row, $gallery_list, $width, $height, $videoid, $AllowPagination, $total_number_of_rows, $custom_itemid);
                    $htmlresult = str_replace($ValueList[$i], $vlu, $htmlresult);
                    $i++;
                }
            }// IF NOT

            $ifname = '[ifnot:' . $fld . ']';
            $endifname = '[endifnot:' . $fld . ']';

            if (!$isEmpty) {
                foreach ($ValueList as $ValueListItem)
                    $htmlresult = str_replace($ValueListItem, '', $htmlresult);

                do {
                    $textlength = strlen($htmlresult);

                    $startif_ = strpos($htmlresult, $ifname);
                    if ($startif_ === false)
                        break;

                    if (!($startif_ === false)) {
                        $endif_ = strpos($htmlresult, $endifname);
                        if (!($endif_ === false)) {
                            $p = $endif_ + strlen($endifname);
                            $htmlresult = substr($htmlresult, 0, $startif_) . substr($htmlresult, $p);
                        }
                    }

                } while (1 == 1);

            } else {
                $htmlresult = str_replace($ifname, '', $htmlresult);
                $htmlresult = str_replace($endifname, '', $htmlresult);
                $vlu = '';
                $i = 0;
                foreach ($ValueOptions as $ValueOption) {

                    $htmlresult = str_replace($ValueList[$i], $vlu, $htmlresult);
                    $i++;
                }
            }

        }//foreach($fields as $fld)

        return $htmlresult;

    }//function

    public static function isEmpty($fld, &$videolist_row, &$theme_row, $gallery_list, $videoid, $AllowPagination, $total_number_of_rows)
    {
        ///'activevideotitlestyle',
        $fields_theme = array('bgcolor', 'cssstyle', 'navbarstyle', 'thumbnailstyle', 'listnamestyle',
            'color1', 'color2', 'descr_style', 'es_rel', 'hrefaddon');

        if (in_array($fld, $fields_theme)) {
            $theme_row_array = get_object_vars($theme_row);

            $tf = $fld;

            if ($tf == 'color1')
                $tf = 'colorone';
            elseif ($tf == 'color2')
                $tf = 'colortwo';
            elseif ($tf == 'descr_style')
                $tf = 'descrstyle';

            if ($theme_row_array['es_' . $tf] == '')
                return true;
            else
                return false;
        }

        switch ($fld) {
            /*
            case 'cols':
                return false;
                */
            case 'social':
                return false;
                break;
            case 'link':
                return false;
            case 'video':
                return false;
                break;


            case 'videolist':
                return false;
                break;

            case 'listname':
                return false;
                break;

            case 'videotitle':
                return false;
                break;

            case 'videodescription':
                return false;
                break;

            case 'videoplayer':
                return false;
                break;

            case 'navigationbar':
                if ($total_number_of_rows == 0)
                    return true; //hide nav bar
                elseif ($total_number_of_rows > 0)
                    return false;
                break;

            case 'thumbnails':
                if ($total_number_of_rows == 0)
                    return true; //hide nav bar
                elseif ($total_number_of_rows > 0)
                    return false;
                break;

            case 'mediafolder':
                if ($theme_row->es_mediafolder == '')
                    return true;
                else
                    return false;
                break;

            case 'count':
                return ($total_number_of_rows > 0 ? false : true);
                break;

            case 'pagination':
                return ($total_number_of_rows > 5 and $AllowPagination ? false : true);
                break;

            case 'width':
                return false;
                break;

            case 'height':
                return false;
                break;

            case 'instanceid':
                return false;

            case 'videoid':
                return false;

                break;

        }
        return true;
    }

    function getValue($fld, $params, &$videoList_row, &$theme_row, $gallery_list, $width, $height, $videoId, $AllowPagination, $total_number_of_rows, $custom_itemid = 0)//,$title
    {
        $fields_theme = array('bgcolor', 'cssstyle', 'navbarstyle', 'thumbnailstyle',
            'color1', 'color2', 'descr_style', 'rel', 'hrefaddon');

        if (in_array($fld, $fields_theme)) {
            $theme_row_array = get_object_vars($theme_row);

            $tf = $fld;

            if ($tf == 'color1')
                $tf = 'colorone';
            elseif ($tf == 'color2')
                $tf = 'colortwo';
            elseif ($tf == 'descr_style')
                $tf = 'descrstyle';

            return $theme_row_array['es_' . $tf];
        }

        switch ($fld) {
            case 'mediafolder':
                if ($theme_row->es_mediafolder == '')
                    return '';
                else
                    return 'images/' . $theme_row->es_mediafolder;

            case 'videolist':
                if ($params != '') {
                    $pair = explode(',', $params);
                    switch ($pair[0]) {
                        case 'title':
                            return $videoList_row->es_listname;
                        case 'description':
                            return Helper::html2txt($videoList_row->es_description);
                        case 'playlist':
                            $pl = YoutubeGalleryLayoutRenderer::getPlaylistIdsOnly($gallery_list);
                            return implode(',', $pl);
                        case 'watchgroup':
                            return $videoList_row->es_watchusergroup;
                        case 'authorurl':
                            return $videoList_row->es_authorurl;
                        case 'image':
                            return $videoList_row->es_image;
                        case 'note':
                            return $videoList_row->es_note;
                    }
                }

                return $videoList_row->es_listname;

            case 'listname':
                return $videoList_row->es_listname;

            case 'videotitle':

                $TitleByVideoID = YouTubeGalleryGalleryList::getTitleByVideoID($videoId, $gallery_list) ?? '';
                $title = str_replace('"', '_quote_', $TitleByVideoID);

                if ($params != '') {
                    $pair = explode(',', $params);
                    $words = (int)$pair[0];
                    if (isset($pair[1]))
                        $chars = (int)$pair[1];
                    else
                        $chars = 0;

                    $title = Helper::html2txt($title);
                    $title = Helper::PrepareDescription_($title, $words, $chars);
                }
                return '<div id="YoutubeGalleryVideoTitle' . $videoList_row->id . '">' . $title . '</div>';

            case 'videodescription':
                $DescriptionByVideoID = YouTubeGalleryGalleryList::getDescriptionByVideoID($videoId, $gallery_list) ?? '';
                $description = str_replace('"', '&quot;', $DescriptionByVideoID);
                $description = Helper::html2txt($description);

                if ($params != '') {
                    $videoDescription_params = explode(',', $params);
                    $description = Helper::PrepareDescription($description, $videoDescription_params);
                }
                return '<div id="YoutubeGalleryVideoDescription' . $videoList_row->id . '">' . $description . '</div>';

            case 'videoplayer':
                $pair = explode(',', $params);

                if ($params != '')
                    $playerWidth = (int)$pair[0];
                else
                    $playerWidth = $width;

                if (isset($pair[1]))
                    $playerHeight = (int)$pair[1];
                else
                    $playerHeight = $height;

                $containerStyle = 'width:' . $playerWidth . 'px;height:' . $playerHeight . 'px;';

                if ((int)$theme_row->es_playvideo == 0)
                    $containerStyle .= 'display:none;';
                else
                    $containerStyle .= 'display:block;';

                return '<div id="YoutubeGallerySecondaryContainer' . $videoList_row->id . '" style="' . $containerStyle . '"></div>';

            case 'navigationbar': //Obselete
                return YoutubeGalleryLayoutThumbnails::NavigationList($gallery_list, $videoList_row, $theme_row, $videoId, $custom_itemid);

            case 'thumbnails':
                return YoutubeGalleryLayoutThumbnails::NavigationList($gallery_list, $videoList_row, $theme_row, $videoId, $custom_itemid);

            case 'count':
                if ($params == 'all')
                    return $videoList_row->TotalVideos;
                else
                    return count($gallery_list);

            case 'pagination':
                return Pagination::Pagination($theme_row, $gallery_list, $width, $total_number_of_rows);

            case 'width':
                return $width;

            case 'height':
                return $height;

            case 'instanceid':
                return $videoList_row->id;

            case 'videoid':
                return $videoId;

            case 'link':
                return $link = Helper::full_url($_SERVER);

            case 'social':
                return YoutubeGallerySocialButtons::SocialButtons('window.location.href', 'yg', $params, $videoList_row->id, $videoId);

            case 'video':

                $pair = Helper::csv_explode(':', $params, '"', false);

                if ($pair[0] != "") {
                    $options = '';
                    if (isset($pair[1]))
                        $options = $pair[1];

                    $tableFields = array('title', 'description',
                        'imageurl', 'videoid', 'videosource', 'publisheddate', 'duration',
                        'rating_average', 'rating_max', 'rating_min', 'rating_numRaters',
                        'keywords', 'commentcount', 'likes', 'dislikes', 'playlist');


                    $listitem = YouTubeGalleryGalleryList::getVideoRowByID($videoId, $gallery_list);

                    if ($listitem)
                        return YoutubeGalleryLayoutRenderer::getTumbnailData($pair[0], "", "", $listitem, $tableFields, $options, $theme_row, $gallery_list, $videoList_row);
                    else
                        return '';
                }
                break;
        }
    }
}
