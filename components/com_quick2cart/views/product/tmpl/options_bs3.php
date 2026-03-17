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

use Joomla\CMS\Editor\Editor;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

Text::script('QTC_SKU_EXIST', true);

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => Text::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
}

$selector       = $this->tagParamData['selector'];
$minTermLength  = $this->tagParamData['minTermLength'];
$allowCustom    = $this->tagParamData['allowCustom'];

$chosenAjaxSettings = new Registry(
	array(
		'selector'      => $selector,
		'type'          => 'GET',
		'url'           => Uri::root() . 'index.php?option=com_tags&task=tags.searchAjax',
		'dataType'      => 'json',
		'jsonTermKey'   => 'like',
		'minTermLength' => $minTermLength
	)
);
HTMLHelper::_('formbehavior.ajaxchosen', $chosenAjaxSettings);

if ($allowCustom)
{
	Factory::getDocument()->addScriptDeclaration(
		"
		jQuery(document).ready(function ($) {
			var customTagPrefix = '#new#';

			function tagHandler(event,element) {
				// Search a highlighted result

				var highlighted = $('" . $selector . "_chzn').find('li.active-result.highlighted').first();

				// Add the highlighted option
				if (event.which === 13 && highlighted.text() !== '')
				{
					// Extra check. If we have added a custom tag with element text remove it
					var customOptionValue = customTagPrefix + highlighted.text();
					$('" . $selector . " option').filter(function () { return $(element).val() == customOptionValue; }).remove();

					// Select the highlighted result
					var tagOption = $('" . $selector . " option').filter(function () { return $(element).html() == highlighted.text(); });
					tagOption.attr('selected', 'selected');
				}
				// Add the custom tag option
				else
				{
					var customTag = element.value;

					// Extra check. Search if the custom tag already exists (typed faster than AJAX ready)
					var tagOption = $('" . $selector . " option').filter(function () { return $(element).html() == customTag; });
					if (tagOption.text() !== '')
					{
						tagOption.attr('selected', 'selected');
					}
					else
					{
						var option = $('<option>');
						option.text(element.value).val(customTagPrefix + element.value);
						option.attr('selected','selected');

						// Append the option and repopulate the chosen field
						$('" . $selector . "').append(option);
					}
				}

				element.value = '';
				$('" . $selector . "').trigger('liszt:updated');
			}

			// Method to add tags pressing comma
			$('" . $selector . "_chzn input').keypress(function(event) {
				if (event.charCode === 44)
				{
					// Tag is greater than the minimum required chars
					if (this.value && this.value.length >= " . $minTermLength . ")
					{
						tagHandler(event, this);
					}

					// Do not add comma to tag at all
					event.preventDefault();
				}
			});

			// Method to add tags pressing enter
			$('" . $selector . "_chzn input').keyup(function(event) {
				// Tag is greater than the minimum required chars and enter pressed
				if (event.which === 13 && this.value && this.value.length >= " . $minTermLength . ")
				{
					tagHandler(event,this);
					event.preventDefault();
				}
			});
		});
		"
	);
}

$document = Factory::getDocument();

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('formbehavior.chosen', '#jform_tags', null, array('placeholder_text_multiple' => JText::_('JGLOBAL_TYPE_OR_SELECT_SOME_TAGS')));
}

$edit_task = 0;

if (!empty($this->item_id))
{
	$edit_task = 1;
}

$user = Factory::getUser();

// GETTING COMP PARAM @todo hv to use this->param to all layouts
$admin_commmisson    = $this->params->get('commission');
$product_image_limit = $this->params->get('maxProdImgUpload_limit', 6);
$isStockEnabled      = $this->params->get('usestock');
$prodAdmin_approval  = $this->params->get('admin_approval', 0);
$entered_numerics    = Text::_('QTC_ENTER_NUMERICS');

// For taskprofile radio button display
$storeHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php","storeHelper");
$storeList   = (array) $storeHelper->getUserStore($user->id);

$selected_id = 0;
$productType = 0;

