function qtc_addtocart(pid)
{
	var addcart_str = qtc_itemdataformat(pid,1);

	let invalidAttributesFlag = 0;

	/* Check for required text attribute of the product */
	techjoomla.jQuery.each(techjoomla.jQuery("."+pid+"_UserField"),function()
	{
		if (techjoomla.jQuery(this).attr("required") != undefined)
		{
			if (techjoomla.jQuery(this).val() == '')
			{
				invalidAttributesFlag = 1;
				techjoomla.jQuery(this).addClass('invalid')
			}
			else
			{
				techjoomla.jQuery(this).removeClass('invalid');
			}
		}
	});

	if (invalidAttributesFlag == 1)
	{
		return false;
	}

	qtc_commonCall(addcart_str);
}

window.onload = function() {
	if (document.formvalidator != undefined)
	{
		document.formvalidator.setHandler('phone', function(value, element) {
			value = punycode.toASCII(value);
			var regex = /^[+]?([0-9]+[-]?)*([0-9]+$)/;
			return regex.test(value);
		});
	}
};

function qtc_itemdataformat(pid,formattype)
{
	/*pid like = com_content-6*/
	var count = techjoomla.jQuery("#"+pid+"_itemcount").val();
	var options ='';

	if(techjoomla.jQuery("."+pid+"_options"))
	{
		techjoomla.jQuery.each(techjoomla.jQuery("."+pid+"_options"),function()
		{
			options =  techjoomla.jQuery(this).val()+","+options;
		});
		options = options.slice(0, -1);
	}

	/** user fields*/
	var userFields = {};
	var index=0;
	var userData = '';
	if(techjoomla.jQuery("."+pid+"_UserField"))
	{
		techjoomla.jQuery.each(techjoomla.jQuery("."+pid+"_UserField"),function()
		{
			var texboxFields ={};
			userFieldsValue =  techjoomla.jQuery(this).val();

			if(userFieldsValue)
			{
				attrFields =  techjoomla.jQuery(this).attr('name');
				attrFields = attrFields.split("_");
				texboxFields['itemattributeoption_id'] = attrFields[1];

				if(options)
				{
					options =  attrFields[1] + ","+options;
				}
				else
				{
					options =  attrFields[1];
				}

				texboxFields['type'] = "Textbox";
				texboxFields['value'] = userFieldsValue;
				index = texboxFields['itemattributeoption_id']
				userFields[index] = texboxFields;
			}
		});
		userData = JSON.stringify(userFields);
	}

	if(formattype ==1)
	{
		var addcart_str = "&id="+pid+"&count="+count;
		if(options != '')
		{
			addcart_str = addcart_str+"&options="+options;
		}

		var retDataObj = {};
		retDataObj['urlParamStr'] =addcart_str;
		retDataObj['userData'] =userData;

		return retDataObj;
	}
	else
	{
		var addcart_obj = {};
		addcart_obj['id'] =pid;
		addcart_obj['count'] =count;

		if(options != '')
		{
			addcart_obj['options'] =options;
		}

		addcart_obj['userData'] =userFields;
		return addcart_obj;
	}
}

function qtcproduct_addtoCart(item_id)
{
	var count = techjoomla.jQuery("#"+item_id+"_qtcitemcount").val();

	/*GETTING SELECTED OPTION IDS*/
	var options ='';
	if(techjoomla.jQuery("."+item_id+"_qtcoptions"))
	{
		techjoomla.jQuery.each(techjoomla.jQuery("."+item_id+"_qtcoptions"),function()
		{
			options =  techjoomla.jQuery(this).val()+","+options;
		});
		options = options.slice(0, -1)
	}

	var addcart_str="&options="+options+"&item_id="+item_id +"&count="+ count;
	var retDataObj = {};
	retDataObj['urlParamStr'] =retDataObj;
	retDataObj['userData'] ='';
	qtc_commonCall(retDataObj);
}

/**
 * @PARAM $options STRING ::string of comma seperated attribure_option_ids (OPTIONS IDS)
 * @PARAM $count-INTEGER :: Product count to buy
  * @PARAM $item_id -INTEGER :: ID of kart_items table
 * */
function qtc_mod_addtocart(options,count,item_id)
{
	var addcart_str="&options="+options+"&item_id="+item_id +"&count="+ count;
	var retDataObj = {};
	retDataObj['urlParamStr'] =addcart_str;
	retDataObj['userData'] ='';
	qtc_commonCall(retDataObj);
}

/** This function takes URL PARMAS STRING
 * @PARAM $options STRING ::PARMAS object
 * */
