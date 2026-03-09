<?php
/**
 * @package     JGive
 * @subpackage  com_jgive
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2020 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;

use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\Component\Categories\Administrator\Table as categoryTable;

jimport('techjoomla.tjnotifications.tjnotifications');

include_once JPATH_SITE . '/components/com_tjvendors/includes/tjvendors.php';

/**
 * Class Quick2CartMailsHelper
 *
 * @since  2.1
 */
class Quick2CartMailsHelper
{
	protected $q2cparams;

	protected $siteConfig;

	protected $sitename;

	protected $siteadminname;

	protected $user;

	protected $client;

	protected $tjnotifications;

	protected $siteinfo;

	protected $comquick2cartHelper;
	/**
	 * Method acts as a consturctor
	 *
	 * @since   1.0.0
	 */
	public function __construct()
	{
		$this->q2cparams         = ComponentHelper::getParams('com_quick2cart');
		$this->siteConfig          = Factory::getConfig();
		$this->sitename            = $this->siteConfig->get('sitename');
		$this->siteadminname       = $this->siteConfig->get('fromname');
		$this->user                = Factory::getUser();
		$this->client              = "com_quick2cart";
		$this->tjnotifications     = new Tjnotifications;
		$this->comquick2cartHelper = new Comquick2cartHelper;

		$this->siteinfo            = new stdClass;
		$this->siteinfo->sitename  = $this->sitename;
		$this->siteinfo->adminname = Text::_('COM_JGIVE_SITEADMIN');
	}

	/**
	 * Send mails when campaign is created
	 *
	 * @param   OBJECT  $campaignDetails  Campaigns Detail
	 *
	 * @return void
	 */
	public function onAfterOrderStatusUpdated($orderIitemInfo)
	{
		$order_info = $orderIitemInfo['order_info'][0];
		$order_items = $orderIitemInfo['items'];

		if($order_info->status == 'C') {
			$recipientEmailArray = array();
			$recipientEmailArray[] = $order_info->user_email ? $order_info->user_email : "";
			
			$recipients = array(
				'email' => array(
					'to' => $recipientEmailArray
				)
			);
				

			$siteInfo           = new stdClass;
			$siteInfo->sitename = $this->sitename;

			$options = new Registry;
			$options->set('product', $order_info);
			$options->set('info', $siteInfo);

			$replacements           = new stdClass;
			$replacements->info     = $this->siteinfo;
			$replacements->product= new stdClass;
			$replacements->product->buyer = $order_info->firstname;

			$guest_email = '';

			if (!$order_info->user_id && $this->q2cparams->get('guest'))
			{
				$guest_email = "&email=" . md5($order_info->user_email);
			}

			foreach($order_items as $item){
				$tempLink = Route::_('index.php?option=com_quick2cart&view=productpage&layout=default&item_id='.$item->item_id.'#jlike_reviews');
				$link       = Uri::root() . substr($tempLink, strlen(Uri::base(true)) + 1);
				$replacements->product->name = $item->order_item_name;
				$replacements->product->link = $link;
				$tjnotifications     = new Tjnotifications;
				$tjnotifications->send($this->client, "addReviewToTheProduct", $recipients, $replacements, $options);
			}
		}

		return;
	}

	/**
	 * Send discount coupon emails to eligible users for a given promotion.
	 *
	 * This method fetches eligible users for a specified promotion ID, prepares
	 * personalized email content including coupon code, discount amount, and expiry,
	 * and sends the emails using the Tjnotifications system.
	 *
	 * @param   int  $promotionId  ID of the promotion for which emails need to be sent.
	 *
	 * @return  void
	 */
	function sendEligibleUserDiscountEmails($promotionId)
	{
		JLoader::import('promotion', JPATH_SITE . '/components/com_quick2cart/models');
		$promotionModelPromo = new Quick2cartModelPromotion();

		$this->params         = ComponentHelper::getParams('com_quick2cart');
		$currencies_sym       = $this->params->get('addcurrency_sym');
		JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_quick2cart/models');
		$promotionModel       = JModelLegacy::getInstance('promotions', 'Quick2cartModel');
		$data                 = $promotionModel->getEligibleUsersForPromotions([$promotionId]);

		$eligibleUsers = $data[$promotionId]['users'];
		$promotion     = $data[$promotionId]['promotion'];

		$couponCode = ($promotion['coupon_required'] == 1) ? $promotion['coupon_code'] : '';
		$discount   = $promotion['discount'] . ($promotion['discount_type'] == 'percentage' ? '%' : $currencies_sym);
		$expiryDate = $promotion['exp_date'];

		$couponDescription = $promotionModelPromo->generatePromotionDescription($promotionModelPromo->getItem($promotion['id'], false));

		if (empty($eligibleUsers))
		{
			return;
		}

		foreach ($eligibleUsers as $userId)
		{
			$user = JFactory::getUser($userId);

			if (!$user || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
				continue;
			}

			$recipients = array(
				'email' => array(
					'to' => array($user->email)
				)
			);

			$options = new Registry;
			$options->set('info', (object)[
				'sitename' => Factory::getApplication()->get('sitename')
			]);

			$replacements = new stdClass;
			$replacements->user = new stdClass;
			$replacements->coupon = new stdClass;

			$replacements->user->name         = $user->name;
			$replacements->coupon->code       = $couponCode;
			$replacements->coupon->id         = $promotionId;
			$replacements->coupon->discount   = $discount;
			$replacements->coupon->expiry     = $expiryDate;
			$replacements->coupon->description = $couponDescription;

			$tjnotifications = new Tjnotifications;
			$tjnotifications->send('com_quick2cart', 'specificUserDiscountCoupon', $recipients, $replacements, $options);
		}
	}

	/**
	 * Send OTP to user via email using the template
	 *
	 * @param string $email   User's email address
	 *
	 * @param string $otp     OTP code
	 *
	 * @return void
	 */
	public function sendOtpToUser($email, $otp)
	{
		$recipients = array(
			'email' => array(
				'to' => array($email)
			)
		);

		$replacements = new stdClass;
		$replacements->user = new stdClass;
		$replacements->user->otp = $otp;
		$options = new Registry;

		$this->tjnotifications->send($this->client, "sendOtpToUser", $recipients, $replacements, $options);
	}
}
