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
defined('_JEXEC') or die();
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// Check user is logged or not
$user = Factory::getUser();
$app  = Factory::getApplication();

HTMLHelper::_('formbehavior.chosen', 'select');

// Addded for invoice layout and PDF
$params             = ComponentHelper::getParams('com_quick2cart');
$multivendor_enable = $params->get('multivendor');

if (!$user->id)
{
	$app    = Factory::getApplication();
	$return = base64_encode(Uri::getInstance());
	$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
	$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
	$app->redirect($login_url_with_return, 403);
}

// Check for user authorization
// For store releated views
if (!empty($this->storeReleatedView))
{
	$authorized = $this->comquick2cartHelper->store_authorize("orders_default"); //($this->store_id);

	if (empty($authorized))
	{
		?>
		<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-orders">
			<div class="well" >
				<div class="alert alert-error">
					<span><?php echo Text::_('QTC_NOT_AUTHORIZED_USER'); ?></span>
				</div>
			</div>
		</div>
		<?php
		return false;
	}
}

$db                      = Factory::getDBO();
$result                  = $this->orders;
$orders_site             = (isset($this->orders_site)) ? $this->orders_site : 0;
$this->orderDetailItemId = $this->comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders&layout=order', 1);
$Itemid                  = (isset($this->Itemid)) ? $this->Itemid : 0;
$vendor_order_view       = (!empty($this->store_id)) ? 1 : 0;
$totalamount             = 0;
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task=='orders.deleteorders')
		{
			if (document.adminForm.boxchecked.value==0)
			{
				alert("<?php echo Text::_('QTC_MAKE_SEL');?>");
				return;
			}

			if(confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_CONFIRM_ORDERS'); ?>"))
			{
				Joomla.submitform(task);
			}
			else
			{
				return false;
			}
		}
		<?php
		if (!$orders_site)
		{
			?>
			if(task=='orders.payment_csvexport')
			{
				exportOrdersCsv();
			}
			else
			{
				Joomla.submitform(task);
			}
			<?php
		}
		?>
	}

	function changeOrderStatus(orderid,elemId)
	{
		document.adminForm.task.value   = "orders.save";
		document.adminForm.id.value     = orderid;
		var status                      = techjoomla.jQuery('#pstatus_'+orderid).val();
		document.adminForm.status.value = status;
		document.adminForm.submit();
	}

	jQuery(document).ready(function () {
		jQuery('#clear-calendar').on('click', function () {
			jQuery('#filter_fromdate').val('');
			jQuery('#filter_todate').val('');
			jQuery('#adminForm').submit();
		});
	});
</script>

<style type="text/css">
	.pagination a{text-decoration:none;}
</style>

