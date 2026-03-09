function qtcRemovePromotionCondition(id)
{
	var confirmdelete = confirm("Do you really want to delete condition from promotion rule?");

	if (confirmdelete == false)
	{
		return false;
	}

	var callurl = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=promotion.qtc_delete_promotion_condition&tmpl=component&cid="+id;

	techjoomla.jQuery.ajax({
		url: callurl,
		type: "POST",
		cache: false,
		success: function(response)
		{
			var message = JSON.parse(response);

			if(message[0].error)
			{
				alert(message[0].error);
			}
			else
			{
				techjoomla.jQuery("#qtc-promotion-condition-div-clone"+id).remove();
			}
		}
	});
}

function qtcRemoveQuantityCondition(id)
{
	var confirmdelete = confirm(Joomla.JText._('COM_QUICK2CART_PROMOTION_CONDITION_REMOVE_QUANTITY_INFO'));

	if( confirmdelete == false )
	{
		return false;
	}

	addQuantityConditionAddButton(id);
}

function checkOperation(id)
{
	var selected = techjoomla.jQuery("#conditions_on"+id+" option:selected");

	var value = selected.closest('optgroup').attr('label');
	techjoomla.jQuery("[name='rule[conditions]["+id+"][condition_on]']").val(value);

	var condition_attribute = techjoomla.jQuery("#conditions_on"+id+" option:selected").val();

	if (condition_attribute == 'cart_amount')
	{
		var clone = techjoomla.jQuery('#qtc-cart-amount-currency-primary-div').clone(false);

		clone.find('#qtc-cart-amount-currency-primary-divclone').attr("id", "qtc-cart-amount-currency-"+id+"-divclone");

		techjoomla.jQuery('#currency_div'+id).empty();
		techjoomla.jQuery('#currency_div'+id).append(clone);
		techjoomla.jQuery('#currency_div'+id+' input').attr("required", "required");
		techjoomla.jQuery('#currency_div'+id+' input').addClass("required");

		techjoomla.jQuery('#currency_div'+id+" input").each(function(){
			var name = techjoomla.jQuery(this).attr('name');
			var updatedName = name.replace("primary", id);

			var oldId = techjoomla.jQuery(this).attr('id');
			var updatedId = oldId.replace("primary", id);

			techjoomla.jQuery(this).attr('id', updatedId);
			techjoomla.jQuery(this).attr('name', updatedName);
			});

		techjoomla.jQuery('#currency_div'+id+" label").each(function(){

		var oldId = techjoomla.jQuery(this).attr('for');
		var updatedId = oldId.replace("primary", id);

		techjoomla.jQuery(this).attr('for', updatedId);
		});

		techjoomla.jQuery("#qtc-cart-amount-currency-"+id+"-divclone").removeAttr("style");

		techjoomla.jQuery('#qtc-cart-amount-currency-'+id+'-div input').each(function(){
				techjoomla.jQuery(this).attr("required","required");
				techjoomla.jQuery(this).addClass("required");
			});

		techjoomla.jQuery('#qtc-cart-amount-currency-'+id+'-div').show();

		techjoomla.jQuery('#qtc-condition-attribute-value-'+id+'-div input').each(function(){
				techjoomla.jQuery(this).removeAttr("required");
				techjoomla.jQuery(this).removeClass("required");
			});
		techjoomla.jQuery('#qtc-condition-attribute-value-'+id+'-div').hide();
	}
	else
	{
		var clone = techjoomla.jQuery('#qtc-condition-attribute-value-primary-div').clone(false);
		clone.find('#qtc-condition-attribute-value-primary-divclone').attr("id", "qtc-condition-attribute-value-"+id+"-divclone");

		clone.find('#rule_conditions_attribute_selectprimary').attr("id", "rule_conditions_attribute_select"+id);
		// clone.find("#rule_conditions_attribute_select"+id).attr("onclick", "openSelector('"+id+"')");

		clone.find('#rule_conditions_primary_condition_attribute_value').attr("id", "rule_conditions_"+id+"_condition_attribute_value");
		clone.find("[name='rule[conditions][primary][condition_attribute_value]']").attr("name", "rule[conditions]["+id+"][condition_attribute_value]");

		clone.find('#rule_conditions_primary_condition_attribute_value_id').attr("id", "rule_conditions_"+id+"_condition_attribute_value_id");

		clone.find("#rule_conditions_"+id+"_condition_attribute_value_id").attr("for", "rule_conditions_"+id+"_condition_attribute_value");

		// Added Newly to checking modal issue
		// clone.find('#promotionConditionOption-primary').attr("id", "promotionConditionOption-"+id);
		clone.find('.qtcHandPointer').attr("data-id", id);
		// clone.find('.qtcHandPointer').attr("data-target", "#promotionConditionOption-"+id);
		// clone.find('.qtcHandPointer').attr("data-bs-target", "#promotionConditionOption-"+id);

		techjoomla.jQuery('#currency_div'+id).empty();
		techjoomla.jQuery('#currency_div'+id).append(clone);

		if (techjoomla.jQuery("#conditions_on"+id).val() == 'quantity_in_store_cart')
		{
			techjoomla.jQuery("#rule_conditions_attribute_select"+id).addClass('af-d-none');
			techjoomla.jQuery("#rule_conditions_"+id+"_condition_attribute_value").removeAttr('readonly');
			techjoomla.jQuery("#rule_conditions_"+id+"_condition_attribute_value").addClass('checkPositiveCartStore');
			techjoomla.jQuery("#rule_conditions_"+id+"_condition_attribute_value").attr('type', 'number');
		}

		techjoomla.jQuery("[name='rule[conditions]["+id+"][condition_attribute_value]']").attr("required", "required");
		techjoomla.jQuery("[name='rule[conditions]["+id+"][condition_attribute_value]']").addClass("required");

		techjoomla.jQuery("#qtc-condition-attribute-value-"+id+"-divclone").removeAttr("style");

		techjoomla.jQuery('#qtc-condition-attribute-value-'+id+'-div input').each(function(){
				techjoomla.jQuery(this).attr("required","required");
				techjoomla.jQuery(this).addClass("required");
			});
		techjoomla.jQuery('#qtc-condition-attribute-value-'+id+'-div').show();

		techjoomla.jQuery('#qtc-cart-amount-currency-'+id+'-div input').each(function(){
				techjoomla.jQuery(this).removeAttr("required");
				techjoomla.jQuery(this).removeClass("required");
			});
		techjoomla.jQuery('#qtc-cart-amount-currency-'+id+'-div').hide();
	}

	if (condition_attribute == 'category' || condition_attribute == 'item_id')
	{
		techjoomla.jQuery('#conditions_operation_wrapper'+id).empty();
		techjoomla.jQuery('#conditions_operation_wrapper'+id).append(""+Joomla.JText._('COM_QUICK2CART_PROMOTION_CONDITION_IN')+"");

		addQuantityConditionAddButton(id);
	}
	else if(condition_attribute == 'user_group')
	{
		techjoomla.jQuery('#conditions_operation_wrapper'+id).empty();
		techjoomla.jQuery('#conditions_operation_wrapper'+id).append(""+Joomla.JText._('COM_QUICK2CART_PROMOTION_CONDITION_IN')+"");
		techjoomla.jQuery('#conditions_operation'+id).removeAttr("style");
		techjoomla.jQuery('#qtc-add-quantity-condition'+id).empty();

	}
	else
	{
		if (techjoomla.jQuery('.bs5Loaded').length) {
			var cloned = techjoomla.jQuery('#conditions_operation_wrapperprimary').clone(false);

			cloned.find('#conditions_operationprimary').attr("id", "conditions_operation"+id);
			cloned.find("[name='rule[conditions][primary][operation]']").attr("name", "rule[conditions]["+id+"][operation]");
			techjoomla.jQuery('#conditions_operation_wrapper'+id).empty();
			techjoomla.jQuery('#conditions_operation_wrapper'+id).append(cloned);
			techjoomla.jQuery('#conditions_operation'+id).removeAttr("style");
			techjoomla.jQuery('#qtc-add-quantity-condition'+id).empty();

		} else {
			var cloned = techjoomla.jQuery('#conditions_operation_divprimary').clone(false);

			cloned.find('#conditions_operationprimary').attr("id", "conditions_operation"+id);
			cloned.find("[name='rule[conditions][primary][operation]']").attr("name", "rule[conditions]["+id+"][operation]");
			techjoomla.jQuery('#conditions_operation_wrapper'+id).empty();
			techjoomla.jQuery('#conditions_operation_wrapper'+id).append('<span> '+Joomla.JText._("COM_QUICK2CART_PROMOTION_CONDITION_IS")+'</span>');
			techjoomla.jQuery('#conditions_operation_wrapper'+id).append(cloned);
			techjoomla.jQuery('#conditions_operation'+id).removeAttr("style");
			techjoomla.jQuery('#qtc-add-quantity-condition'+id).empty();
		}
	}
}

