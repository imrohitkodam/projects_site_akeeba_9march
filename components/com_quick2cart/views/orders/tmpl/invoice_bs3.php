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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;

$user = Factory::getUser();
$input= Factory::getApplication()->input;
$this->comquick2cartHelper = new comquick2cartHelper;
$this->params = ComponentHelper::getParams('com_quick2cart');
$jinput = Factory::getApplication()->input;
$order_id = $jinput->get('orderid', 0, 'INTEGER');
$jinput->set('orderid', $order_id);
$store_id = $jinput->get('store_id');
$guest_email   = $jinput->get('email', '', 'RAW');
$order = $this->orders;

//$order = $order_bk = $this->comquick2cartHelper->getorderinfo($order_id, $store_id);
$this->orderinfo = $order['order_info'];
$this->orderitems = $order['items'];
$this->orders_site = 1;
$this->orders_email = 1;
$this->order_authorized = 1;

// Invoice is always store related
$this->storeReleatedView = 1;
$adminCall   = $jinput->get('adminCall', '', 'INTEGER');

if (empty($adminCall))
{
	// Guest checkout and and called from 1 pg ckout
	if ($guest_email)
	{
		$guest_email_chk = 0;
		$guest_email_chk = $this->comquick2cartHelper->checkmailhash($this->orderinfo[0]->id, $guest_email);

		if (!$guest_email_chk && empty($this->qtcSystemEmails))
		{
	?>
			<div class="<?php echo Q2C_WRAPPER_CLASS;?>">
				<div class="well">
					<div class="alert alert-danger">
						<span>
							<?php echo Text::_('QTC_GUEST_MAIL_UNMATCH');?>
						</span>
					</div>
				</div>
			</div>
			<?php
			return false;
		}
	}
	elseif (!$user->id)
	{
		$app    = Factory::getApplication();
		$return = base64_encode(Uri::getInstance());
		$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
		$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
		$app->redirect($login_url_with_return, 403);
	}
}

if (empty($store_id))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-orders">
		<div class="well" >
			<div class="alert alert-danger">
				<span><?php echo Text::_('COM_QUICK2CART_INVOICE_MISSING_STORE_ID'); ?></span>
			</div>
		</div>
	</div>

	<?php
	return false;
}

$billemail = "";

if (!empty($this->orderinfo[0]->address_type) && $this->orderinfo[0]->address_type == 'BT')
{
	$billemail = $this->orderinfo[0]->user_email;
}
elseif (!empty($this->orderinfo[1]->address_type) && $this->orderinfo[1]->address_type == 'BT')
{
	$billemail = $this->orderinfo[1]->user_email;
}

$fullorder_id = $order['order_info'][0]->prefix . $order_id;
$this->qtcSystemEmails = 1;

