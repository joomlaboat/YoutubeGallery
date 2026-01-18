<?php
/**
 * CustomTables Joomla! 3.x Native Component
 * @author Ivan komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @license GNU/GPL
 **/

namespace Joomla\CMS\Form\Field;

defined('_JEXEC') or die();
class YGStatusField extends PredefinedlistField
{
    public $type = 'YGStatus';

    protected $predefinedOptions = array(
        -2 => 'JTRASHED',
        0 => 'JUNPUBLISHED',
        1 => 'JPUBLISHED',
        "*" => 'JALL',
    );
}
