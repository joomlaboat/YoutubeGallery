<?php
/**
 * YoutubeGallery Joomla! Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
use Joomla\CMS\Factory;

defined('_JEXEC') or die('Restricted access');
 
// import Joomla controller library
jimport('joomla.application.component.controller');
 
    
    class YoutubeGalleryController extends JControllerLegacy
    {
        /**
         * display task
         *
         * @return void
         */
        function display($cachable = false, $urlparams = null) 
        {
                // set default view if not set
                $jinput=Factory::getApplication()->input;
                $view=$jinput->getCmd('view', 'linkslist');
                
                $jinput->set('view', $view);
                
                // call parent behavior
                parent::display($cachable);
        }
    }
