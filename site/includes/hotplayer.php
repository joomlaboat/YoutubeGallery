<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once('misc.php');
require_once('data.php');
require_once('players.php');

class YoutubeGalleryHotPlayer
{
	public static function addHotReloadScript(&$gallery_list,$width,$height,&$videolist_row, &$theme_row)
	{
			$vs=array();
			foreach($gallery_list as $g)
			{

				$v=$g['videosource'];


				if(!in_array($v,$vs))
					$vs[]=$v;
			}

			$document = JFactory::getDocument();
			$document->addScript(JURI::root(true).'/components/com_youtubegallery/js/player.js');
						
			$autoplay=((int)$theme_row->autoplay==1 ? 'true' : 'false');
			
			$allowplaylist=((int)$theme_row->allowplaylist==1 or $theme_row->repeat==1 ? 'true' : 'false'); //to loop video or to play the next one


			$playerapiid='ygplayerapiid_'.$videolist_row->id;
			$initial_volume=(int)$theme_row->volume;




		$pl=YouTubeGalleryGalleryList::getPlaylistIdsOnly($gallery_list,'','',true,true);//(bool)$theme_row->allowplaylist

			$hotrefreshscript='


var youtubeplayer'.$videolist_row->id.' = new YoutubeGalleryPlayerObject('
			.$width.','
			.$height.','
			.'"'.$playerapiid.'",'
			.$initial_volume.','
			.$theme_row->muteonplay.','
			.$autoplay.','
			.$allowplaylist.');


	function onYouTubeIframeAPIReady () {
		
		youtubeplayer'.$videolist_row->id.'.iframeAPIloaded=true;
	}

	youtubeplayer'.$videolist_row->id.'.videolistid="'.$videolist_row->id.'";


	youtubeplayer'.$videolist_row->id.'.VideoSources=["'.implode('", "',$vs).'"];

	youtubeplayer'.$videolist_row->id.'.openinnewwindow="'.$theme_row->openinnewwindow.'";
	youtubeplayer'.$videolist_row->id.'.PlayList="'.implode(',',$pl).'".split(",");
';


		YouTubeGalleryRenderer::SetHeaderTags($videolist_row, $theme_row,$pl);

			$document->addScriptDeclaration($hotrefreshscript);
			$hotrefreshscript='';
			$i=0;

			foreach($vs as $v)
			{
				$player_code='<!-- '.$v.' player -->'.YouTubeGalleryPlayers::ShowActiveVideo($gallery_list,$width,$height,'****youtubegallery-video-id****', $videolist_row, $theme_row,$v);
				$hotrefreshscript.='
	youtubeplayer'.$videolist_row->id.'.Player['.$i.']=\''.$player_code.'\';';
				$i++;
			}

			$hotrefreshscript.='

	for (var i=0;i<youtubeplayer'.$videolist_row->id.'.Player.length;i++)
	{
		var player_code=youtubeplayer'.$videolist_row->id.'.Player[i];
		';
		$hotrefreshscript.='
		player_code=player_code.replace(\'_quote_\',\'\\\'\');
		youtubeplayer'.$videolist_row->id.'.Player[i]=player_code;
	}
';//</script>
		$videoid=JFactory::getApplication()->input->getCmd('videoid');
		if($theme_row->playvideo==1 or $videoid!='')
		{
			


			$hotrefreshscript.='
			youtubeplayer'.$videolist_row->id.'.CurrentVideoID="'.$videoid.'";
			window.addEventListener( "load", function( event ) {

';
		if($videoid=='')
		{
			$hotrefreshscript.='
			
			setTimeout(youtubeplayer'.$videolist_row->id.'.FindNextVideo(), 500);
';
		}
		else
		{
			$hotrefreshscript.='
			setTimeout(youtubeplayer'.$videolist_row->id.'.FindCurrentVideo(), 500);
';
		}

$hotrefreshscript.='
		});
';
		}

		$document->addScriptDeclaration($hotrefreshscript);
	}

}