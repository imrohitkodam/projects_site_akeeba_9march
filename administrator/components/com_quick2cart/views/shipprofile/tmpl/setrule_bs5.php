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
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal');

$jinput               = Factory::getApplication()->input;
$actionViewName       = 'shipprofile';
$actionControllerName = 'shipprofile';
$store_id             = !empty($this->shipProfileDetail) ?$this->shipProfileDetail['store_id'] : 0;
?>
<script type="text/javascript">
	var q2c_baseurl          = '<?php echo Uri::base(); ?>';
	var actionViewName       = '<?php echo $actionViewName; ?>';
	var actionControllerName = '<?php echo $actionControllerName; ?>';

	function qtcLoadPlgMethods()
	{
		var qtcShipPluginId = document.getElementById('qtcShipPlugin').value;
		var data = {
			qtcShipPluginId : qtcShipPluginId,
			store_id : '<?php echo $store_id; ?>',
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url :q2c_baseurl + "index.php?option=com_quick2cart&view="+actionViewName+"&task="+actionControllerName+".qtcLoadPlgMethods",
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
    function qtc_updateShipMethod()
    {
		var qtcShipPluginId = document.getElementById('qtcShipPlugin').value;
		var qtc_shipMethodId = document.getElementById('qtc_shipMethod').value;
		var qtc_shipProfileMethodId = <?php echo $jinput->get('shipmethId'); ?>;

		if(qtcShipPluginId == '' || qtc_shipMethodId == '')
		{
			Joomla.renderMessages({ 'error': ['<?php echo Text::_('COM_QUICK2CART_S_SHIPPLUGIN_INVALID_SELECTION'); ?>'] });
			return false;
		}

		var data = {
			jform : {
				shipprofile_id : document.getElementById('qtcShipProfileId').value,
				qtcShipPluginId : qtcShipPluginId,
				methodId : qtc_shipMethodId,
				qtc_shipProfileMethodId : qtc_shipProfileMethodId,
			}
		};

		var qtc_selectedShipPlugin = techjoomla.jQuery("#qtcShipPlugin").children("option").filter(":selected").text() ;
		var qtc_selectedShipMethod = techjoomla.jQuery("#qtc_shipMethod").children("option").filter(":selected").text() ;

		techjoomla.jQuery.ajax({
			type : "POST",
			url :q2c_baseurl+"index.php?option=com_quick2cart&view="+actionViewName+"&task="+actionControllerName+".updateShipMethod",
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
					alert("<?php echo Text::_('COM_QUICK2CART_ITEM_DATA_SAVED_SUCCESSFULLY'); ?>");
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
</script>

<div class=" <?php echo Q2C_WRAPPER_CLASS; ?>">
	<div class="row-fluid">
		<legend>
			<?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_ADD_SHIPMEHODS'); ?>
			<small><?php echo Text::_('COM_QUICK2CART_SHIPPROFILE_TAXRATE_MAP_HELP'); ?></small>
		</legend>
		<!-- SHIPPROFILE ID-->
		<input type="hidden" name="qtcShipProfileId" id="qtcShipProfileId" value="<?php echo $this->qtcShipProfileId; ?>" />
		<!-- Map the tax rule aginst tax profile -->
		<table class="adminlist table">

			<tr>
				<td id="" colspan="3">
					<div class="error alert alert-danger qtcError" style="display: none;">
						<?php echo Text::_('COM_QUICK2CART_ZONE_ERROR'); ?>
						<i class="icon-cancel float-end"
							onclick="techjoomla.jQuery(this).parent().fadeOut();"> </i> <br />
						<hr />
						<div id="qtcErrorContentDiv"></div>
					</div>
				</td>
			</tr>

			<tr>
				<td><?php echo $this->shipPluglist; ?></td>
				<td>
					<span id="qtcShipMethContainer">
						<?php echo $this->response['shipMethList'];?>
					</span>
					<span class="com_quick2cart_ajax_loading" style="display:none;">
						<img class="" src="<?php echo Uri::root() ?>components/com_quick2cart/assets/images/loadin16x16.gif" height="15" width="15">
					</span>
				</td>
				<td valign="top">
					<input type="button" id="qtcAddShipMeth"
					value="<?php echo Text::_('COM_QUICK2CART_SHIPPLUGIN_UPDATE_SHIP_METH'); ?>"
					class="btn btn-success" onClick="qtc_updateShipMethod()" />
				</td>
			</tr>
		</table>
	</div>
</div> <!--com_quick2cart_wrapper -->