function qtc_commonCall(paramsObj)
{
	var urlParam          = paramsObj['urlParamStr'];
	var postParam         = {};
	postParam['userData'] = paramsObj['userData'];

	techjoomla.jQuery.ajax({
		url: Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=addcart&tmpl=component&format=raw"+urlParam,
		type: 'POST',
		data:postParam,
		cache: false,
		async:false,
		/*crossDomain: true,*/
		dataType: 'json',
		/*beforeSend: setHeader,*/
		success: function(msg)
		{
			if(msg['successCode'] == 1)
			{
				// Update module content.
				update_mod();
				toggleItemQtyinput({'qty' : msg.cart_item_qty, 'pid' : msg['item_id'], 'cart_item_id' : msg['cart_item_id']});

				// Update cart quantity
				if(typeof(msg['itemCount']) !== 'undefined')
				{
					techjoomla.jQuery(".q2c-cart-mod .q2c-cart-mod__count").html(msg['itemCount']);
				}

				if (msg.toastrConfig !== undefined)
				{
					/** global: toastr */
					toastr.options = msg['toastrConfig'];
					toastr["success"](msg['message']);
					techjoomla.jQuery("#toast-container").focus();
				}
				else
				{
					if(jQuery('.tjBs3').length) {
						jQuery('#cartModal').modal('show');
						jQuery('#cartModal').parent().attr('style' , 'display:block !important');
						jQuery('#cartModal').parent().find('.q2c-small-buy-button').attr('style' , 'display:none !important');
					} else {
						jQuery('#cartModal').attr('data-width' , (window.innerWidth)/2);
						jQuery('#cartModal').attr('data-height' , window.innerHeight);
						jQuery('#cartModal').modal('show');
						jQuery('#cartModal').attr('style' , 'display:block !important');
						jQuery('#cartModal').parent().attr('style' , 'display:block !important');
						jQuery('#cartModal').parent().find('.q2c-small-buy-button').attr('style' , 'display:none !important');
					}
				}
			}
			else if(msg['successCode'] == 2)
			{
				/* Single store checkout */
				alert(msg['message']);
				window.location = "index.php?option=com_quick2cart&view=cartcheckout";
			}
			else if(msg['successCode'] == 3)
			{
				/* Invalid attributes */
				alert(msg['message']);
			}
			else if(!msg['success'])
			{
				alert(msg['message']);
			}
		}
	});
}

function update_mod()
{
	var currentPageURI = (location.pathname+location.search);
	var postParam = {};

	if (currentPageURI)
	{
		postParam['currentPageURI'] = currentPageURI;
	}

	techjoomla.jQuery.ajax({
		url: Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=cart.update_mod&tmpl=component",
		type: "POST",
		data:postParam,
		cache: false,
		success: function(data)
		{
			techjoomla.jQuery(".qtcModuleWrapper").html(data);
		}
	});
}

/**
 * this function allow only numberic and specified char (at 0th position)
 * ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
 * (code 46 for dot/full stop .)
 * @param el :: html element
 * @param allowed_ascii::ascii code that shold allow
 **/
function checkforalpha(el, allowed_ascii,enter_numerics )
{
	allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
	var i =0 ;

	for(i=0;i<el.value.length;i++)
	{
		if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 ))
		{
			if(allowed_ascii ==el.value.charCodeAt(i))
			{
				var temp=1;
			}
			else
			{
				alert(enter_numerics);
				el.value = el.value.substring(0,i);
				return false;
			}
		}
	}

	return true;
}

/**
 * this function allow only numberic and specified char (at 0th position) and does not allow value 0
 * ascii (code 43 for +) (48 for 0 ) (57 for 9)  (45 for  - (negative sign))
 * (code 46 for dot/full stop .)
 * @param el :: html element
 * @param allowed_ascii::ascii code that shold allow
 **/
function checkforpricevalue(el, allowed_ascii )
{
	allowed_ascii= (typeof allowed_ascii === "undefined") ? "" : allowed_ascii;
	var i =0 ;
	for(i=0;i<el.value.length;i++)
	{
		if(el.value <= 0)
		{
			alert('Please Enter Price greater than one');
			el.value = el.value.substring(0,i);
			return false;
		}

		if((el.value.charCodeAt(i) <= 47 || el.value.charCodeAt(i) >= 58) || (el.value.charCodeAt(i) == 45 ))
		{
			/* + allowing for phone no at first char*/
			if(allowed_ascii ==el.value.charCodeAt(i) )  /*&& i==0)*/
			{
				var temp=1;
			}
			else
			{
				alert('Please Enter Numerics');
				el.value = el.value.substring(0,i);
				return false;
			}
		}
	}
	return true;
}

/**
 * This function used to get Quantity limit
 * @param limit string eg. min/max
 **/
function selectstatusorder(appid,ele)
{
	var ele = document.getElementById('pstatus'+appid); /* amol change*/
	var status = document.getElementById("pstatus" + appid).value;

	document.getElementById('hidid').value = appid;
	document.getElementById('hidstat').value = status;
	Joomla.submitform('orders.save');
	return;
}

function updateOrderStatus(orderId, ele)
{
	var noteId = "order_note_" + orderId;

	/* Update note field name to "order note" so that it will be compatible to order detail page note field */
	var status = document.getElementById("pstatus" + orderId).value;
	document.getElementById('hidid').value = orderId;
	document.getElementById('hidstat').value = status;
	Joomla.submitform('orders.save');
	return;
}

