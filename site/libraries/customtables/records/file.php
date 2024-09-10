<?php
/**
 * CustomTables Joomla! 3.x/4.x/5.x Component and WordPress 6.x Plugin
 * @package Custom Tables
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @copyright (C) 2018-2024. Ivan Komlev
 * @license GNU/GPL Version 2 or later - https://www.gnu.org/licenses/gpl-2.0.html
 **/

namespace CustomTables;

// no direct access
defined('_JEXEC') or die();

use Exception;

class Save_file
{
    var CT $ct;
    public Field $field;
    var ?array $row_new;

    function __construct(CT &$ct, Field $field, ?array $row_new)
    {
        $this->ct = &$ct;
        $this->field = $field;
        $this->row_new = $row_new;
    }

    /**
     * @throws Exception
     * @since 3.3.3
     */
    function saveFieldSet(?string $listing_id): ?array
    {
        $newValue = null;

        $to_delete = common::inputPostCmd($this->field->comesfieldname . '_delete', null, 'create-edit-record');

        //Get new file
        $CompletePathToFile = null;
        $fileName = null;

        if (defined('_JEXEC')) {
            $temporaryFile = common::inputPostString($this->field->comesfieldname, null, 'create-edit-record');
            if (empty($temporaryFile))
                return null;

            $CompletePathToFile = CUSTOMTABLES_ABSPATH . 'tmp' . DIRECTORY_SEPARATOR . $temporaryFile;

            $fileName = common::inputPostString('com' . $this->field->realfieldname . '_filename', '', 'create-edit-record');
        } elseif (defined('WPINC')) {
            //Get new image
            if (isset($_FILES[$this->field->comesfieldname])) {
                $CompletePathToFile = $_FILES[$this->field->comesfieldname]['tmp_name'];
                $fileName = $_FILES[$this->field->comesfieldname]['name'];
            }
        } else
            return null;

        //Set the variable to "false" to do not delete existing file
        $FileFolder = FileUtils::getOrCreateDirectoryPath($this->field->params[1]);

        if (($CompletePathToFile !== null and $CompletePathToFile != '') or $to_delete == 'true') {

            $ExistingFile = $this->field->ct->Table->getRecordFieldValue($listing_id, $this->field->realfieldname);

            $filepath = str_replace('/', DIRECTORY_SEPARATOR, $FileFolder);
            if (substr($filepath, 0, 1) == DIRECTORY_SEPARATOR)
                $filepath = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, CUSTOMTABLES_ABSPATH . $filepath);
            else
                $filepath = CUSTOMTABLES_ABSPATH . $filepath;

            if ($ExistingFile != '' and !self::checkIfTheFileBelongsToAnotherRecord($ExistingFile)) {
                $filename_full = $filepath . DIRECTORY_SEPARATOR . $ExistingFile;

                if (file_exists($filename_full))
                    unlink($filename_full);
            }
        }

