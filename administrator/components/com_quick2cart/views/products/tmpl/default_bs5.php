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
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$app       = Factory::getApplication();
$user      = Factory::getUser();
$userId    = $user->id;
$input     = $app->input;
$cid       = $input->get( 'cid', '', 'ARRAY');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'a.ordering');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_quick2cart&task=products.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
	HTMLHelper::_('sortablelist.sortable', 'productsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

// Used to store store_name against store_id.
$store_names         = array();
$comquick2cartHelper = new comquick2cartHelper;
$store_details       = $this->store_details;
?>

<script type="text/javascript">
	Joomla.submitbutton = function(action)
	{
		if (action=='products.csvExport')
		{
			url = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&task=products.csvExport&tmpl=component&"+Joomla.getOptions('csrf.token')+"=1";
			window.location.href = url;
		}
		else
		{
			if (action=='products.publish' || action=='products.unpublish')
			{
				Joomla.submitform(action);
			}
			else if (action=='products.delete')
			{
				var r=confirm("<?php echo Text::_('QTC_DELETE_CONFIRM_PROD');?>");
				if (r==true)
				{
					var aa;
				}
				else
				{
					return;
				}
			}
			else
			{
				window.location = 'index.php?option=com_quick2cart&view=products';
			}
			var form = document.adminForm;
			Joomla.submitform( action );
			return;
		}
	}

	function enableUploadButton() {
		jQuery('#import_products #upload-submit') . prop('disabled', false);
	}
</script>
<div>
	<form  method="post" name="adminForm" id="adminForm" class="form-validate">
		<div class="<?php echo Q2C_WRAPPER_CLASS;?> q2c-products">
			<div id="j-main-container">
				<div class="row">
					<div class="col-sm-4">
					</div>
					<div class="col-sm-8">
						<?php echo LayoutHelper::render('joomla.searchtools.default', array('view' => $this));?>
					</div>
				</div>
				<?php
				if (empty($this->items))
				{ ?>
					<div class="clearfix">&nbsp;</div>
					<div class="alert alert-warning">
						<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php
				}
				else
				{ ?>
					<table class="table table-striped" id="productsList">
						<thead>
							<tr>
								<th width="1%" class="nowrap center hidden-phone">
									<?php echo HTMLHelper::_('grid.sort', '<i class="icon-menu-2"></i>', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING');?>
								</th>
								<td class="w-1 af-text-center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</td>
								<th class="nowrap q2c_width_5 center">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_PUB', 'state', $listDirn, $listOrder); ?>
								</th>
								<th>
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_NAM', 'name', $listDirn, $listOrder);?>
								</th>
								<th class="nowrap q2c_width_5 center hidden-phone hidden-tablet">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_FEATURED', 'featured', $listDirn, $listOrder); ?>
								</th>
								<?php
									if ($this->params->get('usestock') == 1 && $this->params->get('usestock') !== null)
									{
										?>
											<th class="nowrap q2c_width_10 center hidden-phone hidden-tablet">
												<?php echo Text::_('COM_QUICK2CART_PRODUCTS_IN_STOCK'); ?>
											</th>
										<?php

									}
								?>
								<th class="nowrap q2c_width_15 hidden-phone hidden-tablet">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_CLIENT', 'parent', $listDirn, $listOrder); ?>
								</th>
								<th class="nowrap q2c_width_15 hidden-phone">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_CAT', 'category', $listDirn, $listOrder); ?>
								</th>
								<th class="nowrap q2c_width_15 hidden-phone">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_STORE_NAME', 'store_id', $listDirn, $listOrder); ?>
								</th>
								<th class="nowrap q2c_width_10 hidden-phone hidden-tablet">
									<?php echo Text::_('COM_QUICK2CART_CREATD_BY'); ?>
								</th>
								<th class="nowrap q2c_width_5 center hidden-phone hidden-tablet">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_CDATE', 'cdate', $listDirn, $listOrder); ?>
								</th>
								<th class="nowrap q2c_width_5 center hidden-phone">
									<?php echo HTMLHelper::_('grid.sort', 'COM_QUICK2CART_ID', 'item_id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$k = 0;

							if (!empty($this->products))
							{
								$n = count($this->products);

								for ($i=0; $i < $n; $i++)
								{
									$zone_type = '';
									$row       = $this->products[$i];
									$ordering  = ($listOrder == 'a.ordering');
									$published = HTMLHelper::_('jgrid.published', $row->state, $i, 'products.');
									$edit_link = '<a href="'. $row->edit_link . '" >' . $row->name . '</a>';
									?>
									<tr class="<?php echo 'row'.$k;?>">
										<td class="order nowrap center hidden-phone">
											<?php $iconClass = (!$saveOrder) ? ' inactive tip-top hasTooltip" title="' . HTMLHelper::tooltipText('JORDERINGDISABLED') : '';?>
											<span class="sortable-handler <?php echo $iconClass ?>">
												<span class="icon-menu"></span>
											</span>
											<?php
											if ($saveOrder)
											{
												?>
												<input
													type="text"
													style="display:none"
													name="order[]"
													size="8"
													value="<?php echo $row->ordering; ?>"
													class="width-20 text-area-order " />
												<?php
											} ?>
										</td>
										<td class="nowrap q2c_width_1 center">
											<?php echo HTMLHelper::_('grid.id', $i, $row->item_id); ?>
										</td>
										<td class="nowrap q2c_width_5 center">
											<?php echo $published;?>
										</td>
										<td>
											<?php echo $edit_link;?>
										</td>
										<td class="nowrap q2c_width_5 center hidden-phone hidden-tablet">
											<a
												href="javascript:void(0);"
												class='tbody-icon btn btn-micro active hasTooltip'
												onclick="return Joomla.listItemTask('cb<?php echo $i;?>','<?php echo ($comquick2cartHelper->isFeatured($row->item_id)) ? 'products.unfeatured' : 'products.featured';?>')"
												title="<?php echo ($comquick2cartHelper->isFeatured($row->item_id)) ? Text::_('COM_QUICK2CART_UNFEATURE_TOOLBAR') : Text::_('COM_QUICK2CART_FEATURE_TOOLBAR');?>" >
												<?php $fclass = ($comquick2cartHelper->isFeatured($row->item_id)) ? 'icon-color-featured icon-star' : 'icon-unfeatured';?>
												<i class="<?php echo $fclass;?>"></i>
											</a>
										</td>
									<?php
									if ($this->params->get('usestock') == 1 && $this->params->get('usestock') !== null)
									{
										?>
										<td class="nowrap q2c_width_15 center hidden-phone">
											<?php
											if (($row->stock > 0 || $row->stock == null) && $row->stock !== 0)
											{
												?>
												<a
													href=""
													onclick="return Joomla.listItemTask('cb<?php echo $i;?>','products.makeItemOutOfStock')"
													title="<?php echo Text::_('COM_QUICK2CART_PRODUCT_IS_OUT_OF_STOCK');?>">
													<img src="<?php echo Uri::root();?>administrator/components/com_quick2cart/assets/images/tick.png" width="16" height="16" border="0" />
												</a>
												<?php
											}
											elseif ($row->stock == 0 && $row->stock !== null)
											{
												?>
												<a
													href="<?php echo $row->edit_link; ?>"
													title="<?php echo Text::_('COM_QUICK2CART_PRODUCT_IS_IN_STOCK');?>">
													<img src="<?php echo Uri::root();?>administrator/components/com_quick2cart/assets/images/publish_x.png" width="16" height="16" border="0" />
												</a>
												<?php
											} ?>
										</td>
										<?php
									}
									?>
										<td class="nowrap q2c_width_15 hidden-phone hidden-tablet">
											<?php echo $row->parent; ?>
										</td>
										<td class="nowrap q2c_width_15 hidden-phone">
											<?php echo $row->category; ?>
										</td>
										<td class="nowrap q2c_width_15 hidden-phone">
											<?php echo $row->store_name; ?>
										</td>
										<td class="nowrap q2c_width_10 hidden-phone hidden-tablet">
											<?php echo $row->store_owner; ?>
										</td>
										<td class="nowrap q2c_width_5 center hidden-phone hidden-tablet">
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
										<td class="nowrap q2c_width_5 center hidden-phone">
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
						?>
						</tbody>
					</table>
				<?php echo $this->pagination->getListFooter(); 
				}?>

				<input type="hidden" name="option" value="com_quick2cart" />
				<input type="hidden" name="view" value="products" />
				<input type="hidden" name="task" value="" />
				<input type="hidden" name="boxchecked" value="0" />
				<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
				<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
				<?php echo HTMLHelper::_( 'form.token' ); ?>
			</div>
		</div>
	</form>
</div>

<div class="modal fade" id="import_products" tabindex="-1" aria-labelledby="importProductModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content" style="width: 900px;height: 350px;">
			<div class="modal-body height-400 overflow-y-auto af-m-10">
				<div id="import_products" class="row q2c-wrapper">
				<div class="col-sm-12">
					<form action="<?php echo Uri::base(); ?>index.php?option=com_quick2cart&task=products.csvImport&tmpl=component&format=html" id="uploadForm" class="form-inline center"  name="uploadForm" method="post" enctype="multipart/form-data">
						<table>
							<tr>&nbsp;</tr>
								<div id="uploadform" class="af-mr-5 af-ml-5">
									<fieldset id="upload-noflash" class="actions">
										<label for="upload-file" class="control-label">
											<?php echo Text::_('COM_QUICK2CART_UPLOADE_FILE');?>
										</label>
										<input type="file" id="upload-file" name="csvfile" id="csvfile" accept=".csv" required onchange="enableUploadButton()"/>
										<button class="btn btn-primary" id="upload-submit" disabled="disabled">
											<i class="icon-upload icon-white"></i>
											<?php echo Text::_('COM_QUICK2CART_PRODUCTS_IMPORT_CSV'); ?>
										</button>
										<hr class="hr hr-condensed">
										<div class="alert alert-warning" role="alert"><i class="icon-info"></i>
											<?php
												echo Text::_('COM_QUICK2CART_CSVHELP');
											?>
											<a href="index.php?option=com_quick2cart&task=products.csvExport&headerFlag=1">
												<?php echo Text::_('COM_QUICK2CART_SAMPLE_CSV_FILE')?>
											</a>
										</div>
									</fieldset>
								</div>
							<tr></tr>
						</table>
					</form>
				</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" id="productImportCloseModalbtn" class="btn btn-secondary" data-bs-dismiss="modal">
					<?php echo Text::_('QTC_CLOSE');?>
				</button>
			</div>
		</div>
	</div>
</div>