function change_curr(curr)
{
	techjoomla.jQuery.ajax({
		url: Joomla.getOptions('system.paths').base+'/index.php?option=com_quick2cart&task=cartcheckout.setCurrencySession&tmpl=component&format=raw&currency='+curr,
		type: 'GET',
		cache: false,
		success: function(data) {
			setCookie('qtc_currency',curr,7);
			window.location.reload();
		}
	});
}

function setCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value+ "; path=/";
}

function emptycart(empty_cart_comfirmation,cart_emtied,redirectUrl)
{
	var flag= confirm(empty_cart_comfirmation);
	if(flag==true)
	{
		techjoomla.jQuery.ajax({
			url: Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=clearcart&tmpl=component&format=raw",
			type: "GET",
			cache: false,
			success: function(msg)
			{
				techjoomla.jQuery(".q2c-cart-mod .q2c-cart-mod__count", window.parent.document).html('0');

				// After emptying cart, close the cart modal popup, reload the parent page & show "Add" button for all products to buy/add the product into the cart again.
				if (window.parent.jQuery('.q2c-wrapper.tjBs5').length && window.parent.jQuery('.q2c-wrapper.tjBs5 #cartModal').length && window.parent.jQuery('#cartModal').hasClass('show'))
				{
					window.parent.location.reload();
				}
				else
				{
					window.location = redirectUrl;
				}
			}
		});
	}
}

function getlimit(limit,pid,parent,min_qtc,max_qtc)
{
	var lim=limit.trim();
	if(lim=="min")
	{
		return min_qtc;
	}
	else
	{
		return max_qtc;
	}
}

function qtc_increment(input_field,pid,cartItemId,parent,slab,min_qtc,max_qtc)
{
	var qty_el = document.getElementById(input_field);
	var tmpQty = Number(qty_el.value);
	var minMsg = Joomla.JText._("QTC_MIN_LIMIT_MSG");
	var maxMsg = Joomla.JText._("QTC_MAX_LIMIT_MSG");

	qty_el.value = tmpQty + Number(slab);

	if (checkforalphaLimit(qty_el,pid,parent,slab,min_qtc,max_qtc,minMsg,maxMsg) == false)
	{
		qty_el.value = tmpQty;
	}

	qty_el.value = tmpQty;
	var limit = getlimit('max',pid,parent,min_qtc,max_qtc);
	limit = parseInt(limit);
	slab = parseInt(slab);
	var qty = qty_el.value;
	qty = parseInt(qty_el.value);

	if( !isNaN(qty) && qty < limit)
	{
		qty = qty + slab;
		updateCartItemQty(qty_el, cartItemId, pid, qty);
	}
	else
	{
		return false;
	}
}

function updateCartItemQty(qty_el, cartItemId, pid, qty) {
	if (jQuery(qty_el).attr("data-cart-item-id") != undefined || jQuery(qty_el).attr("data-cart-item-id") != "")
	{
		var cartItemId = parseInt(jQuery(qty_el).attr("data-cart-item-id"));
	}

	var formData = jQuery("<form>").attr("method", "POST");
	formData.append(jQuery("<input type='hidden'>").attr("name", "cartDetail["+cartItemId+"][cart_item_id]").val(cartItemId));
	formData.append(jQuery("<input type='hidden'>").attr("name", "cartDetail["+cartItemId+"][cart_count]").val(qty));
	formData = formData.serialize();

	var promiseObj = new Promise(function(resolve, reject){
		techjoomla.jQuery.ajax({
			url : Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=cartcheckout.update_cart_item&tmpl=component",
			type : 'POST',
			dataType : 'json',
			async : false,
			data:
			{
				'formData' : formData,
				'cart_item_id' : cartItemId,
				'item_id' : pid
			},
			success : function(ret)
			{
				if (!ret.status)
				{
					alert(Joomla.JText._('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_FAIL') + "( " + ret.message + " )");
				}
				else
				{
					resolve({"qty" : qty, "pid" : pid});
				}
			},
			error : function (e)
			{
				reject();
			}
		});
	});

	promiseObj.then(function (data){
		update_mod();
		qty_el.value = data.qty;
		toggleItemQtyinput(data);
	},
	function (){
	});
}

