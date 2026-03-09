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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$session = Factory::getSession();
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

$document = Factory::getDocument();
$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

if (!class_exists('comquick2cartHelper'))
{
  // Require_once $path;
   JLoader::register('comquick2cartHelper', $path);
   JLoader::load('comquick2cartHelper');
}

$comquick2cartHelper = new comquick2cartHelper;
$productHelper = new productHelper;

// Load component models
BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_activitystream/models');
$Quick2cartModelcart = BaseDatabaseModel::getInstance('cart', 'Quick2CartModel');

// Load Assets which are require for quick2cart.
$comquick2cartHelper->loadQuicartAssetFiles();

$pid = $this->product_id;
$parent = $this->parent;
$stock = $this->stock;
$entered_numerics= '"' . Text::_('QTC_ENTER_NUMERICS') . '"';

//  Here if min  and max qty is not present then we  assign it to min=1 and max=999
$slab = (!empty($this->slab)) ? $this->slab : 1;
$min_qty = (!empty($this->min_quantity))?$this->min_quantity:1;
$max_qty = (!empty($this->max_quantity))?$this->max_quantity:999;
$this->product_id = $this->parent."-".$this->product_id;

require_once JPATH_SITE . '/components/com_quick2cart/defines.php';
?>
<?php
	$params = ComponentHelper::getParams('com_quick2cart');

