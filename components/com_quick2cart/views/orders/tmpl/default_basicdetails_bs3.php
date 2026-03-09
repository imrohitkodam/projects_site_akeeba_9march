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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\User\User;

$app   = Factory::getApplication();
$input = $app->input;
?>
<div class=" qtc_wholeCustInfoDiv qtcPadding" >
	<form action="" name="adminForm" id="adminForm" class="form-validate" method="post">
		<?php
		if (in_array('order_status', $order_blocks))
		{ ?>
			<h4><?php echo Text::_('QTC_ORDER_INFO'); ?></h4>
			<div class="row ">
				<div class="col-md-6 " style="<?php echo  !empty($orders_email) ? "width: 50%; float: left;" : '';?>">
					<table  class="table table-condensed table-bordered qtc-table  " >
						<tbody >
							<tr >
								<td><?php echo Text::_('QTC_ORDER_DATE');?></td>
								<td><?php echo HTMLHelper::date($this->orderinfo->cdate, Text::_("COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT") . ' ' . Text::_('COM_QUICK2CART_TIME_FORMAT_SHOW_AMPM'));?></td>
							</tr>
							<?php
							// If not store releated view
							if (empty($this->storeReleatedView))
							{
								?>
								<tr>
									<td><?php echo Text::_('QTC_AMOUNT');?></td>
									<td>
										<span>
											<?php
											$tprice = 0;

											foreach ($this->orderitems as $order)
											{
												$tprice += $order->product_final_price;
											}

											$store_id = $input->get('store_id');

											if ($store_id == NULL)
											{
												echo $this->comquick2cartHelper->getFromattedPrice($this->orderinfo->amount, $order_currency);
											}
											else
											{
												echo $this->comquick2cartHelper->getFromattedPrice(number_format($tprice, 2), $order_currency);
											}
											?>
										</span>
									</td>
								</tr>
								<?php
							}?>
							<tr>
								<td><?php echo Text::_('QTC_ORDER_STATUS');?></td>
								<td>
									<?php
									$whichever = '';

									switch ($this->orderinfo->status)
									{
										case 'C':
											$whichever = Text::_('QTC_CONFR');
											break;
										case 'RF':
											$whichever = Text::_('QTC_REFUN');
											break;
										case 'S':
											$whichever = Text::_('QTC_SHIP');
											break;
										case 'E':
											$whichever = Text::_('QTC_ERR');
											break;
										case 'P':
											if ($orders_site)
											{
												$whichever = Text::_('QTC_PENDIN');
											}
											break;
										default:
											$whichever = $orders->status;
											break;
									}

									if (!($orders_site))
									{
										echo HTMLHelper::_('select.genericlist', $this->pstatus, "pstatus", 'class="pad_status"  onChange="selectstatusorder(' . $this->orderinfo->id . ',this);"', "value", "text", $this->orderinfo->status);
									}
									else
									{
										echo htmlspecialchars($whichever, ENT_COMPAT, 'UTF-8');
									}?>
								</td>
							</tr>

							<?php
							if (!$orders_site)
							{
								?>
								<tr>
									<td><?php echo Text::_('QTC_NOTIFY');?></td>
									<td>
										<input type="checkbox" id="notify_chk" name="notify_chk|<?php echo $this->orderinfo->id;?>" checked />
									</td>
								</tr>
								<tr>
									<td><?php echo Text::_('QTC_COMMENT');?></td>
									<td><textarea id="" name="comment" rows="3"  value=""></textarea></td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>
				<div class="col-md-6"  style="<?php echo  !empty($orders_email) ? "width: 50%; float: right;" : '';?>">
				<!-- ************* Order Status Info Starts **********  -->
					<table class="table table-condensed table-bordered  " >
						<tr >
							<td><?php echo Text::_('QTC_ORDER_USER');?></td>
							<td>
								<?php
								$table   = User::getTable();
								$user_id = intval($this->orderinfo->payee_id);

								if ($user_id)
								{
									$creaternm = '';

									if ($table->load($user_id))
									{
										$creaternm = Factory::getUser($this->orderinfo->payee_id);
									}

									echo (!$creaternm) ? Text::_('QTC_NO_USER') : htmlspecialchars($creaternm->username, ENT_COMPAT, 'UTF-8');
								}
								else
								{
									echo !empty($billinfo->user_email) ? htmlspecialchars($billinfo->user_email, ENT_COMPAT, 'UTF-8') : '';
								}
								?>
							</td>
						</tr>
						<?php
						if ($this->orderinfo->processor)
						{
						?>
							<tr>
								<td><?php echo Text::_('QTC_ORDER_PAYMENT');?></td>
								<td><?php echo !empty($this->paidPlgName) ? $this->paidPlgName : $this->orderinfo->processor;?></td>
							</tr>
							<?php
						}

						if ($this->orderinfo->transaction_id)
						{
							?>
							<tr>
								<td><?php echo Text::_('QTC_ORDER_PAYMENT_TRANSAC');?></td>
								<td><?php echo htmlspecialchars($this->orderinfo->transaction_id, ENT_COMPAT, 'UTF-8');?></td>
							</tr>
							<?php
						}?>
						<tr>
							<td><?php echo Text::_('QTC_USER_COMMENT');?></td>
							<td class="q2c-max-width-150">
								<?php echo htmlspecialchars($this->orderinfo->customer_note, ENT_COMPAT, 'UTF-8') ? $this->orderinfo->customer_note : Text::_('QTC_USER_COMMENT_NO');?>
							</td>
						</tr>
						<tr>
							<td><?php echo Text::_('COM_QUICK2CART_PAYMENT_NOTE');?></td>
							<td class="q2c-max-width-150">
								<?php echo htmlspecialchars($this->orderinfo->payment_note, ENT_COMPAT, 'UTF-8') ? $this->orderinfo->payment_note : Text::_('QTC_USER_COMMENT_NO');?>
								</td>
						</tr>
					</table>
				</div>
			</div>
			<?php
		} ?>

	<!-- For pending order, show the payment list -->
	<?php

	if (!$orders_email)
	{
		$url = Uri::root() . "index.php?option=com_quick2cart&tmpl=component&task=payment.gethtml&order=" . $this->orderinfo->id . "&" . Session::getFormToken() . "=1&processor=";
		$ajax =
<<<EOT
techjoomla.jQuery(document).ready(function(){
techjoomla.jQuery("input[name='gateways']").change(function(){
var url1 = '{$url}'+techjoomla.jQuery("input[name='gateways']:checked").val();
techjoomla.jQuery('#html-container').empty().html('Loading...');
techjoomla.jQuery.ajax({
url: url1,
type: 'GET',
dataType: 'html',
success: function(response)
{
	techjoomla.jQuery('#html-container').removeClass('ajax-loading').html( response );
}
});
});
});
EOT;
		$document->addScriptDeclaration($ajax);
	}

		if ($orders_site && !($orders_email) && ($this->orderinfo->status == 'P'))
		{
			$jinput          = $app->input;
			$paybuttonstatus = $jinput->get('paybuttonstatus');

			// Means called from my orders list
			$getways_display =  "display:none" ;

			// Means called from my orders list
			$complete_payment_btn = empty($paybuttonstatus) ? "display:none" : "display:block";
			$qtc_processor        = $jinput->get('processor');

			// IF in url processor="payment getway name
			// "is present then dont show
			if (!isset($qtc_processor) && $vendor_order_view == 0)
			{
				?>
				<tr style="<?php echo $complete_payment_btn;?>">
					<td colspan='2'>
						<button type="button" name="qtc_show_getways"
							id="qtc_show_getways"
							class="btn btn-success btn-medium validate"
							onclick="qtc_showpaymentgetways();" style="<?php echo $complete_payment_btn;?>">
								<?php echo Text::_('QTC_COMPLETE_UR_ORDER');?>
						</button>
					</td>
				</tr>
				<?php
			}
			?>
			<div id="qtc_paymentmethods" style="<?php echo $getways_display;?>;">
				<div class="form-group">
					<label for="" class="col-sm-2 control-label">
						<strong><?php echo Text::_('PAY_METHODS');?></strong>
					</label>
					<div class="col-sm-10">
						<?php
						$gateways = $this->gateways;

						if (empty($this->gateways))
						{
							echo Text::_('NO_PAYMENT_GATEWAY');
						}
						else
						{
							$pg_list = HTMLHelper::_('select.radiolist', $gateways, 'gateways', ' autocomplete="off" ', 'id', 'name', '', false);
							echo $pg_list;
						}?>
					</div>
				</div>
			</div>
			<!-- End of qtc_paymentmethods-->
			<?php
		}

		if (!$orders_email)
		{
			?>
			<input type="hidden" name="option" value="com_quick2cart" />
			<input type="hidden" id='hidid' name="id" value="" />
			<input type="hidden" id='hidstat' name="status" value="" />
			<input type="hidden" name="task" id="task" value="" />
			<input type="hidden" name="view" value="orders" />
			<input type="hidden" name="controller" value="orders" />
			<?php
		}
		?>
	</form>
	<!--PAYMENT HIDDEN DATA WILL COME HERE -->
	<div style="clear:both;">&nbsp;</div>
	<div id="html-container" name=""></div>
</div>
