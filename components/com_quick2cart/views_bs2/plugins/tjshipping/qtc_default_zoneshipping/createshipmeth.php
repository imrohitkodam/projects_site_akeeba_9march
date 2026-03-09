<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.tooltip');

if (JVERSION < '4.0.0')
{
	JHtmlBehavior::framework();
}

$comquick2cartHelper = new comquick2cartHelper;
$qtczoneShipHelper = new qtczoneShipHelper;

$path = JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';

if(!class_exists('storeHelper'))
{
	JLoader::register('storeHelper', $path );
	JLoader::load('storeHelper');
}

$zoneHelper   = new zoneHelper;
$app          = Factory::getApplication();
$jinput       = $app->input;
$extension_id = $jinput->get('extension_id', 0);
?>
<script type="text/javascript">
function myValidate(f)
{
	var name = document.getElementById('name').value;

	if(name.trim() == '')
	{
		alert(Joomla.Text._('QTC_NOT_ACCEPTABLE_FORM'));
		return false;
	}

	var msg = "<?php echo Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_NOT_ACCEPTABLE_FORM")?>";
	//vm PRODUCT LAEVAL COP COMPULSORY ::coupon exist then only allow to save & check for other validation

	if (document.formvalidator.isValid(f)) {
		f.check.value='<?php echo Session::getFormToken(); ?>';

		return true;
	}
	else {
	}

	return false;
}

function qtcShipSubmitAction(action)
{
	var form = document.qtcshipform;
	if (action == 'saveshipmethod' || action == 'shipMethodSaveAndClose')
	{
		switch(action)
		{
			case 'saveshipmethod':
				var submit_status = myValidate(form);
				if (!submit_status)
				{
					return false;
				}

				// current processing view. Added url param according to this.
				form.plugview.value='createshipmeth';

				// Task to call.
				form.plugtask.value='qtcshipMethodSave';
			break;
			case 'shipMethodSaveAndClose':
				var submit_status = myValidate(form);
				if (!submit_status)
				{
					return false;
				}

				form.plugview.value='createshipmeth';

				// Task to call.
				form.plugtask.value = 'qtcshipMethodSaveAndClose';
			break;
			case 'cancel':
				form.plugview.value='createshipmeth';

				// Task to call.
				form.plugtask.value = 'cancel';
			break;
		}
	}
	else
	{
		window.location = '';
	}

	// Submit form
	form.submit();
	return;
}

function getFieldHtmlForShippingType(shipping_type)
{
	var data = {
		fieldData :
		{
			shipping_type : shipping_type,
		},
		plugtask : 'getFieldHtmlForShippingType',
	};

	var extension_id = <?php echo $extension_id; ?>;

	techjoomla.jQuery.ajax({
		type : "POST",
		url :"<?php echo Uri::base();?>index.php?option=com_quick2cart&task=shipping.qtcHandleShipAjaxCall&plugview=createshipmeth&extension_id=" +extension_id +'&tmpl=component',
		data : data,
		dataType: 'json',
		beforeSend: function() {},
		success : function(response)
		{
			if (response)
			{
				// No error
				techjoomla.jQuery('#qtcCreateMethMinField').html(response.minFieldHtml);
				techjoomla.jQuery('#qtcCreateMethMaxField').html(response.maxFieldHtml);

				// Change lable
				techjoomla.jQuery('#qtcCreateMethMinFieldLable').html(response.minFieldLable);
				techjoomla.jQuery('#qtcCreateMethMaxFieldLable').html(response.maxFieldLable);
			}
		}
	});
}
</script>

