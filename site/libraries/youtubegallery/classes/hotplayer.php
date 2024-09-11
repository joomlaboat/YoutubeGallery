<?php
/**
 * YouTubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use CustomTables\Environment;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

class YoutubeGalleryHotPlayer
{
    public static function addHotReloadScript(&$gallery_list, $width, $height, &$videoListRow, &$theme_row): void
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

        if (Environment::check_user_agent_for_ie())
            $document->addScript(Uri::root(true) . '/components/com_youtubegallery/js/player_ie_533.js');//Thankx to https://babeljs.io/
        else
            $document->addScript(Uri::root(true) . '/components/com_youtubegallery/js/player_533.js');

        $autoplay = ((int)$theme_row->es_autoplay == 1 ? 'true' : 'false');
        $allowplaylist = ((int)$theme_row->es_allowplaylist == 1 or $theme_row->es_repeat == 1 ? 'true' : 'false'); //to loop video or to play the next one
        $playerapiid = 'ygplayerapiid_' . $videoListRow->id;
        $initial_volume = (int)$theme_row->es_volume;

        $pl = YouTubeGalleryGalleryList::getPlaylistIdsOnly($gallery_list, '', '', true, true);//(bool)$theme_row->allowplaylist

        $hotRefreshScript = '
	var youtubeplayer' . $videoListRow->id . ' = new YoutubeGalleryPlayerObject('
            . $width . ','
            . $height . ','
            . '"' . $playerapiid . '",'
            . $initial_volume . ','
            . $theme_row->es_muteonplay . ','
            . $autoplay . ','
            . $allowplaylist . ');
			
	youtubeplayer' . $videoListRow->id . '.WebsiteRoot="' . Uri::root(true) . '";
    YoutubeGalleryPlayerObjects.push(youtubeplayer' . $videoListRow->id . ');
    
	function onYouTubeIframeAPIReady() {
		YoutubeGalleryPlayersSetAPILoaded();
	}
	
	youtubeplayer' . $videoListRow->id . '.videolistid="' . $videoListRow->id . '";
	youtubeplayer' . $videoListRow->id . '.themeid="' . $theme_row->id . '";
	youtubeplayer' . $videoListRow->id . '.VideoSources=["' . implode('", "', $vs) . '"];
	youtubeplayer' . $videoListRow->id . '.openinnewwindow="' . $theme_row->es_openinnewwindow . '";
	youtubeplayer' . $videoListRow->id . '.PlayList="' . implode(',', $pl) . '".split(",");
';

        YouTubeGalleryRenderer::SetHeaderTags($theme_row, $pl);
        $document->addCustomTag('<script>' . $hotRefreshScript . '</script>');
        $hotRefreshScript = '';
        $i = 0;

        foreach ($vs as $v) {
            $player_code = '<!-- ' . $v . ' player -->' . YouTubeGalleryPlayers::ShowActiveVideo($gallery_list, $width, $height, '****youtubegallery-video-id****',
                    $videoListRow, $theme_row, $v);

            $hotRefreshScript .= '
	youtubeplayer' . $videoListRow->id . '.Player[' . $i . ']=\'' . $player_code . '\';';

            $i++;
        }
        $hotRefreshScript .= '

	for (var i=0;i<youtubeplayer' . $videoListRow->id . '.Player.length;i++)
	{
		var player_code=youtubeplayer' . $videoListRow->id . '.Player[i];
		';

        $hotRefreshScript .= '
		player_code=player_code.replace(\'_quote_\',\'\\\'\');
		youtubeplayer' . $videoListRow->id . '.Player[i]=player_code;
	}
		
	window.addEventListener( "load", function( event ) {
		youtubeplayer' . $videoListRow->id . '.loadVideoRecords(' . $ygstart . ');
	});
';
        $videoId = Factory::getApplication()->input->getCmd('videoid');
        if ((int)$theme_row->es_playvideo == 1 or $videoId != '') {
            $hotRefreshScript .= '
			//Show first video
			youtubeplayer' . $videoListRow->id . '.CurrentVideoID="' . $videoId . '";
			window.addEventListener( "load", function( event ) {
';
            if ($videoId == '') {
                $hotRefreshScript .= '
			setTimeout(youtubeplayer' . $videoListRow->id . '.FindNextVideo(), 500);
';
            } else {
                $hotRefreshScript .= '
			setTimeout(youtubeplayer' . $videoListRow->id . '.FindCurrentVideo(), 500);
';
            }

            $hotRefreshScript .= '
		});
';
        }
        $document->addCustomTag('<script>' . $hotRefreshScript . '</script>');
    }
}
