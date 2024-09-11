<?php
/**
 * YouTubeGallery API for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');

require_once('data.php');

class YouTubeGalleryAPIMisc
{
    static public function APIKey_Youtube()
    {
        $keys = YouTubeGalleryAPIMisc::APIKeys_Youtube();
        if (count($keys) == 0)
            return '';

        $index = rand(0, count($keys) - 1);

        return $keys[$index];
    }

    static public function APIKey_Vimeo_Consumer_Key(): string
    {
        /*
        Vimeo API

        In order to allow Youtube Gallery to fetch metadata (title,description etc) of the Vimeo Video you have to register your own instance of Youtube Gallery.
        https://developer.vimeo.com/apps/new

        Type 'YoutubeGallery Your Site/Name' into 'App Name' field during registration.
        */
        return '30ee2d9b75c95e4d1457402a8ef6be9f5bea209e';//Client ID (Also known as Consumer Key or API Key)
    }

    static public function APIKey_Vimeo_Consumer_Secret(): string
    {
        return '7d028cf274f1e0f644267bcae524be455bce4556';
    }

    static public function APIKey_Vimeo_Oauth_Access_Token(): string
    {
        return '';
    }

    static public function APIKey_SoundCloud_ClientID(): string
    {
        return '15b62a25f18d50104a0860bb62ed4b8f';
    }

    /*
    public static function getMaxResults($spq, &$option): int
    {
        $count = 0;
        $pair = explode('&', $spq);
        foreach ($pair as $p) {
            $opt = explode('=', $p);
            if ($opt[0] == 'maxResults') {
                $option = $opt[0] . '=' . $opt[1];
                $count = (int)$opt[1];
            }
        }

        if ($count == 0)
            $count = 50;

        return $count;
    }
*/
    public static function getValueByAlmostTag($HTML_SOURCE, $AlmostTagStart, $AlmostTagEnd = '"'): string
    {
        $vlu = '';

        $strPartLength = strlen($AlmostTagStart);
        $p1 = strpos($HTML_SOURCE, $AlmostTagStart);
        if ($p1 > 0) {
            $p2 = strpos($HTML_SOURCE, $AlmostTagEnd, $p1 + $strPartLength);
            $vlu = substr($HTML_SOURCE, $p1 + $strPartLength, $p2 - $p1 - $strPartLength);
        }
        return $vlu;
    }

    /*
        public static function getNumberOfPayments()
        {
            $db = Factory::getDBO();
            $query = 'SELECT COUNT(id) AS c FROM #__customtables_table_payments';

            try {
                $db->setQuery($query);
                $recs = $db->loadAssocList();
            } catch (RuntimeException $e) {
                return 0;
            }

            if (count($recs) == 0)
                return 0;

            return (int)$recs[0]['c'];
        }
    */

    function checkLink(bool $active_key, $theLink, &$isNew, $force_update = false, $videoListId = null, $youtube_data_api_key = '')
    {
        $blankArray = array();
        $vsn = YouTubeGalleryAPIData::getVideoSourceName($theLink);//For link validation

        if ($vsn != '') {
            $videoId = YouTubeGalleryAPIData::getVideoID($theLink, $vsn);//For link validation again

            if ($videoId != '') {
                if (YouTubeGalleryAPIData::isVideoList($vsn)) {

                    $videos_rows = $this->getVideoRecords($vsn, $videoId, true);

                    if (count($videos_rows) == 0) {
                        $isNew = 1;
                        return $this->update_cache_table($theLink, $active_key, $videoListId, $youtube_data_api_key);//new
                    } else {
                        $isNew = 0;

                        if ($force_update)
                            return $this->update_cache_table($theLink, $active_key, $videoListId, $youtube_data_api_key);//not new
                        else
                            return $videos_rows;//not new
                    }
                } else {
                    $videos_rows = $this->getVideoRecords($vsn, $videoId, true);
                    //Think about updating videos
                    if (count($videos_rows) == 0) {
                        //Video not found in database, try to grab it from the provider
                        $isNew = 1;
                        return $this->update_cache_table($theLink, $active_key, $videoListId, $youtube_data_api_key);//new
                    } else {
                        $isNew = 0;
                        if ($force_update)
                            return $this->update_cache_table($theLink, $active_key, $videoListId, $youtube_data_api_key);//not new
                        else
                            return $videos_rows;//not new
                    }
                }
            } else {
                $blankArray['es_status'] = -4;
                $blankArray['es_error'] = 'YoutubeGalleryAPI (' . $vsn . '): Not supported video link.';

                if ($videoListId != null)
                    $blankArray['es_videolist'] = (int)$videoListId;
            }
        } else {
            $blankArray['es_status'] = -3;
            $blankArray['es_error'] = 'YoutubeGalleryAPI: Not supported video source.';

            if ($videoListId != null)
                $blankArray['es_videolist'] = (int)$videoListId;
        }
        return $blankArray;
    }

    function getVideoRecords($videosource, $videoid, $noNullDate = false)
    {
        $db = Factory::getDBO();

        $item = YouTubeGalleryAPIMisc::getBlankArray(true);
        $keys = array_keys($item);
        $selects = implode(',', $keys);

        $wheres = array();
        $wheres[] = '(es_videosource=' . $db->quote($videosource) . ' AND es_videoid=' . $db->quote($videoid) . ')';
        if ($noNullDate) {
            $wheres[] = 'es_lastupdate IS NOT NULL';
            $wheres[] = 'DATEDIFF(NOW(), es_lastupdate)<1';
        }

        $query = 'SELECT ' . $selects . ' FROM #__customtables_table_youtubegalleryvideos AS l WHERE ' . implode(' AND ', $wheres);

        $db->setQuery($query);
        $recs = $db->loadAssocList();

        if (count($recs) == 1) {
            $rec = $recs[0];
            if ((int)$rec['es_isvideo'] == 0) {
                $where = 'INSTR(' . $db->quote($rec['es_videoids']) . ',CONCAT(",",es_videoid,","))';
                $query = 'SELECT ' . $selects . ' FROM #__customtables_table_youtubegalleryvideos WHERE ' . $where;

                $db->setQuery($query);
                $recs_videos = $db->loadAssocList();
                return array_merge($recs, $recs_videos);
            }

        }
        return $recs;
    }

    static public function getBlankArray($isPublic = false): array
    {
        $blankArray = array(
            'id' => 0,
            'es_videosource' => '',
            'es_videoid' => '',
            'es_videoids' => '',
            'es_trackid' => '',
            'es_isvideo' => 0,
            'es_link' => '',
            'es_lastupdate' => null,

            'es_imageurl' => '',
            'es_title' => '',
            'es_description' => '',
            'es_publisheddate' => '',
            'es_duration' => 0,

            'es_ratingaverage' => 0,
            'es_ratingmax' => 0,
            'es_ratingmin' => 0,
            'es_ratingnumberofraters' => 0,

            'es_statisticsfavoritecount' => 0,
            'es_statisticsviewcount' => 0,
            'es_keywords' => '',
            'es_likes' => 0,
            'es_dislikes' => '',
            'es_commentcount' => 0,

            'es_channelusername' => '',
            'es_channeltitle' => '',
            'es_channelsubscribers' => 0,
            'es_channelsubscribed' => 0,
            'es_channellocation' => '',
            'es_channelcommentcount' => 0,
            'es_channelviewcount' => 0,
            'es_channelvideocount' => 0,
            'es_channeldescription' => '',

            'es_status' => 0,
            'es_error' => '',
            'es_rawdata' => null,
            'es_latitude' => null,
            'es_longitude' => null,
            'es_altitude' => null

        );

        if (!$isPublic) {
            $blankArray['es_datalink'] = '';
            $blankArray['es_rawdata'] = null;
        }

        //http://api.joomlaboat.com/youtube-gallery?query=aHR0cHM6Ly93d3cueW91dHViZS5jb20vd2F0Y2g/dj0wSEwtTjlvT2pjcyZsaXN0PVJETU11YllHRERJdXVoMCZpbmRleD0yNw==
        return $blankArray;
    }

    function update_cache_table($theLink, bool $active_key, $videoListId = null, $youtube_data_api_key = '')
    {
        $videoList = YouTubeGalleryAPIData::formVideoList($theLink, $active_key, $youtube_data_api_key);
        $parent_id = null;
        $db = Factory::getDBO();

        for ($i = 0; $i < count($videoList); $i++) {
            $g = $videoList[$i];

            if ($videoListId != null)
                $g['es_videolist'] = (int)$videoListId;

            $fields = YouTubeGalleryAPIMisc::makeSetList($g, $parent_id);
            $record_id = $this->isVideo_record_exist($g['es_videosource'], $g['es_videoid'], $videoListId);

            if ($record_id == 0) {
                $fields[] = $db->quoteName('es_allowupdates') . '=1';

                if ($videoListId != null)
                    $fields[] = $db->quoteName('es_videolist') . '=' . (int)$videoListId;

                $query = 'INSERT #__customtables_table_youtubegalleryvideos SET ' . implode(', ', $fields);

                $db->setQuery($query);
                $db->execute();

                $record_id_new = $db->insertid();
                $g['id'] = $record_id_new;

                if ((int)$g['es_isvideo'] == 0)
                    $parent_id = $record_id_new;
            } else {
                $query = 'UPDATE #__customtables_table_youtubegalleryvideos SET ' . implode(', ', $fields) . ' WHERE id=' . $record_id;
                $g['id'] = $record_id;
                $db->setQuery($query);
                $db->execute();
            }

            //To return clean and secure record
            $g['es_datalink'] = '';
            $g['es_rawdata'] = null;
            $videoList[$i] = $g;
        }
        return $videoList;
    }

    protected static function makeSetList($g, &$parent_id): array
    {
        $db = Factory::getDBO();

        $g_title = str_replace('"', '&quot;', $g['es_title']);
        $g_description = str_replace('"', '&quot;', $g['es_description']);

        $fields = array();

        if ((int)$g['es_isvideo'] == 0)
            $parent_id = null;

        if ($parent_id != null)
            $fields[] = $db->quoteName('es_parentid') . '=' . (int)$parent_id;

        if ((int)$g['es_isvideo'] == 0 and isset($g['es_videoids']))//Video List - Playlist etc
            $fields[] = $db->quoteName('es_videoids') . '=' . $db->quote($g['es_videoids']);

        $fields[] = $db->quoteName('es_videosource') . '=' . $db->quote($g['es_videosource']);

        $fields[] = $db->quoteName('es_videoid') . '=' . $db->quote($g['es_videoid']);

        if (isset($g['es_trackid']))
            $fields[] = $db->quoteName('es_trackid') . '=' . $db->quote($g['es_trackid']);

        if (isset($g['es_datalink']))
            $fields[] = $db->quoteName('es_datalink') . '=' . $db->quote($g['es_datalink']);

        $fields[] = $db->quoteName('es_lastupdate') . '=NOW()';//.$db->quote($g['es_lastupdate']);

        if (isset($g['es_rawdata']))
            $fields[] = $db->quoteName('es_rawdata') . '=NULL';//.$db->quote($g['es_rawdata']);

        if ($g['es_imageurl'] != '')
            $fields[] = $db->quoteName('es_imageurl') . '=' . $db->quote($g['es_imageurl']);

        if ($g['es_title'] != '')
            $fields[] = $db->quoteName('es_title') . '=' . $db->quote($g_title);

        if ($g['es_description'] != '')
            $fields[] = $db->quoteName('es_description') . '=' . $db->quote($g_description);

        $fields[] = $db->quoteName('es_link') . '=' . $db->quote($g['es_link']);

        $fields[] = $db->quoteName('es_isvideo') . '=' . $g['es_isvideo'];

        if (isset($g['es_publisheddate'])) {
            $publisheddate = date('Y-m-d H:i:s', strtotime($g['es_publisheddate']));
            $fields[] = $db->quoteName('es_publisheddate') . '=' . $db->quote($publisheddate);
        }

        if (isset($g['es_duration']))
            $fields[] = $db->quoteName('es_duration') . '=' . (int)$g['es_duration'];

        if (isset($g['es_ratingaverage']))
            $fields[] = $db->quoteName('es_ratingaverage') . '=' . (float)$g['es_ratingaverage'];

        if (isset($g['es_ratingmax']))
            $fields[] = $db->quoteName('es_ratingmax') . '=' . (int)$g['es_ratingmax'];

        if (isset($g['es_ratingmin']))
            $fields[] = $db->quoteName('es_ratingmin') . '=' . (int)$g['es_ratingmin'];

        if (isset($g['es_ratingnumberofraters']))
            $fields[] = $db->quoteName('es_ratingnumberofraters') . '=' . (int)$g['es_ratingnumberofraters'];

        if (isset($g['es_statisticsfavoritecount']))
            $fields[] = $db->quoteName('es_statisticsfavoritecount') . '=' . (int)$g['es_statisticsfavoritecount'];

        if (isset($g['es_statisticsviewcount']))
            $fields[] = $db->quoteName('es_statisticsviewcount') . '=' . (int)$g['es_statisticsviewcount'];

        if (isset($g['es_keywords'])) {
            if (is_array($g['es_keywords'])) {
                $key_words = implode(',', $g['es_keywords']);
                $fields[] = $db->quoteName('es_keywords') . '=' . $db->quote($key_words);
            } else
                $key_words = '';
        }

        if (isset($g['es_likes']))
            $fields[] = $db->quoteName('es_likes') . '=' . (int)$g['es_likes'];

        if (isset($g['es_dislikes']))
            $fields[] = $db->quoteName('es_dislikes') . '=' . (int)$g['es_dislikes'];

        if (isset($g['es_channelusername']))
            $fields[] = $db->quoteName('es_channelusername') . '=' . $db->quote($g['es_channelusername']);

        if (isset($g['es_channeltitle']))
            $fields[] = $db->quoteName('es_channeltitle') . '=' . $db->quote($g['es_channeltitle']);

        if (isset($g['es_channelsubscribers']))
            $fields[] = $db->quoteName('es_channelsubscribers') . '=' . (int)$g['es_channelsubscribers'];

        if (isset($g['es_channelsubscribed']))
            $fields[] = $db->quoteName('es_channelsubscribed') . '=' . (int)$g['es_channelsubscribed'];

        if (isset($g['es_channellocation']))
            $fields[] = $db->quoteName('es_channellocation') . '=' . $db->quote($g['es_channellocation']);

        if (isset($g['es_channelcommentcount']))
            $fields[] = $db->quoteName('es_channelcommentcount') . '=' . (int)$g['es_channelcommentcount'];

        if (isset($g['es_channelviewcount']))
            $fields[] = $db->quoteName('es_channelviewcount') . '=' . (int)$g['es_channelviewcount'];

        if (isset($g['es_channelvideocount']))
            $fields[] = $db->quoteName('es_channelvideocount') . '=' . (int)$g['es_channelvideocount'];

        if (isset($g['es_channeldescription']))
            $fields[] = $db->quoteName('es_channeldescription') . '=' . $db->quote($g['es_channeldescription']);

        if (isset($g['es_latitude']))
            $fields[] = $db->quoteName('es_latitude') . '=' . (float)$g['es_latitude'];

        if (isset($g['es_longitude']))
            $fields[] = $db->quoteName('es_longitude') . '=' . (float)$g['es_longitude'];

        if (isset($g['es_altitude']))
            $fields[] = $db->quoteName('es_altitude') . '=' . (int)$g['es_altitude'];

        return $fields;
    }

    function isVideo_record_exist($videosource, $videoid, $videolist_id = null)
    {
        $db = Factory::getDBO();

        $wheres = [];

        $wheres[] = $db->quoteName('es_videosource') . '=' . $db->quote($videosource);
        $wheres[] = $db->quoteName('es_videoid') . '=' . $db->quote($videoid);

        if ($videolist_id != null)
            $wheres[] = $db->quoteName('es_videolist') . '=' . (int)$videolist_id;

        $query = 'SELECT id FROM #__customtables_table_youtubegalleryvideos WHERE ' . implode(' AND ', $wheres) . ' LIMIT 1';

        $db->setQuery($query);

        $videos_rows = $db->loadAssocList();

        if (count($videos_rows) == 0)
            return 0;

        $videos_row = $videos_rows[0];

        return $videos_row['id'];
    }
}
