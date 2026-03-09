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

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css');

$js="
function fill_me(el)
{
	jQuery('#payee_name').val(jQuery('#payee_options option:selected').text());
	jQuery('#user_id').val(jQuery('#payee_options option:selected').val());
	jQuery('#payment_amount').val(user_amount_map[jQuery('#payee_options option:selected').val()]);
	jQuery('#paypal_email').val(user_email_map[jQuery('#payee_options option:selected').val()]);
}

var user_amount_map=new Array();";

foreach($this->getPayoutFormData as $payout)
{
	// TOTATL AMOUT = totl prod price - paid payout sum
	$amt = (float)$payout->total_amount - $payout->fee;
	$js .= "user_amount_map[".$payout->user_id."]=".$amt.";";
}

$js .= "var user_email_map=new Array();";

foreach ($this->getPayoutFormData as $payout)
{
	$js .= "user_email_map[".$payout->user_id."]='".$payout->email."';";
}

$document->addScriptDeclaration($js);
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'payout.cancel')
		{
			Joomla.submitform(task, document.getElementById('payout-form'));
		}
		else
		{
			if (task != 'payout.cancel' && document.formvalidator.isValid(document.getElementById('payout-form')))
			{
				Joomla.submitform(task, document.getElementById('payout-form'));
			}
			else
			{
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>

<form action="" method="post" enctype="multipart/form-data" name="adminForm" id="payout-form" class="form-validate">
	<div class="form-horizontal">
		<div class="row-fluid">
			<div class="span12 form-horizontal">
				<fieldset class="adminform">
					<div class="form-group row">
						<label class="form-label col-md-4" for="payee_name" title="<?php echo Text::_('COM_QUICK2CART_PAYEE_NAME_TOOLTIP');?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PAYEE_NAME_TOOLTIP'), '',  Text::_('COM_QUICK2CART_PAYEE_NAME'). ' *');?>
						</label>
						<div class="col-md-8">
							<div class="row">
								<div class="col-sm-3">
									<input
										type="text"
										id="payee_name"
										name="payee_name"
										class="required form-control"
										maxlength="250"
										placeholder="<?php echo Text::_('COM_QUICK2CART_PAYEE_NAME');?>"
										value="<?php if(isset($this->item->payee_name)) echo $this->item->payee_name;?>" />
								</div>
								<div class="col-sm-3">
									<?php
										echo HTMLHelper::_('select.genericlist', $this->payee_options, "payee_options", 'class="col-sm-3 form-select" size="1"
										onchange="fill_me(this);" name="payee_options"', "value", "text", '');
									?>
								</div>
								<div class="col-sm-3">
									<i><?php echo Text::_('COM_QUICK2CART_PAYOUT_SEL_PAYEENAME');?></i>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group row">
						<label class="form-label col-md-4" for="user_id" title="<?php echo Text::_('COM_QUICK2CART_USER_ID_TOOLTIP');?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_USER_ID_TOOLTIP'), '',  Text::_('COM_QUICK2CART_USER_ID'). ' *');?>
						</label>
						<div class="col-md-8">
							<input type="text" id="user_id" name="user_id"
								class="required validate-numeric form-control" maxlength="250"
								placeholder="<?php echo Text::_('COM_QUICK2CART_USER_ID');?>"
								value="<?php if(isset($this->item->user_id)) echo $this->item->user_id;?>" />
						</div>
					</div>
					<div class="form-group row">
						<label class="form-label col-md-4" for="paypal_email" title="<?php echo Text::_('COM_QUICK2CART_PAYPAL_EMAIL_TOOLTIP');?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PAYPAL_EMAIL_TOOLTIP'), '',  Text::_('COM_QUICK2CART_PAYPAL_EMAIL'). ' *');?>
						</label>
						<div class="col-md-8">
							<input type="text" id="paypal_email" name="paypal_email"
							class="required validate-email form-control" maxlength="250"
								placeholder="<?php echo Text::_('COM_QUICK2CART_PAYPAL_EMAIL');?>"
								value="<?php if(isset($this->item->email_id)) echo $this->item->email_id;?>" />
						</div>
					</div>
					<div class="form-group row">
						<label class="form-label col-md-4" for="transaction_id" title="<?php echo Text::_('COM_QUICK2CART_TRANSACTION_ID_TOOLTIP');?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_TRANSACTION_ID_TOOLTIP'), '',  Text::_('COM_QUICK2CART_TRANSACTION_ID'). ' *');?>
						</label>
						<div class="col-md-8">
							<input type="text" id="transaction_id" name="transaction_id"
								class="required form-control" maxlength="20"
								placeholder="<?php echo Text::_('COM_QUICK2CART_TRANSACTION_ID');?>"
								value="<?php if(isset($this->item->transaction_id)) echo $this->item->transaction_id;?>" />
						</div>
					</div>
					<div class="form-group row">
						<label class="form-label col-md-4" for="payout_date" title="<?php echo Text::_('COM_QUICK2CART_PAYOUT_DATE_TOOLTIP');?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PAYOUT_DATE_TOOLTIP'), '',  Text::_('COM_QUICK2CART_PAYOUT_DATE'). ' *');?>
						</label>
						<div class="col-md-8">
							<?php
							$date = (isset($this->item->date)) ? $this->item->date : date('');
							echo HTMLHelper::_('calendar', date('Y-m-d'), 'payout_date', 'payout_date', '%Y-%m-%d'); ?>
						</div>
					</div>
					<div class="form-group row">
						<label class="form-label col-md-4" for="payment_amount" title="<?php echo Text::_('COM_QUICK2CART_PAYOUT_AMOUNT_TOOLTIP');?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PAYOUT_AMOUNT_TOOLTIP'), '',  Text::_('COM_QUICK2CART_PAYOUT_AMOUNT'). ' *');?>
						</label>
						<div class="col-md-8">
							<div class="input-append">
								<input type="text" id="payment_amount" name="payment_amount"
									class="required validate-numeric form-control" maxlength="11"
									placeholder="<?php echo Text::_('COM_QUICK2CART_PAYOUT_AMOUNT');?>"
									value="<?php if(isset($this->item->amount)) echo $this->item->amount;?>" />
							</div>
						</div>
					</div>
					<div class="form-group row">
						<label class="form-label col-md-4" for="payment_comment" title="<?php echo Text::_('COM_QUICK2CART_PAYOUT_COMMENT_TOOLTIP');?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PAYOUT_COMMENT_TOOLTIP'), '',  Text::_('COM_QUICK2CART_PAYOUT_COMMENT'). ' *');?>
						</label>
						<div class="col-md-8">
							<textarea id="payment_comment" class="form-control bill inputbox required form-control"
								name="payment_comment" maxlength="250" rows="3"
								title="Enter Address" aria-required="true" required="required"
								style="background-color: transparent; white-space: pre-wrap; z-index: auto; position: relative; line-height: 20px; font-size: 14px; -webkit-transition: none; overflow: auto;"
								spellcheck="false"><?php if(isset($this->item->comment)) echo $this->item->comment;?></textarea>
						</div>
					</div>
					<?php
					$status1 = $status2 = '';

					if (isset($this->item->status))
					{
						if ($this->item->status)
						{
							$status1 = 'checked';
						}
						else
						{
							$status2 = 'checked';
						}
					}
					else
					{
						$status2 = 'checked';
					}
					?>
					<div class="form-group row">
						<label class="form-label col-md-4" for="status" title="<?php echo Text::_('COM_QUICK2CART_STATUS_TOOLTIP')?>">
							<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_STATUS_TOOLTIP'), '',  Text::_('COM_QUICK2CART_STATUS'). ' *');?>
						</label>
						<div class="col-md-8">
							<label class="radio inline">
								<input type="radio" name="status" id="status1" value="1" <?php echo $status1;?> />
								<?php echo Text::_('COM_QUICK2CART_PAID');?>
							</label>
							<label class="radio inline">
								<input type="radio" name="status" id="status2" value="0" <?php echo $status2;?> />
								<?php echo Text::_('COM_QUICK2CART_NOT_PAID');?>
							</label>
						</div>
					</div>
					<input type="hidden" name="id" value="<?php echo $this->item->id;?>" />
					<?php echo HTMLHelper::_( 'form.token' ); ?>
				</fieldset>
			</div>
		</div>
		<input type="hidden" name="task" value="" />
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
