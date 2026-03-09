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
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;
use Joomla\CMS\Uri\Uri;

$listOrder    = $this->state->get('list.ordering');
$listDirn     = $this->state->get('list.direction');
$categoryPage = $this->categoryPage;

// For featured and top seller product
$product_path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

if (!class_exists('productHelper'))
{
	JLoader::register('productHelper', $product_path );
	JLoader::load('productHelper');
}

$productHelper       = new productHelper();
$comquick2cartHelper = new comquick2cartHelper;
$store_id            = 0;

$layout_to_load = $this->params->get('layout_to_load', 'flexible_layout', 'string');
$pinHeight      = $this->params->get('fix_pin_height','200','int');
$noOfPin_lg     = $this->params->get('pin_for_lg','3','int');
$noOfPin_md     = $this->params->get('pin_for_md','3','int');
$noOfPin_sm     = $this->params->get('pin_for_sm','4','int');
$noOfPin_xs     = $this->params->get('pin_for_xs','2','int');

// Get if quick2cart model is published on position tj-filters-mod-pos
$document   = Factory::getDocument();
$renderer   = $document->loadRenderer('module');
$com_params = ComponentHelper::getParams('com_quick2cart');
$modules    = ModuleHelper::getModules($com_params->get('product_filter', 'tj-filters-mod-pos'));
HTMLHelper::_('script', 'components/com_quick2cart/assets/js/auto.js');