function toggleItemQtyinput (data) {
	if (data.qty > 0)
	{
		jQuery(".q2c-item-qtycount-increment-decrement-section-"+data.pid).show();
		jQuery(".q2c-item-to-cart-section-"+data.pid).hide();

		jQuery(".q2c-item-qtycount-increment-decrement-section-"+data.pid).find(".qtc_item_count_inputbox").each(function (){
			jQuery(this).attr("id", jQuery(this).attr("id").replace("-tmp", ""));
			jQuery("#"+jQuery(this).attr("id")).val(data.qty);

			if (data.cart_item_id != undefined && data.cart_item_id != "")
			{
				jQuery(this).attr("data-cart-item-id", data.cart_item_id);
			}
		});

		jQuery(".q2c-item-to-cart-section-"+data.pid).find(".qtc_item_count_inputbox").each(function (){
			jQuery(this).attr("id", jQuery(this).attr("id").replace("-tmp", ""));
			jQuery(this).attr("id", jQuery(this).attr("id")+"-tmp");
			jQuery("#"+jQuery(this).attr("id")).val(data.qty);

			if (data.cart_item_id != undefined && data.cart_item_id != "")
			{
				jQuery(this).attr("data-cart-item-id", data.cart_item_id);
			}
		});
	}
	else
	{
		jQuery(".q2c-item-qtycount-increment-decrement-section-"+data.pid).hide();
		jQuery(".q2c-item-to-cart-section-"+data.pid).show();
		jQuery(".q2c-item-to-cart-section-"+data.pid +" .q2c-small-buy-button").attr('style' , 'display:block !important');

		jQuery(".q2c-item-qtycount-increment-decrement-section-"+data.pid).find(".qtc_item_count_inputbox").each(function (){
			jQuery(this).attr("id", jQuery(this).attr("id").replace("-tmp", ""));
			jQuery(this).attr("id", jQuery(this).attr("id")+"-tmp");
			jQuery("#"+jQuery(this).attr("id")).val(data.qty);

			jQuery(this).attr("data-cart-item-id", "");
		});

		jQuery(".q2c-item-to-cart-section-"+data.pid).find(".qtc_item_count_inputbox").each(function (){
			jQuery(this).attr("id", jQuery(this).attr("id").replace("-tmp", ""));
			if (!jQuery("#"+jQuery(this).attr("id")).val())
			{
				jQuery("#"+jQuery(this).attr("id")).val(1);
			}

			jQuery(this).attr("data-cart-item-id", "");
		});
	}
}

function qtc_decrement(input_field,pid,cartItemId,parent,slab,min_qtc,max_qtc)
{
	var limit = getlimit('min',pid,parent,min_qtc,max_qtc);
	var qty_el = document.getElementById(input_field);
	var qty = qty_el.value;
	slab = parseInt(slab);

	if (!isNaN(qty) && qty > limit)
	{
		qty = qty - slab;
		updateCartItemQty(qty_el, cartItemId, pid, qty);
	}
	else
	{
		qty = 0;
		updateCartItemQty(qty_el, cartItemId, pid, qty);
	}
}

function checkforalphaLimit(el,pid,parent,slab,min_qtc,max_qtc,min_violate_msg,max_violate_msg)
{
	var textval=Number(el.value);
	var minlim=getlimit('min',pid,parent,min_qtc,max_qtc)

	if(textval < minlim)
	{
		alert(min_violate_msg+minlim);
		el.value = minlim;
		return false;
	}

	var maxlim=getlimit('max',pid,parent,min_qtc,max_qtc)
	if(textval>maxlim)
	{
		alert(max_violate_msg+maxlim);
		el.value =maxlim;
		return false;
	}

	var slabquantity=textval%slab;

	if(slabquantity!=0)
	{
		alert(Joomla.JText._('COM_QUICK2CARET_SLAB_SHOULD_BE_MULT_MIN_QTY')+ slab);
		el.value = el.defaultValue;
		return false;
	}

	return true;
}

function qtc_fieldTypeChange(element,attContainerId)
{
	if(element.value == 'Textbox')
	{
		var parentdiv=document.getElementById(attContainerId);
		parentdiv.getElementsByClassName('qtc_attributeOpTable')[0].style.visibility='hidden';
		parentdiv.getElementsByClassName('qtc_attributeOpTable')[0].style.display='none';
	}
	else if(element.value == 'Select')
	{
		var parentdiv=document.getElementById(attContainerId);
		parentdiv.getElementsByClassName('qtc_attributeOpTable')[0].style.visibility='visible';
		parentdiv.getElementsByClassName('qtc_attributeOpTable')[0].style.display='';
	}
}

techjoomla.jQuery(function()
{
	/*show hide store owner option on hover*/
	techjoomla.jQuery(".techjoomla-bootstrap .product_wrapper").hover(
		function () {
			techjoomla.jQuery(this).find(".qtc_owner_opts").show();
		},
		function () {
			techjoomla.jQuery(this).find(".qtc_owner_opts").hide();
		}
	);

	/* Create slideshow instances*/
	if(techjoomla.jQuery("#gallery").length)
	{
		/* Declare variables*/
		var totalImages = techjoomla.jQuery("#gallery > li").length;
		if(totalImages >= 1)
		{
			var imageWidth = techjoomla.jQuery("#gallery > li:first").outerWidth(true),
			totalWidth = imageWidth * totalImages,
			visibleImages = Math.round(techjoomla.jQuery("#gallery-wrap").width() / imageWidth),
			visibleWidth = visibleImages * imageWidth,
			stopPosition = (visibleWidth - totalWidth);
			techjoomla.jQuery("#gallery").width(totalWidth);

			techjoomla.jQuery("#gallery-prev").click(function()
			{
				if(techjoomla.jQuery("#gallery").position().left < 0 && !techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery("#gallery").animate({left : "+=" + imageWidth + "px"});
					techjoomla.jQuery('#gallery-next').show();
					techjoomla.jQuery('#gallery-prev').show();
				}
				else if(!techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery('#gallery-prev').hide();
				}
				return false;
			});

			techjoomla.jQuery("#gallery-next").click(function()
			{
				if((techjoomla.jQuery("#gallery").position().left) > stopPosition && !techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery("#gallery").animate({left : "-=" + imageWidth + "px"});
					techjoomla.jQuery('#gallery-next').show();
					techjoomla.jQuery('#gallery-prev').show();
				}
				else if((techjoomla.jQuery("#gallery").position().left) < stopPosition)
				{
					techjoomla.jQuery("#gallery").position().left = stopPosition;
				}
				else if(!techjoomla.jQuery("#gallery").is(":animated"))
				{
					techjoomla.jQuery('#gallery-next').hide();
				}
				return false;
			});
		}
	}
});

