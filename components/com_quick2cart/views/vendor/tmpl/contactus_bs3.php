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

use Joomla\CMS\Captcha\Captcha;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('behavior.framework');
}

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');

$document = Factory::getDocument();
HTMLHelper::_('script',Uri::root() . 'components/com_quick2cart/assets/js/send_enquiry.js');
Factory::getDocument()->addScriptDeclaration('
	var enquiryformvalidation = {
		submitAction: function (action)
		{
			var validateflag = document.formvalidator.isValid(document.getElementById("adminForm"));

			if (validateflag) {
				var contact = techjoomla.jQuery("#contact_no");
				var email   = techjoomla.jQuery("#cust_email");

				return enquiry.checkCaptcha(contact,email);
			}
			else {
			return false;
			}
		}
	}
');
?>
 <style>
.contact-us-textarea {
	height:250px;
}
</style>

 <script>
var baseUrl = Joomla.getOptions('system.paths').base;
 </script>
<div class='<?php echo Q2C_WRAPPER_CLASS; ?> container-fluid' >
	<form  name="adminForm" id="adminForm" class="form-validate" method="post">
		<div class="container-fluid">
			<legend><?php echo Text::_("QTC_CONTACT_TO_PRODUCT_OWNER"); ?> </legend>
			<div class="row form-group">
				<label for="cust_email" class="col-sm-3 col-xs-12 form-label">
					<?php echo  Text::_('QTC_ENTER_EMAIL') ?>
				</label>
				<div class="col-sm-7 col-xs-12">
					<div class="input-group">
						<input
							type="text"
							name="cust_email"
							id="cust_email"
							required="true"
							class="form-control required  validate-email q2c-addon-button"
							placeholder="<?php echo  Text::_('QTC_CONTCT_ENTER_EMAIL') ?>">
						<div class="q2c-addon-button input-group-text"><i class="fa fa-envelope"></i></div>
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label for="contact_no" class="col-sm-3 col-xs-12 form-label">
					<?php echo  Text::_('QTC_ENTER_CONTACT_NUMBER') ?>
				</label>
				<div class="col-sm-7 col-xs-12">
					<div class="input-group">
						<input
							type="text"
							name="contact_no"
							id="contact_no"
							required="true"
							class="form-control required q2c-addon-button tel"
							placeholder="<?php echo  Text::_('QTC_CONTCT_ENTER_CONTACT_NUMBER') ?>">
						<div class="input-group-text q2c-addon-button"><i class="fa fa-phone"></i></div>
					</div>
				</div>
			</div>
			<div class="row form-group">
				<label for="message" class="col-sm-3 col-xs-12 form-label">
					<?php echo  Text::_('QTC_EMAIL_BODY') ?>
				</label>
				<div class="col-sm-7 col-xs-12">
					<textarea name="message" id="message" required="true" class="form-control contact-us-textarea  required col-sm-9 col-xs-12" rows="15"></textarea>
				</div>
				<div class="clearfix"></div>
			</div>

			<?php
				if (Factory::getApplication()->get('captcha'))
				{ ?>

					<div class="row form-group captchaSection">
						<label class="col-sm-3 col-xs-12 form-label">
							<?php echo Text::_('COM_QUICK2CART_ENTER_CAPTCHA');?>
						</label>
						<div class="col-sm-7 col-xs-12">
							<?php echo Factory::getApplication()->get('captcha') ? Captcha::getInstance(Factory::getApplication()->get('captcha'))->display('recaptcha', 'recaptcha', 'g-recaptcha') : ''; ?>
						</div>
					</div>
				<?php
				}
			?>
			<div class="row form-group">
				<label class="col-sm-3 col-xs-12 form-label">&nbsp;</label>
				<div class="col-sm-7 col-xs-12">
					<div class="input-group">
						<button type="submit" class="btn btn-primary" id="sendInquiryBtn" onclick="return enquiryformvalidation.submitAction()"><i class="icon-envelope <?php  echo Q2C_ICON_WHITECOLOR;?>"> &nbsp;</i><?php echo  Text::_('QTC_SEND') ?></button>
					</div>
				</div>
			</div>
		</div>

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="vendor" />
		<input type="hidden" name="task" value="vendor.contactUsEmail" />
		<input type="hidden" name="store_id" value="<?php echo $this->store_id;?>" />
		<input type="hidden" name="item_id" value="<?php echo $this->item_id;?>" />
	</form>
</div>
