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

HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$closeAddressModal = ($this->item->id) ? 'editAddressModal_' . $this->item->id : 'addAddressModal';?>
<script type="text/javascript">

	techjoomla.jQuery(document).ready(function() {
		generateStoreState(<?php echo isset($this->item->country_code)?$this->item->country_code:0;?>, <?php echo !empty($this->item->state_code)?$this->item->state_code:"0";?>);
	});

	var closeAddressModal = '<?php echo $closeAddressModal?>';

	function closeAddressModalFun()
	{
		window.parent.closeAddressModal(closeAddressModal);
	}

	function saveaddress(id)
	{
		values = techjoomla.jQuery('#formcustomeraddress').serialize();
		var qtcBillForm = document.formcustomeraddress;

		if (!document.formvalidator.isValid(qtcBillForm))
		{
			return false;
		}

		var u_country = techjoomla.jQuery("#country_code option:selected").val();
		var u_state   = techjoomla.jQuery("#state_code option:selected").val();
		var userId    = window.parent.techjoomla.jQuery("#qtcuser option:selected").val();
		var callurl   = Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&task=customer_addressform.save&userid="+userId+"&country_code="+u_country+"&state_code="+u_state+"&tmpl=component";

		techjoomla.jQuery.ajax({
			url: callurl,
			type: "GET",
			data:values,
			cache: false,
			success: function(data)
			{
				if (data.length == 0)
				{
					alert(Joomla.Text._('COM_QUICK2CART_CUSTOMER_ADDRESS_WRONG_INPUT'));
				} else {
					if (id == '-1')
					{
						window . parent . techjoomla . jQuery('#qtc_user_addresses') . removeAttr("style");
						window . parent . techjoomla . jQuery('.checkout-addresses-select-message').removeClass('d-none');
						window . parent . techjoomla . jQuery('#qtc_user_addresses .qtc_user_addresses_wrapper') . append(data);
						alert('<?php echo Text::_('COM_QUICK2CART_CUSTOMER_ADDRESS_ADD_MSG');?>');
					}
					else
					{
						window . parent . techjoomla . jQuery('#qtc_user_addresses') . removeAttr("style");
						alert('<?php echo Text::_('COM_QUICK2CART_CUSTOMER_ADDRESS_UPDATE_MSG');?>');
						window . parent . techjoomla . jQuery('.qtc-address' + id) . replaceWith(data);
					}

					if (window . parent . techjoomla . jQuery('#qtc_user_addresses .qtc_user_addresses_wrapper') . length)
					{
						window . parent . techjoomla . jQuery('.checkout-addresses') . show();
					}

					if (id == '-1')
					{
					}
					else
					{
						jQuery('.modal-backdrop') . addClass('hide');
						jQuery('.modal-backdrop') . removeClass('show');
					}

					window . parent . techjoomla . jQuery('#' + closeAddressModal).modal('hide');
					window . parent . jQuery('.modal-backdrop.fade.hide').html('');
					window . parent . jQuery('.modal-backdrop.fade.hide').attr('class', '');
				}
			}
		});
	}

	function generateStoreState(field_name, valToSelect)
	{
		var countryId = 'country_code';
		var country_value=techjoomla.jQuery('#'+countryId).val();

		if (valToSelect == 0)
		{
			var e = document.getElementById("state_code");
			var valToSelect = e.options[e.selectedIndex].value;
		}

		var postData = {default_value : 1}

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&task=vendor.getRegions&tmpl=component&country_id="+country_value+"&tmpl=component",
			data:postData,
			success : function(response)
			{
				techjoomla.jQuery('#state_code').html(response);

				if (valToSelect > 0)
				{
					techjoomla.jQuery("#state_code option[value='" + valToSelect + "']").attr("selected", "true");
				}
			}
		});
	}
</script>

