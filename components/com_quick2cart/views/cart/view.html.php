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
use Joomla\CMS\MVC\View\HtmlView;

/**
 * This Class supports Cart.
 *
 * @package     Joomla.Site
 * @subpackage  com_quick2cart
 * @since       1.0
 */
class Quick2cartViewcart extends HtmlView
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
		$this->params = ComponentHelper::getParams('com_quick2cart');

		JLoader::import('promotion', JPATH_SITE . '/components/com_quick2cart/models');
		$promotionModel = new Quick2cartModelPromotion();

		JLoader::import('cartcheckout', JPATH_SITE . '/components/com_quick2cart/models');
		$cartCheckoutModel = new Quick2cartModelcartcheckout;

		// Get cart details
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

		if (!empty($this->applicablePromotionsList)) {
			foreach ($this->applicablePromotionsList as $promotion) {
				$promotion->description = $promotionModel->generatePromotionDescription(
					$promotionModel->getItem($promotion->id, false)
				);
			}
		}

		parent::display($tpl);
	}
}
