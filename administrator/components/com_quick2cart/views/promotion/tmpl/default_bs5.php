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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal');

Text::script('COM_QUICK2CART_PROMOTION_CONDITION_MSG', true);
Text::script('COM_QUICK2CART_DISCOUNT_VALUE_ERROR', true);
Text::script('COM_QUICK2CART_PROMOTION_CONDITION_IN', true);
Text::script('COM_QUICK2CART_PROMOTION_PRODUCT_QUANTITY_TEXT', true);
Text::script('COM_QUICK2CART_PROMOTION_CONDITION_REMOVE_QUANTITY_INFO', true);
Text::script('COM_QUICK2CART_DATES_INVALID', true);
Text::script('COM_QUICK2CART_USES_INVALID', true);
Text::script('JGLOBAL_VALIDATION_FORM_FAILED', true);
Text::script('COM_QUICK2CART_PROMOTION_CONDITION_IS', true);
Text::script('QTC_ENTER_NUMERICS', true);
Text::script('COM_QUICK2CART_REMOVE_OPTION', true);

$comquick2cartHelper = new comquick2cartHelper;
$storeHelper         = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
$storeList           = (array) $storeHelper->getStoreList();

// Get currencies
$currencies = $this->params->get('addcurrency');
$currency   = explode(',', $currencies);
$entered_numerics= "'".Text::_('QTC_ENTER_NUMERICS')."'";

if (empty($this->storeList))
{
?>
	<div class="alert alert-error">
		<?php echo Text::_("COM_QUICK2CART_CREATE_ORDER_AUTHORIZATION_ERROR");?>
	</div>
<?php
	return false;
}

// Get list of stores in array
foreach ($this->storeList as $key => $value)
{
	$value     = (array)$value;
	$options[] = HTMLHelper::_('select.option', $value["id"],$value['title']);
}

// Get list of types of discounts in array
foreach ($this->discount_type as $key => $value)
{
	$discount_types[] = HTMLHelper::_('select.option', $key, $value);
}

