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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'zone.cancel') {
			Joomla.submitform(task, document.getElementById('zone-form'));
		}
		else {

			if (task != 'zone.cancel' && document.formvalidator.isValid(document.getElementById('zone-form'))) {

				Joomla.submitform(task, document.getElementById('zone-form'));
			}
			else {
				alert("<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>");
			}
		}
	}

	/** Generate state select box
		@param field_name string select element name.
		@param field_id string select element id.
		@param country_value string selected country value.
		@param default_option string default option to set.
	*/
	function qtc_generateState(field_name, field_id)
	{
		var countryId = 'qtc_ZoneCountry';
		var default_option=0;
		var country_value=techjoomla.jQuery('#'+countryId).val();
		var data = {
			jform : {
				country_id : country_value,
				default_option : default_option,
				field_name : field_name,
				field_id : field_id
			}
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url : "<?php echo Uri::base();?>index.php?option=com_quick2cart&task=zone.getStateSelectList",
			data : data,
			success : function(response)
			{
				techjoomla.jQuery('#qtcStateContainer').html(response);
			}
		});
	}
	/** This function adds in zone
	 */
	function qtcUpdateZoneRule()
	{
		var data = {
			jform : {
				zonerule_id : document.getElementById('zonerule_id').value,
				country_id : document.getElementById('qtc_ZoneCountry').value,
				region_id : document.getElementById('jform_qtc_state_id').value,
			}
		};

		// Get country and region name
		var country = jQuery("#qtc_ZoneCountry").children("option").filter(":selected").text() ;
		var region = jQuery("#jform_qtc_state_id").children("option").filter(":selected").text() ;

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&task=zone.updateZoneRule",
			data : data,
			dataType: "json",
			success : function(response)
			{
				// If not Error
				if (response.error != 1)
				{
					// Remove Error dive content
					techjoomla.jQuery('#qtczoneruleError').html('');
					techjoomla.jQuery('.qtcError').fadeOut();
					var zoneRuleId= response.zonerule_id;
					window.parent.jQuery('#country_'+zoneRuleId).html(country);
					window.parent.jQuery('#region_'+zoneRuleId).html(region);
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

	/** Delete the rule from zone.
		@param field_name string select element name.
		@param field_id string select element id.

	*/
	function qtcDeleteZoneRule(ruleId,delBtn)
	{
		var data = {
			jform : {
				zonerule_id : ruleId,
			}
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url : "<?php echo Uri::base();?>index.php?option=com_quick2cart&task=zone.deleteZoneRule",
			data : data,
			success : function(response)
			{
				if (response.error!=1)
				{
					techjoomla.jQuery(delBtn).closest('tr').fadeOut();
				}
				else
				{
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});
	}
</script>
<div class = "<?php echo Q2C_WRAPPER_CLASS; ?>">
	<form action="" method="post" enctype="multipart/form-data" name="adminForm" id="zone-form" class="form-validate">
		<div class="form-horizontal">
			<div class="row-fluid">
				<div class="span10 form-horizontal">
					<!-- For Error Display-->
					<div class="control-group">
						<div class="error alert alert-danger qtcError" style="display: none;">
							<?php echo Text::_('COM_QUICK2CART_ZONE_ERROR') . ' : '; ?><span id="qtczoneruleError"></span>
							<i class="icon-cancel pull-right" style="align: right;" onclick="jQuery(this).parent().fadeOut();"></i>
						</div>
					</div>
					<input type="hidden" name="zonerule_id" id="zonerule_id" value="<?php echo $this->rule_id; ?>" />
					<div class="control-group">
						<div class="controls">
							<?php
							$default = $this->ruleDetail->country_id;
							$options = array();
							$options[] = HTMLHelper::_('select.option', "", Text::_('COM_QUICK2CART_ZONE_SELECT_COUNTRY'));

							foreach($this->country as $country)
							{
								$options[] = HTMLHelper::_('select.option', $country['id'],$country['country']);
							}

							echo $this->dropdown = HTMLHelper::_('select.genericlist',$options,'qtc_ZoneCountry','class=""  required="" aria-invalid="false" size="1"  autocomplete="off" onchange=\'qtc_generateState("jform[qtc_state_id]","jform_qtc_state_id")\' ','value','text',$default,'qtc_ZoneCountry');
							?>
						</div>
					</div>
					<div class="control-group">
						<div class="controls" id="">
							<span id="qtcStateContainer">
								<?php
								$options = array();
								$options[] = HTMLHelper::_('select.option', 0,TEXT::_('COM_QUICK2CART_ZONE_ALL_STATES'));

								if ($this->getRegionList)
								{
									$default_region =  $this->ruleDetail->region_id;

									foreach ($this->getRegionList as $state)
									{
										// This is only to generate the <option> tag inside select tag da i have told n times
										$options[] = HTMLHelper::_('select.option', $state['id'],$state['region']);
									}

									// now we must generate the select list and echo that
									echo $stateList = HTMLHelper::_('select.genericlist', $options, 'jform[qtc_state_id]', '  autocomplete="off"', 'value', 'text',$default_region,'jform_qtc_state_id');
								}
								?>
							</span>
							<span >
								<input type="button" id="qtcAddZoneRules"
									value="<?php echo Text::_('COM_QUICK2CART_ZONE_UPDATE_COUNTRY_OR_STATE'); ?>"
									class="btn btn-success" onClick="qtcUpdateZoneRule(<?php echo $this->rule_id; ?>)"/>
							</span>
						</div>
					</div>
				</div>
			</div>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
