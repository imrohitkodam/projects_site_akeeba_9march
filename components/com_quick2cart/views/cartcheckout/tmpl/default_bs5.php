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
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.tooltip');

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('behavior.framework');
}

HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$root_url = Uri::root();
$document = Factory::getDocument();

if (!class_exists('comquick2cartHelper'))
{
	//require_once $path;
	$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
	JLoader::register('comquick2cartHelper', $path);
	JLoader::load('comquick2cartHelper');
}

$getLanguageConstantForJs = comquick2cartHelper::getLanguageConstantForJs();
$stepjs_initalization="
var  qtc_cartAlertMsg =\"".Text::_("COM_QUICK2CART_COULD_NOT_CHANGE_CART_DETAIL_NOW")."\";
var  qtc_shipMethRemovedMsg =\"".Text::_("COM_QUICK2CART_COULD_NOT_CHANGE_SHIPMETH_DETAIL_NOW")."\";
";
$document->addScriptDeclaration($stepjs_initalization);

HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/fuelux2.3.1.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/qtc_steps.css');

$user             = Factory::getUser();
$app              = Factory::getApplication();
$jinput           = $app->input;
$entered_numerics = "'".Text::_('QTC_ENTER_NUMERICS')."'";

// DECIDE WHETHER TERMS & CONDITON HAVE TO USE
$showCheckoutTermsConditions = $this->params->get('termsConditons', 0);
$checkoutTermsCondArtId      = $this->params->get('termsConditonsArtId', 0);
$isShippingEnabled           = $this->params->get('shipping', 0);
$shippingMode                = $this->params->get('shippingMode', 'itemLevel');
$termsCondArtId              = trim($checkoutTermsCondArtId) ;
$showTersmAndCond            = (!empty($showCheckoutTermsConditions) && !empty($checkoutTermsCondArtId)) ? 1 : 0;
$productHelper               =  new productHelper;

if ($this->onBeforeCheckoutViewDisplay)
{
	echo $this->onBeforeCheckoutViewDisplay;
}

