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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

HTMLHelper::_('bootstrap.renderModal');
Text::script('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_SUCCESS', true);
Text::script('COM_QUICK2CART_CHECKOUT_ITEM_UPDTATED_FAIL', true);

// Get cart details to check if product is already in the cart
if (!isset($cartItems))
{
	JLoader::import('cartcheckout', JPATH_SITE . '/components/com_quick2cart/models');
	$cartCheckoutModel = new Quick2cartModelcartcheckout;

	// Get cart details
	static $cartItems       = array();
	static $cartItemsQty    = array();
	static $itemsCartItemId = array();
	$cartItems              = $cartCheckoutModel->getCheckoutCartitemsDetails();

	foreach ($cartItems as $cartItem)
	{
		$key                   = !empty($cartItem['variant_item_id']) ? $cartItem['variant_item_id'] : $cartItem['item_id'];
		$cartItemsQty[$key]    = $cartItem['qty'];
		$itemsCartItemId[$key] = $cartItem['id'];
	}
}

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);
$params           = ComponentHelper::getParams('com_quick2cart');
$helperobj        = new comquick2cartHelper;
$q2cbaseUrl       = $helperobj->quick2CartRoute('index.php?option=com_quick2cart&view=category&layout=default');
$curr             = $helperobj->getCurrencySession();
$entered_numerics = "'" . Text::_('QTC_ENTER_NUMERICS') . "'";
$path             = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';

if (!class_exists('quick2cartModelAttributes'))
{
	JLoader::register('quick2cartModelAttributes', $path);
	JLoader::load('quick2cartModelAttributes');
}

$quick2cartModelAttributes = new quick2cartModelAttributes;
$item_id                   = (is_object($data)) ? $data->item_id : $data['item_id'];
$productHelper             = new productHelper;
$itemDetailObj             = (object) $data;

// For attribute based stock get attribute details
$completeAttrDetail = $productHelper->getItemCompleteAttrDetail($itemDetailObj->item_id);

if (!empty($completeAttrDetail))
{
	$itemDetailObj->itemAttributes = $completeAttrDetail;
}

// Check whether product is allowd to buy or not. ( our of stock)
$qtcTeaserShowBuyNowBtn = $productHelper->isInStockProduct($itemDetailObj);
$disableBuyBtn          = (empty($qtcTeaserShowBuyNowBtn)) ? '" disabled=disabled "' : "";
$prodAttDetails         = $productHelper->getProdPriceWithDefltAttributePrice($item_id);
$it_price               = $prodAttDetails;

if (isset($it_price['itemdetail']))
{
	$item_price = $it_price['itemdetail'];
}

$store_owner = (!empty($store_list) && in_array($data['store_id'], $store_list)) ? 1 : '';

// class for publish and unpublish icon --- used further

$publish   = QTC_ICON_CHECKMARK;
$unpublish = QTC_ICON_REMOVE;

if (!empty($store_owner))
{
	$itemstate = $data['state'];
}

