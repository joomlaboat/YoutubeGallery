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


// import Joomla modelform library
jimport('joomla.application.component.modeladmin');

/**
 * YoutubeGallery - Theme Form Model
 */
class YoutubegalleryModelListandthemeselection extends JModelAdmin
{
        /**
         * Returns a reference to the a Table object, always creating it.
         *
         * @param       type    The table type to instantiate
         * @param       string  A prefix for the table class name. Optional.
         * @param       array   Configuration array for model. Optional.
         * @return      JTable  A database object
         */
		public $id;
      
        public function getForm($data = array(), $loadData = true)
        {  
           
        }
}
