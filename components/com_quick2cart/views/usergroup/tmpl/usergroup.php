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
use Joomla\CMS\Language\Text;

Text::script('COM_QUICK2CART_PROMOTION_CONDITION_SELECT_USER_GROUP_ALERT', true);

?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?> promotion_usergroup container">
	<div class="row">
		<div class="col-sm-12 col-md-12">
			<div class="form-inline">
				<form action="" method="post" enctype="multipart/form-data" name="catForm" id="catForm-form">
					<?php
					if (empty($this->items))
					{
					?>
						<div class="clearfix">&nbsp;</div>
						<div class="alert alert-warning af-mt-10"><?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?></div>
					<?php
					}
					else
					{
						?>
						<div class="alert alert-info af-mt-10">
							<div class="center">
								<h5><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_SELECT_USER_GROUPS");?></h5>
							</div>
						</div>
						<div class="form-group row qtc-categorys">
							<?php
							foreach ($this->items as $item)
							{
								?>
								<div class="qtc-category form-check col-md-4">
									<label for="qtc-selected-group<?php echo $item->id;?>">
										<input
											type="checkbox"
											id="qtc-selected-group<?php echo $item->id;?>"
											value="<?php echo $item->id;?>"
											class="form-check-input" />
										<?php echo "&nbsp;".$item->title;?>
									</label>
								</div>
							<?php
							}?>
						</div>
						<div class="row">
							<div class="col af-text-center">
								<a class="btn btn-large btn-success af-mb-15 submitGroup">
									<?php echo Text::_('QTC_APPLY');?>
								</a>
							</div>
						</div>
						<?php
					}?>
				</form>
			</div>
		</div>
	</div>
</div>
<script>
	techjoomla.jQuery(document).ready(function ()
	{
		var id = window.parent.selectedConditionCategory;
		var selectedGrp = window.parent.document.getElementById('rule_conditions_'+id+'_condition_attribute_value').value;
		var selectedGrpArray = selectedGrp.split(",");

		for (i = 0; i < selectedGrpArray.length; i++)
		{
			techjoomla.jQuery("#qtc-selected-group"+selectedGrpArray[i]).prop('checked', true);
		}

		jQuery(document).on('click', '.submitGroup', function(e) {
			if (!techjoomla.jQuery('.qtc-categorys :checked').length)
			{
				alert(Joomla.JText._("COM_QUICK2CART_PROMOTION_CONDITION_SELECT_USER_GROUP_ALERT"));

				return false;
			}

			var flag = 0;

			// rule_conditions_60_condition_attribute_value
			var selectedGrp ='';
			techjoomla.jQuery('.qtc-categorys :checked').each(function() {

				if (Number(flag) != 0)
				{
					selectedGrp += ",";
				}

				selectedGrp += techjoomla.jQuery(this).val();
				flag++;
			});

			window.parent.document.getElementById('rule_conditions_'+id+'_condition_attribute_value').value = selectedGrp;
			window.parent.jQuery('#promotionConditionOptionUserGroup .modal-header button').click();
		});
	});
</script>
