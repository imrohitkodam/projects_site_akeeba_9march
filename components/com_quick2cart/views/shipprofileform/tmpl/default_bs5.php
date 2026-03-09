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
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal');

$shipProfileStoreId  = !empty($this->storeDetails['id']) ? $this->storeDetails['id'] : 0;
$comquick2cartHelper = new comquick2cartHelper;
$store_cp_itemid     = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');
HTMLHelper::_('script', 'libraries/techjoomla/assets/js/tjvalidator.js');
?>
<script type="text/javascript">
	function qtcsubmitAction(action)
	{
		var form = document.shipprofileform;

		switch(action)
		{
			case 'save': form.task.value='shipprofileform.save';
			break

			case 'saveAndClose':
			form.task.value='shipprofileform.saveAndClose';
			break
		}
		// Submit form
		form.submit();
		return;
	}

	function deleteShipProfileMethod(methodId,delBtn)
	{
		var data = {
			jform : {
				shipMethodId : methodId,
			}
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=shipprofileform&task=shipprofileform.deleteShipProfileMethod&tmpl=component",
			data : data,
			success : function(response)
			{
				if (response.error!=1)
				{
					techjoomla.jQuery(delBtn).closest('tr').remove();
				}
				else
				{
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});
	}

	function qtcLoadPlgMethods()
	{
		var qtcShipPluginId = document.getElementById('qtcShipPlugin').value;

		var data = {
			qtcShipPluginId : qtcShipPluginId,
			store_id : <?php echo $shipProfileStoreId; ?>,
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=shipprofileform&task=shipprofileform.qtcLoadPlgMethods&tmpl=component",
			data : data,
			dataType: "json",
			beforeSend: function()
			{
				// REMOVE ALL STATE OPTIONS
				techjoomla.jQuery('#qtc_shipMethod').find('option').remove().end();
				techjoomla.jQuery('.com_quick2cart_ajax_loading').show();
			},
			complete: function()
			{
				techjoomla.jQuery('.com_quick2cart_ajax_loading').hide();
			},
			success : function(response)
			{
				if (response.error != 1)
				{
					techjoomla.jQuery('#qtcShipMethContainer').html(response.shipMethList);
				}
				else
				{
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});
	}

	function qtc_addShipMethod()
	{
		var qtcShipPluginId = document.getElementById('qtcShipPlugin').value;
		var qtc_shipMethodId = document.getElementById('qtc_shipMethod').value;

		if(qtcShipPluginId == '' || qtc_shipMethodId == '')
		{
			Joomla.renderMessages({ 'error': ["<?php echo Text::_('COM_QUICK2CART_S_SHIPPLUGIN_INVALID_SELECTION'); ?>"] });
			return false;
		}

		var qtcShipprofile_id = document.getElementById('jform_id').value;
		var data = {
			jform : {
				shipprofile_id : qtcShipprofile_id,
				qtcShipPluginId : qtcShipPluginId,
				methodId : qtc_shipMethodId,
			}
		};

		var qtc_selectedShipPlugin = techjoomla.jQuery("#qtcShipPlugin").children("option").filter(":selected").text() ;
		var qtc_selectedShipMethod = techjoomla.jQuery("#qtc_shipMethod").children("option").filter(":selected").text() ;

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=shipprofileform&task=shipprofileform.addShipMethod&tmpl=component",
			data : data,
			dataType: "json",
			beforeSend: function()
			{
				techjoomla.jQuery('.qtcError').fadeOut();
			},
			complete: function()
			{
			},
			success : function(response)
			{
				if (response.error == 0)
				{
					// Remove Error dive content
					techjoomla.jQuery('#qtcErrorContentDiv').html('');
					techjoomla.jQuery('.qtcError').fadeOut();
					var shipProfileMethodId= response.shipProfileMethodId;
					var q="'";
					var editbtn = '<input type="button" value="<?php echo Text::_('COM_QUICK2CART_SHIPPROFIL_METH_EDIT'); ?>" class="btn btn-primary">';
					var editHref = 'index.php?option=com_quick2cart&view=shipprofileform&layout=setrule&id='+qtcShipprofile_id+'&shipmethId='+shipProfileMethodId+'&tmpl=component';
					var editLink = '<a rel="{handler:\'iframe\',size:{x: window.innerWidth-450, y: window.innerHeight-250}}" href="'+editHref+'" class="modal qtc_modal">'+editbtn+'</a> &nbsp;';

					var delLink = '<input onclick="deleteShipProfileMethod('+
								shipProfileMethodId+',this);" class="btn btn-sm btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_PROFILERULE_DELETE'); ?>">';

					var result='<tr><td id="qtcPlugnameTd_'+shipProfileMethodId+'">'+qtc_selectedShipPlugin+'</td><td id="qtcShipMethTd_'+shipProfileMethodId+'">'+qtc_selectedShipMethod+'</td><td>' + editLink + delLink + '</td></tr>';
					techjoomla.jQuery('#qtcShipMethTableBody').append(result);
					window.parent.location.reload();
				}
				else
				{
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});

		return false;
	}

	function toggleSetShipProfileMethodRuleModal(id)
	{
		jQuery('#setShipProfileMethodRuleModal_'+id).attr('data-width' , (window.innerWidth)/2);
		jQuery('#setShipProfileMethodRuleModal_'+id).attr('data-height' , window.innerHeight);
		jQuery('#setShipProfileMethodRuleModal_'+id).modal('show');
	}
</script>

<div class=" <?php echo Q2C_WRAPPER_CLASS; ?> container-fluid">
	<?php
	// Add store toolbar
	$helperobj      = new comquick2cartHelper;
	$active         = 'zones';
	$order_currency = $helperobj->getCurrencySession();
	$view           = $helperobj->getViewpath('vendor','toolbar_bs5');
	ob_start();
		include $view;
		$html = ob_get_contents();
	ob_end_clean();
	echo $html;
	?>
	<form id="shipprofileform" name="shipprofileform"   method="post" class="form-validate" enctype="multipart/form-data">
		<div style="clear:both;"></div>
		<div class="row">
			<div class="form-horizontal">
				<fieldset class="adminform">
					<h1><strong><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE'); ?></strong></h1>
					<div class="form-group row mt-2">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('name'); ?></div>
					</div>
					<div class="form-group row" style="display:none;">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('id'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('id'); ?></div>
					</div>
					<?php
					if (empty($this->item->id))
					{
						?>
						<div class="form-group row mt-2">
							<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('store_id'); ?></div>
							<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('store_id'); ?>
								<span class="help-block">
									<i class="<?php echo QTC_ICON_INFO; ?>"></i>
									<?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_CAN_NOT_CAHGNE_STORE_MSG'); ?>
								</span>
							</div>
						</div>
						<?php
					}
					else
					{
						?>
						<div class="form-group row mt-2">
							<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('store_id'); ?></div>
							<div class="col-sm-9 col-xs-12"><input type="text" readonly disabled class="form-control" value="<?php echo ucfirst($this->storeDetails['title']); ?>"></div>
						</div>
						<?php
					}?>
					<div class="form-group row mt-2">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('state'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('state'); ?></div>
					</div>
					<input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>" />
					<div class="alert alert-info mt-2">
						<?php echo Text::_('COM_QUICK2CART_SHIPPROFILES_SAVE_HELP_TEXT');?>
					</div>
					<input type="hidden" name="jform[ordering]" value="<?php echo !empty($this->item->ordering)	? $this->item->ordering : ''; ?>" />
					<input type="hidden" name="option" value="com_quick2cart" />
					<input type="hidden" name="task" value="shipprofileform.save" />
					<input type="hidden" name="id" id="id" value="<?php echo $this->item->get('id')?>" />
					<input type="hidden" name="jform[id]" id="jform_taxprofile_id" value="<?php echo $this->item->id; ?>" />
					<?php echo HTMLHelper::_('form.token'); ?>
				</fieldset>
			</div>
			<?php
			if (!empty($this->item->id))
			{
			?>
				<legend>
					<b><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_ADD_SHIPMEHODS'); ?></b>
					<small><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_TAXRATE_MAP_HELP'); ?></small>
				</legend>
				<!-- Map the tax rule aginst tax profile -->
				<div class="row">
					<div class="col-sm-5 col-xs-12">
						<?php echo $this->shipPluglist; ?>
					</div>
					<div class="col-sm-4 col-xs-12">
						<span id="qtcShipMethContainer">
							<?php
							$default ="";
							$options = array();
							$options[] = HTMLHelper::_('select.option', "", Text::_("COM_QUICK2CART_SHIPPLUGIN_SELECT_SHIP_METH"));

							echo $this->dropdown = HTMLHelper::_('select.genericlist',$options,'qtc_shipMethod','class="form-select"  aria-invalid="false" data-chosen="qtc"   autocomplete="off" ','value','text',$default,'qtc_shipMethod');
							?>
						</span>
						<span class="com_quick2cart_ajax_loading" style="display:none;">
							<img class="" src="<?php echo Uri::root() ?>components/com_quick2cart/assets/images/loadin16x16.gif" height="15" width="15">
						</span>
					</div>
					<div class="col-sm-3 col-xs-12">
						<input type="button" id="qtcAddShipMeth"
							value="<?php echo Text::_('COM_QUICK2CART_SHIPPLUGIN_ADD_SHIP_METH'); ?>"
							class="btn btn-success ms-xxl-5" onClick="qtc_addShipMethod()" />
					</div>
				</div>
				<div class="clearfix">&nbsp;</div>
				<!-- For Error Display-->
				<div class="row">
					<div class="error alert alert-danger qtcError" style="display: none;">
						<?php echo Text::_('COM_QUICK2CART_ZONE_ERROR') . ' : '; ?><span id="qtcErrorContentDiv"></span>
						<i class="<?php echo QTC_ICON_REMOVE; ?> float-end" style="align: right;"
							onclick="techjoomla.jQuery(this).parent().fadeOut();"> </i>					
					</div>
				</div>
				<!-- Show the taxprofile rules -->

				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th width="40%"><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_PLUGIN_NAME'); ?> </th>
							<th width="35%"><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_PLUGIN_METHOD'); ?></th>
							<th width="20%"><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_ACTION'); ?></th>
						</tr>
					</thead>
					<tbody id="qtcShipMethTableBody">
						<?php
						$i=1;

						foreach ($this->shipMethods as $meths)
						{
							?>
							<tr>
								<td id="qtcPlugnameTd_<?php echo $meths->id; ?>" >
									<?php echo $meths->plugName;?>
								</td>
								<td id="qtcShipMethTd_<?php echo $meths->methodId; ?>"><?php
									// Get shipping method description
									$import = PluginHelper::importPlugin('tjshipping', $meths->client);
									$result = Factory::getApplication()->triggerEvent('onTjShip_getShipMethodDetail', array($meths->methodId));
									$shipMethDetail = array();

									if (!empty($result))
									{
										$shipMethDetail = $result[0];
									}

									echo !empty($shipMethDetail['name']) ? ucfirst($shipMethDetail['name']) : '';
								?>
								</td>
								<td>
									<button class="btn btn-sm btn-primary" type="button" onclick="toggleSetShipProfileMethodRuleModal('<?php echo $meths->id ?>');">
										<?php echo Text::_('COM_QUICK2CART_SHIPPROFIL_METH_EDIT'); ?>
									</button>
									<?php
										$setShipProfileMethodRulelink = Route::_('index.php?option=com_quick2cart&view=shipprofileform&layout=setrule_bs5&id=' . $meths->shipprofile_id. '&shipmethId=' . $meths->id . '&tmpl=component');

										echo HTMLHelper::_(
											'bootstrap.renderModal',
											'setShipProfileMethodRuleModal_' . $meths->id,
											array(
												'title'		 => Text::_('COM_QUICK2CART_SHIPPROFIL_METH_EDIT'),
												'url'        => $setShipProfileMethodRulelink,
												'modalWidth' => '80',
												'bodyHeight' => '30',
												'width' => '100%',
												'height' => '300px',
											)
										)
									?>
									<input onclick="deleteShipProfileMethod(<?php echo $meths->id;?>,this);" class="btn btn-sm btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_SHIPPROFIL_METH_DELETE'); ?>">
								</td>
							</tr>
							<?php
						}?>
					</tbody>
				</table>
			<?php
			}
			?>
		</div> <!-- ROW-FLUID END-->
		<div class="form-horizontal">
			<div class="">
				<button type="button" class="btn btn-success validate" title="<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>" onclick="qtcsubmitAction('save');">
					<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>
				</button>
				<?php if ($this->item->id): ?>
					<button type="button" class="btn btn-primary validate" title="<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>" onclick="qtcsubmitAction('saveAndClose');">
						<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>
					</button>
				<?php endif; ?>
				 <a href="<?php echo Route::_('index.php?option=com_quick2cart&task=shipprofileform.cancel&id='.$this->item->id); ?>&Itemid=<?php echo $store_cp_itemid; ?>" class="btn btn-default" title="<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>">
					<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>
				 </a>
			</div>
		</div>
	</form>
</div> <!--com_quick2cart_wrapper -->
