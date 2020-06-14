<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');


class YouTubeGalleryPagination
{
	
	public static function Pagination(&$theme_row,$the_gallery_list,$width,$total_number_of_rows)
	{
		$mainframe = JFactory::getApplication();

		if(((int)$theme_row->customlimit)==0)
		{
			//limit=0; // UNLIMITED
			//No pagination - all items shown
			return '';
		}
		else
			$limit = (int)$theme_row->customlimit;

		$limitstart = JFactory::getApplication()->input->getInt('ygstart', 0);

		$pagination=YouTubeGalleryPagination::getPagination($total_number_of_rows,$limitstart,$limit,$theme_row);

		$paginationcode='';

		if($limit==0)
		{
			$paginationcode.='
				<table cellspacing="0" style="padding:0px;width:'.$width.'px;border-style: none;"  border="0" >
				<tr style="height:30px;border-style: none;border-width:0px;">
				<td style="text-align:left;width:140px;vertical-align:middle;border: none;">'.JText::_( 'SHOW' ).': '.$pagination->getLimitBox("").'</td>
				<td style="text-align:right;vertical-align:middle;border: none;"><div class="pagination">'.$pagination->getPagesLinks().'</div></td>
				</tr>
				</table>
				';
		}
		else
		{
			$paginationcode.='<div class="pagination">'.$pagination->getPagesLinks().'</div>';

		}

		return $paginationcode;
	}
	
	public static function getPagination($num,$limitstart,$limit,&$theme_row)
	{
				$AddAnchor=false;
				if($theme_row->openinnewwindow==2 or $theme_row->openinnewwindow==3)
				{
					$AddAnchor=true;
				}
				//require_once(JPATH_SITE.DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_youtubegallery'.DIRECTORY_SEPARATOR.'includes'.DIRECTORY_SEPARATOR.'pagination.php');
				require_once('pagination.php');
				$thispagination = new YGPagination($num, $limitstart, $limit, '', $AddAnchor );

				return $thispagination;
	}
	
}