function qtc_expirationChange(mediaNum)
{
	/* Get the DOM reference of bill details*/
	var downEle = techjoomla.jQuery('[name="prodMedia['+mediaNum+'][downCount]"]');
	var expEle = techjoomla.jQuery('[name="prodMedia['+mediaNum+'][expirary]"]');

	if(document.getElementsByName('prodMedia['+mediaNum+'][purchaseReq]')[0].checked)
	{
		if(downEle)
		{
			downEle.closest(".control-group").show();
		}
		if(expEle)
		{
			 expEle.closest(".control-group").show();
		}
	}
	else
	{
		if(downEle)
		{
			downEle.closest(".control-group").hide();
		}
		if(expEle)
		{
			 expEle.closest(".control-group").hide();
		}
	}
}

/* Funtion to Update shipping profile list*/
function qtcUpdateShipProfileList(store_id)
{
	techjoomla.jQuery.ajax({
		url:Joomla.getOptions('system.paths').base+'/index.php?option=com_quick2cart&task=product.qtcUpdateShipProfileList&tmpl=component&store_id='+store_id,
		type: 'GET',
		dataType: 'json',
		success: function(data)
		{
			techjoomla.jQuery('#qtc_shipProfileSelListWrapper').html(data.selectList);
		}
	});
}

/* This function load taxprofiles according to store id*/
function qtcLoadTaxprofileList(store_id, selected_taxid)
{
	techjoomla.jQuery.ajax({
		url:Joomla.getOptions('system.paths').root + '/index.php?option=com_quick2cart&task=product.getTaxprofileList&tmpl=component&store_id='+store_id+'&selected='+selected_taxid,
		type: 'GET',
		dataType: 'json',
		success: function(data)
		{
			techjoomla.jQuery('.taxprofile').html(data);
		}
	});
}

function qtcIsPresentSku(actUrl, skuele)
{
	techjoomla.jQuery.ajax({
		url: actUrl,
		cache: false,
		type: 'GET',
		success: function(data)
		{
			if (data == '1')
			{
				alert(Joomla.JText._('QTC_SKU_EXIST'));
				skuele.value="";
			}
			else
			{
				var tem='';
			}
		}
	});
}

function showHideNoteTextarea(ref, order_id)
{
	if(ref.checked == 1)
	{
		document.getElementById("order_note_"+order_id).style.display="block";
	}
	else
	{
		document.getElementById("order_note_"+order_id).style.display="none";
	}
}

/*
 * Editable cart item.
*/
function updateCartItemsAttribute(cart_item_id, item_id)
{
	//serialize
	var formData = techjoomla.jQuery('#adminForm').serialize();

	techjoomla.jQuery.ajax({
		url : Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=cartcheckout.update_cart_item&tmpl=component",
		type : 'POST',
		dataType : 'json',
		data:
		{
			'formData': formData,
			'cart_item_id' : cart_item_id,
			'item_id' : item_id
		},
		success : function(ret)
		{
			if (!ret.status)
			{
				alert(Joomla.JText._('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_FAIL') + "( " + ret.message + " )");
			}

			window.location.reload();
		},
		error : function (e)
		{
			console.log('Someting is wrong');
		}
	});
}

function removecart(id)
{
	techjoomla.jQuery.ajax({
		url: Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=removecart&tmpl=component&id="+id,
		type: "GET",
		success: function(msg)
		{
			window.location.reload();
		}
	});
}

function updateOrderItemAttribute(orderId , COM_QUICK2CART_ORDER_UPDATED)
{
	/* Serialize */
	var formData = techjoomla.jQuery('#orderItemForm').serialize();

	techjoomla.jQuery.ajax({
		url : Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=orders.updateOrderItemAttribute&tmpl=component",
		type : 'POST',
		dataType : 'json',
		data:
		{
			'order_id': orderId,
			'formData': formData
		},
		success : function(data)
		{
			window.location.reload();
		},
		error : function (e)
		{
			console.log('Someting is wrong');
		}
	});
}

