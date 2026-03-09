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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

$comquick2cartHelper = new comquick2cartHelper;
$storeHelper=new storeHelper;

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('behavior.framework');
}

HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.modal', 'a.modal');

// 1.check user is logged or not
$user = Factory::getUser();
$app = Factory::getApplication();
$storeHelper=new storeHelper;

//added by aniket
$entered_numerics = "'" . Text::_('QTC_ENTER_NUMERICS') . "'";

// Check user is logged or not.
if (!$user->id)
{
	$return = base64_encode(Uri::getInstance());
	$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
	$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
	$app->redirect($login_url_with_return, 403);
}

if (!$app->isClient('administrator'))
{
	// 1.check AUTHORIZATION
	if (isset($this->orders_site) && !empty($this->editview) && empty($this->store_authorize) )
	{
		?>
		<div class="<?php echo Q2C_WRAPPER_CLASS;?>">
			<div class="well" >
				<div class="alert alert-error">
					<span>
						<?php echo Text::_('QTC_NOT_AUTHORIZED_USER_TO_VIEW_ORDER'); ?>
					</span>
				</div>
			</div>
		</div>
		<?php
		return false;
	}
}

// 3.CHECK MAX CREATE STORE LIMIT
if (empty($this->allowToCreateStore))
{
	$userStoreCount=$storeHelper->getUserStoreCount();
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

if (!$this->allowed)
{
	?>
	<div class="alert alert-info alert-help-inline">
		<?php echo Text::_('COM_QUICK2CART_VENDOR_ENFORCEMENT_ERROR');?>
		<?php echo Text::_('COM_QUICK2CART_VENDOR_ENFORCEMENT_VENDOR_REDIRECT_MESSAGE');?>
	</div>
	<div>
		<a href="<?php echo Route::_('index.php?option=com_tjvendors&view=vendor&layout=edit&client=com_quick2cart');?>" target="_blank" >
		<button class="btn btn-primary">
			<?php echo Text::_('COM_QUICK2CART_VENDOR_ENFORCEMENT_VENDOR_REDIRECT_LINK'); ?>
		</button>
		</a>
	</div>
	<?php

	return;
}

$store_edit=0;
$store_vanity='0';

if (!empty($this->storeinfo[0]))
{
	$store_edit=0;
	$store_vanity=$this->storeinfo[0]->vanityurl;
}

$qtc_params = ComponentHelper::getparams('com_quick2cart');
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
					/*added by aniket to make the textbox blank if worng value entered in vanity url*/
					techjoomla.jQuery('#storeVanityUrl').val('');
				}

				return status;
			});
		});
	}

	function onLoadScript(){
		/*added by aniket --to get the title value alpha numeric along with space.*/
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
			var newvanityURL = techjoomla.jQuery('#storeVanityUrl').value;

			if (typeof newvanityURL === 'undefined')
			{
				newvanityURL = '';
			}

			var n = newvanityURL.replace(/([0-9]*)(:)/i,"$1-");
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
		let showStoreTermsConditions = "<?php echo $qtc_params->get('storeTermsConditons', 0);?>";
		let storeTermsCondArtId = "<?php echo $qtc_params->get('storeTermsConditonsArtId', 0);?>";
		let privacyTermsConditions = jQuery('#privacy_terms_condition').is(":checked");

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
			var msg = "<?php echo Text::_('COP_NOT_ACCEPTABLE_ENTERY');?>";
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

		var status = false;

		if (oldVanity != newvanityURL)
		{
			techjoomla.jQuery.ajax({
				url: '?option=com_quick2cart&task=vendor.ckUniqueVanityURL&vanityURL='+newvanityURL+'&tmpl=component',
				cache: false,
				type: 'GET',
				async:false,
				success: function(data)
				{
					/* already exist*/
					if (data == '1')
					{
						status = false;
					}
					else
					{
						status = true;
					}
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

	function jSelectUser_jform_created_by(id, title)
	{
		var old_id = document.getElementById("store_creator_id").value;

		if (old_id != id)
		{
			document.getElementById("store_creator_id").value = id;
			document.getElementById("store_creator_name").value = title;
		}

		SqueezeBox.close();
	}
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS;?> store-form">
	<form name="qtcCreateStoreForm" id="qtcCreateStoreForm" class="form-validate form-horizontal" method="post" enctype="multipart/form-data">
		<?php
		if ($this->checkGatewayDetails === true && $this->directPaymentConfig == 1)
		{
			?>
			<div class="alert alert-warning">
			<?php
				$vendor_id = $this->vendorCheck;
				$link = 'index.php?option=com_tjvendors&view=vendor&layout=profile&client=com_quick2cart';
				echo Text::_('COM_QUICK2CART_PAYMENT_DETAILS_ERROR_MSG1');
				?>
					<a href="<?php echo Route::_($link . '&vendor_id=' . $vendor_id, false);?>" target="_blank">
					<?php echo Text::_('COM_QUICK2CART_VENDOR_FORM_LINK'); ?></a>
				<?php echo Text::_('COM_QUICK2CART_PAYMENT_DETAILS_ERROR_MSG2');?>
			</div>
			<?php
		}

		$active = 'create_store';
		$comquick2cartHelper = new comquick2cartHelper;
		$user_stores = $storeHelper->getuserStoreList();

		if (count($user_stores) >0)
		{
			if (!$app->isClient('administrator'))
			{
				$view=$comquick2cartHelper->getViewpath('vendor','toolbar');
				ob_start();
				include($view);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
			}
		}
		?>

		<legend>
			<?php echo (empty($this->storeinfo))? Text::_( "QTC_CREATE_VENDER") : Text::_( "QTC_EDIT_VENDER_STORE"); ?>
		</legend>
		<!--main div -->
		<div>
			<div class="control-group">
				<label for="title" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('VENDER_TITLE_TOOLTIP'), Text::_('VENDER_TITLE'), '', '* '.Text::_('VENDER_TITLE'));?></label>
				<div class="controls">
					<input type="text" name="title" id="title" class="inputbox required"  value="<?php if (!empty($this->storeinfo)){ echo $this->escape( stripslashes( $this->storeinfo[0]->title ) ); } ?>" />
					<!--<div class="text-warning">
						<p><?php //echo Text::_('COM_Q2C_ALPHANUM_NOTE'); ?></p>
					</div> -->
				</div>
			</div>

			<!-- for STORE VANITY URL like sitename.com/index.php/store/storevanity-->
			<!--Added by aniket .. show this only if SEF is on-->
			<?php
			$is_sef = $app->get('sef');

			if ($is_sef==1 && !$app->isClient('administrator'))
			{
				?>
				<div class="control-group">
					<label for="vendor_storeVanityUrl" class="control-label">
						<?php echo HTMLHelper::tooltip(Text::_('VENDER_STORE_VANITY_URL_TOOLTIP'), Text::_('STORE_VANITY_URL'), '', Text::_('STORE_VANITY_URL'));?>
					</label>
					<div class="controls">
						<?php
						$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category');

						/* @TODO JUGAD HERE for Vanity URL to display, DO NOT REMOVE &vanitydisplay=1 from $vanity_url */
						//  $vanity_url = JUri::root().substr(Route::_('index.php?option=com_quick2cart&view=vendor&layout=store&vanitydisplay=1&Itemid='.$Itemid), strlen(JUri::base(true)) + 1);

						if (!empty($this->storeinfo[0]->id))
						{
							$vanity_url = Uri::root().substr($storeHelper->getStoreLink($this->storeinfo[0]->id), strlen(Uri::base(true)) + 1);
						}

						$menu = Factory::getApplication()->getMenu();
						$lang = Factory::getLanguage();
						/* @TODO JUGAD HERE for adding index.php when category menu is default menu*/
						?>

						<input type="text" name="storeVanityUrl" id="storeVanityUrl"
							class="inputbox validate-qtc_alphanum "
							value="<?php if (!empty($this->storeinfo[0]->vanityurl)){ echo stripslashes($this->storeinfo[0]->vanityurl);}?>" placeholder="<?php echo Text::_("COM_QUICK2CART_VANITY_URL_HINT"); ?>" />

						<?php
						$multivendor_enable = $qtc_params->get('multivendor');

						if (!empty($multivendor_enable))
						{
							?>
							<span id="store_alias" style="<?php echo (empty($this->storeinfo[0]->vanityurl)) ? "display:none;": "";?>" class="help-inline">
								<strong><?php echo Text::_('QTC_VANITY_DES_EG').'&nbsp';?></strong>
								<i>
									<?php
										//  echo $vanity_url . (($Itemid==$menu->getDefault($lang->getTag())->id) ? 'index.php' : '') . '/';
										if (!empty($vanity_url))
										{
											echo $vanity_url;
										}
										//~ if (!empty($this->storeinfo[0]->vanityurl))
										//~ {
											//~ echo preg_replace('/([0-9]*)(:)/i', "$1-", stripslashes($this->storeinfo[0]->vanityurl));
										//~ }

										//~ echo '/' . Text::_('QTC_VANITY_PAGE');
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

			<?php if ($app->isClient('administrator'))
			{
				?>
				<!-- for user selection  -->
				<div class="control-group ">
					<label for="store_creator_name" class="control-label">
						<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CRT_STORE_OWNER_TITLE'), Text::_('COM_QUICK2CRT_STORE_OWNER'), '', Text::_('COM_QUICK2CRT_STORE_OWNER'));?>
					</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" id="store_creator_name" name="store_creator_name"
								class="input-medium required" disabled="disabled"
								placeholder="<?php echo Text::_('COM_QUICK2CRT_STORE_OWNER');?>"
								value="<?php echo (isset( $this->storeinfo[0]->owner)) ? Factory::getUser($this->storeinfo[0]->owner)->name : Factory::getUser()->name; ?>">
								<a class="modal qtc_modal  button btn btn-info modal_jform_created_by"
									rel="{handler: 'iframe', size: {x: 800, y: 500}}"
									href="index.php?option=com_users&view=users&layout=modal&tmpl=component&field=jform_created_by"
									title="<?php echo Text::_('COM_STORE_STORE_CREATOR');?>" >
										<i class="icon-user"></i>
								</a>
						</div>

						<input type="hidden" id="store_creator_id" name="store_creator_id"
							class="required"
							value="<?php echo (isset($this->storeinfo[0]->owner)) ? Factory::getUser($this->storeinfo[0]->owner)->id : Factory::getUser()->id; ?>" />
					</div>
				</div>
				<?php
			}
			?>

			<div class="control-group">
				<label for="description" class="control-label">
					<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_VENDER_DESCRIPTION_TOOLTIP'), Text::_('VENDER_DESCRIPTION'), '', Text::_('VENDER_DESCRIPTION'));?>
				</label>
				<div class="controls">
					<!--
					<input type="text" name="description" id="description" class="inputbox required validate-name"    value="<?php //if ($this->storeinfo){ echo $this->escape( stripslashes( $this->storeinfo[0]->code ) ); } ?>" />
					-->
					<?php
					$enableEditor = $qtc_params->get('enable_editor', 0);

					if (empty($enableEditor))
					{
						?>
						<textarea  size="28" rows="3" name="description" id="description" class="inputbox" ><?php if (!empty($this->storeinfo)){ echo trim($this->storeinfo[0]->description);}?></textarea>
						<?php
					}
					else
					{
						$editor = Factory::getEditor();
						$storeDescription = isset($this->storeinfo[0]->description) ? $this->storeinfo[0]->description : '';

						echo $editor->display("description", $storeDescription, 400, 400, 40, 20, true);
					}
					?>
				</div>
			</div>

			<!-- Company name -->
			<div class="control-group">
				<label for="companyname" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('VENDER_COMPANY_NAME_TOOLTIP'), Text::_('COMPANY_NAME'), '',Text::_('COMPANY_NAME'));?></label>
				<div class="controls">
					<input type="text" name="companyname" id="companyname"
						class="inputbox"
						value="<?php if (!empty($this->storeinfo[0]->company_name)){ echo stripslashes($this->storeinfo[0]->company_name); } ?>" />
				</div>
			</div>

			<div class="control-group">
				<label for="email" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('VENDER_EMAIL_TOOLTIP'), Text::_('VENDER_EMAIL'), '', '* '.Text::_('VENDER_EMAIL'));?>
				</label>
				<div class="controls">
					<!--
					<input type="email" name="email" id="email" class="inputbox required validate-email"   value="<?php /*if (!$app->isAdmin() && !empty($this->storeinfo)){  echo stripslashes($this->storeinfo[0]->store_email); } elseif (!$app->isAdmin() && !empty($user->email)){echo $user->email;}*/?>" />
					-->
					<input type="email" name="email" id="email"
						class="inputbox required validate-email"
						value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->store_email); } elseif (!empty($user->email)){echo $user->email;}?>" />
				</div>
			</div>

			<hr class="hr hr-condensed"/>

			<!-- ADDRESS -->
			<div class="control-group">
				<label for="address" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('VENDER_ADDRESS_TOOLTIP'), Text::_('VENDER_ADDRESS'), '','* '.Text::_('VENDER_ADDRESS'));?>
				</label>
				<div class="controls">
					<textarea   rows="3" name="address" id="address" class="inputbox required" ><?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->address);}?></textarea>
				</div>
			</div>

			<!--Land Mark-->
			<div class="control-group">
				<label for="land_mark" class="control-label "><?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY_TOOLTIP'), Text::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY'), '',Text::_('COM_QUICK2CART_VENDER_LAND_MARK_CITY'));?>
				</label>
				<div class="controls">
					<input type="text" name="land_mark" id="land_mark" class=" inputbox" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->land_mark);}?>"></input>
				</div>
			</div>


			<div class="control-group">
				<label for="pincode" class="control-label "><?php echo HTMLHelper::tooltip(Text::_('QTC_BILLIN_ZIP_DESC'), Text::_('QTC_BILLIN_ZIP'), '','* '.Text::_('QTC_BILLIN_ZIP'));?>
				</label>
				<div class="controls">
					<input type="text" name="pincode" id="pincode" class=" inputbox required" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->pincode);}?>"></input>
				</div>
			</div>

			<!--Country-->
			<div class="control-group">
				<label for="storecountry" class="control-label"><?php echo "* " . Text::_('QTC_BILLIN_COUNTRY')?></label>
				<div class="controls">
				<?php
					$country = $this->countrys;
					$options = array();
					$options[] = HTMLHelper::_('select.option', "", Text::_('QTC_BILLIN_SELECT_COUNTRY'));

					foreach ($country as $key=>$value)
					{
						$options[] = HTMLHelper::_('select.option', $value['id'], $value['country']);
					}

					if (!empty($this->storeinfo[0]->country))
					{
						$country = $this->storeinfo[0]->country;
					}
					else
					{
						$country = "";
					}

					echo $this->dropdown = HTMLHelper::_('select.genericlist',$options,'storecountry','required="required  " onchange=\'generateStoreState(id,"1")\' ','value','text', $country);
				?>

				</div>
			</div>

			<!--State-->
			<div class="control-group" >
				<label for="qtcstorestate" class=" control-label"><?php echo "* " .  Text::_('QTC_BILLIN_STATE')?></label>
				<div class="controls">
					<select name="qtcstorestate" id="qtcstorestate" class="" >
						<option selected="selected"><?php echo Text::_('QTC_BILLIN_SELECT_STATE')?></option>
					</select>
				</div>
				<div class="qtcClearBoth"></div>
			</div>

			<!--City-->
			<div class="control-group">
				<label for="city" class="control-label "><?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_VENDER_CITY_TOOLTIP'), Text::_('COM_QUICK2CART_VENDER_CITY'), '','* '.Text::_('COM_QUICK2CART_VENDER_CITY'));?>
				</label>
				<div class="controls">
					<input type="text" name="city" id="city" class=" inputbox required" value="<?php if (!empty($this->storeinfo)){ echo stripslashes($this->storeinfo[0]->city);}?>"></input>
				</div>
			</div>

			<hr class="hr hr-condensed"/>

			<!--avatar -->
			<div class="control-group">
				<label for="avatar" class="control-label">
					<?php echo HTMLHelper::tooltip(Text::_('VENDER_AVTAR_TOOLTIP'), Text::_('VENDER_AVTAR'), '', Text::_('VENDER_AVTAR'));?>
				</label>
				<div class="controls">
					<?php
					$width  = $qtc_params->get('storeavatar_width');
					$height = $qtc_params->get('storeavatar_height');

					if (!empty($this->storeinfo[0]->store_avatar))
					{
						?>
						<input type="file" name="avatar" id="avatar"
							placeholder="<?php echo Text::_('COM_QUICK2CART_IMAGE_MSG');?>"
							accept="image/*" />
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
						<input type="file" name="avatar" id="avatar"
							placeholder="<?php echo Text::_('COM_QUICK2CART_IMAGE_MSG');?>"
							class="" accept="image/*" />
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
			$showStoreTermsConditions  = $this->params->get('storeTermsConditons', 0);
			$storeTermsCondArtId = $this->params->get('storeTermsConditonsArtId', 0);
			$showStoreTersmAndCond = 0;

			if (!empty($showStoreTermsConditions) && !empty($storeTermsCondArtId))
			{
				$showStoreTersmAndCond = 1;
			}

			if ($showStoreTersmAndCond)
			{?>
				<div class="row">
					<div class="col-sm-12 col-sm-push-1">
						<div class="control-group ">
							<div class="checkbox">
								<?php
								$checked = '';

								if (!empty($this->storeinfo[0]->privacy_terms_condition))
								{
									$checked = 'checked';
								}

								$userPrivacyTermsConditionsLink = Uri::root()."index.php?option=com_content&view=article&id=".$storeTermsCondArtId."&tmpl=component";
							?>
							<label for="privacy_terms_condition">
								<input class="af-mb-10" type="checkbox" name="privacy_terms_condition" id="privacy_terms_condition"
								size="30" <?php echo $checked ?> required/>
								<?php  echo Text::_('COM_QUICK2CART_ACCEPT_STORE_TERMS_CONDITIONS_FIRST'); ?>
								<a rel="{handler: 'iframe', size: {x: 600, y: 600}}" href="<?php echo $userPrivacyTermsConditionsLink;?>"
								class="modal qtc_modal"><?php echo Text::_('COM_QUICK2CART_STORE_PRIVACY_POLICY');?></a>
								<?php  echo Text::_('COM_QUICK2CART_ACCEPT_STORE_TERMS_CONDITIONS_LAST'); ?>
							</label>
							</div>
						</div>
					</div>
				</div>
			<?php
			}
			?>


			<!--  for STORE header  -->
			<!--
			<div class="control-group">
				<label for="vendor_storeheader" class="control-label"><?php // echo HTMLHelper::tooltip(Text::_('VENDER_STORE_HEADER_TOOLTIP'), Text::_('STORE_HEADER'), '', Text::_('STORE_HEADER'));?></label>
				<div class="controls">
					<input type="file" name="storeheader"  placeholder="<?php //echo Text::_('COM_QUICK2CART_IMAGE_MSG');?>" accept="image/*">
					<span class="help-block"><?php //echo Text::_('QTC_HEADER_SIZE_MASSAGE');?></span>
					<?php /*
					if (!empty($this->storeinfo[0]->header) )
					{
					?>

						<div class="text-warning">
							<?php echo Text::_('COM_Q2C_EXISTING_IMAGE_MSG');?>
						</div>
						<div class="text-info">
							<?php echo Text::_('COM_Q2C_EXISTING_IMAGE');?>
						</div>
						<div>
							<?php
							//foreach($cdata['images'] as $img){
								echo "<img class='img-rounded com_qtc_header_img com_qtc_img_border' src='".Uri::root().$this->storeinfo[0]->header."' />";
							//}
							?>
						</div>
					<?php
					}*/
					?>
				</div>
			</div>
			-->

<!--     	/* @for now: On create store, hide def length,weight, tax ship details
			<hr class="hr hr-condensed"/>
-->

			<?php
			// Check for view override

			/*
			 * $taxshipPath = $comquick2cartHelper->getViewpath('vendor', 'taxship', "SITE", "SITE");
			ob_start();
			include($taxshipPath);
			$taxshipDetail = ob_get_contents();
			ob_end_clean();
			echo $taxshipDetail;
			*/
			?>

			<?php
			/*$paypalMode = " checked='checked' ";
			$otherMode = "";
			$display = " display:block;";
			$displaynone = " display:none;";
			$paypalEmailClass = " required ";*/

			// Means yes / 1
			/*if (!empty($this->storeinfo[0]->payment_mode))
			{
				$paypalMode = "";
				$otherMode = " checked='checked' ";
				$paypalEmailClass = '';
			}*/
			?>

			<!-- <hr class="hr hr-condensed"/> -->

			<!--PAYMENT mode paypal or other -->
			<!-- <div class="control-group">
				<label for="paymentMode" class="control-label">
					<?php //echo HTMLHelper::tooltip(Text::_('VENDER_PAYMENT_MODE_TOOLTIP'), Text::_('PAYMENT_MODE'), '','* '. Text::_('PAYMENT_MODE'));?>
				</label>
				<div class="controls">
					<input type="radio" class="inputbox" <?php //echo $paypalMode;?> value="0" id="paymentMode0" name="paymentMode" onclick="paymode(0)">
					<label class="radiobtn"  id="outofstockship0-lbl" for="paymentMode0"><?php //echo Text::_('QTC_PAYPAL');?></label>

					<input type="radio" class="inputbox" <?php //echo $otherMode;?> value="1" id="paymentMode1" name="paymentMode" onclick="paymode(1)">
					<label class="radiobtn" id="outofstockship1-lbl" for="paymentMode1"><?php //echo Text::_('QTC_OTHER');?></label>
				</div>
			</div> -->

			<!--for PAYPAL provide textbox for paypal email -->
			<?php //$pay_details= !empty($this->storeinfo[0]->pay_detail) ? $this->storeinfo[0]->pay_detail : ''; ?>

			<!-- <div class="control-group" id="paypalmodeDiv" style="<?php //echo (!empty($paypalMode)?$display:$displaynone); ?>" >
				<label for="paypalemail" class="control-label">
					<?php //echo HTMLHelper::tooltip(Text::_('VENDER_PAYPAL_EMAIL_TOOLTIP'), Text::_('PAYPAL_EMAIL'), '', '* '.Text::_('PAYPAL_EMAIL'));?>
				</label>
				<div class="controls">
					<input type="text" name="paypalemail" id="paypalemail" class="inputbox validate-email <?php //echo $paypalEmailClass ;?>"   value="<?php //echo !empty($paypalMode) ? $pay_details : ''; ?>" />
				</div>
			</div> -->

			<!--  IF OTHER PAYMENT METHOD -->
			<!-- <div class="control-group" id="othermodeDiv" style="<?php //echo (!empty($otherMode)?$display:$displaynone); ?>" >
				<label for="otherPayMethod" class="control-label">
					<?php //echo HTMLHelper::tooltip(Text::_('VENDER_OTHER_PAY_METHOD_TOOLTIP'), Text::_('OTHER_PAY_METHOD'), '','* '. Text::_('OTHER_PAY_METHOD'));?>
				</label>
				<div class="controls">
					<textarea   rows="3" name="otherPayMethod" id="otherPayMethod" class="inputbox" ><?php //if (!empty($this->storeinfo[0]->payment_mode)){ echo stripslashes($this->storeinfo[0]->pay_detail);}?></textarea>
				</div>
			</div> -->

			<?php
			// Trigger OnBeforeCreateStore
			if (!empty($this->OnBeforeCreateStore))
			{
				echo $this->OnBeforeCreateStore;
			}

			// Store limit msg
			$storeLimitPerUser = $qtc_params->get('storeLimitPerUser');

			if (!$app->isClient('administrator') && !empty($storeLimitPerUser))
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
					onclick="qtcbuttonAction('vendor.saveAndClose');"/>
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
