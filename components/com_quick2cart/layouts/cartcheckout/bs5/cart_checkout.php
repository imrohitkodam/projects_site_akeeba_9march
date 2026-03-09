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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

$data          = $displayData;
$input         = Factory::getApplication()->input;
$tmpl          = $input->get('tmpl');
$showoptioncol = (isset($data->showoptioncol)) ? $data->showoptioncol : 0;
$path          = JPATH_SITE . '/components/com_quick2cart/helpers/promotion.php';

if (!class_exists('PromotionHelper'))
{
	JLoader::register('PromotionHelper', $path);
	JLoader::load('PromotionHelper');
}

$PromotionHelper = new PromotionHelper;
$ccode           = isset($data->coupon)?$data->coupon : array() ;
$coupanexist     = empty($ccode)?0:1;

// TO use lanugage cont in javascript
Text::script('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_SUCCESS', true);
Text::script('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_FAIL', true);

?>
<div class="qtc_chekout_cartdetailWrapper  broadcast-expands" >
	<?php $align_style='align="right"'; ?>
	<div>
		<?php echo (!empty($this->beforecart))?$this->beforecart:""; ?>
	</div>

	<?php
	$comparams           = ComponentHelper::getParams('com_quick2cart');
	$currencies          = $comparams->get('addcurrency');
	$currencies_sym      = $comparams->get('addcurrency_sym');
	$comquick2cartHelper = new comquick2cartHelper;
	$default             = $comquick2cartHelper->getCurrencySession();
	$model               = new Quick2cartModelcart;

	// Load helper file
	JLoader::import('components.com_quick2cart.helpers.products', JPATH_SITE);
	$productHelper = new productHelper;
	$option = array();

	if ($currencies)
	{
	?>
		<div class="qtcChekoutCurrSelect">
			<br>
			<?php
			$multi_currs     = explode(",",$currencies);
			$currencies_syms = explode(",",$currencies_sym);

			foreach ($multi_currs as $key => $curr)
			{
				$currtext = $curr;

				if (!empty($currencies_syms[$key]) )
				{
					$currtext = $currencies_syms[$key];
				}

				$option[] = HTMLHelper::_('select.option', trim($curr), trim($currtext));
			}

			$cur_display = '';

			if (count($multi_currs) == 1)
			{
				$cur_display = 'style="display:none"';
			}
			?>
			<div <?php echo $cur_display;?> >
				<span><?php echo Text::_('QTC_SEL_CURR');?> </span>
				<?php echo HTMLHelper::_('select.genericlist',$option, "multi_curr", 'class="" onchange=" document.getElementById(\'task\').value=\'cartcheckout.setCookieCur\';document.adminForm.submit();" autocomplete="off" ', "value", "text", $default );?>
				<br><br>
			</div>
		</div>
		<div style="clear:both;"></div>
	<?php
	}

	$showqty_style = "";
	$showqty       = $comparams->get('qty_buynow', 1);

	if (empty($showqty))
	{
		$showqty_style = "display:none;";
	}
	?>
	<div class="table-responsive">
		<table class="table table-checkout qtc-table table-hover border table-bordered table-striped mt-2">
			<thead class="table-primary">
				<tr class="qtcborderedrow">
					<th class="cartitem_name text-start"" align="left">
						<b><?php echo Text::_( 'QTC_CART_TITLE' ); ?> </b>
					</th>
					<?php
					if ($showoptioncol == 1)
					{
					?>
						<th class="cartitem_opt text-start"" align="left">
							<b><?php echo Text::_( 'QTC_CART_OPTS' ); ?></b>
						</th>
					<?php
					}
					?>
					<th class="cartitem_price rightalign text-start">
						<b><?php echo Text::_( 'QTC_CART_PRICE' );?></b>
					</th>
					<th style="<?php echo $showqty_style; ?>" class="cartitem_qty rightalign text-start">
						<b><?php echo Text::_( 'QTC_CART_QTY' ); ?></b>
					</th>
					<th class="cartitem_tprice rightalign text-start" <?php echo $align_style ?>>
						<b><?php echo Text::_( 'QTC_CART_TOTAL_PRICE' ); ?> </b>
					</th>
					<th style="width:70px;"></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$tprice             = 0;
				$store_array        = array();
				$params             = ComponentHelper::getParams('com_quick2cart');
				$multivendor_enable = $params->get('multivendor');
				$storeHelper        = new storeHelper();

				foreach ($data->cart as $cart)
				{
					// IF MUTIVENDER ENDABLE
					if (!empty($multivendor_enable) && (!in_array($cart['store_id'], $store_array)))
					{
						$store_array[] = $cart['store_id'];
						$storeinfo     = $comquick2cartHelper->getSoreInfo($cart['store_id']);
						$storeLink     = $storeHelper->getStoreLink($cart['store_id']);
						?>
						<tr class="info">
							<td colspan="<?php echo (($showoptioncol == 1) ?"7" : "6" ); ?>" >
								<strong>
									<?php
										if ($tmpl === 'component')
										{
											echo $storeinfo['title'];
										}
										else
										{
											?>
											<a href="<?php echo $storeLink; ?>">
												<?php echo $storeinfo['title'];?>
											</a>
											<?php
										}
										?>
								</strong>
							</td>
						</tr>
						<?php
					}?>
					<?php
					$product_link = $comquick2cartHelper->getProductLink($cart['item_id']);
					?>
					<tr class="qtcborderedrow">
						<td class="cartitem_name" >
							<input
								class="inputbox cart_fields"
								id=""
								name="<?php echo 'cartDetail[' . $cart['id'] . '][cart_item_id]'; ?>"
								type="hidden"
								value="<?php echo $cart['id']; ?>" >

							<?php
							$images     = $cart['item_images'];
							$itemDetail = $model->getItemRec($cart['item_id']);
							$attributes = $productHelper->getItemCompleteAttrDetail($cart['item_id']);

							if (!empty($attributes))
							{
								$itemDetail->itemAttributes = $attributes;
							}

							$productInStock = $productHelper->isInStockProduct($itemDetail);

							if (empty($product_link) || $tmpl === 'component')
							{
								echo $cart['title'];
							}
							else
							{
								?>
								<a href="<?php echo $product_link;?>"><?php echo $cart['title']; ?></a>
								<?php
							}

							if (!$productInStock)
							{
								?>
								<div class="alert alert-warning alert-dismissible fade show" role="alert">
									<strong><?php echo Text::_('QTC_WARNING'); ?></strong>
									<?php echo Text::_('QTC_OUT_OF_STOCK_MSG'); ?>
									<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
								</div>
								<?php
							}
							?>
						</td>

						<?php
						if($showoptioncol==1)
						{
							?>
							<td class="cartitem_opt" >
								<?php
								if (!empty($cart['prodAttributeDetails']))
								{
									// seleted product attributes ids
									$product_attributes = explode(',', $cart['product_attributes']);

									// Show each product attribute
									foreach ($cart['prodAttributeDetails'] as $key=>$attribute)
									{
										?>
										<div class="qtc_bottom ">
											<span class=""><?php echo $attribute->itemattribute_name; ?></span>
											<input
												class=""
												id=""
												name="<?php echo 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][type]'; ?>"
												type="hidden"
												value="<?php echo $attribute->attributeFieldType ?>" >
											<?php
											// For text type attribute
											if (! empty($attribute->attributeFieldType) && $attribute->attributeFieldType == 'Textbox')
											{
												$itemattributeoption_id = 'new';
												if(isset($attribute->optionDetails[0]->itemattributeoption_id))
												{
													$itemattributeoption_id = $attribute->optionDetails[0]->itemattributeoption_id;
												}

												$value = isset($cart['product_attributes_values'][$attribute->optionDetails[0]->itemattributeoption_id]->cartitemattribute_name)?$cart['product_attributes_values'][$attribute->optionDetails[0]->itemattributeoption_id]->cartitemattribute_name:'';
												?>
												<br/>

												<input
													type="text"
													name="<?php echo 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][value]' ?>"
													class="input input-small"
													value ="<?php echo $value; ?>"/>
												<input
													type="hidden"
													name="<?php echo 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][itemattributeoption_id]' ?>"
													class="input input-small" 
													value ="<?php echo $itemattributeoption_id; ?>" />
											<?php
											}
											else
											{
												$attributeData = array();
												foreach ($attribute->optionDetails as $optionDetail)
												{
													if (in_array($optionDetail->itemattributeoption_id, $product_attributes))
													{
														$attributeData['default_value'] = $optionDetail->itemattributeoption_id;
														break;
													}
												}

												$productHelper                         = new productHelper();
												$attributeData['itemattribute_id']     = $attribute->itemattribute_id;
												$attributeData['fieldType']            = $attribute->attributeFieldType;
												$attributeData['cart_id']              = $cart['id'];
												$attributeData['product_id']           = $cart['item_id'];
												$attributeData['attribute_compulsary'] = $attribute->attribute_compulsary;

												if (!empty($attributeData['default_value']))
												{
													$attrDetailsObject = $cart['product_attributes_values'][$attributeData['default_value']];
												}

												$attributeData['field_name'] = 'cartDetail[' . $cart['id'] . '][attrDetail][' . $attribute->itemattribute_id . '][value]';

												// Generate field html (select box)
												$fieldHtml = $productHelper->getAttrFieldTypeHtml($attributeData);
												echo $fieldHtml;
											}
											?>
										</div>
										<div class="qtcClearBoth">&nbsp;</div>
									<?php
									}
								}?>
							</td>
							<?php
						}
						?>

						<td class="cartitem_price rightalign" id="cart_price" name="cart_price[]">
							<div>
								<?php $original_prod_price = $pro_price = $cart['amt'] + $cart['opt_amt'];
								echo $comquick2cartHelper->getFromattedPrice(number_format($original_prod_price,2));?>
							</div>
						</td>
						<td style="<?php echo $showqty_style; ?>" class="cartitem_qty rightalign" >
							<?php
							$minmax          = $comquick2cartHelper->getMinMax($cart['item_id']);
							$minmsg          = Text::_( 'QTC_MIN_LIMIT_MSG' );
							$maxmsg          = Text::_( 'QTC_MAX_LIMIT_MSG' );
							$qtc_min         = isset($minmax['min_quantity'])?$minmax['min_quantity']:1;
							$qtc_max         = isset($minmax['max_quantity'])?$minmax['max_quantity']:999;
							$caltotal_params = "'".$cart['id'] ."',".$cart['amt'].",".$qtc_min.",".$qtc_max.",'".$minmsg."','".$maxmsg."'";
							?>
							<input
								type ="hidden"
								id="quantity_parmas_<?php echo $cart['id'];?>"
								value="<?php echo $caltotal_params ;?> " />
							<input
								id ="quantity_field_<?php echo $cart['id'];?>"
								class="cart_fields pull-right float-end input qtc-input-small"
								id="cart_count"
								name="<?php echo 'cartDetail[' . $cart['id'] . '][cart_count]' ?>"
								type="number"
								step="<?php echo $minmax['slab'];?>"
								value="<?php echo $cart['qty'];?>"
								maxlength="3"
								onchange="updateCartItemsAttribute('<?php echo $cart['id'];?>', '<?php echo $cart['item_id']; ?>')">
						</td>
						<td class="cartitem_tprice rightalign" <?php //echo $align_style ?> >
							<span id="cart_total_price<?php echo $cart['id'];?>">
								<?php echo $comquick2cartHelper->getFromattedPrice(number_format(($pro_price * $cart['qty']) ,2));  ?>
							</span>
							<?php $tprice = $tprice + ($pro_price * $cart['qty']);?>
						</td>
						<td>
							<div class="qtc_float_right">
								<span class="qtcHandPointer" onclick="removecart('<?php echo $cart['id'];?>');" >
									<span class="qtcHandPointer qtcUpdateItemImg <?php echo QTC_ICON_REMOVE; ?>"   title="<?php echo Text::_('QTC_CKOUT_REMVOVE_FROM_CART'); ?>" onclick="updateCartItemsAttribute('<?php echo $cart['id'];?>', '<?php echo $cart['item_id']; ?>')"></span>
								</span>
							</div>
						</td>
					</tr>
				<?php
				}

				$totalprice = $tprice;?>
				<tr class="qtcborderedrow highlightedrow">
					<?php $col = ($showoptioncol == 1) ? 3 : 2;?>
					<?php $msg_order_js = "'" . Text::_('QTC_CART_EMPTY_CONFIRMATION') . "','".Text::_('QTC_CART_EMPTIED') . "'";?>
					<td colspan = "<?php echo $col; ?>">
						<div class = "form-inline">
							<input
								type="checkbox"
								id = "coupon_chk"
								autocomplete="off"
								name = "coupon_chk"
								value=""
								size= "10"
								onchange="show_cop(<?php echo $coupanexist; ?>)" <?php echo ($ccode) ? 'checked' : '' ; ?>  />
							<label class="checkbox-inline">
								<?php echo Text::_('QTC_HAVE_COP');?>
							</label>
							<span id = "cop_tr" class="col-md-6">
								<span class="input-group">
									<input
										type="text"
										class="form-control-sm border-1 input input-medium"
										id = "coupon_code"
										name="cop"
										value="<?php echo $ccode ?>"
										placeholder="<?php echo Text::_('QTC_CUPCODE');?>"/>
									<span class="input-group-btn">
										<input type="button"  class="btn btn-sm btn-primary"  onclick="cart_applycoupon('<?php echo Text::_('QTC_ENTER_COP_COD')?>','#coupon_code')" value="<?php echo Text::_('QTC_APPLY');?>" >
									</span>
								</span>
							</span>
						</div>
					</td>
					<td  class="cartitem_tprice_label rightalign <?php echo !empty($showIfDiscountPresent) ? $showIfDiscountPresent : '' ; ?>" >
						<strong><?php echo Text::_( 'QTC_TOTALPRICE' ); ?></strong>
					</td>
					<td class="cartitem_tprice rightalign" >
						<strong>
							<span name="total_amt" id="total_amt">
								<?php echo $comquick2cartHelper->getFromattedPrice(number_format($totalprice,2)); ?>
							</span>
						</strong>
					</td>
					<td></td>
				</tr>
				<?php
				$col = ($showoptioncol == 1) ? 3 : 2;
				$maximumDiscount       = 0;
				$maxDisPromo           = array();
				$showIfDiscountPresent = "q2c-display-none";

				if (!empty($data->promotions) && !empty($data->promotions->maxDisPromo))
				{
					$maxDisPromo = $data->promotions->maxDisPromo;
					$showIfDiscountPresent = "";
				}

				if (!empty($maxDisPromo))
				{
					$tprice = $tprice - $maxDisPromo->applicableMaxDiscount;
					?>
					<tr class="dis_tr qtcborderedrow highlightedrow" >
						<td colspan = "<?php echo ($col + 1);?>" class="cartitem_tprice_label rightalign">
							<div>
								<strong><?php echo Text::_('COM_QUICK2CART_PROMOTION_DICOUNT');?></strong>
							</div>
							(
								<?php
								if (!empty($ccode))
								{
									?>
									<small>
										<strong><?php echo Text::_('QTC_DISCOUNT_CODE') . " : " . $ccode . " "; ?></strong>
									</small>
									<?php
								}
								?>
								<span class="promDicountTitle">
									<small>
										<?php echo $maxDisPromo->name ?>
									</small>
								</span>
							)
						</td>
						<td class="cartitem_tprice rightalign"  >
							<strong>
								<span id= "dis_cop" >
									<?php echo $comquick2cartHelper->getFromattedPrice(number_format($maxDisPromo->	applicableMaxDiscount,2)); ?>
								</span>
							</strong>
						</td>
						<td></td>
					</tr>

				<?php
				}

				$col = ($showoptioncol == 1) ? 3 : 2;
				?>
				<tr class="dis_tr qtcborderedrow highlightedrow <?php echo !empty($showIfDiscountPresent) ? $showIfDiscountPresent : '' ;  ?>"   >
					<td colspan = "<?php echo $col;?>"></td>
					<td class="cartitem_tprice_label rightalign">
						<strong><?php echo Text::_('QTC_NET_AMT_PAY');?></strong>
					</td>
					<td class="cartitem_tprice rightalign">
						<strong>
							<span id= "dis_amt">
								<?php echo $comquick2cartHelper->getFromattedPrice(number_format($tprice,2)); ?>
							</span>
						</strong>
					</td>
					<td></td>
				</tr>
				<?php
				// taxation plugin
				PluginHelper::importPlugin('qtctax');//@TODO:need to check plugim type..
				$taxresults = new stdClass;
				$taxval     = $tprice;

				if (!empty($taxresults))
				{
					$tax_total = 0;

					foreach ($taxresults as $tax)
					{
						if(!empty($tax))
						{
							$col = ($showoptioncol == 1) ? 3 : 2;?>
							<tr class=" qtcborderedrow highlightedrow">
								<td colspan = "<?php echo $col;?>"></td>
								<td class="cartitem_tprice_label rightalign">
									<?php echo Text::sprintf('QTC_TAX_AMT_PAY',$tax[0]);?>
								</td>
								<td class="cartitem_tprice rightalign">
									<span id= "tax_amt" >
										<?php echo $comquick2cartHelper->getFromattedPrice(number_format($tax[1],2)); ?>
									</span>
									<input type="hidden" class="inputbox" value="<?php echo $tax[0]; ?>" name="tax[val][]" id="tax[val][]">
									<input type="hidden" class="inputbox" value="<?php echo $tax[1]; ?>" name="tax[amt][]" id="tax[amt][]">
								</td>
								<td></td>
							</tr>
							<?php
							$tax_total += $tax[1];
						}
					}

					$taxval = $tprice;

					if ($tax_total)
					{
						$taxval = $comquick2cartHelper->calamt($tprice,$tax_total);
						?>
							<tr class=" qtcborderedrow highlightedrow">
								<td colspan= "<?php echo $col;?>"></td>
								<td class="cartitem_tprice_label rightalign">
									<?php echo Text::_('QTC_TAX_TOTAL_AMT_PAY');?>
								</td>
								<td class="cartitem_tprice rightalign" >
									<span id= "after_tax_amt">
										<?php echo $comquick2cartHelper->getFromattedPrice(number_format($taxval,2)); ?>
									</span>
								</td>
								<td></td>
							</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
	</div>
		<?php
			$userId = Factory::getUser()->id;

			JLoader::register('Quick2cartModelPromotions', JPATH_SITE . '/components/com_quick2cart/models/promotions.php');
			$helper = new Quick2cartModelPromotions;

			$hasVisiblePromotion = false;

			$applicablePromotions = $data->applicablePromotionsList ?? [];

			// Create map of eligible users per promotion
			$promoIds = is_array($applicablePromotions) ? array_map(function ($p) { return $p->id; }, $applicablePromotions) : [];

			$eligibleUsersMap = $helper->getEligibleUsersForPromotions($promoIds);

			// Check if any promotion is viewable to current user
			foreach ($applicablePromotions as $promotion)
			{
				$isSpecific = (int) $promotion->allowspecificuserpromotion === 1;
				$isUserEligible = in_array($userId, $eligibleUsersMap[$promotion->id]['users'] ?? []);

				if (!$isSpecific || ($isSpecific && $isUserEligible))
				{
					$hasVisiblePromotion = true;
					break;
				}
			}

			if ($hasVisiblePromotion)
			{
			?>
			<h4 class="mt-3 mb-3"><?php echo Text::_("COM_QUICK2CART_AVAILABLE_OFFERS"); ?></h4>
			<div class="table-responsive">
				<table class="table table-bordered table-striped table-hover border">
					<thead class="table-primary border table-bordered">
						<tr>
							<th class="fw-bold"><?php echo Text::_("QTC_COUPON_NAME"); ?></th>
							<th class="fw-bold"><?php echo Text::_("QTC_CUPCODE"); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						foreach ($data->applicablePromotionsList as $promotion)
						{
							$quantity = $promotion->quantity; 
							$operation = isset($promotion->operation) ? $promotion->operation : '';
						?>
						<tr>
							<td>
								<?php
								echo $promotion->name;
								if (!empty($promotion->description)) {
								echo " [ " . $promotion->description . " ] ";
								}
								?>
							</td>
							<td>
								<?php 
								if ($promotion->coupon_required == '1' && !empty($promotion->coupon_code))
								{
									echo '<h6><span class="text-warning font-weight-bold">' . $promotion->coupon_code . '</span></h6>';
								} 
								else
								{
									echo "-";
								}
								?>
							</td>
						</tr>
						<?php 
						}
						?>
					</tbody>
				</table>
			</div>
		<?php 
		}
	?>
	<div>
		<input
			type="hidden"
			class="inputbox"
			value="<?php echo $taxval; ?>"
			name="total_after_tax"
			id="total_after_tax">
		<?php echo (!empty($this->aftercart))?$this->aftercart:'';?>
	</div>
	<?php
		$jinput  = Factory::getApplication();
		$baseUrl = $jinput->input->server->get('REQUEST_URI', '', 'STRING');
	?>
	<button type="button" class="btn btn-danger btn-sm" onclick="emptycart(<?php echo $msg_order_js . ",'" . $baseUrl."'"; ?>);" >
		<i class="<?php echo Q2C_ICON_TRASH; ?> <?php echo Q2C_ICON_WHITECOLOR; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>&nbsp;<?php echo Text::_('QTC_BTN_EMPTY_CART')?>
	</button>
</div>
