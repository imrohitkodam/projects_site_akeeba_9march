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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');
HTMLHelper::_('script',Uri::root(true) . '/libraries/techjoomla/assets/js/tjvalidator.js');
?>
<script type="text/javascript">
	var root_url="<?php echo Uri::root(); ?>";
	Joomla.submitbutton = function(task)
	{
		if (task == 'zone.cancel')
		{
			Joomla.submitform(task, document.getElementById('zone-form'));
		}
		else
		{
			if (task != 'zone.cancel' && document.formvalidator.isValid(document.getElementById('zone-form')))
			{
				Joomla.submitform(task, document.getElementById('zone-form'));
			}
			else
			{
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
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
	function qtcAddZoneRule()
	{
		/* Clear old msg */

		var qtc_ZoneCountry = document.getElementById('qtc_ZoneCountry').value;
		if (qtc_ZoneCountry == '')
		{
			Joomla.renderMessages({ 'error': ["<?php echo Text::_('COM_QUICK2CART_ZONE_INVALID_COUNTRY_SEL'); ?>"] });

			return false;
		}

		var data = {
			jform : {
				zone_id : document.getElementById('zone_id').value,
				country_id : document.getElementById('qtc_ZoneCountry').value,
				region_id : document.getElementById('jform_qtc_state_id').value,
			}
		};

		// Get country and region name
		var country = jQuery("#qtc_ZoneCountry").children("option").filter(":selected").text() ;
		var region = jQuery("#jform_qtc_state_id").children("option").filter(":selected").text() ;

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&task=zone.addZoneRule",
			data : data,
			dataType: "json",
			success : function(response)
			{
				// If not Error
				if (response.error != 1)
				{
					// Remove Error dive content

					var zoneRuleId = response.zonerule_id;
					var q = "'";
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
					//techjoomla.jQuery(delete_btn).parent().parent().fadeOut();
					techjoomla.jQuery(delBtn).closest('tr').fadeOut();
				}
				else
				{
					Joomla.renderMessages({ 'error': [response.errorMessage] });
				}
			}
		});
	}

	function toggleSetZoneRuleModal(id)
	{
		jQuery('#setZoneRuleModal_'+id).attr('data-width' , (window.innerWidth)/2);
		jQuery('#setZoneRuleModal_'+id).attr('data-height' , window.innerHeight);
		jQuery('#setZoneRuleModal_'+id).modal('show');
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span10 form-horizontal">
				<fieldset class="adminform">
					<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="adminForm" id="zone-form" class="form-validate">
						<legend><?php echo Text::_('COM_QUICK2CART_ZONE_LEGEND'); ?></legend>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('state'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
						</div>
						<div class="control-group">
							<div class="control-label"><?php echo $this->form->getLabel('store_id'); ?></div>
							<div class="controls"><?php echo $this->form->getInput('store_id'); ?></div>
						</div>
						<div class="control-group">
							<div class="alert alert-info"><?php echo Text::_('COM_QUICK2CART_ZONE_HELP_TEXT'); ?></div>
						</div>
						<!-- For Error Display-->
						<div class="control-group">
							<div class="error alert alert-danger qtcError" style="display: none;">
								<?php echo Text::_('COM_QUICK2CART_ZONE_ERROR') . ' : '; ?><span id="qtczoneruleError"></span>
								<i class="icon-cancel pull-right" style="align: right;"
									onclick="jQuery(this).parent().fadeOut();"> </i>
							</div>
						</div>
						<input type="hidden" name="id" id="zone_id" value="<?php echo $this->item->get('id')?>" />
						<input type="hidden" name="task" id="" value="" />
						<?php echo HTMLHelper::_('form.token'); ?>
					</form>
				</fieldset>
				<fieldset>
					<?php
					if (!empty($this->item->id))
					{
					?>
						<div class=" form-horizontal">
							<h3 class="af-mb-10"><?php echo Text::_('COM_QUICK2CART_ZONE_COUNTRIES_AND_REGIONS'); ?></h3>
							<table class="adminlist table">
								<tbody>
									<tr>
										<td width="30%">
											<?php
											$default   = "";
											$options   = array();
											$options[] = HTMLHelper::_('select.option', "", Text::_('COM_QUICK2CART_ZONE_SELECT_COUNTRY'));

											foreach($this->country as $country)
											{
												$options[] = HTMLHelper::_('select.option', $country['id'], $country['country']);
											}
											echo $this->dropdown = HTMLHelper::_('select.genericlist',$options,'qtc_ZoneCountry','class=""  required="" aria-invalid="false" size="1"  autocomplete="off" onchange=\'qtc_generateState("jform[qtc_state_id]","jform_qtc_state_id")\' data-chosen="qtc" ','value','text',$default,'qtc_ZoneCountry');
											?>
										</td>
										<td width="40%">
											<span id="qtcStateContainer"></span>
										</td>
										<td>
											<span>
												<input type="button" id="qtcAddZoneRules"
													value="<?php echo Text::_('COM_QUICK2CART_ZONE_ADD_COUNTRY_OR_STATE'); ?>"
													class="btn btn-success" onClick="qtcAddZoneRule()"/>
											</span>
										</td>
									</tr>
								</tbody>
							</table>
							<table class="adminlist table table-striped table-bordered">
								<thead>
									<tr>
										<th><?php echo Text::_('COM_QUICK2CART_ZONERULE_COUNTRY'); ?></th>
										<th><?php echo Text::_('COM_QUICK2CART_ZONERULE_REGION'); ?></th>
										<th><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ACTION'); ?></th>
									</tr>
								</thead>
								<tbody id="qtcTableBody">
									<?php
									if (!empty($this->geozonerules))
									{
										foreach ($this->geozonerules as $rule)
										{ ?>
											<tr>
												<td id="qtc_country_<?php echo $rule->id; ?>" ><?php echo $rule->country; ?></td>
												<td id="qtc_region_<?php echo $rule->id; ?>">
													<?php
													if (empty($rule->region))
													{
														echo Text::_('COM_QUICK2CART_ZONERULE_ALL_REGION');
													}
													else
													{
														echo $rule->region;
													}
													?>
												</td>
												<td>
													<button class="btn btn-success" type="button" onclick="toggleSetZoneRuleModal('<?php echo $rule->id ?>');">
														<?php echo Text::_('COM_QUICK2CART_ZONERULE_EDIT'); ?>
													</button>
													<?php
														$setZoneRulelink = 'index.php?option=com_quick2cart&view=zone&layout=setrule_bs2&id=' . $rule->id . '&tmpl=component';

														echo HTMLHelper::_(
															'bootstrap.renderModal',
															'setZoneRuleModal_' . $rule->id,
															array(
																'title' => Text::_('COM_QUICK2CART_ZONE_COUNTRIES_AND_ZONES'),
																'url'        => $setZoneRulelink,
																'modalWidth' => '40',
																'bodyHeight' => '70',
																'width'      => '100px',
																'height'     => '800px',
															)
														)
													?>
													<input onclick="qtcDeleteZoneRule(<?php echo $rule->id;?>,this);" class="btn btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_ZONERULE_DELETE'); ?>">
												</td>
											</tr>
											<?php 
										}
									}
									?>
								</tbody>
							</table>
						</div>
					<?php
					}
					?>
				</fieldset>
			</div>
		</div>
	</div>
</div>
