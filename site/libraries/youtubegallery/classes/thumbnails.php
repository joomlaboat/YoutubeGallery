<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

class YoutubeGalleryLayoutThumbnails
{
    public static function NavigationList($the_gallery_list, &$videoList_row, &$theme_row, $videoId, $custom_itemid = 0): string
    {
        $catalogResult = '';
        $gallery_list = $the_gallery_list;

        $count = 0;
        $item_index = 1;
        $isForShadowBox = false;
        if (isset($theme_row)) {
            if ($theme_row->es_rel != '')
                $isForShadowBox = true;
        }

        $thumbnail_item = '';

        foreach ($gallery_list as $listItem) {

            if ($listItem['es_title'] !== null and !str_contains($listItem['es_title'], '***Video not found***')) {

                if (!$isForShadowBox and ($theme_row->es_openinnewwindow == 4 or $theme_row->es_openinnewwindow == 5)) {
                    $aLink = 'javascript:youtubeplayer' . $videoList_row->id . '.HotVideoSwitch(\'' . $videoList_row->id . '\',\'' . $listItem['es_videoid']
                        . '\',\'' . $listItem['es_videosource'] . '\',' . $listItem['id'] . ')';
                } else
                    $aLink = YoutubeGalleryLayoutThumbnails::makeLink($listItem, $theme_row->es_rel, $videoList_row->id, $theme_row->id, $custom_itemid);

                if ($isForShadowBox and $theme_row->es_rel != '')
                    $aLink .= '&tmpl=component';

                if ($theme_row->es_hrefaddon != '' and $theme_row->es_openinnewwindow != 4 and $theme_row->es_openinnewwindow != 5) {
                    $hrefAddon = str_replace('?', '', $theme_row->es_hrefaddon);
                    if ($hrefAddon[0] == '&')
                        $hrefAddon = substr($hrefAddon, 1);

                    if (!str_contains($aLink, $hrefAddon)) {

                        if (!str_contains($aLink, '?'))
                            $aLink .= '?';
                        else
                            $aLink .= '&';

                        $aLink .= $hrefAddon;
                    }
                }

                if ($theme_row->es_openinnewwindow != 4 and $theme_row->es_openinnewwindow != 5) {
                    if (!str_contains($aLink, '&amp;'))
                        $aLink = str_replace('&', '&amp;', $aLink);

                    $aLink = $aLink . (($theme_row->es_openinnewwindow == 2 or $theme_row->es_openinnewwindow == 3) ? '#youtubegallery' : '');
                }

                //to apply to shadowbox
                //do not route the link
                if ($theme_row->es_rel == '') {
                    $aHrefLink = '<a href="' . $aLink . '"' . (($theme_row->es_openinnewwindow == 1 or $theme_row->es_openinnewwindow == 3) ? ' target="_blank"' : '') . '>';
                } else {
                    if ($theme_row->es_rel == 'jce')
                        $aHrefLink = '<a href="' . $aLink . '" class="modal">';
                    else
                        $aHrefLink = '<a href="' . $aLink . '" rel="' . $theme_row->es_rel . '">';
                }

                $thumbnail_item = YoutubeGalleryLayoutThumbnails::renderThumbnailForNavBar($aHrefLink, $aLink, $videoList_row, $theme_row, $listItem, $videoId, $item_index, $gallery_list);

                if ($thumbnail_item != '') {

                    $catalogResult .= '<div id="youtubegallery_thumbnail_' . $videoList_row->id . '_' . $count . '" style="display:contents;">'
                        . '<div id="youtubegallery_thumbnail_box_' . $videoList_row->id . '_' . $listItem['id'] . '" class="ygThumb-inactive" style="display:contents;">'
                        . $thumbnail_item . '</div></div>';
                    $count++;
                }
                $item_index++;
            } else
                $thumbnail_item = '';
        }//for

        if ($count < abs($theme_row->es_customlimit)) {
            for ($i = $count + 1; $i <= $theme_row->es_customlimit; $i++) {//'.$thumbnail_item.'
                $catalogResult .= '<div id="youtubegallery_thumbnail_' . $videoList_row->id . '_' . $i . '" style="display:none;">' . $thumbnail_item . '</div>'; //placeholder for thumbnail
            }
        }

        return '<div id="youtubegallery_thumbnails_' . $videoList_row->id . '">' . $catalogResult . '</div>';
    }

