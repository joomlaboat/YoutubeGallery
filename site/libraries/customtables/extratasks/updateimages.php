<?php
/**
 * CustomTables Joomla! 3.x/4.x Native Component
 * @package Custom Tables
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @copyright (C) 2018-2022 Ivan Komlev
 * @license GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 **/

// no direct access
if (!defined('_JEXEC') and !defined('WPINC')) {
    die('Restricted access');
}

use CustomTables\CT;
use CustomTables\Field;
use CustomTables\Fields;
use Joomla\CMS\Factory;

class updateImages
{
    public static function process(): array
    {
        $ct = new CT;

        $stepsize = $ct->Env->jinput->getInt('stepsize', 10);
        $startindex = $ct->Env->jinput->getInt('startindex', 0);

        $old_typeparams = base64_decode($ct->Env->jinput->get('old_typeparams', '', 'BASE64'));
        if ($old_typeparams == '')
            return array('error' => 'old_typeparams not set');

        $old_params = JoomlaBasicMisc::csv_explode(',', $old_typeparams);

        $new_typeparams = base64_decode($ct->Env->jinput->get('new_typeparams', '', 'BASE64'));
        if ($new_typeparams == '')
            return array('error' => 'new_typeparams not set');

        $new_params = JoomlaBasicMisc::csv_explode(',', $new_typeparams);

        $fieldid = $ct->Env->jinput->getInt('fieldid', 0);
        if ($fieldid == 0)
            return array('error' => 'fieldid not set');

        $fieldRow = Fields::getFieldRow($fieldid);

        $ct->getTable($fieldRow->tableid);

        $count = 0;
        if ($startindex == 0) {
            $count = updateImages::countImages($ct->Table->realtablename, $fieldRow->realfieldname, $ct->Table->realidfieldname);
            if ($stepsize > $count)
                $stepsize = $count;
        }

        $status = updateImages::processImages($ct, $fieldRow, $old_params, $new_params, $startindex, $stepsize);

        return array('count' => $count, 'success' => (int)($status === null), 'startindex' => $startindex, 'stepsize' => $stepsize, 'error' => $status);
    }

    public static function countImages(string $realtablename, string $realfieldname, string $realidfieldname): int
    {
        $db = Factory::getDBO();
        $query = 'SELECT count(' . $realidfieldname . ') AS c FROM ' . $realtablename . ' WHERE ' . $realfieldname . '>0';
        $db->setQuery($query);
        $recs = $db->loadAssocList();
        return (int)$recs[0]['c'];
    }

    public static function processImages(CT &$ct, $fieldRow, array $old_params, array $new_params, int $startindex, int $stepsize): ?string
    {
        $db = Factory::getDBO();
        $query = 'SELECT ' . $fieldRow->realfieldname . ' FROM ' . $ct->Table->realtablename . ' WHERE ' . $fieldRow->realfieldname . '>0';
        $db->setQuery($query, $startindex, $stepsize);

        $imagelist = $db->loadAssocList();
        $old_ImageFolder = '';
        $imgMethods = new CustomTablesImageMethods;

        foreach ($imagelist as $img) {
            $field_row_old = (array)$fieldRow;
            $field_row_old['params'] = $old_params;

            $field_old = new Field($ct, $field_row_old, $img);
            $field_old->params = $old_params;

            $old_ImageFolder_ = CustomTablesImageMethods::getImageFolder($field_old->params);
            $old_ImageFolder = str_replace('/', DIRECTORY_SEPARATOR, $old_ImageFolder_);

            $old_imagesizes = $imgMethods->getCustomImageOptions($field_old->params[0]);

            $field_row_new = (array)$fieldRow;

            $field_new = new Field($ct, $field_row_new, $img);
            $field_new->params = $new_params;

            $new_ImageFolder_ = CustomTablesImageMethods::getImageFolder($field_new->params);
            $new_ImageFolder = str_replace('/', DIRECTORY_SEPARATOR, $new_ImageFolder_);

            $new_imagesizes = $imgMethods->getCustomImageOptions($field_new->params[0]);

            $status = updateImages::processImage($imgMethods, $old_imagesizes, $new_imagesizes, $img[$fieldRow->realfieldname], $old_ImageFolder, $new_ImageFolder);
            //if $status is null then all good, status is a text string with error message if any
            if ($status !== null)
                return $status;
        }

        //Check if the old folder is already empty, if it is empty to delete the folder
        $files = scandir(JPATH_SITE . DIRECTORY_SEPARATOR . $old_ImageFolder);
        $count = 0;
        foreach ($files as $file) {
            if ($file != '.' and $file != '..')
                $count += 1;
        }

        if ($count == 0)
            rmdir(JPATH_SITE . DIRECTORY_SEPARATOR . $old_ImageFolder);

        return null;
    }

