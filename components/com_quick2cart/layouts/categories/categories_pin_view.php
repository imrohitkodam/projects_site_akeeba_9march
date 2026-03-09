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
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;

$app                 = Factory::getApplication();
$comquick2cartHelper = new comquick2cartHelper;
$productHelper       = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/product.php", "ProductHelper");
$input               = $app->input;

$menu           = $app->getMenu();
$activeMenuItem = $menu->getActive();

// If product category not found in URL then assign product category according menu
$categoryProductsCount = $productHelper->getCategoryProductsCount($qtc_store_id, 1);

$items   = isset($displayData['items']) ? $displayData['items'] : [];
$display_pin_page   = isset($displayData['display_pin_page']) ? $displayData['display_pin_page'] : 0;
$qtc_linkparam   = isset($displayData['qtc_linkparam']) ? $displayData['qtc_linkparam'] : 0;
$qtc_catname   = isset($displayData['qtc_catname']) ? $displayData['qtc_catname'] : 0;
$catItemid   = isset($displayData['catItemid']) ? $displayData['catItemid'] : 0;
$itsStoreOwner   = isset($displayData['itsStoreOwner']) ? $displayData['itsStoreOwner'] : 0;
$selectedcat   = isset($displayData['selectedcat']) ? $displayData['selectedcat'] : 0;

foreach ($items as $key => $category)
{
	if (isset($category->id) && $category->id)
	{
		$cat             = new stdClass;
		$cat->value      = $category->id ? $category->id : $category->value;
	}
	else 
	{
		$cat     = $category;
	}

	if (!empty($cat->value))
	{
		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables');
		$categoryTable = Table::getInstance('Category', 'JTable');
		$categoryTable->load($cat->value);

		if (isset($categoryTable->id))
		{
			$categoryparams = json_decode($categoryTable->params);
		}
	}

	if ($cat->value && !empty($categoryProductsCount[$cat->value]['count']))
	{
		?>

		<div class="q2c_pin_item_q2c_pc_category qtc-prod-pin col-xs-12 col-md-3 col-sm-6 mt-3 q2c-category-card"> 

			<?php
			$activecat   = '';
			$defaultCategoryImage = 0;
			if ($display_pin_page)
			{
				$menuParams = $app->getParams('com_quick2cart');
				$show_child_categories = $menuParams->get('show_child_categories');
				if ($show_child_categories)
				{
					$catItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&' . $qtc_linkparam . "&prod_cat=" . $category->id);
					$categoryUrl = Route::_('index.php?option=com_quick2cart&view=category&layout=default&prod_cat=' . $category->id . '&Itemid=' . $catItemid, false);
				} 
				else 
				{
					$itemId  = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=categories&layout=default');
					$categoryUrl = Route::_('index.php?option=com_quick2cart&view=categories&layout=default&cat_id=' . $category->id . '&Itemid=' . $itemId, false);
				}

				$params = json_decode($category->params);
				if ($params && $params->image)
				{
					$imagePath = $params->image;
				}
				else 
				{
					$imagePath = 'components/com_quick2cart/assets/images/no-image-available.png';
					$defaultCategoryImage = 1;
				}
			}
			else 
			{
				// Making value = '' to value = 0 for all product
				$cat->value = !empty($cat->value) ? $cat->value : 0;

				// GETTING ITEM ID
				$catItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&' . $qtc_linkparam . "&prod_cat=" . $cat->value);
				$categoryUrl   = Uri::root() . substr(Route::_('index.php?option=com_quick2cart&' . $qtc_linkparam . '&' . $qtc_catname . '=' . $cat->value . '&Itemid=' . $catItemid . $itsStoreOwner) , strlen(Uri::base(true)) + 1);

				$activecat = ($selectedcat == $cat->value) ? "active" : "";

				if (empty($categoryparams->image))
				{
					$imagePath = "components/com_quick2cart/assets/images/no-image-available.png";
					$defaultCategoryImage = 1;
				}
				else 
				{
					$imagePath = $categoryparams->image;
				}
			}
			?>

			<a class="q2c-category--link <?php echo $activecat; ?>" href="<?php echo $categoryUrl; ?>">
				<div class="q2c-category--image br-15">
					<img class="af-br-5" src="<?php echo $imagePath; ?>">
					<?php
						if ($defaultCategoryImage)
						{
							?>
							<div class="category-image-text">
								<h3><?php echo $display_pin_page ? $category->title : $category->text; ?></h3>
							</div>
							<?php
						}
					?>
				</div>
				<div class="q2c-category--title">
					
					<?php echo $display_pin_page ? $category->title : $category->text; ?>
				</div>
			</a>

		</div> 
		<?php
	}
}
?>