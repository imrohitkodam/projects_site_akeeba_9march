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
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

$app      = Factory::getApplication();
$user     = Factory::getUser();
$vars     = new stdclass;
$currency = $cart[0]['currency'];
$params   = ComponentHelper::getParams('com_quick2cart');
JLoader::import('components.com_quick2cart.tables.customer_address', JPATH_ADMINISTRATOR);
$customerAddressTable = Table::getInstance('customer_Address', 'Quick2cartTable', array('dbo', Factory::getDbo()));

require_once JPATH_SITE . '/components/com_quick2cart/helpers/order.php';
$orderHelper = new OrderHelper;

if (!empty($user->id))
{
	if ($inputArray['shipping_address'])
	{
		$customerAddressTable->load($inputArray['shipping_address']);
		$shipinfo = $customerAddressTable->getProperties();

		if (!empty($shipinfo['state_code']))
		{
			$shipinfo['state_name'] = $quick2cartHelper->getStateName($shipinfo['state_code']);
		}

		if (!empty($shipinfo['country_code']))
		{
			$shipinfo['country_name'] = $quick2cartHelper->getCountryName($shipinfo['country_code']);
		}

		$vars->shipping_address = $createOrderHelper->mapUserAddress($shipinfo);
	}

	if ($inputArray['billing_address'])
	{
		$customerAddressTable->load($inputArray['billing_address']);
		$billinfo = $customerAddressTable->getProperties();

		if (!empty($billinfo['state_code']))
		{
			$billinfo['state_name'] = $quick2cartHelper->getStateName($billinfo['state_code']);
		}

		if (!empty($billinfo['country_code']))
		{
			$billinfo['country_name'] = $quick2cartHelper->getCountryName($billinfo['country_code']);
		}

		$vars->billing_address = $createOrderHelper->mapUserAddress($billinfo);
	}
}
else
{
	$vars->shipping_address = $inputArray['ship'];
	$vars->billing_address = $inputArray['bill'];
}
$multivendorEnable      = $params->get('multivendor');
$isShippingEnabled      = $params->get('shipping', 0);
$isTaxationEnabled      = $params->get('enableTaxtion', 0);
$showTermsCond          = $params->get('termsConditons', 0);
$checkoutTermsCondArtId = $params->get('termsConditonsArtId', 0);
$shippingMode           = $params->get('shippingMode', 'itemLevel');
$termsCondArtId         = trim($checkoutTermsCondArtId);
$shippingCharges        = 0;
$taxCharges             = 0;
$showTermsAndConditions = (!empty($showTermsCond) && !empty($termsCondArtId)) ? 1 : 0;

if ($isShippingEnabled)
{
	$vars->ship_chk = 1;
	$itemsShipMethRateDetail = $app->input->get('itemshipMethDetails', '', '');

	if (!empty($itemsShipMethRateDetail))
	{
		$vars->itemsShipMethRateDetail = $app->input->get('itemshipMethDetails', '', '');
	}

	$selectedItemshipMeth = $app->input->get('itemshipMeth', '', '');

	if (!empty($selectedItemshipMeth))
	{
		$vars->selectedItemshipMeth = $app->input->get('itemshipMeth', '', '');
	}

	$vars->cartItemDetail = array();

	foreach ($productsData as $productData)
	{
		$product                        = array();
		$product['item_id']             = $productData['product_id'];
		$product['product_final_price'] = $product['tamt'] = $createOrderHelper->calculateProductPrice($productData);
		$product['product_attributes']  = "";

		if (!empty($productData['att_option']))
		{
			$product['product_attributes'] = $createOrderHelper->getSelectedAttributesList($productData['att_option']);
			$attri_items_info              = $createOrderHelper->getItemAttributeDetails($product['product_attributes']);

			if (!empty($attri_items_info))
			{
				$product['product_attribute_names'] = $createOrderHelper->getItemsSelectedAttributeName($productData['att_option'], $attri_items_info);
			}
		}

		$product['qty']         = $productData["product_quantity"];
		$curr                   = $quick2cartHelper->getCurrencySession();
		$product['currency']    = $curr;
		$vars->cartItemDetail[] = $product;
	}

	$coupon           = $promotionHelper->getSessionCoupon();
	$taxDetails       = $cartCheckoutModel->afterTaxPrice($orderAmount, $vars);

	if (!empty($taxDetails->charges))
	{
		$taxCharges = $taxDetails->charges;
	}

	$shippingDetails = $cartCheckoutModel->afterShipPrice($orderAmount, $vars);

	// If allowed to place order
	if (!empty($shippingDetails['allowToPlaceOrder']) && $shippingDetails['allowToPlaceOrder'] == 1 && !empty($shippingDetails['charges']))
	{
		$shippingCharges = $shippingDetails['charges'];
	}
}

$showoptioncol = 0;

foreach ($cart as $citem)
{
	if (!empty($citem['options']))
	{
		$showoptioncol = 1;
		break;
	}
}

$allowToScheduleDeliverySlot = $params->get('allow_schedule_delivery_slot', '0', 'String');
$advanceDeliveryDays         = $params->get('advance_delivery_days', 7, 'Integer');
$minDeliveryTime             = $params->get('min_delivery_time', 30, 'Integer');
$deliverytimeslots           = $params->get('deliverytimeslots');
$deliveryTimeFormat          = $params->get('delivery_time_format', 12, 'Integer');

