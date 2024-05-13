<?php
/**
 * YoutubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ListModel;

//jimport('joomla.application.component.modellist');
//jimport('joomla.filesystem.file');
//jimport('joomla.filesystem.folder');
//jimport('joomla.filesystem.archive');

/**
 * YoutubeGallery - Theme Import Model
 */
class YoutubeGalleryModelThemeImport extends ListModel
{
    function upload_theme(&$msg)
    {
        $jinput = Factory::getApplication()->input;
        $file = $_FILES['themefile'];

        if (!isset($file['name'])) {
            $msg = 'No file has bee uploaded.';
            return false; //wrong file format, expecting .zip
        }

        $uploadedfile = basename($file['name']);
        echo 'Uploaded file: "' . $uploadedfile . '"<br/>';


        $folder_name = $this->getFolderNameOnly($file['name']);
        if ($folder_name == '') {
            $msg = 'Wrong file format, expecting ".zip"';
            return false; //wrong file format, expecting .zip
        }


        $this->prepareFolderYG();
        $path = JPATH_SITE . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR;

        if (file_exists($path . $uploadedfile)) {
            echo 'Existing "' . $uploadedfile . '" file deleted.<br/>';
            unlink($path . $uploadedfile);
        }


        if (!move_uploaded_file($file['tmp_name'], $path . $uploadedfile)) {
            $msg = 'Cannot Move File';

            return false;
        }

        echo 'File "' . $uploadedfile . '" moved form temporary location.<br/>';


        $folder_name_created = $this->prepareFolder($folder_name, $path);
        echo 'Folder "tmp' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR . $folder_name_created . '" created.<br/>';

        $zip = JArchive::getAdapter('zip');

        $zip->extract($path . $uploadedfile, $path . $folder_name_created);
        echo 'File "' . $uploadedfile . '" extracted.<br/>';

        unlink($path . $uploadedfile);
        echo 'File "' . $uploadedfile . '" deleted.<br/>';


        if (file_exists($path . $folder_name_created . DIRECTORY_SEPARATOR . 'theme.txt')) {
            //Ok archive is fine, looks like it is really YG theme.
            $filedata = file_get_contents($path . $folder_name_created . DIRECTORY_SEPARATOR . 'theme.txt');
            if ($filedata == '') {
                //Archive doesn't containe Gallery Data
                $msg = 'Gallery Data file is empty';

                JFolder::delete($path . 'youtubegallery');
                return false;
            }

            $theme_row = unserialize($filedata);

            $theme_row->es_themename = $this->getThemeName(str_replace('"', '', $theme_row->es_themename), true);//force to install
            if ($theme_row->es_themename != '') {
                $theme_row->es_themedescription = file_get_contents($path . $folder_name_created . DIRECTORY_SEPARATOR . 'about.txt');

                echo 'Theme Data Found<br/>';

                if ($theme_row->es_mediafolder != '') {
                    //prepare media folder
                    $theme_row->es_mediafolder = $this->prepareFolder($theme_row->es_mediafolder, JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR);
                    echo Text::_('COM_YOUTUBEGALLERY_FIELD_MEDIAFOLDER_LABEL') . ' "' . $theme_row->es_mediafolder . '" created.<br/>';

                    //move files
                    $this->moveFiles(
                        JPATH_SITE . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'youtubegallery' . DIRECTORY_SEPARATOR . $folder_name_created,
                        JPATH_SITE . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $theme_row->es_mediafolder);
                }


                JFolder::delete($path);

                echo 'Theme Name: ' . $theme_row->es_themename . '<br/>';

                $this->saveTheme($theme_row);
                echo 'Theme Imported<br/>';
            }
        } else {
            $msg = "Archive doesn't containe Gallery Data";
            return false;
        }

        return true;
    }

    function getFolderNameOnly($filename)
    {
        $p = explode('.', $filename);

        if (count($p) < 2)
            return '';

        if (strtolower($p[1]) != 'zip')
            return '';

        return $p[0];
    }

    function prepareFolderYG()
    {
        $path = JPATH_SITE . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR;

        if (file_exists($path . 'youtubegallery')) {
            //JFolder::delete($path.'youtubegallery');
        } else {
            echo 'Folder "tmp/youtubegallery" created.<br/>';
            mkdir($path . 'youtubegallery');
        }
    }