if (!Factory::getUser()->id && $this->params->get('guest'))
{
	$jinput->set('email', md5($billemail));
}
?>

	<?php
	/* if user is on payment layout and log out at that time undefined order is is found
	in such condition send to home page or provide error msg
	*/
	if(isset($this->orders_site) && isset($this->undefined_orderid_msg) )
	{
			return false;
	}

	$user=Factory::getUser();
	$jinput=Factory::getApplication()->input;
	$guest_email = $jinput->get('email','','STRING');

	if($guest_email)
	{
		$guest_email_chk =0;
		$guest_email_chk = $this->comquick2cartHelper->checkmailhash($this->orderinfo[0]->id,$guest_email);
		if(!$guest_email_chk )
		{
			?>

			<div class="well" >
				<div class="alert alert-danger">
					<span ><?php echo Text::_('QTC_GUEST_MAIL_UNMATCH'); ?> </span>
				</div>
			</div>

		<?php
			return false;
		}
	}
	else if(!$user->id && !$this->params->get( 'guest' ))
	{
		$msg = Text::_('QTC_LOGIN');

		// Get current url.
		$current = Uri::getInstance()->toString();
		$url = base64_encode($current);
		Factory::getApplication()->redirect(Route::_('index.php?option=com_users&view=login&return=' . $url, false), $msg);
	}
	?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" style="border-width: 1px 1px 1px 1px; border-style: solid; border-color: #DDD; border-collapse: separate;padding:5px;">
	<?php
	// 1 check : for "MY ORDERS"=check for authorized user or not ( it should be site,authorized to view order and not store releated view)
	if(isset($this->orders_site) && empty($this->order_authorized) )
	{
		$authorized=0;
		//2 check : "FOR STORE ORDER " order should be releated to store
		if( !empty($this->storeReleatedView))  // if vendor releated view is present then current order should be releated to store
		{
			//3. store releated view but not logged in then (directly accessed known url at that time it require )
			if (empty($user->id))
			{
				$msg = Text::_('QTC_LOGIN');

				// Get current url.
				$current = Uri::getInstance()->toString();
				$url = base64_encode($current);
				Factory::getApplication()->redirect(Route::_('index.php?option=com_users&view=login&return=' . $url, false), $msg);
			}

			$result=$this->comquick2cartHelper->getStoreOrdereAuthorization($this->store_id,$this->orderid);
			$authorized=(!empty($result))?1:0;
		}

		if($authorized==0)
		{
			?>
				<div class="well" >
					<div class="alert alert-danger">
						<span><?php echo Text::_('QTC_NOT_AUTHORIZED_USER_TO_VIEW_ORDER'); ?> </span>
					</div>
				</div>
			<?php
				return false;
		}// end of if($authorized==0)
	}

	$coupon_code=$this->orderinfo[0]->coupon_code ;

	if (!empty($this->orderinfo[0]->address_type) && $this->orderinfo[0]->address_type == 'BT')
	{
		$billinfo = $this->orderinfo[0];
	}
	elseif (!empty($this->orderinfo[1]->address_type) && $this->orderinfo[1]->address_type == 'BT')
	{
		$billinfo = $this->orderinfo[1];
	}

	if( $this->params->get( 'shipping' ) == '1' )
	{
		if($this->orderinfo[0]->address_type == 'ST')
			$shipinfo = $this->orderinfo[0];
		else if(isset($this->orderinfo[1]))
						if($this->orderinfo[1]->address_type == 'ST')
								$shipinfo = $this->orderinfo[1];
	}

	$this->orderinfo = $this->orderinfo[0];
	// 1 for site 0 for admin
	$orders_site       = (isset($this->orders_site)) ? $this->orders_site : 0;
	$orders_email      = (isset($this->orders_email)) ? $this->orders_email : 0;
	$emailstyle        = "style='background-color: #cccccc;  padding: 7px;'";
	$vendor_order_view = (!empty($this->store_id)) ? 1 : 0;
	$order_currency    = $this->orderinfo->currency;

	$order_currency = $this->orderinfo->currency;
	//$order_currency = ($this->orderinfo->currency)?$this->orderinfo->currency :$or_currency;

	if(isset($this->order_blocks))
	{
		$order_blocks = $this->order_blocks;
	}
	else
	{
		$order_blocks  = array ('0'=>'shipping','1'=>'billing','2'=>'cart','3'=>'order','4'=>'order_status');
	}

	$document = Factory::getDocument();
	
	HTMLHelper::_('script', 'components/com_quick2cart/assets/js/bootstrap-tooltip.js');
	HTMLHelper::_('script', 'components/com_quick2cart/assets/js/bootstrap-popover.js');
?>
	<script type="text/javascript">
		techjoomla.jQuery(document).ready(function()
		{
			techjoomla.jQuery('.discount').popover(
			);

		});

		function	qtc_showpaymentgetways()
		{
			document.getElementById("qtc_paymentmethods").style.display='block';
		}
	</script>


