<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Load language file for plugin
$lang = Factory::getLanguage();
$lang->load('plg_system_qtcamazon_easycheckout', JPATH_ADMINISTRATOR);

require_once JPATH_SITE . '/plugins/system/qtcamazon_easycheckout/qtcamazon_easycheckout/qtceasycheckouthelper.php';

/**
 * Quick2cart
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class PlgSystemQtcamazon_Easycheckout extends CMSPlugin
{
		public $qtcmainHelper = null;

	/**
	 * Constructor
	 *
	 * @param   string  &$subject  subject
	 *
	 * @param   string  $config    config
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$workingMode = $this->params->get('workingMode', 0);

		if (!defined('Q2C_AMAZOON_CKOUT_PLUG_WORKING_MODE'))
		{
			define('Q2C_AMAZOON_CKOUT_PLUG_WORKING_MODE', $workingMode);
		}

		$this->loadQ2cHelper();
	}

	/**
	 * This method called when amazon send Instand Order Processing Notifications (IOPN). ATP: amazon transcation processing
	 *
	 * @return  boolean
	 *
	 * @since   2.6
	 */
	public function onATP_processIOPN()
	{
		$plugPath = JPATH_SITE . '/plugins/system/qtcamazon_easycheckout';
		$logFile = JPATH_SITE . '/plugins/system/qtcamazon_easycheckout/lib/iopn_processing/log/cbaiopn.log';
		$includeStatus = set_include_path(get_include_path() . PATH_SEPARATOR . $logFile);
		require_once dirname(__FILE__) . '/lib/iopn_processing/CBAIOPN.php';
	}

	/**
	 * This method called when on checkout view before rendoring the checkout view.
	 *
	 * @return  boolean
	 *
	 * @since   2.6
	 */
	public function onBeforeQ2cCheckoutViewDisplay()
	{
		$Qtceasycheckouthelper = new Qtceasycheckouthelper;
		$status = $Qtceasycheckouthelper->isComponentEnabled("quick2cart");

		if (empty($status))
		{
			return 0;
		}

		// 1: Place order
		$placeOrderStatus = $this->_placeOrder();

		if (!empty($placeOrderStatus->status)  && $placeOrderStatus->status === "success")
		{
			$order_id = $placeOrderStatus->order_id;
		}
		else
		{
			$msg = Text::_('PLUG_AMAZON_SOMTHING_WRONG_WHILE_PLACING_THE_ORDER');

			if ($placeOrderStatus->message)
			{
				$msg .= " ( " . $placeOrderStatus->message . " ) ";
			}

			return "<div class='alert alert-danger'>" . $msg . "</div>";
		}

		$path = JPATH_SITE . '/components/com_quick2cart/models/cart.php';

		if (!class_exists('Quick2cartControllercartcheckout'))
		{
			JLoader::register('Quick2cartControllercartcheckout', $path);
			JLoader::load('Quick2cartControllercartcheckout');
		}

		$Quick2cartModelcart = new Quick2cartModelcart;

		// Remove items from cart
		$Quick2cartModelcart->empty_cart();

		// @TODO make cart as empty

		// 2: Get checkout button according to order items
		$amazonCheckoutButtonHTML = $Qtceasycheckouthelper->getAmazonCheckoutButton($order_id);

		$data = new stdClass;
		$data->amazonCheckoutButtonHTML = $amazonCheckoutButtonHTML;

		$layoutHTML = $this->buildLayout($data);

		return $layoutHTML;
	}

	/**
	 * Method to loads Quick2cart's helper files
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	private function loadQ2cHelper()
	{
		$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

		if (!class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$this->qtcmainHelper = new comquick2cartHelper;

		// LOAD STORE HELPER
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';

		if (!class_exists('storeHelper'))
		{
			JLoader::register('storeHelper', $path);
			JLoader::load('storeHelper');
		}

		// LOAD product HELPER
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

		if (!class_exists('productHelper'))
		{
			JLoader::register('productHelper', $path);
			JLoader::load('productHelper');
		}
	}

	/**
	 * Internal use functions. Check for override and give the layout path
	 *
	 * @param   string  $layout  layout
	 *
	 * @since   2.2
	 *
	 * @return   string  layout
	 */
	private function buildLayoutPath($layout = "default")
	{
		$layout = "default";
		$app       = Factory::getApplication();
		$core_file = dirname(__FILE__) . '/' . $this->_name . '/' . 'tmpl' . '/' . $layout . '.php';
		$override  = JPATH_BASE . '/templates/' . $app->getTemplate() . '/html/plugins/'
			. $this->_type . '/' . $this->_name . '/' . $layout . '.php';

		if (File::exists($override))
		{
			return $override;
		}
		else
		{
			return $core_file;
		}
	}

	/**
	 * Builds the layout to be shown
	 *
	 * @since   2.2
	 *
	 * @return   object  vars
	 */
	private function _placeOrder()
	{
		// GETTING CART ITEMS
		JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
		$cartCheckoutModel = new Quick2cartModelcartcheckout;
		$cartDetails = $cartCheckoutModel->getCheckoutCartitemsDetails();

		// Prepare data for order API
		$orderData = new stdClass;
		$products_data = array();
		$apiData = new stdClass;
		$apiData->userId = 0;
		$apiData->address = array();

		if (Factory::getUser()->id)
		{
			$apiData->userId = Factory::getUser()->id;
		}

		$apiData->products_data = array();

		if (!empty($cartDetails))
		{
			foreach ($cartDetails as $data)
			{
				$citem = new stdclass;
				$citem->store_id = $data['store_id'];
				$citem->product_id = $data['item_id'];
				$citem->product_quantity = $data['qty'];
				$citem->att_option = array();
				$selectedAttrOptions = $data['product_attributes'];

				if (!empty($selectedAttrOptions))
				{
					$selectedAttrOptionsArray = explode(",", $selectedAttrOptions);
					$selectedAttrOptionsArray = array_filter($selectedAttrOptionsArray, "trim");

					$prodAttributeDetails = $data['prodAttributeDetails'];

					// If attribute detail array present
					if ($data['prodAttributeDetails'])
					{
						// This is array which containg the optionid, cart_attribute id (NOT ATTRIBUTE ID), option text (for text box: entered text)
						$product_attributes_values = $data['product_attributes_values'];

						foreach ($data['prodAttributeDetails'] as $attDetail)
						{
							// Get option list
							foreach ($attDetail->optionDetails as $option)
							{
								// CUrrent option in selected options
								if (in_array($option->itemattributeoption_id, $selectedAttrOptionsArray))
								{
									$attri_id = $option->itemattribute_id;
									$itemattributeoption_id = $option->itemattributeoption_id;

									if ($attDetail->attributeFieldType == "Select")
									{
										$citem->att_option[$attri_id] = $itemattributeoption_id;
									}
									elseif ($attDetail->attributeFieldType == "Textbox")
									{
										// We have to create array
										$citem->att_option[$attri_id]['option_id'] = $itemattributeoption_id;
										$citem->att_option[$attri_id]['value'] = $product_attributes_values[$itemattributeoption_id]->cartitemattribute_name;
									}

									break;
								}
							}
						}
					}
				}

				// Add in array
				$apiData->products_data[] = (array) $citem;
			}
		}
		else
		{
			$orderStatus = new stdclass;
			$orderStatus->status = "error";
			$orderStatus->order_id = 0;
			$orderStatus->message = Text::_('PLUG_AMAZON_INVALID_DATA_CART_EMPTY');

			return $orderStatus;
		}

		// Get coupon detail from current session
		$session = Factory::getSession();
		$coupon = $session->get('coupon');
		$cartCopCode = '';

		if (!empty($coupon[0]->item_id))
		{
			// Item specific coupon
		}
		else
		{
			$cartCopCode = $coupon[0]["code"];
		}

		$apiData->coupon_code[] = $cartCopCode;

		// GETTING CART ITEMS
		JLoader::import('createorder', JPATH_SITE . '/components/com_quick2cart/helpers');
		$CreateOrderHelper = new CreateOrderHelper;

		return $orderStatus = $CreateOrderHelper->qtc_place_order($apiData);
	}

	/**
	 * This trigger will call when amazon redict on success
	 *
	 * @since   2.2
	 *
	 * @return   string  vars
	 */
	public function onATP_successRedirect()
	{
		// Delay for 10 sec
		// usleep(10000000);

		$siteBaseURL = str_replace("plugins/system/qtcamazon_easycheckout/", "", Uri::root(false));
		$jinput = Factory::getApplication()->input;
		$order_id = $jinput->get("amznPmtsReqId");

		if (empty($order_id))
		{
			// Redirect to home page
			return;
		}

		// $ajaxURL = $siteBaseURL . 'index.php?option=com_quick2cart&plgType=system&task=getOrderEmail&callType=0&order_id=' . $order_id;

		require_once JPATH_SITE . '/components/com_quick2cart/helper.php';
		$comquick2cartHelper = new comquick2cartHelper;
		$getorderinfo = $comquick2cartHelper->getorderinfo($order_id);

		// $comquick2cartHelper->updatestatus($order_id, "P", '', 1);

		$getorderinfo = $comquick2cartHelper->getorderinfo($order_id);
		$user_id = Factory::getUser()->id;
		$guest_email = '';

		$siteHome = Uri::root();
		$link = $this->params->get('returnVisitPage', $siteHome);

		Factory::getApplication()->enqueueMessage(Text::_('PLUG_AMAZON_ORDER_SUCCESS_MSG'), 'success');
		$app = Factory::getApplication();
		$app->redirect($link, "");
	}

	/**
	 * This trigger will call when amazon redict on cancel
	 *
	 * @since   2.2
	 *
	 * @return   string  vars
	 */
	public function onATP_cancelRedirect()
	{
		$jinput = Factory::getApplication()->input;
		require_once JPATH_SITE . '/components/com_quick2cart/helper.php';
		$comquick2cartHelper = new comquick2cartHelper;

		/* As cancel  status will be hanlded in IOPN function
		 * $comquick2cartHelper->updatestatus($order_id, "E",  '', $send_mail = 0);
		 */

		$siteHome = Uri::root();
		$link = $this->params->get('returnVisitPage', $siteHome);
		Factory::getApplication()->enqueueMessage(Text::_('PLUG_AMAZON_ORDER_CANCEL_MSG'), 'error');
		$app = Factory::getApplication();
		$app->redirect($link, "");
	}

	/**
	 * Builds the layout to be shown
	 *
	 * @param   string  $data    Data needed for layout
	 * @param   string  $layout  layout
	 *
	 * @since   2.2
	 *
	 * @return   string  vars
	 */
	private function buildLayout($data, $layout = 'default')
	{
		// Load the layout & push variables
		ob_start();
		$layout = $this->buildLayoutPath($layout);

		include $layout;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