    protected static function processImage(&$imgMethods, &$old_imagesizes, &$new_imagesizes, $rowValue, $old_ImageFolder, $new_ImageFolder)
    {
        $original_image_file = '';

        $status = updateImages::processImage_Original($imgMethods, $rowValue, $old_ImageFolder, $new_ImageFolder, $original_image_file);

        if ($status !== null)
            return null;//Skip if original file not found

        $status = updateImages::processImage_Thumbnail($imgMethods, $rowValue, $old_ImageFolder, $new_ImageFolder);
        if ($status !== null) {
            //Create Thumbnail file
            $r = $imgMethods->ProportionalResize(JPATH_SITE . DIRECTORY_SEPARATOR . $original_image_file, JPATH_SITE . DIRECTORY_SEPARATOR . $new_ImageFolder . DIRECTORY_SEPARATOR . '_esthumb_' . $rowValue . '.jpg', 150, 150, 1, true, -1, '');

            if ($r != 1)
                $isOk = false;
        }

        //Move custom size files to new folder, or create if custom size file in original folder is missing
        $status = updateImages::processImage_CustomSizes($imgMethods, $old_imagesizes, $new_imagesizes, $rowValue, $old_ImageFolder, $new_ImageFolder, $original_image_file);

        //Delete custom size file if no longer in use or size or propery changed
        $image_sizes_to_delete = updateImages::findChangedOrDeletedCustomSizes($old_imagesizes, $new_imagesizes);

        //Delete old files
        foreach ($image_sizes_to_delete as $img) {
            $status = updateImages::processImage_CustomSize_deleteFile($imgMethods, $rowValue, $new_ImageFolder, $prefix = $img[0], $imagefile_ext = $img[4], $original_image_file);
            if ($status !== null)
                return $status;
        }

        //Create custom size file that doesnt exist
        foreach ($new_imagesizes as $img) {
            $status = updateImages::processImage_CustomSize_createFile($imgMethods, $img, $rowValue, $new_ImageFolder, $prefix = $img[0], $imagefile_ext = $img[4], $original_image_file);
            if ($status !== null)
                return $status;
        }

        return null;

    }

    protected static function processImage_Original(&$imgMethods, $rowValue, $old_ImageFolder, $new_ImageFolder, &$original_image_file)
    {
        //Check original image file
        $prefix = '_original';
        $old_imagefile = $old_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue;

        $imagefile_ext = $imgMethods->getImageExtention(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile);//file extension is unknow - let's find out

        if ($imagefile_ext != '') {
            $old_imagefile = $old_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;
            $new_imagefile = $new_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;
            if (file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile)) {
                if ($old_ImageFolder != $new_ImageFolder) {
                    if (!@rename(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile, JPATH_SITE . DIRECTORY_SEPARATOR . $new_imagefile))
                        return 'cannot move file to ' . $new_imagefile;
                    else
                        $original_image_file = $new_imagefile;
                } else
                    $original_image_file = $old_imagefile;
            } else
                return 'file not found';
        } else
            return 'file not found';

