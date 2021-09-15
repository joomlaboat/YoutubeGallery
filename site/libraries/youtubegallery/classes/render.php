<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

defined('_JEXEC') or die('Restricted access');

use YouTubeGallery\Helper;

jimport('joomla.application.component.view');

class YouTubeGalleryRenderer
{
	function render(&$gallery_list,	&$videolist_row, &$theme_row, $total_number_of_rows, $videoid,$custom_itemid=0)
	{
		$width=$theme_row->es_width;
		if($width==0)
			$width=400;

		$height=$theme_row->es_height;
		if($height==0)
			$height=300;

		//Head Script
		YouTubeGalleryRenderer::setHeadScript($theme_row,$videolist_row->id,$width,$height);
		
		YoutubeGalleryHotPlayer::addHotReloadScript($gallery_list,$width,$height,$videolist_row, $theme_row);
		
		$result='
<a name="youtubegallery"></a>
<div id="YoutubeGalleryMainContainer'.$videolist_row->id.'" style="position: relative;display: block;'
	.((int)$theme_row->es_width!=0 ? 'width:'.$width.'px;' : '').($theme_row->es_cssstyle!='' ? $theme_row->es_cssstyle.';' : '').'">
';

		$LayoutRenderer=new YoutubeGalleryLayoutRenderer;
		
		$jinput=JFactory::getApplication()->input;
		if($theme_row->es_rel!='' and $jinput->getCmd('tmpl')!='')
			$layoutcode='[videoplayer]'; // Shadow box
		else
			$layoutcode=$theme_row->es_customlayout;

		$result.=$LayoutRenderer->render($layoutcode, $videolist_row, $theme_row, $gallery_list, $width, $height, $videoid, $total_number_of_rows,$custom_itemid);

		$result.='
</div>
';
	
		return $result;
	}
	
	protected static function setHeadScript(&$theme_row,$instance_id,$width,$height)
	{
		$headscript=$theme_row->es_headscript;
		
		if($headscript!='')
		{
			$headscript=str_replace('[instanceid]',$instance_id,$headscript);
			$headscript=str_replace('[width]',$width,$headscript);
			$headscript=str_replace('[height]',$height,$headscript);
			$headscript=str_replace('[mediafolder]','images/'.$theme_row->es_mediafolder,$headscript);

			$fields_theme=array('es_bgcolor','es_cssstyle','es_navbarstyle','es_thumbnailstyle','es_listnamestyle','es_activevideotitlestyle','es_colorone',
				'es_colortwo','es_descrstyle','es_rel','es_hrefaddon','es_mediafolder');

			$theme_row_array = get_object_vars($theme_row);

			foreach($fields_theme as $fld)
				$headscript=str_replace('['.$fld.']',$theme_row_array[$fld],$headscript);
		
			$document = JFactory::getDocument();
			$document->addCustomTag($headscript);
		}
		
		YouTubeGallery\RendererCSS::renderCSS($theme_row,$instance_id);
		
		if($theme_row->es_responsive==1)
		{
			YouTubeGalleryRendererJS::getResponsiveCode_JS($instance_id,$width,$height);
		}
	}
	
	public static function SetHeaderTags(&$videolist_row, &$theme_row,$pl)
	{
		if(count($pl)==0)
			return;

		$parts=explode('*',$pl[0]);
		$videoid=$parts[0];
		$videosource=$parts[2];
		
		$VideoRow=YoutubeGalleryDB::getVideoRowByID($videoid);
		if(!$VideoRow)
			return;

		$mydoc = JFactory::getDocument();

		if($theme_row->es_changepagetitle!=3)
		{
			$mainframe = JFactory::getApplication();
			$sitename =$mainframe->getCfg('sitename');

			$title=$VideoRow['es_title'];

			if($theme_row->es_changepagetitle==0)
				$mydoc->setTitle($title.' - '.$sitename);
			elseif($theme_row->es_changepagetitle==1)
				$mydoc->setTitle($sitename.' - '.$title);
			elseif($theme_row->es_changepagetitle==2)
				$mydoc->setTitle($title);
		}

		//add meta keywords
		
		if((int)$theme_row->es_prepareheadtags!=0 and $videosource=='youtube')
		{
			$mydoc->setMetaData( 'keywords', Helper::html2txt($VideoRow['es_keywords']));//tags
			$description_=str_replace('*email*','@',Helper::html2txt($VideoRow['es_description']));
			$mydoc->setMetaData( 'description', $description_);
		}

		$image_link=$VideoRow['es_imageurl'];

		if($theme_row->es_prepareheadtags==2 or $theme_row->es_prepareheadtags==3)
		{
			if($image_link!='' and strpos($image_link,'#')===false)
			{
				$curPageUrl=Helper::curPageURL();

				$image_link_array=explode(',',$image_link);
				if(count($image_link_array)>=3)
					$imagelink=$image_link_array[3];
				else
					$imagelink=$image_link_array[0];

				$imagelink=(strpos($imagelink,'http://')===false and strpos($image_link,'https://')===false  ? $curPageUrl.'/' : '').$imagelink;

				if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
					$imagelink=str_replace('http://','https://',$imagelink);

				$mydoc->addCustomTag('<link rel="image_src" href="'.$imagelink.'" /><!-- active -->');
			}
		}
	}
}