<div class="container-fluid">
	<div class="customer_address-edit front-end-edit">
		<form name="formcustomeraddress" id="formcustomeraddress" method="post" class="form-validate form-horizontal" enctype="multipart/form-data" >
			<input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>" />
			<div class="row">
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('firstname'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('firstname'); ?></div>
				</div>
				<?php
				if ($this->params->get('qtc_middlenmae')==1)
				{
				?>
					<div class="form-group col-md-6 row mt-2">
						<div class="form-label col-md-4"><?php echo $this->form->getLabel('middlename'); ?></div>
						<div class="col-md-8"><?php echo $this->form->getInput('middlename'); ?></div>
					</div>
				<?php
				}
				?>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('lastname'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('lastname'); ?></div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('user_email'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('user_email'); ?></div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('vat_number'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('vat_number'); ?></div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('address'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('address'); ?></div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('land_mark'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('land_mark'); ?></div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('city'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('city'); ?></div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('zipcode'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('zipcode'); ?></div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4">
						<label for="country_code">
							<?php echo Text::_("COM_QUICK2CART_FORM_LBL_CUSTOMER_ADDRESS_COUNTRY"); ?>
							<span class="star">&nbsp;*</span>
						</label>
					</div>
					<div class="col-md-8">
						<?php
							$default_country = (isset($this->item->country_code)) ? $this->item->country_code : $this->params->get('set_default_country','');
							$options = array();
							$options[] = HTMLHelper::_('select.option', "", Text::_('QTC_BILLIN_SELECT_COUNTRY'));

							foreach ($this->countrys as $key=>$value)
							{
								$options[] = HTMLHelper::_('select.option', $value['id'], $value['country']);
							}

							echo HTMLHelper::_('select.genericlist',$options,'country_code','class="form-select" data-chosen="qtc" required="required" onchange=\'generateStoreState(id,"1")\' ','value','text', $default_country);
						?>
					</div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><label for="state_code"><?php echo Text::_('QTC_BILLIN_STATE') . " *"; ?></label></div>
					<div class="col-md-8">
						<select id="state_code" class="form-select required='required'" data-chosen="qtc">
							<option selected="selected"><?php echo Text::_('QTC_BILLIN_SELECT_STATE')?></option>
						</select>
					</div>
				</div>
				<div class="form-group col-md-6 row mt-2">
					<div class="form-label col-md-4"><?php echo $this->form->getLabel('phone'); ?></div>
					<div class="col-md-8"><?php echo $this->form->getInput('phone'); ?></div>
				</div>
			</div>
			<?php
			$showAddressTermsConditions      = $this->params->get('addressTermsConditons', 0);
			$addressTermsConditionsArticleId = $this->params->get('addressTermsConditonsArtId', 0);
			$showAddressTersmAndCond         = (!empty($showAddressTermsConditions) && !empty($addressTermsConditionsArticleId)) ? 1 : 0;

			if ($showAddressTersmAndCond)
			{?>
				<div class="row">
					<div class="col-sm-12 col-sm-push-1">
						<div class="form-group col-md-6 row mt-2">
							<div class="checkbox">
								<?php
									$checked = (!empty($this->item->privacy_terms_condition)) ? 'checked' : '';
									$link = Uri::root()."index.php?option=com_content&view=article&id=".$addressTermsConditionsArticleId."&tmpl=component";
								?>
								<label for="address_terms_condition">
									<input
										class="af-mb-10"
										type="checkbox"
										name="address_terms_condition"
										id="address_terms_condition"
										size="30" <?php echo $checked ?> required />
									<?php  echo Text::_('COM_QUICK2CART_ACCEPT_ADDRESS_TERMS_CONDITIONS_FIRST');
										$modalConfig = array('width' => '600px', 
											'height' => '600px', 
											'title' => Text::_('COM_QUICK2CART_ADDRESS_PRIVACY_POLICY'), 
											'closeButton' => true, 
											'modalWidth' => 80, 
											'bodyHeight' => 70);
										$modalConfig['url'] = $link;
										echo HTMLHelper::_('bootstrap.renderModal', 'privacyPolicyModal', $modalConfig);
										?>
										<a data-bs-target="#privacyPolicyModal" data-bs-toggle="modal" class="">
											<?php echo Text::_('COM_QUICK2CART_ADDRESS_PRIVACY_POLICY');?>
										</a>
									<?php  echo Text::_('COM_QUICK2CART_ACCEPT_ADDRESS_TERMS_CONDITIONS_LAST'); ?>
								</label>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			?>
			<div class="form-group row">
				<div class="col-md-12">
					<button type="button" onclick="closeAddressModalFun()" class="btn float-end ms-2 me-2 btn-default" title="<?php echo Text::_('JCANCEL'); ?>" data-bs-dismiss="modal" >
						<?php echo Text::_('JCANCEL'); ?>
					</button>

					<?php
					if ($this->canSave)
					{ ?>
						<a onclick="saveaddress('<?php echo ($this->item->id)?$this->item->id:'-1';?>')" class="btn float-end ms-2 btn-primary">
							<?php echo Text::_('JSUBMIT'); ?>
						</a>
					<?php
					} ?>
				</div>
			</div>
			<input type="hidden" name="option" value="com_quick2cart"/>
			<input type="hidden" name="task" value="customer_addressform.save"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</form>
	</div>
</div>
