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

?>
<form action="" name="adminForm" id="adminForm" class="form-horizontal form-validate" method="post">
	<?php
		echo HTMLHelper::_('uitab.startTabSet', 'orderInfo', ['active' => 'order_status_info', 'recall' => true, 'breakpoint' => 768]);
			echo HTMLHelper::_('uitab.addTab', 'orderInfo', 'order_status_info', Text::_('QTC_ORDER_STAT_INFO'));
			?>
				<input class="" id="" name="orderItemsStr" type="hidden" value="<?php echo implode('||', $orderItemIds); ?>" >
				<div class="span12">
					<div class="table-responsive">
						<table class="" id="complete-order" name="complete-order">
							<thead>
								<tr>
									<th class="q2c_width_20"></th>
									<th class="q2c_width_70"></th>
								</tr>
							</thead>
							<tr>
								<td class=""><?php echo Text::_('QTC_ORDER_STATUS');?></td>
								<td>
									<?php
									if (! ($orders_site))
									{
										echo HTMLHelper::_('select.genericlist', $this->pstatus, "pstatus".$this->orderinfo->id, 'class="form-select pad_status" size="1" ', "value", "text", $this->orderinfo->status);
									}
									else
									{
										echo $OrderStatus;
									}
									?>
								</td>
							</tr>
							<tr>
								<td><?php echo Text::_('QTC_NOTIFY');?></td>
								<td>
									<div>
										<input type="checkbox" id="notify_chk" name="notify_chk|<?php echo $this->orderinfo->id; ?>" size="10" checked />
									</div>
								</td>
							</tr>
							<tr>
								<td><?php echo Text::_('QTC_COMMENT');?></td>
								<td><textarea id="" class="form-control w-100" name="order_note|<?php echo $this->orderinfo->id; ?>" rows="3" value=""></textarea></td>
							</tr>
							<tr>
								<td></td>
								<td>
									<button
										type="button"
										class="btn btn-success"
										onClick="selectstatusorder(<?php echo $this->orderinfo->id; ?>, this);"
										title="<?php echo Text::_('COM_QUICK2CART_UPDAE_ORDER_STATUS');?>">
											<?php echo Text::_('COM_QUICK2CART_UPDAE_ORDER_STATUS'); ?>
									</button>
								</td>
							</tr>
						</table>
						<hr/>
					</div>
					<input type="hidden" name="option" value="com_quick2cart" />
					<input type="hidden" id='hidid' name="id" value="" />
					<input type="hidden" id='hidstat' name="status" value="" />
					<input type="hidden" name="task" id="task" value="" />
					<input type="hidden" name="view" value="orders" />
					<input type="hidden" name="controller" value="orders" />
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
			<?php
			echo HTMLHelper::_('uitab.endTab');
			echo HTMLHelper::_('uitab.addTab', 'orderInfo', 'order_history', Text::_('COM_QUICK2CART_ORDER_HISTORY'));
			?>
				<!--- Other info tab ----->
				<div class="span12">
					<?php
					if (!empty($this->orderHistory))
					{
					?>
						<div class="table-responsive">
							<table class="table table-condensed table-striped table-bordered">
								<thead>
									<th class="q2c_width_15"><?php echo Text::_("QTC_PRODUCT_NAM"); ?></th>
									<th class="q2c_width_15"><?php echo Text::_("COM_QUICK2CART_CDATE"); ?></th>
									<th class="q2c_width_15"><?php echo Text::_("COM_QUICK2CART_CUSTOMER_NOTIFIED"); ?></th>
									<th class="q2c_width_15"><?php echo Text::_("QTC_PROD_STATUS"); ?></th>
									<th class="q2c_width_15"><?php echo Text::_("COM_QUICK2CART_ORDER_NOTE"); ?></th>
								</thead>
								<tbody>
								<?php
									$oldItem_id = "";
									foreach($this->orderHistory as $row)
									{
										?>
										<tr>
											<td class="q2c_width_15">
												<?php
													if ($oldItem_id !== $row->order_item_id)
													{
														echo htmlspecialchars($row->name, ENT_COMPAT, 'UTF-8');
														$oldItem_id = $row->order_item_id;
													}
												?>
											</td>
											<td class="q2c_width_15">
												<?php
													echo Factory::getDate($row->mdate)->Format(Text::_('COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT'));
													echo '  ';
													echo Factory::getDate($row->mdate)->Format(Text::_('COM_QUICK2CART_TIME_FORMAT_SHOW_AMPM'));
												?>
											</td>
											<td class="q2c_width_15">
												<?php
													if($row->customer_notified == 0)
													{
														echo '<i class="fa fa-remove"></i>';
													}
													else
													{
														echo '<i class="icon-ok"></i>';
													}
												?>
											</td>
											<td class="q2c_width_15">
												<?php
													switch($row->order_item_status)
													{
														case 'C':
															$status = Text::_('QTC_CONFR');
														break;

														case 'RF':
															$status = Text::_('QTC_REFUN') ;
														break;

														case 'S':
															$status = Text::_('QTC_SHIP') ;
														break;

														case 'E':
															$status = Text::_('QTC_ERR') ;
														break;

														case 'P':
															$status = Text::_('QTC_PENDIN') ;
														break;

														default:
														$status = !empty($orders->order_item_status) ? $orders->order_item_status : '';
														break;
													}

													echo htmlspecialchars($status, ENT_COMPAT, 'UTF-8');
													?>
											</td>
											<td class="">
												<?php echo $row->note;?>
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
					else
					{
						?>
						<div class="alert alert-info">
							<p><?php echo Text::_('COM_QUICK2CART_NO_HISTORY'); ?></p>
						</div>
						<?php
					}
					?>
				</div>
			<?php
			echo HTMLHelper::_('uitab.endTab');
		echo HTMLHelper::_('uitab.endTabSet');?>
</form>