if (isset($allowToScheduleDeliverySlot) && $allowToScheduleDeliverySlot == '1')
{
	$now              = date("d-m-Y H:i:s",time());
	$currenctDateTime = new DateTime($now);

	// Get min delivery time slot
	$minDeliverySlotArray   = $orderHelper->getMinDeliveryTimeSlot();
	$minDeliverySlotTime    = $minDeliverySlotArray['deliveryslottotime'];
	$minDeliverySlotTimeKey = $minDeliverySlotArray['deliveryslottotimeKey'];

	// Get max delivery time slot
	$maxDeliverySlotArray   = $orderHelper->getMaxDeliveryTimeSlot();
	$maxDeliverySlotTime    = $maxDeliverySlotArray['deliveryslottotime'];
	$maxDeliverySlotTimeKey = $maxDeliverySlotArray['deliveryslottotimeKey'];
	$todaysMaxDeliveryTime  = new DateTime(date('d-m-Y') . $maxDeliverySlotTime . ':00:00');

	// Get Today's next immediate delivery time slot
	$nextImmediateDeliverySlotArray    = $orderHelper->getNextImmediateDeliveryTimeSlot();
	$nextImmediateDeliveryTimeSlot     = $nextImmediateDeliverySlotArray['deliveryslottotime'];
	$nextImmediateDeliveryTimeSlotKey  = $nextImmediateDeliverySlotArray['deliveryslottotimeKey'];
	$nextImmediateDeliveryTimeSlotFlag = $nextImmediateDeliverySlotArray['nextImmediateDeliveryTimeSlotFlag'];

	if ($nextImmediateDeliveryTimeSlotFlag)
	{
		$todaysNextImmediateDeliveryTime = date('d-m-Y') . ' ' . $nextImmediateDeliveryTimeSlot . ':00:00';
	}

	$timeinterval = round(abs(strtotime($todaysNextImmediateDeliveryTime) - strtotime($now))/60);

	if ($deliveryTimeFormat == 12)
	{
		if($currenctDateTime > $todaysMaxDeliveryTime)
		{
			$fromDateString = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslotfromtime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
			$toDateString   = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslottotime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
		}
		else
		{
			$fromDateString = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslotfromtime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
			$toDateString = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslottotime.':00:00 ' . HTMLHelper::_('date', $now, 'd-m-Y');
		}

		$fromDate = date('h a', strtotime($fromDateString));
		$toDate   = date('h a', strtotime($toDateString));
	}
	else
	{
		if($currenctDateTime > $todaysMaxDeliveryTime)
		{
			$fromDate = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslotfromtime;
			$toDate   = $deliverytimeslots->$minDeliverySlotTimeKey->deliveryslottotime;
		}
		else
		{
			$fromDate = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslotfromtime;
			$toDate   = $deliverytimeslots->$nextImmediateDeliveryTimeSlotKey->deliveryslottotime;
		}
	}

	//If delivery time slot available within currenttime + minDeliveryTime then display Today mindeliverytime min
	//For e.g. Today 30 min
	if ($nextImmediateDeliveryTimeSlotFlag && ($timeinterval < $minDeliveryTime))
	{
		$immediateOrderBtnLabel = JText::sprintf("QTC_ORDER_DETAIL_DEFAULT_MIN_DELIVERY_SLOT_TIME", $minDeliveryTime);
	}
	elseif($currenctDateTime > $todaysMaxDeliveryTime)
	{
		$immediateOrderBtnLabel = JText::sprintf("QTC_ORDER_DETAIL_DEFAULT_MIN_DELIVERY_SLOT_FOR_TOMORROW", $fromDate . ' - ' . $toDate);
	}
	else
	{
		$immediateOrderBtnLabel = JText::sprintf("QTC_ORDER_DETAIL_IMMEDIATE_DELIVERY_SLOT_TIME_FOR_TODAY", $fromDate . ' - ' . $toDate);
	}
}
?>
<script type="text/javascript">
	techjoomla.jQuery(document).ready(function() {
		jQuery('.discount').popover();

		//~ jQuery("#deliverySlotModalToggleBtn").click(function (e) {
			//~ alert("Deepa dont take tension");
			//~ jQuery('.modal-content').style.width = window.innerWidth + 'px !important';
			//~ jQuery('.modal-content').style.height = window.innerHeight + 'px !important';
		//~ });
		
		
		//~ jQuery('#deliverySlotModalToggleBtn').on('show.bs.modal', function () {
			   //~ jQuery(this).find('.modal-body').css({
					  //~ width:"'"+ window.innerWidth +"'", //probably not needed
					  //~ height:'auto', //probably not needed 
					  //~ 'max-height':'100%'
			   //~ });
		//~ });

		jQuery(".selectDeliverySlot").click(function (e) {
			jQuery('#defaultSlot').attr('class', 'btn btn-primary selectDeliverySlot');
			jQuery('.perDaydeliverySlots button.active').removeClass('active');
			jQuery(this).addClass('btn btn-primary selectDeliverySlot active');
		});

		getDeliverySlotDetails();
	});

	function getDeliverySlotDetails()
	{
		var Datadate      = jQuery('.perDaydeliverySlots').find('li').find('button.active').data('date');
		var Datatime      = jQuery('.perDaydeliverySlots').find('li').find('button.active').data('time');
		var Dataslotlabel = jQuery('.perDaydeliverySlots').find('li').find('button.active').data('slotlabel');

		jQuery('#deliveryDate').attr('value', Datadate);
		jQuery('#deliveryTime').attr('value', Datatime);
		jQuery('#deliverySlotModalToggleBtn').html(Dataslotlabel + ' ' +'<i class="fa fa-angle-down" aria-hidden="true"></i>');
	}
