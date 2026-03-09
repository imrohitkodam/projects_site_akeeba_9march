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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
HTMLHelper::_('bootstrap.renderModal');

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);
$helperobj = new comquick2cartHelper;
$curr = $helperobj->getCurrencySession();
$entered_numerics = "'" . Text::_('QTC_ENTER_NUMERICS') . "'";

$path = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';

if (!class_exists('quick2cartModelAttributes'))
{
	JLoader::register('quick2cartModelAttributes', $path);
	JLoader::load('quick2cartModelAttributes');
}

$quick2cartModelAttributes = new quick2cartModelAttributes;
$item_id = (is_object($data)) ? $data->item_id : $data['item_id'];
$productHelper = new productHelper;

$itemDetailObj = (object)$data;

// For attribute based stock get attribute details
$completeAttrDetail= $productHelper->getItemCompleteAttrDetail($itemDetailObj->item_id);

if (!empty($completeAttrDetail))
{
	$itemDetailObj->itemAttributes = $completeAttrDetail;
}

// Check whether product is allowd to buy or not. ( our of stock)
$qtcTeaserShowBuyNowBtn = $productHelper->isInStockProduct($itemDetailObj);
$disableBuyBtn = (empty($qtcTeaserShowBuyNowBtn))?'" disabled=disabled "':"";
$prodAttDetails = $productHelper->getProdPriceWithDefltAttributePrice($item_id);
$it_price = $prodAttDetails;

if (isset($it_price['itemdetail']))
{
	$item_price = $it_price['itemdetail'];
}

// STORE OWNER IS LOGGED IN
$store_owner = '';

if (!empty($store_list))
{

	if (in_array($data['store_id'], $store_list))
	{
		//$store_owner=$data['store_id'];
		$store_owner = 1;
	}
}
// class for publish and unpublish icon --- used further

$publish = QTC_ICON_CHECKMARK;
$unpublish = QTC_ICON_REMOVE;

if (!empty($store_owner))
{
	$itemstate = $data['state'];
}

