<?php
/**
 * Youtube Gallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class JHTMLTheme
{
        public static function render($control_name,$value,$attribute)
        {
				
          $db = JFactory::getDBO();

          $query = 'SELECT id, themename FROM #__youtubegallery_themes ORDER BY themename';
				
         $db->setQuery( $query );
         $themes = $db->loadAssocList( );
         if(!$themes) $themes= array();
		
         //$themes[]=array('id'=>'0','themename'=>'- ROOT');
         if($value=='' and count($themes)>0)
          $value=$themes[0]['id'];
				
        return JHTML::_('select.genericlist',  $themes, $control_name, 'class="inputbox"'.$attribute, 'id', 'themename', $value);
		
				
        }
	
}