function hideCouponData()
{
	techjoomla.jQuery(".qtc-promotion-coupon-dependent").hide();
	techjoomla.jQuery(".condition-req input").removeClass('required validate-blankspace');
	techjoomla.jQuery(".condition-req input").removeAttr('required', 'required');
	techjoomla.jQuery(".condition-req input").removeAttr('validate', 'blankspace');
}

function showCouponData()
{
	techjoomla.jQuery(".qtc-promotion-coupon-dependent").show();
	techjoomla.jQuery(".condition-req input").addClass('validate-blankspace required');
	techjoomla.jQuery(".condition-req input").attr('required', 'required');
	techjoomla.jQuery(".condition-req input").attr('validate', 'blankspace');
}

function hideMaxDiscount()
{
	techjoomla.jQuery(".qtc-promotion-discount-type-dependent").hide();
}

function showMaxDiscount()
{
	techjoomla.jQuery(".qtc-promotion-discount-type-dependent").show();
}

function maxDiscount()
{
	var discount_type = techjoomla.jQuery("[name='jform[discount_type]']").val();

	if (discount_type == 'percentage')
	{
		showMaxDiscount();
	}
	else
	{
		hideMaxDiscount();
	}
}

techjoomla.jQuery(document).ready(function ()
{
	maxDiscount();

	if (techjoomla.jQuery("#coupon_required0").is(':checked') == true)
	{
		hideCouponData();
	}
	else
	{
		showCouponData();
	}

	if (techjoomla.jQuery("#jform_discount_type").val() == "flat")
	{
		techjoomla.jQuery(".qtc-promotion-flat-discount-div input").each(function(){
		techjoomla.jQuery(this).attr("required","required");
				techjoomla.jQuery(this).addClass("required");
			});
		techjoomla.jQuery(".qtc-promotion-flat-discount-div").show();

		techjoomla.jQuery(".qtc-promotion-percent-discount-div input").each(function(){
			techjoomla.jQuery(this).removeAttr("required");
			techjoomla.jQuery(this).removeClass("required");
			});

		techjoomla.jQuery(".qtc-promotion-percent-discount-div").hide();
	}
	else
	{
		techjoomla.jQuery(".qtc-promotion-flat-discount-div input").each(function(){
			techjoomla.jQuery(this).removeAttr("required");
			techjoomla.jQuery(this).removeClass("required");
		});

		techjoomla.jQuery(".qtc-promotion-flat-discount-div").hide();

		techjoomla.jQuery(".qtc-promotion-percent-discount-div input").each(function(){
				techjoomla.jQuery(this).attr("required","required");
				techjoomla.jQuery(this).addClass("required");
			});
		techjoomla.jQuery(".qtc-promotion-percent-discount-div").show();
	}

	techjoomla.jQuery("#jform_discount_type").change(function()
	{
		if (techjoomla.jQuery("#jform_discount_type").val() == "flat")
		{
			techjoomla.jQuery(".qtc-promotion-flat-discount-div input").each(function(){
				techjoomla.jQuery(this).attr("required","required");
				techjoomla.jQuery(this).addClass("required");
			});
			techjoomla.jQuery(".qtc-promotion-flat-discount-div").show();

			techjoomla.jQuery(".qtc-promotion-percent-discount-div input").each(function(){
				techjoomla.jQuery(this).removeAttr("required");
				techjoomla.jQuery(this).removeClass("required");
			});

			techjoomla.jQuery(".qtc-promotion-percent-discount-div").hide();
		}
		else
		{
			techjoomla.jQuery(".qtc-promotion-percent-discount-div input").each(function(){
				techjoomla.jQuery(this).attr("required","required");
				techjoomla.jQuery(this).addClass("required");
			});

			techjoomla.jQuery(".qtc-promotion-percent-discount-div").show();

			techjoomla.jQuery(".qtc-promotion-flat-discount-div input").each(function(){
				techjoomla.jQuery(this).removeAttr("required");
				techjoomla.jQuery(this).removeClass("required");
			});
			techjoomla.jQuery(".qtc-promotion-flat-discount-div").hide();
		}
	});

	techjoomla.jQuery("[name='jform[coupon_required]']").click(function()
	{
		if (techjoomla.jQuery("#coupon_required0").is(':checked') == true)
		{
			hideCouponData();
		}
		else
		{
			showCouponData();
		}
	});

	techjoomla.jQuery("[name='jform[discount_type]']").change(function()
	{
		maxDiscount();
	});
});

