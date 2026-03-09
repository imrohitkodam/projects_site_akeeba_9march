<?php
/**
 * @package    Quick2Cart
 * @author     Techjoomla
 * @license    GNU GPL v2 or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

$document = Factory::getDocument();
$app      = Factory::getApplication();
$user     = Factory::getUser();
$input    = $app->input;

$products = $data ?? [];
?>

<div id="mod_quick2cart_container<?php echo $module->id;?>" class="q2c-personalized-products <?php echo $params->get('moduleclass_sfx'); ?>">
	<?php if (empty($products)) :?>
		<div class="alert alert-warning">
			<?php echo Text::_('MOD_QUICK2CART_NO_PRODUCTS_FOUND'); ?>
		</div>
	<?php else : ?>
		<div class="row justify-content-center">
			<?php foreach ($products as $product) : ?>
				<?php
					$productLink = Route::_('index.php?option=com_quick2cart&view=productpage&layout=default&item_id=' . (int) $product->item_id);
					$imageFile   = !empty($product->images) ? json_decode($product->images)[0] : '';
					$imagePath   = $imageFile ? 'images/quick2cart/' . $imageFile : 'images/quick2cart/default-product.png';
				?>
				<div class="col-sm-3 col-xs-12 quick2cart_product_item">
					<div class="qtc-prod-pin-inner mt-1 rounded-1">
						<div class="qtc-prod-pin-header">

							<?php if (!empty($product->featured)) : ?>
								<div class="qtc-prod-tag-cover qtc-feat-prod-visible">
									<span class="qtc-prod-tag" title="<?php echo Text::_('COM_QUICK2CART_FEATURED_PRODUCT') ?>">
										<?php echo Text::_('COM_QUICK2CART_FEATURED_PRODUCT') ?>
									</span>
									<div class="clear-fix"></div>
								</div>
							<?php endif; ?>
						</div>

						<div class="qtc-prod-img-cover">
							<a title="<?php echo htmlentities($product->name); ?>" href="<?php echo $productLink; ?>">
								<div class="productimg qtc-prod-img"
									style="background-image: url('<?php echo Uri::root() . $imagePath; ?>'); height: 200px;"></div>
							</a>
						</div>

						<div class="qtc-prod-footer-cover">
							<div class="qtc-prod-name-cover">
								<strong>
									<a title="<?php echo htmlentities($product->name); ?>" href="<?php echo $productLink; ?>"
										class="qtc-cv-prod-name text-dark fs-6 fw-bold text-decoration-none">
										<?php echo $product->name; ?>
									</a>
								</strong>
							</div>

							<div class="qtc-prod-price-cover">
								<span class='qtcproductprice mt-2'>
									<b><?php echo $product->price == 0 ? Text::_('MOD_QUICK2CART_FREE') : '$ ' . number_format($product->price, 2); ?></b>
								</span>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
</div>

<style>
	@media (min-width: 480px) {
		#mod_quick2cart_container<?php echo $module->id; ?> .quick2cart_product_item {
			width: <?php echo $params->get('mod_pin_width', '230', 'INT'); ?>px !important;
			padding: <?php echo $params->get('mod_pin_padding', '10', 'INT'); ?>px;
		}
	}
</style>

