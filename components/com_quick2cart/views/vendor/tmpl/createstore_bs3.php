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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$comquick2cartHelper = new comquick2cartHelper;
$storeHelper         = new storeHelper;

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('behavior.framework');
}

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

// 1.check user is logged or not
$user             = Factory::getUser();
$app              = Factory::getApplication();
$storeHelper      = new storeHelper;
$entered_numerics = "'" . Text::_('QTC_ENTER_NUMERICS') . "'";

// Check user is logged or not.
if (!$user->id)
{
	$return                = base64_encode(Uri::getInstance());
	$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
	$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
	$app->redirect($login_url_with_return, 403);
}
else
{
	$canEdit = $user->authorise('core.edit', 'com_quick2cart') || $user->authorise('core.create', 'com_quick2cart');

	if (!$canEdit)
	{
		Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
		return false;
	}
}

if (isset($this->orders_site) && !empty($this->editview) && empty($this->store_authorize))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS;?>">
		<div class="well" >
			<div class="alert alert-danger">
				<span><?php echo Text::_('QTC_NOT_AUTHORIZED_USER_TO_VIEW_ORDER'); ?></span>
			</div>
		</div>
	</div>
	<?php
	return false;
}

// 3.CHECK MAX CREATE STORE LIMIT
if (empty($this->allowToCreateStore))
{
	$userStoreCount = $storeHelper->getUserStoreCount();
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS;?>">
		<div class="well">
			<div class="alert alert-danger">
				<span><?php echo Text::sprintf('QTC_ALREADY_YOU_HAVE_STORES',$userStoreCount); ?></span>
			</div>
		</div>
	</div>
	<?php
	return false;
}

if (!$this->allowed)
{
	?>
	<div class="alert alert-info alert-help-inline">
		<?php echo Text::_('COM_QUICK2CART_VENDOR_ENFORCEMENT_ERROR');?>
		<?php echo Text::_('COM_QUICK2CART_VENDOR_ENFORCEMENT_VENDOR_REDIRECT_MESSAGE');?>
	</div>
	<div>
		<a
			href="<?php echo Route::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=com_quick2cart');?>"
			target="_blank" >
			<button class="btn btn-primary">
				<?php echo Text::_('COM_QUICK2CART_VENDOR_ENFORCEMENT_VENDOR_REDIRECT_LINK'); ?>
			</button>
		</a>
	</div>
	<?php

	return;
}