// GETTING ALL PRODUCTS ATTRIBURES
$attribure_option_ids = $prodAttDetails['attrDetail']['attrOptionIds'];
$tot_att_price        = $prodAttDetails['attrDetail']['tot_att_price'];
$classes              = !empty($classes) ? $classes : '';
$prodivsize           = !empty($prodivsize) ? $prodivsize : 'default_product_div_size';
$img_width            = $params->get('medium_width', 120);
$showAddToCartBtn     = $params->get('show_addtocart_on_pin', '0', 'INT');
?>
<div class="qtc-prod-pin-inner mt-3 rounded-1">
	<div class="qtc-prod-pin-header">
		<?php $product_link = $helperobj->getProductLink($data['item_id'], 'detailsLink');

		if (isset($data['featured']) && $data['featured'] == 1)
		{
			?>
			<div class="qtc-prod-tag-cover <?php if ($data['featured']=='1') {echo 'qtc-feat-prod-visible';} ?>">
				<span href="#" class="qtc-prod-tag" title="<?php echo  Text::_('COM_QUICK2CART_FEATURED_PRODUCT') ?>">
					<?php echo  Text::_('COM_QUICK2CART_FEATURED_PRODUCT') ?>
				</span>
				<div class="clear-fix"></div>
			</div>
			<?php
		}

		$prodname = $data['name'];
		?>
	</div>
	<?php
	$images = json_decode($data['images'], true);
	$img    = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';

	if (!empty($images))
	{
		// Get first key
		$firstKey = 0;

		foreach ($images as $key=>$img)
		{
			$firstKey = $key;

			break;
		}

		require_once(JPATH_SITE . '/components/com_quick2cart/helpers/media.php');

		// create object of media helper class
		$media                       = new qtc_mediaHelper();
		$file_name_without_extension = $media->get_media_file_name_without_extension($images[$firstKey]);
		$media_extension             = $media->get_media_extension($images[$firstKey]);
		$img                         = $helperobj->isValidImg($file_name_without_extension.'_L.'.$media_extension);

		if (empty($img))
		{
			$img = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';
		}
	}
	?>

	<div class="qtc-prod-img-cover mt-2 <?php if (empty($qtcTeaserShowBuyNowBtn)){ echo 'poos';} ?>">
		<a title="<?php echo htmlentities($data['name']);?>" href="<?php echo $product_link; ?>">
			<?php
			if ($layout_to_load == "fixed_layout")
			{
			?>
				<div class="qtc-prod-img" style="background-image: url('<?php echo htmlentities($img) ; ?>'); height:<?php echo !empty($pinHeight) ? $pinHeight : 200;?>px"></div>
			<?php
			}
			else
			{
				?>
				<img class=' img-rounded q2c_pin_image'
					src="<?php echo $img;?>"
					alt="<?php echo  Text::_('QTC_IMG_NOT_FOUND') ?>"
					title="<?php echo $data['name'];?>" />
			<?php
			} ?>
		</a>
	</div>
	<div class="qtc-prod-footer-cover">
		<div class="qtc-prod-name-cover mt-1">
			<strong>
				<a title="<?php echo htmlentities($data['name']);?>" href="<?php echo $product_link; ?>" class="qtc-cv-prod-name">
					<?php echo $prodname;?>
				</a>
			</strong>
		</div>
		<div class="qtc-prod-price-cover mb-2 mt-2">
			<?php
			$discount_present = ($params->get('usedisc') && isset($item_price['discount_price']) && !is_null($item_price['discount_price'])) ? 1 : 0;
			$p_price = (!is_null($item_price['discount_price'])) ? $item_price['discount_price'] : $item_price['price'];

			if ($discount_present == 1)
			{
			?>
				<span class="qtc-offer-price">
					<small><del><?php echo $helperobj->getFromattedPrice($item_price['price']);?></del></small>
				</span>
			<?php
			}
			?>
			<span class='qtcproductprice'>
				<b><?php echo $helperobj->getFromattedPrice($p_price + $tot_att_price);?></b>
			</span>
			<div class="discount">
				<?php
				if ($discount_present == 1)
				{
					$discount_percent = ($item_price['price'] == 0) ? 0 : (100 - (($item_price['discount_price'] / $item_price['price']) * 100));
				?>
					<span class='qtcproductdiscount' title= "<?php echo Text::sprintf('QTC_PERCENT_OFF',round($discount_percent)."%");?>">
						<b><?php echo round($discount_percent) . " %";?> <?php echo Text::_('COM_QUICK2CART_DISC_POST'); ?></b>
					</span>
				<?php
				}
				?>
			</div>
		</div>

		<?php
		$textboxid = $data['parent'] . '-' . $item_id . "_itemcount";
		$parent    = $data['parent'];
		$slab      = !empty($data['slab']) ? $data['slab'] : 1;
		$maxQty    = $data['max_quantity'];

		if ($params->get('usestock', '', 'int') && !$params->get('outofstock_allowship', '', 'int') && is_numeric($data['stock']) && ($data['stock'] < $data['max_quantity']))
		{
			$maxQty = $data['stock'];
		}

		$limits    = $data['min_quantity'] . "," . $maxQty;
		$arg       = "'" . $textboxid."','" . $item_id."','" . $itemsCartItemId[$item_id] . "','" . $parent . "','" . $slab . "'," . $limits;
		$min_msg   = Text::_('QTC_MIN_LIMIT_MSG');
		$max_msg   = Text::_('QTC_MAX_LIMIT_MSG');
		$fun_param = $parent . '-' . $data['product_id'];

		//com_content-31_itemcount
		$qty_buynow  = $params->get('qty_buynow', 1);
		$qtyDivStyle = (empty($qty_buynow)) ? "display:none" : "";

		// ADD to cart button - start
		if ($showAddToCartBtn)
		{
			$showQtyIncDecSecStyle       = "";
			$showQtyToCartSecStyle       = "";
			$showQtyIncDecSecStyleSuffix = "";
			$showQtyToCartSecStyleSuffix = "";

			if (array_key_exists($data['product_id'], $cartItemsQty))
			{
				$showQtyToCartSecStyleSuffix = '-tmp';
				$showQtyToCartSecStyle       = 'style="display:none;"';
			}
			else
			{
				$showQtyIncDecSecStyleSuffix = '-tmp';
				$showQtyIncDecSecStyle       = 'style="display:none;"';
			}
			?>
			<hr class="hr hr-condensed"/>
			<div class="center">
				<div style="<?php echo $qtyDivStyle;?>" class="form-inline <?php echo $layout_to_load == 'fixed_layout' ? 'q2c-inline-block' : 'q2c-inline-grid'; ?>">
					<div class="form-group q2c-table-cell-center q2c-item-qtycount-increment-decrement-section-<?php echo $data['product_id'];?>" <?php echo $showQtyIncDecSecStyle;?>>
						<div class="q2c-display-table">
							<div class="form-group q2c-table-cell-center">
								<div class="input-group">
									<span class="input-group-addon">
										<a href="javascript:void(0);" onclick="qtc_decrement(<?php echo $arg;?>)" class="qtc_icon-qtcminus qtc_pointerCusrsor"></a>
									</span>
									<input id="<?php echo $textboxid . $showQtyIncDecSecStyleSuffix; ?>"
										name="<?php echo $data['product_id']; ?>_itemcount"
										class="qtc_textbox_small qtc_item_count_inputbox qtc_count form-control q2c-height-26px"
										type="text"
										value="<?php echo $cartItemsQty[$data['product_id']]; ?>"
										size="2"
										maxlength="3"
										data-cart-item-id="<?php echo $itemsCartItemId[$item_id];?>"
										<?php echo $disableBuyBtn; ?>
										onblur="checkforalphaLimit(this,'<?php echo $data['product_id']; ?>','<?php echo $parent; ?>','<?php echo $slab; ?>',<?php echo $limits; ?>,'<?php echo $min_msg; ?>','<?php echo $max_msg; ?>');"
										Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>)" />
									<span class="input-group-addon">
										<a href="javascript:void(0);" onclick="qtc_increment(<?php echo $arg;?>)" class="qtc_icon-qtcplus qtc_pointerCusrsor"></a>
									</span>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group q2c-table-cell-center q2c-item-to-cart-section-<?php echo $data['product_id'];?>" <?php echo $showQtyToCartSecStyle;?>>
						<div class="q2c-display-table">
							<div class="form-group q2c-table-cell-center">
								<div class="input-group" >
									<span class="input-group-addon q2c-padding-5px" for="<?php echo $textboxid . $showQtyToCartSecStyleSuffix; ?>">
										<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_PIN_QUANTITY_TOOLTIP'), Text::_('COM_QUICK2CART_PIN_QUANTITY'), '', Text::_('COM_QUICK2CART_PRODUCT_QTY')); ?>
									</span>
									<input id="<?php echo $textboxid . $showQtyToCartSecStyleSuffix; ?>"
										name="<?php echo $data['product_id']; ?>_itemcount"
										class="qtc_textbox_small qtc_item_count_inputbox qtc_count form-control q2c-height-26px"
										type="text"
										value="<?php echo $data['min_quantity']; ?>"
										size="2"
										maxlength="3"
										data-cart-item-id="<?php echo $itemsCartItemId[$item_id];?>"
										<?php echo $disableBuyBtn; ?>
										onblur="checkforalphaLimit(this,'<?php echo $data['product_id']; ?>','<?php echo $parent; ?>','<?php echo $slab; ?>',<?php echo $limits; ?>,'<?php echo $min_msg; ?>','<?php echo $max_msg; ?>');"
										Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>)" />
								</div>
							</div>
							<span>&nbsp;&nbsp;</span>
						</div>
					</div>
					<div class="form-group q2c-table-cell-center q2c-item-to-cart-section-<?php echo $data['product_id'];?>" <?php echo $showQtyToCartSecStyle;?>>
						<button class="btn btn-sm btn-success q2c-small-buy-button" type="button" <?php echo $disableBuyBtn; ?> onclick="qtc_addtocart('<?php echo $fun_param; ?>');">
							<i class="<?php echo QTC_ICON_CART; ?>"></i>
							<?php echo Text::_('COM_QUICK2CART_ADD_PRODUCT_TO_CART'); ?>
						</button>
						<?php
							$cartlink = Route::_('index.php?option=com_quick2cart&view=cart&tmpl=component');

							echo HTMLHelper::_(
								'bootstrap.renderModal',
								'cartModal',
								array(
									'title'		 => Text::_('QTC_CART'),
									'url'        => $cartlink,
									'modalWidth' => '80',
									'bodyHeight' => '70',
									'height'     => '600px',
									'width'      => '1000px'
								)
							)
						?>
					</div>
					<?php $contact_ink = Uri::base() . 'index.php?option=com_quick2cart&view=vendor&layout=contactus&store_id=' . $data['store_id'] . '&item_id=' . $data['item_id'] . '&tmpl=component';?>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
			</div>
			<?php
		}
		// ADD to cart button - end
		?>
		<div class="clearfix"></div>
		<?php
		$options_str = implode(',', $attribure_option_ids);
		?>
	</div>
	<div class="qtc-prod-oos <?php if (empty($qtcTeaserShowBuyNowBtn)){ echo 'oos';} ?>">
		<span class="label label-grey "><?php echo Text::_('QTC_OUT_OF_STOCK_MSG'); ?></span>
	</div>
</div>
<div class="clearfix"></div>
