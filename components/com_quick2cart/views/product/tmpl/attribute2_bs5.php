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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.formvalidator');
$jinput       = Factory::getApplication()->input;

$entered_numerics = "'".Text::_('QTC_ENTER_NUMERICS')."'";
$addpre_select    = array();
$addpre_select[]  = HTMLHelper::_('select.option','+', Text::_('QTC_ADDATTRI_PREADD'));
$addpre_select[]  = HTMLHelper::_('select.option','-', Text::_('QTC_ADDATTRI_PRESUB'));

$js_key="Joomla.submitbutton = function(task){ ";
	$js_key.="
		if (task == 'cancel')
		{";
			$js_key.="Joomla.submitform(task);";
			$js_key.="
		}
		else
		{
			var validateflag = document.formvalidator.isValid(document.qtcAddProdForm);
			if (validateflag)
			{";
				$js_key.="Joomla.submitform(task);";
				$js_key.="
			}
			else
			{
				return false;
			}
		}
	}
	function checkfornum(el)
	{
		var i =0 ;
		for(i=0;i<el.value.length;i++){
		   if (el.value.charCodeAt(i) > 47 && el.value.charCodeAt(i) < 58) { alert(\"".Text::_('QTC_NUMERICS_NOT_ALLOWED')."\"); el.value = el.value.substring(0,i); break;}
		}
	}

	function qtc_ispositive(ele)
	{
		var val=ele.value;
		if (val==0 || val < 0)
		{
			ele.value='';
			alert(\"".Text::_('QTC_ENTER_POSITIVE_ORDER')."\");
			return false;
		}

		var attributeContainerId = techjoomla.jQuery(ele).closest(\".qtc_container\").attr('id');
		var elems =techjoomla.jQuery(\"#\" + attributeContainerId).find(\".qtc-attribute-Option-order\");
		var values = [];

		elems.each(function () {
			if(values.indexOf(this.value) !== -1)
			{
				alert(Joomla.Text._('QTC_ORDER_IS_REPEATED'));
				return false;
			}
			values.push(this.value);
		});
	}
";
	$document->addScriptDeclaration($js_key);
	$addpre_select   = array();
	$addpre_select[] = HTMLHelper::_('select.option','+', Text::_('QTC_ADDATTRI_PREADD'));
	$addpre_select[] = HTMLHelper::_('select.option','-', Text::_('QTC_ADDATTRI_PRESUB'));

	$pid         = $jinput->get('pid',0,'INTEGER');
	$client      = $jinput->get( 'client','','STRING');
	$attri_model = new quick2cartModelAttributes();

	// clearing previous contain(garbage)
	$this->attribute_opt = array();
	$att_op_count        = 0;

	if (!empty($this->allAttribues[$i]))
	{
		$this->attribute_opt = $attri_model->getAttributeoption($this->allAttribues[$i]->itemattribute_id);
		$att_op_count        = count($this->attribute_opt);
	}

	//  For field type and is compulsory field
	$hideFieldsForStockableAttribute = (!empty($stockableAttrId) && $stockableAttrId == $this->allAttribues[$i]->itemattribute_id) ? "display:none;" : '';
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 ">
			<table class="table table-condensed removeMargin qtc-table qtc-noborder" >
				<tbody>
					<tr class="attributeTrHeading hidden-xs hidden-sm">
						<td width="33%" align="left">
							<label class="" title="<?php echo Text::_('QTC_PROD_SEL_ATTRIBUTE_TOOLTIP');?>" >
								<b><?php echo Text::_('QTC_PROD_SEL_ATTRIBUTE'); ?></b>
							</label>
						</td>
						<td width="33%"	align="left"></td>
						<td align="left">
							<label for="att_detail[<?php echo $i; ?>][is_stock_keeping]" class="isStockKeeping">
								<b class="<?php echo $stockCheckboxClass; ?>" title="<?php echo Text::_('COM_QUICK2CART_IS_STOCKABLE_UNIT_DESC');?>" >
									<?php echo Text::_('COM_QUICK2CART_IS_STOCKABLE_UNIT')?>
								</b>
							</label>
						</td>
					</tr>
					<tr class="attributeTrHeading">
						<td width="33%" align="left" data-title="<?php echo Text::_('QTC_PROD_SEL_ATTRIBUTE');?>">
							<?php
							$selectedAttrSet = !empty($this->allAttribues[$i]->global_attribute_id) ? $this->allAttribues[$i]->global_attribute_id : "0";
							echo HTMLHelper::_('select.genericlist', $attributeSetOptions, "att_detail[". $i . "][global_attribute_set]",'class="form-select qtcAttriSetType" sutocomplete="off" style="" onChange="qtcLoadArribute(this.id, ' . $i . ', qtcJsData)" ','value','text',$selectedAttrSet, "qtcAttriSetTypeId" . (string) $i);
							?>
							<span class="attri_ajax_loading_<?php echo $i; ?>" style="display:none;">
								<img class="" src="<?php echo Uri::root() ?>components/com_quick2cart/assets/images/loadin16x16.gif" height="15" width="15">
							</span>
						</td>
						<td class="hidden-xs hidden-sm" width="33%"	align="left"></td>
						<td	align="left" data-title="<?php echo Text::_('COM_QUICK2CART_IS_STOCKABLE_UNIT');?>">
							<div class="<?php echo ($stockableAttrId || $qtc_stck_att  || $productType != 2) ? "qtc_hideEle" : ''?>">
									<?php
									$qtc_stck_att="";

									if (isset($this->allAttribues[$i]->is_stock_keeping) && $this->allAttribues[$i]->is_stock_keeping == 1)
									{
										$qtc_stck_att = ($this->allAttribues[$i]->is_stock_keeping) ? "checked" : "";
									}
									?>
									<!-- // (SHOW only varible product) OR If any of product attribute has one of stockable attribute then don't show checkbox  -->
									<label class="qtcAttrCheckbox  " title="<?php echo Text::_('COM_QUICK2CART_IS_STOCKABLE_UNIT_DESC');?>">
										<input type="checkbox" class="form-check-input qtc_is_stock_keeping <?php echo ($qtc_stck_att == "checked") ? "qtc_hideEle" : ''?>" id="att_detail[<?php echo $i; ?>][is_stock_keeping]" name="att_detail[<?php echo $i; ?>][is_stock_keeping]" autocomplete="off" <?php echo $qtc_stck_att;?> onchange="qtcOnChange_is_stock_keeping(this)">
									</label>

									<?php
									// Edit view: for stockable attribute-> hide checkbod and show badges
									if(($qtc_stck_att == "checked"))
									{
										?>
										<span class="badge af-bg-success"><?php echo Text::_("COM_QUICK2CART_STOCK_KEEPING_ATTR_BEDGE");?></span>
										<?php
									}
								?>
							</div>
						</td>
					</tr>
					<tr class="hidden-xs hidden-sm">
						<td width="33%" align="left">
							<b class="qtcAttriTabLabel"><?php echo Text::_( 'QTC_ADDATTRI_NAME' ); ?></b>
						</td>
						<td width="33%"	align="left">
							<b class="qtcfieldTypeTitle" style="<?php echo $hideFieldsForStockableAttribute ?>"><?php echo Text::_( 'QTC_ADDATTRI_FIELD_TYPE_TO_USE' ); ?></b>
						</td>
						<td align="left" style="<?php echo $hideFieldsForStockableAttribute ?>">
							<label for="att_detail[<?php echo $i; ?>][iscompulsary_attr]" class="isCompulsaryAttr">
								<b class="checkboxdivTitle" >
								<?php echo Text::_( 'QTC_ATT_COMPALSARY_CK' ); ?> </b>
							</label>
						</td>
					</tr>
					<tr>
						<td data-title="<?php echo Text::_('QTC_ADDATTRI_NAME');?>">
							<input id="atrri_name" name="att_detail[<?php echo $i; ?>][attri_name]" class="form-control input-medium bill qtc_attrib " type="text" value="<?php echo (isset($this->allAttribues[$i]->itemattribute_name)) ? htmlentities($this->allAttribues[$i]->itemattribute_name) : ''; ?>" maxlength="250"  title="<?php echo Text::_('QTC_ADDATTRI_NAME_DESC')?>">
							<!-- Hidded field -->
							<input id="atrri_name_id" name="att_detail[<?php echo $i; ?>][attri_id]" class="form-control input-medium bill " type="hidden" value="<?php echo (isset($this->allAttribues[$i]->itemattribute_id))?$this->allAttribues[$i]->itemattribute_id:''; ?>" maxlength="250"  title="<?php echo Text::_('QTC_ADDATTRI_NAME_DESC')?>">
						</td>
						<td data-title="<?php echo Text::_('QTC_ADDATTRI_FIELD_TYPE_TO_USE');?>" style="<?php echo $hideFieldsForStockableAttribute ?>">
							<?php
							$fields = array();
							$default =  !empty($this->allAttribues[$i]->attributeFieldType)? $this->allAttribues[$i]->attributeFieldType :"Select";
							$tableDisplay ='display:table';

							if ($default == 'Textbox')
							{
								$tableDisplay ='display:none';
							}

							$fields = $this->productHelper->getAttributeFieldsOption();
							$fnparam = "this,'".$attribute_container_id."'";
							?>

							<div>
								<?php
									echo HTMLHelper::_('select.genericlist', $fields, "att_detail[$i][fieldType]", 'class="form-select no_chzn qtcfieldType input-small" data-chosen="qtc" onChange="qtc_fieldTypeChange('.$fnparam.')"', "value", "text",$default);
								?>
							</div>
						</td>
						<td data-title="<?php echo Text::_('QTC_ATT_COMPALSARY_CK');?>" style="<?php echo $hideFieldsForStockableAttribute ?>">
							<label class="qtcAttrCheckbox" >
							<?php
								$qtc_ck_att="";

								if (isset($this->allAttribues[$i]->attribute_compulsary) && $this->allAttribues[$i]->attribute_compulsary == 1)
								{
									$qtc_ck_att=($this->allAttribues[$i]->attribute_compulsary)?"checked":"";
								}
							?>
								<input type="checkbox" class="form-check-input checkboxdiv " id="att_detail[<?php echo $i; ?>][iscompulsary_attr]" name="att_detail[<?php echo $i; ?>][iscompulsary_attr]" autocomplete="off" <?php echo $qtc_ck_att;?> >
							</label>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	<?php
	$k = 1;
	$lastkey_opt = $att_op_count ;
	?>
	<div class="qtcMarginTop10px">
		<table class="table table-condensed removeMargin qtc_attributeOpTable qtc-table " style="<?php echo $tableDisplay; ?>"><!-- the table width is fixed to 450px -->
			<thead >
				<tr >
					<th width="20%"><?php echo Text::_( 'QTC_ADDATTRI_OPTNAME' ); ?> </th>
					<th width="100px" align="left"><?php echo Text::_('QTC_PROD_STATUS'); ?> </th>
					<th width="7%" class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" ><?php echo Text::_('QTC_ADDATTRI_STOCK'); ?></th>
					<th width="10%" class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields"><?php echo Text::_('QTC_ADDATTRI_SKU'); ?></th>
					<th width="12%"	align="left"><?php echo Text::_( 'QTC_ADDATTRI_OPTPREFIX' ); ?></th>
					<th><?php echo Text::_( 'QTC_ADDATTRI_OPTVAL' ); ?> </th>
					<th width="10%"><?php echo Text::_( 'QTC_ADDATTRI_OPTORDER' ); ?></th>
					<th width="5%" ></th>
				</tr>
			</thead>
			<tbody>
				<?php
				$showAddBtn = 1;

				if (!empty($this->allAttribues[$i]->itemattribute_id) && $selectedAttrSet)
				{
					/* Dont show add btn for global attibure set or stockable  attribute */
					// Don't generate last option for stock keeping attibure
					$att_op_count = $att_op_count -1;
					$showAddBtn = 0;
				}

				for ($k = 0; $k <=$att_op_count ; $k++)
				{
					$childProdDetail = !empty($this->attribute_opt[$k]->child_product_detail) ? $this->attribute_opt[$k]->child_product_detail : new stdClass;
					echo '';
					?>
					<tr class="form-inline clonedInput" id="attri_opts<?php echo $k; ?>" >
						<td data-title="<?php echo Text::_('QTC_ADDATTRI_OPTNAME');?>">
							<input type="hidden" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][id]" value="<?php echo (!empty($this->attribute_opt[$k]->itemattributeoption_id))?$this->attribute_opt[$k]->itemattributeoption_id:''; ?>">
							<input type="hidden" class="qtcGlobalOptionId" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][globalOptionId]" value="<?php echo (!empty($this->attribute_opt[$k]->global_option_id)) ? $this->attribute_opt[$k]->global_option_id:''; ?>">
							<input type="hidden" class="" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][child_product_item_id]" value="<?php echo (!empty($this->attribute_opt[$k]->child_product_item_id)) ? $this->attribute_opt[$k]->child_product_item_id:''; ?>">
							<input type="text" class="form-control input-small qtcOptionNameClass" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][name]" placeholder="<?php echo Text::_('QTC_ADDATTRI_OPTNAME')?>" value="<?php echo (isset($this->attribute_opt[$k]->itemattributeoption_name))? htmlentities($this->attribute_opt[$k]->itemattributeoption_name):''; ?>">
						</td>
						<td data-title="<?php echo Text::_('QTC_PROD_STATUS');?>">
							<?php
							$option_status = array();
							$option_status[] = HTMLHelper::_('select.option',1, Text::_('QTC_PROD_PUBLISH'));
							$option_status[] = HTMLHelper::_('select.option',0, Text::_('QTC_PROD_UNPUBLISH'));

							$defOptionStatus = (isset($this->attribute_opt[$k]->state))?$this->attribute_opt[$k]->state: 1;
							echo HTMLHelper::_('select.genericlist', $option_status, "att_detail[$i][attri_opt][$k][state]", 'class="form-select chzn-done qtc-attribute-option-state"', "value", "text", $defOptionStatus);
							?>
						</td>
						<td class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" data-title="<?php echo Text::_('QTC_ADDATTRI_STOCK');?>">
							<!-- For stockable attribute - sku and stock field -->
							<input type="number" class="qtcAttriOptionStock form-control" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][stock]" Onkeyup="" onchange="" id=""   placeholder="<?php echo Text::_('QTC_ADDATTRI_STOCK')?>" value="<?php echo isset($childProdDetail->stock) ? $childProdDetail->stock : '' ?>">
						</td>
						<td class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" data-title="<?php echo Text::_('QTC_ADDATTRI_SKU');?>">
							<input type="text" class="qtcAttriOptionSku form-control" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][sku]" Onkeyup="" onchange="" id="" class=""  placeholder="<?php echo Text::_('QTC_ADDATTRI_SKU')?>" value="<?php echo !empty($childProdDetail->sku) ? $childProdDetail->sku : '' ?>">
						</td>
						<td data-title="<?php echo Text::_('QTC_ADDATTRI_OPTPREFIX');?>">
							<?php
							$addpre_val = (isset($this->attribute_opt[$k]->itemattributeoption_prefix))?$this->attribute_opt[$k]->itemattributeoption_prefix:'';
							echo HTMLHelper::_('select.genericlist', $addpre_select, "att_detail[$i][attri_opt][$k][prefix]", 'class="form-select chzn-done qtc-attribute-option-prefix" data-chosen="qtc"', "value", "text", $addpre_val);
							?>
						</td>
						<td data-title="<?php echo Text::_('QTC_ADDATTRI_OPTVAL');?>">
						<?php	$currencies     = $this->params->get('addcurrency');
							$curr           = explode(',',$currencies);
							$currencies_sym = $this->params->get('addcurrency_sym');

							if (!empty($currencies_sym) )
							{
								$curr_syms=explode(',',$currencies_sym);
							}
							?>
							<div class='qtc_currencey_textbox'>
								<?php $quick2cartModelAttributes =  new quick2cartModelAttributes();
								foreach($curr as $currKey=>$value)    // key contain 0,1,2... // value contain INR...
								{
									$currtext   = $value;
									$currvalue  = array();
									$storevalue = "";

									if (isset($this->attribute_opt[$k] ))
									{
										$currvalue = $quick2cartModelAttributes->getOption_currencyValue($this->attribute_opt[$k]->itemattributeoption_id,$value);
										$storevalue = (isset($currvalue[0]['price']))?$currvalue[0]['price'] : '';
									}

									if (!empty($curr_syms[$currKey]))
									{
										$currtext = $curr_syms[$currKey];
									}?>
									<div class="input-group curr_margin " >
										<input type='text' name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][currency][<?php echo $value; ?>]"  id='' value="<?php echo ((isset($currvalue[0]['price']))?$currvalue[0]['price'] : ''); ?>" class="col-md-2 currtext form-control" Onkeyup="checkforalpha(this,46,<?php echo $entered_numerics; ?>);">
										<div class="input-group-text "><?php echo $currtext; ?></div>
									</div>
								<?php
								}
								?>
							</div>
						</td>
						<td data-title="<?php echo Text::_('QTC_ADDATTRI_OPTORDER');?>">
							<input type="text" name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][order]" Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>);" onchange="qtc_ispositive(this)"	 id="" class="qtc-attribute-Option-order form-control"  placeholder="<?php echo Text::_('QTC_ADDATTRI_OPTORDER')?>" value="<?php echo (isset($this->attribute_opt[$k]->ordering))?$this->attribute_opt[$k]->ordering:1; ?>">
						</td>
						<td class="qtcAddRmBTD">
						<?php
						/* Dont show add btn for stockable attribute */
						if ($k == $lastkey_opt && $showAddBtn)
						{ ?>
							<button type="button"  class="btnAdd btn btn-sm btn-primary" id="<?php echo "qtc_container". $i;?>" title="<?php echo Text::_('QTC_ADD_MORE_OPTION_TITLE')?>" onclick="addopt(this.id);">
								<i class="<?php echo QTC_ICON_PLUS;?> icon22-white22"></i>
							</button>
							<?php
						}
						else
						{
							?>
							<button type="button" class="btn btn-sm btn-danger qtcRemoveOption" id="btnRemove<?php echo $k; ?>" title="<?php echo Text::_('QTC_REMOVE_MORE_OPTION_TITLE');?>" onclick="techjoomla.jQuery(this).closest('tr').remove();" >
								<i class="<?php echo Q2C_ICON_TRASH;?> icon22-white22"></i>
							 </button>
							<?php
						}
						?>
						</td>
					</tr>
					<?php 
				}?>
			</tbody>
		</table>

		<div class="">
			<?php
				$hideAddGlobalOptionFields = 1;

				if ($selectedAttrSet && $this->allAttribues[$i]->global_attribute_id)
				{
					$hideAddGlobalOptionFields = 0;
				}
			?>
			<div class="qtcGlobalOptionsList" style="<?php echo !empty($hideAddGlobalOptionFields) ? "display:none;" : '' ?>">
				<?php
				// Get global options
				$goptions = $this->productHelper->getGlobalAttriOptions($this->allAttribues[$i]->global_attribute_id);

				// Generate option select box
				$layout = new FileLayout('addproduct.attribute_global_options');
				echo $layout->render($goptions);
				?>
			</div>
		</div>
	</div>
</div>
<?php echo HTMLHelper::_( 'form.token' ); ?>
