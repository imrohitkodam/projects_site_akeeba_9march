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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;

/**
 * This Class supports checkout process.
 *
 * @package     Joomla.Site
 * @subpackage  com_quick2cart
 * @since       1.0
 */
class Quick2cartViewcartcheckout extends HtmlView
{
	/**
	 * Render view.
	 *
	 * @param   array  $tpl  An optional associative array of configuration settings.
	 *
	 * @since   1.0
	 * @return   null
	 */
	public function display($tpl = null)
	{
		$user    = Factory::getUser();
		$app     = Factory::getApplication();
		$input   = $app->input;
		$session = Factory::getSession();
		$params  = $this->params = ComponentHelper::getParams('com_quick2cart');

		$comquick2cartHelper        = new comquick2cartHelper;
		$modelsPath                 = JPATH_SITE . '/components/com_quick2cart/models/customer_addressform.php';
		$customer_addressform_model = $comquick2cartHelper->loadqtcClass($modelsPath, "Quick2cartModelCustomer_AddressForm");

		if (!empty($user->id))
		{
			$this->addressesListHtml = $customer_addressform_model->getUserAddressList($user->id);
		}

		require_once JPATH_SITE . '/components/com_quick2cart/helpers/media.php';

		// Create object of media helper class
		$this->media = new qtc_mediaHelper;
		$model       = $this->getModel('cartcheckout');
		$layout      = $input->get('layout', '');

		// Send to joomla's registration of guest ckout is off
		if ($layout == 'cancel' || $layout == 'orderdetails')
		{
			$input->set('remote', 1);
			$sacontroller = new quick2cartController;
			$sacontroller->execute('clearcart');
		}
		else
		{
			$guestcheckout = $params->get('guest');

			if ($guestcheckout == 0 && !($user->id))
			{
				$msg = Text::_('QTC_LOGIN');
				$uri = $_SERVER["REQUEST_URI"];
				$url = base64_encode($uri);
				$app->enqueueMessage($msg, 'error');
				$app->redirect(Route::_('index.php?option=com_users&view=login&return=' . $url, false));
			}

			// GETTING CART ITEMS
			JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
			$cartCheckoutModel = new Quick2cartModelcartcheckout;
			$this->cart = $cartCheckoutModel->getCheckoutCartitemsDetails();

			// Get promtion discount
			$path = JPATH_SITE . '/components/com_quick2cart/helpers/promotion.php';

			if (!class_exists('PromotionHelper'))
			{
				JLoader::register('PromotionHelper', $path);
				JLoader::load('PromotionHelper');
			}

			$promotionHelper  = new PromotionHelper;
			$this->coupon     = $promotionHelper->getSessionCoupon();
			$this->promotions = $promotionHelper->getCartPromotionDetail($this->cart, $this->coupon);
			$this->applicablePromotionsList = $promotionHelper->getApplicableCoupons($this->cart);

			JLoader::import('promotion', JPATH_SITE . '/components/com_quick2cart/models');
			$promotionModel = new Quick2cartModelPromotion();

			if (!empty($this->applicablePromotionsList)) {
				foreach ($this->applicablePromotionsList as $promotion) {
					$promotion->description = $promotionModel->generatePromotionDescription(
						$promotionModel->getItem($promotion->id, false)
					);
				}
			}
			
			if ($user->id != 0)
			{
				$this->userdata = $model->userdata();
			}

			if ($layout == 'payment')
			{
				$orders_site = '1';
				$orderid     = $session->get('order_id');
				$order       = $comquick2cartHelper->getorderinfo($orderid);

				if (!empty($order))
				{
					if (is_array($order))
					{
						$this->orderinfo  = $order['order_info'];
						$this->orderitems = $order['items'];
					}
					elseif ($order == 0)
					{
						$this->undefined_orderid_msg = 1;
					}

					JLoader::import('payment', JPATH_SITE . '/components/com_quick2cart/models');
					$paymodel      = new Quick2cartModelpayment;
					$payhtml       = $paymodel->getHTML($order['order_info'][0]->processor, $orderid);
					$this->payhtml = $payhtml[0];
				}
				else
				{
					$this->undefined_orderid_msg = 1;
				}

				$orders_site = '1';
				$this->orders_site = $orders_site;

				// Make cart empty
				JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
				$Quick2cartModelcart = new Quick2cartModelcart;
				$Quick2cartModelcart->empty_cart();
			}
			else
			{
				// START Q2C Sample development
				PluginHelper::importPlugin('system');

				// Call the plugin and get the result
				$result     = $app->triggerEvent('onBeforeQ2cCheckoutCartDisplay');
				$beforecart = '';

				if (!empty($result))
				{
					$beforecart .= trim(implode(" \n ", $result));
				}

				// Depricated start
				$result = $app->triggerEvent('onBeforeQ2cCheckoutCartDisplay');

				if (!empty($result))
				{
					$beforecart .= trim(implode(" \n ", $result));
				}

				// Depricated end  //////////////////////////////////////////////////////////////
				$this->beforecart = $beforecart;
				$result           = $app->triggerEvent('onAfterQ2cCheckoutCartDisplay');
				$aftercart        = '';

				if (!empty($result))
				{
					$aftercart .= trim(implode(" \n ", $result));
				}

				// Depricated start

				if (!empty($result))
				{
					$aftercart .= trim(implode(" \n ", $result));
				}

				// Depricated end  //////////////////////////////////////////////////////////////
				$this->aftercart = $aftercart;

				// END Q2C Sample development
				// Q2C Sample development - ADD TAB in ckout page
				PluginHelper::importPlugin('system');
				$result = $app->triggerEvent('onQ2cAddTabOnCheckoutPage', array($this->cart));
				$this->addTab = '';
				$this->addTabPlace = '';

				if (!empty($result))
				{
					$this->addTab = $result[0];
					$this->addTabPlace = !empty($result[0]['tabPlace']) ? $result[0]['tabPlace'] : '';
				}
				// END - Q2C Sample development - ADD TAB in ckout page
				// Trigger plg to add plg after shipping tab

				// GETTING country
				$country       = $this->get("Country");
				$this->country = $country;
			}

			// Getting GETWAYS
			PluginHelper::importPlugin('payment');

			if (!is_array($params->get('gateways')) )
			{
				$gateway_param[] = $params->get('gateways');
			}
			else
			{
				$gateway_param = $params->get('gateways');
			}

			if (!empty($gateway_param))
			{
				$gateways = $app->triggerEvent('onTP_GetInfo', array($gateway_param));
			}

			$this->gateways = $gateways;

			// START Q2C Sample development
			PluginHelper::importPlugin('system');

			// Call the plugin and get the result
			$result = $app->triggerEvent('onSystemBeforeDisplayingPaymentList', array($this->gateways, $this->cart));

			if (!empty($result[0]))
			{
				$beforecart     = trim(implode(" \n ", $result));
				$this->gateways = $result[0];
			}

			// START Q2C Sample development
			PluginHelper::importPlugin('system', "qtcamazon_easycheckout");

			/* Call the plugin and get the result
			$results = $dispatcher->trigger('onATP_processIOPN');
			*/
			$results = $app->triggerEvent('onBeforeCheckoutViewDisplay');
			$this->onBeforeCheckoutViewDisplay = '';

			if (!empty($results))
			{
				$this->onBeforeCheckoutViewDisplay = trim(implode(" \n ", $results));
			}
		}

		$this->_setToolBar();
		parent::display($tpl);
	}

	/**
	 * Method Allow to set toolbar.
	 *
	 * @return  void
	 */
	private function _setToolBar()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('QTC_CARTCHECKOUT_PAGE'));
	}
}