/* This function is used for LOT/slab from add product view*/
function checkSlabValue()
{
	var slabvalue = techjoomla.jQuery('#item_slab').val();
	if(slabvalue==0)
	{
		alert(Joomla.JText._('COM_QUICK2CARET_LOT_VALUE_SHOULDNOT_BE_ZERO'));
		techjoomla.jQuery('#item_slab').val(1);
	}

	if(slabvalue!=1 && slabvalue!=0)
	{
		var minval=techjoomla.jQuery('#min_item').val();
		if(minval!='' && minval!=0 )
		{
			var Rem = minval % slabvalue;

			if(Rem!=0)
			{
				alert(Joomla.JText._('COM_QUICK2CARET_SLAB_MIN_QTY'));
				techjoomla.jQuery('#min_item').val(slabvalue);
			}
		}
	}
}

/* Check the slab value for while modifing min field */
function checkSlabValueField(el, allowed_ascii,enter_numerics )
{
	var alphaStatus = checkforalpha(el, allowed_ascii,enter_numerics );

	if (alphaStatus)
	{
		// Get lot quantity
		var slabvalue = techjoomla.jQuery('#item_slab').val();
		// Get item stock value
		var stockvalue = techjoomla.jQuery('#stock').val();
		if(slabvalue > 0)
		{
			var minval=techjoomla.jQuery('#'+ el.id).val();
			// Check if the minimum value is not empty, not zero, and is less than or equal to (slabValue * stockValue)
			if((minval != '') && (minval != 0) && (minval <= slabvalue * stockvalue))
			{
				var Rem = minval % slabvalue;
				if(Rem!=0)
				{
					alert(Joomla.JText._('COM_QUICK2CARET_SLAB_MIN_QTY'));
					techjoomla.jQuery('#'+ el.id).val(slabvalue);
				}
			}
			// If the minimum value exceeds the maximum allowable value
			else if(minval > slabvalue * stockvalue)
			{
				alert(Joomla.JText._('COM_QUICK2CART_SLAB_MAX_QTY'));
				techjoomla.jQuery('#'+ el.id).val(slabvalue * stockvalue);
			}
		}
	}
}

function qtc_expirationChange(mediaNum)
{
	// Get the DOM reference of bill details
	var downEle = techjoomla.jQuery('[name="prodMedia['+mediaNum+'][downCount]"]');
	var expEle = techjoomla.jQuery('[name="prodMedia['+mediaNum+'][expirary]"]');

	if (document.getElementsByName('prodMedia['+mediaNum+'][purchaseReq]')[0].checked)
	{
		if (downEle)
		{
			downEle.closest(".form-group").show();
		}

		if (expEle)
		{
			expEle.closest(".form-group").show();
		}
	}
	else
	{
		if (downEle)
		{
			downEle.closest(".form-group").hide();
		}

		if (expEle)
		{
			expEle.closest(".form-group").hide();
		}
	}
}

/* For Checkout view  */
function qtc_guestContinue(id)
{
	techjoomla.jQuery('#' + id).toggle('slow');
	techjoomla.jQuery('#qtc_ckout_billing-info').toggle('slow');
	qtcHideAndShowNextButton();
}

/* For Checkout view  */
function qtc_hideShowLoginTab(tab1, tab2)
{
	techjoomla.jQuery('#' + tab1).toggle();
	techjoomla.jQuery('#' + tab2).toggle();
	qtcHideAndShowNextButton();
}

/* For Checkout view  */
function qtcHideAndShowNextButton()
{
	var activeTabId = techjoomla.jQuery("#qtc-steps li[class='active']").attr("id");
	if (activeTabId == "qtc_billing")
	{
		/* When user using guest checkout */
		if (techjoomla.jQuery('#button-user-info').length > 0)
		{
			/* For guest checkout to hide billing tab or registration tab */
			if (techjoomla.jQuery('#qtc_ckout_billing-info').is(':visible'))
			{
				techjoomla.jQuery(".ad-form #btnWizardNext").show();
			}
			else
			{
				/* If order has been placed then don't hide */
				if (techjoomla.jQuery('#order_id').val())
				{
					techjoomla.jQuery(".ad-form #btnWizardNext").show();
				}
				else
				{
					techjoomla.jQuery(".ad-form #btnWizardNext").hide();
				}
			}
		}
	}
	else if (activeTabId == "qtc_cartDetails")
	{
		techjoomla.jQuery(".ad-form #btnWizardNext").show();
	}
}

/** PIN set up */
function QttPinArrange(random_containerId, columnWidth, itemSelector, pin_padding )
{
	var random_containerEle = document.getElementById(random_containerId);
	var msnry = new Masonry(random_containerEle, {
		columnWidth: columnWidth,
		itemSelector: itemSelector,
		gutter: pin_padding
	});
}

/** function to delete stored options on ajax **/
function deleteOption(optionId,q2coptremovebuttonId)
{
	var confirmdelete = confirm("Do you want to delete this attribute option?");

	if( confirmdelete == false )
	{
		return false;
	}

	var deleteclass = "q2cattributeoption"+optionId;
	var optionId = "&optionid=" + optionId;
	var url = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=globalattribute.deleteoption"+optionId+'&tmpl=component';
	techjoomla.jQuery.ajax({
		type: "get",
		url:url,
		async:false,
		success: function(response)
		{
			var message = JSON.parse(response);

			if(message[0].error)
			{
				alert(message[0].error);
			}
			else
			{
				techjoomla.jQuery("#"+q2coptremovebuttonId).parent().parent().parent().parent().remove();
				if (!techjoomla.jquery("#qtcoptionclone").length)
				{
					techjoomla.jquery('#qtcoptionheading').remove();
				}
			}
		},
		error: function(response)
		{
			alert("error");
			console.log(' ERROR!!' );
			return e.preventDefault();
		}
	});
}

