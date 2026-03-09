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
defined('_JEXEC') or die();
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\String\StringHelper;

/**
 * View class for a list of orders.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewOrders extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->comquick2cartHelper = new comquick2cartHelper;
		$app    = Factory::getApplication();
		$input  = $app->input;
		$option = $input->get('option');
		$this->params = ComponentHelper::getParams('com_quick2cart');

		// Load language for frontend
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$this->model = $this->getModel('Orders');

		$pstatus       = array();
		$pstatus[]     = HTMLHelper::_('select.option', 'P', Text::_('QTC_PENDIN'));
		$pstatus[]     = HTMLHelper::_('select.option', 'C', Text::_('QTC_CONFR'));
		$pstatus[]     = HTMLHelper::_('select.option', 'RF', Text::_('QTC_REFUN'));
		$pstatus[]     = HTMLHelper::_('select.option', 'S', Text::_('QTC_SHIP'));
		$pstatus[]     = HTMLHelper::_('select.option', 'E', Text::_('QTC_ERR'));
		$this->pstatus = $pstatus;

		$sstatus       = array();
		$sstatus[]     = HTMLHelper::_('select.option', '-1', Text::_('COM_QUICK2CART_SELECT_APPROVAL_STATUS'));
		$sstatus[]     = HTMLHelper::_('select.option', 'P', Text::_('QTC_PENDIN'));
		$sstatus[]     = HTMLHelper::_('select.option', 'C', Text::_('QTC_CONFR'));
		$sstatus[]     = HTMLHelper::_('select.option', 'RF', Text::_('QTC_REFUN'));
		$sstatus[]     = HTMLHelper::_('select.option', 'S', Text::_('QTC_SHIP'));
		$sstatus[]     = HTMLHelper::_('select.option', 'E', Text::_('QTC_ERR'));
		$this->sstatus = $sstatus;

		$layout = $input->get('layout', '');

		// To change invoice design
		if ($layout == 'order' || $layout == 'invoice')
		{
			$orderid  = $input->get('orderid', '', 'INT');
			$store_id = $input->get('store_id', '', 'INT');
			$this->orderid = $orderid;

			// Changed j Requests
			$input->set('orderid', $orderid);

			if ($layout == 'invoice')
			{
				$this->store_id          = $store_id;
				$this->storeReleatedView = 1;
				$this->orders = $orderInfo = $this->comquick2cartHelper->getorderinfo($orderid, $store_id);
			}
			else
			{
				$this->orders = $orderInfo = $this->model->getorderinfo($orderid);
			}

			$this->orderinfo    = $orderInfo['order_info'];
			$this->orderitems   = $orderInfo['items'];
			$this->order_xref   = $this->model->getOrderXrefData($orderid);
			$this->orderHistory = $this->model->getOrderHistory($orderid);
		}
		else
		{
			$search_gateway   = $app->getUserStateFromRequest($option . 'search_gateway', 'search_gateway', '', 'string');
			$search_gateway   = StringHelper::strtolower($search_gateway);
			$filter_order_Dir = $app->getUserStateFromRequest($option . "filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
			$filter_type      = $app->getUserStateFromRequest($option . "filter_order", 'filter_order', 'id', 'string');

			$sstatus_gateway   = array();
			$sstatus_gateway[] = HTMLHelper::_('select.option', '0', Text::_('QTC_FILTER_GATEWAY'));
			$gatewaylist       = $this->model->gatewaylist();

			if ($gatewaylist)
			{
				foreach ($gatewaylist as $key => $gateway)
				{
					$gateway_nm        = $gateway->processor;
					$this->paidPlgName = $this->comquick2cartHelper->getPluginName($gateway_nm);
					$sstatus_gateway[] = HTMLHelper::_('select.option', $gateway_nm, $this->paidPlgName);
				}
			}

			$this->sstatus_gateway = $sstatus_gateway;

			// End Added by Sagar For Gateway Filter
			$filter_search = $app->getUserStateFromRequest($option . 'filter.search', 'filter_search', '', 'string');
			$filter_todate = $app->getUserStateFromRequest($option . 'filter.todate', 'filter_todate', '', 'string');
			$filter_fromdate = $app->getUserStateFromRequest($option . 'filter.fromdate', 'filter_fromdate', '', 'string');

			$filter_state = $app->getUserStateFromRequest($option . 'search_list', 'search_list', '', 'string');
			$search       = $app->getUserStateFromRequest($option . 'search_select', 'search_select', '', 'string');
			$filter_state = StringHelper::strtolower($filter_state);

			if ($filter_state == null)
			{
				$filter_state = '';
			}

			// Get data from the model
			$total      = $this->get('Total');
			$pagination = $this->get('Pagination');
			$orders     = $this->get('Orders');

			// Search filter
			$lists['filter_search']   = $filter_search;
			$lists['filter_todate']   = $filter_todate;
			$lists['filter_fromdate'] = $filter_fromdate;
			$lists['search_select']   = $search;
			$lists['search_list']     = $filter_state;
			$lists['search_gateway']  = $search_gateway;
			$lists['order_Dir']       = $filter_order_Dir;
			$lists['order']           = $filter_type;

			// Get data from the model
			$this->lists      = $lists;
			$this->pagination = $pagination;
			$this->orders     = $orders;
		}

		// FOR J3.0
		$this->addToolbar();

		// FOR DISPLAY SIDE FILTER
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
	public function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/quick2cart.php';
		$canDo = Quick2cartHelper::getActions();

		$input  = Factory::getApplication()->input;
		$layout = $input->get('layout', '');

		// If default layout
		if ($layout != 'order' && $layout != 'invoice')
		{
			if (isset($this->orders[0]))
			{
				if ($canDo->get('core.delete'))
				{
					ToolBarHelper::deleteList('', 'orders.deleteorders');
				}

				// CSV EXPORT
				ToolBarHelper::custom('orders.payment_csvexport', 'download', 'download', 'COM_QUICK2CART_SALES_CSV_EXPORT', false);
			}

			ToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart');
			ToolBarHelper::title(Text::_('QTC_ORDERS'), 'list');
		}
		elseif ($layout != 'invoice')
		{
			$params             = ComponentHelper::getParams('com_quick2cart');
			$multivendor_enable = $params->get('multivendor');
			ToolBarHelper::back('COM_QUICK2CART_BACK', 'index.php?option=com_quick2cart&view=orders');
			ToolBarHelper::title(Text::_('COM_QUICK2CART_ORDER_TITLE'), 'list');
		}

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_quick2cart');
		}

		if (JVERSION >= '3.0')
		{
			// JHtmlSidebar class to render a list view sidebar
			// setAction::Set value for the action attribute of the filter form
			JHtmlSidebar::setAction('index.php?option=com_quick2cart');
		}
	}
}
