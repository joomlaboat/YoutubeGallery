<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'misc.php');
$getinfomethod=YouTubeGalleryMisc::getSettingValue('getinfomethod');

$s=false;
if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
	$s=true;

?>
<?php foreach($this->items as $i => $item):

        $link2edit='index.php?option=com_youtubegallery&view=linksform&layout=edit&id='.$item->id;

        ?>


        <tr class="row<?php echo $i % 2; ?>">
                <td>

		<?php
			if($item->isvideo)
			{

			$images=explode(';',$item->imageurl);
			if(count($images)>0 and $item->imageurl!='')
			{

				$index=0;
				if($item->custom_imageurl!='')
				{
					//this allows to select an image
					if(!(strpos($item->custom_imageurl,'#')===false))
					{
						$index=(int)(str_replace('#','',$item->custom_imageurl));
						if($index<0)
							$index=0;
						if($index>=count($images))
							$index=count($images)-1;

						$img_=$images[$index];
					}
					else
						$img_=$item->custom_imageurl;
				}
				else
					$img_=$images[0];
				
				$parts=explode(',',$img_);
				$img=$parts[0];

				if($s)
					$img=str_replace('http:','https:',$img);

				//For local imeagse, return one folder back
				if(strpos($img,'://')===false and $img!='' and $img[0]!='/')
					$img='../'.$img;

				echo '<p style="text-align:center;"><div id="thumbnail'.$item->id.'"><a href="'.$img.'" target="_blank"><img src="'.$img.'" style="width:200px;" /></a></div></p>';
				
				echo '<div id="thumbnails'.$item->id.'" style="text-align:center;">';

				$i=0;
				foreach($images as $img_)
				{
					$parts=explode(',',$img_);
					$img=$parts[0];
					
					if($i==$index)
						echo $i.'  ';
					else
					{
						//show another thumbnail image on link click
						$link='changeThumb('.$item->id.',\''.$item->imageurl.'\','.$i.')';//document.getElementById(\'thumbnail'.$item->id.'\').src=\''.$img.'\'';
						$alt='Thumbnail '.$parts[1].'x'.$parts[2];
						echo '<a href="javascript:'.$link.';" alt="'.$alt.'" title="'.$alt.'" />'.$i.'  </a>';
					}
					$i++;
				}
				echo '</div>';
			}

			}else
				echo 'Playlist/Videolist';
		?>
		</td>
                <td><a href="<?php echo $item->link; ?>" target="_blank"><?php echo $item->videosource; ?></a></td>
                <td><a href="<?php echo $item->link; ?>" target="_blank"><?php echo $item->videoid; ?></a></td>
                <td><div id="video_<?php echo $item->id;?>_title"><?php echo $item->title; ?></div></td>
                <td><div id="video_<?php echo $item->id;?>_description"><?php echo $item->description; ?></div></td>
                <td><div id="video_<?php echo $item->id;?>_lastupdate"><?php echo $item->lastupdate; ?></div></td>
				
				<?php /*
                <td style="text-align: center;">

		<?php

		if($getinfomethod=='js' or $getinfomethod=='jsmanual')
		{
			$pair=explode(',',$item->datalink);
			$link=$pair[0];

			echo '
		<script>
		function UpdateVideoData_'.$item->id.'()
		{
			UpdateVideoData("'.$link.'","'.$item->videoid.'",'.$item->id.');
		}
		</script>
		';
			echo '<div id="video_'.$item->id.'_status"><a href="javascript:UpdateVideoData_'.$item->id.'()">Update</a></div>';
		}
		else
		{
			if($item->status==200)
			        echo '<span style="color:green;">Ok</span>';
			elseif($item->status==0)
			        echo '<span style="color:black;">-</span>';
			else
			        echo '<span style="color:red;font-weight:bold;">Error: '.$item->status.'</span>';
		}

                ?>

		</td>
                <td><?php echo $item->ordering; ?></td> */ ?>
        </tr>


<?php endforeach; ?>
