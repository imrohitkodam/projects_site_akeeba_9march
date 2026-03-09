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
		$activecat   = '';
		if ($display_pin_page)
		{
			$menuParams = $app->getParams('com_quick2cart');
			$show_child_categories = $menuParams->get('show_child_categories');
			if ($show_child_categories)
			{
				$catItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&' . $qtc_linkparam . "&prod_cat=" . $category->id);
				$categoryUrl = Route::_('index.php?option=com_quick2cart&view=category&layout=default&prod_cat=' . $category->id . '&Itemid=' . $catItemid, false);
			} else 
			{
				$itemId  = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=categories&layout=default');
				$categoryUrl = Route::_('index.php?option=com_quick2cart&view=categories&layout=default&cat_id=' . $category->id . '&Itemid=' . $itemId, false);
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
		}
		?>

		<div class="qtcWordWrap">
			<a class="tj-list-group-item <?php echo $activecat;?>" href="<?php echo $categoryUrl ;?>">
				<?php echo $display_pin_page ? $category->title : $category->text; ?>
				<?php if (isset($categoryProductsCount[$cat->value]['count'])): ?>
					<span class="badge bg-primary"><?php echo $categoryProductsCount[$cat->value]['count'];?></span>
				<?php endif; ?>
			</a>
		</div>

		<?php
	}
}
?>
