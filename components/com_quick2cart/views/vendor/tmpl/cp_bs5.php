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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('behavior.framework');
}

HTMLHelper::_('bootstrap.renderModal');

$document = Factory::getDocument();

// Load css files
$app            = Factory::getApplication();
$currentBSViews = $this->params->get('bootstrap_version', "bs3");
$load_boostrap  = $this->params->get('qtcLoadBootstrap', 1);

// Load Css
if (!empty($load_boostrap))
{
	HTMLHelper::_('stylesheet', 'media/techjoomla_strapper/bs3/css/bootstrap.min.css');
}

HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/morris.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/tjdashboard-sb-admin.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/quick2cart.css');

$comquick2cartHelper = new comquick2cartHelper;

// Global icon constants.
define('Q2C_DASHBORD_ICON_ORDERS', "fa fa-shopping-cart fa-3x");
define('Q2C_DASHBORD_ICON_ITEMS', "fa fa-barcode fa-3x");
define('Q2C_DASHBORD_ICON_SALES', "far fa-money-bill-alt fa-3x");
define('Q2C_DASHBORD_ICON_AVG_ORDER', "fa fa-bars fa-3x");
define('Q2C_DASHBORD_ICON_ALL_SALES', "far fa-money-bill-alt fa-3x");
define('Q2C_DASHBORD_ICON_USERS', "fa fa-users fa-3x");

// CHECK LOGIN STATUS
$user = Factory::getUser();

if (!$user->id)
{
	$return = base64_encode(Uri::getInstance());
	$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
	$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
	$app->redirect($login_url_with_return, 403);
}

// CHECK WHETHER User HAS STORE
if (!$this->store_id)
{
?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<?php
		$msg = Text::sprintf('COM_QUICK2CART_MULTIVENDOR_OFF_CANNT_CREATE_MSG');

		if ($this->params->get('multivendor'))
		{
			$createstore_Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');
			$createStore_link   = Route::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&Itemid='.$createstore_Itemid);
			$clickhere          = '<a href="'.$createStore_link.'">'.Text::_( 'QTC_CLICK_HERE' ).'</a> '.Text::_( 'QTC_TO_CREATE_STORE' );
			$msg                = Text::sprintf('NO_STORE_FOUND',$clickhere);
		}

		$app->enqueueMessage($msg, 'Notice');
		?>
	</div>
	<!-- eoc techjoomla-bootstrap -->
	<?php
	return false;
}