    public static function makeLink(array &$listItem, $rel, $videoListRowId, $theme_row_id, $custom_itemid = 0)
    {
        $videoId = $listItem['es_videoid'];
        $theView = 'youtubegallery';

        $juri = new JURI();
        $WebsiteRoot = $juri->root();

        if ($WebsiteRoot[strlen($WebsiteRoot) - 1] != '/') //Root must have slash / in the end
            $WebsiteRoot .= '/';

        $URLPath = $_SERVER['REQUEST_URI']; // example:  /index.php'

        $pattern = '/[^\pL\pN$-_.+!*\'\(\)\,\{\}\|\\\\\^\~\[\]\<\>\#\%\"\;\/\?\:\@\&\=\.]/u';
        $URLPath = preg_replace($pattern, '', $URLPath);
        $URLPath = preg_replace('/"(\n.)+?"/m', '', $URLPath);
        $URLPath = str_replace('"', '', $URLPath);

        if ($URLPath != '') {
            $p = strpos($URLPath, '?');
            if (!($p === false))
                $URLPath = substr($URLPath, 0, $p);
        }

        $URLPathSecondPart = '';

        if ($URLPath != '') {
            //Path (URI) must be without leadint /
            if ($URLPath != '') {
                if ($URLPath[0] != '/')
                    $URLPath = '' . $URLPath;

            }


        }//if($URLPath!='')

        if ($custom_itemid != 0) {
            //For Shadow/Light Boxes
            $aLink = $WebsiteRoot . 'index.php?option=com_youtubegallery&view=' . $theView;
            $aLink .= '&Itemid=' . $custom_itemid;
            $aLink .= '&videoid=' . $videoId;
            $aLink = JRoute::_($aLink);

            return $aLink;
        } elseif ($rel != '') {
            //For Shadow/Light Boxes
            $aLink = $WebsiteRoot . 'index.php?option=com_youtubegallery&view=' . $theView;
            $aLink .= '&listid=' . $videoListRowId;
            $aLink .= '&themeid=' . $theme_row_id;
            $aLink .= '&videoid=' . $videoId;

            return $aLink;

        }
        /////////////////////////////////

        if (Factory::getApplication()->input->getCmd('option') == 'com_youtubegallery' and Factory::getApplication()->input->getCmd('view') == $theView) {
            //For component only

            $aLink = 'index.php?option=com_youtubegallery&view=' . $theView . '&Itemid=' . Factory::getApplication()->input->getInt('Itemid', 0);

            $aLink .= '&videoid=' . $videoId;

            $aLink = JRoute::_($aLink);

            if (strpos($aLink, 'ygstart') === false and Factory::getApplication()->input->getInt('ygstart') != 0) {
                if (strpos($aLink, '?') === false)
                    $aLink .= '?ygstart=' . Factory::getApplication()->input->getInt('ygstart');
                else
                    $aLink .= '&ygstart=' . Factory::getApplication()->input->getInt('ygstart');
            }

            return $aLink;
        }


        /////////////////////////////////

        $URLQuery = $_SERVER['QUERY_STRING'];
        $URLQuery = str_replace('"', '', $URLQuery);


        $URLQuery = Helper::deleteURLQueryOption($URLQuery, 'videoid');

        $URLQuery = Helper::deleteURLQueryOption($URLQuery, 'onclick');
        $URLQuery = Helper::deleteURLQueryOption($URLQuery, 'onmouseover');
        $URLQuery = Helper::deleteURLQueryOption($URLQuery, 'onmouseout');
        $URLQuery = Helper::deleteURLQueryOption($URLQuery, 'onmouseeenter');
        $URLQuery = Helper::deleteURLQueryOption($URLQuery, 'onmousemove');
        $URLQuery = Helper::deleteURLQueryOption($URLQuery, 'onmouseleave');

        $aLink = $URLPath . $URLPathSecondPart;


        $aLink .= ($URLQuery != '' ? '?' . $URLQuery : '');


        if (!str_contains($aLink, '?'))
            $aLink .= '?';
        else
            $aLink .= '&';

        $allowSEF = YouTubeGalleryDB::getSettingValue('allowsef');
        if ($allowSEF == 1) {
            $aLink = Helper::deleteURLQueryOption($aLink, 'video');
            $aLink .= 'video=' . $listItem['es_alias'];
        } else
            $aLink .= 'videoid=' . $videoId;

        if (!str_contains($aLink, 'ygstart') and Factory::getApplication()->input->getInt('ygstart') != 0)
            $aLink .= '&ygstart=' . Factory::getApplication()->input->getInt('ygstart');

        return JRoute::_($aLink);
    }

