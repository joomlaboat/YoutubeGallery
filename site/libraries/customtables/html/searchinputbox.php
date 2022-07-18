<?php
/**
 * CustomTables Joomla! 3.x/4.x Native Component
 * @package Custom Tables
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link http://www.joomlaboat.com
 * @copyright (C) 2018-2022 Ivan Komlev
 * @license GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 **/

namespace CustomTables;

if (!defined('_JEXEC') and !defined('WPINC')) {
    die('Restricted access');
}

use CustomTables\DataTypes\Tree;
use \JoomlaBasicMisc;

use \Joomla\CMS\Factory;
use \JHTML;

JHTML::addIncludePath(JPATH_SITE . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_customtables' . DIRECTORY_SEPARATOR . 'helpers');

class SearchInputBox
{
    var CT $ct;
    var $modulename;
    var $field;

    function __construct(CT &$ct, $modulename)
    {
        $this->ct = $ct;
        $this->modulename = $modulename;
    }

    function renderFieldBox($prefix, $objname, &$fieldrow, $cssclass, $index, $where, $innerjoin, $wherelist, $default_Action, $field_title = null)
    {
        $this->field = new Field($this->ct, $fieldrow);

        $place_holder = $this->field->title;

        if ($field_title === null)
            $field_title = $place_holder;

        $result = '';

        $value = Factory::getApplication()->input->getCmd($prefix . $objname);

        if ($value == '') {
            if (isset($fieldrow['fields']) and count($fieldrow['fields']) > 0)
                $where_name = implode(';', $fieldrow['fields']);
            else
                $where_name = $this->field->fieldname;

            $value = $this->getWhereParameter($where_name);
        }

        $objname_ = $prefix . $objname;

        if ($this->ct->Env->version < 4)
            $default_class = 'inputbox';
        else
            $default_class = 'form-control';

        switch ($this->field->type) {
            case 'int':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '"'
                    . ' value="' . $value . '" placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'float':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" value="' . $value . '"'
                    . ' value="' . $value . '" placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)" '
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'string':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' value="' . $value . '" ' . ((int)$this->field->params[0] > 0 ? 'maxlength="' . (int)$this->field->params[0] . '"' : 'maxlength="255"')
                    . ' placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'phponchange':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' value="' . $value . '" ' . ((int)$this->field->params[0] > 0 ? 'maxlength="' . (int)$this->field->params[0] . '"' : 'maxlength="255"')
                    . ' placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'phponadd':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' value="' . $value . '" ' . ((int)$this->field->params[0] > 0 ? 'maxlength="' . (int)$this->field->params[0] . '"' : 'maxlength="255"')
                    . ' placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'multilangstring':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' value="' . $value . '" ' . ((int)$this->field->params[0] > 0 ? 'maxlength="' . (int)$this->field->params[0] . '"' : 'maxlength="255"')
                    . ' placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'text':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' value="' . $value . '" ' . ((int)$this->field->params[0] > 0 ? 'maxlength="' . (int)$this->field->params[0] . '"' : 'maxlength="255"')
                    . ' placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'multilangtext':
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' value="' . $value . '" ' . ((int)$this->field->params[0] > 0 ? 'maxlength="' . (int)$this->field->params[0] . '"' : 'maxlength="255"')
                    . ' placeholder="' . $field_title . '" onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'checkbox':
                $result .= $this->getCheckBox($fieldrow, $default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'range':
                $result .= $this->getRangeBox($fieldrow, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'customtables':
                $result .= $this->getCustomTablesBox($prefix, $innerjoin, $default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass, $place_holder);
                break;

            case 'userid':
                $result .= $this->getUserBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'user':
                $result .= $this->getUserBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'usergroup':
                $result .= $this->getUserGroupBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'usergroups':
                $result .= $this->getUserGroupBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'records':
                $result .= $this->getRecordsBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'sqljoin':
                $result .= $this->getTableJoinBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass);
                break;

            case 'email';
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' value="' . $value . '" maxlength="255"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'url';
                $result .= '<input type="text" name="' . $objname_ . '" id="' . $objname_ . '" class="' . $cssclass . ' ' . $default_class . '" '
                    . ' placeholder="' . $field_title . '"'
                    . ' onkeypress="es_SearchBoxKeyPress(event)"'
                    . ' value="' . $value . '" maxlength="1024"'
                    . ' data-type="' . $this->field->type . '" />';
                break;

            case 'date';
                $result .= JHTML::calendar($value, $objname_, $objname_);
                break;
        }
        return $result;
    }

    protected function getWhereParameter($field)
    {
        $f = str_replace($this->ct->Env->field_prefix, '', $field);//legacy support

        $list = $this->getWhereParameters();

        foreach ($list as $l) {
            $p = explode('=', $l);
            $fld_name = str_replace('_t_', '', $p[0]);
            $fld_name = str_replace('_r_', '', $fld_name); //range

            if ($fld_name == $f and isset($p[1]))
                return $p[1];

        }
        return '';
    }

    protected function getWhereParameters()
    {
        $value = Factory::getApplication()->input->getString('where');
        $value = str_replace('update', '', $value);
        $value = str_replace('select', '', $value);
        $value = str_replace('drop', '', $value);
        $value = str_replace('grant', '', $value);
        $value = str_replace('user', '', $value);

        $b = base64_decode($value);
        $b = str_replace(' or ', ' and ', $b);
        $b = str_replace(' OR ', ' and ', $b);
        $b = str_replace(' AND ', ' and ', $b);
        $list = explode(' and ', $b);
        return $list;
    }

    protected function getCheckBox(&$fieldrow, $default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass)
    {
        $result = '';

        if ($this->ct->Env->version < 4)
            $default_class = 'inputbox';
        else
            $default_class = 'form-select';

        $translations = array(JoomlaBasicMisc::JTextExtended('COM_CUSTOMTABLES_ANY'), JoomlaBasicMisc::JTextExtended('COM_CUSTOMTABLES_YES'), JoomlaBasicMisc::JTextExtended('COM_CUSTOMTABLES_NO'));

        if ($default_Action != '') {
            $onchange = $default_Action;
        } else {
            $onchange = ' onChange="' . $this->modulename . '_onChange('
                . $index . ','
                . 'this.value,'
                . '\'' . $this->field->fieldname . '\','
                . '\'' . urlencode($where) . '\','
                . '\'' . urlencode($wherelist) . '\','
                . '\'' . $this->ct->Languages->Postfix . '\''
                . ')"';
        }

        $result .= '<select'
            . ' id="' . $objname_ . '"'
            . ' name="' . $objname_ . '"'
            . ' ' . $onchange
            . ' class="' . $cssclass . ' ' . $default_class . '"'
            . ' data-type="checkbox">'
            . '<option value="" ' . ($value == '' ? 'SELECTED' : '') . '>' . $this->field->title . ' - ' . $translations[0] . '</option>'
            . '<option value="true" ' . ($value == 'true' ? 'SELECTED' : '') . '>' . $this->field->title . ' - ' . $translations[1] . '</option>'
            . '<option value="false" ' . ($value == 'false' ? 'SELECTED' : '') . '>' . $this->field->title . ' - ' . $translations[2] . '</option>'
            . '</select>';

        return $result;
    }

    protected function getRangeBox(&$fieldrow, $index, $where, $wherelist, $objname_, $value, $cssclass)
    {
        $jinput = Factory::getApplication()->input;
        $result = '';

        if ($this->ct->Env->version < 4)
            $default_class = 'inputbox';
        else
            $default_class = 'form-control';

        $value_min = '';
        $value_max = '';

        if ($this->field->params == 'date')
            $d = '-to-';

        if ($this->field->params == 'float')
            $d = '-';

        $values = explode($d, $value);
        $value_min = $values[0];

        if (isset($values[1]))
            $value_max = $values[1];

        if ($value_min == '')
            $value_min = $jinput->getString($objname_ . '_min');

        if ($value_max == '')
            $value_max = $jinput->getString($objname_ . '_max');

        //header function
        $document = Factory::getDocument();
        $js = '
	function Update' . $objname_ . 'Values()
	{
		var o=document.getElementById("' . $objname_ . '");
		var v_min=document.getElementById("' . $objname_ . '_min").value
		var v_max=document.getElementById("' . $objname_ . '_max").value;
		o.value=v_min+"' . $d . '"+v_max;

		//' . $this->modulename . '_onChange(' . $index . ',v_min+"' . $d . '"+v_max,"' . $this->field->fieldname . '","' . urlencode($where) . '","' . urlencode($wherelist) . '");
	}
';
        $document->addScriptDeclaration($js);
        //end of header function

        $attribs = 'onChange="Update' . $objname_ . 'Values()" class="' . $default_class . '" ';

        $result .= '<input type="hidden"'
            . ' id="' . $objname_ . '" '
            . ' name="' . $objname_ . '" '
            . ' value="' . $value_min . $d . $value_max . '" '
            . ' onkeypress="es_SearchBoxKeyPress(event)"'
            . ' data-type="range" />';

        $result .= '<table class="es_class_min_range_table" border="0" cellpadding="0" cellspacing="0" class="' . $cssclass . '" ><tbody><tr><td valign="middle">';

        //From
        if ($fieldrow['typeparams'] == 'date') {
            $result .= JHTML::calendar($value_min, $objname_ . '_min', $objname_ . '_min', '%Y-%m-%d', $attribs);
        } else {
            $result .= '<input type="text"'
                . ' id="' . $objname_ . '_min" '
                . ' name="' . $objname_ . '_min" '
                . 'value="' . $value_min . '" '
                . ' onkeypress="es_SearchBoxKeyPress(event)" '
                . ' ' . str_replace('class="', 'class="es_class_min_range ', $attribs)
                . ' data-type="range" />';
        }

        $result .= '</td><td width="20" align="center">-</td><td align="left" width="140" valign="middle">';

        //To
        if ($fieldrow['typeparams'] == 'date') {
            $result .= JHTML::calendar($value_max, $objname_ . '_max', $objname_ . '_max', '%Y-%m-%d', $attribs);
        } else {
            $result .= '<input type="text"'
                . ' id="' . $objname_ . '_max"'
                . ' name="' . $objname_ . '_max"'
                . ' value="' . $value_max . '"'
                . ' onkeypress="es_SearchBoxKeyPress(event)"'
                . ' ' . str_replace('class="', 'class="es_class_min_range ', $attribs)
                . ' data-type="range" />';
        }

        return '</td></tr></tbody></table>';
    }

    protected function getCustomTablesBox($prefix, $innerjoin, $default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass, $place_holder = '')
    {
        $result = '';
        $optionname = $this->field->params[0];
        $parentid = Tree::getOptionIdFull($optionname);

        if ($default_Action != '') {
            $onchange = $default_Action;
            $requirementdepth = 1;
        } else {
            $onchange = $this->modulename . '_onChange('
                . $index . ','
                . 'me.value,'
                . '\'' . $this->field->params->fieldname . '\','
                . '\'' . urlencode($where) . '\','
                . '\'' . urlencode($wherelist) . '\','
                . '\'' . $this->ct->Languages->Postfix . '\''
                . ')';

            $requirementdepth = 0;
        }

        $result .= JHTML::_('ESComboTree.render',
            $prefix,
            $this->ct->Table->tablename,
            $this->field->fieldname,
            $optionname,
            $this->ct->Languages->Postfix,
            $value,
            $cssclass,
            $onchange,
            $where,
            $innerjoin, false, $requirementdepth,
            $place_holder,
            '',
            '');

        return $result;
    }

    protected function getUserBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass)
    {
        $result = '';
        $mysqljoin = $this->ct->Table->realtablename . ' ON ' . $this->ct->Table->realtablename . '.' . $this->field->realfieldname . '=#__users.id';

        if ($default_Action != '') {
            $onchange = $default_Action;
        } else {
            $onchange = ' onChange=   "' . $this->modulename . '_onChange('
                . $index . ','
                . 'this.value,'
                . '\'' . $this->field->fieldname . '\','
                . '\'' . urlencode($where) . '\','
                . '\'' . urlencode($wherelist) . '\','
                . '\'' . $this->ct->Languages->Postfix . '\''
                . ')"';
        }

        if ($this->ct->Env->version < 4)
            $default_class = 'inputbox';
        else
            $default_class = 'form-control';

        if ($this->ct->Env->user->id != 0)
            $result = JHTML::_('ESUser.render', $objname_, $value, '', 'class="' . $cssclass . ' ' . $default_class . '" ', $this->field->params[0], $onchange, $where, $mysqljoin);


        return $result;
    }

    protected function getUserGroupBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass)
    {
        $result = '';
        $mysqljoin = $this->ct->Table->realtablename . ' ON ' . $this->ct->Table->realtablename . '.' . $this->field->realfieldname . '=#__usergroups.id';
        $usergroup = $this->field->params[0];

        if ($this->ct->Env->version < 4)
            $cssclass = 'class="inputbox ' . $cssclass . '" ';
        else
            $cssclass = 'class="form-control ' . $cssclass . '" ';

        $user = Factory::getUser();

        if ($default_Action != '') {
            $onchange = $default_Action;
        } else {
            $onchange = ' onChange=   "' . $this->modulename . '_onChange('
                . $index . ','
                . 'this.value,'
                . '\'' . $this->field->fieldname . '\','
                . '\'' . urlencode($where) . '\','
                . '\'' . urlencode($wherelist) . '\','
                . '\'' . $this->ct->Languages->Postfix . '\''
                . ')"';
        }

        if ($user->id != 0)
            $result = JHTML::_('ESUserGroup.render', $objname_, $value, '', $cssclass, $onchange, $where, $mysqljoin);

        return $result;
    }

    protected function getRecordsBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass)
    {
        $result = '';

        if (count($this->field->params) < 1)
            $result .= 'table not specified';

        if (count($this->field->params) < 2)
            $result .= 'field or layout not specified';

        if (count($this->field->params) < 3)
            $result .= 'selector not specified';

        $esr_table = $this->field->params[0];
        $esr_field = $this->field->params[1];
        $esr_selector = $this->field->params[2];

        if ($wherelist != '')
            $esr_filter = $wherelist;
        elseif (count($this->field->params) > 3)
            $esr_filter = $this->field->params[3];
        else
            $esr_filter = '';

        $dynamic_filter = '';

        $sortbyfield = '';
        if (isset($this->field->params[5]))
            $sortbyfield = $this->field->params[5];

        $v = array();
        $v[] = $index;
        $v[] = 'this.value';
        $v[] = '"' . $this->field->fieldname . '"';
        $v[] = '"' . urlencode($where) . '"';
        $v[] = '"' . urlencode($wherelist) . '"';
        $v[] = '"' . $this->ct->Languages->Postfix . '"';

        if ($default_Action != '' and $default_Action != ' ')
            $onchange = $default_Action;
        else
            $onchange = ' onkeypress="es_SearchBoxKeyPress(event)"';

        if (is_array($value))
            $value = implode(',', $value);

        $real_selector = $esr_selector;
        $real_selector = 'single';

        $result .= JHTML::_('ESRecords.render', $this->field->params, $objname_,
            $value, $esr_table, $esr_field, $real_selector, $esr_filter, '',
            $cssclass, $onchange, $dynamic_filter, $sortbyfield,
            $this->ct->Languages->Postfix, $this->field->title);

        return $result;
    }

    protected function getTableJoinBox($default_Action, $index, $where, $wherelist, $objname_, $value, $cssclass)
    {
        $result = '';

        if ($default_Action != '' and $default_Action != ' ')
            $onchange = $default_Action;
        else
            $onchange = ' onkeypress="es_SearchBoxKeyPress(event)"';

        if (is_array($value))
            $value = implode(',', $value);

        if ($this->ct->Env->version < 4)
            $default_class = 'inputbox';
        else
            $default_class = 'form-control';

        $result .= '<div class="' . $cssclass . '">' . JHTML::_('ESSQLJoin.render', $this->field->params, $value, true, $this->ct->Languages->Postfix, $objname_,
                $this->field->title,
                ' ' . $default_class . ' es_class_sqljoin', $onchange, true) . '</div>';

        return $result;
    }
}