// Take date a one year back in past.
$backdate = date('Y-m-d', strtotime(date('Y-m-d').' - 365 days'));
$store_id = $this->store_id;
$js = "
	var linechart_imprs;
	var linechart_clicks;
	var linechart_day_str=new Array();

	function refreshViews()
	{
		fromDate = document.getElementById('from').value;
		toDate = document.getElementById('to').value;
		fromDate1 = new Date(fromDate.toString());
		toDate1 = new Date(toDate.toString());
		difference = toDate1 - fromDate1;
		days = Math.round(difference/(1000*60*60*24));
		if (parseInt(days) < 0)
		{
			alert(\"".Text::_('COM_QUICK2CART_DATELESS')."\");
			return;

		}
		techjoomla.jQuery.ajax({
			type: 'GET',
			url: 'index.php?option=com_quick2cart&task=vendor.refreshVendorDashboard&tmpl=component&fromDate='+fromDate+'&toDate='+toDate+'&storeid=2',
			async:false,
			dataType: 'json',
			success: function(data)
			{
				window.location.reload();
			}
		});
	}";

	$document->addScriptDeclaration($js);
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> tj-dashboard">
	<form name="adminForm" id="adminForm" class="form-validate" method="post">
		<div class="qtc_toolbarDiv">
			<?php
			$active = 'cp';
			$view = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
			?>
		</div>

		<h1>
			<strong>
			<?php
			if (!empty($this->store_role_list))
			{
				$storehelp = new storeHelper();
				$index     = $storehelp->array_search2d($this->store_id, $this->store_role_list);

				if (is_numeric($index))
				{
					$store_name = $this->store_role_list[$index]['title'];
				}

				echo Text::sprintf('QTC_STORE_DASHBOARD',$store_name);
			}
			else
			{
				echo Text::_('QTC_STORE_DASHBOARD_DEFAULT');
			}
			?>
			</strong>
		</h1>

		<?php
		//If there is no products in store, then provide such msg.
		if (empty($this->prodcountprodCount))
		{
			$addProd_Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=product&layout=default');
			$addprodlink    = Route::_('index.php?option=com_quick2cart&view=product&current_store=' . $store_id . '&Itemid=' . $addProd_Itemid);
			$clickhere      = '<a href="' . $addprodlink . '">' . Text::_('QTC_CLICK_HERE') . '</a>' . Text::_('QTC_TO_ADD_PROD');
			$app->enqueueMessage(Text::sprintf('NO_PROD_AGAINST_STORE', $clickhere), 'Notice');
		}
		?>
		<!-- TJ Bootstrap3 -->
		<div class="tjBs3">
			<!-- TJ Dashboard -->
			<div class="tjDB">
				<div class="row mt-4">
					<?php
						$backdate    = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 30 days'));
						$backdate    = $app->getUserStateFromRequest('from', 'from', $backdate, 'string');
						$currentdate = date('Y-m-d H:i:s');
						$currentdate = $app->getUserStateFromRequest('to', 'to', $currentdate, 'string');
					?>
					<div class="col-lg-4 col-sm-5 col-xs-9">
						<div class="form-group row">
							<label label-default class="col-sm-2 col-xs-3 form-label align-self-center px-2">
								<?php echo Text::_('QTC_FROM_DATE'); ?>
							</label>
							<div class="col-sm-9 col-xs-9 p-0">
								<div class="input-group">
									<?php echo HTMLHelper::_('calendar', $backdate, 'from', 'from', '%Y-%m-%d', array('class'=>'input-small form-control', 'readonly'=>'true')); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-5 col-xs-9">
						<div class="form-group row">
							<label label-default class="col-sm-2 col-xs-3 form-label align-self-center px-4">
								<?php echo Text::_('QTC_TO_DATE'); ?>
							</label>
							<div class="col-sm-9 col-xs-9 p-0">
								<div class="input-group">
									<?php echo HTMLHelper::_('calendar', $currentdate, 'to', 'to', '%Y-%m-%d', array('class'=>'input-small form-control', 'readonly'=>'true')); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-2 col-xs-3">
						<button class="btn btn-primary" onclick="refreshViews()" title="<?php echo Text::_('COM_QUICK2CART_DASHB_GO_TOOLTIP');?>">
							<?php echo Text::_('COM_QUICK2CART_FILTER_GO');?>
						</button>
					</div>
					<div class="clearfix"></div>
				</div>

				<!--Periodic-Quick-stats-->
				<?php $perdIncome = $this->getPeriodicIncome;?>
				<!-- Start - stat boxes -->
				<div class="row mt-4">
					<div class="col-sm-4 col-lg-4 col-md-12">
						<div class="card panel-green">
							<div class="card-heading">
								<div class="row">
									<div class="col-sm-3 col-xs-3">
										<i class="<?php echo Q2C_DASHBORD_ICON_SALES; ?>"></i>
									</div>
									<div class="col-sm-9 col-xs-9 af-text-right">
										<div class="huge">
											<?php echo !empty($perdIncome['amount']) ? $comquick2cartHelper->getFromattedPrice(number_format($perdIncome['amount'], 2)) : "0"; ?>
										</div>
									</div>
								</div>
							</div>
							<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=storeorder&Itemid=' . $this->orders_itemid, false);?>">
								<div class="card-footer">
									<span class="float-start">
										<?php echo Text::_('COM_Q2C_PRD_REVENUE');?>
									</span>
									<span class="float-end">
										<i class="fa fa-arrow-circle-right"></i>
									</span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-sm-4 col-lg-4 col-md-6">
						<div class="card panel-primary">
							<div class="card-heading">
								<div class="row">
									<div class="col-xs-3 col-sm-3">
										<i class="<?php echo Q2C_DASHBORD_ICON_ORDERS; ?>"></i>
									</div>
									<div class="col-xs-9 col-sm-9 af-text-right">
										<div class="huge">
											<?php echo !empty($perdIncome['totorders']) ? $perdIncome['totorders'] : "0"; ?>
										</div>
									</div>
								</div>
							</div>
							<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=storeorder&Itemid=' . $this->orders_itemid, false);?>">
								<div class="card-footer">
									<span class="float-start">
										<?php echo Text::_('COM_Q2C_PRD_TOTORDER');?>
									</span>
									<span class="float-end">
										<i class="fa fa-arrow-circle-right"></i>
									</span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-sm-4 col-lg-4 col-md-6">
						<div class="card panel-yellow">
							<div class="card-heading">
								<div class="row">
									<div class="col-xs-3 col-sm-3">
										<i class="<?php echo Q2C_DASHBORD_ICON_ITEMS; ?>"></i>
									</div>
									<div class="col-xs-9 col-sm-9 af-text-right">
										<div class="huge">
											<?php echo !empty($perdIncome['qty']) ? $perdIncome['qty'] : "0"; ?>
										</div>
									</div>
								</div>
							</div>
							<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=storeorder&Itemid=' . $this->orders_itemid, false);?>">
								<div class="card-footer">
									<span class="float-start">
										<?php echo Text::_('COM_Q2C_PRD_QTY');?>
									</span>
									<span class="float-end">
										<i class="fa fa-arrow-circle-right"></i>
									</span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
				</div>
				<!-- End - stat boxes -->

				<?php
				$chartsDataFlag = 0;

				if ($this->getPeriodicIncomeGrapthData)
				{
					// Get formatted data for charts.
					$incomedata = $comquick2cartHelper->getLineChartFormattedData($this->getPeriodicIncomeGrapthData);
					$chartsDataFlag = 1;
				}
				?>

				<!--Periodic-Graphs-->
				<div class="row mt-4">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-header">
								<i class="fa fa-area-chart fa-fw"></i>
								<?php echo Text::_('COM_QUICK2CART_STORE_STATS'); ?>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-sm-12">
										<?php
										echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'overview_1', 'recall' => true, 'breakpoint' => 768]);
											echo HTMLHelper::_('uitab.addTab', 'myTab', 'overview_1', Text::_('COM_QUICK2CART_STORE_SALES_AMOUNT'));
											?>
												<div class="row">
													<div class="col-sm-12">
														<?php
														if ($chartsDataFlag)
														{ ?>
															<div id="q2c_chart_amount" style="height: 250px;"></div>
														<?php 
														}
														else
														{ ?>
															<div>&nbsp;</div>
															<div class="alert alert-info">
																<?php echo Text::_("COM_Q2C_NO_PERIODIC_INCOME");?>
															</div>
														<?php 
														}?>
													</div>
												</div>
											<?php
											echo HTMLHelper::_('uitab.endTab');
											echo HTMLHelper::_('uitab.addTab', 'myTab', 'overview_3', Text::_('COM_QUICK2CART_STORE_ORDERS_PLACED'));
											?>
												<div class="row">
													<div class="col-sm-12">
														<?php if ($chartsDataFlag): ?>
															<div id="q2c_chart_orders" style="height: 250px;"></div>
														<?php else: ?>
															<div>&nbsp;</div>
															<div class="alert alert-info">
																<?php echo Text::_("COM_Q2C_NO_PERIODIC_INCOME");?>
															</div>
														<?php endif;?>
													</div>
												</div>
											<?php
											echo HTMLHelper::_('uitab.endTab');
										echo HTMLHelper::_('uitab.endTabSet');
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!--Global-Quick-stats-->
				<div class="row mt-4">
					<div class="col-sm-4 col-lg-4 col-md-12">
						<div class="card panel-green">
							<div class="card-heading">
								<div class="row">
									<div class="col-sm-3 col-xs-3">
										<i class="<?php echo Q2C_DASHBORD_ICON_ALL_SALES; ?>"></i>
									</div>
									<div class="col-sm-9 col-xs-9 af-text-right">
										<div class="huge"><?php echo $comquick2cartHelper->getFromattedPrice(number_format($this->totalSales, 2)); ?></div>
									</div>
								</div>
							</div>
							<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=storeorder&Itemid=' . $this->orders_itemid, false);?>">
								<div class="card-footer">
									<span class="float-start">
										<?php echo Text::_('COM_Q2C_TOTAL_SALE');?>
									</span>
									<span class="float-end">
										<i class="fa fa-arrow-circle-right"></i>
									</span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<?php $avg = (!empty($this->totalOrdersCount)) ? (($this->totalSales) / $this->totalOrdersCount) : 0; ?>
					<div class="col-sm-4 col-lg-4 col-md-6">
						<div class="card panel-primary">
							<div class="card-heading">
								<div class="row">
									<div class="col-sm-3 col-xs-3 ">
										<i class="<?php echo Q2C_DASHBORD_ICON_AVG_ORDER; ?>"></i>
									</div>
									<div class="col-sm-9 col-xs-9 af-text-right">
										<div class="huge"><?php echo $comquick2cartHelper->getFromattedPrice(number_format($avg, 2)); ?></div>
									</div>
								</div>
							</div>
							<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=storeorder&Itemid=' . $this->orders_itemid, false);?>">
								<div class="card-footer">
									<span class="float-start">
										<?php echo Text::_('COM_Q2C_AVG_ORDERS');?>
									</span>
									<span class="float-end">
										<i class="fa fa-arrow-circle-right"></i>
									</span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
					<div class="col-sm-4 col-lg-4 col-md-6">
						<div class="card panel-yellow">
							<div class="card-heading">
								<div class="row">
									<div class="col-sm-3 col-xs-3">
										<i class="<?php echo Q2C_DASHBORD_ICON_USERS; ?>"></i>
									</div>
									<div class="col-sm-9 col-xs-9 af-text-right">
										<div class="huge"><?php echo $this->storeCustomersCount; ?></div>
									</div>
								</div>
							</div>
							<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=mycustomer&Itemid=' . $this->store_customers_itemid, false);?>">
								<div class="card-footer">
									<span class="float-start">
										<?php echo Text::_('COM_QUICK2CART_CUSTOMERS');?>
									</span>
									<span class="float-end">
										<i class="fa fa-arrow-circle-right"></i>
									</span>
									<div class="clearfix"></div>
								</div>
							</a>
						</div>
					</div>
				</div>
				<!-- End - stat boxes -->
				<!--End Global-Quick-stats-->

				<!--Periodic-Graphs-->
				<div class="row mt-4">
					<div class="col-sm-12">
						<div class="card">
							<div class="card-header">
								<i class="fa fa-list fa-fw"></i>
								<span><?php echo Text::_('COM_QUICK2CART_QUICK_REPORTS'); ?></span>
							</div>
							<div class="card-body">
								<div class="row">
									<div class="col-sm-12">
										<?php
										echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'report_4', 'recall' => true, 'breakpoint' => 768]);
											echo HTMLHelper::_('uitab.addTab', 'myTab', 'report_4', Text::_('COM_Q2C_LAST_5_ORDERS'));
												if (!empty($this->last5orders))
												{ ?>
													<div>&nbsp;</div>
													<table class="table table-striped table-hover table-bordered">
														<thead class="table-primary border table-bordered">
															<tr>
																<th><?php echo Text::_('COM_QUICK2CART_DASHB_ID'); ?></th>
																<th><?php echo Text::_('COM_QUICK2CART_DASHB_NAME'); ?></th>
																<th class="hidden-xs"><?php echo Text::_('COM_QUICK2CART_DASHB_DATE'); ?></th>
																<th class=""><?php echo Text::_('COM_QUICK2CART_DASHB_AMOUNT'); ?></th>
																<th class=""><?php echo Text::_('COM_QUICK2CART_DASHB_STATUS'); ?></th>
															</tr>
														</thead>
														<tbody>
															<?php
															foreach($this->last5orders as $ord)
															{
																$order_currency            = $comquick2cartHelper->getCurrencySymbol($ord['currency']);
																$this->store_orders_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders&layout=storeorder');
																?>
																<tr>
																	<td>
																		<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=order&orderid=' . $ord['id'] . '&store_id=' . $this->store_id . '&calledStoreview=1&Itemid=' . $this->store_orders_itemid, false); ?>">
																			<?php echo HTMLHelper::tooltip(Text::sprintf('QTC_TOOLTIP_VIEW_ORDER_MSG', $ord['prefix'] . $ord['id']), Text::_('QTC_TOOLTIP_VIEW_ORDER'), '', $ord['prefix'] . $ord['id']) ;?>
																		</a>
																	</td>
																	<td><?php echo $ord['name'];?></td>
																	<td class="hidden-xs">
																		<?php
																		echo Factory::getDate($ord['cdate'])->Format(Text::_('COM_QUICK2CART_DATE_FORMAT_SHOW_SHORT'));
																		echo '<br/>';
																		echo Factory::getDate($ord['cdate'])->Format(Text::_('COM_QUICK2CART_TIME_FORMAT_SHOW_AMPM'));
																		?>
																	</td>
																	<td class="">
																		<?php echo $comquick2cartHelper->getFromattedPrice(number_format($ord['price'],2),$order_currency)?>
																	</td>
																	<td class="">
																		<?php
																		switch($ord['status'])
																		{
																			case 'C':
																			$labelClass    = 'label-success';
																			$ord['status'] = Text::_('QTC_CONFR');
																			break;

																			default:
																			case 'P':
																			$labelClass    = 'label-warning';
																			$ord['status'] = Text::_('QTC_PENDIN');
																			break;

																			case 'RF':
																			$labelClass    = 'label-danger';
																			$ord['status'] = Text::_('QTC_REFUN');
																			break;

																			case 'S' :
																			$labelClass    = 'label-success';
																			$ord['status'] = Text::_('QTC_SHIP');
																			break;

																			case 'E' :
																			$labelClass    = 'label-danger';
																			$ord['status'] = Text::_('QTC_ERR');
																			break;
																		}
																		?>
																		<span class="label label-sm <?php echo $labelClass;?> ">
																			<?php echo $ord['status'];?>
																		</span>
																	</td>
																</tr>
																<?php
															}
															?>
														</tbody>
													</table>
												<?php 
												}
												else
												{?>
													<div>&nbsp;</div>
													<div class="alert alert-info">
														<?php echo Text::_("NO_STORE_PREVIOUS_ORDERS");?>
													</div>
												<?php 
												}
											echo HTMLHelper::_('uitab.endTab');
											echo HTMLHelper::_('uitab.addTab', 'myTab', 'report_1', Text::_('COM_QUICK2CART_TOP_SELLER_PRODUCTS'));
												if (!empty($this->topSellerProducts))
												{
													?>
													<div>&nbsp;</div>
													<table class="table table-striped table-hover table-bordered">
														<thead class="table-primary border table-bordered">
															<tr>
																<th><?php echo Text::_('QTC_PRODUCT_NAM'); ?></th>
																<th class="center"><?php echo Text::_('COM_QUICK2CART_DASHB_QUANTITY_SOLD'); ?></th>
															</tr>
														</thead>
														<tbody>
															<?php
															foreach($this->topSellerProducts as $product)
															{
																$p_link ='index.php?option=com_quick2cart&view=productpage&layout=default&item_id='.$product['item_id'] . '&Itemid=' . $this->catpage_Itemid;
																$product_link = Route::_($p_link, false);
																?>
																<tr>
																	<td>
																		<a href="<?php echo $product_link; ?>"><?php echo $product['name']; ?></a>
																	</td>
																	<td class="center"><?php echo $product['qty'];?></td>
																</tr>
																<?php
															}
															?>
														</tbody>
													</table>
													<?php
												}
												else
												{
													?>
													<div>&nbsp;</div>
													<div class="alert alert-info">
														<?php echo Text::_("NO_STORE_PREVIOUS_ORDERS");?>
													</div>
													<?php
												}
											echo HTMLHelper::_('uitab.endTab');
										echo HTMLHelper::_('uitab.endTabSet');
										?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<!--Store-info-->
		<div class="row">
			<div class="col-sm-12 col-xs-12">
				<?php if (!empty($this->storeDetailInfo)) : ?>
					<div>
						<?php
						$this->editstoreBtn  = 1;
						$view = $comquick2cartHelper->getViewpath('vendor', 'storeinfo_bs5');
						ob_start();
						include($view);
						$html = ob_get_contents();
						ob_end_clean();
						echo $html;
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="vendor" />
		<input type="hidden" name="layout" value="cp" />
	</form>