    public static function renderThumbnailForNavBar($aHrefLink, $aLink, $videoListRow, &$theme_row, array $listItem, $videoId, $item_index, &$gallery_list)
    {
        //------------------------------- title
        $thumbTitle = '';
        if ($listItem['es_title'] != '') {
            $thumbTitle = str_replace('"', '', $listItem['es_title']);
            $thumbTitle = str_replace('\'', '&rsquo;', $thumbTitle);

            if (!str_contains($thumbTitle, '&amp;'))
                $thumbTitle = str_replace('&', '&amp;', $thumbTitle);
        }

        if ($theme_row->es_customnavlayout != '') {
            $result = YoutubeGalleryLayoutThumbnails::renderThumbnailLayout($theme_row->es_customnavlayout,
                $listItem, $aHrefLink, $aLink, $videoId, $theme_row, $item_index, $gallery_list, $videoListRow);
        } else {
            $thumbnail_layout = '[a][image][/a]'; //with link

            if ($thumbTitle != '')
                $thumbnail_layout .= '<br/>' . ($theme_row->es_thumbnailstyle == '' ? '<span style="font-size: 8pt;" >[title]</span>' : '<div style="' . $theme_row->es_thumbnailstyle . '">[title]</div>');

            $result = YoutubeGalleryLayoutThumbnails::renderThumbnailLayout($thumbnail_layout, $listItem, $aHrefLink, $aLink, $videoId, $theme_row, $item_index, $gallery_list, $videoListRow);
        }

        return $result;
    }

    public static function renderThumbnailLayout($thumbnail_layout, array $listItem, $aHrefLink, $aLink, $videoId, &$theme_row, $item_index, &$gallery_list, &$videolist_row)
    {
        //TODO: Check the record update
        $listItem = YouTubeGalleryData::updateSingleVideo($listItem, $videolist_row);

        $fields = array('width', 'height', 'imageurl', 'image', 'link', 'a', '/a', 'link', 'title', 'description',
            'videoid', 'videosource', 'publisheddate', 'duration',
            'rating_average', 'rating_max', 'rating_min', 'rating_numRaters',
            'statistics_favoriteCount', 'viewcount', 'favcount', 'keywords', 'isactive', 'commentcount', 'likes', 'dislikes', 'channel', 'social',
            'odd', 'even', 'videolist', 'inwatchgroup', 'latitude', 'longitude', 'altitude'
        );

        $tableFields = array('title', 'description',
            'imageurl', 'videoid', 'videosource', 'publisheddate', 'duration',
            'rating_average', 'rating_max', 'rating_min', 'rating_numRaters',
            'keywords', 'commentcount', 'likes', 'dislikes', 'latitude', 'longitude', 'altitude');

        foreach ($fields as $fld) {

            if ($listItem !== null)
                $imageFound = (strlen($listItem['es_imageurl']) > 0);
            else
                $imageFound = false;

            $isEmpty = YoutubeGalleryLayoutThumbnails::isThumbnailDataEmpty($fld, $listItem, $tableFields, $imageFound, $videoId, $item_index, $videolist_row);

            $ValueOptions = array();
            $ValueList = JoomlaBasicMisc::getListToReplace($fld, $ValueOptions, $thumbnail_layout, '[]');

            $ifName = '[if:' . $fld . ']';
            $endIfName = '[endif:' . $fld . ']';

            if ($isEmpty) {
                foreach ($ValueList as $ValueListItem)
                    $thumbnail_layout = str_replace($ValueListItem, '', $thumbnail_layout);

                while (1) {
                    $startIfStatement = strpos($thumbnail_layout, $ifName);
                    if ($startIfStatement === false)
                        break;

                    if (!($startIfStatement === false)) {

                        $endif_ = strpos($thumbnail_layout, $endIfName);
                        if (!($endif_ === false)) {
                            $p = $endif_ + strlen($endIfName);
                            $thumbnail_layout = substr($thumbnail_layout, 0, $startIfStatement) . substr($thumbnail_layout, $p);
                        }
                    }
                }
            } else {
                $thumbnail_layout = str_replace($ifName, '', $thumbnail_layout);
                $thumbnail_layout = str_replace($endIfName, '', $thumbnail_layout);

                $i = 0;
                foreach ($ValueOptions as $ValueOption) {
                    $options = $ValueOptions[$i];
                    $vlu = YoutubeGalleryLayoutThumbnails::getThumbnailData($fld, $aHrefLink, $aLink, $listItem, $tableFields, $options, $theme_row, $gallery_list, $videolist_row); //NEW
                    $thumbnail_layout = str_replace($ValueList[$i], $vlu, $thumbnail_layout);
                    $i++;
                }
            }// IF NOT

            $ifName = '[ifnot:' . $fld . ']';
            $endIfName = '[endifnot:' . $fld . ']';

            if (!$isEmpty) {
                foreach ($ValueList as $ValueListItem)
                    $thumbnail_layout = str_replace($ValueListItem, '', $thumbnail_layout);

                while (1) {
                    $startIfStatement = strpos($thumbnail_layout, $ifName);
                    if ($startIfStatement === false)
                        break;

                    if (!($startIfStatement === false)) {
                        $endif_ = strpos($thumbnail_layout, $endIfName);
                        if (!($endif_ === false)) {
                            $p = $endif_ + strlen($endIfName);
                            $thumbnail_layout = substr($thumbnail_layout, 0, $startIfStatement) . substr($thumbnail_layout, $p);
                        }
                    }
                }
            } else {
                $thumbnail_layout = str_replace($ifName, '', $thumbnail_layout);
                $thumbnail_layout = str_replace($endIfName, '', $thumbnail_layout);
                $vlu = '';
                $i = 0;
                foreach ($ValueOptions as $ValueOption) {
                    $thumbnail_layout = str_replace($ValueList[$i], $vlu, $thumbnail_layout);
                    $i++;
                }
            }
        }
        return $thumbnail_layout;
    }

