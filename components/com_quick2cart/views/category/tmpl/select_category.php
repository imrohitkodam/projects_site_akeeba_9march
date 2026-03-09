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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

Text::script('COM_QUICK2CART_PROMOTION_CONDITION_SELECT_CATEGORY_ALERT', true);

$this->items = HTMLHelper::_('category.options', 'com_quick2cart', array('filter.published' => array(1)));
$id          = Factory::getApplication()->input->get('id', "0", 'INT');
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?> promotion_category container">
	<div class="row">
		<div class="col-sm-12">
			<div class="">
				<form action="" method="post" enctype="multipart/form-data" name="catForm" id="catForm-form">
					<div class="row-fluid q2c-wrapper">
						<div class="form-group qtc-categorys">
							<?php
							if (empty($this->items))
							{
							?>
								<div class="clearfix">&nbsp;</div>
								<div class="alert alert-warning af-mt-10">
									<?php echo Text::_('COM_QUICK2CART_NO_MATCHING_RESULTS'); ?>
								</div>
							<?php
							}
							else
							{
								?>
								<div class="alert alert-info af-mt-10">
									<div class="center">
										<h3><?php echo Text::_("COM_QUICK2CART_PROMOTION_CONDITION_SELECT_CATEGORYS");?></h3>
									</div>
								</div>
								<?php
								foreach ($this->items as $item)
								{
									?>
									<div class="qtc-category form-check">
										<label for="qtc-selected-category<?php echo $item->value;?>" class="form-check-label">
											<input
											type="checkbox"
											id="qtc-selected-category<?php echo $item->value;?>"
											value="<?php echo $item->value;?>"
											class="form-check-input"/>
											<?php echo "&nbsp;".$item->text;?>
										</label>
									</div>
									<hr class="hr-condensed">
								<?php
								}?>
								<div class="row">
									<div class="col af-text-center">
										<a class="btn btn-large btn-success af-mb-15 submitCat">
											<?php echo Text::_('QTC_APPLY'); ?>
										</a>
									</div>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	techjoomla.jQuery(document).ready(function ()
	{		
		var id = window.parent.selectedConditionCategory;
		var selectedCats = window.parent.document.getElementById('rule_conditions_'+id+'_condition_attribute_value').value;

		var selectedCatsArray = selectedCats.split(",");

		for (i = 0; i < selectedCatsArray.length; i++)
		{
			techjoomla.jQuery("#qtc-selected-category"+selectedCatsArray[i]).prop('checked', true);
		}

		jQuery(document).on('click', '.submitCat', function(e) {
			if (!techjoomla.jQuery('.qtc-categorys :checked').length)
			{
				alert(Joomla.JText._("COM_QUICK2CART_PROMOTION_CONDITION_SELECT_CATEGORY_ALERT"));

				return false;
			}

			var flag = 0;
			var selectedCat ='';

			techjoomla.jQuery('.qtc-categorys :checked').each(function() {
				
				if (Number(flag) != 0)
				{
					selectedCat += ",";
				}

				selectedCat += techjoomla.jQuery(this).val();

				flag++;
			});

			window.parent.document.getElementById('rule_conditions_'+id+'_condition_attribute_value').value = selectedCat;
		
			window.parent.jQuery('#promotionConditionOptionCategory .modal-header button').click();
		});
	});
</script>
