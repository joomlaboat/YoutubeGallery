<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

defined('_JEXEC') or die('Restricted access');

class YouTubeGalleryRendererJS extends YouTubeGalleryRenderer
{
	
protected static function getResponsiveCode_JS($instance_id,$width,$height)
{
	$result='
<!-- Make it responsive to window size -->
<script type="text/javascript">
//<![CDATA[

function YoutubeGalleryClientWidth'.$instance_id.'() {
	return YoutubeGalleryResults'.$instance_id.' (
		window.innerWidth ? window.innerWidth : 0,
		document.documentElement ? document.documentElement.clientWidth : 0,
		document.body ? document.body.clientWidth : 0
	);
}
function YoutubeGalleryScrollLeft'.$instance_id.'() {
	return YoutubeGalleryResults'.$instance_id.' (
		window.pageXOffset ? window.pageXOffset : 0,
		document.documentElement ? document.documentElement.scrollLeft : 0,
		document.body ? document.body.scrollLeft : 0
	);
}
function YoutubeGalleryFindHorizontalOffset'.$instance_id.'(id) {
	var node = document.getElementById(id);
	var curleft = 0;
	var curleftscroll = 0;
	var scroll_left = YoutubeGalleryScrollLeft'.$instance_id.'();
	if (node.offsetParent) {
	        do {
		        curleft += node.offsetLeft;
		        curleftscroll =0;
		} while (node = node.offsetParent);

		var imaged_x=(curleft - curleftscroll)-scroll_left;
		return imaged_x;
		}
		return 0;
	}
function YoutubeGalleryResults'.$instance_id.'(n_win, n_docel, n_body) {
	var n_result = n_win ? n_win : 0;
	if (n_docel && (!n_result || (n_result > n_docel)))
		n_result = n_docel;
		return n_body && (!n_result || (n_result > n_body)) ? n_body : n_result;
	}
function YoutubeGalleryAutoResizePlayer'.$instance_id.'(){
	var clientWidth=YoutubeGalleryClientWidth'.$instance_id.'();



	var playerObject=document.getElementById("youtubegalleryplayerid_'.$instance_id.'");
	if(playerObject==null) return false;
	var mainObject=document.getElementById("YoutubeGalleryMainContainer'.$instance_id.'");

	var parentObject=mainObject.parentNode;
	var parentWidth=parentObject.offsetWidth;

	var secondaryObject=document.getElementById("YoutubeGallerySecondaryContainer'.$instance_id.'");
	var playerWidth='.$width.';
	var x=YoutubeGalleryFindHorizontalOffset'.$instance_id.'("YoutubeGalleryMainContainer'.$instance_id.'");

	var setWidth=false;

	if(playerWidth>parentWidth)
	{
		playerWidth=parentWidth;
		setWidth=true;
	}


	if(x+playerWidth>clientWidth)
	{
		playerWidth=clientWidth-x;
		setWidth=true;
	}

	if(playerObject.width!=playerWidth)
			setWidth=true;

	if(setWidth)
	{
		mainObject.style.width= (playerWidth) + "px";

		var newH='.$height.'/('.$width.'/playerWidth);

		secondaryObject.style.width= (playerWidth) + "px";
		secondaryObject.style.height= (newH) + "px";

		playerObject.width= (playerWidth) + "px";
		playerObject.height= (newH) + "px";
	}
}

window.onresize = function() { YoutubeGalleryAutoResizePlayer'.$instance_id.'(); }

//]]>
</script>
';

		$document = JFactory::getDocument();
		$document->addCustomTag($result);

	}
	
}