$store_edit    = 0;
$store_vanity  = (!empty($this->storeinfo[0])) ? $this->storeinfo[0]->vanityurl : '0';
$qtcshiphelper = new qtcshiphelper;
?>
<script type="text/javascript">
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
					/*To make the textbox blank if worng value entered in vanity url*/
					techjoomla.jQuery('#storeVanityUrl').val('');
				}

				return status;
			});
		});
	}

	/*To get the title value alpha numeric along with space.*/
	function onLoadScript(){
		window.addEvent('domready', function()
		{
			document.formvalidator.setHandler('qtc_alphanum_title', function(value)
			{
				var regex = /^[a-z\d\-_\s]+$/i;
				var status=regex.test(value);

				return status;
			});
		});
	}

	techjoomla.jQuery(document).ready(function() {
		generateStoreState(<?php echo isset($this->storeinfo[0]->country)?$this->storeinfo[0]->country:0;?>, <?php echo !empty($this->storeinfo[0]->region)?$this->storeinfo[0]->region:"0";?>);
	});

	function myValidate(f)
	{
		var vanityCheck = ckUniqueVanityURL();

		if (vanityCheck)
		{
			var newvanityURL = (typeof newvanityURL === 'undefined') ? '' : techjoomla.jQuery('#storeVanityUrl').value;
			var n            = newvanityURL.replace(/([0-9]*)(:)/i,"$1-");
			techjoomla.jQuery('#store_alias span').html(n);
			techjoomla.jQuery('#store_alias').show();
		}
		else
		{
			alert("<?php echo Text::_( 'QTC_VANITY_ALREADY_EXIST')?>");
			techjoomla.jQuery('#store_alias').hide();
			techjoomla.jQuery('#storeVanityUrl').focus();
			techjoomla.jQuery('#storeVanityUrl').addClass('invalid');

			return false;
		}

		// User accepted terms and condition validation.
		let showStoreTermsConditions = "<?php echo $this->params->get('storeTermsConditons', 0);?>";
		let storeTermsCondArtId      = "<?php echo $this->params->get('storeTermsConditonsArtId', 0);?>";
		let privacyTermsConditions   = jQuery('#privacy_terms_condition').is(":checked");

		if (showStoreTermsConditions != 0 && storeTermsCondArtId != 0)
		{
			if(privacyTermsConditions === false)
			{
				let privacytermsAndConditonsFailureMsg = Joomla.Text._('COM_QUICK2CART_CHECK_USER_PRIVACY_TERMS');
				Joomla.renderMessages({'alert alert-error':[privacytermsAndConditonsFailureMsg]});
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
			techjoomla.jQuery('#paypalemail').addClass('required');
			techjoomla.jQuery('#otherPayMethod').removeClass('required');
			techjoomla.jQuery('#otherPayMethod').removeAttr('required');
			techjoomla.jQuery('#otherPayMethod').val('');
		}
		else
		{
			techjoomla.jQuery('#paypalmodeDiv').hide();
			techjoomla.jQuery('#othermodeDiv').show();
			techjoomla.jQuery('#otherPayMethod').addClass('required');
			techjoomla.jQuery('#paypalemail').removeClass('required');
			techjoomla.jQuery('#paypalemail').removeAttr('required');
			techjoomla.jQuery('#paypalemail').val('');
		}
	}

	/*THIS FUNCTION CHECK WHETHER VANITY URL IS UNIQE OR NOT*/
	function ckUniqueVanityURL()
	{
		var editstore    = "<?php echo $store_edit;?>";
		var newvanityURL = (typeof newvanityURL === 'undefined') ? '' :techjoomla.jQuery('#storeVanityUrl').value;
		var oldVanity    = "<?php echo htmlspecialchars($store_vanity, ENT_COMPAT, 'UTF-8');?>";
		var status       = false;

		if (oldVanity != newvanityURL)
		{
			techjoomla.jQuery.ajax({
				url: '?option=com_quick2cart&task=vendor.ckUniqueVanityURL&vanityURL='+newvanityURL+'&tmpl=component',
				cache: false,
				type: 'GET',
				async:false,
				success: function(data)
				{
					status = (data == '1') ? false : true;
				}
			});

			return status;
		}
		else
		{
			return true;
		}
	}

	function generateStoreState(field_name, valToSelect)
	{
		var countryId = 'storecountry';
		var country_value=techjoomla.jQuery('#'+countryId).val();

		if (valToSelect == 0)
		{
			var e = document.getElementById("qtcstorestate");
			var valToSelect = e.options[e.selectedIndex].value;
		}

		techjoomla.jQuery.ajax({
			type : "POST",
			url : Joomla.getOptions('system.paths').base + "/index.php?option=com_quick2cart&task=vendor.getRegions&tmpl=component&country_id="+country_value,
			success : function(response)
			{
				console.log(response);
				techjoomla.jQuery('#qtcstorestate').html(response);

				if (valToSelect > 0)
				{
					techjoomla.jQuery("#qtcstorestate option[value='" + valToSelect + "']").attr("selected", "true");
				}
			}
		});
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
			document.qtcCreateStoreForm.submit();
		}
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS;?> store-form container-fluid">
	<form name="qtcCreateStoreForm" id="qtcCreateStoreForm" class="form-validate form-horizontal" method="post" enctype="multipart/form-data">
		<?php
		if ($this->checkGatewayDetails === true && $this->directPaymentConfig == 1)
		{
			?>
			<div class="alert alert-warning">
				<?php
				$vendor_id = $this->vendorCheck;
				$link      = 'index.php?option=com_tjvendors&view=vendor&layout=profile&client=com_quick2cart';
				echo Text::_('COM_QUICK2CART_PAYMENT_DETAILS_ERROR_MSG1');
				?>
					<a href="<?php echo Route::_($link . '&vendor_id=' . $vendor_id, false);?>" target="_blank">
						<?php echo Text::_('COM_QUICK2CART_VENDOR_FORM_LINK'); ?>
					</a>
				<?php echo Text::_('COM_QUICK2CART_PAYMENT_DETAILS_ERROR_MSG2');?>
			</div>
			<?php
		}

		$active = 'create_store';
		$user_stores = $storeHelper->getuserStoreList();

		if (count($user_stores) >0)
		{
			$view=$comquick2cartHelper->getViewpath('vendor','toolbar_bs3');
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
		}
		?>

		<h1>
			<strong><?php echo (empty($this->storeinfo))? Text::_( "QTC_CREATE_VENDER") : Text::_( "QTC_EDIT_VENDER_STORE"); ?></strong>
		</h1>
		<div>
			<div class="form-group row">
				<label for="title" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('VENDER_TITLE_TOOLTIP')?>">
					<?php echo '* ' . Text::_('VENDER_TITLE');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<input
						type="text"
						name="title"
						id="title"
						class="required form-control"
						value="<?php if (!empty($this->storeinfo)){ echo $this->escape(stripslashes($this->storeinfo[0]->title)); } ?>" />
				</div>
			</div>
			<?php
			$is_sef = $app->get('sef');

			if ($is_sef==1)
			{
				?>
				<div class="form-group row">
					<label for="vendor_storeVanityUrl" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('VENDER_STORE_VANITY_URL_TOOLTIP')?>">
						<?php echo Text::_('STORE_VANITY_URL');?>
					</label>
					<div class="col-sm-9 col-xs-12">
						<?php
						$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category');

						if (!empty($this->storeinfo[0]->id))
						{
							$vanity_url = Uri::root().substr($storeHelper->getStoreLink($this->storeinfo[0]->id), strlen(Uri::base(true)) + 1);
						}

						$menu = $app->getMenu();
						$lang = Factory::getLanguage();
						/* @TODO JUGAD HERE for adding index.php when category menu is default menu*/
						?>

						<input
							type="text"
							name="storeVanityUrl"
							id="storeVanityUrl"
							class="validate-qtc_alphanum form-control"
							value="<?php if (!empty($this->storeinfo[0]->vanityurl)){ echo stripslashes($this->storeinfo[0]->vanityurl);}?>"
							placeholder="<?php echo Text::_("COM_QUICK2CART_VANITY_URL_HINT"); ?>" />

						<?php
						$multivendor_enable = $this->params->get('multivendor');

						if (!empty($multivendor_enable))
						{
							?>
							<span id="store_alias" style="<?php echo (empty($this->storeinfo[0]->vanityurl)) ? "display:none;": "";?>" class="help-inline">
								<strong><?php echo Text::_('QTC_VANITY_DES_EG').'&nbsp';?></strong>
								<i>
									<?php
										if (!empty($vanity_url))
										{
											echo $vanity_url;
										}
									?>
								</i>
							</span>
							<?php
						}
						?>
					</div>
				</div>
			<?php
			}
			// if SEF if OFF ENDS....dont show vanity URL
			?>

			<hr class="hr hr-condensed"/>
			<div class="form-group row">
				<label for="description" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('COM_QUICK2CART_VENDER_DESCRIPTION_TOOLTIP')?>">
					<?php echo Text::_('VENDER_DESCRIPTION');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<?php
					$enableEditor = $this->params->get('enable_editor', 0);

					if (empty($enableEditor))
					{
						?>
						<textarea size="28" rows="3" name="description" id="description" class="inputbox form-control" ><?php if (!empty($this->storeinfo)){ echo trim($this->storeinfo[0]->description);}?></textarea>
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
				<label for="companyname" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('VENDER_COMPANY_NAME_TOOLTIP')?>">
					<?php echo Text::_('COMPANY_NAME');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<input
						type="text"
						name="companyname"
						id="companyname"
						class="form-control"
						value="<?php if (!empty($this->storeinfo[0]->company_name)){ echo stripslashes($this->storeinfo[0]->company_name); } ?>" />
				</div>
			</div>

			<div class="form-group row">
				<label for="email" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('VENDER_EMAIL_TOOLTIP')?>">
					<?php echo '* ' . Text::_('VENDER_EMAIL');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<input
						type="email"
						name="email"
						id="email"
						class="required validate-email form-control"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->store_email); } elseif (!empty($user->email)){echo $user->email;}?>" />
				</div>
			</div>
			<hr class="hr hr-condensed"/>

			<div class="form-group row">
				<label for="address" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('VENDER_ADDRESS_TOOLTIP')?>">
					<?php echo '* '.Text::_('VENDER_ADDRESS');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<textarea maxlength="200" size="28" rows="3" name="address" id="address" class="required form-control" ><?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->address);}?></textarea>
				</div>
				<div class="col-sm-5 col-xs-12">&nbsp;</div>
			</div>
			<div class="form-group row">
				<label for="phone" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('VENDER_PHONE_TOOLTIP')?>">
					<?php echo '* ' . Text::_('VENDER_PHONE');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<input type="text" name="phone" id="phone"
						class="required form-control"
						onBlur="checkforalpha(this ,'43', <?php echo $entered_numerics; ?>);"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->phone);}?>" />
				</div>
			</div>

			<!--Land Mark-->
			<div class="form-group row">
				<label for="land_mark" class="form-label col-sm-3 col-xs-12" title="<?php echo Text::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY_TOOLTIP')?>">
					<?php echo Text::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<input
						type="text"
						name="land_mark"
						id="land_mark"
						class="form-control"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->land_mark);}?>" />
				</div>
			</div>

			<div class="form-group row">
				<label for="pincode" class="form-label col-sm-3 col-xs-12" title="<?php echo Text::_('QTC_BILLIN_ZIP_DESC')?>">
					<?php echo '* '.Text::_('QTC_BILLIN_ZIP');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<input
						type="text"
						name="pincode"
						id="pincode"
						class="form-control required form-control"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->pincode);}?>" />
				</div>
			</div>

			<!--Country-->
			<div class="form-group row">
				<label for="storecountry" class="col-sm-3 col-xs-12 form-label">
					<?php echo "* " . Text::_('QTC_BILLIN_COUNTRY')?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<?php
					$country   = $this->countrys;
					$options   = array();
					$options[] = HTMLHelper::_('select.option', "", Text::_('QTC_BILLIN_SELECT_COUNTRY'));

					foreach ($country as $key=>$value)
					{
						$options[] = HTMLHelper::_('select.option', $value['id'], $value['country']);
					}

					$country = (!empty($this->storeinfo[0]->country)) ? $this->storeinfo[0]->country : "";

					echo $this->dropdown = HTMLHelper::_('select.genericlist', $options, 'storecountry', 'required="required" class="form-select" onchange=\'generateStoreState(id,"1")\' ', 'value', 'text', $country);
					?>
				</div>
			</div>

			<!--State-->
			<div class="form-group row" >
				<label for="qtcstorestate" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('QTC_BILLIN_STATE')?>">
					<?php echo "* " .  Text::_('QTC_BILLIN_STATE')?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<select name="qtcstorestate" id="qtcstorestate" class="form-select" >
						<option selected="selected"><?php echo Text::_('QTC_BILLIN_SELECT_STATE')?></option>
					</select>
				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<!--City-->
			<div class="form-group row">
				<label for="city" class="form-label col-sm-3 col-xs-12" title="<?php echo Text::_('COM_QUICK2CART_VENDER_CITY_TOOLTIP')?>">
					<?php echo '* ' . Text::_('COM_QUICK2CART_VENDER_CITY');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<input
						type="text"
						name="city"
						id="city"
						class="form-control required"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->city);}?>" />
				</div>
			</div>
			<hr class="hr hr-condensed"/>

			<!--avatar -->
			<div class="form-group row">
				<label for="avatar" class="col-sm-3 col-xs-12 form-label" title="<?php echo Text::_('VENDER_AVTAR_TOOLTIP')?>">
					<?php echo Text::_('VENDER_AVTAR');?>
				</label>
				<div class="col-sm-9 col-xs-12">
					<?php
					$width  = $this->params->get('storeavatar_width');
					$height = $this->params->get('storeavatar_height');

					if (!empty($this->storeinfo[0]->store_avatar))
					{
						?>
						<input
							type="file"
							name="avatar"
							id="avatar"
							class="form-control"
							placeholder="<?php echo Text::_('COM_QUICK2CART_IMAGE_MSG');?>"
							accept="image/jpeg,image/png,image/jpg,image/gif" />
						<div class="text-warning"><p><?php echo Text::_('COM_Q2C_EXISTING_IMAGE_MSG');?></p></div>
						<div class="text-info"><p><?php echo Text::_('COM_Q2C_EXISTING_IMAGE');?></p></div>
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
							class="form-control"
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
			$showStoreTermsConditions = $this->params->get('storeTermsConditons', 0);
			$storeTermsCondArtId      = $this->params->get('storeTermsConditonsArtId', 0);
			$showStoreTersmAndCond    = (!empty($showStoreTermsConditions) && !empty($storeTermsCondArtId)) ? 1 : 0;

			if ($showStoreTersmAndCond)
			{
				?>
				<div class="form-group row af-text-center">
					<div class="checkbox">
						<?php
							$checked = '';

							if (!empty($this->storeinfo[0]->privacy_terms_condition))
							{
								$checked = 'checked';
							}

							$link = Uri::root() . "index.php?option=com_content&view=article&id=" . $storeTermsCondArtId . "&tmpl=component";
						?>
						<label for="privacy_terms_condition" class="form-label">
							<input
								class=""
								type="checkbox"
								name="privacy_terms_condition"
								id="privacy_terms_condition" <?php echo $checked ?>/>
							<?php echo Text::_('COM_QUICK2CART_ACCEPT_STORE_TERMS_CONDITIONS_FIRST'); ?>
							<a href="" class="" data-bs-toggle="modal" data-bs-target="#storeCreationTermConditionModal">
								<?php echo Text::_('COM_QUICK2CART_STORE_PRIVACY_POLICY');?>
							</a>
							<?php
								echo HTMLHelper::_(
									'bootstrap.renderModal',
									'storeCreationTermConditionModal',
									array(
										'title'		 => Text::_('COM_QUICK2CART_STORE_PRIVACY_POLICY'),
										'url'        => $link,
										'height' => '100%',
										'width'  => '100%',
										'modalWidth' => '80',
										'bodyHeight' => '70'
									)
								);
							?>
							<?php echo Text::_('COM_QUICK2CART_ACCEPT_STORE_TERMS_CONDITIONS_LAST');?>
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
			$storeLimitPerUser = $this->params->get('storeLimitPerUser');

			if (!empty($storeLimitPerUser))
			{
				?>
				<div class="alert alert-info">
					<span><?php echo Text::sprintf('QTC_CRAETE_STORE_LIMIT_NOTE', $storeLimitPerUser); ?></span>
				</div>
				<?php
			}
			?>

			<div class="">
				<button type="button"
					title="<?php echo Text::_('BUTTON_SAVE_TEXT'); ?>"
					class="q2c-btn-wrapper btn btn-medium btn-success"
					onclick="qtcbuttonAction('vendor.save');" >
						<?php echo Text::_('BUTTON_SAVE_TEXT');?>
				</button>
				<button type="button"
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
		<input type="hidden" name="option" value="com_quick2cart"/>
		<input type="hidden" name="task" value="vendor.save" />
		<input type="hidden" name="btnAction" value="saveAndClose" />
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

		echo HTMLHelper::_('form.token'); ?>
	</form>
</div>