?>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?> container-fluid qtc-cat-prod">
	<form name="adminForm" id="adminForm" class="form-validate" method="post">
		<?php
		$input      = Factory::getApplication()->input;
		$option     = $input->get('option', '', 'STRING' );
		$storeOwner = $input->get( 'qtcStoreOwner', 0, 'INTEGER');

		if (!empty($this->store_role_list) && $storeOwner==1)
		{
			$active = 'products';
			$view = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
		}
		?>
		<div class="row qtc_productblog container-fluid">
			<?php $gridClass = ($this->qtcShowCatStoreList == 0) ? "col-sm-12 col-xs-12" : "col-md-9 col-sm-12 col-xs-12";?>
			<div class="<?php echo $gridClass; ?>">
				<div class="row">
					<div class="">
						<h1><strong><?php echo Text::_(trim($this->productPageTitle));?></strong></h1>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-8 col-xs-12">
						<div class="js-stools-container-selector btn-group float-start filter-search">
								<input type="text"
									placeholder="<?php echo Text::_('COM_QUICK2CART_FILTER_SEARCH_DESC_PRODUCTS'); ?>"
									name="filter_search" id="filter_search"
									value="<?php echo $this->escape($this->searchkey); ?>" class="form-control"
									onkeyup="fetchProducts(this.value);" autocomplete="off" />
								<!-- Dropdown for suggestions -->
								<ul id="suggestions" class="dropdown-menu position-absolute top-100 start-0 w-100 z-index-1000 p-0 m-0">
								</ul>
							<button
								type="button"
								onclick="this.form.submit();"
								class="btn btn-primary"
								title="<?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?>">
								<i class="<?php echo QTC_ICON_SEARCH; ?>"></i>
							</button>
							<button
								onclick="document.getElementById('filter_search').value='';this.form.submit();"
								type="button"
								class="btn btn-secondary"
								title="<?php echo Text::_('JSEARCH_FILTER_CLEAR');?>">
								<i class="<?php echo QTC_ICON_REMOVE; ?>"></i>
							</button>
						</div>
					</div>
					<?php
					if ($modules)
					{
						?>
						<i class="fa fa-filter fa-2x" aria-hidden="true" onclick="q2cShowFilter()"></i>
						<?php
					}
					?>
					<div class="col-sm-4 col-xs-12">
						<div class="btn-group float-end">
							<?php echo HTMLHelper::_('select.genericlist', $this->product_sorting, "sort_products", 'class="form-select" onchange="document.adminForm.submit();" name="sort_products"', "value", "text", $this->state->get('sort_products'));?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<div class="clearfix"></div>
				<div class="qtcClearBoth">&nbsp;</div>

				<!-- Code to show filters-->
				<div id="q2chorizontallayout" style="display:none;" class="q2c-filter-horizontal-div">
					<?php
					if ($modules)
					{
						$moduleParams = new Registry($modules['0']->params);
						$params       = array();

						if ($moduleParams->get('client_type') == "com_quick2cart.product")
						{
							foreach ($modules as $module)
							{
								echo $renderer->render($module, $params);
							}
						}
						else
						{
							echo Text::_('COM_QUICK2CART_NO_FILTERS');
						}
					}
					?>
				</div>
				<div class="row">
					<?php
					// GETTING ALL FEATURED PRODUCT
					$target_data = ($this->products);
					
					
					$prodivsize  = "category_product_div_size";

					if (empty($target_data))
					{
					?>
						<div class="alert alert-warning">
							<span><?php echo Text::_('QTC_NO_PRODUCTS_FOUND'); ?></span>
						</div>
					<?php
					}
					else
					{
						$random_container = 'q2c_pc_category';?>
 						<div id="q2c_pc_category" class="q2c_pin_container">
							<div class="row">
								<?php
								foreach ($target_data as $data)
								{
									$Fixed_pin_classes = "";

									if ($layout_to_load == "fixed_layout")
									{
										$Fixed_pin_classes = " qtc-prod-pin col-xs-" . $noOfPin_xs . " col-sm-" . $noOfPin_sm . " col-md-" . $noOfPin_md. " col-lg-" . $noOfPin_lg . " ";
									}
									?>
									<div class="q2c_pin_item_<?php echo $random_container . $Fixed_pin_classes;?>">
										<?php
										$data       = (array)$data;
										$path       = $comquick2cartHelper->getViewpath('product','product_bs5');
										$store_list = !empty($this->store_list)?$this->store_list:array();
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
								<div class="clearfix"></div>
								<div class="qtcClearBoth">&nbsp;</div>
							</div>
						</div>
						<!-- setup pin layout script-->
						<?php
						if ($layout_to_load == "flexible_layout")
						{
						?>
							<script type="text/javascript">
								var pin_container_<?php echo $random_container; ?> = 'q2c_pc_category'
							</script>
							<?php
							$view = $comquick2cartHelper->getViewpath('product', 'pinsetup_bs5');
							ob_start();
							include($view);
							$html = ob_get_contents();
							ob_end_clean();
							echo $html;
						}
					}
					?>
				</div>
				<!-- END ::for featured product  -->
			</div>
			<?php
			if ($this->qtcShowCatStoreList == 1)
			{
			?>
				<div class="col-md-3 col-sm-12 col-xs-12">
				<!-- for category list-->
					<?php
					$view = $comquick2cartHelper->getViewpath('category','categorylist_bs5');
					ob_start();
					include($view);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
					?>
					<hr class="hr hr-condensed">
					<?php
					if ($this->params->get('multivendor'))
					{
						$storeHelper = new storeHelper();
						$options     = $storeHelper->getStoreList();
						$view        = $comquick2cartHelper->getViewpath('vendor', 'storelist_bs5');
						ob_start();
						include($view);
						$html = ob_get_contents();
						ob_end_clean();
						echo $html;
					}?>
				</div>
				<?php
			}
			?>
		</div>
		<!-- FIRST ROW-FLOUID DIV-->
		<?php echo $this->pagination->getListFooter(); ?>
		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="view" value="category" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="" />
	</form>
</div>

<script>
	
	// Language constant for "No products found" message
	const noProductsFoundText = "<?php echo Text::_('QTC_NO_PRODUCTS_FOUND'); ?>";

	const fetchProductsUrl = "<?php echo Uri::root(); ?>index.php?option=com_quick2cart&task=category.fetchProducts";


	// Get the product Names in Auto suggestion
	function fetchProducts(query) {
		const suggestions = document.getElementById('suggestions');


		// Clear dropdown if input is empty
		if (!query) {
			suggestions.style.display = 'none';
			suggestions.innerHTML = '';
			return;
		}

		// Send AJAX request to fetch matching product names
		fetch(`${fetchProductsUrl}&query=${encodeURIComponent(query)}`)
		.then(response => response.json())
		.then(data => {

			// Clear existing suggestions
			suggestions.innerHTML = '';

			if (data.length > 0) {

				data.forEach(product => {
					const item = document.createElement('li');
					item.textContent = product.name; // Assuming "name" is a property in your response
					item.className = 'dropdown-item'; // Bootstrap dropdown styling
					item.style.cursor = 'pointer'; // Add pointer cursor dynamically

					// Add click event to redirect to product detail page
					item.onclick = () => {
						window.location.href = product.url;
					};

					suggestions.appendChild(item);
				});

				suggestions.style.display = 'block'; // Show dropdown
			} else {
				suggestions.innerHTML = `<li class="dropdown-item">${noProductsFoundText}</li>`;
				suggestions.style.display = 'block';
			}
		})
		.catch(error => {
			console.error('Error fetching products:', error); // If AJAx request fails error message show 
			suggestions.style.display = 'none';
		});
	}

</script>