/** function to check pincode availability on ajax **/
function checkPincode(item_id)
{
	var delivert_pincode = techjoomla.jQuery("#pincode").val()

	if (delivert_pincode == '')
	{
		alert('Enter pincode');
		return false;
	}

	var phoneno = /^\d*$/;
	if(!delivert_pincode.match(phoneno))
	{
		alert("Pincode must be numeric");
		return false;
	}

	var url = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=shipping.checkDeliveryAvailability&tmpl=component&item_id="+item_id+"&delivery_pincode="+delivert_pincode;
	techjoomla.jQuery.ajax({
		type: "get",
		url:url,
		//beforeSend: function(){
		/*code to append loading image mean while data is recived from ajax method*/
		//techjoomla.jQuery( "<center><img id='loadimg' src='http://blog.teamtreehouse.com/wp-content/uploads/2015/05/InternetSlowdown_Day.gif'></img></center>" ).appendTo( ".availabilitystatus" );
		//},
		success: function(response)
		{
			response = JSON.parse(response);

			techjoomla.jQuery('.availabilitystatus').empty();
			if (response.priority == 1)
			{
				techjoomla.jQuery('.availabilitystatus').append('<p>priority Available</p>');
			}

			if (response.standard == 1)
			{
				techjoomla.jQuery('.availabilitystatus').append('<p>standard Available</p>');
			}

			if (response.economy == 1)
			{
				techjoomla.jQuery('.availabilitystatus').append('<p>economy Available</p>');
			}

			if (response == "")
			{
				techjoomla.jQuery('.availabilitystatus').empty();
				techjoomla.jQuery('.availabilitystatus').append('<p>Not Available</p>');
			}
		},
		error: function(response)
		{
			alert("error");
			console.log(' ERROR!!' );
			return e.preventDefault();
		}
	});
}

/** function to resend email invoice**/
function qtcSendInvoiceEmail(callurl)
{
	techjoomla.jQuery.ajax({
		url: callurl + '&' + Joomla.getOptions('csrf.token') + '=1',
		beforeSend: function(){
			openModal();
		},
		type: "GET",
		cache: false,
		success: function(data)
		{
			closeModal();
			alert(data);
		}
	});
}

function openModal()
{
	document.getElementById('q2c-ajax-call-loader-modal').style.display = 'block';
	document.getElementById('q2c-ajax-call-fade-content-transparent').style.display = 'block';
}

function closeModal()
{
	document.getElementById('q2c-ajax-call-loader-modal').style.display = 'none';
	document.getElementById('q2c-ajax-call-fade-content-transparent').style.display = 'none';
}

function cart_applycoupon(enterCopMsg, copFieldselector)
{
	if (techjoomla.jQuery(copFieldselector).val() =='')
	{
		alert(enterCopMsg);
	}
	else
	{
		var coupon_code=techjoomla.jQuery(copFieldselector).val();

		techjoomla.jQuery.ajax({
			url: Joomla.getOptions('system.paths').base+'/index.php?option=com_quick2cart&task=cartcheckout.isExistPromoCode&tmpl=component&coupon_code='+coupon_code,
			type: 'GET',
			dataType: 'json',
			success: function(data) {
				amt=0;
				val=0;
				if (data != 0)
				{
					window.location.reload();
				}
				else
				{
					alert(enterCopMsg);
					techjoomla.jQuery('#cart_coupon_code').val('');
				}
			}
		});
	}
}

function show_cop(coupanexist)
{
	if (techjoomla.jQuery('#coupon_chk').is(':checked'))
	{
		techjoomla.jQuery('#cop_tr').show();
	}
	else
	{
		var cop_notempty=techjoomla.jQuery('#coupon_code').val();

		/* no coupan entered or coupan  present in session */
		if (coupanexist)
		{
			remove_cop();
		}
		else
		{
		techjoomla.jQuery('#cop_tr').hide();
		}
	}
}

function remove_cop()
{
	var flag= confirm(Joomla.JText._('QTC_U_R_SURE_TO_REMOVE_COP'));

	if (flag==true)
	{
		techjoomla.jQuery.ajax({
			url: Joomla.getOptions('system.paths').base+'/index.php?option=com_quick2cart&task=cartcheckout.clearcop&tmpl=component',
			cache: false,
			type: 'GET',
			success: function(msg)
			{
				window.location.reload();
			}
		});
	}
}

function q2cShowFilter()
{
	techjoomla.jQuery("#q2chorizontallayout").toggle();
}

function exportOrdersCsv()
{
	var url = Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&task=orders.payment_csvexport&tmpl=component&"+Joomla.getOptions('csrf.token')+"=1";
	window.location.href = url;
}

