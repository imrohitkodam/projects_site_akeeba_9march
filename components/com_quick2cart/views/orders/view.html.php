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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for list view of products.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewOrders extends HtmlView
{
	protected $gateways;
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$app                       = Factory::getApplication();
		$jinput                    = $app->input;
		$option                    = $jinput->get('option');
		$this->layout              = $layout = $jinput->get('layout', 'default');
		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->storeHelper         = new storeHelper;
		$this->params              = ComponentHelper::getParams('com_quick2cart');

		$orders_site       = '1';
		$this->orders_site = $orders_site;
		$this->model       = $this->getModel('Orders');

		$pstatus       = array();
		$pstatus[]     = HTMLHelper::_('select.option', 'P', Text::_('QTC_PENDIN'));
		$pstatus[]     = HTMLHelper::_('select.option', 'C', Text::_('QTC_CONFR'));
		$pstatus[]     = HTMLHelper::_('select.option', 'RF', Text::_('QTC_REFUN'));
		$pstatus[]     = HTMLHelper::_('select.option', 'E', Text::_('QTC_ERR'));
		$this->pstatus = $pstatus;

		// For filter
		$sstatus       = array();
		$sstatus[]     = HTMLHelper::_('select.option', '-1', Text::_('QTC_SELONE'));
		$sstatus[]     = HTMLHelper::_('select.option', 'P', Text::_('QTC_PENDIN'));
		$sstatus[]     = HTMLHelper::_('select.option', 'C', Text::_('QTC_CONFR'));
		$sstatus[]     = HTMLHelper::_('select.option', 'S', Text::_('QTC_SHIP'));
		$sstatus[]     = HTMLHelper::_('select.option', 'RF', Text::_('QTC_REFUN'));
		$sstatus[]     = HTMLHelper::_('select.option', 'E', Text::_('QTC_ERR'));
		$this->sstatus = $sstatus;

		$vendorstatus   = array();

		// Commented after discussing with DJ
		$vendorstatus[] = HTMLHelper::_('select.option', '-1', Text::_('QTC_SEL_STATUS'));
		$vendorstatus[] = HTMLHelper::_('select.option', 'P',  Text::_('QTC_PENDIN'));
		$vendorstatus[] = HTMLHelper::_('select.option', 'C', Text::_('QTC_CONFR'));
		$vendorstatus[] = HTMLHelper::_('select.option', 'S', Text::_('QTC_SHIP'));
		$vendorstatus[] = HTMLHelper::_('select.option', 'E', Text::_('QTC_ERR'));
		$vendorstatus[] = HTMLHelper::_('select.option', 'RF', Text::_('QTC_REFUN'));
		$store_id       = $jinput->get('store_id', 0, 'INTEGER');

		// NOTE :: (this is used to view order detail in vendor store product detail )
		if (!empty($store_id))
		{
			$this->store_id          = $store_id;
			$this->storeReleatedView = 1;
		}

		// Check for multivender COMPONENT PARAM
		if ($layout == "mycustomer" || $layout == "storeorder")
		{
			$isMultivenderOFFmsg = $this->comquick2cartHelper->isMultivenderOFF();

			if (!empty($isMultivenderOFFmsg))
			{
				print $isMultivenderOFFmsg;

				return false;
			}
		}

		$Itemid       = $this->comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders');
		$this->Itemid = $Itemid;

		if ($layout == "order" || $layout == "customerdetails")
		{
			$orderid       = $jinput->get('orderid', 0, 'integer');
			$this->orderid = $orderid;
			$jinput->set('orderid', $orderid);

			if ($layout == 'customerdetails')
			{
				$orderid               = $jinput->get('orderid', '', 'GET');
				$this->store_authorize = $store_authorize = $this->comquick2cartHelper->store_authorize("orders_customerdetails", $store_id);

				// Authorization may be depends on roll of user eg, store owner, manager,admin,front desk employee
				$order       = $this->comquick2cartHelper->getorderinfo($orderid, $store_id);
				$customer_id = '';

				if (!empty($order["order_info"][0]->user_id))
				{
					$customer_id = $order["order_info"][0]->user_id;
				}
				else
				{
					if ($order["order_info"][0]->address_type == 'BT')
					{
						$customer_id = $order["order_info"][0]->user_email;
					}
					elseif ($order["order_info"][1]->address_type == 'BT')
					{
						$customer_id = $order["order_info"][1]->user_email;
					}
				}

				$this->vendorstatus = $vendorstatus;
				$this->pagination   = $this->get('Pagination');

				// Get orders for customer for selected store
				$this->orders = $this->model->getOrders($store_id, $customer_id);
			}
			else
			{
				$order       = $this->comquick2cartHelper->getorderinfo($orderid, $store_id);
				$guest_email = $jinput->get('email', '', "RAW");
				$authDetail                = new stdClass;
				$authDetail->store_id      = $store_id;
				$authDetail->order_user_id = $this->storeHelper->getOrderUser($orderid);
				$authDetail->guest_email   = $guest_email;
				$authDetail->order_id      = $orderid;

				@$allowToViewOrderDetailView = $this->storeHelper->allowToViewStoreOrderDetailView($authDetail);
				$this->storeReleatedView     = (empty($store_id)) ? 0 : 1;

				if ($allowToViewOrderDetailView != 1)
				{
					echo $allowToViewOrderDetailView;

					return false;
				}
				else
				{
					$this->order_authorized = 1;
				}

				// Get order history
				$this->vendorstatus = $vendorstatus;
				$this->orderHistory = $this->model->getOrderHistory($orderid, $store_id);
			}

			$this->orderinfo  = $order["order_info"];
			$this->orderitems = $order["items"];

			// Get plugin name.
			if (!empty($this->orderinfo[0]->processor))
			{
				$this->paidPlgName = $this->comquick2cartHelper->getPluginName($this->orderinfo[0]->processor);
			}

			// PAYMENT
			PluginHelper::importPlugin('payment');

			if (!is_array($this->params->get('gateways')))
			{
				$gateway_param[] = $this->params->get('gateways');
			}
			else
			{
				$gateway_param = $this->params->get('gateways');
			}

			if (!empty($gateway_param))
			{
				$orderDetails   = $order['items'];
				$storeId        = $orderDetails[0]->store_id;
				$gateways       = $this->comquick2cartHelper->getValidGateways($gateway_param, $storeId);
				$this->gateways = $gateways;
			}
		}
		elseif ($layout == "mycustomer")
		{
			$this->store_authorize   = $store_authorize = $this->comquick2cartHelper->store_authorize("orders_mycustomer");

			// Retrun store_id,role etc with order by role,store_id
			$this->store_role_list = $store_role_list = $this->comquick2cartHelper->getStoreIds();

			// Store_id is changed from manage storeorder view
			$change_storeto = (int) $app->getUserStateFromRequest('com_quick2cart' . '.current_store', 'current_store', '', 'INT');
			$store_id              = (!empty($change_storeto)) ? $change_storeto : $store_role_list[0]['store_id'];
			$this->store_id        = $this->selected_store = $store_id;

			if (!empty($store_authorize))
			{
				$user_info       = $this->model->getCustomers($store_id);
				$this->user_info = $user_info;
			}

			$this->pagination = $this->model->getPagination($this->model->getCustomerTotal($store_id));
		}
		elseif ($layout == "invoice" || $layout == "invoice_" . QUICK2CART_LOAD_BOOTSTRAP_VERSION)
		{
			// Store_releated view
			$this->storeReleatedView = 1;
			$this->store_id          = $store_id;
			$orderid                 = $jinput->get('orderid', 0, 'INTEGER');
			$this->orders            = $this->comquick2cartHelper->getorderinfo($orderid, $this->store_id);
		}
		else
		{
			if ($layout == "storeorder")
			{
				// Store_releated view
				$this->vendorstatus      = $vendorstatus;
				$this->storeReleatedView = 1;
				$this->store_authorize   = $store_authorize = $this->comquick2cartHelper->store_authorize("orders_storeorder");
				$this->store_role_list   = $store_role_list = $this->comquick2cartHelper->getStoreIds();

				// Store_id is changed from manage storeorder view
				$change_storeto   = (int) $app->getUserStateFromRequest('com_quick2cart' . '.current_store', 'current_store', '', 'INT');
				$store_id         = (!empty($change_storeto)) ? $change_storeto : $store_role_list[0]['store_id'];
				$this->store_id   = $this->selected_store = $store_id;
				$orders           = (!empty($store_id)) ? $this->model->getOrders($store_id) : array();
				$this->pagination = $this->model->getPagination(0, $store_id);
			}
			else
			{
				// My orders view is not releated to store (any user can access it)
				$this->storeReleatedView = 0;
				$orders                  = $this->model->getOrders();
				$this->pagination        = $this->get('Pagination');
			}

			$this->orders = $orders;
		}

		$filter_order_Dir = $app->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$filter_type      = $app->getUserStateFromRequest($option . 'filter_order', 'filter_order', 'id', 'string');
		$filter_search    = $app->getUserStateFromRequest($option . 'filter.search', 'filter_search', '', 'string');
		$filter_state     = $app->getUserStateFromRequest($option . 'search_list', 'search_list', '', 'string');
		$search           = $app->getUserStateFromRequest($option . 'search_select', 'search_select', '', 'string');

		if ($search == null)
		{
			$search = '-1';
		}

		// Search filter
		$this->lists['filter_search'] = $filter_search;
		$this->lists['search_select'] = $search;
		$this->lists['search_list']   = $filter_state;
		$this->lists['order_Dir']     = $filter_order_Dir;
		$this->lists['order']         = $filter_type;

		$this->addToolbar();
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
		$document = Factory::getDocument();
		$jinput   = Factory::getApplication()->input;
		$layout   = $jinput->get('layout', '', 'string');

		$smallButtonClass = JVERSION < '4.0' ? 'btn-small' : 'btn-sm';

		// Add toolbar buttons
		jimport('techjoomla.tjtoolbar.toolbar');
		$tjbar = TJToolbar::getInstance('tjtoolbar', 'pull-right float-end');

		switch ($layout)
		{
			case 'mycustomer':
				$document->setTitle(Text::_('QTC_MYCUSTOMER_PAGE'));
				break;
			case 'storeorder':
				$document->setTitle(Text::_('QTC_STOREORDERS_PAGE'));
				break;
			case 'customerdetails':
				$document->setTitle(Text::_('QTC_CUS_DETAILS_PAGE'));
				break;
			case 'order':
				$document->setTitle(Text::_('QTC_ORDERS_PAGE'));
				break;
			case 'default':
				$document->setTitle(Text::_('QTC_ORDERS_PAGE'));
				break;
		}

		if ($layout == 'storeorder')
		{
			$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

			if (!class_exists('comquick2cartHelper'))
			{
				JLoader::register('comquick2cartHelper', $path);
				JLoader::load('comquick2cartHelper');
			}

			$comquick2cartHelper = new comquick2cartHelper;
			$canDo = $comquick2cartHelper->getActions();

			if ($canDo->get('core.delete'))
			{
				if (isset($this->orders[0]))
				{
					$tjbar->appendButton('orders.deleteorders', 'TJTOOLBAR_DELETE', '', 'class="btn btn-sm btn-danger"');
				}
			}

			$this->toolbarHTML = $tjbar->render();
		}
	}
}
