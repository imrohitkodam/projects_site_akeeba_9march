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

HTMLHelper::_('bootstrap.tooltip');

if (JVERSION < '4.0.0')
{
	HTMLHelper::_('behavior.framework');
}

HTMLHelper::_('bootstrap.renderModal');

// check for store
if (empty($this->store_id))
{
	?>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
		<div>
			<div class="alert alert-danger">
				<span>
					<?php echo Text::_('QTC_ILLEGAL_PARAMETARS'); ?>
				</span>
				<?php
				$qtc_back = Q2C_ICON_ARROW_RIGHT;
				?>
				<button type="button"  title="<?php echo Text::_( 'QTC_DEL' ); ?>" class="btn btn-sm btn-primary pull-right" onclick="javascript:history.back();" >
					<i class="<?php echo $qtc_back;?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i>&nbsp; <?php echo Text::_( 'QTC_BACK_BTN');?>
				</button>

			</div>
		</div>
	</div>
	<?php
	return false;
}

//load style sheet
$document = Factory::getDocument();

// for featured and top seller product
$product_path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

if (!class_exists('productHelper'))
{
	JLoader::register('productHelper', $product_path );
	JLoader::load('productHelper');
}

$productHelper       = new productHelper();
$comquick2cartHelper = new comquick2cartHelper;
$store_id            = $this->store_id;
$layout_to_load      = $this->params->get('layout_to_load','','string');
$pinHeight           = $this->params->get('fix_pin_height','200','int');
$noOfPin_lg          = $this->params->get('pin_for_lg','3','int');
$noOfPin_md          = $this->params->get('pin_for_md','3','int');
$noOfPin_sm          = $this->params->get('pin_for_sm','4','int');
$noOfPin_xs          = $this->params->get('pin_for_xs','2','int');
?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> container-fluid">
	<form name="adminForm" id="adminForm" class="form-validate" method="post">
		<div class="row">
			<div class="col-sm-9 col-xs-12 ">
				<!-- START ::for store info  -->
				<?php
				if (!empty($this->storeDetailInfo))
				{
					$sinfo = $this->storeDetailInfo;
				}
				?>

				<legend align="">
					<?php echo Text::sprintf('QTC_WECOME_TO_STORE', htmlspecialchars($sinfo['title'], ENT_COMPAT, 'UTF-8'));?>
				</legend>

				<?php
				// Show store info is category is not selected
				if (empty($this->change_prod_cat))
				{
					$view = $comquick2cartHelper->getViewpath('vendor', 'storeinfo_bs3');
					ob_start();
					include($view);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
				}
				?>
				<!-- END ::for store info  -->

				<?php
				// featured prod and top seller should be shown only if categoty is not selected
				if (empty($this->change_prod_cat))
				{
					?>
					<!-- START ::for featured product  -->
					<?php
					// 	GETTING ALL FEATURED PRODUCT
					$featured_limit = $this->params->get('featured_limit');
					$target_data    = $productHelper->getAllFeturedProducts($store_id, $this->change_prod_cat, $featured_limit);

					if (!empty($target_data))
					{
						?>
						<div class="">
							<div class="col-sm-12 col-xs-12" >
								<legend align="center">
									<?php echo Text::_('QTC_FEATURED_PRODUCTS') ;?>
								</legend>
								<?php $random_container = 'q2c_pc_featured';?>
								<div id="q2c_pc_featured">
									<?php
									$Fixed_pin_classes = "";

									if ($layout_to_load == "fixed_layout")
									{
										$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md. " col-lg-" . $noOfPin_lg . " ";
									}

									// REDERING FEATURED PRODUCT
									foreach($target_data as $data)
									{
										$html = '';
										?>
										<div class='q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes?>'>
										<?php
										$path = JPATH_SITE . '/components/com_quick2cart/views/product/tmpl/product_bs3.php';

										ob_start();
										include($path);
										$html = ob_get_contents();
										ob_end_clean();
										echo $html;
										?>
										</div>
										<?php
									}
									?>
								</div>
								<?php
								if ($layout_to_load == "flexible_layout")
								{
								?>
									<!-- setup pin layout script-->
									<script type="text/javascript">
										var pin_container_<?php echo $random_container; ?> = 'q2c_pc_featured'
									</script>

									<?php
									$view = $comquick2cartHelper->getViewpath('product', 'pinsetup_bs3');
									ob_start();
									include($view);
									$html = ob_get_contents();
									ob_end_clean();
									echo $html;
								}
								?>
							</div>
						</div>
						<?php
					}
					?>
					<!-- END ::for featured product  -->

					<?php
					// GETTING ALL Top  seller PRODUCT
					$topSeller_limit = $this->params->get('topSeller_limit');
					$target_data     = $productHelper->getTopSellerProducts($store_id, $this->change_prod_cat, $topSeller_limit, "com_quick2cart");

					if (!empty($target_data))
					{
						?>
						<!-- START ::for top seller  -->
						<div class="">
							<div class="col-sm-12 col-xs-12" >
								<legend align="center">
									<?php echo Text::_('QTC_TOP_SELLER_STORE_PRODUCTS') ;?>
								</legend>
								<?php $random_container = 'q2c_pc_top_seller';?>
								<div id="q2c_pc_top_seller">
									<?php
									$Fixed_pin_classes = "";

									if ($layout_to_load == "fixed_layout")
									{
										$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md. " col-lg-" . $noOfPin_lg . " ";
									}

									// REDERING Top  seller  PRODUCT
									foreach($target_data as $data)
									{
										$html = '';
									?>
										<div class='q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes;?>'>
										<?php
										$path = JPATH_SITE . '/components/com_quick2cart/views/product/tmpl/product_bs3.php';
										ob_start();
										include($path);
										$html = ob_get_contents();
										ob_end_clean();
										echo $html;
										?>
										</div>
										<?php
									}
									?>
								</div>
								<?php
								if ($layout_to_load == "flexible_layout")
								{
								?>
									<!-- setup pin layout script-->
									<script type="text/javascript">
										var pin_container_<?php echo $random_container; ?> = 'q2c_pc_top_seller'
									</script>

									<?php
									$view = $comquick2cartHelper->getViewpath('product', 'pinsetup_bs3');
									ob_start();
									include($view);
									$html = ob_get_contents();
									ob_end_clean();
									echo $html;
								}
								?>
							</div>
						</div>
						<?php
					}
				}
				?>

				<!-- All products frm store -->
				<?php
				if (!empty($this->allStoreProd))
				{
					?>
					<!-- START ::for top seller  -->
					<div class="">
						<div class="col-sm-12 col-xs-12" >
							<legend align="center">
								<?php echo Text::_('QTC_PROD_FROM_THIS_STORE_PRODUCTS') ;?>
							</legend>
							<?php $random_container = 'q2c_pc_store_products';?>
							<div id="q2c_pc_store_products">
								<?php
								$Fixed_pin_classes = "";

								if ($layout_to_load == "fixed_layout")
								{
									$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md. " col-lg-" . $noOfPin_lg . " ";
								}

								// REDERING Top  seller  PRODUCT
								foreach($this->allStoreProd as $data)
								{
									$data=(array)$data;
									$html = '';
									?>
									<div class='q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes;?>'>
									<?php
									$path = JPATH_SITE . '/components/com_quick2cart/views/product/tmpl/product_bs3.php';
									ob_start();
									include($path);
									$html = ob_get_contents();
									ob_end_clean();
									echo $html;
									?>
									</div>
									<?php
								}
								?>
							</div>
							<?php
							if ($layout_to_load == "flexible_layout")
							{
							?>
								<!-- setup pin layout script-->
								<script type="text/javascript">
									var pin_container_<?php echo $random_container; ?> = 'q2c_pc_store_products'
								</script>

								<?php
								$view = $comquick2cartHelper->getViewpath('product', 'pinsetup_bs3');
								ob_start();
								include($view);
								$html = ob_get_contents();
								ob_end_clean();
								echo $html;
							}
							?>
							<div class="pager" style="margin:0px;">
								<?php echo $this->pagination->getPagesLinks(); ?>
							</div>
						</div>
					</div>
					<?php
				}
				?>
				<!-- END ALL PRODU FRM store -->
			</div>

			<div class="col-sm-3 col-xs-12">
				<div class="row">
					<!-- for category list-->
					<?php
					// DECLARE STORE RELEATED PARAMS
					$qtc_catname  = "store_cat";
					$qtc_view     = "vendor";
					$qtc_layout   = "store";
					$qtc_store_id = $this->store_id;

					//GETTING STORE RELEATED CATEGORIES
					$storeHelper       = new storeHelper();
					$storeHomePage     = 1;
					$viewReleated_cats = $storeHelper->getStoreCats($this->store_id, '', '', '', '', 0);
					$catListHeader     = Text::_('COM_QUICK2CART_STOREHOME_CATLIST_HEADER');
					$view              = $comquick2cartHelper->getViewpath('category', 'categorylist_bs3');
					ob_start();
					include($view);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
					?>
				</div>
			</div>
		</div>
		<!-- FIRST ROW-FLOUID DIV-->

		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="vendor" />
		<input type="hidden" name="task" value="refreshStoreView" />
		<input type="hidden" name="controller" value="vendor" />
	</form>
</div>