// Get list of condition types in array
foreach ($this->condition_type as $key => $value)
{
	$condition_type[] = HTMLHelper::_('select.option', $key, $value);
}
?>
<script>
	var conditionCnt    = <?php echo !empty($this->conditionMaxCount)? $this->conditionMaxCount:0;?>;
	var conditionCount  = (Number(conditionCnt)+1);
	var discountError = false;
	var selectedConditionCategory = 0;
	var storeId = '';

	Joomla.submitbutton = function (task)
	{
		if (task != 'promotion.cancel')
		{
			discountError = false;
			if (techjoomla.jQuery(".qtc-promotion-condition-div-clone").length < 2)
			{
				alert(Joomla.Text._("COM_QUICK2CART_PROMOTION_CONDITION_MSG"));
				return false;
			}

			if (jQuery('#jform_discount_type').val() == 'flat') {
				jQuery(".qtc_currency_price_discount.required").each(function( index ) {
					if (techjoomla.jQuery(this).val() <= 0)
					{
						alert(Joomla.Text._("COM_QUICK2CART_DISCOUNT_VALUE_ERROR"));
						discountError = true;
					}
				});
			}

			if (jQuery('#jform_discount_type').val() == 'percentage') {
				if (techjoomla.jQuery(".currtext").val() <= 0)
				{
					alert(Joomla.Text._("COM_QUICK2CART_DISCOUNT_VALUE_ERROR"));
					discountError = true;
				}
			}
		}

		if (discountError) {
			return false;
		}

		if (task == 'promotion.cancel')
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else
		{
			if (document.getElementById('jform_from_date').value !== "" && document.getElementById('jform_exp_date').value !== "")
			{
				if (document.getElementById('jform_from_date').value > document.getElementById('jform_exp_date').value)
				{
					alert(Joomla.Text._("COM_QUICK2CART_DATES_INVALID"));
					return false;
				}
			}

			if (document.getElementById('jform_max_use') !== null && document.getElementById('jform_max_per_user') !== null)
			{
				if (Number(document.getElementById('jform_max_use').value) < Number(document.getElementById('jform_max_per_user').value))
				{
					alert(Joomla.Text._("COM_QUICK2CART_USES_INVALID"));
					return false;
				}
			}

			if (task != 'promotion.cancel' && document.formvalidator.isValid(document.getElementById('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
			else
			{
				alert(Joomla.Text._('JGLOBAL_VALIDATION_FORM_FAILED'));
			}
		}
	}
</script>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?> my_promotions bs5Loaded">
	<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate">
		<div class="form-horizontal">
			<?php
			echo HTMLHelper::_('uitab.startTabSet', 'promotions', ['active' => 'promotionrules', 'recall' => true, 'breakpoint' => 768]);
				echo HTMLHelper::_('uitab.addTab', 'promotions', 'promotionrules', Text::_('COM_QUICK2CART_TITLE_PROMOTION', true)); ?>
					<div class="row-fluid">
						<div class="span10 form-horizontal">
							<fieldset class="adminform">
								<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
								<div class="control-group">
									<label for="store_id" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_STORE_ID');?>">
										<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_STORE_ID'), '', Text::_('COM_QUICK2CART_FORM_LBL_PROMOTION_STORE_ID'));?>
									</label>
									<div class="controls">
										<?php echo HTMLHelper::_('select.genericlist', $options,'jform[store_id]','class="form-select"','value','text',$this->item->store_id,'jform_store_id');?>
									</div>
								</div>
								<?php echo $this->form->renderField('name'); ?>
								<?php echo $this->form->renderField('description'); ?>
								<div class="control-group">
									<label for="state" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_PUBLISHED');?>">
										<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_PUBLISHED'), '', Text::_('COM_QUICK2CART_FORM_LBL_PROMOTION_PUBLISHED'));?>
									</label>
									<div class="controls">
										<div class="radio qtc-radio-button">
											<input
												class="btn valid form-control-success"
												type="radio"
												id="state1"
												name="jform[state]" <?php echo empty($this->item->state)?'':'checked="checked"';?>
												value="1"/>
											<label for="state1"><?php echo Text::_("JYES");?></label>
											<input
												class="btn valid form-control-success"
												type="radio" <?php echo empty($this->item->state)?'checked="checked"':'';?>
												id="state0"
												name="jform[state]"
												value="0"/>
											<label for="state0"><?php echo Text::_("JNO");?></label>
										</div>
									</div>
								</div>
								<?php echo $this->form->renderField('from_date'); ?>
								<?php echo $this->form->renderField('exp_date'); ?>
								<?php echo $this->form->renderField('allowspecificuserpromotion'); ?>
								<?php echo $this->form->renderField('orderamount'); ?>
								<div class="control-group">
									<label for="discount_type" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_COUPON_REQUIRED');?>">
										<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_COUPON_REQUIRED'), '', Text::_('COM_QUICK2CART_FORM_PROMOTION_COUPON_REQUIRED'));?>
									</label>
									<div class="controls">
										<div class="radio qtc-radio-button">
											<?php $coupon_required = $this->item->coupon_required?>
											<input
												class="btn"
												type="radio"
												id="coupon_required1"
												name="jform[coupon_required]" <?php echo empty($this->item->coupon_required)?'':'checked="checked"';?>
												value="1"/>
											<label for="coupon_required1"><?php echo Text::_("JYES");?></label>
											<input
												class="btn"
												type="radio" <?php echo empty($this->item->coupon_required)?'checked="checked"':'';?>
												id="coupon_required0"
												name="jform[coupon_required]"
												value="0"/>
											<label for="coupon_required0"><?php echo Text::_("JNO");?></label>
										</div>
									</div>
								</div>
								<div class="control-group">
									<label for="discount_type" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_VAL_TYPE');?>">
										<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_VAL_TYPE'), '', Text::_('COM_QUICK2CART_FORM_LBL_PROMOTION_VAL_TYPE'));?>
									</label>
									<div class="controls">
										<?php echo HTMLHelper::_('select.genericlist', $discount_types,'jform[discount_type]','class="form-select"','value','text',$this->item->discount_type,'jform_discount_type');?>
									</div>
								</div>
								<div class="control-group">
									<label for="discount<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_VALUE')?>">
										<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_VALUE'), '', Text::_('COM_QUICK2CART_FORM_LBL_PROMOTION_VALUE'));?>
										<span class="star">&nbsp;*</span>
									</label>
									<div class="controls">
										<div class="qtc-promotion-flat-discount-div">
											<?php
											foreach ($currency as $key => $value)
											{
												if (!empty($this->discount))
												{
													foreach ($this->discount as $discount)
													{
														if ($discount['currency'] == $value)
														{
															$dicountAmount = !empty($discount['discount'])?$discount['discount']:'';
														}
													}
												}

												$currsymbol = $comquick2cartHelper->getCurrencySymbol($value);
												?>
												<div>
													<div class="input-group curr_margin">
														<?php
														if (count($currency) > 1)
														{ ?>
															<label for="qtc_discount<?php echo trim($value);?>" class="control-label qtc_currency_price_discount_lbl" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_VALUE');?>">
																<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_VALUE'), '', Text::_('COM_QUICK2CART_PRICE_DISCOUNT') . ' ' . Text::_('COM_QUICK2CART_PRICE_IN') . ' ' . trim($currsymbol));?>
															</label>
														<?php
														} ?>
														<input
															class="qtc_currency_price_discount required form-control"
															id="qtc_discount<?php echo trim($value);?>"
															type="number"
															required="required"
															name="qtc_discount[flat][<?php echo trim($value);?>]"
															value="<?php echo !empty($dicountAmount)?$dicountAmount:'0';?>"
															Onkeyup= "checkforalpha(this,46,<?php echo $entered_numerics; ?>);"
															placeholder="<?php echo trim($currsymbol);?>" />
														<span class="input-group-addon input-group-text"><?php echo $currsymbol;?></span>
														<div class="qtcClearBoth"></div>
													</div>
												</div>
											<?php
											}
											?>
										</div>
										<div class="qtc-promotion-percent-discount-div">
											<input
												type="number"
												name="qtc_discount[percent]"
												class="form-control currtext"
												Onkeyup= "checkforalpha(this,46,<?php echo $entered_numerics; ?>);"
												value="<?php echo !empty($this->discount[0]['discount'])?$this->discount[0]['discount']:'0';?>"
												class="form-control"/>
										</div>
									</div>
								</div>
								<div class="qtc-promotion-discount-type-dependent">
									<div class="control-group">
										<label for="discount<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_MAX_DISCOUNTS');?>">
											<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_MAX_DISCOUNTS'), '', Text::_('COM_QUICK2CART_FORM_LBL_PROMOTION_MAX_DISCOUNTS'));?>
										</label>
										<div class="controls">
										<?php
										foreach ($currency as $key => $value)
										{
											if (!empty($this->discount))
											{
												foreach ($this->discount as $discount)
												{
													if ($discount['currency'] == $value)
													{
														$maxDicountAmount = !empty($discount['max_discount'])?$discount['max_discount']:'';
													}
												}
											}

											$currsymbol = $comquick2cartHelper->getCurrencySymbol($value);

											if (count($currency) > 1)
											{
											?>
												<div class="input-group curr_margin">
													<?php if (count($currency) > 1) : ?>
													<label for="multi_cur<?php echo trim($value);?>" class="control-label qtc_currency_price_discount_lbl" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_MAX_DISCOUNTS')?>">
														<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_MAX_DISCOUNTS'), '', Text::_('COM_QUICK2CART_MAX_PRICE_DISCOUNT') . ' ' . Text::_('COM_QUICK2CART_PRICE_IN') . ' ' . trim($currsymbol));?>
													</label>
													<?php endif; ?>
													<input
														class="qtc_currency_price_discount form-control"
														id="multi_cur<?php echo trim($value);?>"
														type="number"
														name="multi_cur[<?php echo trim($value);?>]"
														value="<?php echo $maxDicountAmount;?>"
														Onkeyup= "checkforalpha(this,46,<?php echo $entered_numerics; ?>);"
														placeholder="<?php echo trim($currsymbol);?>" />
													<span class="input-group-addon input-group-text"><?php echo $currsymbol;?></span>
													<div class="qtcClearBoth"></div>
												</div>
											<?php
											}
											else
											{
											?>
												<div class="input-group curr_margin">
													<input
														class="required qtc_requiredoption form-control"
														id="price_<?php echo trim($value);?>"
														type="number"
														Onkeyup= "checkforalpha(this,46,<?php echo $entered_numerics; ?>);"
														name="multi_cur[<?php echo trim($value);?>]"
														value="<?php echo !empty($maxDicountAmount)?$maxDicountAmount:'0';?>"
														placeholder="<?php echo trim($currsymbol);?>" />
													<span class="input-group-addon input-group-text"><?php echo $currsymbol;?></span>
												</div>
												<div class="qtcClearBoth"></div>
											<?php
											}
										}
										?>
										</div>
									</div>
								</div>
								<div class="qtc-promotion-coupon-dependent">
									<div class ="condition-req"><?php echo $this->form->renderField('coupon_code'); ?></div>
									<?php echo $this->form->renderField('max_use'); ?>
									<?php echo $this->form->renderField('max_per_user'); ?>
									<div class="control-group">
										<label for="discount_type" class="control-label" title="<?php echo Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_CATLOG_PROMOTION')?>">
											<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_CATLOG_PROMOTION'), '', Text::_('COM_QUICK2CART_FORM_PROMOTION_CATLOG_PROMOTION'));?>
										</label>
										<div class="controls">
											<div class="radio qtc-radio-button">
												<?php $catlog_promotion = $this->item->catlog_promotion?>
												<input class="btn" type="radio" id="catlog_promotion1" name="jform[catlog_promotion]" <?php echo empty($this->item->catlog_promotion)?'':'checked="checked"';?> value="1"/>
												<label for="catlog_promotion1"><?php echo Text::_("JYES");?></label>
												<input class="btn" type="radio" <?php echo empty($this->item->catlog_promotion)?'checked="checked"':'';?> id="catlog_promotion0" name="jform[catlog_promotion]" value="0"/>
												<label for="catlog_promotion0"><?php echo Text::_("JNO");?></label>
											</div>
										</div>
									</div>
								</div>
							</fieldset>
						</div>
					</div>
			<?php echo HTMLHelper::_('uitab.endTab'); ?>
			<!--Div to be cloned-starts -->
			<div class="row-fluid">
				<div id="qtc-promotion-condition-div-primary" class="qtc-promotion-condition-text col-md-12 mt-2">
					<div id="qtc-promotion-condition-div-cloneprimary" class="qtc-promotion-condition-div-clone mt-2" style="display:none;">
						<div class="row mt-5">
							<div class="col-md-2">
								<input type="hidden"class="input-small" name="rule[conditions][primary][id]">
								<span><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_IF");?></span>
							</div>
							<div class="col-md-2">
								<input type="hidden" name="rule[conditions][primary][condition_on]" />
								<select id="conditions_onprimary" name="rule[conditions][primary][condition_on_attribute]" class="qtc-promotion-margin q2c-inline form-select" onchange="checkOperation('primary')">
									<optgroup label="<?php echo Text::_("COM_QUICK2CART_ADD_CONDITION_INFO");?>" disabled></optgroup>
									<optgroup label="<?php echo Text::_("COM_QUICK2CART_PRODUCT");?>">
										<option value="category"><?php echo Text::_("COM_QUICK2CART_CAT");?></option>
										<option value="item_id"><?php echo Text::_("COM_QUICK2CART_PRODUCT");?></option>
									</optgroup>
									<optgroup label="<?php echo Text::_('COM_QUICK2CART_CONDITION_ON_CART');?>">
										<option value="cart_amount"><?php echo Text::_("COM_QUICK2CART_CONDITION_ON_CART_TOTAL_AMOUNT");?></option>
										<option value="quantity_in_store_cart"><?php echo Text::_("COM_QUICK2CART_CONDITION_ON_TOTAL_QUANTITY_IN_CART");?></option>
									</optgroup>
									<optgroup label="<?php echo Text::_('COM_QUICK2CART_CONDITION_ON_USER');?>">
										<option value="user_group"><?php echo Text::_("COM_QUICK2CART_CONDITION_ON_USER_GROUP");?></option>
									</optgroup>
								</select>
							</div>
							<div class="col-md-2">
								<span id="conditions_operation_wrapperprimary">
									<div class="row">
										<div class="col-md-3">
											<span><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_IS");?></span>
										</div>
										<div class="col-md-9">
											<span id="conditions_operation_divprimary">
												<select id="conditions_operationprimary" name="rule[conditions][primary][operation]" class="qtc-promotion-margin form-select">
													<option value="="><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_EQUAL_TO");?></option>
													<option value="<"><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_LESS_THAN");?></option>
													<option value=">"><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_GREATER_THAN");?></option>
													<option value=">="><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_GREATER_THAN_EQUALTO");?></option>
													<option value="<="><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_LESS_THAN_EQUALTO");?></option>
												</select>
											</span>
										</div>
									</div>
								</span>
							</div>
							<div class="col-md-2">
								<span id="currency_wrapperprimary">
									<span id="currency_divprimary"></span>
								</span>
							</div>
							<div class="col-md-2">
								<span id="attribute_value_wrapper1">
									<span id="attribute_value_div1"></span>
								</span>
							</div>
							<div class="col-md-2">
								<span id="qtc-remove-conditionprimary">
									<a class="qtcHandPointer" onclick="qtcRemovePromotionCondition('primary');" title="<?php echo Text::_('COM_Q2C_REMOVE_TOOLTIP');?>">
										<button type="button" class="group-add btn btn-sm btn-danger" aria-label="<?php echo Text::_('COM_QUICK2CART_REMOVE_OPTION');?>"><i class="fa fa-minus"></i> </button>
									</a>
								</span>
							</div>
							<div class="col-md-12 mb-2 mt-2 promotion">
								<!-- Div for quantity condition-start -->
								<div id="qtc-add-quantity-condition-divprimary">
									<div id="qtc-add-quantity-conditionprimary" class="qtc-add-quantity-condition-padding-left g-2 row mt-2 mb-2 pro ">
										<a class="qtcHandPointer ms-3" onclick="qtcAddQuantityCondition('primary');">
											<button type="button" class="group-add btn btn-sm btn-success" aria-label="<?php echo Text::_('COM_QUICK2CART_ADD_QUANTITY_CONDITION');?>"><i class="fa fa-plus"></i> </button>
										</a>
									</div>
								</div>
							</div>
						</div>
						<!-- Div for quantity condition-end -->
					</div>
				</div>

				
			</div>
			<!--Div to be cloned-ends-->

			<!--Div for attribute value to be cloned - starts-->
			<div class="row-fluid">
				<span id="qtc-condition-attribute-value-primary-div">
					<span id="qtc-condition-attribute-value-primary-divclone" class="input-group" style="display:none;">
						<input
							type="text"
							name="rule[conditions][primary][condition_attribute_value]"
							id="rule_conditions_primary_condition_attribute_value"
							class="qtc_condition_attribute_value qtc-promotion-margin form-control"
							readonly="true" />
						<label id="rule_conditions_primary_condition_attribute_value_id" for="rule_conditions_primary_condition_attribute_value" class="hidden">
							<?php echo Text::_("COM_QUICK2CART_CONDITION_VALUE_LABEL");?>
						</label>
						<a class="qtcHandPointer input-group-text openSelectorWithModule" id="rule_conditions_attribute_selectprimary" title="<?php echo Text::_('COM_QUICK2CART_PROMOTION_CONDITION_SELECT')?>">
							<img src="<?php echo Uri::root();?>components/com_quick2cart/assets/images/magnifier.png">
						</a>
					</span>
				</span>
			</div>

			<!--Div for attribute value to be cloned - ends-->

			<!--Currency div clone starts-->
			<?php
			if (!empty($currency))
			{
			?>
				<div class="row-fluid">
					<span id="qtc-cart-amount-currency-primary-div">
						<span id="qtc-cart-amount-currency-primary-divclone" style="display:none;">
						<?php
						foreach ($currency as $key => $value)
						{
							if (!empty($this->discount))
							{
								foreach ($this->discount as $discount)
								{
									if ($discount['currency'] == $value)
									{
										$dicountAmount = !empty($discount['discount'])?$discount['discount']:'';
									}
								}
							}

							$currsymbol = $comquick2cartHelper->getCurrencySymbol($value);
							?>
							<span class="input-group curr_margin qtc-promotion-margin">
								<input class="qtc_currency_price_discount form-control"
									type="number"
									id="conditions_primary_currency_value_<?php echo $value;?>"
									name="rule[conditions][primary][condition_attribute_value][<?php echo $value;?>]"
									placeholder="<?php echo trim($currsymbol);?>" 
									Onkeyup= "checkforalpha(this,46,<?php echo $entered_numerics; ?>);"
									class="form-control"/>
								<span class="input-group-addon input-group-text"><?php echo $currsymbol;?></span>
								<div class="qtcClearBoth"></div>
							</span>
							<?php
						}
						?>
						</span>
					</span>
				</div>
				<?php
			}
			?>

			<!--Currency div clone ends-->
			<?php echo HTMLHelper::_('uitab.addTab', 'promotions', 'conditions', Text::_('COM_QUICK2CART_FORM_DESC_PROMOTION_CONDITION', true)); ?>
			<div class="row">
				<h3 class="d-flex align-items-center"><?php echo Text::_("COM_QUICK2CART_CONDITION_INFO1");?>
					<span id="qtc-promotion-condition-compulsory" class="ms-2 fs-8">
						<?php echo HTMLHelper::_('select.genericlist', $condition_type,'conditions_compulsory','','value','text',(!empty($this->conditionList[0]->is_compulsary)?$this->conditionList[0]->is_compulsary:''),'conditions_compulsory');?>
					</span>
					&nbsp;
					<?php echo Text::_("COM_QUICK2CART_CONDITION_INFO2");?>
				</h3>
				<div id="qtc-promotion-condition-div" class="qtc-promotion-condition-text row">
					<?php
					if ($this->item->id && (!empty($this->conditionList)))
					{
						foreach ($this->conditionList as $key => $condition)
						{
							?>
						<div id="qtc-promotion-condition-div-clone<?php echo $condition->id;?>" class="qtc-promotion-condition-div-clone mt-2">
							<input type="hidden"class="input-small" name="rule[conditions][<?php echo $condition->id;?>][id]" value="<?php echo $condition->id;?>">
							<div class="row mt-5">
								<div class="col-md-2">
									<span><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_IF");?></span>
								</div>
								<div class="col-md-2">
									<input type="hidden" name="rule[conditions][<?php echo $condition->id;?>][condition_on]" value="<?php echo $condition->condition_on;?>" />
									<select id="conditions_on<?php echo $condition->id;?>" name="rule[conditions][<?php echo $condition->id;?>][condition_on_attribute]" class="qtc-promotion-margin q2c-inline form-select" onchange="checkOperation('<?php echo $condition->id;?>')">
										<optgroup label="<?php echo Text::_("COM_QUICK2CART_ADD_CONDITION_INFO");?>" disabled>
										</optgroup>
										<optgroup label="<?php echo Text::_("COM_QUICK2CART_PRODUCT");?>">
											<option value="category" <?php echo ($condition->condition_on_attribute == 'category')?' selected="true" ':''?>><?php echo Text::_("COM_QUICK2CART_CAT");?></option>
											<option value="item_id" <?php echo ($condition->condition_on_attribute == 'item_id')?' selected="true" ':''?>><?php echo Text::_("COM_QUICK2CART_PRODUCT");?></option>
										</optgroup>
										<optgroup label="<?php echo Text::_('COM_QUICK2CART_CONDITION_ON_CART');?>">
											<option value="cart_amount" <?php echo ($condition->condition_on_attribute == 'cart_amount')?' selected="true" ':''?>><?php echo Text::_("COM_QUICK2CART_CONDITION_ON_CART_TOTAL_AMOUNT");?></option>
											<option value="quantity_in_store_cart" <?php echo ($condition->condition_on_attribute == 'quantity_in_store_cart')?' selected="true" ':''?>><?php echo Text::_("COM_QUICK2CART_CONDITION_ON_TOTAL_QUANTITY_IN_CART");?></option>
										</optgroup>
										<optgroup label="<?php echo Text::_('COM_QUICK2CART_CONDITION_ON_USER');?>">
											<option value="user_group" <?php echo ($condition->condition_on_attribute == 'user_group')?' selected="true" ':''?>><?php echo Text::_("COM_QUICK2CART_CONDITION_ON_USER_GROUP");?></option>
										</optgroup>
									</select>
								</div>
								<div class="col-md-2">
									<span id="conditions_operation_wrapper<?php echo $condition->id;?>">
										<?php
										if ($condition->condition_on_attribute == 'cart_amount' || $condition->condition_on_attribute == 'quantity_in_store_cart')
										{
										?>
										<div class="row">
											<div class="col-md-3">
												<span><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_IS");?></span>
											</div>
											<div class="col-md-9">
												<span id="conditions_operation_div<?php echo $condition->id?>">
													<select id="conditions_operation<?php echo $condition->id?>" class="qtc-promotion-margin form-select" name="rule[conditions][<?php echo $condition->id?>][operation]">
														<option value="=" <?php echo ($condition->operation == '=')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_EQUAL_TO");?>
														</option>
														<option value="<" <?php echo ($condition->operation == '<')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_LESS_THAN");?>
														</option>
														<option value=">" <?php echo ($condition->operation == '>')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_GREATER_THAN");?>
														</option>
														<option value=">=" <?php echo ($condition->operation == '>=')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_GREATER_THAN_EQUALTO");?>
														</option>
														<option value="<=" <?php echo ($condition->operation == '<=')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_LESS_THAN_EQUALTO");?>
														</option>
													</select>
												</span>
											</div>
										</div>
										<?php
										}
										else
										{
										?>
											<span><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_IN");?></span>
										<?php
										}
										?>
									</span>
								</div>

								<div class="col-md-2 ">

									<?php
									if (!empty($condition->condition_attribute_value))
									{
										$conditionAttributeValue = json_decode($condition->condition_attribute_value);
									}

									if ($condition->condition_on_attribute == "cart_amount")
									{
										if (!empty($currency))
										{
											?>
										<span id="currency_wrapper<?php echo $condition->id;?>">
											<span id="currency_div<?php echo $condition->id;?>">
											<?php
											foreach ($currency as $key => $value)
											{
												if (!empty($this->discount))
												{
													foreach ($this->discount as $discount)
													{
														if ($discount['currency'] == $value)
														{
															$dicountAmount = !empty($discount['discount'])?$discount['discount']:'';
														}
													}
												}

												$currsymbol = $comquick2cartHelper->getCurrencySymbol($value);
												?>
												<span class="input-group curr_margin qtc-promotion-margin">
													<input class="qtc_currency_price_discount required form-control"
															type="text"
															required="required"
															id="conditions_<?php echo $condition->id;?>_currency_value_<?php echo $value;?>"
															name="rule[conditions][<?php echo $condition->id;?>][condition_attribute_value][<?php echo $value;?>]"
															value="<?php echo ($condition->condition_on_attribute == 'cart_amount')?$conditionAttributeValue->$value:""?>"
															placeholder="<?php echo trim($currsymbol);?>"
															class="form-control"/>
													<span class="input-group-addon input-group-text"><?php echo $currsymbol;?></span>
													<div class="qtcClearBoth"></div>
												</span>
											<?php
											}
											?>
											</span>
										</span>
											<?php
										}
									}
									else
									{
										$conditionAttributeValue = implode(",", $conditionAttributeValue);
									?>
									<span id="currency_wrapper<?php echo $condition->id;?>">
										<span id="currency_div<?php echo $condition->id;?>" class="input-group">
										<input type="text" <?php echo ($condition->condition_on_attribute == 'quantity_in_store_cart')?"":"readonly='true'";?> name="rule[conditions][<?php echo $condition->id;?>][condition_attribute_value]" required="required" id="rule_conditions_<?php echo $condition->id;?>_condition_attribute_value" class="qtc_condition_attribute_value required qtc-promotion-margin form-control" value="<?php echo ($condition->condition_on_attribute == 'cart_amount')?"":$conditionAttributeValue;?>" />
										<label id="rule_conditions_<?php echo $condition->id;?>_condition_attribute_value_id" for="rule_conditions_<?php echo $condition->id;?>_condition_attribute_value" class="hidden"><?php echo Text::_("COM_QUICK2CART_CONDITION_VALUE_LABEL");?></label>
										<a
											class="qtcHandPointer input-group-text openSelectorWithModule <?php echo ($condition->condition_on_attribute == 'quantity_in_store_cart') ? "af-d-none" : "";?>" id="rule_conditions_attribute_select<?php echo $condition->id;?>" 
											data-id="<?php echo $condition->id;?>"
											title="<?php echo Text::_('COM_QUICK2CART_PROMOTION_CONDITION_SELECT')?>">
											<img src="<?php echo Uri::root();?>components/com_quick2cart/assets/images/magnifier.png">
										</a>
										</span>
									</span>
									<?php
									}
									?>
								</div>

								<div class="col-md-2">
									<span id="attribute_value_wrapper1">
										<span id="attribute_value_div1"></span>
									</span>
								</div>

								<div class="col-md-2">
									<span id="qtc-remove-conditionprimary">
										<a class="qtcHandPointer" onclick="qtcRemovePromotionCondition('<?php echo $condition->id;?>');">
											<button type="button" class="group-add btn btn-sm btn-danger" aria-label="<?php echo Text::_('COM_QUICK2CART_REMOVE_OPTION');?>"><i class="fa fa-minus"></i> </button>
										</a>
									</span>
								</div>

								<div class="col-md-12 mt-2">
									<div id="qtc-add-quantity-condition-div<?php echo $condition->id;?>">
										<div id="qtc-add-quantity-condition<?php echo $condition->id;?>" class="row qtc-add-quantity-condition-padding-left promo g-2">
											<?php

											if (($condition->condition_on_attribute == 'category' || $condition->condition_on_attribute == 'item_id'))
											{
												if (!empty($condition->quantity))
												{
											?>
												<div class="col-md-2 fisrt">
													<span><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_QUANTITY_INFO");?></span>
													<span id="conditions_operation_wrapper<?php echo $condition->id;?>">
													</span>
												</div>
												<span id="conditions_operation_div<?php echo $condition->id?>" class="col-md-2 equal">
													<select id="conditions_operation<?php echo $condition->id;?>" name="rule[conditions][<?php echo $condition->id;?>][operation]" class="qtc-promotion-margin form-select">
														<option value="=" <?php echo ($condition->operation == '=')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_EQUAL_TO");?>
														</option>
														<option value="<" <?php echo ($condition->operation == '<')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_LESS_THAN");?>
														</option>
														<option value=">" <?php echo ($condition->operation == '>')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_GREATER_THAN");?>
														</option>
														<option value=">=" <?php echo ($condition->operation == '>=')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_GREATER_THAN_EQUALTO");?>
														</option>
														<option value="<=" <?php echo ($condition->operation == '<=')?'selected="true"':'';?>>
															<?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_LESS_THAN_EQUALTO");?>
														</option>
													</select>
												</span>
												
												<div class="col-md-2">
													<label for="rule_conditions_<?php echo $condition->id;?>_quantity" class="hidden"><?php echo Text::_("COM_QUICK2CART_CONDITION_ON_PRODUCT_QUANTITY");?></label>
													<input type='number' Onkeyup= "checkforalpha(this,46,<?php echo $entered_numerics; ?>);" id='rule_conditions_<?php echo $condition->id;?>_quantity' name='rule[conditions][<?php echo $condition->id;?>][quantity]' 
													class='required qtc_condition_quantity form-control col-md-2' required='required' value="<?php echo $condition->quantity;?>">
												</div>

												<div class="col-md-2">
												</div>	

												<div class="col-md-2">
												</div>	
											
												<div class="col-md-2">
													<label for='rule_conditions_"<?php echo $condition->id;?>"_quantity' class='hidden'>Condition : Quantity</label>
													<a class="qtcHandPointer af-ml-10 col-md-2" onclick="qtcRemoveQuantityCondition('<?php echo $condition->id;?>');" title="<?php echo Text::_('COM_Q2C_REMOVE_TOOLTIP');?>">
														<button type="button" class="group-add btn btn-sm btn-danger" aria-label="<?php echo Text::_('COM_QUICK2CART_REMOVE_OPTION');?>"><i class="fa fa-minus"></i> </button>
													</a>
												</div>
												<?php
												}
												else
												{
												?>
													<a class="qtcHandPointer af-ml-10" onclick="qtcAddQuantityCondition('<?php echo $condition->id;?>');">
														<button type="button" class="group-add btn btn-sm btn-success" aria-label="<?php echo Text::_('COM_QUICK2CART_ADD_QUANTITY_CONDITION');?>"><i class="fa fa-plus"></i> </button>
													</a>
												<?php
												}
											}
											?>
										</div>
										<br>
									</div>
								</div>
							</div>
						</div>
					<?php
						}
					}?>
				</div>
			</div>
			<div>
				<div id="qtc-promotion-condition"></div>
				<a class="qtcHandPointer" onclick="qtcAddPromotionCondition();" >
					<button type="button" class="group-add btn btn-sm btn-success" aria-label="<?php echo Text::_('COM_QUICK2CART_ADD_QUANTITY_CONDITION');?>"><i class="fa fa-plus"></i> </button>
				</a>
			</div>
			<br><br><br>
			<?php
				if(!empty($this->promotionDescription))
				{
				?>
					<div class="qtc_promotion_rule_description_div alert alert-info">
						<?php echo Text::_("COM_QUICK2CART_FORM_LBL_PROMOTION_DESCRIPTION") . " : " . ucwords($this->promotionDescription);?>
						<div>
							<?php echo Text::_("COM_QUICK2CART_PROMOTION_DESCRIPTION_NOTE");?>
						</div>
					</div>
				<?php
				}

				echo HTMLHelper::_(
					'bootstrap.renderModal',
					'promotionConditionOptionCategory',
					array(
						'title'       => Text::_('COM_QUICK2CART_PROMOTION_CONDITION_SELECT'),
						'closeButton' => true,
						'url'        => Uri::root() . 'index.php?option=com_quick2cart&view=category&layout=select_category&tmpl=component',
						'modalWidth' => '40',
						'bodyHeight' => '60'
					)
				);

				echo HTMLHelper::_(
					'bootstrap.renderModal',
					'promotionConditionOptionProduct',
					array(
						'title'       => Text::_('COM_QUICK2CART_PROMOTION_CONDITION_SELECT'),
						'closeButton' => true,
						'url'        => Uri::root() . 'index.php?option=com_quick2cart&view=category&layout=select_product&tmpl=component',
						'modalWidth' => '80',
						'bodyHeight' => '80'
					)
				);

				echo HTMLHelper::_(
					'bootstrap.renderModal',
					'promotionConditionOptionUserGroup',
					array(
						'title'       => Text::_('COM_QUICK2CART_PROMOTION_CONDITION_SELECT'),
						'closeButton' => true,
						'url'        => Uri::root() . 'index.php?option=com_quick2cart&view=usergroup&layout=usergroup&tmpl=component',
						'modalWidth' => '60',
						'bodyHeight' => '50'
					)
				);

			echo HTMLHelper::_('uitab.endTab');
			echo HTMLHelper::_('uitab.endTabSet');?>
			<input type="hidden" name="task" value=""/>
			<?php echo HTMLHelper::_('form.token');?>
		</div>
	</form>
</div>