function qtcAddQuantityCondition(id)
{
	if (jQuery('.bs5Loaded').length)
	{
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).empty();
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).addClass('mt-1');
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).addClass('mb-1');
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).append("<div class='col-md-2'><span>" + Joomla.JText._("COM_QUICK2CART_PROMOTION_PRODUCT_QUANTITY_TEXT") + "  </span></div><div class='col-md-2'><span id='qtc_condition_on_product_operation"+id+"'></span></div>");

		var cloned = techjoomla.jQuery('#conditions_operation_divprimary').clone(false);

		cloned.find('#conditions_operationprimary').attr("id", "conditions_operation"+id);
		cloned.find("[name='rule[conditions][primary][operation]']").attr("name", "rule[conditions]["+id+"][operation]");
		techjoomla.jQuery('#qtc_condition_on_product_operation'+id).empty();
		techjoomla.jQuery('#qtc_condition_on_product_operation'+id).append(cloned);
		techjoomla.jQuery('#conditions_operation'+id).removeAttr("style");

		techjoomla.jQuery('#qtc-add-quantity-condition'+id).append("<div class='col-md-2'><input type='number' onChange='checkforalpha(this, 46, `" + Joomla.JText._('QTC_ENTER_NUMERICS') + "`)' id='rule_conditions_"+id+"_quantity' name='rule[conditions]["+id+"][quantity]' class='form-control required qtc_condition_quantity q2c-inline' required='required'><label for='rule_conditions_"+id+"_quantity' class='hidden'>Condition : Quantity</label></div>");
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).append('<div class="col-md-2"><a class="af-ml-10" onclick="qtcRemoveQuantityCondition('+id+');"><button type="button" class="group-add btn btn-sm btn-danger" aria-label="'+Joomla.JText._("COM_QUICK2CART_REMOVE_OPTION")+'"><i class="fa fa-minus"></i> </button></a></div>');
		
	}
	else 
	{
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).empty();
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).append("<span>" + Joomla.JText._("COM_QUICK2CART_PROMOTION_PRODUCT_QUANTITY_TEXT") + "  </span><span id='qtc_condition_on_product_operation"+id+"'></span>");

		var cloned = techjoomla.jQuery('#conditions_operation_divprimary').clone(false);

		cloned.find('#conditions_operationprimary').attr("id", "conditions_operation"+id);
		cloned.find("[name='rule[conditions][primary][operation]']").attr("name", "rule[conditions]["+id+"][operation]");
		techjoomla.jQuery('#qtc_condition_on_product_operation'+id).empty();
		techjoomla.jQuery('#qtc_condition_on_product_operation'+id).append(cloned);
		techjoomla.jQuery('#conditions_operation'+id).removeAttr("style");

		techjoomla.jQuery('#qtc-add-quantity-condition'+id).append("<input type='number' onChange='checkforalpha(this, 46, `" + Joomla.JText._('QTC_ENTER_NUMERICS') + "`)' id='rule_conditions_"+id+"_quantity' name='rule[conditions]["+id+"][quantity]' class='form-control mt-xxl-2 required qtc_condition_quantity q2c-inline' required='required'><label for='rule_conditions_"+id+"_quantity' class='hidden'>Condition : Quantity</label>");
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).append('<a class="af-ml-10" onclick="qtcRemoveQuantityCondition('+id+');"><img title="'+Joomla.JText._("COM_QUICK2CART_REMOVE_OPTION")+'" src="'+ Joomla.getOptions('system.paths').root +'/components/com_quick2cart/assets/images/remove_rule_condition.png"/></a>');
	}
}

