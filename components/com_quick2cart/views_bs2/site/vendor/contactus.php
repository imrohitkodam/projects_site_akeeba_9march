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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('behavior.framework');
}

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator')

 ?>
 <style>
.contact-us-textarea {
    height:250px;
}

</style>

 <script type="text/javascript">

 function submitAction(action)
 {
	 var validateflag = document.formvalidator.isValid(document.adminForm);
	 console.log("validateflag" +validateflag);
 }
</script>
<div class='<?php echo Q2C_WRAPPER_CLASS; ?>' >
	<form  name="adminForm" id="adminForm" class="form-validate" method="post">
	<div class="row-fluid">
			<legend><?php	echo Text::_( "QTC_CONTACT_TO_PRODUCT_OWNER"); ?> </legend>
				<div class="well span12">
							<div class="row-fluid">

									<label><?php echo  Text::_('QTC_ENTER_EMAIL') ?></label>
									<div class="input-prepend">
										<span class="add-on"><i class="icon-envelope"></i></span>
										<input type="text" name="cust_email" id="inputIcon" class="span2 required  validate-email" style="width:233px" placeholder="<?php echo  Text::_('QTC_CONTCT_ENTER_EMAIL') ?>">
									</div>
							</div>
							<div class="row-fluid">

									<label><?php echo  Text::_('QTC_EMAIL_BODY') ?></label>
									<textarea name="message" id="message" class="input-xlarge span12 required" rows="10"></textarea>


							</div>
							<div class="row-fluid">
							<button type="submit" class="btn btn-primary pull-right"><i class="icon-envelope icon22-white22"></i><?php echo  Text::_('QTC_SEND') ?> </button>
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