<div style="padding:20px;">
	<table  style="  width: 100%; " >
		<thead>
			<tr style="vertical-align:middle;border:0;">
				<td style="vertical-align:middle;border:0;">
					<h2><?php echo Text::_('QTC_INVOICE_VIEW_HEAD');?></h2>
				</td>
				<td style="vertical-align:middle; text-align:right;border:0;">
					<h4 style="margin:0; padding:0;"><span><strong><?php echo Text::_('QTC_INVOICE_DATE');?>:</strong></span>
					<?php echo HTMLHelper::date($this->orderinfo->cdate, Text::_("COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT"));?></h4>
				</td>
			</tr>
		</thead>

		<tbody>
			<tr>
				<td  style="background-color: #ccc;  padding: 7px;">
					<h4 style="margin:0; padding:0;"><?php echo Text::_('COM_QUICK2CART_INVOICE_SOLD_BY_LBL');?></h4>
				</td>
				<td  style="background-color: #ccc;  padding: 7px;">
					<h4 style="margin:0; padding:0;"><?php echo Text::_('QTC_INVOICE_DETAIL') ?></h4>
				</td>
			</tr>

			<tr>
				<td  style="border:1px solid #dddddd;  padding: 5px 10px;" class="tdaddress">
					<?php $storeinfo = $this->comquick2cartHelper->getSoreInfoInDetail($jinput->get('store_id', '0', 'INTEGER')); ?>
						<h4 style="margin:0; padding:0;"><?php echo htmlspecialchars($storeinfo['title'], ENT_COMPAT, 'UTF-8'); ?></h4>
						<address class="shop-address"><?php echo htmlspecialchars($storeinfo['address'], ENT_COMPAT, 'UTF-8'); ?></address>
						<p>
							<strong><?php echo Text::_('COM_QUICK2CART_INVOICE_SHOP_EMAIL_LBL'); ?> </strong>
							<a href="mailto:<?php echo $storeinfo['store_email']; ?>" title="<?php echo htmlspecialchars($storeinfo['title'], ENT_COMPAT, 'UTF-8'); ?>">
								<?php echo htmlspecialchars($storeinfo['store_email'], ENT_COMPAT, 'UTF-8'); ?>
							</a>
						</p>
						<?php
						if (!empty($storeinfo['phone']))
						{
						?>
						<p>
							<strong><?php echo Text::_('COM_QUICK2CART_INVOICE_SHOP_PHONE_LBL'); ?></strong>
							<span><?php echo htmlspecialchars($storeinfo['phone'], ENT_COMPAT, 'UTF-8'); ?></span>
						</p>
						<?php
						}
						?>
				</td>
				<td  style="border:1px solid #dddddd;  padding: 5px 10px;" class="tdaddress">
					<p>
						<strong><?php echo Text::_('QTC_INVOICE_ID'); ?></strong>
						<span>
							<?php echo $this->orderinfo->prefix . $this->orderinfo->id . '-' . $jinput->get('store_id'); ?>
						</span>
					</p>
					<p>
						<strong><?php echo Text::_('QTC_INVOICE_DATE');?></strong>
						<span>
							<?php echo HTMLHelper::date($this->orderinfo->cdate, Text::_("COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT")); ?>
						</span>
					</p>
					<p>
						<strong><?php echo Text::_('QTC_INVOICE_USER');?></strong>
						<span>
							<?php
								$table   = User::getTable();
								$user_id = intval( $this->orderinfo->payee_id );
								if ($user_id)
								{
									$creaternm = '';
									if ($table->load( $user_id ))
									{
										$creaternm = Factory::getUser($this->orderinfo->payee_id);
									}
									echo (!$creaternm) ? Text::_('QTC_NO_USER'): $creaternm->username;
								}
								else
								{
									echo htmlspecialchars($billinfo->user_email, ENT_COMPAT, 'UTF-8');
								}
							?>
						</span>
					</p>
					<p>
						<span>
							<strong><?php echo Text::_('QTC_INVOICE_PAID_MSG'); ?></strong>
							<?php
							$orderStatus ='';
							$orderStatusColor ='';
							switch($this->orderinfo->status)
							{
								case 'C':
									$orderStatus = Text::_('QTC_CONFR');
									$orderStatusColor = "success";
								break;

								case 'RF':
									$orderStatus = Text::_('QTC_REFUN') ;
									$orderStatusColor = "danger";
								break;

								case 'S':
									$orderStatus = Text::_('QTC_SHIP') ;
									$orderStatusColor = "success";
								break;

								case 'E':
									$orderStatus = Text::_('QTC_ERR') ;
									$orderStatusColor = "error";
								break;

								case 'P':
								if ($orders_site)
								{
									$orderStatus = Text::_('QTC_PENDIN') ;
								}

								$orderStatusColor = "warning";
								break;

								default:
								$orderStatus = $orders->status;
								break;
							}
							?>
							<strong>
								<span class=" text-<?php echo $orderStatusColor; ?>"><?php echo $orderStatus ; ?>
								</span>
							</strong>
						</span>
					</p>
					<?php if($this->orderinfo->processor) { ?>
					<p>
						<strong><?php echo Text::_('QTC_INVOICE_PAYMENT');?></strong>:
						<span><?php echo htmlspecialchars($this->orderinfo->processor, ENT_COMPAT, 'UTF-8'); ?></span>
					</p>
					<?php
					}
					?>

					<?php if($this->orderinfo->transaction_id) { ?>
					<p>
						<strong><?php echo Text::_('QTC_INVOICE_PAYMENT_TRANSAC');?></strong>:
						<span><?php echo htmlspecialchars($this->orderinfo->transaction_id, ENT_COMPAT, 'UTF-8'); ?></span>
					</p>
					<?php
					}
					?>
					<?php if(!empty($billinfo->vat_number)) { ?>
					<p>
						<strong><?php echo Text::_('QTC_BILLIN_VAT_NUM');?></strong>:
						<span><?php echo htmlspecialchars($billinfo->vat_number, ENT_COMPAT, 'UTF-8');?></span>
					</p>
					<?php
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<div style="clear:both;"></div>

	<?php
	$price_col_style = "style=\"".(!empty($orders_email)? 'text-align: right;' :'')."\"";
	$showoptioncol = 0;

	foreach($this->orderitems as $citem)
	{
		if(!empty($citem->product_attribute_names))
		{
			// Found attributes for atleast one product
			$showoptioncol=1;
			break;
		}
	}

		// Added by vijay
		if (empty($multivendor_enable))
		{
			$storeinfo     = $this->comquick2cartHelper->getSoreInfoInDetail($this->orderitems[0]->store_id);
		}
		?>
		<!-- this row will not appear when printing -->
		<div class="">
			<?php
			if (isset($this->email_table_bordered))
			{
				$this->email_table_bordered .= ";width:100%;";
			}
			else
			{
				$this->email_table_bordered = ";width:100%;";
			}
				// Display basic order detail.
			$view                = $this->comquick2cartHelper->getViewpath('orders', 'default_billing_bs3');
			ob_start();
				include($view);
				$html = ob_get_contents();
			ob_end_clean();
			echo $html;
			?>
		</div>
		<!-- Added by vijay ends here -->
		<!-- Table row -->
		<div class="">
			<div class="" style="width: 100%;">
				<!-- Display cart detail -->
				<?php
				$view = $this->comquick2cartHelper->getViewpath('orders', 'default_cartdetail_bs3');
				ob_start();
				include($view);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;	?>

			</div><!-- /.col -->
		</div><!-- /.row -->

		<?php
		$mainSiteAdress = $this->params->get('mainSiteAdress');
		$vat_num = $this->params->get('vat_num');

		if ($mainSiteAdress || $vat_num)
		{
		?>
		<div style="clear:both;">&nbsp;</div>
		<div class="row qtcPadding">
			<div class="" style=" color: gray;">
			<!--
			@dj Site invoice detail and store invoice detail are different. Here we should display site detail.
			-->
				 <div><b><i><?php echo Text::_('QTC_INVOICE_CONT_INFO'); ?></i></b></div>

				<?php
				if(!empty($mainSiteAdress))
				{
				?>
					<div><b><?php echo Text::_('COM_QUICK2CART_INV_STIE_ADDRESS');?></b> :
					<?php echo htmlspecialchars($mainSiteAdress, ENT_COMPAT, 'UTF-8');
					?>
					</div>
					<?php
				}

				if(!empty($vat_num))
				{
				?>
					<div><b><?php echo Text::_('QTC_INVOICE_VAT');?></b> :
					<?php echo htmlspecialchars($vat_num, ENT_COMPAT, 'UTF-8');
					?>
					</div>
					<?php
				}
				?>
			</div>
		</div>
		<?php
		}
	?>
		<div style="clear:both;">&nbsp;</div>

</div>
<!--
<style>
@media print {

*{display:block; position:static; float:n one;}
script,style{display:none;}
}

</style>
-->

