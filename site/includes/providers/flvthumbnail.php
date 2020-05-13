<?php
/**
 * Youtube Gallery Joomla! 3.0 Native Component
 * @version 5.0.0
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

$jinput=JFactory::getApplication()->input;

$videofile= $jinput->getString('videofile');
$videofile= html2txt($videofile);
$videofile= '../../../'.$videofile;

if(!file_exists($videofile))
{
    $application = JFactory::getApplication();
	$application->enqueueMessage('File not found.', 'error');
}
else
{
    require_once('flv4php'.DIRECTORY_SEPARATOR.'FLV.php');

    $flv = new FLV($videofile);

    echo $flv->getFlvThumb();

    function html2txt($document)
	{
		$search = array('@<script[^>]*?>.*?</script>@si',  // Strip out javascript
               '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
               '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
               '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments including CDATA
		);
		$text = preg_replace($search, '', $document);
		return $text;
	}

}
