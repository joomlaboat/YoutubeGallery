<?php
/**
 * YoutubeGallery for Joomla!
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file

namespace YouTubeGallery;

defined('_JEXEC') or die('Restricted access');

use \Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use \YGPagination;

class Pagination
{
    public static function Pagination(&$theme_row, $the_gallery_list, $width, $total_number_of_rows)
    {
        $mainframe = Factory::getApplication();

        if (((int)$theme_row->es_customlimit) == 0) {
            //limit=0; // UNLIMITED
            //No pagination - all items shown
            return '';
        } else
            $limit = (int)$theme_row->es_customlimit;

        $limitstart = Factory::getApplication()->input->getInt('ygstart', 0);

        $pagination = Pagination::getPagination($total_number_of_rows, $limitstart, $limit, $theme_row);

        $paginationcode = '';

        if ($limit == 0) {
            $paginationcode .= '
				<table cellspacing="0" style="padding:0px;width:' . $width . 'px;border-style: none;"  border="0" >
				<tr style="height:30px;border-style: none;border-width:0px;">
				<td style="text-align:left;width:140px;vertical-align:middle;border: none;">' . Text::_('SHOW') . ': ' . $pagination->getLimitBox("") . '</td>
				<td style="text-align:right;vertical-align:middle;border: none;"><div class="pagination">' . $pagination->getPagesLinks() . '</div></td>
				</tr>
				</table>
				';
        } else {
            $paginationcode .= '<div class="pagination">' . $pagination->getPagesLinks() . '</div>';
        }

        return $paginationcode;
    }

    public static function getPagination($num, $limitstart, $limit, &$theme_row)
    {
        $AddAnchor = false;
        if ($theme_row->es_openinnewwindow == 2 or $theme_row->es_openinnewwindow == 3) {
            $AddAnchor = true;
        }

        $thispagination = new YGPagination($num, $limitstart, $limit, '', $AddAnchor);

        return $thispagination;
    }
}
