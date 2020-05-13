<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class YoutubeGalleryViewYoutubeGallery extends JViewLegacy
{
        // Overwriting JView display method
        var $youtubegallerycode;

        function display($tpl = null)
        {
                // Assign data to the view
                $this->youtubegallerycode = $this->get('YoutubeGalleryCode');

                // Check for errors.
                if (count($errors = $this->get('Errors')))
                {
                        JFactory::getApplication()->enqueueMessage( implode('<br />', $errors), 'error');
                        return false;
                }

                // Display the view
                parent::display($tpl);
        }
}
