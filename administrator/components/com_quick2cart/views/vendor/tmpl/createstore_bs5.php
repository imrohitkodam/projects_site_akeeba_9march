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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$comquick2cartHelper = new comquick2cartHelper;
$storeHelper         = new storeHelper;

$user             = Factory::getUser();
$app              = Factory::getApplication();
$entered_numerics = "'" . Text::_('QTC_ENTER_NUMERICS') . "'";

// 3.CHECK MAX CREATE STORE LIMIT
if (empty($this->allowToCreateStore))
{
	$userStoreCount = $storeHelper->getUserStoreCount();
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS;?>">
		<div class="well">
			<div class="alert alert-error">
				<span>
					<?php echo Text::sprintf('QTC_ALREADY_YOU_HAVE_STORES',$userStoreCount); ?>
				</span>
			</div>
		</div>
	</div>
	<?php
	return false;
}

$store_edit    = 0;
$store_vanity  = (!empty($this->storeinfo[0])) ? $this->storeinfo[0]->vanityurl : '0';
$qtc_params    = ComponentHelper::getparams('com_quick2cart');
$qtcshiphelper = new qtcshiphelper;

Text::script('QTC_NOT_ACCEPTABLE_FORM', true);
Text::script('COM_QUICK2CART_CHECK_USER_PRIVACY_TERMS', true);
?>

