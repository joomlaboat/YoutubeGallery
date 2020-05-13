<?php
/**
 * YoutubeGallery for Joomla!
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class YouTubeGalleryPlayers
{
	public static function ShowActiveVideo(&$gallery_list,$width,$height,$videoid, &$videolist_row, &$theme_row,$videosource='')
	{
		if($videoid=='****youtubegallery-video-id****')//****youtubegallery-video-id****
			$VideoRow=false;
		else
			$VideoRow=YoutubeGalleryLayoutRenderer::getVideoRowByID($videoid,$gallery_list);

		$result='';

		$divstyle_player='';

		if($theme_row->playvideo==0)
		{
				if($theme_row->openinnewwindow==4 or $theme_row->openinnewwindow==5)
				{
					$vs='youtube';
					$divstyle_player='display:none;';
				}
		}

		if($videoid)
		{
			$vpoptions=array();
			$vpoptions['width']=$width;
			$vpoptions['height']=$height;

			$vpoptions['videoid']=$videoid;
			$vpoptions['autoplay']=$theme_row->autoplay;
			$vpoptions['showinfo']=$theme_row->showinfo;
			$vpoptions['relatedvideos']=$theme_row->related;
			$vpoptions['repeat']=$theme_row->repeat;
			$vpoptions['allowplaylist']=$theme_row->allowplaylist;
			$vpoptions['border']=$theme_row->border;
			$vpoptions['color1']=$theme_row->color1;
			$vpoptions['color2']=$theme_row->color2;


			$vpoptions['controls']=$theme_row->controls;
			$vpoptions['playertype']=$theme_row->playertype;
			$vpoptions['youtubeparams']=$theme_row->youtubeparams;

			$vpoptions['fullscreen']=$theme_row->fullscreen;

			$list_index=YoutubeGalleryLayoutRenderer::getListIndexByVideoID($videoid,$gallery_list);

			//----------------------------------------------------------------------------
			$includeallplayers=false;
			$divstyle='';
			$divstyle_player='';



			//----------------------------------------------------------------------------
			if($videoid=='****youtubegallery-video-id****')
			{
				//Hot Switch
				if($videosource!='')
					$vs=$videosource;
				else
					$vs='';

				//$image_link='';
				//$startsecond='****youtubegallery-video-startsecond****';
				//$endsecond='****youtubegallery-video-endsecond****';
			}
			elseif($list_index==-1)
			{
				//$VideoRow=YoutubeGalleryLayoutRenderer::getVideoRowByID($videoid,$gallery_list,false);
				//if(!$VideoRow)
					//return '';

				if($videosource!='')
					$vs=$videosource;
				else
					$vs=$VideoRow['videosource'];

				//$image_link=$row['imageurl'];
				//$startsecond=$row['startsecond'];
				//$endsecond=$row['endsecond'];
			}
			else
			{
				if($videosource!='')
					$vs=$videosource;
				else
					$vs=$gallery_list[$list_index]['videosource'];

				//$image_link=$gallery_list[$list_index]['imageurl'];
				//$startsecond=$gallery_list[$list_index]['startsecond'];
				//$endsecond=$gallery_list[$list_index]['endsecond'];
			}




			if((int)$vpoptions['width']==0)
				$width=400;
			else
				$width=(int)$vpoptions['width'];


			if((int)$vpoptions['height']==0)
				$height=200;
			else
				$height=(int)$vpoptions['height'];




			if($includeallplayers or $vs=='break')
			{
					require_once('providers'.DIRECTORY_SEPARATOR.'break.php');
					$result.='<div id="yg_player_break_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_Break::renderBreakPlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}

			if($includeallplayers or $vs=='vimeo')
			{
					require_once('providers'.DIRECTORY_SEPARATOR.'vimeo.php');
					$result.='<div id="yg_player_vimeo_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_Vimeo::renderVimeoPlayer($vpoptions, $width, $height, $videolist_row,$theme_row).'</div>';
			}

			if($includeallplayers or $vs=='own3dtvlive')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'own3dtvlive.php');
				$result.='<div id="yg_player_own3dtvlive_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_Own3DTvLive::renderOwn3DTvLivePlayer($vpoptions, $width, $height, $videolist_row,$theme_row).'</div>';
			}

			if($includeallplayers or $vs=='own3dtvvideo')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'own3dtvvideo.php');
				$result.='<div id="yg_player_own3dtvvideo_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_Own3DTvVideo::renderOwn3DTvVideoPlayer($vpoptions, $width, $height, $videolist_row,$theme_row).'</div>';
			}

			if($includeallplayers or $vs=='youtube')
			{
				$result='<div id="yg_player_youtube_id-'.$videolist_row->id.'" '.$divstyle.'>';

						$pl=YoutubeGalleryLayoutRenderer::getPlaylistIdsOnly($gallery_list,$videoid,'youtube');
						$shorten_pl=array();
						$i=0;
						foreach($pl as $p)
						{
							$i++;
							if($i>20)
								break;
							$shorten_pl[]=$p;
						}
						$YoutubeVideoList=implode(',',$shorten_pl);

						$full_pl=YoutubeGalleryLayoutRenderer::getPlaylistIdsOnly($gallery_list,'','youtube',true);
						$shorten_full_pl=array();
						$i=0;
						foreach($full_pl as $p)
						{
							$i++;
							if($i>20)
								break;
							$shorten_full_pl[]=$p;
						}
						$full_YoutubeVideoList=implode(',',$shorten_full_pl);

						if($vpoptions['youtubeparams']=='')
							$vpoptions['youtubeparams']='playlist='.$YoutubeVideoList;
						else
							$vpoptions['youtubeparams'].=';playlist='.$YoutubeVideoList;

					require_once('providers'.DIRECTORY_SEPARATOR.'youtube.php');

					$temp=VideoSource_Youtube::renderYouTubePlayer($vpoptions, $width, $height, $videolist_row,$theme_row);//,$startsecond,$endsecond);

					if($temp!='')
					{
						if($theme_row->useglass or $theme_row->logocover)
							$result.='<div class="YoutubeGalleryLogoCover'.$videolist_row->id.'" style="position: relative;width:100%;height:100%;padding:0;border:none;">';

						$result.=$temp;

						if($theme_row->logocover)
						{
							if($theme_row->controls and ($theme_row->playertype==3 or $theme_row->playertype==4))
								$bottom_px='25';
							else
								$bottom_px='0';


							$result.='<div style="position: absolute;bottom:'.$bottom_px.'px;right:0px;margin-top:0px;margin-left:0px;">'
							.'<img src="'.$theme_row->logocover.'" style="margin:0px;padding:0px;display:block;border: none;" /></div>';
						}

						if($theme_row->useglass)
							$result.='<div class="YoutubeGalleryGlassCover"></div>';

						if($theme_row->useglass or $theme_row->logocover)
							$result.='</div>';
					}

					$result.='</div>';
			}

			if($includeallplayers or $vs=='google')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'google.php');
				$result.='<div id="yg_player_google_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_Google::renderGooglePlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}
			if($includeallplayers or $vs=='yahoo')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'yahoo.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_yahoo_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_Yahoo::renderYahooPlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}
			if($includeallplayers or $vs=='collegehumor')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'collegehumor.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_collegehumor_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_CollegeHumor::renderCollegeHumorPlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}
			if($includeallplayers or $vs=='dailymotion')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'dailymotion.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_dailymotion_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_DailyMotion::renderDailyMotionPlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}

			if($includeallplayers or $vs=='presentme')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'presentme.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_presentme_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_PresentMe::renderPresentMePlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}

			if($includeallplayers or $vs=='ustream')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'ustream.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_ustream_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_Ustream::renderUstreamPlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}

			if($includeallplayers or $vs=='ustreamlive')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'ustreamlive.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_ustreamlive_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_UstreamLive::renderUstreamLivePlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}
			
			if($includeallplayers or $vs=='soundcloud')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'soundcloud.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_soundcloud_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_SoundCloud::renderPlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}
			
			if($includeallplayers or $vs=='tiktok')
			{
				require_once('providers'.DIRECTORY_SEPARATOR.'tiktok.php');
				$vpoptions['thumbnail']=YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);
				$result.='<div id="yg_player_tiktok_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_TikTok::renderPlayer($vpoptions, $width, $height, $videolist_row, $theme_row).'</div>';
			}

			if($includeallplayers or $vs=='.flv')
			{
					if($list_index!=-1)
					{
						//Not Hot Switch
						$vpoptions['thumbnail']=$gallery_list[$list_index]['imageurl'];//YoutubeGalleryLayoutRenderer::getThumbnailByID($videoid,$gallery_list);;
						$videolink=$gallery_list[$list_index]['link'];
					}
					else
						$videolink='****youtubegallery-video-link****'; //For Hot Switch

						require_once('providers'.DIRECTORY_SEPARATOR.'flv.php');

						$result.='<div id="yg_player_flv_id-'.$videolist_row->id.'" '.$divstyle.'>'.VideoSource_FLV::renderFLVPlayer($vpoptions, $width, $height, $videolist_row, $theme_row, $videolink).'</div>';
			}

		}

		$imageurl='';
		$isHot=false;
		if($videoid=='****youtubegallery-video-id****')
		{
			$isHot=true;
			$videoid_d='hot'.$videolist_row->id;
			$imageurl='****youtubegallery-video-customimage****';
		}
		else
		{
			$videoid_d=$videoid;
			if($VideoRow)
				$imageurl=$VideoRow['custom_imageurl'];
		}

		if($imageurl!='' and $theme_row->rel=='' and strpos($imageurl,'#')===false and strpos($imageurl,'_small')===false)
		{
			//Specific preview image for your YouTube video
			//The idea of Jarrett Gucci (Modified: play button added)

			$result=($isHot ? '***code_begin***' : '').'<div onclick="ygimage'.$videoid_d.'=document.getElementById(\'ygvideoplayer'.$videoid_d.'\');ygimage'.$videoid_d.'.style.display=\'block\';this.style.display=\'none\'"'
				.' style="position:relative;width:'.$width.'px;height:'.$height.'px;padding:0;">'
				.'<img src="'.$imageurl.'" style="cursor:pointer;width:'.$width.'px;height:'.$height.'px;padding:0;" />'
				.'<div style="position:absolute;width:100px;height:100px;left:'.floor($width/2-50).'px;top:'.floor($height/2-50).'px;">'
				.'<img src="components/com_youtubegallery/images/play.png" style="border:none!important;cursor:pointer;width:100px;height:100px;padding:0;" />'
				.'</div>'
				.'</div>'
				.'<div id="ygvideoplayer'.$videoid_d.'" style="display:none">'.($isHot ? '***code_end***' : '').$result.($isHot ? '***code_begin***' : '').'</div>'.($isHot ? '***code_end***' : '');
		}



		if($videoid!='****youtubegallery-video-id****')
			$result=str_replace('****youtubegallery-video-id****',$videoid,$result);
		else
			$result=str_replace('\'','_quote_',$result);

		return $result;

	}//function ShowAciveVideo()
}