<form name="adminForm" id="adminForm" class="form-validate " method="post">
	<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-orders">
		<?php
		if ($app->isClient('administrator'))
		{
			if (!empty( $this->sidebar)) : ?>
				<div id="j-sidebar-container" class="span2">
					<?php echo $this->sidebar; ?>
				</div>
				<div id="j-main-container" class="span10">
			<?php else : ?>
				<div id="j-main-container">
			<?php endif;
		}
		?>

		<?php
		if($orders_site)
		{
			if($vendor_order_view == 1)
			{
				// For store releated views
				if (!empty($this->storeReleatedView))
				{
					if (!empty($this->store_role_list))
					{
						$active = 'storeorders';
						$view   = $this->comquick2cartHelper->getViewpath('vendor', 'toolbar');
						ob_start();
						include($view);
						$html = ob_get_contents();
						ob_end_clean();
						echo $html;
						?>

						<legend>
							<?php
							if (!empty($this->store_role_list))
							{
								$storehelp = new storeHelper();
								$index     = $storehelp->array_search2d($this->store_id, $this->store_role_list);

								if(is_numeric( $index))
								{
									$store_name = $this->store_role_list[$index]['title'];
								}

								echo Text::sprintf('QTC_STORE_ORDERS_OWN',$store_name) ;
							}
							else
							{
								echo Text::_('QTC_STORE_ORDERS');
							}
							?>
						</legend>
					<?php
					}
					else
					{
						?>
						<legend><strong><?php echo Text::_('QTC_STORE_CUS_ORDERS')?> </strong></legend>
						<?php
					}
				}
			}
			else
			{
				?>
				<div class="page-header"><h2><?php echo Text::_('QTC_MYORDERS')?>&nbsp;</h2></div>
				<?php
			}
		}
		?>

		<div class="">
			<div id="qtc-filter-bar" class="qtc-btn-toolbar">
				<div class="filter-search btn-group pull-left float-start">
					<input type="text" name="filter_search" id="filter_search"
					placeholder="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_ORDERS'); ?>"
					value="<?php echo $this->lists['filter_search']; ?>"
					class="qtc-hasTooltip input-medium"
					title="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_ORDERS'); ?>" />
				</div>

				<div class="qtc-btn-group pull-left float-start">
					<button type="submit" class="btn qtc-hasTooltip"
					title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
					</button>
					<button type="button" class="btn qtc-hasTooltip"
					title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"
					onclick="document.getElementById('filter_search').value='';this.form.submit();">
						<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
					</button>
				</div>
				<?php
				$attr = array(
						'placeholder'=> Text::_('COM_QUICK2CART_ORDERS_SELECT_FROM_DATE'),
						'onChange'=>'document.adminForm.submit()',
						'class' => 'inputbox date__field'
				);
				?>
				<div class="pull-left float-start af-ml-5">
					<?php echo HTMLHelper::_('calendar', $this->lists['filter_fromdate'], 'filter_fromdate', 'filter_fromdate', '%Y-%m-%d', $attr);?>

				</div>
				<div class=" pull-left float-start af-ml-5">
					<?php echo HTMLHelper::_('calendar', $this->lists['filter_todate'], 'filter_todate', 'filter_todate', '%Y-%m-%d', array('placeholder'=> Text::_('COM_QUICK2CART_ORDERS_SELECT_TO_DATE'), 'onChange'=>'document.adminForm.submit()', 'class' => 'inputbox date__field'));?>
				</div>
				<div class="pull-left float-start af-ml-5">
					<button class="btn btn-primary" id="clear-calendar" type="button" title="<?php echo Text::_('JSEARCH_CALENDAR_CLEAR'); ?>">
						<i class="fa fa-remove"></i>
					</button>
				</div>

				<div class="qtc-btn-group pull-right float-end btn-wrapper af-ml-5">
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>

				<div class="qtc-btn-group pull-right float-end btn-wrapper">
					<?php echo HTMLHelper::_('select.genericlist', $this->sstatus, "search_select", 'class="ad-status input-medium"  onchange="document.adminForm.submit();" name="search_select"',"value", "text", $this->lists['search_select']);?>
				</div>

				<div class="clearfix">&nbsp;</div>
			</div>
		</div>

		<div class="clearfix">&nbsp;</div>

		<?php if (empty($result)) : ?>
			<div class="clearfix">&nbsp;</div>
			<div class="alert alert-warning alert-warning">
				<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
			</div>
		<?php
		else : ?>
			<div id='no-more-tables'>
				<?php if ($orders_site): ?>
				<table class="table qtc-table table-bordered">
				<?php else: ?>
				<table class="table table-striped qtc-table">
				<?php endif; ?>
					<thead>
						<tr>
							<?php
							if (!$orders_site)
							{
								?>
								<th class="q2c_width_1 nowrap ">
									<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
								</th>
								<?php
							}
							?>

							<th class='q2c_width_20'>
								<?php echo HTMLHelper::_('grid.sort', 'QTC_ORDER_ID', 'id', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>

							<?php
							if (!$orders_site)
							{
								?>
								<th class='q2c_width_10 hidden-phone hidden-tablet nowrap'>
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_ORDERS_GATEWAY', 'processor', $this->lists['order_Dir'], $this->lists['order']); ?>
								</th>
								<?php
							}
							?>

							<?php
							if ((!$orders_site) || $this->layout == 'storeorder')
							{
								?>
								<th class="q2c_width_15">
									<?php echo HTMLHelper::_('grid.sort', 'QTC_USERNAME', 'payee_id', $this->lists['order_Dir'], $this->lists['order']); ?>
								</th>
								<?php
							}
							?>

							<th class="  ">
								<?php echo HTMLHelper::_('grid.sort', 'QTC_ORDER_STATUS', 'status', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>

							<th class="q2c_width_10 nowrap">
								<?php echo HTMLHelper::_('grid.sort', 'QTC_ORDER_DATE', 'cdate', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>

							<th class="q2c_width_15 nowrap rightalign" >
								<?php echo HTMLHelper::_('grid.sort', 'QTC_AMOUNT', 'amount', $this->lists['order_Dir'], $this->lists['order']); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$id = 1;

						foreach ($result as $orders)
						{
							$order_currency = !empty($orders->currency) ? $orders->currency : '';

							// Added by aniket for task id #15931
							// CODE START FOR ORDER STATUS || STATUS SELECT BOX START
							$whichever = '';
							$row_color = '';

							// Get store item status in order . (releated to store item only )
							if (($orders_site) && !empty($this->storeReleatedView))
							{
								$orders->status = $this->comquick2cartHelper->getStoreItemStatus($orders->id, $this->store_id);
							}

							switch($orders->status)
							{
								case 'C':
									$whichever = Text::_('QTC_CONFR');
									$row_color = "success";
								break;

								case 'RF':
									$whichever = Text::_('QTC_REFUN') ;
									$row_color = "danger";
								break;

								case 'S':
									$whichever = Text::_('QTC_SHIP') ;
									$row_color = "success";
								break;

								case 'E':
									$whichever = Text::_('QTC_ERR') ;
									$row_color = "error";
								break;

								case 'P':
								if ($orders_site)
								{
									$whichever = Text::_('QTC_PENDIN') ;
								}

								$row_color = "warning";
								break;

								default:
								$whichever = $orders->status;
								break;
							}
							// END BY aniket
							?>

							<tr class="<?php echo $row_color; ?>">
								<?php
								if (!$orders_site)
								{
									?>
									<td class="q2c_width_1 nowrap " data-title="<?php echo Text::_('COM_QUICK2CART_GRID_SELECT');?>">
										<?php echo HTMLHelper::_('grid.id', $id, $orders->id ); ?>
									</td>
									<?php
								}
								?>

								<td class='small' data-title="<?php echo Text::_('QTC_ORDER_ID');?>">
									<div class="orderlist_orderid">
									<?php
										$passed_store_id = "";

										if (!empty($this->store_id) && $vendor_order_view == 1)
										{
											$passed_store_id = "&store_id=" . $this->store_id;
											$passed_store_id .= "&calledStoreview=1";
										}
										?>
										<a href="<?php echo Uri::base().substr(Route::_('index.php?option=com_quick2cart&view=orders&layout=order&orderid=' . $orders->id . '&Itemid=' . $this->orderDetailItemId . $passed_store_id), strlen(Uri::base(true)) + 1) . '';?>">
											<?php echo HTMLHelper::tooltip(Text::sprintf('QTC_TOOLTIP_VIEW_ORDER_MSG', $orders->prefix.$orders->id), Text::_('QTC_TOOLTIP_VIEW_ORDER'), '', $orders->prefix . $orders->id) ;?>
										</a>
									</div>
									<!-- Invoice layout and PDF -->
									<?php
									//If multi vendor is off then show the other option for invoice
									if (empty($multivendor_enable))
									{
										$order            = $this->comquick2cartHelper->getorderinfo($orders->id);
										$this->orderitems = $order['items'];
										$store_id         = $this->orderitems[0]->store_id;
										$streLinkPrarm    = "";

										if ($app->isClient('administrator'))
										{
											$streLinkPrarm .= '&adminCall=1';
										}
										?>

										<div class="qtcOrderlist-invoiceIcons">
											<a href="<?php echo Uri::root().substr(Route::_('index.php?option=com_quick2cart&view=orders&layout=invoice&orderid=' . $orders->id . '&tmpl=component&store_id=' . $store_id  . $streLinkPrarm . '&Itemid=' . $this->orderDetailItemId),strlen(Uri::root(true))+1); ?>"
											target="_blank">
												<img title="<?php echo Text::_('COM_QUICK2CART_INVOICE_VIEW_ICON_TITLE');?>" src="<?php echo Uri::root();?>components/com_quick2cart/assets/images/eye-icon.png"/>
											</a>
											<a href="<?php echo Uri::root().substr(Route::_('index.php?option=com_quick2cart&view=orders&task=orders.generateInvoicePDF&orderid=' . $orders->id . '&store_id=' . $store_id  . $streLinkPrarm . '&Itemid=' . $this->orderDetailItemId),strlen(Uri::root(true))+1); ?>" >
												<img title="<?php echo Text::_('COM_QUICK2CART_INVOICE_PDF_ICON_TITLE');?>" src="<?php echo Uri::root();?>components/com_quick2cart/assets/images/pdf_16.png"/>
											</a>
											<a onclick="qtcSendInvoiceEmail('<?php echo Uri::root() . 	'index.php?option=com_quick2cart&task=orders.resendInvoice&orderid=' . $orders->id . '&tmpl=component&store_id=' . $store_id  . $streLinkPrarm . '&Itemid=' . $this->orderDetailItemId ?>')" >
												<img class="invoice-pdf-img" title="<?php echo Text::_('COM_QUICK2CART_INVOICE_EMAIL_ICON_TITLE');?>" src="<?php echo Uri::root();?>components/com_quick2cart/assets/images/email_16.png"/>
											</a>
										</div>
									<?php
									}
									?>
								</td>

								<?php
								if (!$orders_site)
								{
									?>
										<td class='q2c_width_10 hidden-phone hidden-tablet  small nowrap' data-title="<?php echo Text::_('COM_QUICK2CART_ORDERS_GATEWAY');?>">
										<?php $this->paidPlgName = $this->comquick2cartHelper->getPluginName($orders->processor);
										echo $this->paidPlgName;?>
										</td>
									<?php
								}
								?>

								<?php
								if ((!$orders_site) || $this->layout == 'storeorder')
								{
									?>
									<td class="q2c_width_15 small qtcWordWrap" data-title="<?php echo Text::_('QTC_USERNAME');?>">
										<?php
										$user_id = intval($orders->payee_id);

										if ($user_id)
										{
											echo $orders->name . " (";
											echo $orders->username . ")<br/>";
											echo $orders->email;
										}
										else
										{
											echo $orders->email;
										}
										?>
									</td>
									<?php
								}
								?>

								<td class="qtc_pending_action q2c_width_30 small" data-title="<?php echo Text::_('QTC_ORDER_STATUS');?>">
									<?php
									if ((!($orders_site) || !empty($this->storeReleatedView)))
									{
										// For checkbox to send email status. SHOW ONLY  for admin or store owner
										?>
										<div class="row-fluid" >
											<span class="pull-left float-start">
												<label class="radio inline" for="add_note_chk_<?php echo $orders->id;?>">
													<?php echo Text::_('COM_QUICK2CART_ADD_NOTE');?>:
												</label>
												<input type="checkbox" id="add_note_chk_<?php echo $orders->id;?>"
												name = "add_note_chk|<?php echo $orders->id;?>"
												size= "10"
												onclick="showHideNoteTextarea(this,<?php echo $orders->id;?>)" />

											</span> &nbsp;
											<span class="pull-left float-start">
												<label class="radio inline" for="notify_chk_<?php echo $orders->id;?>">
													<?php echo Text::_('QTC_ORDER_SEND_MAIL');?>:
												</label>
												<input type="checkbox" id="notify_chk_<?php echo $orders->id;?>"
												name = "notify_chk|<?php echo $orders->id;?>"
												size= "10" checked />
											</span>
										</div>
										<?php
									}

									$validStatuses = $this->model->getValidOrderStatusList($orders->status, $this->pstatus);

									// Admin side
									if (!($orders_site))
									{
										echo HTMLHelper::_('select.genericlist', $validStatuses, "pstatus", 'class="pad_status input-medium pull-left float-start" size="1" ', "value", "text", $orders->status, 'pstatus' . $orders->id);
									}
									else if(!empty($this->storeReleatedView))
									{
										//-- ORDER CONFORM FOR STORE RELEATED VIEW
										$temp_vendorstatus = $this->vendorstatus;

										if ($orders->status == 'S' || $orders->status == 'RF' || $orders->status == 'E')
										{
											// Remove select status option
											unset($temp_vendorstatus[0]);
										}

										$temp_vendorstatus = $this->model->getValidOrderStatusList($orders->status, $temp_vendorstatus);

										echo "&nbsp;" . HTMLHelper::_('select.genericlist', $temp_vendorstatus, "vendor_orderItemStatus", 'class="pad_status input-small pull-left float-start"  ;" autocomplete="off"', "value", "text", $orders->status, 'pstatus' . $orders->id);
									}
									else
									{
										echo $whichever ;
									}

									// CODE END FOR ORDER STATUS || STATUS SELECT BOX END
									/* for complete your order  START */
									if (($orders->status == 'P') && $orders_site && $vendor_order_view == 0)
									{ ?>

										<a class="btn btn-success btn-sm validate" href="<?php echo Uri::base().substr(Route::_('index.php?option=com_quick2cart&view=orders&layout=order&orderid=' . $orders->id . '&Itemid=' . $this->orderDetailItemId . '&paybuttonstatus=1'), strlen(Uri::base(true)) + 1) . '#complete-order';?>">
											<?php echo Text::_('QTC_ORDER_COMPLETE_PAYMENT');?>
										</a>
										<?php
									}

									// Admin side selectstatusorder
									if (!($orders_site))
									{ ?>

										<button type="button" class="btn btn-sm" onClick="updateOrderStatus(<?php echo $orders->id; ?>, this);">
											<?php echo Text::_('COM_QUICK2CART_ORDER_STATUS_UPDATE');?>
										</button>
										<div style="clear: both;">
										<textarea id="order_note_<?php echo $orders->id;?>" name="order_note|<?php echo $orders->id;?>" rows="2" class="qtc_media_hide" placeholder="<?php echo Text::_('COM_QUICK2CART_ENTER_NOTE');?>"></textarea>
										</div>
										<?php
									}
									else if ((!($orders_site) || !empty($this->storeReleatedView)))
									{ ?>

										<button type="button" class="btn btn-sm" onClick="updateOrderStatus(<?php echo $orders->id; ?>, this);">
											<?php echo Text::_('COM_QUICK2CART_ORDER_STATUS_UPDATE');?>
										</button>
										<div style="clear: both;">
											<textarea id="order_note_<?php echo $orders->id;?>" name="order_note|<?php echo $orders->id;?>" rows="2" class="qtc_media_hide" placeholder="<?php echo Text::_('COM_QUICK2CART_ENTER_NOTE');?>"></textarea>
										</div>
									<?php
									} ?>
								</td>

								<td class="q2c_width_10 nowrap small " data-title="<?php echo Text::_('COM_QUICK2CART_PAYOUT_DATE');?>">
									<?php
									echo HTMLHelper::date($orders->cdate, Text::_('COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT'));
									echo '<br/>';
									echo HTMLHelper::date($orders->cdate, Text::_('COM_QUICK2CART_TIME_FORMAT_SHOW_AMPM'));
									?>
								</td>

								<!-- Order price-->
								<td class="q2c_width_10 nowrap rightalign" data-title="<?php echo Text::_('QTC_AMOUNT');?>">
									<?php
									// Getting store_id and current order_id releated  product and its total price
									$amount = $orders->amount;

									if (!empty($this->storeReleatedView))
									{
										if (!class_exists('quick2cartModelOrders'))
										{
											$path= JPATH_SITE . '/components/com_quick2cart/models/order.php';
											JLoader::register('quick2cartModelOrders', $path);
											JLoader::load('comquick2cartHelper');
										}

										$quick2cartModelOrders = new quick2cartModelOrders();
										$store_items_price = $quick2cartModelOrders->getStore_product_price($this->store_id, $orders->id);
										$amount = $orders->store_items_price = $store_items_price;
									}
									?>

									<span>
										<?php
										// @TODO have to remove this but getting error on removal
										$this->comquick2cartHelper = new comquick2cartHelper;
										 echo $this->comquick2cartHelper->getFromattedPrice($amount, $orders->currency);?>
									</span>
									<?php
									if (!empty($this->storeReleatedView))
									{
										$totalamount = $totalamount + $orders->store_items_price;
									}
									else
									{
										$totalamount = $totalamount + $orders->amount;
									}
									?>
								</td>
							</tr>
							<?php
						}
						// end of foreach
						?>
						</tbody>
						<tfoot>
						<?php
						// This is for adjusting colspan, as same view is used in many layouts
						if ( ($orders_site && $this->layout == 'default') || ($orders_site && $this->layout == 'customerdetails'))
						{
							$colspan = 2;
						}
						else
						{
							$colspan = 3;
						}

						// #49334 - doesn't make sence to display untill we show the report according to currency
						if ($orders_site && 0)
						{
							?>
							<!-- Total amount of all orders-->
							<tr>
								<td colspan="<?php echo $colspan;?>" class="hidden-phone"></td>
								<td class="hidden-xs rightalign"><b> <?php echo Text::_('QTC_PRODUCT_TOTAL'); ?></b></td>
								<td data-title="<?php echo Text::_('QTC_PRODUCT_TOTAL');?>" class="rightalign">
									<span>
										<b><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($totalamount, 2), $order_currency);?></b>
									</span>
								</td>
							</tr>

							<?php
							// IF SITE VIEW AND STORE RELEATED VIEW
							if (!empty($this->storeReleatedView))
							{
								$commission = $this->params->get('commission');
								$commissionApplied = $this->storeHelper->totalCommissionApplied($totalamount);
								$commissionCutTprice = (float)$totalamount - $commissionApplied;
								?>

								<tr>
									<td colspan="<?php echo $colspan;?>"  class="hidden-xs"></td>
									<td align="" class="hidden-xs rightalign"><strong><?php echo sprintf(Text::_('QTC_COMMISSION_CUT_SUB_TOT'),'('.$commission.'%)'); ?> </strong></td>
									<td data-title="<?php echo Text::_('QTC_COMMISSION_CUT_SUB_TOT');?>" class="rightalign">
										<span>
											<b><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($commissionApplied,2), $order_currency);?></b>
										</span>
									</td>
								</tr>

								<tr>
									<td colspan="<?php echo $colspan;?>" class="hidden-xs"></td>
									<td align="" class="hidden-xs rightalign"><strong><?php echo Text::_('QTC_TOTAL'); ?> </strong></td>
									<td class="rightalign" data-title="<?php echo Text::_('QTC_TOTAL');?> ">
										<span>
											<b><?php echo $this->comquick2cartHelper->getFromattedPrice(number_format($commissionCutTprice, 2), $order_currency);?></b>
										</span>
									</td>
								</tr>
							<?php
							}
						}
						?>
					</tfoot>
				</table>
				<div id="q2c-ajax-call-fade-content-transparent"></div>
				<div id="q2c-ajax-call-loader-modal">
					<img id="q2c-ajax-loader" src="<?php echo Uri::root() . 'components/com_quick2cart/assets/images/ajax.gif';?>" />
				</div>
			</div>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php endif; ?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="orders" />
		<input type="hidden" name="task" id="task" value="" />

		<input type="hidden" id='hidid' name="id" value="" />
		<input type="hidden" id='hidstat' name="status" value="" />

		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />

		<?php
			// Add CSRF token
			echo HTMLHelper::_('form.token');
		?>

		<?php if(!empty($this->store_id))
		{
			?>
				<input type="hidden" name="store_id" value="<?php echo $this->store_id;?>" />
			<?php
		}
		?>

		<?php
		// @ sice version 3.0 Jhtmlsidebar for menu
		if ($app->isClient('administrator'))
		{
			?>
				</div>
			<?php
		}
		?>
	</div>
</form>
