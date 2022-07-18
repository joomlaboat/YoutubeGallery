<?php
/**
 * YoutubeGallery
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_youtubegallery' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'misc.php');

class YGAPI_VideoSource_YoutubeStandard
{


    public static function getVideoIDList($youtubeURL, $optionalparameters, &$playlistid, &$datalink)
    {
        $videolist = array();
        /*
        $linkPair=explode(':',$youtubeURL);

        if(!isset($linkPair[1]))
            return array();

        $url='';

        $playlistid=$linkPair[1];

        switch($linkPair[1])
        {
            case 'top_rated':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/top_rated';
                break;

            case 'top_favorites':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/top_favorites';
                break;

            case 'most_viewed':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/most_viewed';
                break;

            case 'most_shared':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/most_shared';
                break;

            case 'most_popular':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/most_popular';
                break;

            case 'most_recent':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/most_recent';
                break;

            case 'most_discussed':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/most_discussed';
                break;

            case 'most_responded':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/most_responded';
                break;

            case 'recently_featured':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/recently_featured';
                break;

            case 'on_the_web':
                $url='https://gdata.youtube.com/feeds/api/standardfeeds/on_the_web';
                break;

            default:
                return array();
            break;
        }


        //-------------------------------
        $videoitems=array();

        $videolistitem=YouTubeGalleryAPIMisc::getBlankArray();
        $videolistitem['es_videosource']=$vsn;
        $videolistitem['es_link']=$theLink;
        $videolistitem['es_isvideo']=0;
        $videolistitem['es_videoid']=$listid;

        require_once('youtube.php');

        $part='id,snippet';
        $base_url='https://www.googleapis.com/youtube/v3';
        $datalink = $base_url.'/'.$query.'&part='.$part.'&key='.YouTubeGalleryAPIMisc::APIKey_Youtube();
        $videolistitem['es_datalink']=$url;

        $newlist=YGAPI_VideoSource_YoutubePlaylist::getPlaylistVideos($url,$videolistitem);
        $videoitems[]=$videolistitem;
        $videoitems=array_merge($videoitems,$newlist);

        return $videoitems;
        //-------------------------------

        $datalink=$url;


        $optionalparameters_arr=explode(',',$optionalparameters);


        $spq=implode('&',$optionalparameters_arr);


        $spq=str_replace('max-results','maxResults',$spq);
        $url.= ($spq!='' ? '?'.$spq : '' );

        $xml=false;
        $htmlcode=Helper::getURLData($url);

        if($htmlcode=='')
            return $videolist;

        if(strpos($htmlcode,'<?xml version')===false)
        {
            if(strpos($htmlcode,'Invalid id')===false)
                return 'Cannot load data, Invalid id';

            return 'Cannot load data, no connection';
        }

        $xml = simplexml_load_string($htmlcode);

        if($xml){
            foreach ($xml->entry as $entry)
            {


                //
                $media = $entry->children('http://search.yahoo.com/mrss/');
                $link = $media->group->player->attributes();
                if(isset($link['url']))
                {
                    $videolist[] = $link['url'];
                }
                //

            }
        }
        */

        return $videolist;

    }


}