function addQuantityConditionAddButton(id)
{
	techjoomla.jQuery("#qtc-add-quantity-condition"+id).empty();

	if (jQuery('.bs5Loaded').length)
	{
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).append('<a class="af-ml-10" onclick="qtcAddQuantityCondition(\''+id+'\');"><button type="button" class="group-add btn btn-sm btn-success" aria-label="'+Joomla.JText._("COM_QUICK2CART_ADD_CONDITION")+'"><i class="fa fa-plus"></i> </button></a><br>');
	}
	else 
	{
		techjoomla.jQuery("#qtc-add-quantity-condition"+id).append('<a class="af-ml-10" onclick="qtcAddQuantityCondition(\''+id+'\');"><img title="'+Joomla.JText._("COM_QUICK2CART_ADD_CONDITION")+'" src="'+ Joomla.getOptions('system.paths').root +'/components/com_quick2cart/assets/images/add_rule_condition.png"/></a><br>');
	}
}

function qtcAddPromotionCondition()
{
	var clone = techjoomla.jQuery('#qtc-promotion-condition-div-primary').clone(false);

	// Added Newly to checking modal issue
	clone.find('#qtc-promotion-condition-div-cloneprimary').attr("id", "qtc-promotion-condition-div-clone"+conditionCount);

	/* code to change name and ids of elements in new clone - start*/
	clone.find('#qtc-promotion-condition-div-cloneprimary').attr("id", "qtc-promotion-condition-div-clone"+conditionCount);
	clone.find('#qtc-promotion-condition-div-clone'+conditionCount).removeAttr("style", "");
	clone.find('#qtc-remove-conditionprimary').attr("id", "qtc-remove-condition"+conditionCount);
	clone.find('#qtc-remove-condition'+conditionCount+' a').attr("onclick", "qtcRemovePromotionCondition('"+conditionCount+"')");
	clone.find('#qtc-add-quantity-conditionprimary').attr("id", "qtc-add-quantity-condition"+conditionCount);
	clone.find('#rule_conditions_primary_condition_attribute_value').attr("id", "rule_conditions_"+conditionCount+"_condition_attribute_value");

	clone.find('#qtc-add-quantity-condition-divprimary').attr("id", "qtc-add-quantity-condition-div"+conditionCount);
	clone.find('#conditions_operation_wrapperprimary').attr("id", "conditions_operation_wrapper"+conditionCount);

	clone.find('#qtc-condition-attribute-value-primary-div').attr("id", "qtc-condition-attribute-value-"+conditionCount+"-div");
	clone.find('#qtc-condition-attribute-value-primary-div').attr("id", "qtc-condition-attribute-value-"+conditionCount+"-div");
	clone.find('#qtc-cart-amount-currency-primary-div').attr("id", "qtc-cart-amount-currency-"+conditionCount+"-div");

	clone.find('#currency_wrapperprimary').attr("id", "currency_wrapper"+conditionCount);
	clone.find('#currency_divprimary').attr("id", "currency_div"+conditionCount);

	clone.find('#attribute_value_wrapperprimary').attr("id", "attribute_value_wrapper"+conditionCount);
	clone.find('#attribute_value_divprimary').attr("id", "attribute_value_div"+conditionCount);

	clone.find('#conditions_onprimary').attr("id", "conditions_on"+conditionCount);
	clone.find('#conditions_on'+conditionCount).attr("onchange", "checkOperation('"+conditionCount+"')");
	clone.find('#qtc-add-quantity-condition'+conditionCount+' a').attr("onclick", "qtcAddQuantityCondition('"+conditionCount+"')");
	clone.find("[name='rule[conditions][primary][condition_on_attribute]']").attr("name", "rule[conditions]["+conditionCount+"][condition_on_attribute]");

	clone.find("[name='rule[conditions][primary][condition_on]']").attr("name", "rule[conditions]["+conditionCount+"][condition_on]");

	clone.find("[name='rule[conditions][primary][condition_attribute_value]']").attr("name", "rule[conditions]["+conditionCount+"][condition_attribute_value]");
	clone.find("[name='rule[conditions]["+conditionCount+"][condition_attribute_value]']").attr("value", "");
	clone.find('#conditions_operationprimary').attr("id", "conditions_operation"+conditionCount);
	clone.find("[name='rule[conditions][primary][operation]']").attr("name", "rule[conditions]["+conditionCount+"][operation]");

	/* code to change name and ids of elements in new clone - end*/
	techjoomla.jQuery('#qtc-promotion-condition').append(clone);

	techjoomla.jQuery('#conditions_on'+conditionCount).val('category').trigger('change');

	conditionCount++;
}

jQuery( document ).ready(function() {
	jQuery(document).on('click', '.openSelectorWithModule', function(e) { 
		selectedConditionCategory = jQuery(this).data('id');

		var attribute = techjoomla.jQuery('#conditions_on'+selectedConditionCategory).val();

		if (attribute == 'category')
		{
			jQuery('#promotionConditionOptionCategory').modal('show');
		}
		else if(attribute == 'user_group')
		{
			jQuery('#promotionConditionOptionUserGroup').modal('show');
		}
		else
		{
			storeId = techjoomla.jQuery('#jform_store_id').val();

			if (storeId != '')
			{
				jQuery('#promotionConditionOptionProduct').modal('show');
			}
			else
			{
				alert(Joomla.JText._("COM_QUICK2CART_PROMOTION_CONDITION_SELECT_STORE_ALERT"));
			}
		}
	});

	jQuery(document).on('change', '.checkPositiveCartStore', function(e) {
		var val= jQuery(this).val();
		checkforalpha(this, 46, Joomla.JText._('QTC_ENTER_NUMERICS'));
	});
});