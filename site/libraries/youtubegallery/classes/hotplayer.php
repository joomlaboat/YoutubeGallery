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

class YoutubeGalleryHotPlayer
{
    public static function addHotReloadScript(&$gallery_list, $width, $height, &$videolist_row, &$theme_row)
    {
        $jinput = Factory::getApplication()->input;
        $ygstart = $jinput->getInt('ygstart', 0);

        $vs = array();
        foreach ($gallery_list as $g) {
            $v = $g['es_videosource'];

            if (!in_array($v, $vs))
                $vs[] = $v;
        }

        $document = Factory::getDocument();

        if (Helper::check_user_agent_for_ie())
            $document->addScript(JURI::root(true) . '/components/com_youtubegallery/js/player_ie_533.js');//Thankx to https://babeljs.io/
        else
            $document->addScript(JURI::root(true) . '/components/com_youtubegallery/js/player_533.js');


        $autoplay = ((int)$theme_row->es_autoplay == 1 ? 'true' : 'false');

        $allowplaylist = ((int)$theme_row->es_allowplaylist == 1 or $theme_row->es_repeat == 1 ? 'true' : 'false'); //to loop video or to play the next one


        $playerapiid = 'ygplayerapiid_' . $videolist_row->id;
        $initial_volume = (int)$theme_row->es_volume;

        $pl = YouTubeGalleryGalleryList::getPlaylistIdsOnly($gallery_list, '', '', true, true);//(bool)$theme_row->allowplaylist

        $hotrefreshscript = '
	var youtubeplayer' . $videolist_row->id . ' = new YoutubeGalleryPlayerObject('
            . $width . ','
            . $height . ','
            . '"' . $playerapiid . '",'
            . $initial_volume . ','
            . $theme_row->es_muteonplay . ','
            . $autoplay . ','
            . $allowplaylist . ');
			
	youtubeplayer' . $videolist_row->id . '.WebsiteRoot="' . JURI::root(true) . '";

	function onYouTubeIframeAPIReady () {
		
		youtubeplayer' . $videolist_row->id . '.iframeAPIloaded=true;
	}

	youtubeplayer' . $videolist_row->id . '.videolistid="' . $videolist_row->id . '";
	youtubeplayer' . $videolist_row->id . '.themeid="' . $theme_row->id . '";

	youtubeplayer' . $videolist_row->id . '.VideoSources=["' . implode('", "', $vs) . '"];

	youtubeplayer' . $videolist_row->id . '.openinnewwindow="' . $theme_row->es_openinnewwindow . '";
	youtubeplayer' . $videolist_row->id . '.PlayList="' . implode(',', $pl) . '".split(",");
';

        YouTubeGalleryRenderer::SetHeaderTags($videolist_row, $theme_row, $pl);

        $document->addScriptDeclaration($hotrefreshscript);
        $hotrefreshscript = '';
        $i = 0;

        foreach ($vs as $v) {
            $player_code = '<!-- ' . $v . ' player -->' . YouTubeGalleryPlayers::ShowActiveVideo($gallery_list, $width, $height, '****youtubegallery-video-id****',
                    $videolist_row, $theme_row, $v);

            $hotrefreshscript .= '
	youtubeplayer' . $videolist_row->id . '.Player[' . $i . ']=\'' . $player_code . '\';';

            $i++;
        }

        $hotrefreshscript .= '

	for (var i=0;i<youtubeplayer' . $videolist_row->id . '.Player.length;i++)
	{
		var player_code=youtubeplayer' . $videolist_row->id . '.Player[i];
		';

        $hotrefreshscript .= '
		player_code=player_code.replace(\'_quote_\',\'\\\'\');
		youtubeplayer' . $videolist_row->id . '.Player[i]=player_code;
	}
		
	window.addEventListener( "load", function( event ) {
		youtubeplayer' . $videolist_row->id . '.loadVideoRecords(' . $ygstart . ');
	});
';
        $videoid = Factory::getApplication()->input->getCmd('videoid');
        if ((int)$theme_row->es_playvideo == 1 or $videoid != '') {
            $hotrefreshscript .= '
			//Show first video
			youtubeplayer' . $videolist_row->id . '.CurrentVideoID="' . $videoid . '";
			window.addEventListener( "load", function( event ) {
';
            if ($videoid == '') {
                $hotrefreshscript .= '
			setTimeout(youtubeplayer' . $videolist_row->id . '.FindNextVideo(), 500);
';
            } else {
                $hotrefreshscript .= '
			setTimeout(youtubeplayer' . $videolist_row->id . '.FindCurrentVideo(), 500);
';
            }

            $hotrefreshscript .= '
		});
';
        }

        $document->addScriptDeclaration($hotrefreshscript);
    }
}
