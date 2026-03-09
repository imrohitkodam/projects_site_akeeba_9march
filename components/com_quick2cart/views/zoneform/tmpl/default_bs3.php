<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$doc = Factory::getDocument();
HTMLHelper::_('script', 'libraries/techjoomla/assets/js/tjvalidator.js');
?>
<script type="text/javascript">
	var root_url="<?php echo Uri::root(); ?>";

	function qtcsubmitAction(action)
	{
		var valid = document.formvalidator.isValid(document.getElementById('zoneForm'));

		if(valid == false)
		{
			alert("<?php echo $this->escape(Text::_('COM_QUICK2CART_ZONEFORM_FILL_REQUIRED_FIELDS')); ?>");
			return false;
		}

		var form = document.zoneForm;

		switch(action)
		{
			case 'save': form.task.value='zoneform.save';
			break

			case 'saveAndClose':
			form.task.value='zoneform.saveAndClose';
			break
		}

		form.submit();

		return;
	}

	/** Generate state select box
		@param field_name string select element name.
		@param field_id string select element id.
		@param country_value string selected country value.
		@param default_option string default option to set.
	*/
	function qtc_generateState(field_name, field_id)
	{
		var countryId      = 'qtc_ZoneCountry';
		var default_option = 0;
		var country_value  = techjoomla.jQuery('#'+countryId).val();

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
			url  : "<?php echo Uri::base();?>index.php?option=com_quick2cart&task=zoneform.getStateSelectList&tmpl=component",
			data : data,
			success : function(response)
			{
				techjoomla.jQuery('#qtcStateContainer').html(response);
			}
		});
	}

	/** This function adds in zone
	 */
	function qtcAddZoneRule(zone_id)
	{
		/* Clear old msg */
		techjoomla.jQuery('#qtczoneruleError').html("");
		techjoomla.jQuery('.qtcError').fadeOut();

		var qtc_ZoneCountry = document.getElementById('qtc_ZoneCountry').value;
		if (qtc_ZoneCountry == '')
		{
			Joomla.renderMessages({ 'error': ["<?php echo Text::_('COM_QUICK2CART_ZONE_INVALID_COUNTRY_SEL'); ?>"] });
			return false;
		}

		var data = {
			jform : {
				zone_id : zone_id,
				country_id : document.getElementById('qtc_ZoneCountry').value,
				region_id : document.getElementById('jform_qtc_state_id').value,
			}
		};

		// Get country and region name
		var country = techjoomla.jQuery("#qtc_ZoneCountry").children("option").filter(":selected").text() ;
		var region  = techjoomla.jQuery("#jform_qtc_state_id").children("option").filter(":selected").text() ;
		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&task=zoneform.addZoneRule&tmpl=component",
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

					var zoneRuleId = response.zonerule_id;
					var q = "'";
					var url = Joomla.getOptions('system.paths').base+"/index.php?option=com_quick2cart&view=zoneform&layout=setrule_bs3&id="+zone_id+"&zonerule_id="+zoneRuleId+"&tmpl=component";
					var editLink = '<a rel="{handler:\'iframe\',size:{x: window.innerWidth-350, y: window.innerHeight-150},onClose: function(){window.parent.document.location.reload(true);}}" '
								+' href="'+url+'"  class="modal qtc_modal"><input type="button" value="<?php echo Text::_('COM_QUICK2CART_ZONERULE_EDIT'); ?>" class=" btn btn-primary"></a> &nbsp;&nbsp;&nbsp;';
					var delLink = '<input onclick="qtcDeleteZoneRule('+
								zoneRuleId+',this);" class="btn btn-sm btn-danger" type="button" value="<?php echo Text::_('COM_QUICK2CART_ZONERULE_DELETE'); ?>">';
					var result=' <tr><td id="qtc_country_'+zoneRuleId+'">'+country+'</td><td id="qtc_region_'+zoneRuleId+'">'+region+'</td><td>'+ editLink + delLink +'</td></tr>';
					techjoomla.jQuery('#qtcTableBody').append(result);
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

	function qtcDeleteZoneRule(ruleId,delBtn)
	{
		var data = {
			jform : {
				zonerule_id : ruleId,
			}
		};

		techjoomla.jQuery.ajax({
			type : "POST",
			url : "<?php echo Uri::base();?>index.php?option=com_quick2cart&task=zoneform.deleteZoneRule&tmpl=component",
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
	function toggleSetZoneRuleModal(id)
	{
		jQuery('#setZoneRuleModal_'+id).attr('style' , 'display:flex !important; flex-direction: column; align-items: center;');
	}
</script>

<div class="qyc_admin_zones <?php echo Q2C_WRAPPER_CLASS; ?>  container-fluid">
	<?php
	$helperobj      = new comquick2cartHelper;
	$active         = 'zones';
	$order_currency = $helperobj->getCurrencySession();
	$view           = $helperobj->getViewpath('vendor','toolbar_bs3');
	ob_start();
		include $view;
		$html = ob_get_contents();
	ob_end_clean();
	echo $html;
	?>
	<div class="row">
		<div class="form-horizontal">
			<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit&id=' . (int) $this->item->id); ?>" method="post" enctype="multipart/form-data" name="zoneForm" id="zoneForm" class="form-validate">
				<div style="clear:both;"></div>
				<h1><strong><?php echo Text::_('COM_QUICK2CART_ZONE_LEGEND'); ?></strong></h1>
				<fieldset class="adminform">
					<div class="form-group row">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('name'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('name'); ?></div>
					</div>
					<div class="form-group row">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('state'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('state'); ?></div>
					</div>
					<div class="form-group row">
						<div class="col-sm-3 col-xs-12 form-label"><?php echo $this->form->getLabel('store_id'); ?></div>
						<div class="col-sm-9 col-xs-12"><?php echo $this->form->getInput('store_id'); ?></div>
					</div>
					<div class="form-group row">
						<div class="alert alert-info"><?php echo Text::_('COM_QUICK2CART_ZONE_HELP_TEXT'); ?></div>
					</div>
					<!-- For Error Display-->
					<div class="form-group row">
						<div class="error alert alert-danger qtcError" style="display: none;">
							<?php echo Text::_('COM_QUICK2CART_ZONE_ERROR') . ' : '; ?><span id="qtczoneruleError"></span>
							<i class="<?php echo QTC_ICON_REMOVE; ?> pull-right" style="align: right;" onclick="jQuery(this).parent().fadeOut();"></i>
						</div>
					</div>
				</fieldset>
				<fieldset>
				<?php
					if (!empty($this->item->id))
					{
					?>
					<div class=" form-horizontal">
						<legend>
							<?php echo Text::_('COM_QUICK2CART_ZONE_COUNTRIES_AND_REGIONS'); ?>
						</legend>
						<div class="row">
							<div class="col-sm-5 col-xs-12">
								<?php
								$default   = "";
								$options   = array();
								$options[] = HTMLHelper::_('select.option', "", Text::_('COM_QUICK2CART_ZONE_SELECT_COUNTRY'));

								foreach ($this->country as $country)
								{
									$options[] = HTMLHelper::_('select.option', $country['id'], $country['country']);
								}
								echo HTMLHelper::_('select.genericlist',$options,'qtc_ZoneCountry','class=""  aria-invalid="false" autocomplete="off" onchange=\'qtc_generateState("jform[qtc_state_id]","jform_qtc_state_id")\' ','value','text',$default,'qtc_ZoneCountry');
								?>
							</div>
							<div class="col-sm-4 col-xs-12">
								<span id="qtcStateContainer"></span>
							</div>
							<div class="col-sm-2 col-xs-12 af-ml-30">
								<span>
									<input
										type="button"
										id="qtcAddZoneRules"
										value="<?php echo Text::_('COM_QUICK2CART_ZONE_ADD_COUNTRY_OR_STATE'); ?>"
										class="btn btn-success" onClick="qtcAddZoneRule(<?php echo $this->item->id; ?>)"/>
								</span>
							</div>
						</div>
						<div class="clearfix">&nbsp;</div>
						<table class="table table-striped table-bordered">
							<thead>
								<tr>
									<th width="40%"><?php echo Text::_('COM_QUICK2CART_ZONERULE_COUNTRY');?></th>
									<th width="35%"><?php echo Text::_('COM_QUICK2CART_ZONERULE_REGION');?></th>
									<th width="20%"><?php echo Text::_('COM_QUICK2CART_TAXPROFILE_ACTION'); ?></th>
								</tr>
							</thead>
							<tbody id="qtcTableBody">
								<?php
								$i = 1;

								if (!empty($this->geozonerules))
								{
									foreach ($this->geozonerules as $rule)
									{ ?>
										<tr>
											<td id="qtc_country_<?php echo $rule->id; ?>">
												<?php echo $rule->country;?>
											</td>
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
												<button
													class="btn btn-sm btn-primary"
													type="button"
													onclick="toggleSetZoneRuleModal('<?php echo $rule->id;?>');"
													data-toggle="modal"
													data-target="#setZoneRuleModal_<?php echo  $rule->id?>">
													<?php echo Text::_('COM_QUICK2CART_ZONERULE_EDIT'); ?>
												</button>
												<?php
													$setZoneRulelink = Route::_('index.php?option=com_quick2cart&view=zoneform&layout=setrule_bs3&id=' . $this->item->id . '&zonerule_id=' . $rule->id . '&tmpl=component');
													echo HTMLHelper::_(
														'bootstrap.renderModal',
														'setZoneRuleModal_' . $rule->id,
														array(
															'title'		 => Text::_('COM_QUICK2CART_ZONERULE_EDIT'),
															'url'        => $setZoneRulelink,
															'modalWidth' => '40',
															'bodyHeight' => '30',
															'height'     => '400px',
															'width'      => '800px',
														)
													)
												?>
												<input
													onclick="qtcDeleteZoneRule(<?php echo $rule->id;?>,this);"
													class="btn btn-sm btn-danger"
													type="button"
													value="<?php echo Text::_('COM_QUICK2CART_ZONERULE_DELETE'); ?>" />
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
				<!-- Action part -->
				<div>
					<button type="button" class="btn btn-success validate" title="<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>" onclick="qtcsubmitAction('save');">
						<?php echo Text::_('COM_QUICK2CART_SAVE_ITEM'); ?>
					</button>
					<?php if(!empty($this->item) && $this->item->get('id')){?>
						<button type="button" class="btn btn-default validate" title="<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>" onclick="qtcsubmitAction('saveAndClose');">
							<?php echo Text::_('COM_QUICK2CART_COMMON_SAVE_AND_CLOSE'); ?>
						</button>
					<?php
					}?>
					<a
						href="<?php echo Route::_('index.php?option=com_quick2cart&task=zoneform.cancel&id='.$this->item->id); ?>"
						class="btn btn-default"
						title="<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>">
						<?php echo Text::_('COM_QUICK2CART_CANCEL_ITEM'); ?>
					</a>
					<input type="hidden" name="jform[id]" id="zone_id" value="<?php echo $this->item->get('id')?>" />
					<input type="hidden" name="option" value="com_quick2cart" />
					<input type="hidden" name="task" value="" />
					<?php echo HTMLHelper::_('form.token'); ?>
				</div>
			</form>
		</div>
	</div>
</div>
