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

class InputBox_usergroup extends BaseInputBox
{
    function __construct(CT &$ct, Field $field, ?array $row, array $option_list = [], array $attributes = [])
    {
        parent::__construct($ct, $field, $row, $option_list, $attributes);
    }

    /**
     * @throws Exception
     * @since 3.2.0
     */
    function render(?string $value, ?string $defaultValue, bool $showUserWithRecords = false): string
    {
        if ($this->ct->Env->user->id === null)
            return '';

        if ($value === null) {
            $value = common::inputGetInt($this->ct->Env->field_prefix . $this->field->fieldname);
            if (!$value)
                $value = $defaultValue;
        }

        self::selectBoxAddCSSClass($this->attributes, $this->ct->Env->version);

        $availableUserGroups = $this->field->params[0] ?? '';
        $availableUserGroupList = (trim($availableUserGroups) == '' ? [] : explode(',', strtolower(trim($availableUserGroups))));

        if ($showUserWithRecords)
            $innerJoin = ' INNER JOIN ' . $this->ct->Table->realtablename . ' ON ' . $this->ct->Table->realtablename . '.' . $this->field->realfieldname . '=#__usergroups.id';
        else
            $innerJoin = null;

        try {
            $records = $this->ct->Env->user->getUserGroupArray($availableUserGroupList, $innerJoin);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        // Start building the select element with attributes
        $select = '<select ' . self::attributes2String($this->attributes) . '>';

        if (defined('_JEXEC')) {
            // Optional default option
            $selected = (0 === (int)$value) ? ' selected' : '';
            $select .= '<option value=""' . $selected . '> - ' . common::translate('COM_CUSTOMTABLES_SELECT') . '</option>';

            // Generate options for each file in the folder
            foreach ($records as $record) {
                $selected = ($record['id'] === (int)$value) ? ' selected' : '';
                $select .= '<option value="' . $record['id'] . '"' . $selected . '>' . $record['name'] . '</option>';
            }
        } elseif (defined('WPINC')) {
            $value = trim($value);

            // Optional default option
            $selected = ('' === $value) ? ' selected' : '';
            $select .= '<option value=""' . $selected . '> - ' . common::translate('COM_CUSTOMTABLES_SELECT') . '</option>';

            // Generate options for each file in the folder
            foreach ($records as $record) {
                $selected = ($record['id'] === $value) ? ' selected' : '';
                $select .= '<option value="' . $record['id'] . '"' . $selected . '>' . $record['name'] . '</option>';
            }
        }
        $select .= '</select>';

        return $select;
    }
}