    public static function isThumbnailDataEmpty($fld, array $listItem, &$tableFields, $ImageFound, $videoId, $item_index, &$videoList_row)
    {
        foreach ($tableFields as $tf) {
            if ($fld == $tf) {
                if ($tf == 'rating_average')
                    $tf = 'ratingaverage';
                elseif ($tf == 'rating_max')
                    $tf = 'ratingmax';
                elseif ($tf == 'rating_min')
                    $tf = 'ratingmin';
                elseif ($tf == 'rating_numRaters')
                    $tf = 'ratingnumberofraters';

                if ($listItem['es_' . $tf] == '' or (is_numeric($listItem['es_' . $tf]) and $listItem['es_' . $tf] == 0)) {
                    //return true;
                } else
                    return false;
            }
        }

        switch ($fld) {
            case 'height':
            case '/a':
            case 'videolist':
            case 'social':
            case 'viewcount':
            case 'link':
            case 'a':
            case 'width':
                return false;

            case 'inwatchgroup':
                $u = (int)$videoList_row->es_watchusergroup;

                if ($videoList_row->es_watchusergroup == 0 or $videoList_row->es_watchusergroup == 1)
                    return false; //public videos

                //check is authorized or not
                $user = Factory::getUser();
                $usergroups = $user->get('groups');

                if (in_array($videoList_row->es_watchusergroup, $usergroups)) {
                    //The user group has access
                    return false;
                }
                return true;

            case 'odd':
                if ($item_index % 2 == 0)
                    return true; //not odd
                else
                    return false; //odd

            case 'even':
                if ($item_index % 2 == 0)
                    return false; //even
                else
                    return true; //not even

            case 'isactive':

                if ($listItem['es_videoid'] == $videoId)
                    return false;
                else
                    return true;

            case 'image':
                if (!$ImageFound)
                    return true;
                else
                    return false;

            case 'favcount':
                if ($listItem['es_statisticsfavoritecount'] == 0)
                    return true;
                else
                    return false;

            case 'channel':
                if ($listItem['es_channelusername'] == '')
                    return true;
                else
                    return false;
        }
        return false;
    }