</script>

<div class="q2c-wrapper tjBs3  q2c_border">
	<div id="printOrder">
		<legend><?php echo Text::_('QTC_ORDER_DETAIL'); ?></legend>
		<div style="clear: both;"></div>

		<!-- Display cart detail -->
		<div class="row">
			<div class="qtcPadding">
				<!-- Start Cart detail -->
				<div class="qtcPadding1111">
					<h4><?php echo Text::_('QTC_ORDER_DETAILS'); ?></h4>
					<div class="table-responsive" id="no-more-tables">
						<table width="100%" class="table table-condensed table-bordered qtc-table table-hover border table-striped" cellspacing="0">
							<thead class="table-primary">
								<tr class="hidden-xs hidden-sm">
									<th class="cartitem_num text-start" width="5%" align="left">
										<?php echo Text::_('QTC_NO');?>
									</th>
									<th class="cartitem_name text-start" align="left">
										<?php echo Text::_('QTC_PRODUCT_NAM');?>
									</th>
									<?php
									if ($showoptioncol == 1)
									{
										?>
										<th class="cartitem_opt text-start" align="left">
											<?php echo Text::_('QTC_PRODUCT_OPTS');?>
										</th>
										<?php
									}
									?>
									<th class="cartitem_qty rightalign text-start" width="5%"><?php echo Text::_('QTC_PRODUCT_QTY'); ?></th>
									<th class="cartitem_price rightalign text-start" width="15%"><?php echo Text::_('QTC_PRODUCT_PRICE'); ?></th>
									<th class="cartitem_tprice rightalign text-start" width="15%"><?php echo Text::_('QTC_PRODUCT_TPRICE'); ?></th>
								</tr>
							</thead>
							<?php
							$tprice            = 0;
							$itemCount         = 1;
							$storeArray        = array();
							$cartItemIds       = array();
							$totalItemDiscount = 0;

							foreach ($cart as $cartItem)
							{
								// If multivendor enabled then show store title
								if (!empty($multivendorEnable))
								{
									if (!in_array($cartItem['store_id'], $storeArray))
									{
										$storeArray[] = $cartItem['store_id'];
										$storeinfo    = $quick2cartHelper->getSoreInfo($cartItem['store_id']);
										?>
										<tr class="info">
											<td class="hidden-xs"></td>
											<td colspan="<?php echo (($showoptioncol == 1) ? "5" : "4"); ?>">
												<strong><?php echo htmlspecialchars($storeinfo['title'], ENT_COMPAT, 'UTF-8'); ?></strong>
											</td>
										</tr>
										<?php
									}
								}
								?>
								<tr class="row0 table-light">
									<td class="cartitem_num" data-title="<?php echo Text::_('QTC_NO'); ?>"><?php echo $itemCount++; ?></td>
									<td class="cartitem_name" data-title="<?php echo Text::_('QTC_PRODUCT_NAM'); ?>">
										<?php
											$product_link = $quick2cartHelper->getProductLink($cartItem['item_id'], 'detailsLink', 1);

											if (empty($product_link))
											{
												echo htmlspecialchars($cartItem['title'], ENT_COMPAT, 'UTF-8');
											}
											else
											{
												?>
												<a href="<?php echo $product_link; ?>">
													<?php echo htmlspecialchars($cartItem['title'], ENT_COMPAT, 'UTF-8'); ?>
												</a>
												<?php
											}

											// Show sku
											$sku_item_id = !empty($cartItem['variant_item_id']) ? $cartItem['variant_item_id'] : $cartItem['item_id'];
											$sku         = $productHelper->getSku($sku_item_id);

											if (!empty($sku))
											{
											?>
												<span title="<?php echo Text::_('QTC_PROD_SKU_TOOLTIP'); ?>">
													( <?php echo Text::_('QTC_PROD_SKU'); ?> : <?php echo $sku; ?> )
												</span>
											<?php
											}

											$cartItemIds[] = $cartItem['item_id'];

											// Showing shipping method name
											if (!empty($shippingDetails['itemShipMethDetail']))
											{
												$shippingDetails['itemShipMethDetail'] = array_unique($shippingDetails['itemShipMethDetail']);
												foreach ($shippingDetails['itemShipMethDetail'] as $itemShipDetail)
												{
													if ($itemShipDetail['item_id'] == $cartItem['item_id'] && (!empty($itemShipDetail['name'])))
													{
														?>
														<span>
															<strong><br /><?php echo Text::_('COM_QUICK2CART_ORDER_SHIP_METH') . ": " ?> </strong>
															<?php echo $itemShipDetail['name']; ?>
														</span>
														<?php
													}
												}
											}
										?>
									</td>
									<?php
									if ($showoptioncol == 1)
									{
										?>
										<td class="cartitem_opt" data-title="<?php echo Text::_('QTC_PRODUCT_OPTS'); ?>">
											<?php
											if (($cartItem['options']))
											{
												echo nl2br(str_replace(",", "\n", htmlspecialchars($cartItem['options'], ENT_COMPAT, 'UTF-8')));
											} ?>
										</td>
										<?php
									}
									?>
									<td class="cartitem_qty rightalign" data-title="<?php echo Text::_('QTC_PRODUCT_QTY'); ?>"><?php echo $cartItem['qty']; ?></td>
									<td class="cartitem_price rightalign" data-title="<?php echo Text::_('QTC_PRODUCT_PRICE'); ?>">
										<span>
											<?php $prodprice = (float)($cartItem['product_item_price'] + $cartItem['product_attributes_price']);
												echo $quick2cartHelper->getFromattedPrice(number_format($prodprice, 2) , $currency);
											?>
										</span>
									</td>
									<?php
										$productPrice = ($cartItem['qty'] * $prodprice);
										$tprice += $productPrice;
									?>
									<td class="cartitem_tprice rightalign" data-title="<?php echo Text::_('QTC_PRODUCT_TPRICE'); ?>">
										<span><?php echo $quick2cartHelper->getFromattedPrice(number_format($productPrice, 2) , $currency); ?></span>
									</td>
								</tr>
								<?php
								// Get promotion discount details
								$promotionDetails = $promotionHelper->getCartPromotionDetail($cart, $coupon);
								if (!empty($promotionDetails) && !empty($promotionDetails->maxDisPromo))
								{
									$maxDisPromo = $promotionDetails->maxDisPromo;

									if ($maxDisPromo->applicableMaxDiscount)
									{
										$totalItemDiscount = (float)$maxDisPromo->applicableMaxDiscount;
									}
								}
							}
							?>
							<!-- Finished the displaying item list-->
							<?php $col = ($showoptioncol == 1) ? 5 : 4;?>
							<tr>
								<td colspan="<?php echo $col; ?>" class="cartitem_tprice_label hidden-xs hidden-xs rightalign" align="left">
									<strong><?php echo Text::_('QTC_PRODUCT_TOTAL'); ?></strong>
								</td>
								<td class="cartitem_tprice rightalign" data-title="<?php echo Text::_('QTC_PRODUCT_TOTAL'); ?>">
									<span id="cop_discount">
										<?php echo $quick2cartHelper->getFromattedPrice(number_format($tprice, 2) , $currency); ?>
									</span>
								</td>
							</tr>

							<!-- Promotion discount price -->
							<?php
							if (!empty($totalItemDiscount))
							{
								$disAmt = $totalItemDiscount;
								?>
								<tr>
									<td colspan="<?php echo $col; ?>" class="cartitem_tprice_label hidden-xs hidden-xs rightalign" align="left">
										<div>
											<strong><?php echo Text::_('COM_QUICK2CART_PROMOTION_DICOUNT'); ?></strong>
										</div>
										<?php
										// Currently used for item level promotion
										if (isset($maxDisPromo->id))
										{
											if (!empty($maxDisPromo->name))
											{
												?>
												(
													<?php
													if (!empty($maxDisPromo->coupon_code))
													{
													?>
														<small>
															<strong><?php echo Text::_('QTC_DISCOUNT_CODE') . " : " . $maxDisPromo->coupon_code . " "; ?></strong>
														</small>
														<?php
													}
													?>
													<span class="promDicountTitle">
														<small><?php echo $maxDisPromo->name ?></small>
													</span>
												)
												<?php
											}
										}
										?>
									</td>
									<td class="cartitem_tprice rightalign " data-title="<?php echo sprintf(Text::_('QTC_PRODUCT_DISCOUNT') , $maxDisPromo->coupon_code); ?>">
										<span id="coupon_discount">
											<?php echo $quick2cartHelper->getFromattedPrice(number_format($disAmt, 2) , $currency); ?>
										</span>
									</td>
								</tr>

								<!-- total amt after Discount row-->
								<tr class="dis_tr">
									<td colspan="<?php echo $col; ?>" class="cartitem_tprice_label hidden-xs hidden-xs rightalign" align="left">
										<strong><?php echo Text::_('QTC_NET_AMT_PAY'); ?></strong>
									</td>
									<td class="cartitem_tprice rightalign" data-title="<?php echo Text::_('QTC_NET_AMT_PAY'); ?>">
										<span id="total_dis_cop">
											<?php echo $quick2cartHelper->getFromattedPrice(number_format($tprice - $disAmt, 2) , $currency); ?>
										</span>
									</td>
								</tr>
								<?php
								$tprice = $tprice - $disAmt;
							}
							?>
							<?php
							// Chnage ship charges according to called view. (Called from vender view then use item level shipping charges else order level)
							// Multivendor is off then display order level tax and ship(Considered: admin has only one store)
							$orderTaxAmount = (!empty($taxCharges)) ? (float) $taxCharges : 0;

							if ($isTaxationEnabled && !empty($orderTaxAmount))
							{
								?>
								<tr>
									<td colspan="<?php echo $col; ?>" class="cartitem_tprice_label hidden-xs hidden-xs rightalign" align="left">
										<strong><?php echo Text::sprintf('QTC_TAX_AMT_PAY', $orderTaxPer); ?></strong>
									</td>
									<td class="cartitem_tprice rightalign" data-title="<?php echo Text::sprintf('QTC_TAX_AMT_PAY', $orderTaxPer); ?>">
										<span id="tax_amt"><?php echo $quick2cartHelper->getFromattedPrice(number_format($orderTaxAmount, 2) , $currency); ?></span>
									</td>
								</tr>
								<?php
							}

							$orderShipAmount = (!empty($shippingCharges)) ? (float) $shippingCharges : 0;

							if (!empty($orderShipAmount))
							{
								?>
								<tr>
									<td colspan="<?php echo $col; ?>" class="cartitem_tprice_label hidden-xs hidden-xs rightalign" align="left">
										<strong><?php echo Text::sprintf('QTC_SHIP_AMT_PAY', ''); ?></strong>
									</td>
									<td class="cartitem_tprice rightalign" data-title="<?php echo Text::sprintf('QTC_SHIP_AMT_PAY', ''); ?>">
										<span id="ship_amt">
											<?php echo $quick2cartHelper->getFromattedPrice(number_format($orderShipAmount, 2) , $currency); ?>
										</span>
									</td>
								</tr>
								<?php
							}
							?>
							<!--  final order  total -->
							<tr>
								<td colspan="<?php echo $col; ?>" class="cartitem_tprice_label hidden-xs hidden-xs rightalign" align="left">
									<strong><?php echo Text::_('QTC_ORDER_TOTAL'); ?></strong>
								</td>
								<td class="cartitem_tprice rightalign" data-title="<?php echo Text::_('QTC_ORDER_TOTAL'); ?>">
									<strong>
										<span id="final_amt_pay" name="final_amt_pay">
											<?php
											$finalPaymentAmount = $tprice + ($isTaxationEnabled ? $orderTaxAmount : 0) + $orderShipAmount;
											echo $quick2cartHelper->getFromattedPrice(number_format($finalPaymentAmount, 2) , $currency);
											?>
										</span>
									</strong>
								</td>
							</tr>
						</table>
					</div>
					<!--table-responsive -->
				</div>
				<div id="q2c-ajax-call-fade-content-transparent"></div>
				<div id="q2c-ajax-call-loader-modal">
					<img id="q2c-ajax-loader" src="<?php echo Uri::root() . 'components/com_quick2cart/assets/images/ajax.gif';?>">
				</div>
				<!-- End Cart detail -->
			</div>

			<div class="qtcPadding py-3">
				<?php
				if (!empty($shipinfo) || !empty($billinfo))
				{
				?>
					<h4><?php echo Text::_('QTC_CUST_INFO'); ?></h4>
					<div class="table-responsive" id='no-more-tables' style="margin:10px 0 0px 0 !important;">
						<table class="table table-condensed table-bordered qtc-table table-hover">
							<thead class="table-dark">
								<tr>
									<th align="left">
										<?php echo Text::_('QTC_BILLIN_INFO'); ?>
									</th>
									<?php
									if ($params->get('shipping') == '1' && isset($shipinfo))
									{
									?>
									<th align="left">
										<?php echo Text::_('QTC_SHIPIN_INFO'); ?>
									</th>
									<?php
									}
									?>
								</tr>
							</thead>
							<tbody>
								<tr style="width: 100%;">
									<?php
									if (!empty($billinfo))
									{
										$billinfo = (object)$billinfo;
									?>
										<td data-title="<?php echo Text::_('QTC_BILLIN_INFO'); ?>" class="qtcWordWrap">
											<address>
												<strong>
													<?php echo htmlspecialchars($billinfo->firstname, ENT_COMPAT, 'UTF-8') . ' ';
													if ($billinfo->middlename)
													{
														echo htmlspecialchars($billinfo->middlename, ENT_COMPAT, 'UTF-8') . '&nbsp;';
													}
													echo htmlspecialchars($billinfo->lastname, ENT_COMPAT, 'UTF-8');
													?> &nbsp;&nbsp;
												</strong><br />
												<?php echo htmlspecialchars($billinfo->address, ENT_COMPAT, 'UTF-8') . ","; ?>
												<br />

												<?php
												if (!empty($billinfo->land_mark))
												{
													echo htmlspecialchars($billinfo->land_mark, ENT_COMPAT, 'UTF-8') . ', ';
												}

												echo htmlspecialchars($billinfo->city, ENT_COMPAT, 'UTF-8') . ', ';
												echo (!empty($billinfo->state_name) ? $billinfo->state_name : $billinfo->state_code) . ' ' . htmlspecialchars($billinfo->zipcode, ENT_COMPAT, 'UTF-8');
												echo '<br/>';
												echo (!empty($billinfo->country_name) ? $billinfo->country_name : $billinfo->country_code) . ', ';

												?>
												<br />
												<?php echo htmlspecialchars($billinfo->user_email, ENT_COMPAT, 'UTF-8'); ?>
												<br />
												<abbr title="<?php echo Text::_('QTC_BILLIN_PHON'); ?>"><?php echo Text::_('QTC_BILLIN_PHON'); ?> :</abbr> <?php echo htmlspecialchars($billinfo->phone, ENT_COMPAT, 'UTF-8'); ?>
											</address>
										</td>
									<?php
									}
									?>
									<?php
									if ($params->get('shipping') == '1' && isset($shipinfo))
									{
										$shipinfo = (object)$shipinfo;
										?>
										<td data-title="<?php echo Text::_('QTC_SHIPIN_INFO'); ?>" class="qtcWordWrap">
											<address>
												<strong>
													<?php echo htmlspecialchars($shipinfo->firstname, ENT_COMPAT, 'UTF-8') . ' ';
													if ($shipinfo->middlename)
													{
														echo htmlspecialchars($shipinfo->middlename, ENT_COMPAT, 'UTF-8') . '&nbsp;';
													}
													echo htmlspecialchars($shipinfo->lastname, ENT_COMPAT, 'UTF-8');
													?> &nbsp;&nbsp;
												</strong><br />
												<?php echo htmlspecialchars($shipinfo->address, ENT_COMPAT, 'UTF-8') . ","; ?>
												<br />
												<?php
												if (!empty($shipinfo->land_mark))
												{
													echo htmlspecialchars($shipinfo->land_mark, ENT_COMPAT, 'UTF-8') . ", ";
												}
												echo htmlspecialchars($shipinfo->city, ENT_COMPAT, 'UTF-8') . ', ';
												echo (!empty($shipinfo->state_name) ? $shipinfo->state_name : $shipinfo->state_code) . ' ' . $shipinfo->zipcode;
												echo '<br/>';
												echo (!empty($shipinfo->country_name) ? $shipinfo->country_name : $shipinfo->country_code) . ', ';
												?>
												<br />
												<?php echo htmlspecialchars($shipinfo->user_email, ENT_COMPAT, 'UTF-8'); ?>
												<br />
												<abbr title="<?php echo Text::_('QTC_BILLIN_PHON'); ?>"><?php echo Text::_('QTC_BILLIN_PHON'); ?>:</abbr> <?php echo htmlspecialchars($shipinfo->phone, ENT_COMPAT, 'UTF-8'); ?>
											</address>
										</td>
										<?php
									}
								?>
								</tr>
							</tbody>
						</table>
					</div>
				<?php
				}
				?>
			</div>
			<?php

			if (isset($allowToScheduleDeliverySlot) && $allowToScheduleDeliverySlot == '1')
			{
				?>
				<div class="qtcPadding">
					<h4><?php echo Text::_('QTC_ORDER_DETAIL_CHOOSE_DELIVERY_SLOT'); ?></h4>
					<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#deliverySlotModal"
					title="<?php echo Text::_('QTC_ORDER_DETAIL_CHOOSE_DELIVERY_SLOT')?>" id="deliverySlotModalToggleBtn">
						<?php echo $immediateOrderBtnLabel . '&nbsp';?><i class="fa fa-angle-down" aria-hidden="true"></i>
					</button>
					<div class="modal fade" id="deliverySlotModal" tabindex="-1" role="dialog" aria-labelledby="deliverySlotModalLabel" aria-hidden="true">
						<div class="modal-dialog modal-md modal-dialog-scrollable" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h5 class="modal-title af-text-center" id="deliverySlotModalLabel">
										<?php echo Text::_('QTC_ORDER_DETAIL_SELECT_DELIVERY_SLOT_TITLE');?>
									</h5>
									<button type="button" class="btn-close novalidate" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">
									<ul class="nav nav-tabs" role="tablist">
										<?php
										$today = new DateTime("today");

										for ($i=0; $i< $advanceDeliveryDays; $i++)
										{
											$currentTime = new Date('now +' . $i . 'day');
											$tabLinkValue = 'nav-link';
											$tabLiClass = 'nav-item';

											if ($i == 0)
											{
												$tabLinkValue = 'nav-link active';
												$tabHeader = Text::_('QTC_ORDER_DETAIL_SELECT_DELIVERY_SLOT_TODAY') . ' ' . HTMLHelper::_('date', $currentTime, 'j M');
											}
											elseif($i == 1)
											{
												$tabHeader = Text::_('QTC_ORDER_DETAIL_SELECT_DELIVERY_SLOT_TOMORROW') . ' ' . HTMLHelper::_('date', $currentTime, 'j M');
											}
											else
											{
												$tabHeader = HTMLHelper::_('date', $currentTime, 'j M');
											}
										?>
											<li class="<?php echo $tabLiClass;?>">
												<a class="<?php echo $tabLinkValue?>" data-bs-toggle="tab" href="<?php echo '#advanceDeliveryDate_' . HTMLHelper::_('date', $currentTime, 'j')?>">
													<?php echo $tabHeader;?>
												</a>
											</li>
										<?php
										}?>
									</ul>
									<div class="tab-content">
										<?php
										for ($j=0; $j< $advanceDeliveryDays; $j++)
										{
											$currentTime = new Date('now +' . $j . 'day');
											$deliverySlotHtml = $orderHelper->getDeliverySlotHtml(HTMLHelper::_('date', $currentTime, 'd-m-Y'));
											$tabContentclassValue = 'container tab-pane fade ';

											if ($j == 0)
											{
												$tabContentclassValue = 'container tab-pane active ';
											}
											?>
											<div id="<?php echo 'advanceDeliveryDate_' . HTMLHelper::_('date', $currentTime, 'j')?>" class="<?php echo $tabContentclassValue;?>">
												<p><?php echo $deliverySlotHtml;?></p>
											</div>
										<?php
										}?>
									</div>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-secondary" data-bs-dismiss="modal" title="<?php echo Text::_('QTC_ORDER_DETAIL_SELECT_DELIVERY_SLOT_CLOSE')?>">
										<?php echo Text::_('QTC_ORDER_DETAIL_SELECT_DELIVERY_SLOT_CLOSE');?>
									</button>
									<button type="button" class="btn btn-primary" data-bs-dismiss="modal" title="<?php echo Text::_('QTC_ORDER_DETAIL_SELECT_DELIVERY_SLOT_PROCEED_TO_PAYMENT')?>" onclick="getDeliverySlotDetails();">
										<?php echo Text::_('QTC_ORDER_DETAIL_SELECT_DELIVERY_SLOT_PROCEED_TO_PAYMENT');?>
									</button>
								</div>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			?>
			<div style="clear:both">&nbsp;</div>
			<div class="qtcPadding">
				<!-- show payment option start -->
				<?php
				// Getting gateways
				PluginHelper::importPlugin('payment');

				if (!is_array($params->get('gateways')))
				{
					$gatewayParam[] = $params->get('gateways');
				}
				else
				{
					$gatewayParam = $params->get('gateways');
				}

				// Get payment plugins info.
				if (!empty($gatewayParam))
				{
					if (isset($cart[0]['store_id']) && !empty($cart[0]['store_id']))

					// Getting email validation chceked for gateways and returns valid gateways
					$gateways = $quick2cartHelper->getValidGateways($gatewayParam, $cart[0]['store_id']);
				}

				$finalPaymentAmount = (float)$finalPaymentAmount;

				if (!empty($finalPaymentAmount))
				{
					if (count($gateways) > 1)
					{
						?>
						<div class="paymentHTMLWrapper well well-small" id="qtcPaymentGatewayList">
							<?php
							// START Q2C Sample development
							PluginHelper::importPlugin('system');

							// Call the plugin and get the result
							$result = $app->triggerEvent('onSystemBeforeDisplayingPaymentList', array($gateways, $cart));

							if (!empty($result[0]))
							{
								$gateways = $result[0];
							} ?>
							<div class="" id="qtc_paymentlistWrapper">
								<div class="form-group " id="qtc_paymentGatewayList">
									<div class="">
										<h4><?php echo Text::_('SEL_GATEWAY'); ?> </h4>
									</div>
									<div class="">
										<?php
										if (empty($gateways))
										{
											echo Text::_('NO_PAYMENT_GATEWAY');
										}
										else
										{
											foreach ($gateways as $gateway)
											{
												?>
												<div class="radio">
													<label>
														<input type="radio" name="gateways" onclick="processOrder()" id="qtc_<?php echo $gateway->id; ?>" value="<?php echo $gateway->id; ?>">
														<?php echo $gateway->name; ?>
													</label>
												</div>
												<?php
											}
										} ?>
									</div>
								</div>
							</div>
						</div>
						<div style="clear:both">&nbsp;</div>
					<?php
					}
					else
					{
						?>
						<div style="display:none;">
							<input
								type="radio"
								name="gateways"
								checked="checked"
								id="qtc_<?php echo $gateways[0]->id; ?>"
								value="<?php echo $gateways[0]->id; ?>">
								<?php echo $gateways[0]->name; ?>
						</div>
						<?php
					}
				}
				else
				{
					JLoader::import('components.com_quick2cart.controllers.cartcheckout', JPATH_SITE);
					$cartcheckoutController = new Quick2cartControllercartcheckout;
				}
				?>
				<!-- show payment option end -->
				<div>
					<?php
					if ($showTermsAndConditions)
					{
						?>
						<div class="checkbox checkout-addresses">
							<label>
								<input
									class="qtc_checkbox_style"
									type="checkbox"
									name="qtc_accpt_terms"
									id="qtc_accpt_terms"
									aria-invalid="false">
								<?php echo Text::_('COM_QUICK2CART_ACCEPT_CHECKOUT_TERMS_CONDITIONS_FIRST');
								
								?>
								<a data-bs-target="#privacyPolicyModal" data-bs-toggle="modal" class="qtc_modal" title="<?php echo Text::_('COM_QUICK2CART_TERMS_CONDITION'); ?>">
									<?php echo Text::_('COM_QUICK2CART_CHECKOUT_PRIVACY_POLICY');?>
								</a>
								
								<?php echo Text::_('COM_QUICK2CART_ACCEPT_CHECKOUT_TERMS_CONDITIONS_LAST'); ?>
							</label>
						</div>
						<?php
					}
					?>
				</div>
				<?php
				if ($finalPaymentAmount > 0)
				{
					$otpEnabled = $params->get('enable_otp', 0);
					?>
					<?php if($otpEnabled) { ?>
					<!-- OTP Verification UI Start -->
					<div class="qtcAddBorder p-4 mb-4">
						<h3 class="mb-4"><?php echo Text::_("COM_QUICK2CART_VERIFY_OTP_TITLE"); ?></h3>

						<div class="row mb-3">
							<div class="col-md-6">
								<label for="otp_input" class="form-label">
									<?php echo Text::_("COM_QUICK2CART_ENTER_OTP"); ?>
								</label>
								<input type="text" maxlength="6" id="otp_input" name="otp_input" class="form-control" placeholder="Enter OTP">
								<small class="text-muted"><?php echo Text::_("COM_QUICK2CART_OTP_SENT_NOTE"); ?></small>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 d-flex gap-2">
								<button class="btn btn-primary" type="button" onclick="confirmOtp()">
									<?php echo Text::_("COM_QUICK2CART_CONFIRM_OTP"); ?>
								</button>

								<button class="btn btn-outline-secondary" type="button" onclick="resendOtp()">
									<?php echo Text::_("COM_QUICK2CART_RESEND_OTP"); ?>
								</button>
							</div>
						</div>

						<div class="mt-3" id="otp_status_message"></div>
						<div id="otp-msg" class="fw-semibold" style="color:red; margin-top:10px;"></div>
					</div>
					<?php } ?>
					<!-- OTP Verification UI End -->
					<div>
						<button class="btn btn-large btn-success" id="qtc-co-place-order" onclick="processOrder()" type="button" style="display:none;">
							<?php echo Text::_("QTC_PAYMENT"); ?>
						</button>
						<span id="qtc-co-place-order-loader" class="hidden">
							<img src="<?php echo Uri::root() . 'components/com_quick2cart/assets/images/ajax.gif'; ?>">
						</span>
					</div>
					<?php
				}
				else
				{
					?>
					<div>
						<button class="btn btn-large btn-success" id="qtc-co-place-order" onclick="placeOrder(1)" type="button" style="display:none;">
							<?php echo Text::_("QTC_CONFORM_ORDER"); ?>
						</button>
						<span id="qtc-co-place-order-loader" class="hidden">
							<img src="<?php echo Uri::root() . 'components/com_quick2cart/assets/images/ajax.gif'; ?>">
						</span>
					</div>
					<?php
				}
				?>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	var otpVerifiedSuccess = "<?php echo Text::_('COM_QUICK2CART_OTP_VERIFIED_SUCCESS'); ?>";
	var otpVerifiedFail = "<?php echo Text::_('COM_QUICK2CART_OTP_VERIFIED_FAIL'); ?>";
	var otpResentSuccess = "<?php echo Text::_('COM_QUICK2CART_OTP_RESENT_SUCCESS'); ?>";
	var otpResentFail = "<?php echo Text::_('COM_QUICK2CART_OTP_RESENT_FAIL'); ?>";
	var otpEnabled = <?php echo (int)$otpEnabled; ?>;

	jQuery(document).ready(function() {
    if (!otpEnabled) {
        jQuery('#qtc-co-place-order').show();
    }})

	<?php if($otpEnabled) { ?>
	function confirmOtp() {
		var otp = jQuery('#otp_input').val();
		jQuery.ajax({
			url: root_url + 'index.php?option=com_quick2cart&task=cartcheckout.verifyOtp',
			type: 'POST',
			dataType: 'json',
			data: { otp: otp },
			success: function(response) {
				if (response && response.success) {
					jQuery('#qtc-co-place-order').show();
					jQuery('#otp-msg').hide();
					jQuery('#otp_status_message').html('<span class="text-success fw-semibold">' + otpVerifiedSuccess + '</span>');
					jQuery('#otp_input').val('');
				} else {
					jQuery('#qtc-co-place-order').hide();
					jQuery('#otp_status_message').html('<span class="text-danger fw-semibold">' + (response.message || otpVerifiedFail) + '</span>');
				}
			},
			error: function() {
				jQuery('#qtc-co-place-order').hide();
				jQuery('#otp_status_message').html('<span class="text-danger fw-semibold">' + otpVerifiedFail + '</span>');
			}
		});
	}

	function resendOtp() {
		jQuery.ajax({
			url: root_url + 'index.php?option=com_quick2cart&task=cartcheckout.generateOtp',
			type: 'POST',
			dataType: 'json',
			success: function(response) {
				if (response && response.success) {
					jQuery('#otp_input').val('');
					jQuery('#otp_status_message').html('<span class="text-success fw-semibold">' + otpResentSuccess + '</span>');
				} else {
					jQuery('#otp_status_message').html('<span class="text-danger fw-semibold">' + otpResentFail + '</span>');
				}
			},
			error: function() {
				jQuery('#otp_status_message').html('<span class="text-danger fw-semibold">' + otpResentFail + '</span>');
			}
		});
	}
	<?php } ?>
</script>