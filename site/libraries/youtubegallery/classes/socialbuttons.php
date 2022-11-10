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

class YoutubeGallerySocialButtons
{
    public static function SocialButtons($link, $prefix, $params, $videolist_row_id, $videoid)
    {
        $pair = explode(',', $params);

        $w = 80;
        if (isset($pair[2]))
            $w = (int)$pair[2];

        switch ($pair[0]) {
            case 'facebook_comments':

                $head_result = '

<script>(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=6245995.0.07869";
  fjs.parentNode.insertBefore(js, fjs);
}(document, \'script\', \'facebook-jssdk\'));

document.write(\'<div id="fb-root"></div>\');
</script>
';

                $document = Factory::getDocument();
                $document->addCustomTag($head_result);


                $numposts = '3';
                if (isset($pair[1]))
                    $numposts = (int)$pair[1];

                $width = '';//style="width:auto !important;"';
                if (isset($pair[2]))
                    $width = 'data-width="' . (int)$pair[2] . 'px"';

                $colorscheme = 'light';
                if (isset($pair[3]))
                    $colorscheme = $pair[3];

                if ($link == '' or $link = 'window.location.href')
                    $link = Helper::full_url($_SERVER);

                $result = '<div class="fb-comments" data-href="' . $link . '" data-num-posts="' . $numposts . '" ' . $width . ' data-colorscheme="' . $colorscheme . '"></div>';


                return $result;
                break;
            //------------------------------------------------------------------------------------------------------------
            case 'facebook_share':

                $bName = 'Share Link';
                if (isset($pair[1]))
                    $bName = $pair[1];


                $dName = $prefix . 'fbshare_' . $videolist_row_id . 'x' . $videoid;
                $tStyle = 'width:' . $w . 'px;height:20px;border: 1px #29447e solid;background-color:#5972a7;color:white;font-size:12px;font-weight:bold;text-align:center;position:relative;';
                $tStyle2 = 'border-top:#8a9cc2 1px solid;width:' . ($w - 2) . 'px;height:18px;padding:0px;font-decoration:none;';
                $result = '
	<div id="' . $dName . '"></div>
	<script>
		var theURL=escape(' . $link . ');

		var fbobj=document.getElementById("' . $dName . '");
		var sBody=\'<a href="https://www.facebook.com/sharer/sharer.php?u=\'+theURL+\'" target="_blank" style="color:white;"><div style="' . $tStyle . '"><div style="' . $tStyle2 . '">' . $bName . '</div>\';
		sBody+=\'<div style="position:absolute;bottom:0;left:0;margin-bottom:-2px;width:' . $w . 'px;height:1px;border-bottom:1px solid #e5e5e5;"></div>\';
		sBody+=\'</div></a>\';
	        fbobj.innerHTML = sBody;
	</script>
	';
                return $result;
                break;
            //------------------------------------------------------------------------------------------------------------
            case 'facebook_like':

                $FBLanguage = '';
                if (isset($pair[1]))
                    $FBLanguage = $pair[1];

                $dName = $prefix . 'fblike_' . $videolist_row_id . 'x' . $videoid;
                $result = '
	<div id="' . $dName . '" style="width:' . $w . 'px;"></div>
	<script>
		var theURL=escape(' . $link . ');
		var fbobj=document.getElementById("' . $dName . '");
		var sBody=\'<iframe src="https://www.facebook.com/plugins/like.php?href=\';
		sBody+=theURL;
		sBody+=\'&layout=button_count&locale=' . $FBLanguage . '&show_faces=false&action=like&font=tahoma&colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; height:20px" ></iframe>\';
	        fbobj.innerHTML = sBody;
	</script>
	';
                return $result;
                break;
            //------------------------------------------------------------------------------------------------------------
            case 'twitter':

                $TwitterAccount = '';//"YoutubeGallery";
                if (isset($pair[1]))
                    $TwitterAccount = $pair[1];
                else
                    return '<p style="color:white;background-color:red;">Set Twitter Account.<br/>Example: [social:twitter,JoomlaBoat]</p>';

                $dName = $prefix . 'witter_' . $videolist_row_id . 'x' . $videoid;
                $result = '
	<div id="' . $dName . '" style="width:' . $w . 'px;"></div>
	<script>
		var theURL=escape(' . $link . ');
		var twobj=document.getElementById("' . $dName . '");
		var TwBody=\'<a href="https://twitter.com/share" class="twitter-share-button" data-url="\'+theURL+\'" data-via="' . $TwitterAccount . '" data-hashtags="\'+theURL+\'">Tweet</a>\';
		twobj.innerHTML = TwBody;
		!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
	</script>
	';
                return $result;
                break;
        }
    }
}