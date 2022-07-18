<?php
/**
 * Youtube Gallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// UNUSED

/*
use YouTubeGallery\Helper;        

$videoid=Factory::getApplication()->input->getVar('videoid');
if($jinput->get('ygvdata','','RAW')!='')
{
	$jinput=Factory::getApplication()->input;
    $video_data=$jinput->get('ygvdata','','RAW');

    $video_data=str_replace('"','\"',$video_data);

    Helper::setRawData($videoid,$video_data);

    $db = Factory::getDBO();
    $query = 'SELECT * FROM #__customtables_table_youtubegalleryvideos WHERE es_videoid="'.$videoid.'"';
    $db->setQuery($query);

    $videos_rows=$db->loadAssocList();
    
    $ygDB=new YouTubeGalleryDB;
    $ygDB->RefreshVideoData($videos_rows,true);

    $query = 'SELECT * FROM #__customtables_table_youtubegalleryvideos WHERE es_videoid="'.$videoid.'"';
    $db->setQuery($query);

    $videos_rows=$db->loadAssocList();

    if(count($videos_rows)!=0)
    {
        $row=$videos_rows[0];
        echo '*title_start*='.$row['es_title'].'*title_end*';
        echo '*description_start*='.$row['es_description'].'*description_end*';
        echo '*lastupdate_start*='.$row['es_lastupdate'].'*lastupdate_end*';
    }
    else
        echo '*status_start*=video not found*status_end*';
}
else
    echo 'Data not set.';
*/