if (empty($this->cart))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?> quick2cart_coat">
		<div class="alert alert-danger">
			<span><?php echo Text::_("QTC_EMPTY_CART");?></span>
		</div>
	</div>
	<?php
	return false;
}
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?> quick2cart_coat" >
	<div>
		<h1><strong><?php echo Text::_('QTC_CHECKOUT');?></strong></h1>
	</div>
	<?php
	$showoptioncol = 0;

	foreach ($this->cart as $citem)
	{
		if (!empty($citem['options']))
		{
			// Atleast one found then show
			$showoptioncol = 1;
			break;
		}
	}

	$userbill = (isset($this->userdata['BT']))?$this->userdata['BT']:'';
	$usership = (isset($this->userdata['ST']))?$this->userdata['ST']:'';
	$baseurl  = Route::_ (Uri::root().'index.php');

	/*  ON setting useGuestCheckoutOnly= 1.registration tab hide(cart detail will display),coupon div hide,*/
	$qtc_hideregistrationTabFlag = $this->params->get('useGuestCheckoutOnly',0);

	// for getting current tab status one page chkout::
	$session       = Factory::getSession();
	$qtc_tab_state = $session->get('one_pg_ckout_tab_state');

	$js = "
	var isgst=".$this->params->get('guest').";
	var qtc_baseurl='".$baseurl."';
	var statebackup;

	techjoomla.jQuery('#dis_cop').hide();
		// used in new checkout
		function validateExtraFields()
		{
			var showTersmAndCond =".$showTersmAndCond.";

			if (showTersmAndCond)
			{
				// TERMS AND CONDITION
				// If (adminForm.qtc_accpt_terms.checked == false)
				if (document.getElementById('qtc_accpt_terms').checked)
				{
				}
				else
				{  // not checked
					return \"".Text::_('COM_QUICK2CART_TERMS_CONDITION_ALERT_MSG')."\";
				}
			}
		}
	";
	$document->addScriptDeclaration($js);

	$js = "Joomla.submitbutton = function(pressbutton){
			show_ship();
			submitform(pressbutton);
			return;
		}

		function submitform(pressbutton){
			 if (pressbutton) {
				document.adminForm.task.value = pressbutton;
			 }
			 if (typeof document.adminForm.onsubmit == 'function') {
				document.adminForm.onsubmit();
			 }
				document.adminForm.submit();
		}
	";
	$document->addScriptDeclaration($js);
	?>

	<script type="text/javascript">
		var tjWindowWidth = techjoomla.jQuery('div#qtc_mainwrapper').width();

		function loadingImage()
		{
			jQuery('<div id="appsloading"></div>')
			.css("background", "rgba(255, 255, 255, .8) url('"+root_url+"components/com_quick2cart/assets/images/ajax.gif') 50% 15% no-repeat")
			.css("top", jQuery('#TabConetent').position().top - jQuery(window).scrollTop())
			.css("left", jQuery('#TabConetent').position().left - jQuery(window).scrollLeft())
			.css("width", jQuery('#TabConetent').width())
			.css("height", jQuery('#TabConetent').height())
			.css("position", "fixed")
			.css("z-index", "1000")
			.css("opacity", "0.80")
			.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
			.css("filter", "alpha(opacity = 80)")
			.appendTo('#TabConetent');
		}

		function hideImage()
		{
			techjoomla.jQuery('#appsloading').remove();
		}
		// for new checkout
		function qtc_chkValidStep(stepId)
		{
			// for billing tab
			if (stepId=="qtc_billing" && techjoomla.jQuery("#adminForm").length)
			{
				var  qtcBillForm = document.adminForm;

				if (document.formvalidator.isValid(qtcBillForm))
				{
					// return true;
				}
				else
				{
					jQuery("html, body").animate({ scrollTop: 0 }, "slow");
					return false;
				}
			}

			return true;
		}

		// For new checkout
		function qtc_gatewayHtml(ele,orderid,loadingImgPath)
		{
			techjoomla.jQuery.ajax({
				url: '?option=com_quick2cart&task=cartcheckout.qtc_gatewayHtml&tmpl=component&gateway='+ele+'&order_id='+orderid,
				type: 'POST',
				data:'',
				dataType: 'text',
				beforeSend: function()
				{
					var loadMsg = "<?php echo Text::_( "QTC_PAYMENT_GATEWAY_LOADING_MSG" , true); ?>";
					techjoomla.jQuery('#qtc_paymentGatewayList').after('<div class=\"com_quick2cart_ajax_loading\"><div class=\"com_quick2cart_ajax_loading_text\">'+loadMsg+' </div><img class=\"com_quick2cart_ajax_loading_img\" src="'+root_url+'components/com_quick2cart/assets/images/ajax.gif"></div>');
				},
				complete: function() {
					techjoomla.jQuery('.com_quick2cart_ajax_loading').remove();

				},
				success: function(data)
				{
					if (data)
					{
						techjoomla.jQuery('#qtc_payHtmlDiv').html(data);
						techjoomla.jQuery('#qtc_payHtmlDiv div.form-actions input[type="submit"]').addClass('float-end');
						techjoomla.jQuery('html, body').animate({
							'scrollTop' : techjoomla.jQuery("#qtc-co-place-order").position().top
						});

						techjoomla.jQuery("input[name=gateways]").on("change", function()
						{
							if (techjoomla.jQuery("#qtc_payHtmlDiv").length > 0)
							{
								techjoomla.jQuery("#qtc_payHtmlDiv").html("");
							}
						});
					}
				}
			});
		}

		function show_ship()
		{
			var totalprice = techjoomla.jQuery('#total_after_tax').val();

			if (techjoomla.jQuery('#ship_chk').is(':checked'))
			{
				techjoomla.jQuery('.ship_tr').hide();
				techjoomla.jQuery.each(techjoomla.jQuery('.bill'),function()
				{
					var bval = techjoomla.jQuery(this).val();
					var bid = techjoomla.jQuery(this).attr('id');

					/*when we r going to copy value from state to ship_stateid select box*/
					if (bid=='country')
					{
						generateoption(statebackup,'ship_country','')
					}
					techjoomla.jQuery('#ship_'+bid).val(bval);
				});

				/*changeFormClass('form-horizontal', 2);*/
				changeWidthClass('col-md-12');
			}
			else if(techjoomla.jQuery('#ship_chk').length == 0)
			{
				changeWidthClass('col-md-12');
				/*changeFormClass('form-horizontal', 1);*/
			}
			else
			{
				techjoomla.jQuery('.ship_tr').show();
				/*changeFormClass('form-vertical', 1);*/
				changeWidthClass('col-md-6');
			}
		}

		function generateState(countryId,Dbvalue)
		{
			var country=techjoomla.jQuery('#'+countryId).val();

			if (country==undefined)
			{
				return (false);
			}

			techjoomla.jQuery.ajax({
				url: '?option=com_quick2cart&task=cartcheckout.loadState&tmpl=component&country='+country,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					if (countryId=='country')
					{
						statebackup=data;
						show_ship();
					}
					generateoption(data,countryId,Dbvalue);
				}
			});
		}

		function generateoption(data,countryId,Dbvalue)
		{
			var country=techjoomla.jQuery('#'+countryId).val();
			var options, index, select, option;

			// add empty option according to billing or shipping
			if (countryId=='country')
			{
				select = techjoomla.jQuery('#state');
				default_opt = "<?php echo Text::_('QTC_BILLIN_SELECT_STATE')?>";
			}
			else
			{
				select = techjoomla.jQuery('#ship_state');
				default_opt = "<?php echo Text::_('QTC_SHIPIN_SELECT_STATE')?>";
			}

			// REMOVE ALL STATE OPTIONS
			select.find('option').remove().end();

			// To give msg TASK  "please select country START"
			var selected="selected=\"selected\"";
			var op='<option '+selected+' value="">'  +default_opt+   '</option>'     ;

			if (countryId=='country')
			{
				techjoomla.jQuery('#state').append(op);
			}
			else
			{
				techjoomla.jQuery('#ship_state').append(op);
			}

			if (data)
			{
				options = data.options;
				for (index = 0; index < data.length; ++index)
				{
					var opObj = data[index];
					selected="";

					if (opObj.id==Dbvalue)
					{
						selected="selected=\"selected\"";
					}
					var op='<option '+selected+' value=\"'+opObj.id+'\">'  +opObj.region+   '</option>';

					if (countryId=='country')
					{
						techjoomla.jQuery('#state').append(op);
					}
					else
					{
						techjoomla.jQuery('#ship_state').append(op);
					}
				}
			}
		}

		techjoomla.jQuery(document).ready(function()
		{
			techjoomla.jQuery(".bill").bind("change",show_ship);

			var DBuserbill="<?php echo (isset($userbill->state_code))?$userbill->state_code:''; ?>";
			var DBusership="<?php echo (isset($usership->state_code))?$usership->state_code:''; ?>";
			var tax_tot = techjoomla.jQuery('#total_after_tax').val();
			generateState("country",DBuserbill) ;
			setTimeout(function(){
					generateState("ship_country",DBusership) ;
				},1000);

			show_ship();
			techjoomla.jQuery('.discount').popover();
		});

		function caltotal(totalpriceid,amt,minqty,maxqty,minmsg,maxmsg,obj)
		{
			if (obj.value < minqty)
			{
				alert(minmsg+" "+minqty);
				obj.value=minqty;
				return false;
			}
			else if (obj.val() > maxqty)
			{
				alert(maxmsg+" "+maxqty);
				obj.val()=maxqty;
				return false;
			}

			var correct = checkforalpha(obj,'',"<?php echo Text::_('QTC_ENTER_NUMERICS')  ?>");
			if (correct)
			{
				update_cart();
			}
		}

		function update_cart()
		{
			var cartfields=techjoomla.jQuery('.cart_fields').serializeArray();

			techjoomla.jQuery.ajax({
				url: "?option=com_quick2cart&task=updatecart&tmpl=component",
				type: "POST",
				data:  cartfields,
				dataType: 'json',
				success: function(data)
				{
					window.location.reload();
				}
			});
		}

		function calculateship(totalprice)
		{
			var ship_country = techjoomla.jQuery('#ship_country').val();
			var ship_state   = techjoomla.jQuery('#ship_state').val();
			var ship_city    = techjoomla.jQuery('#ship_city').val();
			var data         = new Array();

			if (ship_country && ship_state && ship_city)
			{
				var saveData        = {};
				saveData.totalprice = totalprice;
				saveData.country    = ship_country;
				saveData.region     = ship_state;
				saveData.city       = ship_city;
				var jsondata        = JSON.stringify(saveData);

				techjoomla.jQuery.ajax({
					url: '?option=com_quick2cart&task=cartcheckout.calFinalShipPrice&tmpl=component',
					type: 'POST',
					dataType: 'json',
					data:{data : jsondata},
					success: function(shipprice)
					{
						if (shipprice['charges'] && shipprice['totalamt'])
						{
							var tax=document.getElementById("ship_amt");
							var netamt=document.getElementById("after_ship_amt");
							var final_amt=document.getElementById("after_ship_amt");

							if (tax && netamt && final_amt)
							{
								tax.innerHTML =shipprice['charges'];
								netamt.innerHTML =shipprice['totalamt'];
								final_amt.innerHTML =shipprice['totalamt'];
							}
						}
					}
				});
			}
		}

		function chkbillmail(email)
		{
			var userid="<?php echo $user->id;?>";

			// if user is not logged in
			if (userid > 0)
			{
				return (false);
			}

			var status=false;
			techjoomla.jQuery.ajax({
				url: '?option=com_quick2cart&task=cartcheckout.chkbillmail&tmpl=component&email='+email,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					if (data[0] == 1)
					{
						var duplicateemail="<div class=\"alert alert-danger qtc_removeBottomMargin \">"+data[1]+"</div>";
						techjoomla.jQuery('#billmail_msg').html(duplicateemail);
						techjoomla.jQuery("input[type=submit]").attr("disabled", "disabled");
						techjoomla.jQuery('#qtc_billmail_msg_div').show();
						status=false;
					}
					else
					{
						techjoomla.jQuery('#billmail_msg').html('');
						techjoomla.jQuery("input[type=submit]").removeAttr("disabled");
						techjoomla.jQuery('#qtc_billmail_msg_div').hide();
						status=true;
					}
				}
			});

			return (status);
		}

		// Added by Sneha, take to user details page when entered registered email on billing
		function chkbillmailregistered(email){
			var userid="<?php echo $user->id;?>";

			if (userid > 0)
			{
				return (false);
			}

			var status=false;
			techjoomla.jQuery.ajax({
				url: '?option=com_quick2cart&task=cartcheckout.chkbillmail&tmpl=component&email='+email,
				type: 'GET',
				dataType: 'json',
				success: function(data)
				{
					if (data[0] == 1)
					{
						var duplicateemail="<div class=\"alert alert-danger qtc_removeBottomMargin \">"+data[1]+"</div>";

						// Get login type (guest or registration)
						var guestck = "";
						guestck     = techjoomla.jQuery('input[name=qtc_guest_regis]:checked').val();

						if(guestck == 'guest')
						{
							if (confirm("<?php echo Text::_("COM_QUICK2CART_R_U_SURE_U_WANT_USE_SAMEEMAIL")?>"))
							{
								// Show user info block
								techjoomla.jQuery('#qtc_user-info').slideUp('slow');
							}
							else
							{
								// Show user info block
								techjoomla.jQuery('#qtc_user-info').slideDown('slow');

								// Show msg
								techjoomla.jQuery('#qtc_loginmail_msg_div').show();
								goToByScroll('qtc_user-info');
							}
						}
						else if(guestck == 'register')
						{
							alert("<?php echo Text::_("QTC_BILLMAIL_EXISTS")?>");
							// Show user info block
							techjoomla.jQuery('#qtc_user-info').slideDown('slow');

							// Show msg
							techjoomla.jQuery('#qtc_loginmail_msg_div').show();
							goToByScroll('qtc_user-info');
						}

						techjoomla.jQuery('#loginmail_msg').html(duplicateemail);
					}
					else
					{
						techjoomla.jQuery('#billmail_msg1').html('');
						techjoomla.jQuery("input[type=submit]").removeAttr("disabled");
						techjoomla.jQuery('#qtc_billmail_msg_div1').hide();
						status=true;
					}
				}
			});

			return (status);
		}

		// This is a functions that scrolls to #{blah}link
		function goToByScroll(id)
		{
			techjoomla.jQuery('html,body').animate({
				scrollTop: techjoomla.jQuery("#"+id).offset().top},
				'slow'
			);
		}

		function addEditLink(selectorObj)
		{
			var objId = techjoomla.jQuery(selectorObj).attr('id');
			techjoomla.jQuery(selectorObj).append('<a class="qtc_editTab" onclick="qtc_hideshowTabs(this)"><?php echo Text::_('COM_QUICK2CART_EDIT'); ?></a>');
		}

		function qtc_hideAllEditLinks()
		{
			techjoomla.jQuery("a.qtc_editTab").hide();
		}

		function qtc_showAllEditLinks()
		{
			techjoomla.jQuery("a.qtc_editTab").show();
		}

		function qtc_checkoutMethod(obj)
		{
			var regType=techjoomla.jQuery(obj).val();
			if (regType)
			{
				techjoomla.jQuery.ajax({
					url: '?option=com_quick2cart&task=cartcheckout.setCheckoutMethod&tmpl=component&regType='+regType,
					type: 'POST',
					dataType: 'json',
					success: function(shipprice)
					{}
				});
			}
		}

		techjoomla.jQuery(document).ready(function(){
			techjoomla.jQuery(".checkout-content").hide();
			var current_state="<?php echo $qtc_tab_state;?>";

			// if NOT null, undefine NaN,"",0,false
			if (current_state) {
				// on refresh go to current tab
				switch (current_state)
				{
					case 'qtc_cart':
							techjoomla.jQuery("#qtc_cartInfo-tab").show();
							// ADDING EDIT LINK TO LOGIN TAB
							addEditLink(techjoomla.jQuery('#user-info .checkout-heading'));
					break;
				}
			}
			else
			{
				var userid=techjoomla.jQuery('#userid').val();
				var hideRegTab="<?php echo $qtc_hideregistrationTabFlag;?>";
				if (parseInt(userid)==0 && hideRegTab==0)
				{
					//LOGGED IN
					techjoomla.jQuery(".checkout-first-step-user-info").show();
				}
				else
				{
					techjoomla.jQuery(".checkout-first-step-cart-info").show();
				}
			}
		});

		function qtc_hideshowTabs(obj)
		{
			techjoomla.jQuery('.checkout-content').slideUp('slow');
			var parentid=techjoomla.jQuery(obj).parent().parent().attr('id');
			goToByScroll(parentid);
			techjoomla.jQuery(obj).parent().parent().find('.checkout-content').slideDown('slow');

			// hide ckout error msg
			techjoomla.jQuery('#qtcShowCkoutErrorMsg').hide();
		}

		function qtc_ckpg_login(objid)
		{
			var d= techjoomla.jQuery('#qtc_user-info #login :input');

			techjoomla.jQuery.ajax({
				url: qtc_baseurl+'?option=com_quick2cart&task=registration.login_validate&tmpl=component',
				type: 'post',
				data: techjoomla.jQuery('#qtc_user-info #login :input'),
				dataType: 'json',
				beforeSend: function() {
					techjoomla.jQuery('#button-login').attr('disabled', true);
					techjoomla.jQuery('#button-login').after('<span class="wait">&nbsp;Loading..</span>');
				},
				complete: function() {
					techjoomla.jQuery('#button-login').attr('disabled', false);
					techjoomla.jQuery('.wait').remove();
				},
				success: function(json)
				{
					if (typeof json.error !== 'undefined')
					{
						alert(json.error.warning);
					}
					else
					{
						window.location.reload();
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
				}
			});
		}

		//function for validation of overview
		function open_div(geo,camp)
		{
			btnWizardNext();
		}

		function changeFormClass(newClass, multiplier)
		{
			tjWindowWidth = multiplier * techjoomla.jQuery('#q2c_billing').width();

			if (newClass=='form-vertical')
			{
				techjoomla.jQuery('div#qtc_mainwrapper').removeClass('form-horizontal');
				techjoomla.jQuery('div#qtc_mainwrapper').addClass('form-vertical');
			}
			if (newClass=='form-horizontal')
			{
				techjoomla.jQuery('div#qtc_mainwrapper').removeClass('form-vertical');
				techjoomla.jQuery('div#qtc_mainwrapper').addClass('form-horizontal');
			}
		}

		function changeWidthClass(newClass)
		{
			if (newClass=='col-md-12')
			{
				techjoomla.jQuery('div#q2c_billing').removeClass('col-md-6');
				techjoomla.jQuery('div#q2c_billing').addClass('col-md-12');
			}

			if (newClass=='col-md-6')
			{
				techjoomla.jQuery('div#q2c_billing').removeClass('col-md-12');
				techjoomla.jQuery('div#q2c_billing').addClass('col-md-6');
			}
		}
	</script>
	<?php
	$helperobj           = new comquick2cartHelper;
	$comquick2cartHelper = new comquick2cartHelper;

	if (!$user->id)
	{
		$ssession = Factory::getSession();
		$jinput   = $app->input;
		$itemid   = $jinput->get('Itemid');
		$cart     = $ssession->get('cart'.$user->id);
		$ssession->set('cart_temp',$cart);
		$ssession->set('socialadsbackurl', $_SERVER["REQUEST_URI"]);
	}

	$document->addScriptDeclaration($js);
	?>
	<script type="text/javascript">
	techjoomla.jQuery(document).ready(function(){
		var width = techjoomla.jQuery(window).width();
		var height = techjoomla.jQuery(window).height();
		techjoomla.jQuery('a#modal_billform').attr('rel','{handler: "iframe", size: {x: '+(width-(width*0.30))+', y: '+(height-(height*0.12))+'}}');
	});
	window.closeAddressModal = function(closeModalId){
		jQuery('#' + closeModalId).modal('hide');
	};
	</script>
	<div class="">
		<div class="ad-form">
			<div class="fuelux wizard-example">
				<div class="sa_steps_parent ">
					<div id="MyWizard" class="">
						<?php $s=1; ?>
						<ol class="qtc-steps-ol d-sm-flex steps clearfix" id="qtc-steps">
							<li id="qtc_cartDetails" data-target="#step1" class="active">
								<span class="badge"><?php echo $s++; ?></span>
								<span><?php echo Text::_('COM_QUICK2CART_CART_INFO'); ?></span>
							</li>
							<li id="qtc_billing" data-target="#step2" >
								<span class="badge"><?php echo $s++; ?></span>
								<span><?php echo Text::_('COM_QUICK2CART_BILLING_INFO'); ?></span>
							</li>
							<?php
							if ($isShippingEnabled && $shippingMode != "orderLeval")
							{
								?>
								<li id="qtc_shippingStep" data-target="#step3" >
									<span class="badge"><?php echo $s++; ?></span>
									<span><?php echo Text::_('COM_QUICK2CART_SHIP_INFO'); ?></span>
								</li>
							<?php
							} ?>
							<li id="qtc_summaryAndPay" data-target="#step4">
								<span class="badge"><?php echo $s++; ?></span>
								<span><?php echo Text::_('COM_QUICK2CART_PAYMENT_INFO')?></span>
							</li>
						</ol>
					</div>
				</div>
				<div id="TabConetent" class="tab-content step-content">
					<form method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class=" form-validate" onsubmit="return validateForm();">
						<div class="tab-pane step-pane active" id="step1">
							<div id="qtc_step1_cartdetail">
								<?php
								$comquick2cartHelper = new comquick2cartHelper;
								$cartpath            = $comquick2cartHelper->getViewpath('cartcheckout','default_cartdetail_bs5');
								$bootstrapVersion    = $this->params->get("bootstrap_version","bs3","STRING");

								$data                = new stdclass;
								$data->cart          = $this->cart;
								$data->coupon        = $this->coupon;
								$data->showoptioncol = $showoptioncol;
								$data->promotions    = !empty($this->promotions) ? $this->promotions : array();

								$data->applicablePromotionsList = $this->applicablePromotionsList;
								$data->promotionDescription = $this->promotionDescription;
								$layoutName = "cartcheckout." . $bootstrapVersion . ".cart_checkout";
								$layout     = new FileLayout($layoutName);
								$response   = $layout->render($data);
								echo $response;
								?>
							</div>
							<div  id="qtc_cartStepAlert"></div>
						</div>

						<input type="hidden" name="order_id" id="order_id" value="" />

						<div class="tab-pane step-pane" id="step2">
							<div class="qtcAddBorder">
								<?php
								$html                = "";
								$comquick2cartHelper = new comquick2cartHelper;
								ob_start();
								$path = $comquick2cartHelper->getViewpath('registration','default_bs5');
								include_once($path);
								$html = ob_get_contents();
								ob_end_clean();
								echo $html;

								// If user is not logged-in then dont show addresses
								if (!empty($user->id))
								{
									?>
									<div class="row">
										<?php
										if (!empty($this->addressesListHtml))
										{
										?>
											<div class="col-xs-12">
												<h3 class="checkout-addresses">
													<?php echo Text::_("COM_QUICK2CART_CREATE_ORDER_SELECT_ADDRESS");?>
												</h3>
											</div>
											<div id="qtc_user_addresses" class="checkout-addresses col-xs-12">
												<div class="row qtc_user_addresses_wrapper">
													<?php echo $this->addressesListHtml;?>
												</div>
											</div>
										<?php
										}
										else 
										{
											?>
												<div class="col-xs-12">
													<h3 class="checkout-addresses checkout-addresses-select-message d-none">
														<?php echo Text::_("COM_QUICK2CART_CREATE_ORDER_SELECT_ADDRESS");?>
													</h3>
												</div>

												<div id="qtc_user_addresses" class="checkout-addresses col-xs-12">
													<div class="row qtc_user_addresses_wrapper">
													</div>
												</div>
											<?php
										}
										?>
									</div>
									<div class="clearfix">&nbsp;</div>
									<div class="row">
										<div class="container-fluid">
											<div class="qtcadd_address_button center">
												<a class="btn btn-success" onclick="addAddress('<?php echo JUri::root();?>' , 'addAddressModal')">
													<?php echo Text::_('COM_QUICK2CART_ADD_CUSTOMER_ADDRESS');?>
												</a>
												<?php
												$link = Route::_('index.php?option=com_quick2cart&view=customer_addressform&userid=');
												$link = $link. '&tmpl=component';
												echo HTMLHelper::_(
													'bootstrap.renderModal',
													'addAddressModal',
													array(
														'title'		=> Text::_('COM_QUICK2CART_ADD_CUSTOMER_ADDRESS'),
														'url'        => $link,
														'modalWidth' => '80',
														'bodyHeight' => '70',
														'height'     => '100%',
														'width'      => '100%'
													)
												)
												?>
											</div>
										</div>
									</div>
									<?php
								}
								else
								{
									// billing and shipping info
									$comquick2cartHelper = new comquick2cartHelper;
									$billpath            = $comquick2cartHelper->getViewpath('cartcheckout','default_billing_bs5');
									?>
									<div class="clearfix">&nbsp;</div>
									<div class="row">
										<div class="container-fluid checkout-addresses">
											<?php
											ob_start();
												include($billpath);
												$html = ob_get_contents();
											ob_end_clean();
											echo $html;
											?>
										</div>
									</div>
									<?php
								}
								?>
							</div>
						</div>
						<!--step2-->

						<?php
						if ($isShippingEnabled)
						{
							?>
							<div class="tab-pane step-pane" id="step3">
								<div class="row "></div>
								<div id="qtcProdShippingMethos"></div>
								<div id="qtc_shipStepAlert"></div>
							</div>

							<div class="tab-pane step-pane" id="step4">
								<br/>
								<div id="qtc_reviewAndPayHTML"></div>
							</div>
							<?php
						}
						else 
						{
							?>
							<div class="tab-pane step-pane" id="step4">
								<br/>
								<div id="qtc_reviewAndPayHTML"></div>
							</div>
							<?php

						}
						?>
						<input type="hidden" name="task" id="task" value="cartcheckout.qtc_autoSave" />
						<input type="hidden" name="deliveryDate" id="deliveryDate" value="" />
						<input type="hidden" name="deliveryTime" id="deliveryTime" value="" />
					</form>
					<div style="clear:both">&nbsp;</div>
					<!-- show payment hmtl form-->
					<div id="qtc_payHtmlDiv"></div>
				</div>
				<?php $this->target_div=0;?>
				<br/>
				<div class="prev_next_wizard_actions">
					<div class="">
						<button id="btnWizardPrev" type="button" style="display:none" class="btn btn-primary float-start" >
							<i class="<?php echo Q2C_ICON_ARROW_CHEVRON_LEFT; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>" ></i>&nbsp;<?php echo Text::_('COM_QUICK2CART_PREV');?>
						</button>
						<!-- Next/Save and Next Button -->
						<?php
							$otpEnabled = $this->params->get('enable_otp', 0);
						?>
						<button
							id="btnWizardNext"
							type="button"
							class="btn btn-primary float-end"
							data-last="Finish"
							onclick="<?php echo ($otpEnabled ? 'generateOtpAndNext();' : 'btnWizardNext();'); ?>">
							<span><?php echo Text::_("COM_QUICK2CART_BTN_SAVEANDNEXT"); ?></span>
							<i class="<?php echo Q2C_ICON_ARROW_CHEVRON_RIGH; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>
						</button>
					</div>
				</div>
				<div id="qtc_StepLoading" style="display:none;">
					<div class="com_quick2cart_ajax_loading" >
						<div class="com_quick2cart_ajax_loading_text"></div>
						<img class="com_quick2cart_ajax_loading_img" src="<?php echo $root_url?>components/com_quick2cart/assets/images/ajax.gif">
					</div>
				</div>
			</div>
			<div style="clear:both;"></div>
		</div>
	</div>

	<?php
	$checkoutTermsCondArtId = $this->params->get('termsConditonsArtId', 0);
	$menuItemId     = $comquick2cartHelper->getitemid('index.php?option=com_content&view=article');
	$terms_link = Uri::root() . "index.php?option=com_content&view=article&id=". $checkoutTermsCondArtId ."&Itemid=". $menuItemId ."&tmpl=component";
	$modalConfig = array('width' => '600px', 
		'height' => '600px', 
		'title' => Text::_('COM_QUICK2CART_CHECKOUT_PRIVACY_POLICY'), 
		'closeButton' => true, 
		'modalWidth' => 80, 
		'url'        => $terms_link,
		'bodyHeight' => 70);
	echo HTMLHelper::_('bootstrap.renderModal', 'privacyPolicyModal', $modalConfig);
	?>
</div>

<script type="text/javascript">
	var deleteAddressMessage = Joomla.Text._('COM_QUICK2CART_DELETE_ADDRESS');
	var root_url="<?php echo $root_url; ?>";
	var valid_msg="<?php echo Text::_('COM_SA_FLOAT_VALUE_NOT_ALLOWED'); ?>";
	var savennextbtn_text="<?php echo Text::_("COM_QUICK2CART_BTN_SAVEANDNEXT");?>";
	var savenexitbtn_text="<?php echo Text::_("COM_QUICK2CART_BTN_SAVEANDEXIT");?>";
	var root_url = "<?php echo Uri::root(); ?>";
	<?php if ($otpEnabled): ?>
		var otpSentMsg = "<?php echo Text::_('COM_QUICK2CART_OTP_SENT_SUCCESSFULLY'); ?>";
		var otpFailedMsg = "<?php echo Text::_('COM_QUICK2CART_OTP_GENERATION_FAILED'); ?>";
		function generateOtpAndNext() {
			var activeStep = jQuery('.tab-pane.step-pane.active').attr('id');
			if (activeStep !== 'step2') {
				btnWizardNext();
				return;
			}
			jQuery.ajax({
				url: root_url + 'index.php?option=com_quick2cart&task=cartcheckout.generateOtp',
				type: 'POST',
				dataType: 'json',
				success: function(response) {
					if (response && response.success) {
						jQuery('#otp-msg').css('color', 'green').html(otpSentMsg);
						btnWizardNext();
					} else {
						jQuery('#otp-msg').css('color', 'red').html(otpFailedMsg);
					}
				},
				error: function() {
					jQuery('#otp-msg').css('color', 'red').html(otpFailedMsg);
				}
			});
		}
	<?php endif; ?>
</script>