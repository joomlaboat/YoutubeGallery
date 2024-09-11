<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;

// import Joomla view library
jimport('joomla.application.component.view');

class YoutubeGalleryViewYoutubeGallery extends HtmlView
{
    // Overwriting JView display method
    var $youtubegallerycode;
    var $Model;

    function display($tpl = null)
    {
        // Assign data to the view
        $this->youtubegallerycode = $this->get('YoutubeGalleryCode');

        $this->Model = $this->getModel();

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            Factory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');
            return false;
        }

        // Display the view
        parent::display($tpl);
    }

    function htmlEscape($var, $charset = 'UTF-8', $shorten = false, $length = 40)
    {
        if (isset($var) && is_string($var) && strlen($var) > 0) {
            $filter = new JFilterInput();
            $string = $filter->clean(html_entity_decode(htmlentities($var, ENT_COMPAT, $charset)), 'HTML');
            if ($shorten) {
                return self::shorten($string, $length);
            }
            return $string;
        } else {
            return '';
        }
    }
}