        return null;
    }

    protected static function processImage_Thumbnail(&$imgMethods, $rowValue, $old_ImageFolder, $new_ImageFolder)
    {
        //Check thumbnail
        $prefix = '_esthumb';
        $imagefile_ext = 'jpg';
        $old_imagefile = $old_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;
        $new_imagefile = $new_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;

        if (file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile)) {
            if ($old_ImageFolder != $new_ImageFolder) {
                if (!@rename(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile, JPATH_SITE . DIRECTORY_SEPARATOR . $new_imagefile))
                    return 'cannot move file to ' . $new_imagefile;
            }
        } else
            return 'file not found';

        return null;
    }

    protected static function processImage_CustomSizes(&$imgMethods, $old_imagesizes, $new_imagesizes, $rowValue, $old_ImageFolder, $new_ImageFolder, $original_image_file): ?string
    {
        //Move files if necessary
        foreach ($old_imagesizes as $img) {
            $status = updateImages::processImage_CustomSize_MoveFile($imgMethods, $img, $rowValue, $old_ImageFolder, $new_ImageFolder, $img[0], $img[4], $original_image_file);
            if ($status !== null)
                return $status;
        }

        return null;
    }

    protected static function processImage_CustomSize_MoveFile($imgMethods, $old_imagesize, $rowValue, $old_ImageFolder, $new_ImageFolder, $prefix, string $imagefile_ext, string $original_image_file)
    {
        if ($imagefile_ext == '')
            $imagefile_ext = $imgMethods->getImageExtention(JPATH_SITE . DIRECTORY_SEPARATOR . $original_image_file);//file extension is unknown - let's find out based on original file

        if ($imagefile_ext != '') {
            $old_imagefile = $old_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;
            $new_imagefile = $new_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;

            if (!file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile)) {
                //Custom size file not found, create it
                $width = (int)$old_imagesize[1];
                $height = (int)$old_imagesize[2];

                $color = (int)$old_imagesize[3];
                $watermark = $old_imagesize[5];

                $r = $imgMethods->ProportionalResize(JPATH_SITE . DIRECTORY_SEPARATOR . $original_image_file, JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile, $width, $height, 1, true, $color, $watermark);

                if ($r != 1)
                    return 'cannot create file: ' . $old_imagefile;
            }

            if (file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile)) {
                if ($old_ImageFolder != $new_ImageFolder) {
                    //Move exiting custom size file to new folder
                    if (!@rename(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile, JPATH_SITE . DIRECTORY_SEPARATOR . $new_imagefile))
                        return 'cannot move file to ' . $new_imagefile;
                }
            } else {
                return 'cannot create and move file: ' . $old_imagefile;
            }
        }

        return null;
    }

    protected static function findChangedOrDeletedCustomSizes($old_imagesizes, $new_imagesizes)
    {
        $image_sizes_to_delete = array();

        foreach ($old_imagesizes as $old_img) {
            $changed = false;

            foreach ($new_imagesizes as $new_img) {
                if ($old_img[0] == $new_img[0])//check if the size name is match
                {
                    //Compare parameters
                    for ($i = 1; $i < 6; $i++) {
                        if ($old_img[$i] != $new_img[$i]) {
                            $changed = true;
                            $image_sizes_to_delete[] = $old_img;
                            break;
                        }
                    }

                    if ($changed)
                        break;
                }
            }
        }

        return $image_sizes_to_delete;
    }

    protected static function processImage_CustomSize_deleteFile($imgMethods, $rowValue, $old_ImageFolder, $prefix, string $imagefile_ext, string $original_image_file): ?string
    {
        if ($imagefile_ext == '')
            $imagefile_ext = $imgMethods->getImageExtention(JPATH_SITE . DIRECTORY_SEPARATOR . $original_image_file);//file extension is unknow - let's find out based on original file

        if ($imagefile_ext != '') {
            $old_imagefile = $old_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;

            if (file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile)) {
                if (!@unlink(JPATH_SITE . DIRECTORY_SEPARATOR . $old_imagefile))
                    return 'cannot delete old file: ' . $old_imagefile;
            }
        }

        return null;
    }

    protected static function processImage_CustomSize_createFile($imgMethods, $new_imagesize, $rowValue, $new_ImageFolder, $prefix, string $imagefile_ext, string $original_image_file): ?string
    {
        if ($imagefile_ext == '')
            $imagefile_ext = $imgMethods->getImageExtention(JPATH_SITE . DIRECTORY_SEPARATOR . $original_image_file);//file extension is unknow - let's find out based on original file

        if ($imagefile_ext != '') {
            $new_imagefile = $new_ImageFolder . DIRECTORY_SEPARATOR . $prefix . '_' . $rowValue . '.' . $imagefile_ext;

            if (!file_exists(JPATH_SITE . DIRECTORY_SEPARATOR . $new_imagefile)) {
                //Custom size file not found, create it
                $width = (int)$new_imagesize[1];
                $height = (int)$new_imagesize[2];

                $color = (int)$new_imagesize[3];
                $watermark = $new_imagesize[5];

                $r = $imgMethods->ProportionalResize(JPATH_SITE . DIRECTORY_SEPARATOR . $original_image_file, JPATH_SITE . DIRECTORY_SEPARATOR . $new_imagefile, $width, $height, 1, true, $color, $watermark);

                if ($r != 1)
                    return 'cannot create file: ' . $new_imagefile;
            }
        }

        return null;
    }
}
