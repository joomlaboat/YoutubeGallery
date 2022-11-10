<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

$client_id = YouTubeGalleryDB::getSettingValue('soundcloud_api_client_id');

$document = Factory::getDocument();
$document->addCustomTag('
<script>
	function YGGetSoundCloudClientID()
	{
		return "' . $client_id . '";
	}
</script>');

?>

<table style="border:none;">
    <tbody>
    <tr>
        <td style="vertical-align: top;" valign="top">
            <p style="font-weight:bold;">Source</p>

            <?php /** @var TYPE_NAME $textarea_box the variable declared in edit.php file */
            echo $textarea_box; ?>
        </td>
        <td style="vertical-align: bottom;" valign="bottom">

            <div style="border: none;padding-left:10px;margin:0px;">

                <p><b>Video Link Examples:</b></p>
                <br/>
                <ul>
                    <li>
                        <b>Youtube Video</b><br/>
                        https://www.youtube.com/watch?v=VSGMqfGmjG0<br/>
                        https://www.youtube.com/watch?v=baLkXC_qWJY&feature=related
                    </li>

                    <li>
                        <b>Youtube Video Playlist, Channel, Standard Feeds, Search Results and Show</b><br/>
                        https://www.youtube.com/playlist?list=PL5298F5DAD70298FC&feature=mh_lolz<br/>
                        https://www.youtube.com/user/ivankomlev/favorites<br/>
                        https://www.youtube.com/user/ivankomlev<br/>
                        https://www.youtube.com/results?search_query=wins+compilation+2012<br/>
                        https://www.youtube.com/show/californication <b>(Must be resolved - Use "Add Link"
                            button)</b><br/>
                        youtubestandard:<i>video_feed</i><br/>
                        <a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-standard-feeds" target="_blank">More
                            about Standard Video Feeds</a>
                    </li>


                    <li>
                        <b>Vimeo Video</b><br/>
                        http://vimeo.com/34592862
                    </li>

                    <li>
                        <b>Vimeo User Videos</b><br/>
                        http://vimeo.com/user2668497
                    </li>

                    <li>
                        <b>Vimeo Channel</b><br/>
                        http://vimeo.com/channels/123456
                    </li>

                    <li>
                        <b>Vimeo Album</b><br/>
                        https://vimeo.com/album/2585295
                    </li>

                    <li>
                        <b>Break.com</b><br/>
                        https://www.break.com/pranks/biker-falls-off-dock-wall-2392751
                    </li>

                    <li>
                        <b>Daily Motion</b><br/>
                        https://www.dailymotion.com/video/xrcy5b
                    </li>

                    <li>
                        <b>Daily Motion Playlist</b><br/>
                        https://www.dailymotion.com/playlist/x2jcwc_f669221398_reality/1
                    </li>


                    <li>
                        <b>College Humor Video</b><br/>
                        https://www.collegehumor.com/video/65.0.01/what-pi-sounds-like
                    </li>

                    <li>
                        <b>Own3D.tv Video (live and uploaded)</b><br/>
                        http://own3d.tv/l/153518<br/>
                        http://own3d.tv/v/816530
                    </li>

                    <li>
                        <b>Present.me</b><br/>
                        https://www.present.me/view/82240-video-cv-blog-tutorials
                    </li>

                    <li>
                        <b>Ustream.tv (recorded)</b><br/>
                        https://www.ustream.tv/recorded/40925310
                        <br/>
                        <b>Ustream.tv (live)</b><br/>
                        https://www.ustream.tv/channel/95.0.02
                    </li>

                    <li>
                        <b>Tik Tok</b><br/>
                        https://www.tiktok.com/@USERNAME/video/6796040944783394373
                    </li>

                    <li>
                        <b>SoundCloud</b><br/>
                        http://soundcloud.com/official-p-nk/try <b>(Must be resolved - Use "Add Link" button)</b><br/>
                        http://api.soundcloud.com/tracks/71591554.json
                    </li>


                    <li>
                        <b>Local .FLV files</b><br/>
                        images/videos/test.flv
                    </li>

                    <li>
                        <b>Video Lists</b><br/>
                        videolist:3 <i>Will insert all videos from Video List ID:3 to current video list.</i><br/>
                        videolist:all <i>Will insert all videos of all Video Lists.</i><br/>
                        videolist:catid:4<i>Will insert all videos of Video Lists of Category with ID #4.</i><br/>
                        videolist:category:music<i>Will insert all videos of Video Lists of Category "music"</i><br/>
                    </li>

                    <hr/>
                    <p></p>
                    <b>Also you may have your own title, description and thumbnail for each video.</b>
                    To do this type comma then "<span style="color:green;">title</span>","<span style="color:green;">description</span>",
                    "<span style="color:green;">imagepath</span>","<span style="color:green;">special_parameters</span>",
                    "<span style="color:green;">startsecond</span>","<span style="color:green;">endsecond</span>"<br/>
                    Should look like: <b>https://www.youtube.com/watch?v=baLkXC_qWJY</b>,"<b>Video Title</b>","<b>Video
                        description</b>","<b>images/customthumbnail.jpg</b>"<br/>
                    or<br/>
                    <b>https://www.youtube.com/watch?v=baLkXC_qWJY</b>,"<b>Video Title</b>",,"<b>images/customthumbnail.jpg</b>"
                    <br/>
                    you may pass #1..#6 to imagepath to select specific video thumbnail.
                    <p>
                        <b><span style="color:green;">Youtube Special parameters:</span></b> max-results=<i>NUMBER</i>,start-index=<i>NUMBER</i>,orderby=<i>FIELD_NAME</i><br/>
                        <a href="https://joomlaboat.com/youtube-gallery/youtube-gallery-special-parameters"
                           target="_blank">More about Special Parameters</a>
                    </p>
                    <p>
                        <b><span style="color:green;">Vimeo Special parameters:</span></b>
                        per_page=<i>NUMBER</i>,page=<i>NUMBER</i><br/>
                    </p>

                    <p><b><span style="color:green;">startsecond</span></b> (supported players: AS3, AS2, HTML5)<br/>
                        Values: A positive integer. This parameter causes the player to begin playing the video at the
                        given number of seconds from the start of the video. Note that similar to the seekTo function,
                        the player will look for the closest keyframe to the time you specify. This means sometimes the
                        play head may seek to just before the requested time, usually no more than ~2 seconds.</p>


                    <p><b><span style="color:green;">endsecond</span></b> (supported players: AS3)<br/>
                        Values: A positive integer. This parameter specifies the time, measured in seconds from the
                        start of the video, when the player should stop playing the video. Note that the time is
                        measured from the beginning of the video and not from either the value of the start player
                        parameter or the startSeconds parameter, which is used in YouTube Player API functions for
                        loading or queueing a video.</p>

            </div>

        </td>
    </tr>
    </tbody>
</table>
