<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\String\StringHelper;

/**
 * View class for a list of coupons.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewManagecoupon extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->_setToolBar();
		$app              = Factory::getApplication();
		$input            = $app->input;
		$option           = $input->get('option');
		$filter_order_Dir = $app->getUserStateFromRequest("$option.filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		$filter_type      = $app->getUserStateFromRequest("$option.filter_type", 'filter_type', 0, 'string');
		$filter_state     = $app->getUserStateFromRequest($option . 'search_list', 'search_list', '', 'string');
		$search           = $app->getUserStateFromRequest($option . 'search', 'search', '', 'string');
		$search           = StringHelper::strtolower($search);
		$limit            = '';
		$limitstart       = '';
		$cid[0]           = '';

		if ($search == null)
		{
			$search = '';
		}

		$edit   = $input->get('edit', '');
		$layout = $input->get('layout', '');
		$cid    = $input->get('cid', '', 'ARRAY');
		$model  = $this->getModel('Managecoupon');

		if ($cid)
		{
			$total      = $this->get('Total');
			$pagination = $this->get('Pagination');
			$coupons    = $model->Editlist($cid[0]);
			$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', 'limit', 'int');
			$limitstart = $app->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');

			$model->setState('limit', $limit);
			$model->setState('limitstart', $limitstart);
		}
		else
		{
			$total      = $this->get('Total');
			$pagination = $this->get('Pagination');
			$coupons    = $this->get('Managecoupon');

			$limit      = $app->getUserStateFromRequest('global.list.limit', 'limit', 'limit', 'int');
			$limitstart = $app->getUserStateFromRequest($option . 'limitstart', 'limitstart', 0, 'int');
			$model->setState('limit', $limit);
			$model->setState('limitstart', $limitstart);
		}

		// Search filter
		$lists['search_select'] = $search;
		$lists['search']        = $search;
		$lists['search_list']   = $filter_state;
		$lists['order']         = $filter_type;
		$lists['order_Dir']     = $filter_order_Dir;
		$lists['limit']         = $limit;
		$lists['limitstart']    = $limitstart;

		// Get data from the model
		$this->lists      = $lists;
		$this->pagination = $pagination;
		$this->coupons    = $coupons;

		// FOR DISPLAY SIDE FILTER
		if (JVERSION >= 3.0)
		{
			JHtmlSidebar::setAction('index.php?option=com_quick2cart');
		}

		if (JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function _setToolBar()
	{
		$bar = ToolBar::getInstance('toolbar');
		ToolBarHelper::title(Text::_('AD_COUPAN_TITLE'), 'icon-48-quick2cart.png');
	}
}
