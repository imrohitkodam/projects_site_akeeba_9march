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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);
$comquick2cartHelper = new comquick2cartHelper;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task=='shipprofile.add')
		{
			Joomla.submitform(task);

			return true;
		}
		else if (task=='shipprofile.edit')
		{
			if (document.adminForm.boxchecked.value===0)
			{
				alert('<?php echo Text::_("COM_QUICK2CART_NO_SELECTION_MSG");?>');

				return;
			}
			elseif (document.adminForm.boxchecked.value > 1)
			{
				alert('<?php echo Text::_("COM_QUICK2CART_MAKE_ONE_SEL");?>');

				return;
			}

			Joomla.submitform(task);
		}
		else
		{
			if (document.adminForm.boxchecked.value==0)
			{
				alert('<?php echo Text::_("COM_QUICK2CART_MESSAGE_SELECT_ITEMS");?>');
				return false;
			}
			switch(task)
			{
				case 'shipprofile.publish':
					Joomla.submitform(task);
				break

				case 'shipprofile.unpublish':
					Joomla.submitform(task);
				break

				case 'shipprofile.delete':
					if (confirm("<?php echo Text::_('COM_QUICK2CART_DELETE_CONFIRM_SHIPPROFILE'); ?>"))
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

	function deleteShipProfileMethod(methodId,delBtn)
	{
		var data = {
			jform : {
				shipMethodId : methodId,
			}
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=<?php echo $actionViewName; ?> &task=<?php echo $actionControllerName; ?>.deleteShipProfileMethod",
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
			url :"<?php echo Uri::root();?>index.php?option=com_quick2cart&view=<?php echo $actionViewName; ?>&task=<?php echo $actionControllerName; ?>.qtcLoadPlgMethods",
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

		var data = {
			jform : {
				shipprofile_id : document.getElementById('jform_id').value,
				qtcShipPluginId : qtcShipPluginId,
				methodId : qtc_shipMethodId,
			}
		};

		var qtc_selectedShipPlugin = techjoomla.jQuery("#qtcShipPlugin").children("option").filter(":selected").text() ;
		var qtc_selectedShipMethod = techjoomla.jQuery("#qtc_shipMethod").children("option").filter(":selected").text() ;

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&view=<?php echo $actionViewName; ?>&task=<?php echo $actionControllerName; ?>.addShipMethod",
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
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<?php
	if (!empty($this->sidebar)): ?>
	<div id="j-sidebar-container" class="span2">
		<?php echo $this->sidebar; ?>
	</div>
	<div id="j-main-container" class="span10">

	<?php
	else : ?>
	<div id="j-main-container">
		<?php
	endif; ?>
		<?php
		$actionViewName       = 'shipprofile';
		$actionControllerName = 'shipprofile';
		$formName             = 'adminForm';
		$att_list_path        = $comquick2cartHelper->getViewpath('shipprofiles', 'shipprofilesdata_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
		ob_start();
		include($att_list_path);
		$item_options = ob_get_contents();
		ob_end_clean();
		echo $item_options;
		?>
	</div>
</div>
