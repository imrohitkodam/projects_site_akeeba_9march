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
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');

$user      = Factory::getUser();
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

$input = Factory::getApplication()->input;
$cid   = $input->get('cid', '', 'ARRAY');

$comquick2cartHelper = new comquick2cartHelper;

// Used to store store_name against store_id.
$store_names = array();

// Store details
$store_details = $this->store_details;
?>

<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'products.csvExport')
		{
			url = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=products.csvExport&tmpl=component&"+"layout=my&"+Joomla.getOptions('csrf.token')+"=1";

			window.location.href = url;
		}
		else
		{
			if (task=='product.addNew')
			{
				Joomla.submitform(task);

				return true;
			}
			// This is for product import open popup
			else if (task=='Custom')
			{
				Joomla.submitform(task);

				return true;
			}
			else if (task=='product.edit')
			{
				if (document.adminForm.boxchecked.value===0)
				{
					alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_NO_PRODUCT_SELECTED')); ?>");

					return;
				}
				else if (document.adminForm.boxchecked.value > 1)
				{
					alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_MAKE_ONE_SEL')); ?>");

					return;
				}

				Joomla.submitform(task);
			}
			else
			{
				if (document.adminForm.boxchecked.value==0)
				{
					alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_NO_PRODUCT_SELECTED')); ?>");
					return false;
				}
				switch(task)
				{
					case 'category.publish':
						Joomla.submitform(task);
					break

					case 'category.unpublish':
						<?php
						$admin_approval_stores = (int) $this->params->get('admin_approval');

						if ($admin_approval_stores) :
						?>
							if (confirm("<?php echo Text::_('COM_QUICK2CART_MSG_CONFIRM_UNPUBLISH_PRODUCT'); ?>"))
							{
								Joomla.submitform(task);
							}
							else
							{
								return false;
							}
						<?php
						else:
						?>
							Joomla.submitform(task);
						<?php
						endif;
						?>
					break

					case 'category.delete':
						if (confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_CONFIRM_PRODUCT'); ?>"))
						{
							Joomla.submitform(task);
						}
						else
						{
							return false;
						}
					break
				}
			}
		}
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> my-products">
	<form  method="post" name="adminForm" id="adminForm" class="form-validate">
		<?php
		$input      = Factory::getApplication()->input;
		$option     = $input->get('option', '', 'STRING');

		if (!empty($this->store_role_list))
		{
			$active = 'my_products';
			ob_start();
			include($this->toolbar_view_path);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
		}
		?>

		<legend><?php echo Text::_('COM_QUICK2CART_MY_PRODUCTS') ?></legend>
		<?php echo $this->toolbarHTML;?>
		<div class="clearfix"> </div>
		<hr class="hr-condensed" />

		<div id="filter-bar" class="qtc-btn-toolbar">
			<div class="filter-search btn-group float-start">
				<input type="text" name="filter_search" id="filter_search"
				placeholder="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_PRODUCTS'); ?>"
				value="<?php echo $this->escape($this->state->get('filter.search')); ?>"
				class="qtc-hasTooltip input-medium"
				title="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_PRODUCTS'); ?>" />
			</div>

			<div class="float-start">
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

			<div class="pull-right float-end hidden-phone ">
				<label for="limit" class="element-invisible">
					<?php echo Text::_('COM_QUICK2CART_SEARCH_SEARCHLIMIT_DESC'); ?>
				</label>
				<?php echo $this->pagination->getLimitBox(); ?>
			</div>

			<div class="pull-right float-end hidden-phone">
			<?php
				echo HTMLHelper::_('select.genericlist', $this->statuses, "filter_published", 'class="input-medium"  onchange="document.adminForm.submit();" name="filter_published"', "value", "text", $this->state->get('filter.published'));
			?>
			</div>

			<div class="pull-right float-end hidden-phone">
				<select name="filter_category" class="inputbox input-medium" onchange="this.form.submit()">
					<option value=""><?php echo Text::_('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo HTMLHelper::_('select.options', HTMLHelper::_('category.options', 'com_quick2cart'), 'value', 'text', $this->state->get('filter.category'));?>
				</select>
			</div>
		</div>

		<div class="clearfix">&nbsp;</div>
		<div class="row-fluid qtc_productblog">
			<?php
			if (empty($this->items)) : ?>
				<div class="alert alert-warning">
					<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			else : ?>
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
							<th class="q2c_width_5 nowrap center">
								<?php echo Text::_('COM_QUICK2CART_EDIT'); ?>
							</th>
							<th class="q2c_width_15 hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_STORE_NAME', 'store_id', $listDirn, $listOrder); ?>
							</th>
							<th class="q2c_width_15 hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_CAT', 'category', $listDirn, $listOrder); ?>
							</th>
							<th class="q2c_width_10 nowrap center hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_CDATE', 'cdate', $listDirn, $listOrder); ?>
							</th>
							<th class="q2c_width_1 nowrap center hidden-phone">
								<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_ID', 'item_id', $listDirn, $listOrder); ?>
							</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$comquick2cartHelper = new comquick2cartHelper;
						$k = 0;

						if (!empty($this->products))
						{
							$n = count($this->products);

							for ($i=0; $i < $n; $i++)
							{
								$zone_type = '';
								$row       = $this->products[$i];
								$link      = $comquick2cartHelper->getProductLink($row->item_id, 'detailsLink');
								$edit_link = $comquick2cartHelper->getProductLink($row->item_id, 'editLink');
								$link      = '<a href="'. $link . '">' . $row->name . '</a>';
								$edit_link = '<a href="'. $edit_link . '">' . Text::_('QTC_EDIT') . '</a>';
								?>
								<tr class="<?php echo 'row'.$k; ?>">
									<td class="q2c_width_1 nowrap center">
										<?php echo HTMLHelper::_('grid.id', $i, $row->item_id); ?>
									</td>
									<td class=''><?php echo $link; ?></td>
									<td class="q2c_width_1 nowrap center">
										<a class=" "
											href="javascript:void(0);"
											title="<?php echo ( $row->state ) ? Text::_('QTC_UNPUBLISH') : Text::_('QTC_PUBLISH'); ;?>"
											onclick="document.adminForm.cb<?php echo $i;?>.checked=1; document.adminForm.boxchecked.value=1; Joomla.submitbutton('<?php echo ($row->state) ? 'category.unpublish' : 'category.publish';?>');">
												<img class="q2c_button_publish" src="<?php echo Uri::root(true);?>/components/com_quick2cart/assets/images/<?php echo ($row->state) ? 'publish.png' : 'unpublish.png';?>"/>
										</a>
									</td>
									<td class="q2c_width_5 nowrap center"><?php echo $edit_link; ?></td>
									<td class="q2c_width_15 hidden-phone small">
										<?php
										if (!empty($store_details[$row->store_id]))
										{
											echo $store_details[$row->store_id]['title'];
										}
										?>
									</td>
									<td class="q2c_width_15 hidden-phone small">
										<?php
											$catname = $comquick2cartHelper->getCatName($row->category);
											echo !empty($catname) ? Text::_(trim($catname)) : $row->category;
										 ?>
									</td>
									<td class="q2c_width_10 nowrap center hidden-phone small">
										<?php
										if ($row->cdate !='0000-00-00 00:00:00')
										{
											$cdate=date("Y-m-d",strtotime($row->cdate));
											echo $cdate;
										}
										else
										{
											echo "-";
										}
										?>
									</td>
									<td class="q2c_width_1 nowrap center hidden-phone small">
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
						}
						// End if products.
						?>
					</tbody>
				</table>
				<?php echo $this->pagination->getListFooter(); ?>
			<?php endif; ?>

			<input type="hidden" name="option" value="com_quick2cart" />
			<input type="hidden" name="view" value="category" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
	<div style="display:none">
		<div id="import_products">
			<?php $uploadUrl = Uri::base() . 'index.php?option=com_quick2cart&task=products.csvImport&tmpl=component&format=html'?>
			<form action="<?php echo $uploadUrl;?>" id="uploadForm" class="form-inline center" name="uploadForm" method="post" enctype="multipart/form-data">
				<table>
					<tr>&nbsp;</tr>
						<div id="uploadform">
							<fieldset id="upload-noflash" class="actions">
								<label for="upload-file" class="control-label">
									<?php echo Text::_('COM_QUICK2CART_UPLOADE_FILE');?>
								</label>
								<input type="file" id="upload-file" name="csvfile" id="csvfile" accept=".csv"/>
								<button class="btn btn-primary" id="upload-submit">
									<i class="icon-upload icon-white"></i>
									<?php echo Text::_('COM_QUICK2CART_PRODUCTS_IMPORT_CSV'); ?>
								</button>
								<hr class="hr hr-condensed">
								<div class="alert alert-warning" role="alert"><i class="icon-info"></i>
									<?php echo Text::_('COM_QUICK2CART_CSVHELP');?>
								</div>
							</fieldset>
						</div>
					<tr></tr>
				</table>
			</form>
		</div>
	</div>
</div>
