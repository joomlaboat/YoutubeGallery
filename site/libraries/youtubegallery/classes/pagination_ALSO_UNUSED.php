<?php
/**
 * YouTubeGallery Joomla! 3.0 Native Component
 * @author Ivan Komlev <support@joomlaboat.com>
 * @link https://joomlaboat.com
 * @GNU General Public License
 **/

// no direct access
/*
defined('_JEXEC') or die('Restricted access');

use CustomTables\CTMiscHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use YouTubeGallery\Helper;

class YGPagination extends JObject
{

	public $limitstart = null;

	public $limit = null;

	public $total = null;


	public $prefix = null;
	public $AddAnchor = false;

	protected $_viewall = false;

	protected $_additionalUrlParams = array();


	function __construct($total, $limitstart, $limit, $prefix = '', $AddAnchor = false)
	{
		// Value/type checking.
		$this->total = (int)$total;
		$this->limitstart = (int)max($limitstart, 0);
		$this->limit = (int)max($limit, 0);
		$this->prefix = $prefix;
		$this->AddAnchor = $AddAnchor;


		if ($this->limit > $this->total)
			$this->limitstart = 0;


		if (!$this->limit) {
			$this->limit = $total;
			$this->limitstart = 0;
		}


		if ($this->limitstart > $this->total - $this->limit) {
			$this->limitstart = max(0, (int)(ceil($this->total / $this->limit) - 1) * $this->limit);
		}

		// Set the total pages and current page values.
		if ($this->limit > 0) {
			$this->set('pages.total', ceil($this->total / $this->limit));
			$this->set('pages.current', ceil(($this->limitstart + 1) / $this->limit));
		}

		// Set the pagination iteration loop values.
		$displayedPages = 10;
		$this->set('pages.ygstart', $this->get('pages.current') - ($displayedPages / 2));
		if ($this->get('pages.ygstart') < 1) {
			$this->set('pages.ygstart', 1);
		}
		if (($this->get('pages.ygstart') + $displayedPages) > $this->get('pages.total')) {
			$this->set('pages.stop', $this->get('pages.total'));
			if ($this->get('pages.total') < $displayedPages) {
				$this->set('pages.ygstart', 1);
			} else {
				$this->set('pages.ygstart', $this->get('pages.total') - $displayedPages + 1);
			}
		} else {
			$this->set('pages.stop', ($this->get('pages.ygstart') + $displayedPages - 1));
		}

		// If we are viewing all records set the view all flag to true.
		if ($limit == 0) {
			$this->_viewall = true;
		}
	}


	public function setAdditionalUrlParam($key, $value)
	{
		// Get the old value to return and set the new one for the URL parameter.
		$result = isset($this->_additionalUrlParams[$key]) ? $this->_additionalUrlParams[$key] : null;

		// If the passed parameter value is null unset the parameter, otherwise set it to the given value.
		if ($value === null) {
			unset($this->_additionalUrlParams[$key]);
		} else {
			$this->_additionalUrlParams[$key] = $value;
		}

		return $result;
	}


	public function getAdditionalUrlParam($key)
	{
		$result = isset($this->_additionalUrlParams[$key]) ? $this->_additionalUrlParams[$key] : null;

		return $result;
	}

	public function getRowOffset($index)
	{
		return $index + 1 + $this->limitstart;
	}


	public function getData()
	{
		static $data;
		if (!is_object($data)) {
			$data = $this->_buildDataObject();
		}
		return $data;
	}



	protected function _buildDataObject()
	{
		$yg_anchor = '#youtubegallery';

		$data = new stdClass;

		$WebsiteRoot = Uri::root();

		$u = Uri::getInstance();
		$URLPath = $u->getPath();

		if ($WebsiteRoot[strlen($WebsiteRoot) - 1] != '/') //Root must have slash / in the end
			$WebsiteRoot .= '/';

		if ($URLPath[0] != '/') //Root must have slash / in the end
			$URLPath = '/' . $URLPath;


		$a = explode('/', $WebsiteRoot);

		$b = explode('/', $URLPath);

		$s = -1;
		$ca = count($a);


		for ($i = 0; $i < count($b) and $i < $ca - 3; $i++) {
			if ($a[$ca - 1 - $i] == $b[$i])
				$s = $i;
			else
				break;
		}

		if ($s != -1) {
			$c = array();
			for ($i = $s + 1; $i < count($b); $i++)
				$c[] = $b[$i];

			$URLPath = implode('/', $c);
		}


		if ($WebsiteRoot[strlen($WebsiteRoot) - 1] != '/') //Root must have slash / in the end
			$WebsiteRoot .= '/';


		//the path must start without "/"
		if ($URLPath != '') {
			if ($URLPath[0] == '/')
				$URLPath = substr($URLPath, 1);
		}

		//check if the path already contains video alias
		//delete it if found.
		if (str_contains(JPATH_COMPONENT, '/com_youtubegallery')) {
			//youtube gallery component
			$s = explode('/', $URLPath);
			$sc = count($s);
			if ($sc >= 1) {
				$pair = explode('?', $s[$sc - 1]);


				$alias = str_replace('.html', '', $pair[0]);
				$alias = str_replace('.htm', '', $alias);
				$alias = str_replace('.php', '', $alias);

				if (YGPagination::CheckIfAliasExixst($alias)) {
					if (isset($pair[1])) {
						$s[$sc - 1] = $pair[1];
					} else {
						unset($s[$sc - 1]);
						$URLPath = implode('/', $s);
					}
				}
			}

		}
		//---------------------

		$Translations = array();

		$Translations['all'] = Text::_('JLIB_HTML_VIEW_ALL');
		$Translations['start'] = Text::_('JLIB_HTML_START');
		$Translations['prev'] = Text::_('JPREV');
		$Translations['next'] = Text::_('JNEXT');
		$Translations['end'] = Text::_('JLIB_HTML_END');

		$URLPath .= YGPagination::QuestionmarkOrAnd($URLPath) . $u->getQuery();


		if (!empty($this->_additionalUrlParams)) {
			foreach ($this->_additionalUrlParams as $key => $value) {
				$URLPath .= YGPagination::QuestionmarkOrAnd($URLPath) . $key . '=' . $value;
			}
		}

		$URLPath = CTMiscHelper::deleteURLQueryOption($URLPath, 'videoid');
		$URLPath = CTMiscHelper::deleteURLQueryOption($URLPath, $this->prefix . 'ygstart');

		$computed_prefix = YGPagination::QuestionmarkOrAnd($URLPath) . $this->prefix;

		$URLPath = str_replace('&', '&amp;', $URLPath);

		$FullPath = $WebsiteRoot . $URLPath;


		$data->all = new JYGPaginationObject($Translations['all'], $this->prefix);
		if (!$this->_viewall) {
			$data->all->base = '0';
			$data->all->link = $FullPath;
		}

		// Set the start and previous data objects.

		$data->start = new JYGPaginationObject($Translations['start'], $this->prefix);
		$data->previous = new JYGPaginationObject($Translations['prev'], $this->prefix);

		if ($this->get('pages.current') > 1) {
			$page = ($this->get('pages.current') - 2) * $this->limit;

			// Set the empty for removal from route

			$data->start->base = '0';
			$data->start->link = $FullPath;

			if ($this->AddAnchor)
				$data->start->link .= $yg_anchor;

			$data->previous->base = $page;
			$data->previous->link = $FullPath . $computed_prefix . 'ygstart=' . $page;

			if ($this->AddAnchor)
				$data->previous->link .= $yg_anchor;
		}

		// Set the next and end data objects.
		$data->next = new JYGPaginationObject($Translations['next'], $this->prefix);
		$data->end = new JYGPaginationObject($Translations['end'], $this->prefix);

		if ($this->get('pages.current') < $this->get('pages.total')) {
			$next = $this->get('pages.current') * $this->limit;
			$end = ($this->get('pages.total') - 1) * $this->limit;

			$data->next->base = $next;
			$data->next->link = $FullPath . $computed_prefix . 'ygstart=' . $next;
			if ($this->AddAnchor)
				$data->next->link .= $yg_anchor;

			$data->end->base = $end;
			$data->end->link = $FullPath . $computed_prefix . 'ygstart=' . $end;
			if ($this->AddAnchor)
				$data->end->link .= $yg_anchor;

		}

		$data->pages = array();
		$stop = $this->get('pages.stop');
		for ($i = $this->get('pages.ygstart'); $i <= $stop; $i++) {
			$offset = ($i - 1) * $this->limit;
			// Set the empty for removal from route
			$data->pages[$i] = new JYGPaginationObject($i, $this->prefix);
			if ($i != $this->get('pages.current') || $this->_viewall) {
				$data->pages[$i]->base = $offset;
				if ($offset == 0)
					$data->pages[$i]->link = $FullPath;
				else
					$data->pages[$i]->link = $FullPath . $computed_prefix . 'ygstart=' . $offset;

			}

			if ($this->AddAnchor)
				$data->pages[$i]->link .= $yg_anchor;
		}


		return $data;
	}

	static protected function CheckIfAliasExixst($alias)
	{
		if ($alias == '')
			return true;

		$db = Factory::getDBO();

		$db->setQuery('SELECT id FROM #__customtables_table_youtubegalleryvideos WHERE es_alias="' . $alias . '" LIMIT 1');

		$db->execute();
		if ($db->getNumRows() == 1)
			return true;

		return false;
	}

	static protected function QuestionmarkOrAnd($URLPath)
	{
		if ($URLPath != '') {
			if (!str_contains($URLPath, '?'))
				return '?';

			$a = $URLPath[strlen($URLPath) - 1];
			if ($a == '?' or $a == '&')
				return '';
			else
				return '&';
		} else
			return '?';

	}


	public function getResultsCounter()
	{
		// Initialise variables.
		$html = null;
		$fromResult = $this->limitstart + 1;

		// If the limit is reached before the end of the list.
		if ($this->limitstart + $this->limit < $this->total) {
			$toResult = $this->limitstart + $this->limit;
		} else {
			$toResult = $this->total;
		}

		// If there are results found.
		if ($this->total > 0) {
			$msg = Text::sprintf('JLIB_HTML_RESULTS_OF', $fromResult, $toResult, $this->total);
			$html .= "\n" . $msg;
		} else {
			$html .= "\n" . Text::_('JLIB_HTML_NO_RECORDS_FOUND');
		}

		return $html;
	}


	public function getListFooter()
	{
		$app = Factory::getApplication();

		$list = array();
		$list['prefix'] = $this->prefix;
		$list['limit'] = $this->limit;
		$list['limitstart'] = $this->limitstart;
		$list['total'] = $this->total;
		$list['limitfield'] = $this->getLimitBox();
		$list['pagescounter'] = $this->getPagesCounter();
		$list['pageslinks'] = $this->getPagesLinks();


		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/pagination.php';
		if (file_exists($chromePath)) {
			require_once $chromePath;
			if (function_exists('pagination_list_footer')) {
				return pagination_list_footer($list);
			}
		}
		return $this->_list_footer($list);
	}


	public function getPagesCounter()
	{
		// Initialise variables.
		$html = null;
		if ($this->get('pages.total') > 1) {
			$html .= Text::sprintf('JLIB_HTML_PAGE_CURRENT_OF_TOTAL', $this->get('pages.current'), $this->get('pages.total'));
		}
		return $html;
	}


	public function getPagesLinks()
	{

		$app = Factory::getApplication();

		// Build the page navigation list.
		$data = $this->_buildDataObject();

		$list = array();
		$list['prefix'] = $this->prefix;

		$itemOverride = false;
		$listOverride = false;

		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/pagination.php';
		if (file_exists($chromePath)) {
			require_once $chromePath;
			if (function_exists('pagination_item_active') && function_exists('pagination_item_inactive')) {
				$itemOverride = true;
			}
			if (function_exists('pagination_list_render')) {
				$listOverride = true;
			}
		}

		// Build the select list
		if ($data->all->base !== null) {
			$list['all']['active'] = true;
			$list['all']['data'] = ($itemOverride) ? pagination_item_active($data->all) : $this->_item_active($data->all);
		} else {
			$list['all']['active'] = false;
			$list['all']['data'] = ($itemOverride) ? pagination_item_inactive($data->all) : $this->_item_inactive($data->all);
		}

		if ($data->start->base !== null) {
			$list['start']['active'] = true;
			$list['start']['data'] = ($itemOverride) ? pagination_item_active($data->start) : $this->_item_active($data->start);
		} else {
			$list['start']['active'] = false;
			$list['start']['data'] = ($itemOverride) ? pagination_item_inactive($data->start) : $this->_item_inactive($data->start);
		}
		if ($data->previous->base !== null) {
			$list['previous']['active'] = true;
			$list['previous']['data'] = ($itemOverride) ? pagination_item_active($data->previous) : $this->_item_active($data->previous);
		} else {
			$list['previous']['active'] = false;
			$list['previous']['data'] = ($itemOverride) ? pagination_item_inactive($data->previous) : $this->_item_inactive($data->previous);
		}

		$list['pages'] = array(); //make sure it exists
		foreach ($data->pages as $i => $page) {

			if ($page->base !== null) {
				$list['pages'][$i]['active'] = true;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_active($page) : $this->_item_active($page);
			} else {
				$list['pages'][$i]['active'] = false;
				$list['pages'][$i]['data'] = ($itemOverride) ? pagination_item_inactive($page) : $this->_item_inactive($page);
			}
		}

		if ($data->next->base !== null) {
			$list['next']['active'] = true;
			$list['next']['data'] = ($itemOverride) ? pagination_item_active($data->next) : $this->_item_active($data->next);
		} else {
			$list['next']['active'] = false;
			$list['next']['data'] = ($itemOverride) ? pagination_item_inactive($data->next) : $this->_item_inactive($data->next);
		}

		if ($data->end->base !== null) {
			$list['end']['active'] = true;
			$list['end']['data'] = ($itemOverride) ? pagination_item_active($data->end) : $this->_item_active($data->end);
		} else {
			$list['end']['active'] = false;
			$list['end']['data'] = ($itemOverride) ? pagination_item_inactive($data->end) : $this->_item_inactive($data->end);
		}

		if ($this->total > $this->limit) {
			return ($listOverride) ? pagination_list_render($list) : $this->_list_render($list);
		} else {
			return '';
		}
	}



	protected function _item_active(&$item)
	{
		return "<a title=\"" . $item->text . "\" href=\"" . $item->link . "\" class=\"pagenav\">" . $item->text . "</a>";
	}



	protected function _item_inactive(&$item)
	{
		return "<span class=\"pagenav\">" . $item->text . "</span>";
	}



	protected function _list_render($list)
	{
		// Reverse output rendering for right-to-left display.
		$html = '<ul>';
		$html .= '<li class="pagination-start">' . $list['start']['data'] . '</li>';
		$html .= '<li class="pagination-prev">' . $list['previous']['data'] . '</li>';
		foreach ($list['pages'] as $page) {
			$html .= '<li>' . $page['data'] . '</li>';
		}
		$html .= '<li class="pagination-next">' . $list['next']['data'] . '</li>';
		$html .= '<li class="pagination-end">' . $list['end']['data'] . '</li>';
		$html .= '</ul>';

		return $html;
	}


	protected function _list_footer($list)
	{
		$html = "<div class=\"list-footer\">\n";

		$html .= "\n<div class=\"limit\">" . Text::_('JGLOBAL_DISPLAY_NUM') . $list['limitfield'] . "</div>";
		$html .= $list['pageslinks'];
		$html .= "\n<div class=\"counter\">" . $list['pagescounter'] . "</div>";

		$html .= "\n<input type=\"hidden\" name=\"" . $list['prefix'] . "limitstart\" value=\"" . $list['limitstart'] . "\" />";
		$html .= "\n</div>";

		return $html;
	}

	public function getCBLimitBox($columns)
	{
		$the_step = $columns * 5;

		$app = Factory::getApplication();

		// Initialise variables.
		$limits = array();

		// Make the option list.
		for ($i = $the_step; $i <= $the_step * 6; $i += $the_step) {
			$limits[] = JHTML::_('select.option', "$i");
		}

		$limits[] = JHTML::_('select.option', $the_step * 10);
		$limits[] = JHTML::_('select.option', $the_step * 20);
		$limits[] = JHTML::_('select.option', '0', Text::_('JALL'));

		$selected = $this->_viewall ? 0 : $this->limit;

		// Build the select list.
		if ($app->isClient('administrator')) {
			$html = JHtml::_('select.genericlist', $limits, $this->prefix . 'limit', 'class="inputbox" size="1" onchange="Joomla.submitform();"', 'value', 'text', $selected);
		} else {
			$html = JHtml::_('select.genericlist', $limits, $this->prefix . 'limit', 'class="inputbox" size="1" onchange="this.form.submit()"', 'value', 'text', $selected);
		}
		return $html;
	}

	public function orderUpIcon($i, $condition = true, $task = 'orderup', $alt = 'JLIB_HTML_MOVE_UP', $enabled = true, $checkbox = 'cb')
	{
		if (($i > 0 || ($i + $this->limitstart > 0)) && $condition) {
			return JHtml::_('jgrid.orderUp', $i, $task, '', $alt, $enabled, $checkbox);
		} else {
			return '&#160;';
		}
	}


	public function orderDownIcon($i, $n, $condition = true, $task = 'orderdown', $alt = 'JLIB_HTML_MOVE_DOWN', $enabled = true, $checkbox = 'cb')
	{
		if (($i < $n - 1 || $i + $this->limitstart < $this->total - 1) && $condition) {
			return JHtml::_('jgrid.orderDown', $i, $task, '', $alt, $enabled, $checkbox);
		} else {
			return '&#160;';
		}
	}
}


class JYGPaginationObject extends JObject
{

	public $text;


	public $base;


	public $link;


	public $prefix;

	public function __construct($text, $prefix = '', $base = null, $link = null)
	{
		$this->text = $text;
		$this->prefix = $prefix;
		$this->base = $base;
		$this->link = $link;
	}
}
*/