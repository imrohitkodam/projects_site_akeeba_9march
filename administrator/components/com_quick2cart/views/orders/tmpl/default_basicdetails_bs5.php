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
use Joomla\CMS\User\User;
?>
<div class=" qtc_wholeCustInfoDiv">
	<?php
	if (in_array('order_status', $order_blocks))
	{ ?>
	<h4><?php echo Text::_('QTC_ORDER_INFO'); ?></h4>
	<table class="table table-condensed ">
		<tbody>
			<tr>
				<td>
					<table  class="table table-condensed table-bordered">
						<tbody >
							<tr>
								<td><?php echo Text::_('QTC_ORDER_ID');?></td>
								<td><?php echo $this->orderinfo->prefix . (int) $this->orderinfo->id;?></td>
							</tr>
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

											$jinput = JFactory::getApplication()->input;
											$store_id = $jinput->get('store_id');

											if ($store_id == NULL)
											{
												echo $this->comquick2cartHelper->getFromattedPrice($this->orderinfo->amount, $order_currency);
											}
											else
											{
												echo $this->comquick2cartHelper->getFromattedPrice(number_format($tprice, 2), $order_currency);
											}?>
										</span>
									</td>
								</tr>
								<?php
							}

							if ($this->orderinfo->transaction_id)
							{
								?>
								<tr>
									<td><?php echo Text::_('QTC_ORDER_PAYMENT_TRANSAC');?></td>
									<td><?php echo $this->orderinfo->transaction_id;?></td>
								</tr>
								<?php
							}

							$OrderStatus = '';

							switch ($this->orderinfo->status)
							{
								case 'C':
									$OrderStatus = Text::_('QTC_CONFR');
									break;
								case 'RF':
									$OrderStatus = Text::_('QTC_REFUN');
									break;
								case 'S':
									$OrderStatus = Text::_('QTC_SHIP');
									break;
								case 'E':
									$OrderStatus = Text::_('QTC_ERR');
									break;
								case 'P':
										$OrderStatus = Text::_('QTC_PENDIN');
									break;
								default:
									$OrderStatus = $orders->status;
									break;
							}
							?>
							<tr>
								<td class=""><?php echo Text::_('QTC_ORDER_STATUS');?></td>
								<td><?php echo $OrderStatus?></td>
							</tr>



						</tbody>
					</table>
				</td>
				</td>
				<td>
				<!-- ************* Order Status Info Starts **********  -->
					<table class="table table-condensed table-bordered" >
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

									echo (!$creaternm) ? Text::_('QTC_NO_USER') : $creaternm->username;
								}
								else
								{
									echo !empty($billinfo->user_email) ? $billinfo->user_email : '';
								}?>
							</td>
						</tr>
						<tr >
							<td><?php echo Text::_('QTC_ORDER_IP');?></td>
							<td><?php echo $this->orderinfo->ip_address;?></td>
						</tr>
						<?php
						if ($this->orderinfo->processor)
						{
						?>
							<tr>
								<td><?php echo Text::_('QTC_ORDER_PAYMENT');?></td>
								<td><?php echo $this->paidPlgName = $this->comquick2cartHelper->getPluginName($this->orderinfo->processor);?></td>
							</tr>
							<?php
						}
						?>

						<tr>
							<td><?php echo Text::_('QTC_USER_COMMENT');?></td>
							<td class="q2c-max-width-150"><?php echo ($this->orderinfo->customer_note)?$this->orderinfo->customer_note:Text::_('QTC_USER_COMMENT_NO') ; ?></td>
						</tr>
						<tr>
							<td class="q2c-max-width-150"><?php echo Text::_('COM_QUICK2CART_PAYMENT_NOTE');?></td>
							<td class="q2c-max-width-150"><?php echo ($this->orderinfo->payment_note) ? $this->orderinfo->payment_note : Text::_('QTC_USER_COMMENT_NO');?></td>
						</tr>
					</table>
				</td>
			</tr>
		</tbody>
		</table>
		<?php
	} ?>

</div>