if (!empty($this->itemDetail))
{
	$selected_id = $this->itemDetail['taxprofile_id'] ? $this->itemDetail['taxprofile_id'] : 0;
	$productType = $this->itemDetail['product_type'];
}
?>
<script type="text/javascript">
	jQuery( document ).ready(function() {
		var qtc_base_url            = Joomla.getOptions('system.paths').base;
		var isTaxationEnabled       = "<?php echo $isTaxationEnabled?>";
		var qtc_shipping_opt_status = "<?php echo $qtc_shipping_opt_status?>";
		var store_id = techjoomla.jQuery('#current_store_id').val();

		if (store_id == null)
		{
			store_id = '"<?php echo $this->store_id?>"';
		}
		var selected_taxid = '"<?php echo $selected_id?>"';
		// Get tax profile list

		if(isTaxationEnabled == 1)
		{
			qtcLoadTaxprofileList(store_id, selected_taxid);
		}
	});

	var imageid=0;

	function getTaxprofile()
	{
		var isTaxationEnabled = "<?php echo $isTaxationEnabled?>";
		var qtc_shipping_opt_status = "<?php echo $qtc_shipping_opt_status?>";
		var store_id = techjoomla.jQuery('#current_store_id').val();

		if (store_id == null)
		{
			store_id = '"<?php echo $this->store_id?>"';
		}

		var selected_taxid = '"<?php echo $selected_id?>"';

		if(isTaxationEnabled == 1)
		{
			qtcLoadTaxprofileList(store_id, selected_taxid);
		}

		if(qtc_shipping_opt_status == 1)
		{
			qtcUpdateShipProfileList(store_id);
		}
	}

	function addmoreImg(rId,rClass)
	{
		var selected_imgs=techjoomla.jQuery('.qtc_img_checkbox:checked').length;
		var visible_file=techjoomla.jQuery('.filediv').length;
		var allowed_img=<?php echo $product_image_limit;?> ;
		var remaing_imgs= new Number(allowed_img - selected_imgs - visible_file);

		if (remaing_imgs > 0)
		{
			imageid++;
			var num=imageid;
			var pre = new Number(num - 1);
			var removeButton="<span>";
			removeButton+="<button class='btn btn-danger btn-sm' type='button' id='remove"+num+"' onclick=\"removeClone('filediv"+num+"','jgive_container');\" title=\"<?php echo Text::_('COM_Q2C_REMOVE_TOOLTIP');?>\" >";
			removeButton+="<i class=\"<?php echo QTC_ICON_MINUS;?> <?php echo Q2C_ICON_WHITECOLOR; ?> \"></i></button>";
			removeButton+="</span>";

			var newElem = techjoomla.jQuery('#' +rId).clone().attr('id', rId + num);
			var delid=rId;

			newElem.find('.addmore').attr('id','addmoreid'+ num);
			newElem.find(':file').attr('name','prod_img'+ imageid);
			newElem.find(':file').attr('id','avatar').val('');
			removeClone('addmoreid'+pre ,'addmoreid'+pre );
			techjoomla.jQuery('.'+rClass+':last').after(newElem);
			techjoomla.jQuery('#'+rId+num).append(removeButton);
		}
		else
		{
			alert("<?php echo Text::sprintf('QTC_U_ALLOWD_TO_UPLOAD_IMGES', $product_image_limit)?>");
		}
	}

	function checkForSku(sku)
	{
		var editprod="<?php echo $edit_task;?>";
		var formName = document.qtcAddProdForm;
		var skuval = formName.sku.value;
		/*if not a edit task and not empty sku value then only call ajax*/
		if (skuval)
		{
			var oldSku="<?php if (!empty($this->itemDetail)) {  echo htmlspecialchars(stripslashes($this->itemDetail['sku']), ENT_COMPAT, 'UTF-8'); } ?>";
			/*while edit sku is not changed*/
			if (skuval != oldSku)
			{
				var actUrl = '?option=com_quick2cart&task=product.checkSku&sku='+sku;
				var skuele = formName.sku;
				qtcIsPresentSku(actUrl, skuele);
			}
		}
	}
</script>