    public static function getThumbnailData($fld, $aHrefLink, $aLink, array $listItem, &$tableFields, $options, &$theme_row, &$gallery_list, &$videolist_row) //NEW
    {
        $vlu = '';

        switch ($fld) {
            case 'width':
                $vlu = (int)$theme_row->es_width;
                if ($vlu == 0)
                    $vlu = 400;
                break;

            case 'height':
                $vlu = (int)$theme_row->es_height;
                if ($vlu == 0)
                    $vlu = 300;
                break;

            case 'imageurl':
                $vlu = YoutubeGalleryLayoutThumbnails::PrepareImageTag($listItem, $options, $theme_row, false);
                break;

            case 'image':
                $vlu = YoutubeGalleryLayoutThumbnails::PrepareImageTag($listItem, $options, $theme_row, true);
                break;

            case 'title':
                $vlu = str_replace('"', '&quot;', $listItem['es_title']);
                $vlu = Helper::html2txt($vlu);

                if ($options != '') {
                    $pair = explode(',', $options);
                    $words = (int)$pair[0];
                    if (isset($pair[1]))
                        $chars = (int)$pair[1];
                    else
                        $chars = 0;

                    if ($words != 0 or $chars != 0)
                        $vlu = Helper::PrepareDescription_($vlu, $words, $chars);
                }
                break;

            case 'description':
                $description = str_replace('"', '&quot;', $listItem['es_description']);
                $description = Helper::html2txt($description);
                $vlu = $description;
                break;

            case 'a':
                $vlu = $aHrefLink;
                break;

            case '/a':
                $vlu = '</a>';
                break;

            case 'link':
                if ($options == '')
                    $vlu = $aLink;
                elseif ($options == 'full') {
                    if (strpos($aLink, 'http://') !== false or strpos($aLink, 'https://') !== false or strpos($aLink, 'javascript:') !== false)
                        $vlu = Helper::curPageURL(false) . $aLink; //NEW
                }
                break;

            case 'viewcount':
                $vlu = (int)$listItem['es_statisticsviewcount'];

                if ($options != '')
                    $vlu = number_format($vlu, 0, '.', $options);

                break;

            case 'likes':
                $vlu = (int)$listItem['es_likes'];

                if ($options != '')
                    $vlu = number_format($vlu, 0, '.', $options);

                break;

            case 'dislikes':
                $vlu = (int)$listItem['es_dislikes'];

                if ($options != '')
                    $vlu = number_format($vlu, 0, '.', $options);

                break;

            case 'latitude':
                $vlu = $listItem['es_latitude'];
                break;

            case 'longitude':
                $vlu = $listItem['es_longitude'];
                break;

            case 'altitude':
                $vlu = $listItem['es_altitude'];
                break;

            case 'channel':

                if ($options != '') {
                    $pair = explode(',', $options);
                    $f = 'channel_' . $pair[0];

                    $vlu = $listItem[$f];
                    if (isset($pair[1])) {
                        if ($pair[0] == 'subscribers' or $pair[0] == 'subscribed' or $pair[0] == 'commentcount' or $pair[0] == 'viewcount' or $pair[0] == 'videocount') {
                            $vlu = number_format($vlu, 0, '.', $pair[1]);
                        }
                    }
                } else
                    $vlu = 'Tag "[channel:<i>parameter</i>]" must have a parameter. Example: [channel:viewcount]';
                break;

            case 'commentcount':
                $vlu = (int)$listItem['es_commentcount'];

                if ($options != '')
                    $vlu = number_format($vlu, 0, '.', $options);

                break;

            case 'favcount':
                $vlu = $listItem['es_statisticsfavoritecount'];
                break;

            case 'duration':

                if ($options == '')
                    $vlu = $listItem['es_duration'];
                else {
                    $parts = JoomlaBasicMisc::csv_explode(',', $options, '"', false);
                    $secs = (int)$listItem['es_duration'];
                    $vlu = date($parts[0], mktime(0, 0, $secs));
                }
                break;

            case 'publisheddate':

                if ($options == '')
                    $vlu = $listItem['es_publisheddate'];
                else
                    $vlu = date($options, strtotime($listItem['es_publisheddate']));

                break;

            case 'social':
                if (strpos($aLink, 'javascript:') === false) {
                    $a = Helper::curPageURL(false);
                    if (strpos($aLink, $a) === false)
                        $l = '"' . $a . $aLink . '"';
                    else
                        $l = '"' . $aLink . '"';

                } else
                    $l = '(window.location.href.indexOf("?")==-1 ?  window.location.href+"?videoid=' . $listItem['es_videoid'] . '" : window.location.href+"&videoid=' . $listItem['es_videoid'] . '" )';

                $vlu = YoutubeGalleryLayoutRenderer::SocialButtons($l, 'ygt', $options, $listItem['id'], $listItem['es_videoid']);
                break;

            case 'videolist':

                if ($options != '') {
                    $pair = explode(',', $options);
                    switch ($pair[0]) {
                        case 'title':
                            return $videolist_row->es_listname;
                        case 'description':
                            return $videolist_row->es_description;
                        case 'playlist':
                            $pl = YoutubeGalleryLayoutRenderer::getPlaylistIdsOnly($gallery_list);
                            $vlu = implode(',', $pl);
                            break;
                        case 'watchgroup':
                            return $videolist_row->es_watchusergroup;
                        case 'authorurl':
                            return $videolist_row->es_authorurl;
                        case 'image':
                            return $videolist_row->es_image;
                        case 'note':
                            return $videolist_row->es_note;
                    }
                }

            default:
                if (in_array($fld, $tableFields))
                    $vlu = $listItem['es_' . $fld];
                break;
        }
        return $vlu;
    }

