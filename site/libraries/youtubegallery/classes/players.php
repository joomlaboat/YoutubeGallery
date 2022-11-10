<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class YouTubeGalleryPlayers
{
    public static function ShowActiveVideo(&$gallery_list, $width, $height, $videoid, &$videolist_row, &$theme_row, $videosource = '')
    {
        $VideoRow = YouTubeGalleryGalleryList::getVideoRowByID($videoid, $gallery_list);

        $result = '';

        $divstyle_player = '';

        if ((int)$theme_row->es_playvideo == 0) {
            if ($theme_row->es_openinnewwindow == 4 or $theme_row->es_openinnewwindow == 5) {
                $vs = 'youtube';
                $divstyle_player = 'display:none;';
            }
        }

        if ($videoid) {
            $vpoptions = array();
            $vpoptions['es_width'] = $width;
            $vpoptions['es_height'] = $height;

            $vpoptions['es_videoid'] = $videoid;
            $vpoptions['es_autoplay'] = $theme_row->es_autoplay;
            //$vpoptions['showinfo']=$theme_row->es_showinfo;
            $vpoptions['es_relatedvideos'] = $theme_row->es_related;
            $vpoptions['es_repeat'] = $theme_row->es_repeat;
            $vpoptions['es_allowplaylist'] = $theme_row->es_allowplaylist;
            $vpoptions['es_border'] = $theme_row->es_border;
            $vpoptions['es_colorone'] = $theme_row->es_colorone;
            $vpoptions['es_colortwo'] = $theme_row->es_colortwo;

            $vpoptions['es_controls'] = $theme_row->es_controls;
            //$vpoptions['playertype']=$theme_row->es_playertype;
            $vpoptions['es_youtubeparams'] = $theme_row->es_youtubeparams;

            $vpoptions['es_fullscreen'] = $theme_row->es_fullscreen;

            $list_index = YouTubeGalleryGalleryList::getListIndexByVideoID($videoid, $gallery_list);

            //----------------------------------------------------------------------------
            $includeallplayers = false;
            $divstyle = '';
            $divstyle_player = '';

            //----------------------------------------------------------------------------
            if ($videoid == '****youtubegallery-video-id****') {
                //Hot Switch
                if ($videosource != '')
                    $vs = $videosource;
                else
                    $vs = '';
            } elseif ($list_index == -1) {
                if ($videosource != '')
                    $vs = $videosource;
                else
                    $vs = $VideoRow['videosource'];
            } else {
                if ($videosource != '')
                    $vs = $videosource;
                else
                    $vs = $gallery_list[$list_index]['videosource'];
            }

            if ((int)$vpoptions['es_width'] == 0)
                $width = 400;
            else
                $width = (int)$vpoptions['es_width'];

            if ((int)$vpoptions['es_height'] == 0)
                $height = 200;
            else
                $height = (int)$vpoptions['es_height'];

            if ($includeallplayers or $vs == 'break') {
                $result .= '<div id="yg_player_break_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_Break::renderBreakPlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'vimeo') {
                $result .= '<div id="yg_player_vimeo_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_Vimeo::renderVimeoPlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'own3dtvlive') {
                $result .= '<div id="yg_player_own3dtvlive_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_Own3DTvLive::renderOwn3DTvLivePlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'own3dtvvideo') {
                $result .= '<div id="yg_player_own3dtvvideo_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_Own3DTvVideo::renderOwn3DTvVideoPlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'youtube') {
                $result = '<div id="yg_player_youtube_id-' . $videolist_row->id . '" ' . $divstyle . '>';

                $pl = YouTubeGalleryGalleryList::getPlaylistIdsOnly($gallery_list, $videoid, 'youtube');
                $shorten_pl = array();
                $i = 0;
                foreach ($pl as $p) {
                    $i++;
                    if ($i > 20)
                        break;
                    $shorten_pl[] = $p;
                }
                $YoutubeVideoList = implode(',', $shorten_pl);

                $full_pl = YouTubeGalleryGalleryList::getPlaylistIdsOnly($gallery_list, '', 'youtube', true);
                $shorten_full_pl = array();
                $i = 0;
                foreach ($full_pl as $p) {
                    $i++;
                    if ($i > 20)
                        break;
                    $shorten_full_pl[] = $p;
                }
                $full_YoutubeVideoList = implode(',', $shorten_full_pl);

                if ($vpoptions['es_youtubeparams'] == '')
                    $vpoptions['es_youtubeparams'] = 'playlist=' . $YoutubeVideoList;
                else
                    $vpoptions['es_youtubeparams'] .= ';playlist=' . $YoutubeVideoList;

                $temp = VideoSource_Youtube::renderYouTubePlayer($vpoptions, $width, $height, $videolist_row, $theme_row);//,$startsecond,$endsecond);

                if ($temp != '') {
                    if ($theme_row->es_useglass or $theme_row->es_logocover)
                        $result .= '<div class="YoutubeGalleryLogoCover' . $videolist_row->id . '" style="position: relative;width:100%;height:100%;padding:0;border:none;">';

                    $result .= $temp;

                    if ($theme_row->es_logocover) {
                        if ($theme_row->es_controls)// and ($theme_row->es_playertype==3 or $theme_row->es_playertype==4))
                            $bottom_px = '25';
                        else
                            $bottom_px = '0';


                        $result .= '<div style="position: absolute;bottom:' . $bottom_px . 'px;right:0px;margin-top:0px;margin-left:0px;">'
                            . '<img src="' . $theme_row->es_logocover . '" style="margin:0px;padding:0px;display:block;border: none;" /></div>';
                    }

                    if ($theme_row->es_useglass)
                        $result .= '<div class="YoutubeGalleryGlassCover"></div>';

                    if ($theme_row->es_useglass or $theme_row->es_logocover)
                        $result .= '</div>';
                }

                $result .= '</div>';
            }

            if ($includeallplayers or $vs == 'dailymotion') {
                $vpoptions['es_thumbnail'] = YouTubeGalleryGalleryList::getThumbnailByID($videoid, $gallery_list);
                $result .= '<div id="yg_player_dailymotion_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_DailyMotion::renderDailyMotionPlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'ustream') {
                $vpoptions['es_thumbnail'] = YouTubeGalleryGalleryList::getThumbnailByID($videoid, $gallery_list);
                $result .= '<div id="yg_player_ustream_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_Ustream::renderUstreamPlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'ustreamlive') {
                $vpoptions['es_thumbnail'] = YouTubeGalleryGalleryList::getThumbnailByID($videoid, $gallery_list);
                $result .= '<div id="yg_player_ustreamlive_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_UstreamLive::renderUstreamLivePlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'soundcloud') {
                $vpoptions['thumbnail'] = YouTubeGalleryGalleryList::getThumbnailByID($videoid, $gallery_list);
                $result .= '<div id="yg_player_soundcloud_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_SoundCloud::renderPlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers or $vs == 'tiktok') {
                $vpoptions['es_thumbnail'] = YouTubeGalleryGalleryList::getThumbnailByID($videoid, $gallery_list);
                $result .= '<div id="yg_player_tiktok_id-' . $videolist_row->id . '" ' . $divstyle . '>' . VideoSource_TikTok::renderPlayer($vpoptions, $width, $height, $videolist_row, $theme_row) . '</div>';
            }

            if ($includeallplayers) {
                if ($list_index != -1) {
                    //Not Hot Switch
                    $vpoptions['es_thumbnail'] = $gallery_list[$list_index]['imageurl'];//YouTubeGalleryGalleryList::getThumbnailByID($videoid,$gallery_list);;
                    $videolink = $gallery_list[$list_index]['link'];
                } else
                    $videolink = '****youtubegallery-video-link****'; //For Hot Switch
            }
        }

        $imageurl = '';
        $isHot = false;
        if ($videoid == '****youtubegallery-video-id****') {
            $isHot = true;
            $videoid_d = 'hot' . $videolist_row->id;
            $imageurl = '****youtubegallery-video-customimage****';
        } else {
            $videoid_d = $videoid;
            if ($VideoRow)
                $imageurl = $VideoRow['custom_imageurl'];
        }

        if ($imageurl != '' and $theme_row->es_rel == '' and strpos($imageurl, '#') === false and strpos($imageurl, '_small') === false) {
            //Specific preview image for your YouTube video
            //The idea of Jarrett Gucci (Modified: play button added)

            $result = ($isHot ? '***code_begin***' : '') . '<div onclick="ygimage' . $videoid_d . '=document.getElementById(\'ygvideoplayer' . $videoid_d . '\');ygimage' . $videoid_d . '.style.display=\'block\';this.style.display=\'none\'"'
                . ' style="position:relative;width:' . $width . 'px;height:' . $height . 'px;padding:0;">'
                . '<img src="' . $imageurl . '" style="cursor:pointer;width:' . $width . 'px;height:' . $height . 'px;padding:0;" />'
                . '<div style="position:absolute;width:100px;height:100px;left:' . floor($width / 2 - 50) . 'px;top:' . floor($height / 2 - 50) . 'px;">'
                . '<img src="components/com_youtubegallery/images/play.png" style="border:none!important;cursor:pointer;width:100px;height:100px;padding:0;" />'
                . '</div>'
                . '</div>'
                . '<div id="ygvideoplayer' . $videoid_d . '" style="display:none">' . ($isHot ? '***code_end***' : '') . $result . ($isHot ? '***code_begin***' : '') . '</div>' . ($isHot ? '***code_end***' : '');
        }

        if ($videoid != '****youtubegallery-video-id****')
            $result = str_replace('****youtubegallery-video-id****', $videoid, $result);
        else
            $result = str_replace('\'', '_quote_', $result);

        return $result;

    }//function ShowAciveVideo()
}