// GETTING ALL PRODUCTS ATTRIBURES
$attribure_option_ids = $prodAttDetails['attrDetail']['attrOptionIds'];
$tot_att_price = $prodAttDetails['attrDetail']['tot_att_price'];
$classes = !empty($classes) ? $classes : '';
$prodivsize = !empty($prodivsize) ? $prodivsize : 'default_product_div_size';
$com_params = ComponentHelper::getParams('com_quick2cart');
$img_width = $com_params->get('medium_width', 120);
$showAddToCartBtn = $com_params->get('show_addtocart_on_pin', '0', 'INT');
?>
<div class="qtc-prod-pin-inner">
	<div class="qtc-prod-pin-header">
		<?php
		$product_link = $helperobj->getProductLink($data['item_id'], 'detailsLink');

		if (isset($data['featured']))
		{
			?>
			<div class="qtc-prod-tag-cover <?php if ($data['featured']=='1') {echo 'qtc-feat-prod-visible';} ?>">
				<span href="#" class="qtc-prod-tag" title="<?php echo  Text::_('COM_QUICK2CART_FEATURED_PRODUCT') ?>"><?php echo  Text::_('COM_QUICK2CART_FEATURED_PRODUCT') ?></span>
				<div class="clear-fix"></div>
			</div>
			<?php
		}

		$prodname = $data['name'];
		?>
	</div>
	<?php
	$images = json_decode($data['images'], true);
	$img = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';

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
		$media = new qtc_mediaHelper();
		$file_name_without_extension = $media->get_media_file_name_without_extension($images[$firstKey]);
		$media_extension = $media->get_media_extension($images[$firstKey]);
		$img = $helperobj->isValidImg($file_name_without_extension.'_L.'.$media_extension);

		if (empty($img))
		{
			$img = Uri::base().'components/com_quick2cart/assets/images/default_product.jpg';
		}
	}
	?>

	<div class="qtc-prod-img-cover <?php if (empty($qtcTeaserShowBuyNowBtn)){ echo 'poos';} ?>">
		<a title="<?php echo htmlentities($data['name']);?>" href="<?php echo $product_link; ?>">
		<?php
		if ($layout_to_load == "fixed_layout")
		{
		?>
			<div class="qtc-prod-img" style="background-image: url('<?php echo htmlentities($img) ; ?>'); height:<?php echo !empty($pinHeight) ? $pinHeight : 200;?>px">
			</div>

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
		<div class="qtc-prod-name-cover">
			<strong>
			<a title="<?php echo htmlentities($data['name']);?>" href="<?php echo $product_link; ?>" class="qtc-cv-prod-name">
				<?php echo $prodname;?>
			</a>
			</strong>
		</div>
		<div class="qtc-prod-price-cover">
			<?php
				$discount_present = ($com_params->get('usedisc') && isset($item_price['discount_price']) && !is_null($item_price['discount_price'])) ? 1 : 0;
				$p_price = ($discount_present != 0 && !empty($item_price['discount_price']) && ceil($item_price['discount_price'])) ? $item_price['discount_price'] : $item_price['price'];
			?>
			<?php
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
					<b>
						<?php echo $helperobj->getFromattedPrice($p_price + $tot_att_price);?>
					</b>
				</span>
				<?php
				if ($discount_present == 1)
				{
					$discount_percent = (100 - (($item_price['discount_price'] / $item_price['price']) * 100));
				?>
				<span class='qtcproductdiscount' title= "<?php echo Text::sprintf('QTC_PERCENT_OFF',round($discount_percent)."%");?>">
						<b>
						<?php echo Text::_('COM_QUICK2CART_DISC_PRE'); ?> <?php echo round($discount_percent) . " %";?> <?php echo Text::_('COM_QUICK2CART_DISC_POST'); ?>
						</b>
				</span>
				<?php
				}
				?>
		</div>

		<?php
		$textboxid = $data['parent'] . '-' . $item_id . "_itemcount";
		$parent = $data['parent'];
		$slab = !empty($data['slab']) ? $data['slab'] : 1;
		$maxQty = $data['max_quantity'];

		if (is_numeric($data['stock']) && ($data['stock'] < $data['max_quantity']))
		{
			$maxQty = $data['stock'];
		}

		$limits = $data['min_quantity'] . "," . $maxQty;
		$arg= "'" . $textboxid."','" . $item_id."','" . $parent . "','" . $slab . "'," . $limits;
		$min_msg = Text::_('QTC_MIN_LIMIT_MSG');
		$max_msg = Text::_('QTC_MAX_LIMIT_MSG');
		$fun_param = $parent . '-' . $data['product_id'];
		//com_content-31_itemcount
		$qty_buynow = $com_params->get('qty_buynow', 1);
		$qtyDivStyle = "";

		if (empty($qty_buynow))
		{
			// Dont show quantity
			$qtyDivStyle = "display:none";
		}

		// ADD to cart button - start
		if ($showAddToCartBtn)
		{
		?>
		<hr class="hr hr-condensed"/>
		<div class="center">
			<div style="<?php echo $qtyDivStyle;?>" class="form-inline q2c-inline-block">
			  <div class="control-group q2c-table-cell-center">
				 <div class="q2c-display-table">
					<div class="control-group q2c-table-cell-center">
						<div class="input-prepend" >
							<span class="add-on q2c-padding-5px"
								for="<?php echo $textboxid;?>">
								<?php echo HTMLHelper::tooltip(Text::_('COM_QUICK2CART_PIN_QUANTITY_TOOLTIP'), Text::_('COM_QUICK2CART_PIN_QUANTITY'), '', Text::_('COM_QUICK2CART_PRODUCT_QTY'));?>
							</span>
							<input id="<?php echo $textboxid;?>"
								name="<?php echo $data['product_id'];?>_itemcount"
								class="qtc_textbox_small qtc_count controls q2c-height-26px"
								type="text"
								value="<?php echo $data['min_quantity'];?>"
								size="2"
								maxlength="3"
								<?php echo $disableBuyBtn;?>
								onblur="checkforalphaLimit(this,'<?php echo $data['product_id'];?>','<?php echo $parent;?>','<?php echo $slab;?>',<?php echo $limits;?>,'<?php echo $min_msg;?>','<?php echo $max_msg;?>');"
								Onkeyup="checkforalpha(this,'',<?php echo $entered_numerics; ?>)" />
						</div>
					</div>
					<div class="control-group q2c-table-cell-center">
					<span class="qtc_itemcount" >
						<input type="button" <?php echo $disableBuyBtn;?> onclick="qtc_increment(<?php echo $arg;?>)" class="qtc_icon-qtcplus qtc_pointerCusrsor" />
						<input type="button" <?php echo $disableBuyBtn;?> onclick="qtc_decrement(<?php echo $arg;?>)" class="qtc_icon-qtcminus qtc_pointerCusrsor" />
					</span>
				</div>
				</div>
			  </div>

				<div class="control-group q2c-table-cell-center">
					<button class="btn btn-sm btn-success q2c-small-buy-button" type="button"
						<?php echo $disableBuyBtn;?> onclick="qtc_addtocart('<?php echo $fun_param; ?>');">
							<i class="<?php echo QTC_ICON_CART;?>"></i> <?php echo Text::_('COM_QUICK2CART_ADD_PRODUCT_TO_CART');?>
					</button>
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="clearfix"></div>
		</div>
		<?php
			$popup_buynow = $com_params->get('popup_buynow', 1);

			if ($popup_buynow == 2)
			{
				$checkout = 'index.php?option=com_quick2cart&view=cart';
				$comquick2cartHelper = new Comquick2cartHelper;
				$itemid = $comquick2cartHelper->getitemid($checkout);
				$action_link = Uri::root() . substr(Route::_('index.php?option=com_quick2cart&view=cartcheckout&Itemid=' . $itemid, false), strlen(Uri::base(true)) + 1);
				?>
				<div class="cart-popup" id="<?php echo $fun_param; ?>_popup" style="display: none;">
					<div class="message"></div>
					<div class="cart_link">
						<a class="btn btn-success" href="<?php echo $action_link; ?>">
							<?php echo Text::_('COM_QUICK2CART_VIEW_CART');?>
						</a>
					</div>
					<i class="<?php echo QTC_ICON_REMOVE; ?> cart-popup_close" onclick="techjoomla.jQuery(this).parent().slideUp().hide();"></i>
				</div>
				<?php
			}
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
