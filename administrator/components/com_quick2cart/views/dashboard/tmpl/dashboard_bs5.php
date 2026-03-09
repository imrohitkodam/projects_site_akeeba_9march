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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('stylesheet', 'media/techjoomla_strapper/bs3/css/bootstrap.min.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/morris.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/tjdashboard-sb-admin.css');
HTMLHelper::_('script', 'libraries/techjoomla/assets/js/houseKeeping.js');

$document = Factory::getDocument();
$document->addScriptDeclaration("var tjHouseKeepingView='dashboard';");

$session = Factory::getSession();
$session->set('tj_from_date', '');
$session->set('tj_end_date', '');
$session->set('statsforpie', '');
$session->set('ignorecnt', '');
$session->set('statsfor_line_day_str_final', '');
$session->set('statsfor_line_imprs', '');
$session->set('statsfor_line_clicks', '');
$session->set('periodicorderscount', '');

$mntnm_cnt = 1;
$i         = 0;
$kk        = 0;
$ignorecnt = array();
$curdate   = '';
$backdate  = date('Y-m-d', strtotime(date('Y-m-d') . ' - 30 days'));

$comquick2cartHelper = new comquick2cartHelper;
$dash_currency       = $comquick2cartHelper->getCurrencySymbol();
$model               = $this->getModel('dashboard');

foreach ($this->AllMonthName as $AllMonthName)
{
	$AllMonthName_final[$i]   = $AllMonthName['month'];
	$curr_MON                 = $AllMonthName['month'];
	$month_amt_val[$curr_MON] = 0;
	$i++;
}

$barChartData = 0;

foreach ($this->MonthIncome as $MonthIncome)
{
	$month_year = '';
	$month_year = $MonthIncome->YEARNM;
	$month_name = $MonthIncome->MONTHSNAME;
	$month_int  = (int)$month_name;
	$timestamp  = mktime(0, 0, 0, $month_int);
	$curr_month = date("F", $timestamp);

	foreach ($this->AllMonthName as $AllMonthName)
	{
		if (($curr_month == $AllMonthName['month']) and ($MonthIncome->amount) and ($month_year==$AllMonthName['year']))
		{
			$month_amt_val[$curr_month] = str_replace(",", '', $MonthIncome->amount);
		}

		if ($barChartData==0)
		{
			if ($MonthIncome->amount)
			{
				$barChartData = 1;
			}
		}
	}
}

$month_amt_str    = implode(",", $month_amt_val);
$month_name_str   = implode("','", $AllMonthName_final);
$month_name_str   = "'" . $month_name_str . "'";
$month_array_name = array();

