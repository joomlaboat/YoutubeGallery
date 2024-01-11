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
if (!defined('_JEXEC') and !defined('WPINC')) {
	die('Restricted access');
}

class Search_string extends BaseSearch
{
	function __construct(CT &$ct, Field $field, string $moduleName, array $attributes, int $index, string $where, string $whereList, string $objectName)
	{
		parent::__construct($ct, $field, $moduleName, $attributes, $index, $where, $whereList, $objectName);
		BaseInputBox::inputBoxAddCSSClass($this->attributes, $this->ct->Env->version);
	}

	function render($value): string
	{
		$this->getOnChangeAttributeString();
		$this->attributes['type'] = 'text';
		$this->attributes['id'] = $this->objectName;
		$this->attributes['name'] = $this->objectName;
		$this->attributes['value'] = htmlspecialchars($value ?? '');
		$this->attributes['placeholder'] = $this->attributes['data-label'];
		$this->attributes['onkeypress'] = 'es_SearchBoxKeyPress(event)';
		return '<input ' . BaseInputBox::attributes2String($this->attributes) . ' />';
	}
}