    function prepareFolder($folder_base_name, $path)
    {
        $this->prepareFolderYG();

        if (file_exists($path . $folder_base_name) or file_exists($path . $folder_base_name . '.zip')) {
            $i = 0;
            do {
                $i++;
                $folder = $folder_base_name . '_' . $i;
            } while (file_exists($path . $folder) or file_exists($path . $folder . '.zip'));
        } else
            $folder = $folder_base_name;

        if (mkdir($path . $folder) === false) {
            echo '<p>Cannot create temporary folder in "tmp/"</p>';
            return '';
        }

        return $folder;
    }

    function getThemeName($themename, $force = false)
    {
        if (!$this->checkIfThemenameExist($themename))
            return $themename;//New theme / all good

        //Theme already exists

        if (!$force and ($themename == 'Default' or $themename == 'SimpleGridJCEPopup')) {
            //Do not update default themes
            return '';
        }

        $i = 0;
        do {
            $i++;
        } while ($this->checkIfThemenameExist($themename . ' (' . $i . ')'));

        return $themename . ' (' . $i . ')';
    }

    function checkIfThemenameExist($themename)
    {
        $db = Factory::getDBO();

        $query = 'SELECT id FROM #__customtables_table_youtubegallerythemes WHERE ' . $db->quoteName('es_themename') . '=' . $db->quote($themename) . ' LIMIT 1';
        $db->setQuery($query);
        $db->execute();
        return $db->getNumRows() > 0;
    }

    function moveFiles($dirpath_from, $dirpath_to)
    {
        $files_to_archive = array();


        //$sys_path=JPATH_SITE.DIRECTORY_SEPARATOR.$dirpath_from;
        $sys_path = $dirpath_from;
        if (file_exists($sys_path) === false) {
            echo '<p>' . Text::_('COM_YOUTUBEGALLERY_FIELD_MEDIAFOLDER_LABEL') . ' "' . $dirpath_from . ' (' . $sys_path . ')" not found.</p>';
            return $files_to_archive;
        }

        if ($handle = opendir($sys_path)) {

            while (false !== ($file = readdir($handle))) {

                if ($file != '.' and $file != '..' and $file != 'theme.txt' and $file != 'about.txt' and !str_contains($file, '.xml') and !str_contains($file, '.php')) {
                    if (!is_dir($sys_path . DIRECTORY_SEPARATOR . $file)) {
                        //$destination_file=JPATH_SITE.DIRECTORY_SEPARATOR.$dirpath_to.DIRECTORY_SEPARATOR.$file;
                        $destination_file = $dirpath_to . DIRECTORY_SEPARATOR . $file;

                        if (file_exists($sys_path . DIRECTORY_SEPARATOR . $file) === false) {
                            echo '<span style="color:red;">file "' . $file . '" (' . $sys_path . DIRECTORY_SEPARATOR . $file . ') not found.</span><br/>';
                        } else {
                            if (!(file_exists($destination_file) === false))
                                unlink($destination_file);

                            if (rename($sys_path . DIRECTORY_SEPARATOR . $file, $destination_file) === false)
                                echo '<span style="color:red;">file "' . $file . '" cannot be moved.</span><br/>';
                            else
                                echo 'File "' . $file . '" moved.<br/>';
                        }
                    }
                }


            }

        }
    }

    function saveTheme(&$theme_row)
    {
        if (isset($theme_row->es_themename))
            $this->saveTheme4($theme_row);
        elseif (isset($theme_row->themename))
            $this->saveTheme3($theme_row);
        else
            return false;
    }