        if ($CompletePathToFile !== null and $CompletePathToFile != '') {
            //Upload new file

            if ($listing_id == 0) {
                $fileSystemFileFolder = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, CUSTOMTABLES_ABSPATH . $FileFolder);
                $value = $this->UploadSingleFile(null, $CompletePathToFile, $fileName, $fileSystemFileFolder);
            } else {
                $ExistingFile = $this->field->ct->Table->getRecordFieldValue($listing_id, $this->field->realfieldname);

                $fileSystemFileFolder = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, CUSTOMTABLES_ABSPATH . $FileFolder);
                $value = $this->UploadSingleFile($ExistingFile, $CompletePathToFile, $fileName, $fileSystemFileFolder);
            }

            //Set new image value
            if ($value)
                $newValue = ['value' => $value];

        } elseif ($to_delete == 'true') {
            $newValue = ['value' => null];//This way it will be clear if the value changed or not. If $this->newValue = null means that value not changed.
        }

        return $newValue;
    }

    /**
     * @throws Exception
     * @since 3.3.4
     */
    private function checkIfTheFileBelongsToAnotherRecord(string $filename): bool
    {
        $whereClause = new MySQLWhereClause();
        $whereClause->addCondition($this->field->realfieldname, $filename);
        $col = database::loadColumn($this->field->ct->Table->realtablename, ['COUNT_ROWS'], $whereClause, null, null, 2);
        if (count($col) == 0)
            return false;

        return $col[0] > 1;
    }

    /**
     * @throws Exception
     * @since 3.3.4
     */
    private function UploadSingleFile(?string $ExistingFile, string $CompletePathToFile, string $fileName, string $FileFolder): ?string
    {
        require_once(CUSTOMTABLES_LIBRARIES_PATH . DIRECTORY_SEPARATOR . 'customtables' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'uploader.php');

        if ($this->field->type == 'file')
            $fileExtensions = $this->field->params[2] ?? '';
        elseif ($this->field->type == 'blob')
            $fileExtensions = $this->field->params[1] ?? '';
        else
            return null;

        if ($CompletePathToFile != '') {
            if (empty($this->field->params[3])) {

                //Joomla version the File Uploader adds random value to the filename to make sure it's a unique file name in tmp folder.
                $parts = explode('_', $fileName);
                if (count($parts) > 3 and $parts[0] == 'ct') {
                    //Example:
                    //ct_1717446480_j586scaH994mTWz58cbFMX6RWUu25aJn0tbBI_doc1.pdf
                    //Here we remove the temporary random values
                    unset($parts[2]);
                    unset($parts[1]);
                    unset($parts[0]);
                    $desiredFileName = implode('_', $parts);
                } else
                    $desiredFileName = $fileName;
            } else
                $desiredFileName = $this->field->params[3];

            $accepted_file_types = explode(' ', FileUploader::getAcceptedFileTypes($fileExtensions));
            $accepted_filetypes = array();

            foreach ($accepted_file_types as $filetype) {
                $mime = FileUploader::get_mime_type('1.' . $filetype);
                $accepted_filetypes[] = $mime;

                if ($filetype == 'docx')
                    $accepted_filetypes[] = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
                elseif ($filetype == 'xlsx')
                    $accepted_filetypes[] = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
                elseif ($filetype == 'pptx')
                    $accepted_filetypes[] = 'application/vnd.openxmlformats-officedocument.presentationml.presentation';
            }

            $is_base64encoded = common::inputGetCmd('base64encoded', '');
            if ($is_base64encoded == "true") {

                if (defined('_JEXEC')) {
                    $file = common::inputPostString($this->field->comesfieldname, '');
                    $dst = JPATH_SITE . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . 'decoded_' . basename($file['name']);
                    common::base64file_decode($CompletePathToFile, $dst);
                    $CompletePathToFile = $dst;
                } else {
                    echo 'Base64 encoded file upload is not available in this CMS';
                }
            }

            if (!empty($ExistingFile) and !$this->checkIfTheFileBelongsToAnotherRecord($ExistingFile)) {
                //Delete Old File
                $filename_full = $FileFolder . DIRECTORY_SEPARATOR . $ExistingFile;

                if (file_exists($filename_full))
                    unlink($filename_full);
            }

            if (!file_exists($CompletePathToFile))
                return null;

            $mime = mime_content_type($CompletePathToFile);
            $parts = explode('.', $fileName);
            $fileExtension = end($parts);
            if ($mime == 'application/zip' and $fileExtension != 'zip') {
                //could be docx, xlsx, pptx
                $mime = FileUploader::checkZIP_File_X($CompletePathToFile, $fileExtension);
            }

            if (in_array($mime, $accepted_filetypes)) {
                $new_filename = self::getCleanAndAvailableFileName($desiredFileName, $FileFolder);
                $new_filename_path = str_replace('/', DIRECTORY_SEPARATOR, $FileFolder . DIRECTORY_SEPARATOR . $new_filename);

                if (@copy($CompletePathToFile, $new_filename_path)) {
                    unlink($CompletePathToFile);
                    //Copied

                    return $new_filename;
                } else {
                    unlink($CompletePathToFile);
                    //Cannot copy
                    return null;
                }
            } else {
                unlink($CompletePathToFile);
                return null;
            }
        }
        return null;
    }

    protected static function getCleanAndAvailableFileName(string $desiredFileName, string $FileFolder): string
    {
        //Clean Up file name
        $rawFileName = str_replace(' ', '_', $desiredFileName);

        // Process field name
        if (function_exists("transliterator_transliterate"))
            $rawFileName = transliterator_transliterate("Any-Latin; Latin-ASCII;", $rawFileName);

        $rawFileName = preg_replace("/[^\p{L}\d._]/u", "", $rawFileName);

        $fileNameParts = explode('.', $rawFileName);
        $fileExtension = end($fileNameParts);
        unset($fileNameParts[count($fileNameParts) - 1]);
        $fileName = implode($fileNameParts);

        $i = 0;
        $new_fileName = $fileName . '.' . $fileExtension;
        while (1) {

            if (file_exists($FileFolder . DIRECTORY_SEPARATOR . $new_fileName)) {
                //increase index
                $i++;
                $new_fileName = $fileName . '_' . $i . '.' . $fileExtension;
            } else
                break;
        }
        return $new_fileName;
    }
}
