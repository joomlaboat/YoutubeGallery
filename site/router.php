<?php
/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

//use YouTubeGalleryDB;
use Joomla\CMS\Factory;
use YouTubeGallery\Helper;

function YouTubeGalleryBuildRoute(&$query) {

       $segments = array();
       if(isset($query['view']))
       {
	      if (empty($query['Itemid'])) {
		     $segments[] = $query['view'];
	      }
              unset( $query['view'] );
       }

       if(isset($query['video']))
       {
	      $segments[] = $query['video'];
		  
		  
		  
	      unset( $query['video'] );
       }
       elseif(isset($query['videoid']))
       {
	      $allowsef=YouTubeGalleryDB::getSettingValue('allowsef');
	      if($allowsef==1)
	      {
		     $videoid=$query['videoid'];

		     $db = Factory::getDBO();

		     $db->setQuery('SELECT alias FROM #__youtubegallery_videos WHERE videoid="'.$videoid.'" LIMIT 1');

		     $rows = $db->loadObjectList();

		     if(count($rows)!=0)
		     {
			    $row=$rows[0];
			    if($row->alias!='')
		         	   $segments[] = $row->alias;

			    unset( $query['videoid'] );
		     }
	      }
       }
       return $segments;

}

function YouTubeGalleryParseRoute($segments)
{
       $vars = array();
       $vars['view'] = 'youtubegallery';

       $sIndex=0;


       if(isset($segments[$sIndex]))
       {
	      $alias=str_replace(':','-',$segments[$sIndex]);
	      $alias=preg_replace('/[^a-zA-Z0-9-_]+/', '', $alias);

	      $db = Factory::getDBO();

	      $db->setQuery('SELECT videoid FROM #__youtubegallery_videos WHERE alias="'.$alias.'" LIMIT 1');

	      $rows = $db->loadObjectList();

	      if(count($rows)==0)
		     $vars['videoid'] = '';
	      else
	      {
			  $row=$rows[0];
			  $vars['videoid'] = $row->videoid;
	      }

       }
       return $vars;
}