    public static function PrepareImageTag(&$listitem, $options, &$theme_row, $as_tag = true)
    {
        //image title
        $thumbTitle = $listitem['es_title'];
        if ($thumbTitle == '') {
            $myDocument = Factory::getDocument();
            $thumbTitle = str_replace('"', '', $myDocument->getTitle());
        }

        $thumbTitle = str_replace('"', '', $thumbTitle);
        $thumbTitle = str_replace('\'', '&rsquo;', $thumbTitle);

        if (strpos($thumbTitle, '&amp;') === false)
            $thumbTitle = str_replace('&', '&amp;', $thumbTitle);

        //image src
        if ($listitem['es_imageurl'] == '') {
            if ($as_tag) {
                $imagetag = '<div style="';

                if ($theme_row->es_thumbnailstyle != '')
                    $imagetag .= $theme_row->es_thumbnailstyle;
                else
                    $imagetag .= 'border:1px solid red;background-color:white;';

                if (!str_contains($theme_row->es_thumbnailstyle, 'width'))
                    $imagetag .= 'width:120px;height:90px;';

                $imagetag .= '"></div>';
            } else
                $imagetag = '';

        } else {
            $images = explode(';', $listitem['es_imageurl']);
            $index = 0;
            if ($options != '') {
                $index = (int)$options;
                if ($index < 0)
                    $index = 0;
                if ($index >= count($images))
                    $index = count($images) - 1;

                $imageLink_array = explode(',', $images[$index]);
                $imageLink = $imageLink_array[0];
            } else {
                if (isset($listitem['es_customimageurl']) and $listitem['es_customimageurl'] != '') {
                    if (!(strpos($listitem['es_customimageurl'], '#') === false)) {
                        $index = (int)(str_replace('#', '', $listitem['es_customimageurl']));
                        if ($index < 0)
                            $index = 0;
                        elseif ($index >= count($images))
                            $index = count($images) - 1;
                    } else
                        $imageLink = $listitem['es_customimageurl'];
                } else {
                    $imageLink_array = explode(',', $images[$index]);
                    $imageLink = $imageLink_array[0];
                }
            }

            if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
                $imageLink = str_replace('http://', 'https://', $imageLink);
            else
                $imageLink = str_replace('https://', 'http://', $imageLink);

            if ($as_tag) {
                $imagetag = '<img src="' . $imageLink . '"' . ($theme_row->es_thumbnailstyle != '' ? ' style="' . $theme_row->es_thumbnailstyle . '"' : ' style="border:none;"');

                if (!str_contains($theme_row->es_thumbnailstyle, 'width'))
                    $imagetag .= ' width="120" height="90"';

                $imagetag .= ' alt="' . $thumbTitle . '" title="' . $thumbTitle . '"';
                $imagetag .= ' />';
            } else
                $imagetag = $imageLink;

            if ($theme_row->es_prepareheadtags == 1 or $theme_row->es_prepareheadtags == 3)//thumbnails or both
            {
                $document = Factory::getDocument();
                $curPageUrl = Helper::curPageURL();

                $imageLink = (strpos($imageLink, 'http://') === false and strpos($imageLink, 'https://') === false ? $curPageUrl . '/' : '') . $imageLink;

                $document->addCustomTag('<link rel="image_src" href="' . $imageLink . '" />'); //all thumbnails
            }
        }
        return $imagetag;
    }
}