$js = "
	function refreshViews()
	{
		var dash_currency = \"" . $dash_currency . "\";
		fromDate = document.getElementById('from').value;
		toDate = document.getElementById('to').value;
		fromDate1 = new Date(fromDate.toString());
		toDate1 = new Date(toDate.toString());
		difference = toDate1 - fromDate1;
		days = Math.round(difference/(1000*60*60*24));

		if (parseInt(days) < 0)
		{
			alert(\"".Text::_('DATELESS')."\");

			return;
		}

		/*Set Session Variables*/
		var info = {};
		techjoomla.jQuery.ajax({
			type: 'GET',
			url: 'index.php?option=com_quick2cart&task=dashboard.SetsessionForGraph&fromDate='+fromDate+'&toDate='+toDate,
			dataType: 'json',
			async:false,
			success: function(data) {
			}
		});

		/*Get periodic data and redraw chart*/
		techjoomla.jQuery.ajax({
			type: 'GET',
			url: 'index.php?option=com_quick2cart&task=dashboard.makechart',
			dataType: 'json',
			success: function(data)
			{
				techjoomla.jQuery('#bar_chart_graph').html('' + data.barchart);
				/*Reset hidden field values*/
				document.getElementById('pending_orders').value=data.pending_orders;
				document.getElementById('confirmed_orders').value=data.confirmed_orders;
				document.getElementById('shiped_orders').value=data.shiped_orders;
				document.getElementById('refund_orders').value=data.refund_orders;
				/*Redraw charts*/
				document.getElementById('periodic_orders').innerHTML = dash_currency + ' ' + data.periodicorderscount;
				drawPeriodicOrdersChart();
			}
		});
	}";

$document->addScriptDeclaration($js);
?>

<?php
	$versionHTML = '<span class="label label-info">' .
	Text::_('COM_QUICK2CART_HAVE_INSTALLED_VER') . ': ' . $this->version .'</span>';

	if ($this->latestVersion && $this->latestVersion->version > $this->version)
	{
		$versionHTML = '<div class="alert alert-error">' .'<i class="icon-puzzle install"></i>' .Text::_('COM_QUICK2CART_HAVE_INSTALLED_VER') . ': ' . $this->version .'<br/>' .'<i class="icon icon-info"></i>' .Text::_("COM_QUICK2CART_NEW_VER_AVAIL") . ': ' . '<span class="qtc_latest_version_number">' . $this->latestVersion->version . '</span><br/>' . '<i class="icon icon-warning"></i>' . '<span class="small">' . Text::_("COM_QUICK2CART_LIVE_UPDATE_BACKUP_WARNING") . '</span>' . '</div><div><a href="index.php?option=com_installer&view=update" class="qtc-btn-wrapper btn btn-sm btn-primary">' . Text::sprintf('COM_QUICK2CART_LIVE_UPDATE_TEXT', $this->latestVersion->version) . '</a><a href="' . $this->latestVersion->infourl . '/?utm_source=clientinstallation&utm_medium=dashboard&utm_term=quick2cart&utm_content=updatedetailslink&utm_campaign=quick2cart_ci' . '" target="_blank" class="qtc-btn-wrapper btn btn-sm btn-info">' . Text::_('COM_QUICK2CART_LIVE_UPDATE_KNOW_MORE') . '</a></div>';
	}
?>
<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-admin-dashboard">
	<div id="j-main-container">
		<form name="adminForm" id="adminForm" class="form-validate" method="post">
			<div class="tjBs3">
				<div class="tjDB">
					<div id="wrapper">
						<div id="page-wrapper">
							<div class="clearfix">&nbsp;</div>
							<?php
							if (!$this->allincome)
							{
								$this->allincome=0;
							}

							$this->allincome = $comquick2cartHelper->getFromattedPrice($this->allincome);
							?>
							<!-- Start - stat boxes -->
							<div class="row">
								<div class="col-lg-3 col-md-6">
									<div class="panel panel-green">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3 ">
													<i class="far fa-money-bill-alt fa-4x"></i>
												</div>
												<div class="col-xs-9 af-text-right">
													<div class="huge"><?php echo $this->allincome;?> </div>
													<div><?php echo Text::_('ALL_TIME_INCOME');?></div>
												</div>
											</div>
										</div>
										<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders', false);?>">
											<div class="panel-footer">
												<span class="float-start">
													<?php echo Text::_('COM_QUICK2CART_VIEW_DETAILS');?>
												</span>
												<span class="float-end">
													<i class="fa fa-arrow-circle-right"></i>
												</span>
												<div class="clearfix"></div>
											</div>
										</a>
									</div>
								</div>
								<div class="col-lg-3 col-md-6">
									<div class="panel panel-primary">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-barcode fa-4x"></i>
												</div>
												<div class="col-xs-9 af-text-right">
													<div class="huge"><?php echo $this->productsCount;?> </div>
													<div><?php echo Text::_('COM_QUICK2CART_TOTAL_PRODUCTS');?></div>
												</div>
											</div>
										</div>
										<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=products', false);?>">
											<div class="panel-footer">
												<span class="float-start">
													<?php echo Text::_('COM_QUICK2CART_VIEW_DETAILS');?>
												</span>
												<span class="float-end">
													<i class="fa fa-arrow-circle-right"></i>
												</span>
												<div class="clearfix"></div>
											</div>
										</a>
									</div>
								</div>
								<div class="col-lg-3 col-md-6">
									<div class="panel panel-yellow">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-shopping-cart fa-4x"></i>
												</div>
												<div class="col-xs-9 af-text-right">
													<div class="huge"><?php echo $this->ordersCount; ?></div>
													<div><?php echo Text::_('COM_QUICK2CART_TOTAL_ORDERS');?></div>
												</div>
											</div>
										</div>
										<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders', false);?>">
											<div class="panel-footer">
												<span class="float-start">
													<?php echo Text::_('COM_QUICK2CART_VIEW_DETAILS');?>
												</span>
												<span class="float-end">
													<i class="fa fa-arrow-circle-right"></i>
												</span>
												<div class="clearfix"></div>
											</div>
										</a>
									</div>
								</div>
								<div class="col-lg-3 col-md-6">
									<div class="panel panel-red">
										<div class="panel-heading">
											<div class="row">
												<div class="col-xs-3">
													<i class="fa fa-bookmark fa-4x"></i>
												</div>
												<div class="col-xs-9 af-text-right">
													<div class="huge"><?php echo $this->storesCount; ?></div>
													<div><?php echo Text::_('COM_QUICK2CART_TOTAL_STORES');?></div>
												</div>
											</div>
										</div>
										<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=stores', false);?>">
											<div class="panel-footer">
												<span class="float-start">
													<?php echo Text::_('COM_QUICK2CART_VIEW_DETAILS');?>
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
							<div class="row">
								<div class="col-lg-8">
									<!-- Start - Bar Chart for Monthly Income for past 12 months -->
									<div class="panel panel-default">
										<div class="panel-heading">
											<i class="far fa-chart-bar"></i>
											<?php echo Text::_('MONTHLY_INCOME_MONTH');?>
										</div>
										<div class="panel-body">
											<div id="graph-monthly-sales"></div>
											<hr class="hr hr-condensed"/>
											<div class="af-text-center">
												<?php echo Text::_('BAR_CHART_HAXIS_TITLE');?>
											</div>
										</div>
									</div>
									<!-- End - Bar Chart for Monthly Income for past 12 months -->
									<div class="row">
										<div class="col-lg-6">
											<div class="panel panel-default">
												<div class="panel-heading">
													<i class="fas fa-chart-pie"></i>
													<?php echo Text::_('PERIODIC_ORDERS');?>
												</div>
												<div class="panel-body">
													<!-- CALENDER ND REFRESH BTN  -->
													<div class="clearfix row">
														<div class="col-sm-12">
															<div class="float-end">
																<div class="form-group af-mb-10">
																	<label label-default class="col-md-2 col-sm-3 col-xs-12 control-label"><?php echo Text::_('FROM_DATE');?>
																	</label>
																	<div class="col-sm-10 col-xs-12">
																		<div class="input-group">
																			<?php echo HTMLHelper::_('calendar', $backdate, 'fromDate', 'from', '%Y-%m-%d', array('class' => 'inputbox input-xs', 'style' => 'min-height:35px!important; max-width:90px!important;')); ?>
																		</div>
																	</div>
																</div>
																<div class="form-group af-mb-10">
																	<label label-default class="col-md-2 col-sm-3 col-xs-12 control-label"><?php echo Text::_('TO_DATE');?></label>
																	<div class="col-sm-10 col-xs-12">
																		<div class="input-group">
																			<?php echo HTMLHelper::_('calendar', date('Y-m-d'), 'toDate', 'to', '%Y-%m-%d', array('class' =>'inputbox input-xs', 'style' => 'min-height:35px!important; max-width:90px!important;')); ?>
																		</div>
																	</div>
																</div>
																<div class="form-group">
																	<div class="col-xs-12">
																		<div class="">
																			<input id="btnRefresh" class="float-end btn btn-mini btn-primary" type="button" value="<?php echo Text::_('COM_QUICK2CART_GO'); ?>" style="font-weight: bold;" onclick="refreshViews();" title="<?php echo Text::_('COM_QUICK2CART_GO_TOOLTIP');?>"/>
																		</div>
																	</div>
																</div>
															</div>
														</div>
														<div class="clearifx"></div>
													</div>
													<!--END::CALENDER ND REFRESH BTN  -->
													<div class="clearifx">&nbsp;</div>
													<?php
													if (!$this->tot_periodicorderscount)
													{
														$this->tot_periodicorderscount=0;
													}
													?>
													<div class="list-group">
														<span class="list-group-item">
															<i class="far fa-money-bill-alt"></i>
															<?php echo Text::_('PERIODIC_INCOME');?>
															<span class="float-end text-muted small">
																<strong id="periodic_orders">
																	<?php echo $comquick2cartHelper->getFromattedPrice($this->tot_periodicorderscount);?>
																</strong>
															</span>
														</span>
													</div>

													<!-- Periodic orders - graph start -->
													<div id="graph-periodic-orders"></div>
													<hr class="hr hr-condensed"/>
													<div class="af-text-center">
														<strong class="">
															<?php echo Text::_('PERIODIC_ORDERS');?>
														</strong>
													</div>
													<!-- Periodic orders - graph end -->
												</div>
												<!-- /.panel-body -->
											</div>
										</div>
										<!-- /.col-lg-6 -->
										<div class="col-lg-6">
											<!-- Start - not shipped orders -->
											<div class="panel panel-default">
												<div class="panel-heading">
													<i class="fa fa-list fa-fw"></i>
													<?php echo Text::_('COM_Q2C_NOTSHIPPED_ORDERS');?>
												</div>
												<div class="panel-body">
													<?php
													if(!empty($this->notShippedDetails))
													{
														?>
														<table class="table table-striped table-hover" >
															<thead>
																<th><?php echo Text::_('QTC_ORDER_ID')?></th>
																<th><?php echo Text::_('QTC_NAME')?></th>
																<th><?php echo Text::_('QTC_AMOUNT')?></th>
															</thead>
															<tbody>
																<?php
																foreach($this->notShippedDetails as $ord)
																{
																	?>
																	<tr>
																		<td>
																			<a href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=order&orderid='.$ord['id'], false); ?>"
																				title="<?php echo Text::_('QTC_TOOLTIP_VIEW_ORDER_MSG');?>">
																				<?php echo $ord['prefix'] . $ord['id'];?>
																			</a>
																		</td>
																		<td><?php echo $ord['name'];?></td>
																		<td><?php echo $comquick2cartHelper->getFromattedPrice($ord['amount'])?></td>
																	</tr>
																	<?php
																}
																?>
															</tbody>
														</table>

														<a title="<?php echo Text::_('QTC_STORE_SHOW_ALL');?>"
															class="btn btn-primary btn-sm float-end"
															href="<?php echo Route::_('index.php?option=com_quick2cart&view=orders');?>"
															target="_blank" >
																<?php echo Text::_('QTC_STORE_SHOW_ALL'); ?>
														</a>
														<?php
													}
													else
													{
														?>
														<div class="af-text-center">
															<?php echo Text::_("NO_STORE_PREVIOUS_ORDERS");?>
														</div>
														<?php
													}
													?>
												</div>
											</div>
											<!-- End - not shipped orders -->

											<?php
											if(!empty($this->multivendor_enable))
											{
												?>
													<!-- Start - not shipped orders -->
													<div class="panel panel-default">
														<div class="panel-heading">
															<i class="fa fa-list fa-fw"></i>
															<?php echo Text::_('COM_PENDING_PAYOUTS');?>
														</div>
														<div class="panel-body">
															<?php
															if(!empty($this->getpendingPayouts))
															{
																?>
																<table class="table table-striped table-hover">
																	<thead>
																		<th><?php echo Text::_('QTC_NAME')?></th>
																		<th><?php echo Text::_('QTC_REMAINING_AMOUNT')?></th>
																	</thead>
																	<tbody>
																		<?php
																		$count = 1;

																		foreach ($this->getpendingPayouts as $pay)
																		{
																			?>
																			<tr>
																				<?php
																				$uname = $comquick2cartHelper->getUserName($pay->user_id);
																				$amt = (float)$pay->total_amount - $pay->fee;
																				?>
																				<td><?php echo $uname;?></td>
																				<td><?php echo $comquick2cartHelper->getFromattedPrice($amt); ?></td>
																			</tr>
																			<?php
																			if ($count >= 5)
																			{
																				break;
																			}
																		}
																		?>
																	</tbody>
																</table>

																<a title="<?php echo Text::_('QTC_STORE_SHOW_ALL');?>"
																	class="btn btn-primary btn-sm float-end"
																	href="<?php echo Route::_('index.php?option=com_quick2cart&task=payout.add');?>"
																	target="_blank" >
																		<?php echo Text::_('QTC_STORE_SHOW_ALL'); ?>
																</a>
																<?php
															}
															else
															{
																?>
																<div class="af-text-center">
																	<?php echo Text::_("COM_QUICK2CART_DASHBORD_NO_PENDING_PAYOUTS");?>
																</div>
																<?php
															}
															?>
														</div>
													</div>
													<!-- End - not shipped orders -->
												<?php
											}
											?>
										</div>
										<!-- /.col-lg-6 -->
									</div>
									<!-- /.row -->
								</div>
								<!-- /.col-lg-8 -->
								<div class="col-lg-4">
									<?php if (!$this->downloadid): ?>
										<div class="">
											<div class="clearfix float-end">
												<div class="alert alert-warning">
													<?php echo Text::sprintf('COM_QUICK2CART_LIVE_UPDATE_DOWNLOAD_ID_MSG', '<a href="https://techjoomla.com/my-account/add-on-download-ids">' . Text::_("COM_QUICK2CART_LIVE_UPDATE_DOWNLOAD_ID_MSG2") . '</a>'); ?>
												</div>
											</div>
										</div>
									<?php endif; ?>
									<div class="">
										<div class="clearfix float-end">
											<?php echo $versionHTML; ?>
										</div>
									</div>
									<div class="clearfix"></div><br>

									<!--INFO,HELP + ETC START -->
									<div class="panel panel-default">
										<div class="panel-heading">
											<i class="fa fa-shopping-cart"></i>
											<?php echo Text::_('QTC_KART'); ?>
										</div>
										<div class="panel-body">
											<div class="">
												<blockquote class="blockquote-reverse">
													<p><?php echo Text::_('ABOUT1');?></p>
												</blockquote>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<p class="float-end"><span class="label label-info"><?php echo Text::_('COM_QUICK2CART_LINKS'); ?></span></p>
												</div>
											</div>
											<div class="list-group">
												<a href="http://techjoomla.com/documentation-for-quick2cart/quick-start-for-using-quick2cart-with-ccks-like-joomla-content-zoo-k2-a-flexi-content.html" class="list-group-item">
													<i class="fa fa-file fa-fw i-document"></i> <?php echo Text::_('COM_QUICK2CART_CCK_DOCS');?>
												</a>
												<a href="http://techjoomla.com/table/extension-documentation/documentation-for-quick2cart/" class="list-group-item">
													<i class="fa fa-file fa-fw i-document"></i> <?php echo Text::_('COM_QUICK2CART_DOCS');?>
												</a>
												<a href="http://techjoomla.com/documentation-for-quick2cart/quick-start-for-using-quick2carts-simple-product-manager.html" class="list-group-item">
													<i class="fa fa-file fa-fw i-document"></i> <?php echo Text::_('COM_QUICK2CART_NATIVE_DOCS');?>
												</a>

												<?php
												if ($this->showEasySocialMsg)
												{
													// show if not installed
													?>
													<a href="http://techjoomla.com/documentation-for-quick2cart/integration-with-easysocial.html" class="list-group-item">
														<i class="fa fa-file fa-fw i-document"></i> <?php echo Text::_('COM_QUICK2CART_INTEGRATION_WITH_EASY_SOCIAL');?>
													</a>
													<?php
												}
												?>
												<a href="http://techjoomla.com/documentation-for-quick2cart/quick2cart-faqs.html" class="list-group-item">
													<i class="fa fa-question fa-fw i-question"></i> <?php echo Text::_('COM_QUICK2CART_FAQS');?>
												</a>
												<a href="http://techjoomla.com/support/support-tickets" class="list-group-item">
													<i class="fas fa-life-ring"></i>
													<?php echo Text::_('COM_QUICK2CART_TECHJOOMLA_SUPPORT_CENTER');?>
												</a>
												<a href="http://extensions.joomla.org/extensions/e-commerce/shopping-cart/23958" class="list-group-item">
													<i class="fa fa-bullhorn fa-fw i-horn"></i> <?php echo Text::_('COM_QUICK2CART_LEAVE_JED_FEEDBACK');?>
												</a>
											</div>
											<div class="row">
												<div class="col-sm-12">
													<p class="float-end">
														<span class="label label-info"><?php echo Text::_('COM_QUICK2CART_STAY_TUNNED'); ?></span>
													</p>
												</div>
											</div>
											<div class="list-group">
												<div class="list-group-item">
													<div class="float-start">
														<i class="fab fa-facebook-f"></i>
														<?php echo Text::_('COM_QUICK2CART_FACEBOOK'); ?>
													</div>
													<div class="float-end">
														<!-- facebook button code -->
														<div id="fb-root"></div>
														<script>(function(d, s, id) {
														  var js, fjs = d.getElementsByTagName(s)[0];
														  if (d.getElementById(id)) return;
														  js = d.createElement(s); js.id = id;
														  js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
														  fjs.parentNode.insertBefore(js, fjs);
														}(document, 'script', 'facebook-jssdk'));</script>
														<div class="fb-like" data-href="https://www.facebook.com/techjoomla" data-send="true" data-layout="button_count" data-width="250" data-show-faces="false" data-font="verdana"></div>
													</div>
													<div class="clearfix">&nbsp;</div>
												</div>
												<div class="list-group-item">
													<div class="float-start">
														<i class="fab fa-twitter"></i>
														<?php echo Text::_('COM_QUICK2CART_TWITTER'); ?>
													</div>
													<div class="float-end">
														<!-- twitter button code -->
														<a href="https://twitter.com/techjoomla" class="twitter-follow-button" data-show-count="false">Follow @techjoomla</a>
														<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
													</div>
													<div class="clearfix">&nbsp;</div>
												</div>
												<div class="list-group-item">
													<div class="float-start">
														<i class="fab fa-google"></i>
														<?php echo Text::_('COM_QUICK2CART_GPLUS'); ?>
													</div>
													<div class="float-end">
														<!-- Place this tag where you want the +1 button to render. -->
														<div class="g-plusone" data-annotation="inline" data-width="120" data-href="https://plus.google.com/102908017252609853905"></div>
														<!-- Place this tag after the last +1 button tag. -->
														<script type="text/javascript">
														(function() {
														var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
														po.src = 'https://apis.google.com/js/plusone.js';
														var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
														})();
														</script>
													</div>
													<div class="clearfix">&nbsp;</div>
												</div>
											</div>
											<div class="row">
												<div class="col-sm-12 center">
													<?php
													$logo = '<img src="' . Uri::base() . 'components/com_quick2cart/assets/images/techjoomla.png" alt="TechJoomla" class=""/>';
													?>
													<span class="center thumbnail">
														<a href='http://techjoomla.com/'>
															<?php echo $logo;?>
														</a>
													</span>
													<p><?php echo Text::_('COM_QUICK2CART_COPYRIGHT'); ?></p>
												</div>
											</div>
										</div><!-- /.panel-body -->
									</div><!-- /.panel -->
								</div><!-- /.col-lg-4 -->
							</div><!-- /.row -->
						</div><!-- /#page-wrapper -->
					</div>
				</div><!-- /.tjDB TJ Dashboard -->
			</div><!-- /.tjBs3TJ TJ Bootstrap3 -->
			<?php
			// Get data for periodic orders chart
			$statsforpie    = $this->statsforpie;
			$currentmonth   = '';
			$pending_orders = $confirmed_orders=$shiped_orders=$refund_orders=0;

			if(empty($statsforpie[0][0]) && empty($statsforpie[1][0]) && empty($statsforpie[2][0]))
			{
				$barchart       = Text::_('NO_STATS');
				$emptylinechart = 1;
			}
			else
			{
				if(!empty($statsforpie[0]))
				{
					$pending_orders = $statsforpie[0][0]->orders;
				}

				if(!empty($statsforpie[1]))
				{
					$confirmed_orders = $statsforpie[1][0]->orders;
				}

				if(!empty($statsforpie[2]))
				{
					$refund_orders = $statsforpie[2][0]->orders;
				}

				if(!empty($statsforpie[3]))
				{
					$shiped_orders = $statsforpie[3][0]->orders;
				}
			}

			$emptypiechart=0;

			if(!$pending_orders and !$confirmed_orders and !$refund_orders and !$shiped_orders)
			{
				$emptypiechart=1;
			}
			?>

			<input type="hidden" name="pending_orders" id="pending_orders" value="<?php if($pending_orders) echo $pending_orders; else echo '0'; ?>">
			<input type="hidden" name="confirmed_orders" id="confirmed_orders" value="<?php if($confirmed_orders) echo $confirmed_orders; else echo '0';  ?>">
			<input type="hidden" name="shiped_orders" id="shiped_orders" value="<?php if($shiped_orders) echo $shiped_orders; else echo '0';  ?>">
			<input type="hidden" name="refund_orders" id="refund_orders" value="<?php if($refund_orders) echo $refund_orders; else echo '0'; ?>">
		</form>
	</div>
</div>

<script type="text/javascript">
	techjoomla.jQuery(document).ready(function()
	{
		document.getElementById("pending_orders").value=<?php if($pending_orders) echo $pending_orders; else echo '0'; ?>;
		document.getElementById("confirmed_orders").value=<?php if($confirmed_orders) echo $confirmed_orders; else echo '0'; ?>;
		document.getElementById("shiped_orders").value=<?php if($shiped_orders) echo $shiped_orders; else echo '0'; ?>;
		document.getElementById("refund_orders").value=<?php  if($refund_orders) echo $refund_orders; else echo '0'; ?>;
		drawPeriodicOrdersChart();
	});

	<?php if ($barChartData): ?>
		drawBarChart();

		function drawBarChart()
		{
			Morris.Bar({
				element: 'graph-monthly-sales',
				data:
					<?php
						$dataArray = "[";

						for ($i = 0; $i < count($AllMonthName_final); $i++)
						{
							$dataArray .= "{period : '" . $AllMonthName_final[$i] . "', salesTotal : " . $month_amt_val[$AllMonthName_final[$i]] . "},";
						}
						$dataArray .= "]";
						echo $dataArray;
					?>,
				xkey: 'period',
				ykeys: ['salesTotal'],
				labels: ['<?php echo Text::_('BAR_CHART_VAXIS_TITLE') . ' ('.$dash_currency.')';?>'],
				barColors: ['#428bca'],
				barRatio: 0.4,
				xLabelAngle: 35,
				hideHover: 'auto',
				resize:true
			});
		}
	<?php else: ?>
			techjoomla.jQuery('#graph-monthly-sales').html("<div class='text-center'><?php echo Text::_("NO_STORE_PREVIOUS_ORDERS");?></div>");
	<?php endif; ?>

	function drawPeriodicOrdersChart()
	{
		techjoomla.jQuery('#graph-periodic-orders').html('');

		var pending_orders   = document.getElementById('pending_orders').value;
		var confirmed_orders = document.getElementById('confirmed_orders').value;
		var shiped_orders    = document.getElementById('shiped_orders').value;
		var refund_orders    = document.getElementById('refund_orders').value;

		if (pending_orders > 0  || confirmed_orders > 0 || shiped_orders > 0 || refund_orders > 0)
		{
			Morris.Donut({
				element: 'graph-periodic-orders',
				data: [{
					label: "<?php echo Text::_("PENDING_ORDS");?>",
					value: pending_orders
				}, {
					label: "<?php echo Text::_("CONFIRM_ORDS");?>",
					value: confirmed_orders
				}, {
					label: "<?php echo Text::_("SHIPPED_ORDS");?>",
					value: shiped_orders
				 }, {
					label: "<?php echo Text::_("REFUND_ORDS");?>",
					value: refund_orders
				}],
				colors: ["#f0ad4e", "#5cb85c", "#428bca", "#d9534f"],
				resize: true
			});
		}
		else
		{
			techjoomla.jQuery('#graph-periodic-orders').html("<div class='text-center'><?php echo Text::_("NO_STORE_PREVIOUS_ORDERS");?></div>");
		}
	}
</script>