<?php
if (!empty($shipFormData['methodId']))
{
	$status = $comquick2cartHelper->store_authorize('', $shipFormData['store_id']);

	if (!$status)
	{
		$zoneHelper->showUnauthorizedMsg();
		return false;
	}
}
?>
<form name="qtcshipform" id="adminForm" class="form-validate form-horizontal" method="post" onSubmit="return myValidate(this);" >
	<input type="hidden" name="check" value="post"/>
	<div class="row-fluid">
		<h1 id="qtc_shipmethodInfo" >
			<strong>
				<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SEL_CREATE_SHIPMETHO')?>&nbsp;
			</strong>
		</h1>

		<div class="control-group">
			<label  for="name" class="control-label" title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_METH_NAME_TITLE'); ?>">
				<?php echo "* ". Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_METH_NAME'); ?>
			</label>
			<div class="controls">
				<input id="methodId" name="shipForm[methodId]" class="" type="hidden" value="<?php echo !empty($shipFormData['methodId']) ? $shipFormData['methodId'] : ''; ?>">
				<input id="name" name="shipForm[name]"
				class="input-large bill inputbox required validate-name"
				placeholder="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_METH_NAME_TOOLTIP');?>"
				type="text" value="<?php echo !empty($shipFormData['name']) ? $shipFormData['name'] : ''; ?>">
			</div>
		</div>

		<!-- STORE LIST -->
		<?php
			// Getting user accessible store ids
			$storeList = $comquick2cartHelper->getStoreIds();
			$defaultSstore_id = !empty($shipFormData['store_id']) ? $shipFormData['store_id'] : '';
			$options = array();
			$options[] = HTMLHelper::_('select.option', "", Text::_('PLG_QTC_DEFAULT_SELECT_STORE'));

			foreach ($storeList as $store)
			{
				$storename = ucfirst($store['title']);
				$options[] = HTMLHelper::_('select.option', $store['store_id'], $storename);
			}
		?>
		<div class="control-group">
			<label for="qtcShipMethStoreId" class="control-label" title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_STORE_NAME_DESC'); ?>">
				<?php echo "* " . Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_STORE_NAME'); ?>
			</label>
			<div class="controls">
				<?php echo HTMLHelper::_('select.genericlist',  $options, "shipForm[store_id]", 'class="inputbox required"  size="1" required="required" ', 'value', 'text', $defaultSstore_id, 'qtcShipMethStoreId');?>
			</div>
		</div>

		<!-- Tax profile -->
		<?php
			$default = isset($shipFormData['taxprofileId']) ? $shipFormData['taxprofileId'] : '';//((!empty($shipFormData['taxprofiles']))?$shipFormData['taxprofiles']:'a');
			$options = array();
			$options[] = HTMLHelper::_('select.option', "", Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SEL_TAXPROFILE'));
			$options[] = HTMLHelper::_('select.option', "0", Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_TAXPROFILE_NONE'));

			foreach ($shipFormData['taxprofiles'] as $taxprofile)
			{
				$profileText = '';
				$profileText = $taxprofile['name'] . ' [' . Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_STORE') . ':' . $taxprofile['title'] . ' ] ';
				$options[] = HTMLHelper::_('select.option', $taxprofile['id'], $profileText);
			}
		?>
		<div class="control-group">
			<label  for="taxprofileId" class="control-label" title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPTAXPROFILE_HELP_TITLE'); ?>">
				<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_TAX_PROFILE'); ?>
			</label>
			<div class="controls">
				<?php echo HTMLHelper::_('select.genericlist',$options,"shipForm[taxprofileId]",'class=""   aria-invalid="false" size="1" required="required" ','value','text',$default,'taxprofileId');?>
				<p class="text-info"><?php echo  Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPTAXPROFILE_HELP'); ?></p>
			</div>
		</div>

		<!-- Publish/unpublish -->
		<?php
			$default = empty($shipFormData['state']) ? 0 : 1;
			$options = array();
			$options[] = HTMLHelper::_('select.option', "1", Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_PUBLISH'));
			$options[] = HTMLHelper::_('select.option', "0", Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_UNPUBLISH'));
		?>
		<div class="control-group">
			<label  for="shipstate" class="control-label" title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_STATE_TITLE'); ?>">
				<?php echo "* ". Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_STATE"); ?>
			</label>
			<div class="controls">
				<?php echo HTMLHelper::_('select.genericlist',$options,"shipForm[state]",'class=""  required="required" aria-invalid="false" size="1" ','value','text',$default,'shipstate');?>
			</div>
		</div>

		<!-- Method type -->
		<?php
			$default = !empty($shipFormData['shipping_type']) ? $shipFormData['shipping_type'] : 1;;
			$shipping_typeoptions   = array();
			$shipping_typeoptions[] = HTMLHelper::_('select.option', "1", Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_STORE_QTY'));
			$shipping_typeoptions[] = HTMLHelper::_('select.option', "2", Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_STORE_WEIGHT'));
			$shipping_typeoptions[] = HTMLHelper::_('select.option', "3", Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_STORE_ITEM'));
		?>
		<div class="control-group" >
			<label  for="shipping_type" class="control-label" title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SHIPTYPE_TITLE'); ?>">
				<?php echo "* ". Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_SHIPTYPE"); ?>
			</label>
			<div class="controls">
				<?php echo  HTMLHelper::_('select.genericlist',$shipping_typeoptions,"shipForm[shipping_type]",'class=""  required="required" onChange="getFieldHtmlForShippingType(this.value)" aria-invalid="false" size="1" ','value','text',$default,'shipping_type');?>
			</div>
		</div>
		<!-- min/ max or curr fields -->
		<?php
		$fieldData = array();

		// Price based shipping
		if (!empty($shipFormData['methodId']) && $shipFormData['shipping_type'] == 3)
		{
			// Edit  method
			$comParams = ComponentHelper::getParams('com_quick2cart');

			// Get Currencies
			$currencies = $comParams->get('addcurrency');
			$curr = explode(',', $currencies);
			$currFieldValues = array();

			// Create template field with default value
			foreach ($curr as $key=>$currName)
			{
				$currFieldValues['min'][$currName] = 0;
				$currFieldValues['max'][$currName] = -1;
			}

			// Create default currency value array for fields
			if (!empty($shipFormData['shipMethCurr']))
			{
				foreach ($shipFormData['shipMethCurr'] as $rec)
				{
					$dbcurr = $rec['currency'];
					$currFieldValues['min'][$dbcurr] = $rec['min_value'];
					$currFieldValues['max'][$dbcurr] = $rec['max_value'];
				}
			}

			// Default curr field valuy
			$fieldData['DefFieldValues'] = $currFieldValues;
			$fieldData['shipping_type'] = 3;
		}
		else
		{
			// New method
			$fieldData['shipping_type'] = 1;
			$fieldData['minFieldAmt'] = !empty($shipFormData['min_value']) ? $shipFormData['min_value'] : '';
			$fieldData['maxFieldAmt'] = !empty($shipFormData['max_value']) ? $shipFormData['max_value'] : '';

		}

		$fieldHtml = $qtczoneShipHelper->getFieldHtmlForShippingType($fieldData);
		$fieldHtml = json_decode($fieldHtml, 1);
		?>
		<div class="control-group">
			<label  for="qtcMinAmount" class="control-label" title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_MINIMUM_AMT_TITLE'); ?>" id="qtcCreateMethMinFieldLable">
				<?php echo "* ". $fieldHtml['minFieldLable'] ?>
			</label>
			<div class="controls" id="qtcCreateMethMinField">
				<?php echo !empty($fieldHtml['minFieldHtml'])?$fieldHtml['minFieldHtml'] : '';?>
			</div>
		</div>

		<div class="control-group">
			<label  for="qtcMaxAmount" class="control-label"  title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_MAXIMUM_AMT_TITLE'); ?>" id="qtcCreateMethMaxFieldLable">
				<?php echo "* ". $fieldHtml['maxFieldLable'] ?>
			</label>
			<div class="controls" id="qtcCreateMethMaxField">
				<?php echo !empty($fieldHtml['maxFieldHtml'])?$fieldHtml['maxFieldHtml'] : '';?>
			</div>
		</div>

		<div class="form-actions ">
			<div class="qtc_action_button">
				<button type="button" class="btn btn-success " title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SAVE'); ?>" onclick="qtcShipSubmitAction('saveshipmethod');">
					<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_SAVE'); ?>
				</button>
				<button type="button" class="btn  " title="<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_S_SAVE_CLOSE'); ?>" onclick="qtcShipSubmitAction('shipMethodSaveAndClose');">
					<?php echo Text::_('PLG_QTC_DEFAULT_ZONESHIPPING_S_SAVE_CLOSE'); ?>
				</button>
				<button type="button" class="btn " title="<?php echo Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_CANCEL"); ?>" onclick="qtcShipSubmitAction('cancel');">
					<?php echo Text::_("PLG_QTC_DEFAULT_ZONESHIPPING_CANCEL"); ?>
				</button>
			</div>
		</div>

		<!-- Component related things -->
		<input type="hidden" name="com_quick2cart" value="shipping" />
		<input type="hidden" name="task" value="shipping.getShipView" />
		<input type="hidden" name="view" value="shipping" />

		<!-- plugin related things -->
		<input type="hidden" name="plugview" value="" />
		<input type="hidden" name="plugtask" value="save" />
		<?php echo HTMLHelper::_( 'form.token' ); ?>
	</div> <!-- End row fluid-->
</form>