</div>

<script type="text/javascript">
	<?php if ($chartsDataFlag): ?>
		Morris.Area({
			element: 'q2c_chart_amount',
			data: <?php echo $incomedata[0];?>,
			xkey: 'period',
			ykeys: ['amount'],
			labels: ['<?php echo Text::_('COM_QUICK2CART_STORE_SALES_AMOUNT'); ?>'],
			lineWidth: 2,
			hideHover: 'auto',
			lineColors: ["#30a1ec"]
		});

		function drawOrdersChart()
		{
			setTimeout(function(){
				techjoomla.jQuery('#q2c_chart_orders').html('');

				Morris.Area({
					element: 'q2c_chart_orders',
					data: <?php echo $incomedata[1]; ?>,
					xkey: 'period',
					ykeys: ['orders'],
					labels: ['<?php echo Text::_('COM_QUICK2CART_STORE_ORDERS_PLACED'); ?>'],
					lineWidth: 2,
					hideHover: 'auto',
					lineColors: ["#8ac368"]
				});
			}, 300);
		}
	<?php endif; ?>
	jQuery(document).ready(function() {
		window.onload = function() {
			drawOrdersChart();
		};
		jQuery(document).on('click', '[aria-controls="overview_3"]', function (e) {
			drawOrdersChart();
		});
	});
</script>
