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
$storeHelper = new storeHelper;
?>


<?php
// Taxprofile and ship profile
$isShippingEnabled = $this->params->get('shipping', 0);
$isTaxationEnabled = $this->params->get('enableTaxtion', 0);

?>

<!--  Length and weight unit  -->
<div class="form-group">
	<label for="qtc_length_class" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_LENGTH_VENDOR_UNIT_DESC'), Text::_('COM_QUICK2CART_LENGTH_VENDOR_UNIT'), '', Text::_('COM_QUICK2CART_LENGTH_VENDOR_UNIT'));?>
	</label>
	<div class="controls">
		<?php
			if (!empty($this->legthList))
			{
				echo $this->legthList;
			}
			else
			{
				echo Text::_('COM_QUICK2CART_NO_LENGTH_VENDOR_UNITS');
			}
		?>
	</div>
</div>
<div class="form-group">
	<label for="qtc_weight_class" class="control-label"><?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_WEIGHT_VENDOR_UNIT_DESC'), Text::_('COM_QUICK2CART_WEIGHT_VENDOR_UNIT'), '', Text::_('COM_QUICK2CART_WEIGHT_VENDOR_UNIT'));?>
	</label>
	<div class="controls">
		<?php
			if (!empty($this->weigthList))
			{
				echo $this->weigthList;
			}
			else
			{
				echo Text::_('COM_QUICK2CART_NO_WEIGHT_VENDOR_UNITS');
			}

		?>
	</div>
</div>

<!-- Default tax and shipping profile -->
<div class="form-group">
	<label class="control-label" for="taxprofile_id">
		<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_VEN_TAXPROFILE_DESC'), Text::_('COM_QUICK2CART_VEN_TAXPROFILE'), '', Text::_('COM_QUICK2CART_VEN_TAXPROFILE'));?>
	</label>

	<div class="controls qtc_shipProfileList">
		<span id="qtc_shipProfileSelListWrapper">
		<?php
		if ($isTaxationEnabled  && !empty($this->storeinfo[0]->id))
		{
			$defaultProfile = !empty($this->storeinfo[0]->taxprofile_id) ? $this->storeinfo[0]->taxprofile_id : '';
			echo $tax_listSelect = $storeHelper->getStoreTaxProfilesSelectList($this->storeinfo[0]->id, $defaultProfile, $fieldName = 'taxprofile_id',$fieldClass = '', $fieldId = 'taxprofile_id');

			if (empty($tax_listSelect))
			{
				echo Text::_('COM_QUICK2CART_VEN_U_NEED_TO_SETUP_TAXPROFILE_FIRST');
			}
		}
		else
		{
			echo Text::_('COM_QUICK2CART_VEN_U_NEED_TO_SETUP_TAXPROFILE_FIRST');
		}

		?>
		</span>
	</div>
</div>
<?php
if ($isShippingEnabled)
{
?>
<!-- Default tax and shipping profile -->
<div class="form-group">
	<label class="control-label" for="qtc_shipProfileSelList">
		<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_VEN_SHIPPROFILE_DESC'), Text::_('COM_QUICK2CART_VEN_SHIPPROFILE'), '', Text::_('COM_QUICK2CART_VEN_SHIPPROFILE'));?>
	</label>

	<div class="controls qtc_shipProfileList">
		<span id="qtc_shipProfileSelListWrapper">
		<?php

		if (!empty($this->storeinfo[0]->id))
		{
			// Here default_store_id - before saving the item, value =first store id
			// While edit default_store_id- item's store id
			$defaultProfile = !empty($this->storeinfo[0]->shipprofile_id) ? $this->storeinfo[0]->shipprofile_id : '';
			// Get qtc_shipProfileSelList
			echo $shipProfileSelectList = $qtcshiphelper->qtcLoadShipProfileSelectList($this->storeinfo[0]->id, $defaultProfile);
		}
		else
		{
			echo Text::_('COM_QUICK2CART_VEN_U_NEED_TO_SETUP_SHIPPROFILE_FIRST');
		}
		?>
		</span>
	</div>
</div>
<?php
} ?>