<script type="text/javascript">
	techjoomla.jQuery(document).ready(function() {
		generateStoreState(<?php echo isset($this->storeinfo[0]->country)?$this->storeinfo[0]->country:0;?>, <?php echo !empty($this->storeinfo[0]->region)?$this->storeinfo[0]->region:"0";?>);
	});

	function onLoadScript(){
		window.addEvent('domready', function()
		{
			document.formvalidator.setHandler('qtc_alphanum', function(value)
			{
				var regex = /^[0-9a-zA-Z\-]+$/;
				var status=regex.test(value);

				if (!status)
				{
					alert("<?php echo Text::_('QTC_ALPHA_NUM_ONLY'); ?>");
					techjoomla.jQuery('#store_alias').hide();
					techjoomla.jQuery('#storeVanityUrl').focus();
					techjoomla.jQuery('#storeVanityUrl').val('');
				}
				else
				{
					ckUniqueVanityURL();
				}

				return status;
			});
		});
	}

	function myValidate(f)
	{
		var title   = document.getElementById('title').value;
		var pincode = document.getElementById('pincode').value;
		var address = document.getElementById('address').value;
		var city    = document.getElementById('city').value;

		let privacyTermsConditions   = jQuery('#privacy_terms_condition').is(":checked");
		let showStoreTermsConditions = "<?php echo $qtc_params->get('storeTermsConditons', 0);?>";
		let storeTermsCondArtId      = "<?php echo $qtc_params->get('storeTermsConditonsArtId', 0);?>";

		if(title.trim() == '' || address.trim() == '' || city.trim() == '' || pincode == 0)
		{
			alert(Joomla.Text._('QTC_NOT_ACCEPTABLE_FORM'));

			return false;
		}
		// Check only store creation terms and conditios are enabled and article id is configured
		if (showStoreTermsConditions != 0 && storeTermsCondArtId != 0)
		{
			if(privacyTermsConditions === false)
			{
				let privacytermsAndConditonsFailureMsg = Joomla.Text._('COM_QUICK2CART_CHECK_USER_PRIVACY_TERMS');
				Joomla.renderMessages({"error":[privacytermsAndConditonsFailureMsg]});
				jQuery("html, body").animate({scrollTop: 0 }, 500);

				return false;
			}
		}

		if (document.formvalidator.isValid(f))
		{
			f.check.value="<?php echo Session::getFormToken(); ?>";

			return true;
		}
		else
		{
			let msg = "<?php echo Text::_('COP_NOT_ACCEPTABLE_ENTERY');?>";
			alert(msg);
		}

		return false;
	}

	function paymode(mode)
	{
		if (mode==0)
		{
			techjoomla.jQuery('#paypalmodeDiv').show();
			techjoomla.jQuery('#othermodeDiv').hide();
			/* add and remove required class*/
			techjoomla.jQuery('#paypalemail').addClass('required');
			techjoomla.jQuery('#otherPayMethod').removeClass('required');
			techjoomla.jQuery('#otherPayMethod').removeAttr('required');
			techjoomla.jQuery('#otherPayMethod').val('');
		}
		else
		{
			techjoomla.jQuery('#paypalmodeDiv').hide();
			techjoomla.jQuery('#othermodeDiv').show();
			/*add and remove required class*/
			techjoomla.jQuery('#otherPayMethod').addClass('required');
			techjoomla.jQuery('#paypalemail').removeClass('required');
			techjoomla.jQuery('#paypalemail').removeAttr('required');
			techjoomla.jQuery('#paypalemail').val('');
		}
	}

	/*THIS FUNCTION CHECK WHETHER VANITY URL IS UNIQE OR NOT*/
	function ckUniqueVanityURL()
	{
		var editstore="<?php echo $store_edit;?>";
		var newvanityURL = techjoomla.jQuery('#storeVanityUrl').value;

		if (typeof newvanityURL === 'undefined')
		{
			newvanityURL = '';
		}

		var oldVanity="<?php echo htmlspecialchars($store_vanity, ENT_COMPAT, 'UTF-8');?>";

		if (oldVanity != newvanityURL)
		{
			techjoomla.jQuery.ajax({
				url: '?option=com_quick2cart&controller=vendor&task=ckUniqueVanityURL&vanityURL='+newvanityURL+'&tmpl=component&format=raw',
				cache: false,
				type: 'GET',
				/*dataType: 'json',*/
				success: function(data)
				{
					/* already exist*/
					if (data == '1')
					{
						alert("<?php echo Text::_( 'QTC_VANITY_ALREADY_EXIST')?>");
						techjoomla.jQuery('#store_alias').hide();
						techjoomla.jQuery('#storeVanityUrl').focus();
					}
					elseif (! techjoomla.jQuery('#storeVanityUrl').hasClass('invalid'))
					{
						n=newvanityURL.replace(/([0-9]*)(:)/i,"$1-");
						techjoomla.jQuery('#store_alias span').html(n);
						techjoomla.jQuery('#store_alias').show();
					}
				}
			});
		}
	}

	function qtcbuttonAction(actionName)
	{
		if (actionName=='vendor.cancel')
		{
			document.qtcCreateStoreForm.btnAction.value = actionName;
			document.qtcCreateStoreForm.task.value = actionName;
			document.qtcCreateStoreForm.submit();

			return true;
		}

		var valid = myValidate(document.qtcCreateStoreForm);

		if (valid == true)
		{
			document.qtcCreateStoreForm.btnAction.value = actionName;
			/*document.qtcCreateStoreForm.task.value = actionName;*/
			document.qtcCreateStoreForm.submit();
		}
	}

	function generateStoreState(field_name, valToSelect)
	{
		var countryId     = 'storecountry';
		var country_value = techjoomla.jQuery('#'+countryId).val();

		if (valToSelect == 0)
		{
			var e = document.getElementById("qtcstorestate");
			var valToSelect = e.options[e.selectedIndex].value;
		}

		techjoomla.jQuery.ajax({
			type : "POST",
			url : "index.php?option=com_quick2cart&task=vendor.getRegions&tmpl=component&country_id="+country_value,
			success : function(response)
			{
				techjoomla.jQuery('#qtcstorestate').html(response);

				if (valToSelect > 0)
				{
					techjoomla.jQuery("#qtcstorestate option[value='" + valToSelect + "']").attr("selected", "true");
				}
			}
		});
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS;?> store-form">
	<form name="qtcCreateStoreForm" id="qtcCreateStoreForm" class="form-validate form-horizontal" method="post" enctype="multipart/form-data" onSubmit="return myValidate(this);" >
		<?php
		if (isset($this->checkGatewayDetails) && $this->checkGatewayDetails === true && $this->directPaymentConfig == 1 && !empty($this->vendorCheck))
		{
			?>
			<div class="alert alert-warning">
			<?php
				$vendor_id = $this->vendorCheck;
				$link      = 'index.php?option=com_tjvendors&view=vendor&layout=edit&client=com_quick2cart';
				echo Text::_('COM_QUICK2CART_ADMIN_PAYMENT_DETAILS_ERROR_MSG1');
				?>
					<a href="<?php echo Route::_($link . '&vendor_id=' . $vendor_id, false);?>" target="_blank">
						<?php echo Text::_('COM_QUICK2CART_VENDOR_FORM_LINK'); ?>
					</a>
				<?php echo Text::_('COM_QUICK2CART_ADMIN_PAYMENT_DETAILS_ERROR_MSG2');?>
			</div>
			<?php
		}

		$active      = 'create_store';
		$storehelper = new storehelper();
		$user_stores = $storehelper->getuserStoreList();
		?>
		<legend>
			<?php echo (empty($this->storeinfo))? Text::_( "QTC_CREATE_VENDER") : Text::_( "QTC_EDIT_VENDER_STORE"); ?>
		</legend>

		<!--main div -->
		<div>
			<div class="form-group row">
				<label for="title" class="form-label col-md-2" title="<?php echo Text::_('VENDER_TITLE_TOOLTIP');?>">
					<?php echo Text::_('VENDER_TITLE'). ' *';?>
				</label>
				<div class="col-md-4">
					<input
						type="text"
						name="title"
						id="title"
						class="inputbox required form-control"
						size="20"
						value="<?php if (!empty($this->storeinfo)){ echo $this->escape( stripslashes( $this->storeinfo[0]->title ) ); } ?>" />
				</div>
			</div>
			<hr class="hr hr-condensed"/>

			<!-- for user selection  -->
			<div class="form-group row ">
				<label for="store_creator_name" class="form-label col-md-2" title="<?php echo Text::_('COM_QUICK2CRT_STORE_OWNER')?>">
					<?php echo Text::_('COM_QUICK2CRT_STORE_OWNER');?>
				</label>
				<div class="col-md-4">
					<?php
					$userId = isset($this->storeinfo[0]->owner) ? Factory::getUser($this->storeinfo[0]->owner)->id : Factory::getUser()->id;
					$userFieldData = array ("required"=>1,
						"class" =>"",
						"size" => 0,
						"readonly" => "",
						"onchange" => "",
						"id" => "store_creator_id",
						"name" => "store_creator_id",
						"value" => $userId,
						"userName" => Factory::getUser($userId)->name
						);

					$q2cLayout = new FileLayout('joomla.form.field.user');
					echo $q2cLayout->render($userFieldData);
					?>
				</div>
			</div>

			<input
				type="hidden"
				name="storeVanityUrl"
				id="storeVanityUrl"
				value="<?php if (!empty($this->storeinfo[0]->vanityurl)){ echo stripslashes($this->storeinfo[0]->vanityurl);}?>"/>

			<div class="form-group row">
				<label for="description" class="form-label col-md-2" title="<?php echo Text::_('COM_QUICK2CART_VENDER_DESCRIPTION_TOOLTIP')?>">
					<?php echo Text::_('VENDER_DESCRIPTION');?>
				</label>
				<div class="col-md-4">
					<?php
					$enableEditor = $qtc_params->get('enable_editor', 0);
					$maxlength = $qtc_params->get('storeDescriptionLimit', 100);

					if (empty($enableEditor))
					{
						?>
						<textarea  size="28" rows="3" name="description" id="description" class="inputbox form-control" ><?php if (!empty($this->storeinfo)){ echo trim($this->storeinfo[0]->description);}?></textarea>
						<?php
					}
					else
					{
						$getEditor  = Factory::getApplication()->get('editor');
						$editor     = Editor::getInstance($getEditor);
						$storeDescription = isset($this->storeinfo[0]->description) ? $this->storeinfo[0]->description : '';
						echo $editor->display("description", $storeDescription, 400, 400, 40, 20, true);
					}
					?>
				</div>
			</div>

			<!-- Company name -->
			<div class="form-group row">
				<label for="companyname" class="form-label col-md-2" title="<?php echo Text::_('VENDER_COMPANY_NAME_TOOLTIP')?>">
					<?php echo Text::_('COMPANY_NAME');?>
				</label>
				<div class="col-md-4">
					<input
						type="text"
						name="companyname"
						id="companyname"
						class="inputbox form-control" size="20"
						value="<?php if (!empty($this->storeinfo[0]->company_name)){ echo stripslashes($this->storeinfo[0]->company_name); } ?>" />
				</div>
			</div>
			<div class="form-group row">
				<label for="email" class="form-label col-md-2" title="<?php echo Text::_('VENDER_EMAIL_TOOLTIP')?>">
					<?php echo Text::_('VENDER_EMAIL'). ' *';?>
				</label>
				<div class="col-md-4">
					<input
						type="email"
						name="email"
						id="email"
						class="inputbox required validate-email form-control" size="20"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->store_email); } elseif (!empty($user->email)){echo $user->email;}?>" />
				</div>
			</div>
			<div class="form-group row">
				<label for="phone" class="form-label col-md-2" title="<?php echo Text::_('VENDER_PHONE_TOOLTIP')?>">
					<?php echo Text::_('VENDER_PHONE'). ' *';?>
				</label>
				<div class="col-md-4">
					<input
						type="text"
						name="phone"
						id="phone"
						class="inputbox required form-control"
						onBlur="checkforalpha(this, '43', <?php echo $entered_numerics; ?>);"
						size="20" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->phone);}?>" />
				</div>
			</div>
			<hr class="hr hr-condensed"/>
			<!-- ADDRESS -->
			<div class="form-group row">
				<label for="address" class="form-label col-md-2" title="<?php echo Text::_('VENDER_ADDRESS_TOOLTIP')?>">
					<?php echo Text::_('VENDER_ADDRESS'). ' *';?>
				</label>
				<div class="col-md-4">
					<textarea maxlength="200" size="28" rows="3" name="address" id="address" class="inputbox required form-control" ><?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->address);}?></textarea>
				</div>
			</div>
			<!--Land Mark-->
			<div class="form-group row">
				<label for="land_mark" class="form-label col-md-2" title="<?php echo Text::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY_TOOLTIP')?>">
					<?php echo Text::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY');?>
				</label>
				<div class="col-md-4">
					<input
						type="text"
						name="land_mark"
						id="land_mark"
						class="inputbox form-control"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->land_mark);}?>" />
				</div>
			</div>
			<div class="form-group row">
				<label for="pincode" class="form-label col-md-2" title="<?php echo Text::_('QTC_BILLIN_ZIP_DESC')?>">
					<?php echo Text::_('QTC_BILLIN_ZIP'). ' *';?>
				</label>
				<div class="col-md-4">
					<input
						type="text"
						name="pincode"
						id="pincode"
						class="inputbox required form-control"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->pincode);}?>" />
				</div>
			</div>
			<!--Country-->
			<div class="form-group row">
				<label for="storecountry" class="form-label col-md-2" title="<?php echo Text::_('QTC_BILLIN_COUNTRY')?>">
					<?php echo Text::_('QTC_BILLIN_COUNTRY'). ' *';?>
				</label>
				<div class="col-md-4">
					<?php
					$country = $this->countrys;
					$options = array();
					$options[] = HTMLHelper::_('select.option', "", Text::_('QTC_BILLIN_SELECT_COUNTRY'));

					foreach ($country as $key=>$value)
					{
						$options[] = HTMLHelper::_('select.option', $value['id'], $value['country']);
					}

					$country = (!empty($this->storeinfo[0]->country)) ? $this->storeinfo[0]->country : "";

					echo HTMLHelper::_('select.genericlist',$options,'storecountry','class="form-select" required="required" onchange=\'generateStoreState(id,"1")\' ','value','text', $country);
					?>
				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<!--State-->
			<div class="form-group row" >
				<label for="qtcstorestate" class="form-label col-md-2" title="<?php echo Text::_('QTC_BILLIN_STATE')?>">
					<?php echo  Text::_('QTC_BILLIN_STATE'). ' *';?>
				</label>
				<div class="col-md-4">
					<select name="qtcstorestate" id="qtcstorestate" class="form-select" >
						<option selected="selected"><?php echo Text::_('QTC_BILLIN_SELECT_STATE')?></option>
					</select>
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<!--City-->
			<div class="form-group row">
				<label for="city" class="form-label col-md-2" title="<?php echo Text::_('COM_QUICK2CART_VENDER_CITY_TOOLTIP')?>">
					<?php echo Text::_('COM_QUICK2CART_VENDER_CITY'). ' *';?>
				</label>
				<div class="col-md-4">
					<input
						type="text"
						name="city"
						id="city"
						class="inputbox required form-control"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->city);}?>" />
				</div>
			</div>
			<hr class="hr hr-condensed"/>
			<!--avatar -->
			<div class="form-group row">
				<label for="avatar" class="form-label col-md-2" title="<?php echo Text::_('VENDER_AVTAR_TOOLTIP')?>">
					<?php echo Text::_('VENDER_AVTAR');?>
				</label>
				<div class="col-md-4">
					<?php
					$width  = $qtc_params->get('storeavatar_width');
					$height = $qtc_params->get('storeavatar_height');

					if (!empty($this->storeinfo[0]->store_avatar))
					{
						?>
						<input
							type="file"
							name="avatar"
							id="avatar"
							placeholder="<?php echo Text::_('COM_QUICK2CART_IMAGE_MSG');?>"
							accept="image/jpeg,image/png,image/jpg,image/gif" />
						<div class="text-warning">
							<p><?php echo Text::_('COM_Q2C_EXISTING_IMAGE_MSG');?></p>
						</div>
						<div class="text-info">
							<p><?php echo Text::_('COM_Q2C_EXISTING_IMAGE');?></p>
						</div>

						<div>
							<?php
							$img = '';

							if (!empty($this->storeinfo[0]->store_avatar))
							{
								$img = $comquick2cartHelper->isValidImg($this->storeinfo[0]->store_avatar);
							}

							if (empty($img))
							{
								$img = $storeHelper->getDefaultStoreImage();
							}
							?>
							<img class='img-rounded img-polaroid' src='<?php echo $img;?>' />
						</div>
						<?php
					}
					// While editing image field is not * required
					else
					{
						?>
						<input
							type="file"
							name="avatar"
							id="avatar"
							placeholder="<?php echo Text::_('COM_QUICK2CART_IMAGE_MSG');?>"
							class=""
							accept="image/jpeg,image/png,image/jpg,image/gif" />
						<?php
					}
					?>
					<div class="text-warning">
						<p><?php echo Text::sprintf('QTC_AVTAR_SIZE_MASSAGE', $height, $width);?></p>
						<p><?php echo Text::sprintf('COM_QUICK2CART_ALLOWED_IMG_FORMATS', 'gif, jpeg, jpg, png', $this->params->get('max_size', '1024'));?></p>
					</div>
				</div>
			</div>

			<!--Added in 2.9.12-->
			<?php
			$showStoreTermsConditions = $qtc_params->get('storeTermsConditons', 0);
			$storeTermsCondArtId      = $qtc_params->get('storeTermsConditonsArtId', 0);
			$showStoreTersmAndCond    = (!empty($showStoreTermsConditions) && !empty($storeTermsCondArtId)) ? 1 : 0;

			if ($showStoreTersmAndCond)
			{
			?>
				<div class="form-group row">
					<div class="checkbox">
						<?php
							$checked = (!empty($this->storeinfo[0]->privacy_terms_condition)) ? 'checked' : '';
							$link    = Uri::root() . "index.php?option=com_content&view=article&id=" . $storeTermsCondArtId . "&tmpl=component";
						?>
						<label for="privacy_terms_condition">
							<input
								class="af-mb-10"
								type="checkbox"
								name="privacy_terms_condition"
								id="privacy_terms_condition" size="30" <?php echo $checked ?> />
							<?php  echo Text::_('COM_QUICK2CART_ACCEPT_STORE_TERMS_CONDITIONS_FIRST'); ?>
							<a href="" class="" data-bs-toggle="modal" data-bs-target="#storeCreationTermConditionModal">
								<?php echo Text::_('COM_QUICK2CART_STORE_PRIVACY_POLICY');?>
							</a>
							<?php
								echo HTMLHelper::_(
									'bootstrap.renderModal',
									'storeCreationTermConditionModal',
									array(
										'title'      => Text::_('COM_QUICK2CART_STORE_PRIVACY_POLICY'),
										'url'        => $link,
										'height' => '100%',
										'width'  => '100%',
										'modalWidth' => '80',
										'bodyHeight' => '70'
									)
								);
							?>
							<?php echo Text::_('COM_QUICK2CART_ACCEPT_STORE_TERMS_CONDITIONS_LAST'); ?>
						</label>
					</div>
				</div>
			<?php
			}

			// Trigger OnBeforeCreateStore
			if (!empty($this->OnBeforeCreateStore))
			{
				echo $this->OnBeforeCreateStore;
			}

			// Store limit msg
			$storeLimitPerUser = $qtc_params->get('storeLimitPerUser');
			?>

			<div class="form-actions">
				<button type="button"
					title="<?php echo Text::_('BUTTON_SAVE_TEXT'); ?>"
					class="q2c-btn-wrapper btn btn-medium btn-success"
					onclick="qtcbuttonAction('vendor.save');">
						<?php echo Text::_('BUTTON_SAVE_TEXT');?>
				</button>
				<button type="submit"
					class="q2c-btn-wrapper btn btn-medium btn-success"
					title="<?php echo Text::_('BUTTON_SAVE_AND_CANCEL')?>"
					onclick="qtcbuttonAction('vendor.saveAndClose');">
						<?php echo Text::_('BUTTON_SAVE_AND_CANCEL')?>
				</button>
				<button type="button"
					title="<?php echo Text::_('BUTTON_CANCEL_TEXT');?>"
					class="q2c-btn-wrapper btn btn-medium btn-danger"
					onclick="qtcbuttonAction('vendor.cancel');" >
						<?php echo Text::_( 'BUTTON_CANCEL_TEXT');?>
				</button>
			</div>
		</div>
		<!-- end main div-->
		<input type="hidden" name="option" value="com_quick2cart"/>
		<input type="hidden" name="task" value="vendor.save" />
		<input type="hidden" name="btnAction" value="saveAndClose" />
		<!-- by default saveAndClose -->
		<input type="hidden" name="view" value="vendor" />
		<input type="hidden" name="check" value="" />
		<?php $id=!empty($this->storeinfo)?$this->storeinfo[0]->id:''?>
		<input type="hidden" name="id" value="<?php echo $id;?>"/>
		<?php
		if (!empty($this->adminCall))
		{
			?>
			<input type="hidden" name="qtcadminCall" value="<?php echo $this->adminCall; ?>" />
			<?php
		}
		?>
		<?php echo HTMLHelper::_( 'form.token' ); ?>
	</form>
</div>