if ($this->showBuyNowBtn)
{
?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?> container-fluid" >
		<div class="form-horizontal qtc_buynow" id="<?php echo $this->product_id;?>_item" style="width:auto;">
			<?php
			$discount_present = ($params->get('usedisc') && isset($this->price['discount_price']) && !is_null($this->price['discount_price'])) ? 1 :0;

			if (empty($this->qtcExtraParam['hideOriginalPrice']))
			{
			?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 control-label qtc-label-original_price">
						<strong><?php echo Text::_('QTC_ITEM_AMT')?></strong>
					</label>
					<div class="col-xs-12 col-sm-8 qtc_controls_text qtc-field-original_price">
						<span id="<?php echo ((isset($this->price['discount_price'])) ? $this->product_id.'_price' :'');?>" >
							<?php $pprice = (($discount_present==1)  ? '<del>'.$comquick2cartHelper->getFromattedPrice($this->price['price']).'</del>':$comquick2cartHelper->getFromattedPrice($this->price['price']));
							echo $pprice;?>
						</span>
					</div>
				</div>
			<?php
			}

			if ($discount_present && empty($this->qtcExtraParam['hideDiscountPrice']))
			{ ?>
				<div class="form-group">
					<label class="col-xs-12 col-sm-4 control-label qtc-label-discount_price">
						<strong><?php echo Text::_('QTC_ITEM_DIS_AMT');?></strong>
					</label>
					<div class="col-xs-12 col-sm-8 qtc_controls_text qtc-field-discount_price">
						<span id="<?php echo $this->product_id;?>_price" >
							<?php echo $comquick2cartHelper->getFromattedPrice($this->price['discount_price']);?>
						</span>
					</div>
				</div>
				<?php
			} ?>
			<?php
			// Display Attributes
			if ($this->attributes && empty($this->qtcExtraParam['hideDiscountPrice']))
			{
				foreach($this->attributes as $attribute)
				{ ?>
					<div class="form-group">
						<label class="col-xs-12 col-sm-4 control-label qtc-label-attribute">
							<strong><?php echo $attribute->itemattribute_name; ?></strong>
						</label>
						<?php
							$productHelper                = new productHelper ;
							$data['itemattribute_id']     = $attribute->itemattribute_id;
							$data['fieldType']            = $attribute->attributeFieldType;
							$data['parent']               = $parent;
							$data['product_id']           = $pid;
							$data['attribute_compulsary'] = $attribute->attribute_compulsary;
							$data['attributeDetail']      = $attribute;

							$layout    = new FileLayout('productpage.attribute_option_display', null, array('component' => 'com_quick2cart'));
							$fieldHtml = $layout->render($data);
						?>
						<div class="col-xs-12 col-sm-8 qtc-field-attribute">
							<?php  echo $fieldHtml; ?>
						</div>
					</div>
				<?php
				}
			}

			// Don't Show media file if you found qtcFreeDdownloads=true.
			if (!empty($this->mediaFiles))
			{
				$hideAtt = !empty($this->qtcExtraParam['hideAttributes']) ? 'qtc_hideEle' : '' ;
			?>
				<div class="form-group <?php echo $hideAtt ?>" >
					<div class="col-xs-12 col-sm-4 control-label qtc-label-free_download">
						<strong><?php echo Text::_("COM_QUICK2CART_PROD_FREE_DOWNLOAD"); ?></strong>
					</div>
					<div class="col-xs-12 col-sm-8 qtc_padding_class_attributes qtc-field-free_download">
						<?php
						$productHelper = new productHelper;

						foreach($this->mediaFiles as $mediaFile)
						{
							$linkData = array();
							$linkData['linkName']     = $mediaFile['file_display_name'];
							$linkData['href']         = $productHelper->getMediaDownloadLinkHref($mediaFile['file_id']);
							$linkData['event']        = '';
							$linkData['functionName'] = '';
							$linkData['fnParam']      = '';
							echo $productHelper->showMediaDownloadLink($linkData) ."<br>";
						}
						?>
						</br>
					</div>
				</div>
			<?php
			}

			$showqty       = $params->get('qty_buynow',1);
			$showqty_style = (empty($showqty)) ? "display:none;" : "";
			?>
			<div class="form-group" style="<?php echo $showqty_style; ?>">
				<label class="col-xs-12 col-sm-4 control-label qtc-label-itemcount">
					<strong><?php echo Text::_('QTC_ITEM_QTY'); ?></strong>
				</label>
				<div class="col-xs-12 col-sm-8 qtc-field-itemcount">
					<?php
					$textboxid=$this->product_id."_itemcount" ;

					if (is_numeric($stock) && $stock < $max_qty )
					{
						$max_qty = $stock;
					}

					$limits  = $min_qty .",".$max_qty ;
					$arg     = "'" . $textboxid . "','" . $pid . "','" . $parent . "','" . $slab . "'," . $limits;
					$min_msg = Text::_('QTC_MIN_LIMIT_MSG');
					$max_msg = Text::_('QTC_MAX_LIMIT_MSG');
					?>
					<input
						id="<?php echo $textboxid;?>"
						name="<?php echo $this->product_id;?>_itemcount"
						class="input input-mini qtc_count"
						type="text"
						value="<?php echo $min_qty;?>"
						maxlength="3"
						onblur="checkforalphaLimit(this,'<?php echo $pid;?>','<?php echo $parent;?>',<?php echo $slab;?>,<?php echo $limits;?>,'<?php echo $min_msg;?>','<?php echo $max_msg;?>');"
						Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>)">

					<span class="qtc_itemcount" >
						<input type="button" onclick="qtc_increment(<?php echo $arg;?>)"  class="qtc_icon-qtcplus">
						<input type="button" onclick="qtc_decrement(<?php echo $arg;?>)" class="qtc_icon-qtcminus">
					</span>
					<button class="btn btn-sm btn-success qtc_buyBtn_style" type="button" onclick="qtc_addtocart('<?php echo $this->product_id; ?>');">
						<i class="<?php echo QTC_ICON_CART;?>"></i><?php echo Text::_('QTC_ITEM_BUY');?>
					</button>
				</div>
			</div>

			<?php
			if (empty($showqty))
			{  ?>
				<div class="col-xs-12 col-sm-8">
					<button class="btn btn-sm btn-primary qtc_buyBtn_style" type="button" onclick="qtc_addtocart('<?php echo $this->product_id; ?>');">
						<i class="<?php echo QTC_ICON_CART;?>"></i> <?php echo Text::_('QTC_ITEM_BUY') ;?>
					</button>
				</div>
			<?php
			}

			// Get pop up style
			$popup_buynow = $params->get('popup_buynow', 1);

			if ($popup_buynow == 2)
			{
				$checkout    = 'index.php?option=com_quick2cart&view=cart';
				$itemid      = $comquick2cartHelper->getitemid($checkout);
				$action_link = Uri::root().substr(Route::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid='.$itemid,false),strlen(Uri::base(true))+1);
				?>
				<div class="cart-popup" id="<?php echo $this->product_id; ?>_popup" style="display: none;">
					<div class="message"></div>
					<div class="cart_link">
						<a class="btn btn-success" href="<?php echo $action_link; ?>">
							<?php echo Text::_('COM_QUICK2CART_VIEW_CART')?>
						</a>
					</div>
					<i class="<?php echo QTC_ICON_REMOVE; ?> cart-popup_close" onclick="techjoomla.jQuery(this).parent().slideUp().hide();"></i>
				</div>
				<?php
			}

			if(isset($this->extra_field_data) && count($this->extra_field_data))
			{ ?>
				<div>
				<?php
					foreach($this->extra_field_data as $f)
					{
						if(!empty($f->value))
						{
							?>
							<div class="form-group">
								<label class="col-xs-12 col-sm-4 control-label">
									<strong><?php echo $f->label;?></strong>
								</label>
								<?php
								if (!is_array($f->value))
								{?>
									<div class="col-xs-12 col-sm-8">
										<span>
											<?php echo $f->value;?>
										</span>
									</div>
								<?php
								}
								else
								{
									foreach($f->value as $option)
									{ ?>
										<div class="col-xs-12 col-sm-8">
											<span>
												<?php echo $option->options;?>
											</span>
										</div>
										<br/>
									<?php
									}
								} ?>
							</div>
						<?php
						}
					} ?>
				</div>
			<?php
			}

			// For cck products
			if (empty($productDetailsUrl))
			{
				$item = array();
				$item['id'] = $pid;
				$item['parent'] = $this->parent;
				$item['count'] = '';
				$item['options'] = '';

				$prod_details = $Quick2cartModelcart->getProd($item);
				$item_id = $prod_details[0]['item_id'];

				if (!empty($item_id))
				{
					$productDetailsUrl = $comquick2cartHelper->getProductLink($item_id);
				}
			}

			PluginHelper::importPlugin('discounts');
			$shareButtonHtml = Factory::getApplication()->triggerEvent('onGetDiscountHtml',array($productDetailsUrl));

			if (isset($shareButtonHtml[0]))
			{
				echo $shareButtonHtml[0];
			}
			?>
		</div>
	</div>
<?php
}
else
{
?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >
		<div class="alert alert-warning">
			<button type="button" class="close" data-dismiss="alert"></button>
			<strong><?php echo Text::_('QTC_WARNING'); ?></strong>
			<?php echo Text::_('QTC_OUT_OF_STOCK_MSG'); ?>
		</div>
	</div>
<?php
}
?>