    function saveTheme4(&$theme_row)
    {
        $fields = array();
        $db = Factory::getDBO();
        $fields[] = $db->quoteName('es_themename') . '=' . $db->quote($theme_row->es_themename);
        $fields[] = $db->quoteName('es_width') . '=' . $db->quote($theme_row->es_width);
        $fields[] = $db->quoteName('es_height') . '=' . $db->quote($theme_row->es_height);
        $fields[] = $db->quoteName('es_playvideo') . '=' . $db->quote($theme_row->es_playvideo);
        $fields[] = $db->quoteName('es_repeat') . '=' . $db->quote($theme_row->es_repeat);
        $fields[] = $db->quoteName('es_fullscreen') . '=' . $db->quote($theme_row->es_fullscreen);
        $fields[] = $db->quoteName('es_autoplay') . '=' . $db->quote($theme_row->es_autoplay);
        $fields[] = $db->quoteName('es_related') . '=' . $db->quote($theme_row->es_related);
        $fields[] = $db->quoteName('es_bgcolor') . '=' . $db->quote($theme_row->es_bgcolor);
        $fields[] = $db->quoteName('es_cssstyle') . '=' . $db->quote($theme_row->es_cssstyle);
        $fields[] = $db->quoteName('es_navbarstyle') . '=' . $db->quote($theme_row->es_navbarstyle);
        $fields[] = $db->quoteName('es_thumbnailstyle') . '=' . $db->quote($theme_row->es_thumbnailstyle);
        $fields[] = $db->quoteName('es_listnamestyle') . '=' . $db->quote($theme_row->es_listnamestyle);
        $fields[] = $db->quoteName('es_descrstyle') . '=' . $db->quote($theme_row->es_descrstyle);
        $fields[] = $db->quoteName('es_colorone') . '=' . $db->quote($theme_row->es_colorone);
        $fields[] = $db->quoteName('es_colortwo') . '=' . $db->quote($theme_row->es_colortwo);
        $fields[] = $db->quoteName('es_border') . '=' . $db->quote($theme_row->es_border);
        $fields[] = $db->quoteName('es_openinnewwindow') . '=' . $db->quote($theme_row->es_openinnewwindow);
        $fields[] = $db->quoteName('es_rel') . '=' . $db->quote($theme_row->es_rel);
        $fields[] = $db->quoteName('es_hrefaddon') . '=' . $db->quote($theme_row->es_hrefaddon);
        $fields[] = $db->quoteName('es_customlimit') . '=' . $db->quote($theme_row->es_customlimit);
        $fields[] = $db->quoteName('es_allowplaylist') . '=' . $db->quote($theme_row->es_allowplaylist);

        $fields[] = $db->quoteName('es_controls') . '=' . $db->quote($theme_row->es_controls);
        $fields[] = $db->quoteName('es_youtubeparams') . '=' . $db->quote($theme_row->es_youtubeparams);
        $fields[] = $db->quoteName('es_useglass') . '=' . $db->quote($theme_row->es_useglass);
        $fields[] = $db->quoteName('es_logocover') . '=' . $db->quote($theme_row->es_logocover);
        $fields[] = $db->quoteName('es_customlayout') . '=' . $db->quote($theme_row->es_customlayout);
        $fields[] = $db->quoteName('es_prepareheadtags') . '=' . $db->quote($theme_row->es_prepareheadtags);
        $fields[] = $db->quoteName('es_muteonplay') . '=' . $db->quote($theme_row->es_muteonplay);
        $fields[] = $db->quoteName('es_volume') . '=' . $db->quote($theme_row->es_volume);
        $fields[] = $db->quoteName('es_orderby') . '=' . $db->quote($theme_row->es_orderby);
        $fields[] = $db->quoteName('es_customnavlayout') . '=' . $db->quote($theme_row->es_customnavlayout);
        $fields[] = $db->quoteName('es_responsive') . '=' . $db->quote($theme_row->es_responsive);
        $fields[] = $db->quoteName('es_mediafolder') . '=' . $db->quote($theme_row->es_mediafolder);
        $fields[] = $db->quoteName('es_headscript') . '=' . $db->quote($theme_row->es_headscript);
        $fields[] = $db->quoteName('es_themedescription') . '=' . $db->quote($theme_row->es_themedescription);

        if (isset($theme_row->nocookie))
            $fields[] = $db->quoteName('es_nocookie') . '=' . $db->quote($theme_row->es_nocookie);

        if (isset($theme_row->changepagetitle))
            $fields[] = $db->quoteName('es_changepagetitle') . '=' . $db->quote($theme_row->es_changepagetitle);

        $query = 'INSERT #__customtables_table_youtubegallerythemes SET ' . implode(', ', $fields);

        $db = Factory::getDBO();

        $db->setQuery($query);
        $db->execute();
    }

