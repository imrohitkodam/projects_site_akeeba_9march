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

<div class="clearfix"></div>
<div class="">
	<!-- for Length & weight class option -->
	<?php
	$qtc_shipping_opt_style = ($qtc_shipping_opt_status==1) ? "display:flex" : "display:none";
	$storeHelper            = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php","storeHelper");
	$legthList              = (array) $storeHelper->getStoreShippingLegthClassList($storeid = 0);
	$weigthList             = (array) $storeHelper->getStoreShippingWeigthClassList($storeid = 0);

	if ($isTaxationEnabled)
	{?>
		<div class="form-group row">
			<label class="col-sm-3 col-xs-12 col-md-4 form-label" for="qtcTaxprofileSel" title="<?php echo Text::_('COM_QUICK2CART_TAXPROFILE_DESC_TOOLTIP');?>">
				<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_TAXPROFILE_DESC'), '', Text::_('COM_QUICK2CART_TAXPROFILE_DESC'));?>
			</label>
			<div class="col-md-8 col-sm-6 col-xs-12 taxprofile"></div>&nbsp;
<!--
			<div class="clearfix">&nbsp;</div>
-->
		</div>
		<?php
	}?>
	<?php
	if ($qtc_shipping_opt_status)
	{
	?>
		<div class='form-group row ' style="<?php echo $qtc_shipping_opt_style;?>">
			<label class="col-sm-3 col-xs-12 col-md-4 form-label" for="qtc_item_length" title="<?php echo Text::_('COM_QUICK2CART_PROD_DIMENSION_LENGTH_LABEL_TOOLTIP');?>">
				<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PROD_DIMENSION_LENGTH_LABEL_TOOLTIP'), '', Text::_('COM_QUICK2CART_PROD_DIMENSION_LENGTH_LABEL'));?>
			</label>
			<div class="col-sm-9 col-xs-12 col-md-8">
				<div class="row">
					<input
						type="text"
						class="col-sm-2 input-large af-mr-5 af-ml-15"
						Onkeyup='checkforalpha(this,46,"<?php echo Text::_("QTC_ENTER_NUMERICS"); ?>");'
						name='qtc_item_length' id='qtc_item_length'
						value='<?php echo (!empty($minmaxstock->item_length)) ?  number_format($minmaxstock->item_length, 2, '.', '') : '' ?>'
						placeholder="<?php echo Text::_('COM_QUICK2CART_LENGTH_HINT') ?>" />
					x
					<input 
						type="text"
						class="col-sm-2 input-large af-mr-5 af-ml-5"
						Onkeyup='checkforalpha(this,46,"<?php echo Text::_("QTC_ENTER_NUMERICS"); ?>");'
						name='qtc_item_width'
						id='qtc_item_width'
						value='<?php  echo (!empty($minmaxstock->item_width)) ?  number_format($minmaxstock->item_width, 2, '.', '') : '' ?>'
						placeholder="<?php echo Text::_('COM_QUICK2CART_WIDTH_HINT') ?>" />
					x
					<input
						type="text"
						class="col-sm-2 input-large af-mr-5 af-ml-5"
						Onkeyup='checkforalpha(this,46,"<?php echo Text::_("QTC_ENTER_NUMERICS"); ?>");'
						name='qtc_item_height'
						id='qtc_item_height'
						value='<?php echo (!empty($minmaxstock->item_height)) ?  number_format($minmaxstock->item_height, 2, '.', '') : '' ?>' placeholder="<?php echo Text::_('COM_QUICK2CART_HEIGHT_HINT') ?>" />
					<div class="input-append col-sm-4 mt-1">
						<?php
							// Get store configued length id. The get default value
							$lenUniteId = 0;

							if (isset($minmaxstock) && $minmaxstock->item_length_class_id)
							{
								// While edit used item class id
								$lenUniteId = $minmaxstock->item_length_class_id;
							}
							elseif (isset($this->defaultStoreSettings['length_id']))
							{
								// If for store default length unite has set
								$lenUniteId = $this->defaultStoreSettings['length_id'];
							}

							$lenUnitDetail = $storeHelper->getProductLengthDetail($lenUniteId);
							echo HTMLHelper::_('select.genericlist', $this->lengthClasses, "length_class_id", 'class="form-select"', "id", "title", $lenUnitDetail['id']);
							?>
					</div>
				</div>
			</div>
		</div>

		<!-- weight unit-->
		<div class='form-group row qtc_item_weight' style="<?php echo $qtc_shipping_opt_style;?>">
			<label class="col-sm-3 col-xs-12 col-md-4 form-label" for="qtc_item_weight" title="<?php echo Text::_('COM_QUICK2CART_PROD_DIMENSION_WEIGTH_LABEL_TOOLTIP');?>">
				<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_PROD_DIMENSION_WEIGTH_LABEL'), '', Text::_('COM_QUICK2CART_PROD_DIMENSION_WEIGTH_LABEL'));?>
			</label>
			<div class="col-md-8 col-sm-6 col-xs-12">
				<div class="input-group col-sm-2 mt-1">
					<input
						type="text"
						class="col-sm-2 input-large af-mr-5 form-control"
						Onkeyup='checkforalpha(this,46,"<?php echo Text::_("QTC_ENTER_NUMERICS"); ?>");'
						name='qtc_item_weight'
						id="qtc_item_weight"
						value='<?php if (isset($minmaxstock)) echo number_format($minmaxstock->item_weight, 2, '.', '');?>' />
					<?php
						// Get store configued length id.
						// The get default value
						$weightUniteId = 0;

						if (isset($minmaxstock) && $minmaxstock->item_weight_class_id)
						{
							// While edit used item class id
							$weightUniteId = $minmaxstock->item_weight_class_id;
						}
						elseif (isset($this->defaultStoreSettings['weight_id']))
						{
							// If for store default length unite has set
							$weightUniteId = $this->defaultStoreSettings['weight_id'];
						}

						$weightUniteDetail = $storeHelper->getProductWeightDetail($weightUniteId);
						echo HTMLHelper::_('select.genericlist', $this->weightClasses, "weigth_class_id", 'class="form-select"', "id", "title", $weightUniteDetail['id']);
						?>
				</div>
			</div>
		</div>
		<!-- END for Legth & weigth class option -->
		<!-- Shipping Profile-->
		<div class="form-group row">
			<label class="col-sm-3 col-xs-12 col-md-4 form-label" for="qtc_shipProfileSelList" title="<?php echo Text::_('COM_QUICK2CART_S_SEL_SHIPPROFILE_TOOLTIP');?>">
				<?php echo HTMLHelper::tooltip('', Text::_('COM_QUICK2CART_S_SEL_SHIPPROFILE'), '', Text::_('COM_QUICK2CART_S_SEL_SHIPPROFILE'));?>
			</label>
			<div class="col-sm-5 col-md-8 col-xs-12 control qtc_shipProfileList">
				<span id="qtc_shipProfileSelListWrapper">
				<?php
					// Here default_store_id - before saving the item, value =first store id
					// While edit default_store_id- item's store id
					$defaultProfile   = !empty($this->itemDetail['shipProfileId']) ? $this->itemDetail['shipProfileId'] : '';
					$shipDefaultStore = !empty($this->itemDetail['store_id']) ? $this->itemDetail['store_id'] : $this->store_id;

					// Get qtc_shipProfileSelList
					echo $qtcshiphelper->qtcLoadShipProfileSelectList($shipDefaultStore, $defaultProfile);
				?>
				</span>
			</div>
		</div>
	<?php
	}
	?>
</div>
