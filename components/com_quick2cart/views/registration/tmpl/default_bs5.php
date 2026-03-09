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

$app               = Factory::getApplication();
$document          = Factory::getDocument();
$params            = ComponentHelper::getParams('com_quick2cart');
$guestcheckout     = $params->get('guest');
$registerFormStyle = '';
$jinput            = $app->input;
$itemid            = $jinput->get('Itemid');
$rurl              = 'index.php?option=com_quick2cart&view=cartcheckout&Itemid='.$itemid;
$returnurl         = base64_encode($rurl);

if (!$user->id)
{
	//1. IF GEUEST CHECKOUT IS ON THEN SET guest as default
	$registerMehod = ($guestcheckout==1) ? 0 : 1;

	//2.consider page refresh ::set last selected option.
	$session     = Factory::getSession();
	$ckoutMethod = $session->get('one_pg_ckoutMethod');
	$checked     = 'checked="checked"';

	// LAST SELECTED OPTION
	if ($ckoutMethod == 'guest')
	{
		$registerMehod = 0;
	}
	elseif ($ckoutMethod == 'register')
	{
		// session veriablt to register
		$registerMehod=1;
	}

	$showBillShipTab = 0;

	if (!empty($qtc_hideregistrationTabFlag) && $qtc_hideregistrationTabFlag !='0')
	{
		$registorStyle   = 'display:none;';
		$showBillShipTab = 1;
	}
	?>
	<div id="qtc_user-info" class="com_quick2cart-checkout-steps " style="<?php echo $registorStyle; ?>">
		<div class="checkout-heading">
			<span><?php echo Text::_('COM_QUICK2CART_USER_INFO');?></span>
			<span id="" class="qtcHandPointer btn btn-xs btn-primary float-end" onclick="qtc_hideShowLoginTab('qtc_user-info-content', 'qtc_ckout_billing-info')">
				<?php echo Text::_('COM_QUICK2CART_MODIFY');?>
			</span>
			<div class="clearfix"></div>
		</div>
		<div id="qtc_user-info-content" class="container-fluid">
			<div class="row" >
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 paddleft">
					<h3><?php echo Text::_('COM_QUICK2CART_CHECKOUT_NEW_CUSTOMER'); ?></h3>
					<p><?php echo Text::_('COM_QUICK2CART_CHECKOUT_OPTIONS'); ?></p>
					<!-- registration -->
					<?php
					if(1)
					{ ?>
						<div class="radio">
							<label for="register" title="<?php echo Text::_('COM_QUICK2CART_CHECKOUT_REGISTER_DESC'); ?>" class="form-check-label">
								<input
									type="radio"
									name="qtc_guest_regis"
									value="register"
									id="register" <?php echo (!empty($registerMehod)? $checked: '');?>
									onchange="qtc_checkoutMethod(this)"
									class="form-check-input" />
								<b><?php echo Text::_('COM_QUICK2CART_CHECKOUT_REGISTER'); ?></b>
							</label>
						</div>
					<?php 
					} ?>

					<!-- guest -->
					<?php
					if ($guestcheckout==1)
					{?>
						<div class="radio">
							<label for="guest" class="form-check-label">
								<input
									type="radio"
									name="qtc_guest_regis"
									value="guest"
									id="guest" <?php echo (empty($registerMehod)? $checked: '');?>
									onchange="qtc_checkoutMethod(this)"
									class="form-check-input" />
								<b><?php echo Text::_('COM_QUICK2CART_CHECKOUT_GUEST'); ?></b>
							</label>
						</div>
					<?php 
					} ?>
					<br />
						<?php
					if(1)
					{ ?>
						<p><?php echo Text::_('COM_QUICK2CART_CHECKOUT_REGISTER_ACCOUNT_HELP_TEXT'); ?></p>
						<?php 
					} ?>
				</div>
				<div id="login" class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
					<h3><?php echo Text::_('COM_QUICK2CART_CHECKOUT_RETURNING_CUSTOMER'); ?></h3>
					<p><?php echo Text::_('COM_QUICK2CART_CHECKOUT_RETURNING_CUSTOMER_WELCOME'); ?></p>
					<b><?php echo Text::_('COM_QUICK2CART_CHECKOUT_USERNAME'); ?></b><br />
					<input type="text" name="email" value="" class="form-control"/>
					<br />
					<br />
					<b><?php echo Text::_('COM_QUICK2CART_CHECKOUT_PASSWORD'); ?></b><br />
					<input type="password" name="password" value="" class="form-control"/>
					<br />
					<br />
					<input
						type="button"
						value="<?php echo Text::_('COM_QUICK2CART_CHECKOUT_LOGIN'); ?>"
						id="button-login"
						class="button btn btn-primary"
						onclick="qtc_ckpg_login(this)"/><br />
					<br />
				</div>
			</div>
			<hr/>
			<div class="row">
				<div class="col-xs-12">
					<input
						type="button"
						class=" btn  btn-primary"
						id="button-user-info"
						value="<?php echo Text::_('COM_QUICK2CART_CONTINUE');?>"
						onclick="qtc_guestContinue('qtc_user-info-content')">
				</div>
				<div class="clearfix"></div>
				<br />
			</div>
		</div>
		<!--Added by Sneha, to display message to login on checkout-->
		<div class="form-group" id="qtc_loginmail_msg_div" style="display:none">
			<span class="help-inline qtc_removeBottomMargin" id="loginmail_msg"></span>
		</div>
	</div>
	<?php
}
?>
<!--End User Details Tab-->
