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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Uri\Uri;

$entered_numerics = "'".Text::_('QTC_ENTER_NUMERICS')."'";
$addpre_select    = array();
$addpre_select[]  = HTMLHelper::_('select.option','+', Text::_('QTC_ADDATTRI_PREADD'));
$addpre_select[]  = HTMLHelper::_('select.option','-', Text::_('QTC_ADDATTRI_PRESUB'));

$pid                 = $jinput->get('pid',0,'INTEGER');
$client              = $jinput->get( 'client','','STRING');
$this->attribute_opt = array();
$attri_model         = new quick2cartModelAttributes();

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
<div class="container">
	<div class="row">
		<div class="col-sm-4">
			<label class="form-label" title="<?php echo Text::_('QTC_PROD_SEL_ATTRIBUTE_TOOLTIP');?>">
				<b><?php echo Text::_('QTC_PROD_SEL_ATTRIBUTE'); ?></b>
			</label>
			<?php
				$selectedAttrSet = !empty($this->allAttribues[$i]->global_attribute_id) ? $this->allAttribues[$i]->global_attribute_id : 0;
				echo HTMLHelper::_('select.genericlist', $attributeSetOptions, "att_detail[". $i . "][global_attribute_set]",'class=" qtcAttriSetType noWidthApply form-select" style="" onChange="qtcLoadArribute(this.id, ' . $i . ', qtcJsData)" ','value','text',$selectedAttrSet, "qtcAttriSetTypeId" . (string) $i);
			?>
			<span class="attri_ajax_loading_<?php echo $i; ?>" style="display:none;">
				<img class="" src="<?php echo Uri::root() ?>components/com_quick2cart/assets/images/loadin16x16.gif" height="15" width="15">
			</span>
		</div>
		<div class="col-sm-4"></div>
		<div class="col-sm-4">
		<label class="checkbox <?php echo ($stockableAttrId || $qtc_stck_att  || $productType != 2) ? 'qtc_hideEle' : ''?>" title="<?php echo Text::_('COM_QUICK2CART_IS_STOCKABLE_UNIT_DESC');?>">
				<b class="">
					<?php echo Text::_('COM_QUICK2CART_IS_STOCKABLE_UNIT')?>
				</b>
				<?php
				$qtc_stck_att="";

				if (isset($this->allAttribues[$i]->is_stock_keeping) && $this->allAttribues[$i]->is_stock_keeping == 1)
				{
					$qtc_stck_att = ($this->allAttribues[$i]->is_stock_keeping) ? "checked" : "";
				}
				?>
				<input
					type="checkbox"
					class="qtc_is_stock_keeping <?php echo ($qtc_stck_att == "checked") ? "qtc_hideEle" : ''?>"
					name="att_detail[<?php echo $i; ?>][is_stock_keeping]"
					autocomplete="off" <?php echo $qtc_stck_att;?>
					onchange="qtcOnChange_is_stock_keeping(this)">
			</label>


			<?php
				// Edit view: for stockable attribute-> hide checkbod and show badges
				if(($qtc_stck_att == "checked"))
				{
					?>
					<span class="badge af-bg-success">
						<?php echo Text::_("COM_QUICK2CART_STOCK_KEEPING_ATTR_BEDGE");?>
					</span>
					<?php
				}
			?>
		</div>
	</div>
	<div class="row qtcMarginTop10px">
		<div class="col-sm-4">
			<label class="form-label qtcAttriTabLabel" ><b><?php echo Text::_('QTC_ADDATTRI_NAME'); ?></b></label>
			<input
				id="atrri_name"
				name="att_detail[<?php echo $i; ?>][attri_name]"
				class="form-control input-medium bill inputbox qtc_attrib qtcAttrNameClass"
				type="text"
				value="<?php echo (isset($this->allAttribues[$i]->itemattribute_name)) ? htmlentities($this->allAttribues[$i]->itemattribute_name):''; ?>"
				maxlength="250"
				title="<?php echo Text::_('QTC_ADDATTRI_NAME_DESC')?>">
			<input
				id="atrri_name_id"
				name="att_detail[<?php echo $i; ?>][attri_id]"
				class="input-medium bill inputbox"
				type="hidden"
				value="<?php echo (isset($this->allAttribues[$i]->itemattribute_id))?$this->allAttribues[$i]->itemattribute_id:''; ?>" >
			<input
				type="hidden"
				name="att_detail[<?php echo $i; ?>][global_atrri_id]"
				class="global_atrri_idClass"
				value="<?php echo (isset($this->allAttribues[$i]->global_attribute_id)) ? $this->allAttribues[$i]->global_attribute_id:''; ?>" >
		</div>
		<div class="col-sm-4">
			<label class="form-label">
				<b class="qtcfieldTypeTitle" style="<?php echo $hideFieldsForStockableAttribute ?>">
					<?php echo Text::_('QTC_ADDATTRI_FIELD_TYPE_TO_USE')?>
				</b>
			</label>
			<?php
				$fields       = array();
				$default      = (!empty($this->allAttribues[$i]->attributeFieldType)) ? $this->allAttribues[$i]->attributeFieldType :"Select";
				$tableDisplay = ($default == 'Textbox') ? 'display:none' : 'display:table';
				$fields       = $productHelper->getAttributeFieldsOption();
				$fnparam      = "this,'".$attribute_container_id."'";
			?>
			<div style="<?php echo $hideFieldsForStockableAttribute ?>">
				<?php echo HTMLHelper::_('select.genericlist', $fields, "att_detail[$i][fieldType]", 'class="form-select no_chzn qtcfieldType span6" onChange="qtc_fieldTypeChange('.$fnparam.')"', "value", "text",$default);?>
			</div>
		</div>
		<div class="col-sm-4">
			<label class="checkbox " style="<?php echo $hideFieldsForStockableAttribute ?>">
				<b class="checkboxdivTitle" style="<?php echo $hideFieldsForStockableAttribute ?>">
					<?php echo Text::_('QTC_ATT_COMPALSARY_CK')?>
				</b>
				<?php
				$qtc_ck_att="";

				if (isset($this->allAttribues[$i]->attribute_compulsary) && $this->allAttribues[$i]->attribute_compulsary == 1)
				{
					$qtc_ck_att = ($this->allAttribues[$i]->attribute_compulsary)?"checked":"";
				}
				?>
				<input
					type="checkbox"
					class="checkboxdiv"
					name="att_detail[<?php echo $i; ?>][iscompulsary_attr]"
					autocomplete="off" <?php echo $qtc_ck_att;?> >
			</label>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12 ">
		<div class="clearfix"></div>
		<?php
		$k = 1;
		$lastkey_opt = $att_op_count ;
		?>
		<div class="qtcMarginTop10px">
			<table class="table table-condensed removeMargin qtc_attributeOpTable" style="<?php echo $tableDisplay; ?>">
				<thead>
					<tr>
						<th width="30%" align="left">
							<?php echo Text::_( 'QTC_ADDATTRI_OPTNAME' ); ?>
						</th>
						<th width="100px" align="left">
							<?php echo Text::_('QTC_PROD_STATUS'); ?>
						</th>
						<th class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" width="80px" align="left">
							<?php echo Text::_('QTC_ADDATTRI_STOCK'); ?>
						</th>
						<th class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields" width="40px" align="left">
							<?php echo Text::_('QTC_ADDATTRI_SKU'); ?>
						</th>
						<th width="10%" align="left"><?php echo Text::_('QTC_ADDATTRI_OPTPREFIX');?></th>
						<th width="40%" align="left"><?php echo Text::_('QTC_ADDATTRI_OPTVAL');?></th>
						<th width="10%" align="left"><?php echo Text::_('QTC_ADDATTRI_OPTORDER');?></th>
						<th width="5%"  align="left"></th>
					</tr>
				</thead>
				<tbody>
					<?php
					$showAddBtn = 1;

					if (!empty($this->allAttribues[$i]->itemattribute_id))
					{
						/* Dont show add btn for global attibure set or stockable  attribute */
						if ($selectedAttrSet)
						{
							// Don't generate last option for stock keeping attibure
							$att_op_count = $att_op_count -1;
							$showAddBtn = 0;
						}
					}

					for ($k = 0; $k <=$att_op_count ; $k++)
					{
						$childProdDetail = !empty($this->attribute_opt[$k]->child_product_detail) ? $this->attribute_opt[$k]->child_product_detail : new stdClass;
						echo '';
						?>
						<tr class="form-inline clonedInput" id="attri_opts<?php echo $k; ?>" >
							<td>
								<input
									type="hidden"
									name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][id]"
									value="<?php echo (!empty($this->attribute_opt[$k]->itemattributeoption_id))?$this->attribute_opt[$k]->itemattributeoption_id:''; ?>">
								<input
									type="hidden"
									class="qtcGlobalOptionId"
									name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][globalOptionId]"
									value="<?php echo (!empty($this->attribute_opt[$k]->global_option_id)) ? $this->attribute_opt[$k]->global_option_id:''; ?>">
								<input
									type="hidden"
									class=""
									name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][child_product_item_id]"
									value="<?php echo (!empty($this->attribute_opt[$k]->child_product_item_id)) ? $this->attribute_opt[$k]->child_product_item_id:''; ?>">
								<input
									type="text"
									class="form-control input-small qtcOptionNameClass"
									name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][name]"
									placeholder="<?php echo Text::_('QTC_ADDATTRI_OPTNAME')?>"
									value="<?php echo (isset($this->attribute_opt[$k]->itemattributeoption_name))? htmlentities($this->attribute_opt[$k]->itemattributeoption_name):''; ?>">
							</td>
							<td>
								<?php
								$option_status = array();
								$option_status[] = HTMLHelper::_('select.option',1, Text::_('QTC_PROD_PUBLISH'));
								$option_status[] = HTMLHelper::_('select.option',0, Text::_('QTC_PROD_UNPUBLISH'));
								$defOptionStatus = (isset($this->attribute_opt[$k]->state))?$this->attribute_opt[$k]->state:'';
								echo HTMLHelper::_('select.genericlist', $option_status, "att_detail[$i][attri_opt][$k][state]", 'class="form-select chzn-done input-medium"', "value", "text", $defOptionStatus);
								?>
							</td>
							<td class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields">
								<!-- For stockable attribute - sku and stock field -->
								<input
									type="number"
									class="input-mini qtcAttriOptionStock form-control"
									name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][stock]"
									Onkeyup=""
									onchange=""
									id=""
									placeholder="<?php echo Text::_('QTC_ADDATTRI_STOCK')?>"
									value="<?php echo isset($childProdDetail->stock) ? $childProdDetail->stock : '' ?>">
							</td>
							<td class="<?php echo $stockRelatedFildClass; ?> qtcStockSkufields">
								<input
									type="text"
									class="input-medium	 qtcAttriOptionSku form-control"
									name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][sku]"
									Onkeyup=""
									onchange=""
									id=""
									class=""
									placeholder="<?php echo Text::_('QTC_ADDATTRI_SKU')?>"
									value="<?php echo !empty($childProdDetail->sku) ? $childProdDetail->sku : '' ?>">
							</td>
							<td>
								<?php
								$addpre_val = (isset($this->attribute_opt[$k]->itemattributeoption_prefix))?$this->attribute_opt[$k]->itemattributeoption_prefix:'';
								echo HTMLHelper::_('select.genericlist', $addpre_select, "att_detail[$i][attri_opt][$k][prefix]", 'class="form-select chzn-done input-mini" data-chosen="qtc"', "value", "text", $addpre_val);
							?>
							</td>
							<td>
								<?php
								$currencies     = $params->get('addcurrency');
								$curr           = explode(',',$currencies);
								$currencies_sym = $params->get('addcurrency_sym');

								if (!empty($currencies_sym) )
								{
									$curr_syms=explode(',',$currencies_sym);
								}
								?>
								<div class='control-group'  >
									<span class='qtc_currencey_textbox input-append '>
									<?php $quick2cartModelAttributes =  new quick2cartModelAttributes();

										foreach($curr as $currKey => $value)
										{
											$currvalue  = array();
											$storevalue = "";

											if (isset($this->attribute_opt[$k] ))
											{
												$currvalue = $quick2cartModelAttributes->getOption_currencyValue($this->attribute_opt[$k]->itemattributeoption_id,$value);
												$storevalue=(isset($currvalue[0]['price']))?$currvalue[0]['price'] : '';
											}

											$currtext = (!empty($curr_syms[$currKey])) ? $curr_syms[$currKey] : $value;
											?>
											<div class="input-group curr_margin " >
												<input
													type='text'
													name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][currency][<?php echo $value; ?>]"
													size='1'
													id=''
													value="<?php echo ((isset($currvalue[0]['price']))?$currvalue[0]['price'] : ''); ?>"
													class="span1 currtext controls form-control"
													Onkeyup="checkforalpha(this,46,<?php echo $entered_numerics; ?>);">
												<span class="input-group-text"><?php echo $currtext; ?></span>
											</div>
											<?php
										}
									?>
									</span>
								</div>
							</td>
							<td>
								<input
									type="text"
									name="att_detail[<?php echo $i; ?>][attri_opt][<?php echo $k; ?>][order]"
									Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>);"
									onchange="qtc_ispositive(this)"
									id=""
									class="span1 qtc-attribute-Option-order form-control"
									placeholder="<?php echo Text::_('QTC_ADDATTRI_OPTORDER')?>"
									value="<?php echo (isset($this->attribute_opt[$k]->ordering))?$this->attribute_opt[$k]->ordering:1; ?>">
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
										<i class="fa fa-trash icon22-white22"></i>
									</button>
									<?php
								}
								?>
							</td>
						</tr>
						<?php
					} ?>
				</tbody>
			</table>
			<div class="qtc_float_right">
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
						// https://docs.joomla.org/J3.x:JLayout_Improvements_for_Joomla!
						$goptions = $this->productHelper->getGlobalAttriOptions($this->allAttribues[$i]->global_attribute_id);

						// Generate option select box
						$layout = new FileLayout('addproduct.attribute_global_options',  null, array('client' => 0, 'component' => 'com_quick2cart'));
						echo $layout->render($goptions);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo HTMLHelper::_('form.token'); ?>
