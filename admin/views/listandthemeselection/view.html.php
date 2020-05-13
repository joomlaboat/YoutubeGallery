<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @version 5.0.0
 * @author Ivan Komlev< <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class YoutubegalleryViewListandthemeselection extends JViewLegacy
{
        /**
         * display method of Youtube Gallery view
         * @return void
         */
        public function display($tpl = null)
        {
		$this->session     = JFactory::getSession();
                parent::display($tpl);

        }
        
        
        function checkIfPluginIsEnabled()
        {
                
                
                $db = JFactory::getDBO();

          $query = 'SELECT extension_id, enabled FROM #__extensions WHERE '.$db->quoteName('type').'="plugin" AND folder="content" AND '.$db->quoteName('element').'="youtubegallery" LIMIT 1';
				
         $db->setQuery( $query );
         if (!$db->query())    die( $db->stderr());
         $plugins = $db->loadAssocList();
         
         if(count($plugins)==0)
                return null;
        
        	return $plugins[0];
                
        }
        
        function EnablePlugin()
        {
                
                
                $db = JFactory::getDBO();

          $query = 'UPDATE #__extensions SET enabled=1 WHERE '.$db->quoteName('type').'="plugin" AND folder="content" AND '.$db->quoteName('element').'="youtubegallery"';
          
          
				
         $db->setQuery( $query );
         if (!$db->query())    die( $db->stderr());
        
                
        }
}
