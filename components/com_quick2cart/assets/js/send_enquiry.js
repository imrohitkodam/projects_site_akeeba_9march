/*
 * @version    SVN: <svn_id>
 * @package    Quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2019 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

var enquiry = {

	validateEmail: function (email) {
		var emailVal = email.val();
		var mailformat = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
		if (!emailVal.match(mailformat)) {
			alert(Joomla.Text._('QTC_INVALID_EMAIL_ADDRESS'));
			return false;
		} else {
			return true;
		}
	},

	validateMobile: function (contact) {
		var contactVal = contact.val();
		var mobileFormat = /^[0-9]{10}$/;
		if (!contactVal.match(mobileFormat)) {
			alert(Joomla.Text._('QTC_INVALID_CONTACT_NO'));
			return false;
		}
		else {
			return true;
		}

	},

	checkCaptcha: function (contact, email) {
		contactVal = contact.val();
		emailVal = email.val();

		var emailValidation = this.validateEmail(email);
		if (emailValidation == false) {
			return false;
		}
		var contactValidation = this.validateMobile(contact);
		if (contactValidation == false) {
			return false;
		}

		var captcha = techjoomla.jQuery('#g-recaptcha-response').val();

		if (captcha === "") {
			alert(Joomla.Text._('COM_QUICK2CART_BLANK_CAPTCHA_MSG'));
			return false;
		}

		if (!techjoomla.jQuery('.captchaSection').length)
		{
			return true;
		}

		var captchasend = 'g-recaptcha-response='.concat(captcha);

		var valid = 0;
		techjoomla.jQuery.ajax({
			url: baseUrl + "/index.php?option=com_quick2cart&task=vendor.isCaptchaCorrect",
			type: "POST",
			dataType: "json",
			data: captchasend,
			/*async:false,*/
			success: function (msg) {
				if (msg == 1) {
					valid = 1;
					techjoomla.jQuery.removeAttr('onsubmit'); // prevent endless loop
					techjoomla.jQuery('#adminForm').submit();
				}
				else {
					alert(Joomla.Text._('COM_QUICK2CART_CAPTCHA_ERROR_MSG'));
					return false;
				}
			},
			error: function () {
				alert(Joomla.Text._('COM_QUICK2CART_CAPTCHA_ERROR_MSG'));
				return false;
			}
		});
		return false;
	}

};
