<?php
/**
 * YouTubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');

$s = false;
if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on")
    $s = true;

foreach ($this->items as $i => $item):
    $link2edit = 'index.php?option=com_youtubegallery&view=linksform&layout=edit&id=' . $item->id;
    ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td>
            <?php
            if ($item->es_isvideo) {

                $images = explode(';', ($item->es_imageurl ?? ''));
                if (count($images) > 0 and $item->es_imageurl != '') {

                    $index = 0;
                    if ($item->es_customimageurl != '') {
                        //this allows to select an image
                        if (str_contains($item->custom_imageurl, '#')) {
                            $index = (int)(str_replace('#', '', $item->es_customimageurl));
                            if ($index < 0)
                                $index = 0;
                            if ($index >= count($images))
                                $index = count($images) - 1;

                            $img_ = $images[$index];
                        } else
                            $img_ = $item->es_customimageurl;
                    } else
                        $img_ = $images[0];

                    $parts = explode(',', $img_);
                    $img = $parts[0];

                    if ($s)
                        $img = str_replace('http:', 'https:', $img);

                    //For local imeagse, return one folder back
                    if (!str_contains($img, '://') and $img != '' and $img[0] != '/')
                        $img = '../' . $img;

                    echo '<p style="text-align:center;"><div id="thumbnail' . $item->id . '"><a href="' . $img . '" target="_blank"><img src="' . $img . '" style="width:200px;" /></a></div></p>';

                    echo '<div id="thumbnails' . $item->id . '" style="text-align:center;">';

                    $i = 0;
                    foreach ($images as $img_) {
                        $parts = explode(',', $img_);
                        $img = $parts[0];

                        if ($i == $index)
                            echo $i . '  ';
                        else {
                            //show another thumbnail image on link click
                            $link = 'changeThumb(' . $item->id . ',\'' . $item->es_imageurl . '\',' . $i . ')';
                            $alt = 'Thumbnail ' . $parts[1] . 'x' . $parts[2];
                            echo '<a href="javascript:' . $link . ';" alt="' . $alt . '" title="' . $alt . '" />' . $i . '  </a>';
                        }
                        $i++;
                    }
                    echo '</div>';
                }

            } else
                echo 'Playlist/Videolist';
            ?>
        </td>
        <td><a href="<?php echo $item->link; ?>" target="_blank"><?php echo $item->es_videosource; ?></a></td>
        <td><a href="<?php echo $item->link; ?>" target="_blank"><?php echo $item->es_videoid; ?></a></td>
        <td>
            <div id="video_<?php echo $item->id; ?>_title"><?php echo $item->es_title; ?></div>
        </td>
        <td>
            <div id="video_<?php echo $item->id; ?>_description"><?php echo $item->es_description; ?></div>
        </td>
        <td>
            <div id="video_<?php echo $item->id; ?>_lastupdate"><?php echo $item->es_lastupdate; ?></div>
        </td>
    </tr>

    <?php
    if (isset($item->es_error) and $item->es_error != '') {
        echo '<tr><td colspan="6" style="color:red">' . $item->es_error . '</td></tr>';
    }
    ?>

<?php endforeach; ?>
