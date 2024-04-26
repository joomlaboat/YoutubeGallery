<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\MVC\Model\AdminModel;

// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * YoutubeGallery - Theme Form Model
 */
class YoutubegalleryModelListandthemeselection extends AdminModel
{
    public $id;

    public function getForm($data = array(), $loadData = true)
    {

    }
}