    function saveTheme3(&$theme_row)
    {
        $fields = array();
        $db = Factory::getDBO();
        $fields[] = $db->quoteName('es_themename') . '=' . $db->quote($theme_row->themename);
        $fields[] = $db->quoteName('es_width') . '=' . $db->quote($theme_row->width);
        $fields[] = $db->quoteName('es_height') . '=' . $db->quote($theme_row->height);
        $fields[] = $db->quoteName('es_playvideo') . '=' . $db->quote($theme_row->playvideo);
        $fields[] = $db->quoteName('es_repeat') . '=' . $db->quote($theme_row->repeat);
        $fields[] = $db->quoteName('es_fullscreen') . '=' . $db->quote($theme_row->fullscreen);
        $fields[] = $db->quoteName('es_autoplay') . '=' . $db->quote($theme_row->autoplay);
        $fields[] = $db->quoteName('es_related') . '=' . $db->quote($theme_row->related);
        $fields[] = $db->quoteName('es_bgcolor') . '=' . $db->quote($theme_row->bgcolor);
        $fields[] = $db->quoteName('es_cssstyle') . '=' . $db->quote($theme_row->cssstyle);
        $fields[] = $db->quoteName('es_navbarstyle') . '=' . $db->quote($theme_row->navbarstyle);
        $fields[] = $db->quoteName('es_thumbnailstyle') . '=' . $db->quote($theme_row->thumbnailstyle);
        $fields[] = $db->quoteName('es_listnamestyle') . '=' . $db->quote($theme_row->listnamestyle);
        $fields[] = $db->quoteName('es_descrstyle') . '=' . $db->quote($theme_row->descr_style);
        $fields[] = $db->quoteName('es_colorone') . '=' . $db->quote($theme_row->color1);
        $fields[] = $db->quoteName('es_colortwo') . '=' . $db->quote($theme_row->color2);
        $fields[] = $db->quoteName('es_border') . '=' . $db->quote($theme_row->border);
        $fields[] = $db->quoteName('es_openinnewwindow') . '=' . $db->quote($theme_row->openinnewwindow);
        $fields[] = $db->quoteName('es_rel') . '=' . $db->quote($theme_row->rel);
        $fields[] = $db->quoteName('es_hrefaddon') . '=' . $db->quote($theme_row->hrefaddon);
        $fields[] = $db->quoteName('es_customlimit') . '=' . $db->quote($theme_row->customlimit);
        $fields[] = $db->quoteName('es_controls') . '=' . $db->quote($theme_row->controls);
        $fields[] = $db->quoteName('es_youtubeparams') . '=' . $db->quote($theme_row->youtubeparams);
        $fields[] = $db->quoteName('es_useglass') . '=' . $db->quote($theme_row->useglass);
        $fields[] = $db->quoteName('es_logocover') . '=' . $db->quote($theme_row->logocover);
        $fields[] = $db->quoteName('es_customlayout') . '=' . $db->quote($theme_row->customlayout);
        $fields[] = $db->quoteName('es_allowplaylist') . '=' . $db->quote($theme_row->allowplaylist);


        $fields[] = $db->quoteName('es_prepareheadtags') . '=' . $db->quote($theme_row->prepareheadtags);
        $fields[] = $db->quoteName('es_muteonplay') . '=' . $db->quote($theme_row->muteonplay);
        $fields[] = $db->quoteName('es_volume') . '=' . $db->quote($theme_row->volume);
        $fields[] = $db->quoteName('es_orderby') . '=' . $db->quote($theme_row->orderby);
        $fields[] = $db->quoteName('es_customnavlayout') . '=' . $db->quote($theme_row->customnavlayout);
        $fields[] = $db->quoteName('es_responsive') . '=' . $db->quote($theme_row->responsive);
        $fields[] = $db->quoteName('es_mediafolder') . '=' . $db->quote($theme_row->mediafolder);
        $fields[] = $db->quoteName('es_headscript') . '=' . $db->quote($theme_row->headscript);
        $fields[] = $db->quoteName('es_themedescription') . '=' . $db->quote($theme_row->themedescription);

        if (isset($theme_row->nocookie))
            $fields[] = $db->quoteName('es_nocookie') . '=' . $db->quote($theme_row->nocookie);

        if (isset($theme_row->changepagetitle))
            $fields[] = $db->quoteName('es_changepagetitle') . '=' . $db->quote($theme_row->changepagetitle);

        $query = 'INSERT #__customtables_table_youtubegallerythemes SET ' . implode(', ', $fields);

        $db = Factory::getDBO();

        $db->setQuery($query);
        $db->execute();
    }

    function createTheme($themecode, &$msg)
    {


        $theme_row = unserialize($themecode);
        if ($theme_row === false) {

            $msg = 'Theme Code is corrupted.';
            return false;
        }


        if ($theme_row->es_themename == '') {

            $msg = 'Theme Code is incorrect.';
            return false;
        }

        //Add record to database
        $theme_row->es_themename = $this->getThemeName(str_replace('"', '', $theme_row->es_themename));
        echo 'Theme Name: ' . $theme_row->es_themename . '<br/>';


        $this->saveTheme($theme_row);
        echo 'Theme Imported<br/>';

        return true;
    }
}
