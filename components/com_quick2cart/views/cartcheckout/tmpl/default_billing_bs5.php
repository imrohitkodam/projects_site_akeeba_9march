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
?>
<div id="qtc_billing_alert_on_order_placed"></div>
<div id="qtc_ckout_billing-info" class="qtc_billing_page com_quick2cart-checkout-steps q2ctablewrapper" style="<?php echo (isset($showBillShipTab) && $showBillShipTab ==0 ? "display:none;" : '')?>">
	<div id="qtc_mainwrapper" class="row">
		<div id="q2c_billing" class="<?php echo ($this->params->get( 'shipping' )==1)?' col-lg-6 col-md-6 col-sm-6 col-xs-12':' col-lg-12 col-md-12 col-sm-12 col-xs-12';?> qtc_innerwrapper">
			<legend id="qtc_billin" >
				<?php echo Text::_('QTC_BILLIN')?>&nbsp;
			</legend>
			<div class="form-group">
				<label  for="fnam" class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_FNAM'). ' *'; ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<input
						id="fnam"
						class=" bill required validate-name form-control"
						type="text"
						value="<?php echo (isset($userbill->firstname))?$userbill->firstname:''; ?>"
						maxlength="250"
						name="bill[fnam]"
						title="<?php echo Text::_('QTC_BILLIN_FNAM_DESC')?>">
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<?php
			if ($this->params->get('qtc_middlenmae')==1)
			{
				?>
				<div class="form-group">
					<label  for="mnam" class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_BILLIN_MNAM'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="mnam"
							class=" bill required validate-name form-control"
							type="text"
							value="<?php echo (isset($userbill->middlename))?$userbill->middlename:''; ?>"
							maxlength="250"
							name="bill[mnam]"
							title="<?php echo Text::_('QTC_BILLIN_MNAM_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
			<?php 
			} ?>
			<div class="form-group">
				<label for="lnam" class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_LNAM'). ' *'; ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<input
						id="lnam"
						class=" bill required validate-name form-control"
						type="text"
						value="<?php echo (isset($userbill->lastname))?$userbill->lastname:''; ?>"
						maxlength="250"
						name="bill[lnam]"
						title="<?php echo Text::_('QTC_BILLIN_LNAM_DESC')?>">
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group">
				<label for="email1" class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_EMAIL'). ' *'; ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<input
						id="email1"
						class=" bill required validate-email form-control"
						type="text"
						value="<?php echo (isset($userbill->user_email))?$userbill->user_email:'' ; ?>"
						maxlength="250"
						name="bill[email1]"
						onblur=" chkbillmailregistered(this.value);"
						title="<?php echo Text::_('QTC_BILLIN_EMAIL_DESC')?>">
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group" id="qtc_billmail_msg_div" style="display:none;">
				<span class="help-inline qtc_removeBottomMargin" id="billmail_msg"></span>
			</div>
			<?php
			$enable_bill_vat = $this->params->get('enable_bill_vat');
			if ($enable_bill_vat=="1")
			{
				?>
				<div class="form-group">
					<label for="vat_num"  class="col-xs-12 col-lg-3 control-label">
						<?php echo  Text::_('QTC_BILLIN_VAT_NUM')?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="vat_num"
							class=" bill validate-integer form-control"
							type="text"
							value="<?php echo (isset($userbill->vat_number))?$userbill->vat_number:''; ?>"
							name="bill[vat_num]"
							title="<?php echo Text::_('QTC_BILLIN_VAT_NUM_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
			<?php
			} ?>
			<div class="form-group">
				<label for="phon"  class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_PHON'). ' *'; ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<input
						id="phon"
						class="bill required validate-integer form-control"
						type="text"
						onkeyup="checkforalpha(this,43,<?php echo $entered_numerics; ?>);"
						maxlength="50"
						value="<?php echo (isset($userbill->phone))?$userbill->phone:''; ?>"
						name="bill[phon]"
						title="<?php echo Text::_('QTC_BILLIN_PHON_DESC')?>">
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group">
				<label for="addr" class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_ADDR'). ' *'; ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<textarea id="addr" class="form-control bill required" name="bill[addr]"  maxlength="250" rows="3" title="<?php echo Text::_('QTC_BILLIN_ADDR_DESC')?>" ><?php echo (isset($userbill->address))?$userbill->address:''; ?></textarea>
					<p class="help-block"><?php echo Text::_('QTC_BILLIN_ADDR_HELP')?> </p>
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group">
				<label for="land_mark"  class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_LAND_MARK')?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<input
						id="land_mark"
						class=" bill form-control"
						type="text"
						value="<?php echo (isset($userbill->land_mark))?$userbill->land_mark:''; ?>"
						onblur=""
						maxlength="50"
						name="bill[land_mark]"
						title="<?php echo Text::_('QTC_BILLIN_LAND_MARK_DESC')?>">
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group">
				<label for="zip"  class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_ZIP'). ' *'; ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<input
						id="zip"
						class=" bill required form-control"
						type="text"
						value="<?php echo (isset($userbill->zipcode))?$userbill->zipcode:''; ?>"
						onblur=""
						maxlength="20"
						name="bill[zip]"
						title="<?php echo Text::_('QTC_BILLIN_ZIP_DESC')?>">
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group">
				<label for="country" class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_COUNTRY') . ' *';?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label" >
					<?php
					$country = $this->country;
					$default = (isset($userbill->country_code))?$userbill->country_code: $this->params->get('set_default_country','');
					$options = array();
					$options[] = HTMLHelper::_('select.option', "", Text::_('QTC_BILLIN_SELECT_COUNTRY'));

					foreach ($country as $key => $value)
					{
						$options[] = HTMLHelper::_('select.option', $value['id'], $value['country']);
					}

					echo HTMLHelper::_('select.genericlist',$options,'bill[country]','class="bill form-select" data-chosen="qtc"  required="required"  aria-invalid="false"  onchange=\'generateState(id,"")\' ','value','text',$default,'country');
					?>
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group" >
				<label for="state" class="col-xs-12 col-lg-3 control-label">
					<?php echo  Text::_('QTC_BILLIN_STATE')?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label" id="qtcBillState">
					<select name="bill[state]" id="state" class="bill form-select" data-chosen="qtc">
						<option selected="selected" value="" ><?php echo Text::_('QTC_BILLIN_SELECT_STATE')?></option>
					</select>
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group">
				<label for="city" class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_('QTC_BILLIN_CITY'). ' *'; ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<input
						id="city"
						class="bill required validate-name form-control"
						type="text"
						value="<?php echo (isset($userbill->city))?$userbill->city:''; ?>"
						maxlength="250"
						name="bill[city]"
						title="<?php echo Text::_('QTC_BILLIN_CITY_DESC')?>">
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<div class="form-group ">
				<label for="" class="col-xs-12 col-lg-3 control-label">
					<?php echo Text::_( 'QTC_USER_COMMENT' ); ?>
				</label>
				<div class="col-xs-12 col-lg-9 control-label">
					<textarea id="comment" class="form-control" name="comment"  rows="3" maxlength="135" ></textarea>
				</div>
				<div class="qtcClearBoth"></div>
			</div>
			<?php
			if($this->params->get( 'shipping' ) == '1' )
			{
				?>
				<div class="checkbox">
					<label class="">
						<input
							type="checkbox"
							id = "ship_chk"
							name = "ship_chk"
							value="1"
							size= "10"
							onchange="show_ship()"/>
						<?php echo Text::_('QTC_SHIP_SAME')?>
					</label>
				</div>
			<?php 
			}?>
		</div>

		<?php
		if ( $this->params->get('shipping') == '1' )
		{
			?>
			<div id="qtc_ship1" class="broadcast-expands  col-lg-6 col-md-6 col-sm-6 col-xs-12 qtc_innerwrapper ">
				<legend id="qtc_ship" class="ship_tr">
					<?php echo Text::_('QTC_SHIPIN')?>&nbsp;
				</legend>
				<div class=" form-group ship_tr">
					<label  for="ship_fnam" class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_FNAM'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="ship_fnam"
							class="required validate-name form-control"
							type="text"
							value="<?php echo (isset($usership->firstname))?$usership->firstname:''; ?>"
							maxlength="250"
							name="ship[fnam]"
							title="<?php echo Text::_('QTC_SHIPIN_FNAM_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<?php
				if ($this->params->get('qtc_middlenmae')==1)
				{
				?>
					<div class="form-group ship_tr">
						<label for="ship_mnam" class="col-xs-12 col-lg-3 control-label">
							<?php echo Text::_('QTC_SHIPIN_MNAM'). ' *'; ?>
						</label>
						<div class="col-xs-12 col-lg-9 control-label">
							<input
								id="ship_mnam"
								class="required validate-name form-control"
								type="text"
								value="<?php echo (isset($usership->middlename))?$usership->middlename:''; ?>"
								maxlength="250"
								name="ship[mnam]"
								title="<?php echo Text::_('QTC_SHIPIN_MNAM_DESC')?>">
						</div>
						<div class="qtcClearBoth"></div>
					</div>
				<?php 
				} ?>

				<div class="form-group ship_tr">
					<label for="ship_lnam" class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_LNAM'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="ship_lnam"
							class="required validate-name form-control"
							type="text"
							value="<?php echo (isset($usership->lastname))?$usership->lastname:''; ?>"
							maxlength="250"
							name="ship[lnam]"
							title="<?php echo Text::_('QTC_SHIPIN_LNAM_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr">
					<label for="ship_email1" class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_EMAIL'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="ship_email1"
							class="required validate-email form-control"
							type="text"
							value="<?php echo (isset($usership->user_email))?$usership->user_email:''; ?>"
							maxlength="250"
							name="ship[email1]"
							title="<?php echo Text::_('QTC_SHIPIN_EMAIL_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr">
					<label for="ship_phon"  class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_PHON'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="ship_phon"
							class="required validate-integer form-control"
							maxlength="50"
							type="text"
							onkeyup="checkforalpha(this,43,<?php echo $entered_numerics; ?>);"
							value="<?php echo (isset($usership->phone))?$usership->phone:''; ?>"
							maxlength="50"
							name="ship[phon]"
							title="<?php echo Text::_('QTC_SHIPIN_PHON_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr">
					<label for="ship_addr"  class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_ADDR'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<textarea id="ship_addr" class="form-control bill required " name="ship[addr]"  maxlength="250" rows="3" title="<?php echo Text::_('QTC_SHIPIN_ADDR_DESC')?>" ><?php echo (isset($usership->address))?$usership->address:''; ?></textarea>
						<p class="help-block"><?php echo Text::_('QTC_SHIPIN_ADDR_HELP')?> </p>
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr">
					<label for="ship_land_mark" class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_BILLIN_LAND_MARK')?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="ship_land_mark"
							class="form-control"
							type="text"
							value="<?php echo (isset($usership->land_mark))?$usership->land_mark:''; ?>"
							maxlength="50"  name="ship[land_mark]"
							title="<?php echo Text::_('QTC_BILLIN_LAND_MARK_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr">
					<label for="ship_zip"  class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_ZIP'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="ship_zip"
							class="required form-control"
							type="text"
							value="<?php echo (isset($usership->zipcode))?$usership->zipcode:''; ?>"
							maxlength="20"
							name="ship[zip]"
							title="<?php echo Text::_('QTC_SHIPIN_ZIP_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr">
					<label for="ship_country"  class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_COUNTRY'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label" id='qtcShipCountry'>
						<?php
							$country         = $this->country;
							$default_country = (isset($usership->country_code)) ? $usership->country_code : $this->params->get('set_default_country','');
							$options         = array();
							$options[]       = HTMLHelper::_('select.option', "", Text::_('QTC_SHIPIN_SELECT_COUNTRY'));

							foreach ($country as $key=>$value)
							{
								$options[] = HTMLHelper::_('select.option', $value['id'], $value['country']);
							}

							echo HTMLHelper::_('select.genericlist',$options,'ship[country]','class="form-select" required="required" data-chosen="qtc" aria-invalid="false"  onchange=\'generateState(id,"")\' ','value','text',$default_country,'ship_country');
						?>
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr" >
					<label for="ship_state" class="col-xs-12 col-lg-3 control-label">
						<?php echo Text::_('QTC_SHIPIN_STATE')?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label" id="qtcShipState">
						<select name="ship[state]" id="ship_state"  class="form-select" data-chosen="qtc">
							<option value=""><?php echo Text::_('QTC_SHIPIN_SELECT_STATE')?></option>
						</select>
					</div>
					<div class="qtcClearBoth"></div>
				</div>
				<div class="form-group ship_tr">
					<label for="ship_city" class="col-xs-12 col-lg-3 control-label" >
						<?php echo Text::_('QTC_SHIPIN_CITY'). ' *'; ?>
					</label>
					<div class="col-xs-12 col-lg-9 control-label">
						<input
							id="ship_city"
							class="required validate-name form-control"
							type="text"
							value="<?php echo (isset($usership->city))?$usership->city:''; ?>"
							maxlength="250"
							name="ship[city]"
							title="<?php echo Text::_('QTC_SHIPIN_CITY_DESC')?>">
					</div>
					<div class="qtcClearBoth"></div>
				</div>
			</div>
		<?php  
		}?>
	</div>
	<?php $shipval = $taxval;?>
</div>
