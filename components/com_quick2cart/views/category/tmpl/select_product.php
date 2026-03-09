<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// action = Uri::root() . 'index.php?option=com_quick2cart&view=category&layout=select_product&tmpl=component

// No direct access
defined('_JEXEC') or die();

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');

Text::script('COM_QUICK2CART_PROMOTION_CONDITION_SELECT_PRODUCT_ALERT', true);

$id        = Factory::getApplication()->input->get('id', "0", 'INT');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?> my-products container-fluid af-d-none">
	<div class="row">
		<form  method="post" name="adminForm" id="adminForm" class="form-validate">
			<div class="row">
				<div class="col-sm-12">
					<div id="j-main-container">
						<div class="alert alert-info af-mt-10">
							<div class="center">
								<h5><?php echo Text::_("COM_QUICK2CART_SELECT_PRODUCT");?></h5>
							</div>
						</div>
						<div class="">
							<div id="filter-bar" class="js-stools">
								<div class="js-stools-container-selector btn-group pull-left float-start af-d-flex">
									<input
										type="text"
										name="filter_search"
										id="filter_search"
										placeholder="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_PRODUCTS'); ?>"
										value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
										class="hasTooltip input-medium form-control"
										title="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_PRODUCTS'); ?>" />
									<button
										type="submit"
										class="btn hasTooltip"
										title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
										<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
									</button>
									<button
										type="button"
										class="btn  btn-secondary hasTooltip"
										title="<?php echo Text::_('JSEARCH_FILTER_CLEAR'); ?>"
										onclick="document.getElementById('filter_search').value='';this.form.submit();">
										<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
									</button>
								</div>
								<div class="btn-group pull-right float-end btn-wrapper">
									<label for="limit" class="element-invisible">
										<?php echo Text::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?>
									</label>
									<?php echo $this->pagination->getLimitBox(); ?>
								</div>
								<div class="btn-group pull-right float-end hidden-phone btn-wrapper af-mr-5">
									<select name="filter_category" class="form-select" onchange="this.form.submit()">
										<option value=""><?php echo Text::_('JOPTION_SELECT_CATEGORY');?></option>
										<?php echo HTMLHelper::_('select.options', HTMLHelper::_('category.options', 'com_quick2cart'), 'value', 'text', $this->state->get('filter.category'));?>
									</select>
								</div>
								<div class="clearfix"> &nbsp;</div><br/>
							</div>
						</div>
						<div class="clearfix">&nbsp;</div>
						<div class=" qtc_productblog">
							<?php
							if (empty($this->items))
							{ ?>
								<div class="alert alert-warning">
									<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
								</div>
							<?php
							}
							else
							{
								?>
								<table class="table table-striped table-bordered table-responsive" id="productList">
									<thead>
										<tr>
											<th class="q2c_width_1 nowrap center">
												<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
											</th>
											<th class=''>
												<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_NAM', 'name', $listDirn, $listOrder);?>
											</th>
											<th class="q2c_width_1 nowrap center">
												<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PUB', 'state', $listDirn, $listOrder); ?>
											</th>
											<th class="q2c_width_15 hidden-xs">
												<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_CAT', 'category', $listDirn, $listOrder); ?>
											</th>
											<th class="q2c_width_1 nowrap center hidden-xs">
												<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_ID', 'item_id', $listDirn, $listOrder); ?>
											</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$comquick2cartHelper = new comquick2cartHelper;
										$k = 0;

										foreach ($this->products as $row)
										{
											?>
											<tr class="<?php echo 'row'.$k." "; ?> qtc-products" id="qtc-selected-product<?php echo $row->item_id;?>">
												<td class="q2c_width_1 nowrap center">
													<?php echo HTMLHelper::_('grid.id', $row->item_id, $row->item_id); ?>
												</td>
												<td>
													<?php echo $row->name; ?>
												</td>
												<td class="q2c_width_1 nowrap center">
													<?php echo ($row->state == '1')?Text::_("QTC_PROD_PUBLISH"):Text::_("QTC_PROD_UNPUBLISH"); ?>
												</td>
												<td class="q2c_width_15 hidden-xs">
													<?php
														$catname = $comquick2cartHelper->getCatName($row->category);
														echo !empty($catname) ? $catname : $row->category;
													?>
												</td>
												<td class="q2c_width_1 nowrap center hidden-xs">
													<?php echo $row->item_id; ?>
												</td>
											</tr>
											<?php
											if ($k%2!=1)
											{
												$k++;
											}
											else
											{
												$k = 0;
											}
										}
										?>
									</tbody>
								</table>
								<div class="center af-text-center">
									<a class="btn btn-large btn-success m-2 submitprod">
										<?php echo Text::_('QTC_APPLY');?>
									</a>
								</div>
								<?php
							}?>
							<input type="hidden" name="view" value="category" />
							<input type="hidden" name="storeId" class="storeId" value="<?php echo $this->storeId; ?>" />
							<input type="hidden" name="task" value="" />
							<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
							<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
							<?php echo HTMLHelper::_('form.token'); ?>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
<script>
techjoomla.jQuery(document).ready(function ()
{
	var id = window.parent.selectedConditionCategory;
	var storeId = window.parent.storeId;
	var rootURL = `<?php echo Uri::root(); ?>`;
	var action = rootURL + 'index.php?option=com_quick2cart&view=category&layout=select_product&tmpl=component&store_id=' + storeId;
	jQuery('#adminForm') . attr('action', action);

	if (!jQuery('.storeId') . val() || jQuery('.storeId') . val() != storeId)
	{
		jQuery('.storeId') . val(storeId);
		jQuery('#adminForm') . submit();

	}
	else
	{
		jQuery('.my-products.container-fluid') . removeClass('af-d-none');
	}
	jQuery('#limit') . removeAttr('size');

	var selectedProds = window.parent.document.getElementById('rule_conditions_'+id+'_condition_attribute_value').value;
	var selectedProdsArray = selectedProds.split(",");

	for (i = 0; i < selectedProdsArray.length; i++)
	{
		techjoomla.jQuery("#qtc-selected-product"+selectedProdsArray[i]+" input").prop('checked', true);
	}

	jQuery(document).on('click', '.submitprod', function(e) {
		if (!techjoomla.jQuery('.qtc-products :checked').length)
		{
			alert(Joomla.JText._("COM_QUICK2CART_PROMOTION_CONDITION_SELECT_PRODUCT_ALERT"));

			return false;
		}

		var flag = 0;
		var selectedProducts ='';

		techjoomla.jQuery('.qtc-products :checked').each(function() {
			
			if (Number(flag) != 0)
			{
				selectedProducts += ",";
			}

			selectedProducts += techjoomla.jQuery(this).val();

			flag++;
		});

		window.parent.document.getElementById('rule_conditions_'+id+'_condition_attribute_value').value = selectedProducts;
	
		window.parent.jQuery('#promotionConditionOptionProduct .modal-header button').click();
	});
});
</script>