function processOrder()
{
    placeOrder();
    document.getElementById("qtc-co-place-order").style.display = "none";
}

function placeOrder(freeOrder = 0)
{
	techjoomla.jQuery('#task').val("cartcheckout.save");
	var values   = techjoomla.jQuery('#adminForm').serialize();
	var checkMsg = validateExtraFields();

	if (checkMsg)
	{
		alert(checkMsg);

		return false;
	}

	if (freeOrder == 0)
	{
		var selectedPaymentGateway = techjoomla.jQuery("input[name='gateways']:checked").val();

		if (selectedPaymentGateway == undefined)
		{
			alert(Joomla.JText._('COM_QUICK2CART_SELECT_PAYMENT_OPTION'));

			return false;
		}
	}

	var callurl = Joomla.getOptions('system.paths').base+ "/index.php?option=com_quick2cart&task=cartcheckout.save&tmpl=component";

	techjoomla.jQuery.ajax({
		url: callurl,
		beforeSend: function(){
			techjoomla.jQuery("#qtc-co-place-order").addClass('disabled');
			techjoomla.jQuery("#qtc-co-place-order").attr('disabled', 'disabled');
			techjoomla.jQuery("#qtc-co-place-order-loader").removeClass('hidden');
		},
		type: "POST",
		data:values,
		cache: false,
		success: function(data)
		{
			data = JSON.parse(data);

			if (data.success)
			{
				techjoomla.jQuery("#qtc-co-place-order").removeClass('disabled');
				techjoomla.jQuery("#qtc-co-place-order").removeAttr('disabled');
				techjoomla.jQuery("#qtc-co-place-order-loader").hide();

				// Disable other tabs once the order is placed
				techjoomla.jQuery('.q2c-wrapper #qtc_shippingStep').removeClass('complete');
				techjoomla.jQuery('.q2c-wrapper #qtc_billing').removeClass('complete');
				techjoomla.jQuery('.q2c-wrapper #qtc_cartDetails').removeClass('complete');
				techjoomla.jQuery('.q2c-wrapper #step2').html('</br><div class="alert alert-info">'+Joomla.JText._("COM_QUICK2CART_NOT_ALLOWED_TO_CHANGE_SHIPPING_ADDRESS")+'</div>');

				// Save order id in hidded form and add payment page html
				techjoomla.jQuery('#order_id').val(data.order_id);

				// As order is placed, buyer could not edit cart
				techjoomla.jQuery('#qtc_step1_cartdetail').remove();

				// show "you could not edit cart" msg
				techjoomla.jQuery('#qtc_cartStepAlert').html("</br><div class=\"alert alert-info\">"+qtc_cartAlertMsg+"</div>");
				techjoomla.jQuery('#qtc_billing_alert_on_order_placed').html("</br><div class=\"alert alert-info\">"+Joomla.JText._('COM_QUICK2CART_SHIPPING_ADDRESS_ERROR_MSG')+"</div>");
				techjoomla.jQuery('#qtc_ckout_billing-info').hide();
				techjoomla.jQuery('#qtcShippingMethTab').remove();
				techjoomla.jQuery('#qtc_shipStepAlert').html("</br><div class=\"alert alert-info\">"+qtc_shipMethRemovedMsg+"</div>");

				// Get Payment HTML
				$imgpath = "/components/com_quick2cart/assets/images/ajax.gif";

				if (freeOrder == 1)
				{
					completeFreeOrder(data.order_id);
				}
				else
				{
					qtc_gatewayHtml(selectedPaymentGateway, data.order_id, $imgpath);
				}
			}
			else
			{
				if (data.success_msg != undefined && data.success_msg != '')
				{
					alert(data.success_msg);
					location.reload();
				}
				else
				{
					if (data.msg != undefined && data.msg != '')
					{
						alert(data.msg);
						location.reload();
					}
				}
			}
		}
	});
}

function completeFreeOrder(orderId)
{
	techjoomla.jQuery('#task').val("cartcheckout.processFreeOrder");
	var values  = techjoomla.jQuery('#adminForm').serialize();
	var callurl = Joomla.getOptions('system.paths').base+ "/index.php?option=com_quick2cart&task=cartcheckout.processFreeOrder&tmpl=component";

	techjoomla.jQuery.ajax({
		url: callurl,
		type: "POST",
		data: values,
		cache: false,
		success: function(data)
		{
			data = JSON.parse(data);

			if (data.link)
			{
				top.location.href = data.link;
			}
		}
	});
}

function validateDiscountPrice(currency)
{
	var productDiscountPrice = Number(techjoomla.jQuery('#disc_price_'+currency).val());
	var productOriginalPrice = Number(techjoomla.jQuery('#price_'+currency).val());

	if ((productDiscountPrice > 0) && (productDiscountPrice > productOriginalPrice))
	{
		alert(Joomla.JText._('COM_QUICK2CARET_ORIGINAL_PRICE_LESS_THAN_DISCOUNT_PRICE'));
		techjoomla.jQuery('#disc_price_'+currency).val(0);
	}
}
