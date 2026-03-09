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
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('formbehavior.chosen', 'select');

// used to store store_name against store_id
$store_names         = array();
$comquick2cartHelper = new comquick2cartHelper;
$store_details       = $this->store_details;
$document            = Factory::getDocument();
$js = "
	function validateDates()
	{
		fromDate = document.getElementById('from').value;
		toDate = document.getElementById('to').value;

		fromDate1 = new Date(fromDate.toString());
		toDate1 = new Date(toDate.toString());

		difference = toDate1 - fromDate1;
		days = Math.round(difference/(1000*60*60*24));

		if (parseInt(days)<=0)
		{
			alert(\"" . Text::_('DATELESS') . "\");

			return false;
		}

		return true;
	}";

$document->addScriptDeclaration($js);
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'salesreport.csvexport')
		{
			url = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=salesreport.csvExport&tmpl=component&"+Joomla.getOptions('csrf.token')+"=1";
			window.location.href = url;
		}
		else 
		{
			window.location = 'index.php?option=com_quick2cart&view=salesreport';
		}
	}
</script>

<style>
	.calendar-header{
		font-size:13px !important;
	}
</style>
<form method="post" name="adminForm" id="adminForm" class="form-validate">
	<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-salesreport">
		<?php
		if (!empty( $this->sidebar)) : ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
		<?php else : ?>
		<div id="j-main-container">
		<?php endif;
		?>
			<div class="row-fluid">
				<div id="message" class="alert alert-success">
					<?php echo Text::_('SALES_REPORT_NOTE'); ?>
				</div>
			</div>
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input
						type="text"
						name="filter_search"
						id="filter_search"
						placeholder="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_SALESREPORT'); ?>"
						value="<?php echo htmlspecialchars($this->lists['search']);?>"
						class="hasTooltip input-medium"
						title="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_SALESREPORT'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip" title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
						<i class="icon-search"></i>
					</button>
					<button type="button" class="btn hasTooltip" title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();">
						<i class="icon-remove"></i>
					</button>
				</div>
				<div class="btn-group pull-right btn-wrapper">
					<label for="limit" class="element-invisible">
						<?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
					</label>
					<?php echo $this->pagination->getLimitBox(); ?>
				</div>
				<div class="btn-group pull-right hidden-phone btn-wrapper">
					<?php
					if (!empty($this->store_details))
					{
						$options[] = HTMLHelper::_('select.option', 0, Text::_('QTC_SELET_STORE'));

						if (count($this->store_details)>1)
						{
							$default=!empty($this->lists['search_store'])?$this->lists['search_store']:0;

							foreach($this->store_details as $key=>$value)
							{
								$options[] = HTMLHelper::_('select.option', $key,$value['title']);
							}

							echo $this->dropdown = HTMLHelper::_('select.genericlist', $options, 'search_store','class="input-medium hidden-mobile" size="1" onchange="document.adminForm.submit();" ','value','text',$default);
						}
					}
					?>
				</div>

				<!-- CALENDER ND REFRESH BTN  -->
				<?php
				$backdate = (!empty($this->lists['salesfromDate'])) ? $this->lists['salesfromDate'] : date('Y-m-d', strtotime(date('Y-m-d').' - 30 days'));
				$toDate   = (!empty($this->lists['salestoDate'])) ? $this->lists['salestoDate'] : date('Y-m-d');
				?>
				<div class="pull-left af-ml-5">
					<?php echo HTMLHelper::_('calendar', $backdate, 'salesfromDate', 'from', '%Y-%m-%d', array('class'=>'inputbox input-medium')); ?>
				</div>
				<div class="pull-left af-ml-5">
					<?php echo HTMLHelper::_('calendar', $toDate, 'salestoDate', 'to', '%Y-%m-%d', array('class'=>'inputbox input-medium')); ?>
				</div>
				<div class="pull-left af-ml-5">
					<input id="btnRefresh" class="btn btn-medium btn-primary qtcMarginBothSide" type="button" value="<?php echo Text::_('COM_QUICK2CART_GO'); ?>" style="font-weight: bold;" onclick="if(validateDates()){ this.form.submit();}"/>
				</div>
				<div class="clearfix">&nbsp;</div>
			</div>
			<div class="clearfix">&nbsp;</div>
			<?php
			if (empty($this->items)) : ?>
				<div class="clearfix">&nbsp;</div>
				<div class="alert alert-warning">
					<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
				</div>
		<?php
			else : ?>
			<div id='no-more-tables'>
				<table class="table table-striped">
					<thead>
						<tr>
							<th class="">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_SALESREPORT_PROD_NAME', 'item_name', $this->lists['order_Dir'], $this->lists['order']);?>
							</th>
							<th class="q2c_width_10 center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_SALESREPORT_STORE_ITEMID', 'item_id', $this->lists['order_Dir'], $this->lists['order']);?>
							</th>
							<th class="q2c_width_15">
								<?php echo Text::_('COM_QUICK2CART_STORE_NAME'); ?>
							</th>
							<th class="q2c_width_10 center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_SALESREPORT_STORE_ID', 'store_id', $this->lists['order_Dir'], $this->lists['order']);?>
							</th>
							<th class="q2c_width_10 center">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_SALESREPORT_QTY', 'saleqty', $this->lists['order_Dir'], $this->lists['order']);?>
							</th>
							<th class="q2c_width_10">
								<?php echo Text::_('COM_QUICK2CART_SALESREPORT_AMOUNT');?>
							</th>
							<th class="q2c_width_10">
								<?php echo Text::_('COM_QUICK2CART_SALESREPORT_CREATED_BY');?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$k = 0;
						$n = count($this->items);

						for ($i=0, $n; $i < $n; $i++)
						{
							$zone_type = '';
							$row       = $this->items[$i];
							$published = HTMLHelper::_('grid.published', $row->state, $i );
							$link      = Uri::root().'index.php?option=com_quick2cart&view=productpage&layout=default&item_id='.$row->item_id;
							?>
							<tr class="<?php echo 'row' . $k; ?>">
								<td class="" data-title="<?php echo Text::_('COM_QUICK2CART_SALESREPORT_PROD_NAME');?>">
									<a href="<?php echo $link; ?>" target="_blank">
										<?php echo $row->item_name;?>
									</a>
								</td>
								<td class="q2c_width_10 center" data-title="<?php echo Text::_('COM_QUICK2CART_SALESREPORT_STORE_ITEMID');?>">
									<?php echo $row->item_id; ?>
								</td>
								<td class="q2c_width_15" data-title="<?php echo Text::_('COM_QUICK2CART_STORE_NAME');?>">
									<?php
									if (!empty($store_details[$row->store_id]))
									{
										echo $store_details[$row->store_id]['title'];
									}
									?>
								</td>
								<td class="q2c_width_10 center" data-title="<?php echo Text::_('COM_QUICK2CART_SALESREPORT_STORE_ID');?>">
									<?php echo  $row->store_id; ?>
								</td>
								<td class="q2c_width_10 center" data-title="<?php echo Text::_('COM_QUICK2CART_SALESREPORT_QTY');?>">
									<?php echo $row->saleqty;?>
								</td>
								<td class="q2c_width_10" data-title="<?php echo Text::_('COM_QUICK2CART_SALESREPORT_AMOUNT');?>">
									<?php
									$productHelper  = new productHelper;
									$prodAttDetails = $productHelper->getProdPriceWithDefltAttributePrice( $row->item_id);
									$prodBasePrice  = !is_null($prodAttDetails['itemdetail']['discount_price']) ? $prodAttDetails['itemdetail']['discount_price'] : $prodAttDetails['itemdetail']['price'];
									$prodPrice      = $prodBasePrice + $prodAttDetails['attrDetail']['tot_att_price'];
									echo $comquick2cartHelper->getFromattedPrice($prodPrice);
								 ?>
								</td>
								<!-- created by-->
								<td class="q2c_width_10" data-title="<?php echo Text::_('COM_QUICK2CART_SALESREPORT_CREATED_BY');?>">
									<?php
									if (!empty($store_details[$row->store_id]))
									{
										echo $store_details[$row->store_id]['firstname'];
									}
									?>
								</td>
							</tr>
							<?php
							$k = 1 - $k;
							$k ++;
						}
						?>
					</tbody>
				</table>
			</div>
			<?php echo $this->pagination->getListFooter(); ?>
		<?php endif; ?>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="salesreport" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
