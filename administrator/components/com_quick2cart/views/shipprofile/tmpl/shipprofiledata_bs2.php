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

HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$app                  = Factory::getApplication();
$shipProfileStoreId   = !empty($this->storeDetails['id']) ? $this->storeDetails['id'] : 0;
$comquick2cartHelper  = new comquick2cartHelper;
$store_cp_itemid      = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');
$actionViewName       = !empty($actionViewName) ? $actionViewName :'shipprofileform';
$actionControllerName = !empty($actionControllerName) ? $actionControllerName :'shipprofileform';
$formName             = !empty($actionViewName) ? $actionViewName :'shipprofileform';
?>
<script type="text/javascript">
	var q2c_baseurl          = '<?php echo Uri::base(); ?>';
	var actionViewName       = '<?php echo $actionViewName; ?>';
	var actionControllerName = '<?php echo $actionControllerName; ?>';

	function deleteShipProfileMethod(methodId,delBtn)
	{
		var data = {
			jform : {
				shipMethodId : methodId,
			}
		};
		var deleteShipProfile = q2c_baseurl+"index.php?option=com_quick2cart&view="+actionViewName+"&task=" +actionControllerName+".deleteShipProfileMethod";
		techjoomla.jQuery.ajax({
			type : "POST",
			url  :deleteShipProfile,
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
			url :q2c_baseurl+"index.php?option=com_quick2cart&view="+actionViewName+"&task="+actionControllerName+".qtcLoadPlgMethods",
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
		var qtcShipPluginId  = document.getElementById('qtcShipPlugin').value;
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
			url :q2c_baseurl+"index.php?option=com_quick2cart&view="+actionViewName+"&task="+actionControllerName+".addShipMethod",
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

	Joomla.submitbutton = function(task)
	{
		if (task == 'shipprofile.cancel')
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
		else
		{
			if (task != 'shipprofile.cancel' && document.formvalidator.isValid(document.getElementById('adminForm')))
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
			else
			{
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
				
				return false;
			}
		}
	}
</script>

<div class="row-fluid">
	<div class="form-horizontal">
		<fieldset class="adminform">
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
			</div>
			<div class="control-group" style="display:none;">
				<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('store_id'); ?></div>
				<?php
				if (empty($this->item->id))
				{
					?>
					<div class="controls"><?php echo $this->form->getInput('store_id'); ?>
						<span class="help-block">
							<i class="icon-hand-right"></i>
							<?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_CAN_NOT_CAHGNE_STORE_MSG'); ?>
						</span>
					</div>
					<?php
				}
				else
				{
					?>
					<div class="controls">
						<input type="text" readonly disabled value="<?php echo ucfirst($this->storeDetails['title']); ?>">
					</div>
					<?php
				} ?>
			</div>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
			</div>
			<input type="hidden" name="jform[ordering]" value="<?php echo !empty($this->item->ordering) ? $this->item->ordering : ''; ?>" />
			<div class="alert alert-info">
				<?php echo Text::_('COM_QUICK2CART_SHIPPROFILES_SAVE_HELP_TEXT');?>
			</div>
			<input type="hidden" name="jform[ordering]" value="<?php echo !empty($this->item->ordering)	? $this->item->ordering : ''; ?>" />
			<input type="hidden" name="option" value="com_quick2cart" />
			<input type="hidden" name="task" value="<?php echo $formName; ?>.save" />
			<input type="hidden" name="id" id="id" value="<?php echo !empty($this->item) ? $this->item->get('id') : '';?>" />
			<input type="hidden" name="jform[id]" id="jform_shippofile_id" value="<?php echo !empty($this->item->id) ? $this->item->id : ''; ?>" />
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
		<div class="row-fluid">
			<div class="span4"><?php echo $this->shipPluglist; ?></div>
			<div class="span4">
				<span id="qtcShipMethContainer">
					<?php
					$default ="";
					$options = array();
					$options[] = HTMLHelper::_('select.option', "", Text::_("COM_QUICK2CART_SHIPPLUGIN_SELECT_SHIP_METH"));
					echo HTMLHelper::_('select.genericlist',$options,'qtc_shipMethod','class=""  aria-invalid="false" data-chosen="qtc" size="1"  autocomplete="off" ','value','text',$default,'qtc_shipMethod');
					?>
				</span>
				<span class="com_quick2cart_ajax_loading" style="display:none;">
					<img class="" src="<?php echo Uri::root() ?>components/com_quick2cart/assets/images/loadin16x16.gif" height="15" width="15">
				</span>
			</div>
			<div class="span4">
				<input
					type="button" id="qtcAddShipMeth"
					value="<?php echo Text::_('COM_QUICK2CART_SHIPPLUGIN_ADD_SHIP_METH'); ?>"
					class="btn btn-success" onClick="qtc_addShipMethod()" />
			</div>
		</div>
		<!-- For Error Display-->
		<div class="row-fluid">
			<div class="error alert alert-danger qtcError" style="display: none;">
				<?php echo Text::_('COM_QUICK2CART_ZONE_ERROR') . ' : '; ?><span id="qtcErrorContentDiv"></span>
				<i class="icon-cancel pull-right" style="align: right;"
					onclick="techjoomla.jQuery(this).parent().fadeOut();">
				</i>
			</div>
		</div>
		<!-- Show the taxprofile rules -->
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th width="15%"><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_PLUGIN_NAME'); ?> </th>
					<th width="15%"><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_PLUGIN_METHOD'); ?></th>
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
							$result = $app->triggerEvent('onTjShip_getShipMethodDetail', array($meths->methodId));
							$shipMethDetail = array();

							if (!empty($result))
							{
								$shipMethDetail = $result[0];
							}

							echo !empty($shipMethDetail['name']) ? ucfirst($shipMethDetail['name']) : '';
						?>
						</td>
						<td>
							<button class="btn btn-medium btn-primary" type="button" onclick="toggleSetShipProfileMethodRuleModal('<?php echo $meths->id ?>');">
								<?php echo Text::_('COM_QUICK2CART_SHIPPROFIL_METH_EDIT'); ?>
							</button>
							<?php
								$setShipProfileMethodRulelink = "index.php?option=com_quick2cart&view=" . $actionViewName . "&layout=setrule_bs2&id=" . $meths->shipprofile_id . "&shipmethId=" . $meths->id . "&tmpl=component";

								echo HTMLHelper::_(
									'bootstrap.renderModal',
									'setShipProfileMethodRuleModal_' . $meths->id,
									array(
										'title' => Text::_('COM_QUICK2CART_SHIPPROFILE_EDIT_SHIPMEHODS'),
										'url'        => $setShipProfileMethodRulelink,
										'modalWidth' => '35',
										'bodyHeight' => '70',
									)
								)
							?>
							<input onclick="deleteShipProfileMethod(<?php echo $meths->id;?>,this);" class="btn btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_SHIPPROFIL_METH_DELETE'); ?>">
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