<div class ="form-horizontal">
	<?php
	if ($prodAdmin_approval)
	{
	?>
		<div class="alert alert-info">
			<em>
				<i><?php echo Text::_('COM_QUICK2CART_PROD_ADMIN_APPROVAL_NEEDED_HELP'); ?></i>
			</em>
		</div>
	<?php
	} ?>
	<!-- for TITLE/ NAME -->
	<div class='qtc_title_textbox form-group row' >
		<label for="item_name" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_TITLE_DES'), Text::_('QTC_PROD_TITLE'), '', Text::_('QTC_PROD_TITLE'). ' *');?>
		</label>
		<?php
			$item_id = (!empty($this->item_id)) ? $this->item_id : 0;

			JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
			$model   =  new Quick2cartModelcart;
			$p_title = (!empty($this->itemDetail) && $this->itemDetail['name']) ? ($this->itemDetail['name']) : '';
		?>
		<div class="col-sm-9 col-xs-12">
		<input type="text" class="required form-control" name="item_name" id="item_name" value="<?php echo $this->escape($p_title);?>" />
			<input type="hidden" name="pid" id="pid" value="<?php echo $this->item_id; ?>" />
			<input type="hidden" name="featured" value="<?php echo (isset($this->itemDetail['featured']) ? $this->itemDetail['featured'] : 0) ?>">
			<input type="hidden" name="taxprofile_id" value="<?php echo (isset($this->itemDetail['taxprofile_id']) ? $this->itemDetail['taxprofile_id'] : 0) ?>">
			<input type="hidden" name="parent_id" value="<?php echo (isset($this->itemDetail['parent_id']) ? $this->itemDetail['parent_id'] : 0) ?>">
		</div>
	</div>
	<div class='form-group row'>
		<label for="item_alias" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_ALIAS_DES'), Text::_('QTC_PROD_ALIAS'), '', Text::_('QTC_PROD_ALIAS'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<input type="text" class="form-control" name="item_alias" id="item_alias" value="<?php echo (!empty($this->itemDetail['alias']))?$this->escape($this->itemDetail['alias']):'';  ?>" />
		</div>
	</div>
	<?php
		// For vaiable product show the field. (If you change the stock option from config after saving the vaiable product then this case require)
		if (!empty($productType) && $productType == 2)
		{
			$prodTypeStyle = '';
		}
		else
		{
			$prodTypeStyle = ($isStockEnabled == 1) ? '' : "af-d-none";
		}
	?>
	<div class="form-group row <?php echo $prodTypeStyle ?>">
		<label for="qtc_product_type" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_SEL_TYPE_TOOLTIP'), Text::_('QTC_PROD_SEL_TYPE'), '', Text::_('QTC_PROD_SEL_TYPE'). ' *');?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<?php
			if ($productType != 2)
			{
				echo HTMLHelper::_('select.genericlist',$this->product_types,'qtc_product_type','class="required form-control"','value','text',$productType,'qtc_product_type');
			}
			else
			{
			?>
				<input type="hidden" name="qtc_product_type" value="<?php echo $productType ?>">
				<span class="label label-success"><?php echo $this->product_types[$productType]->text; ?></span>
			<?php
			}

			// show message for product type
			if($item_id == 0)
			{
			?>
			<div class= "text-warning">
				<?php echo Text::_("QTC_PRODUCT_TYPE_WARNING");?>
			</div>
			<?php
			}
			?>
		</div>
	</div>
	<div class="form-group row">
		<label for="prod_cat" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_SEL_CAT_TOOLTIP'), Text::_('QTC_PROD_SEL_CAT'), '', Text::_('QTC_PROD_SEL_CAT'). ' *');?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<?php
			if (!empty($this->isAllowedtoChangeProdCategory))
			{
				?>
				<input type="hidden" name="prod_cat" value="<?php echo $this->itemDetail['category'] ?>">
				<span class="label label-success"><?php echo $this->catName . " "; ?></span>
				<?php
			}
			else
			{
				echo $this->cats;
			}
			?>
		</div>
	</div>

	<!-- SELECT STORE -->
	<?php
	if (count($this->store_role_list))
	{
		?>
		<div class="form-group row <?php (count($this->store_role_list)) ? " " : "af-d-none;" ?>">
			<label for="qtc_store" class="col-sm-3 col-xs-12 form-label">
				<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_SELECT_STORE_DES'), Text::_('QTC_PROD_SELECT_STORE'), '', Text::_('QTC_PROD_SELECT_STORE'));?>
			</label>
			<div class="col-sm-9 col-xs-12">
				<?php
				 $defaultStore = (!empty($this->itemDetail['store_id']))? $this->itemDetail['store_id']:$this->store_role_list[0]['store_id'];

					foreach ($this->store_role_list as $key=>$value)
					{
						$options[] = HTMLHelper::_('select.option', $value["store_id"],$value['title']);
					}

					echo $this->dropdown = HTMLHelper::_('select.genericlist',$options,'store_id','class="qtc_putmargintop10px form-select"   onchange="getTaxprofile();" ','value','text',$defaultStore,'current_store_id');
				?>
			</div>
		</div>
	<?php
	}

	if (count($this->store_role_list) == 1)
	{
		?>
		<input type="hidden"  name="store_id" value="<?php echo $this->store_role_list[0]['store_id']; ?>" />
		<?php
	}
	?>

	<!-- sku -->
	<div class="form-group row">
		<label for="qtc_sku" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_SKU_TOOLTIP'), Text::_('QTC_PROD_SKU'), '', Text::_('QTC_PROD_SKU'). ' *');?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<?php
				$readonly = (!empty($this->item_id)) ? "readonly" : '';
				$edit     = (!empty($this->item_id)) ? 1 : '';
			?>
			<input
				type="text"
				name="sku"
				id="qtc_sku"
				class="form-control required"
				value="<?php if (!empty($this->itemDetail)){ echo stripslashes($this->itemDetail['sku']); } ?>"
				autocomplete="off"
				onBlur="checkForSku(this.value)" />
			<span class="help-inline"><?php echo Text::_('QTC_PROD_SKU_UNIQUE');?></span>
		</div>
	</div>

	<div class="form-group row">
		<label for="qtc_product_tag" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_TAG_TOOLTIP'), Text::_('QTC_PROD_TAG'), '', Text::_('QTC_PROD_TAG'));?>
		</label>
		<div class="col-sm-9 col-xs-12 controls">
			<?php
			$defaultTags = 0;

			if (isset($this->itemDetail['tags']) && !empty($this->itemDetail['tags']->tags))
			{
				$defaultTags = explode(',', $this->itemDetail['tags']->tags);
			}

			$options   = array();

			foreach ($this->tags as $key => $tag)
			{
				$options[] = HTMLHelper::_('select.option', $tag['id'],$tag['title']);
			}

			echo HTMLHelper::_('select.genericlist',$options,'jform[tags][]','multiple class="chosen-select form-select"','value','text',$defaultTags,'jform_tags');
		?>
		</div>
	</div>
	<?php

	if ($prodAdmin_approval == 0)
	{?>
		<!-- publish and unpublish-->
		<div class='form-group row' >
			<label for="state" class="col-sm-3 col-xs-12 form-label">
				<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_STATUS'), Text::_('QTC_PROD_STATUS_DES'), '', Text::_('QTC_PROD_STATUS'));?>
			</label>
			<div class="col-sm-9 col-xs-12">
				<label class="radio-inline">
				<?php
				$isPublished   = " checked ";
				$isUnPublished = "";

				if (!empty($this->itemDetail) && ($this->itemDetail['state'] == 0))
				{
					$isPublished   = "";
					$isUnPublished = " checked ";
				}
				?>
				<input type="radio" name="state" id="state" value="1" <?php echo $isPublished; ?> >
					<?php echo Text::_('QTC_PROD_PUBLISH')?>
				</label>
				<label class="radio-inline">
					<input type="radio" name="state"  value="0" <?php echo $isUnPublished; ?>>
				<?php echo Text::_('QTC_PROD_UNPUBLISH')?>
				</label>
			</div>
		</div>
	<?php
	} ?>

	<!-- PRICE PRICE -->
	<div class='form-group row qtc_currencey_textbox'>
		<label for="price_<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_PRICE_DESC'), Text::_('QTC_ITEM_PRICE'), '', '* ' . Text::_('QTC_ITEM_PRICE'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<?php
			$currdata         = array();
			$base_currency_id = "";

			// if all currency fields r filled
			$currfilled      = 1;
			$multiCurrencies = 0;

			if (count($curr) > 1)
			{
				$multiCurrencies = 1;
			}

			// key contain 0,1,2... // value contain INR...
			foreach ($curr as $key=>$value)
			{
				//$name="jform[attribs][$value]";
				$currvalue="";

				if (!empty($this->item_id))
				{
					$currvalue = $quick2cartModelAttributes->getCurrenciesvalue($this->pid, $value, $this->client, $this->item_id);
				}

				$storevalue = !empty($currvalue) ? (isset($currvalue[0]['price']) ? $currvalue[0]['price'] : '') : '';

				if (empty($storevalue))
				{
					$currfilled=0;
				}

				if (!empty($curr_syms[$key]))
				{
					$currtext = $curr_syms[$key];
				}
				else
				{
					$currtext = $value;
				}
				?>

				<?php
				if ($multiCurrencies)
				{ ?>
					<div class="row">
						<div class="  curr_margin">
							<label for="price_<?php echo trim($value);?>" class="col-sm-2 col-xs-12">
								<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_PRICE_DESC'), Text::_('QTC_ITEM_PRICE'), '', Text::_('QTC_ITEM_PRICE') . ' ' . Text::_('COM_QUICK2CART_IN') . ' ' . trim($currtext));?> &nbsp;
							</label>
							<div class="col-sm-2 col-xs-12 input-group">
								<input Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
									class="currtext required qtc_requiredoption form-control"
									style="align:right;"
									id="price_<?php echo trim($value);?>"
									type="text"
									name="multi_cur[<?php echo trim($value);?>]"
									value="<?php echo $storevalue;?>"
									placeholder="<?php echo trim($currtext);?>" />
								<div class="input-group-addon"><?php echo $currtext;?></div>
							</div>
							<div class="qtcClearBoth"></div>
						</div>
					</div>
				<?php
				}
				else
				{ ?>
					<div class="col-sm-2 col-xs-12 input-group curr_margin">
						<input
							Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
							class="currtext required qtc_requiredoption form-control"
							id="price_<?php echo trim($value);?>"
							type="text"
							name="multi_cur[<?php echo trim($value);?>]"
							value="<?php echo $storevalue;?>"
							placeholder="<?php echo trim($currtext);?>" />
						<div class="input-group-addon"><?php echo $currtext;?></div>
					</div>
					<div class="qtcClearBoth"></div>
				<?php 
				}
			}
			?>
		</div>
	</div>


	<!-- DISCOUNT PRICE -->
	<div class="form-group row qtc_currencey_textbox <?php echo (($this->params->get('usedisc') == '0') ? 'af-d-none' : ''); ?>" >
		<label for="disc_price_<?php echo !empty($curr[0]) ? $curr[0] : '' ;?>" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_DIS_PRICE_DESC'), Text::_('QTC_ITEM_DIS_PRICE'), '', Text::_('QTC_ITEM_DIS_PRICE'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<?php
			$currdata = array();
			$base_currency_id = "";

			// key contain 0,1,2... // value contain INR...
			foreach ($curr as $key=>$value)
			{
				//$name="jform[attribs][$value]";
				$currvalue="";
				if (!empty($this->item_id))
				{
					$currvalue = $quick2cartModelAttributes->getCurrenciesvalue($this->pid, $value, $this->client, $this->item_id);
				}

				$storevalue = !empty($currvalue) ? (isset($currvalue[0]['discount_price']) ? $currvalue[0]['discount_price'] : '') : '';
				$currsymbol = $comquick2cartHelper->getCurrencySymbol($value);
				?>

				<?php if ($multiCurrencies) : ?>
					<div class="row">
						<div class="curr_margin">
							<label for="disc_price_<?php echo trim($value);?>" class="col-sm-2 col-xs-12">
								<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_DIS_PRICE_DESC'), Text::_('QTC_ITEM_DIS_PRICE'), '', Text::_('COM_QUICK2CARET_PRICE') . ' ' . Text::_('COM_QUICK2CART_IN') . ' ' . trim($currsymbol));?> &nbsp;
							</label>
							<div class="col-sm-2 col-xs-12 input-group">
								<input
									Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
									onchange="validateDiscountPrice('<?php echo trim($value);?>')"
									class="currtext form-control"
									style="align:right;"
									id="disc_price_<?php echo trim($value);?>"
									type="text"
									name="multi_dis_cur[<?php echo trim($value);?>]"
									value="<?php echo $storevalue;?>"
									placeholder="<?php echo trim($currsymbol);?>" />
								<div class="input-group-addon"><?php echo $currsymbol;?></div>
							</div>
							<div class="qtcClearBoth"></div>
						</div>
					</div>
				<?php else : ?>
					<div class="col-sm-2 col-xs-12 input-group curr_margin">
						<input
							Onkeyup="checkforalpha(this,'46', '<?php echo addslashes($entered_numerics); ?>')"
							onchange="validateDiscountPrice('<?php echo trim($value);?>')"
							class=" currtext form-control"
							style="align:right;"
							id="disc_price_<?php echo trim($value);?>"
							type="text"
							name="multi_dis_cur[<?php echo trim($value);?>]"
							value="<?php echo $storevalue;?>"
							placeholder="<?php echo trim($currsymbol);?>" />
						<div class="input-group-addon"><?php echo $currsymbol;?></div>
					</div>
					<div class="qtcClearBoth"></div>
				<?php endif; ?>
			<?php
			}
			?>
		</div>
	</div>

	<!--  PROD description -->
	<div class="form-group row">
		<label for="description" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_DES_TOOLTIP'), Text::_('QTC_PROD_DES'), '',Text::_('QTC_PROD_DES'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<?php
			$on_editor = $this->params->get('enable_editor',0);
			if (empty($on_editor))
			{
				?>
				<textarea  rows="3" column="3" name="description[data]" id="description" class="form-control1111"><?php if (!empty($this->itemDetail['description'])){  echo trim($this->itemDetail['description']); } ?></textarea>
			<?php
			}
			else
			{
				$getEditor  = Factory::getApplication()->get('editor');
				$editor     = Editor::getInstance($getEditor);

				if (!empty($this->itemDetail))
				{
					// If you set last parameter to false then other option will not display.
					echo $editor->display("description[data]",$this->itemDetail['description'],400,400,40,20,false);
				}
				else
				{
					echo $editor->display("description[data]",'',400,400,40,20,false);
				}
			}
				?>
		</div>
	</div>
	<!-- END :: PROD description -->

	<!--avatar -->
	<div class="form-group row imagediv" id="imagediv">
		<label for="avatar" class="col-sm-3 col-xs-12 form-label"><?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_IMG_TOOLTIP'), Text::_('QTC_PROD_IMG'), '', Text::_('QTC_PROD_IMG'));?></label>
		<div class="col-sm-9 col-xs-12">
			<?php
			$videoSupportedExtension     = explode(",", $this->params->get('videoExtensions'));

			if (!empty($this->itemDetail['images']) )
			{
				?>
				<div class=" ">
					<div class="text-info">
						<?php echo Text::_('COM_Q2C_UNCHECK_TO_REMOVE_EXISTING_IMAGE');?>
					</div>
					<div><!-- wrapper for images-->
						<ul class="thumbnails qtc_ForLiStyle" id="qtc_nev">
							<?php
							$images = json_decode($this->itemDetail['images'],true);
							require_once(JPATH_SITE . '/components/com_quick2cart/helpers/media.php');
							$media=new qtc_mediaHelper();

							foreach ($images as $key=>$img)
							{
								if (!empty($img))
								{
									$originalImg                 = $img;
									$file_name_without_extension = $media->get_media_file_name_without_extension($img);
									$media_extension             = $media->get_media_extension($img);
									$img                         = Uri::root().'components/com_quick2cart/images/default_product.jpg';

									if ($this->params->get('video_gallery') && in_array($media_extension, $videoSupportedExtension))
									{
										$img = $comquick2cartHelper->isValidImg($file_name_without_extension.'.'.$media_extension);
									}
									else
									{
										$img = $comquick2cartHelper->isValidImg($file_name_without_extension.'_M.'.$media_extension);
									}
									?>
									<li class="">
										<label class="checkbox-inline">
												<input class="qtcmarginLeft1px qtc_img_checkbox" type="checkbox" name="qtc_prodImg[<?php echo $key?>]" value="<?php echo $originalImg;?>"  id="qtc_prodImg_<?php echo $key?>"  autocomplete="off" checked />
												<?php
												if ($this->params->get('video_gallery') && in_array($media_extension, $videoSupportedExtension))
												{
													?>
														<video controls width="20%">
															<source src="<?php echo $img;?>" type="<?php echo 'video/' . $media_extension;?>">
														</video>
													<?php
												}
												else
												{?>
													<img class='img-rounded qtc_prod_img100by100 com_qtc_img_border'   src="<?php echo $img;?>" alt="<?php echo  Text::_('QTC_IMG_NOT_FOUND') ?>"/>
												<?php
												}?>
										</label>
									</li>
								<?php
								}
							}
						?>
						</ul>
					</div>
				</div>
				<!-- span12 END-->
				<?php
			}
			?>
			<div class="form-inline">
				<?php
				$required=" required ";
				if (!empty($this->itemDetail['images']) )
				{
					$required="";
				}
				?>
				<div class="filediv" id="filediv">
					<span>
						<input type="file" name="prod_img" id="avatar" class="af-d-inline form-control" placeholder="<?php echo Text::_('COM_QUICK2CART_IMAGE_MSG');?>" accept="image/*, video/*">
					</span>
				</div>

				<!-- ADD MORE BTN-->
				<div>
					<span class="addmore"  id="addmoreid"  id="addmoreid" >
						<button onclick="addmoreImg('filediv','filediv');" type="button" class="btn btn-sm btn-primary" title="<?php echo Text::_('COM_Q2C_IMAGE_ADD_MORE');?>">
							<i class="<?php echo QTC_ICON_PLUS;?> <?php echo Q2C_ICON_WHITECOLOR; ?> "></i>
						</button>
					</span>
				</div>

				<div class="clearfix">&nbsp;</div>
				<div class="text-warning">
					<p>
						<?php echo Text::sprintf('COM_QUICK2CART_ALLOWED_IMG_FORMATS', 'gif, jpeg, jpg, png, pjpeg', $this->params->get('max_size', '1024'));?>
					</p>

					<?php
					if ($this->params->get('video_gallery') && !empty($videoSupportedExtension))
					{?>
						<p>
							<?php echo Text::sprintf('COM_QUICK2CART_ALLOWED_VIDEO_FORMATS', $this->params->get('videoExtensions'), $this->params->get('max_video_file_size', '10'));?>
						</p>
					<?php
					}?>
				</div>
			</div>
			<!--END OF ROW FLUID -->
		</div>
		<!-- END OF CONTROL-->
	</div>
	<!-- END OF form-group row ->

	<!-- VIDEO LINK-->
	<div class="form-group row" style="display:none;">
		<label for="youtube_link" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('QTC_PROD_YOUTUBE_TOOLTIP'), Text::_('QTC_PROD_YOUTUBE'), '', Text::_('QTC_PROD_YOUTUBE'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<input type="text" name="youtube_link" id="youtube_link" class=""
			value="<?php if (!empty($this->itemDetail)){  echo stripslashes($this->itemDetail['video_link']); } ?>"
			placeholder="<?php echo Text::_('QTC_PROD_YOUTUBE_PLACE'); ?>" />
		</div>
	</div>

	<?php
	//@TODO get all product detail which from getItemDetail (modified)
	/*fetch Minimum/ max /stock  item Quantity*/
	// item_id present i.e  item is saved
	if (!empty($item_id))
	{
		$minmaxstock = $model->getItemRec($item_id);
	}

	$instock="";
	$outofstock="";

	if (isset($minmaxstock))
	{
		if ($minmaxstock->stock==1)
		{
			$instock="checked='checked'";
		}
		else
		{
			$outofstock="checked='checked'";
		}
	}

	if ($this->params->get('usestock'))
	{
		$qtc_stock_style = ($this->params->get('usestock')==1)? "" : "af-d-none";
	?>
		<div class="form-group row <?php echo $qtc_stock_style;?>">
			<label for="stock" class="col-sm-3 col-xs-12 form-label">
				<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_STOCK_DESC'), Text::_('PLG_QTC_ITEM_STOCK'), '', Text::_('PLG_QTC_ITEM_STOCK'));?>
			</label>
			<div class="col-sm-9 col-xs-12">
				<input Onkeyup="checkforalpha(this,'', '<?php echo addslashes($entered_numerics); ?>')"
				type="text" name="stock" id="stock"
				value="<?php if (isset($minmaxstock->stock)) echo $minmaxstock->stock;?>"
				class="form-control-sm validate-integer" />
				<?php
				if ($productType == 2)
				{ ?>
					<div class="text-info"><?php echo Text::_('COM_QUICK2CART_ITEM_STOCK_VS_ATTRI_STOCK_HELP'); ?></div>
				<?php
				} ?>
			</div>
		</div>
	<?php
	}
	?>

	<!-- for Minimum/ max item Quantity -->
	<?php
		$qtc_min_max_status = $this->params->get('minmax_quantity');
		$qtc_min_max_style = ($qtc_min_max_status==1) ? "" : "af-d-none";
	?>

	<div class="form-group row <?php echo $qtc_min_max_style;?>">
		<label class="col-sm-3 col-xs-12 form-label" for="item_slab">
		<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_SLAB_DESC'), Text::_('COM_QUICK2CART_ITEM_SLAB'), '', Text::_('COM_QUICK2CART_ITEM_SLAB'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<input Onkeyup="checkforalpha(this,'', '<?php echo addslashes($entered_numerics); ?>')"  Onchange="checkSlabValue();" type="text" name="item_slab" id="item_slab"  value="<?php echo isset($minmaxstock) ? $minmaxstock->slab: 1  ?>" class="form-control-sm validate-integer"  >
		</div>
	</div>

	<div class="form-group row <?php echo $qtc_min_max_style;?>">
		<label for="min_item" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_MIN_QTY_DESC'), Text::_('QTC_ITEM_MIN_QTY'), '', Text::_('QTC_ITEM_MIN_QTY'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<input onChange="checkSlabValueField(this,'', '<?php echo addslashes($entered_numerics); ?>')"
				type="text" name="min_item" id="min_item"
				value="<?php if (isset($minmaxstock)) echo $minmaxstock->min_quantity;?>"
				class="form-control-sm validate-integer" />
		</div>
	</div>

	<div class="form-group row <?php echo $qtc_min_max_style;?>">
		<label for="max_item" class="col-sm-3 col-xs-12 form-label">
			<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_ITEM_MAX_QTY_DESC'), Text::_('QTC_ITEM_MAX_QTY'), '', Text::_('QTC_ITEM_MAX_QTY'));?>
		</label>
		<div class="col-sm-9 col-xs-12">
			<input Onkeyup="checkforalpha(this,'', '<?php echo addslashes($entered_numerics); ?>')"
			onChange="checkSlabValueField(this,'', '<?php echo addslashes($entered_numerics); ?>')" type="text" name="max_item" id="max_item"
				value="<?php if (isset($minmaxstock))  echo $minmaxstock->max_quantity;?>"
				class="form-control-sm validate-integer" />
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-xs-12 form-label" for="metadesc">
			<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_META_DESC_TOOLTIP'), Text::_('COM_QUICK2CART_META_DESC'), '', Text::_('COM_QUICK2CART_META_DESC'));?>
		</label>
		<div class="col-md-9 col-sm-9 col-xs-12">
			<div>
				<textarea name="metadesc" id="metadesc" cols="19" rows="3" class="form-control"><?php if (isset($this->itemDetail['metadesc'])) echo $this->itemDetail['metadesc']; ?></textarea>
			</div>
		</div>
	</div>

	<div class="form-group row">
		<label class="col-sm-3 col-xs-12 form-label" for="metakey">
			<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_META_KEYWORDS_TOOLTIP'), Text::_('COM_QUICK2CART_META_KEYWORDS'), '', Text::_('COM_QUICK2CART_META_KEYWORDS'));?>
		</label>
		<div class="col-md-9 col-sm-9 col-xs-12">
			<div class="">
			<textarea name="metakey" id="metakey" cols="19" rows="3" class="form-control"><?php if (isset($this->itemDetail['metakey'])) echo $this->itemDetail['metakey']; ?></textarea>
			</div>
		</div>
	</div>
</div>
<!-- form horizantal end-->
