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

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;

$app                 = Factory::getApplication();
$comquick2cartHelper = new comquick2cartHelper;
$productHelper       = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/product.php", "ProductHelper");
$input               = $app->input;

// Start vars
$qtc_catname  = !empty($qtc_catname) ? $qtc_catname : "prod_cat";
$qtc_store_id = !empty($qtc_store_id) ? $qtc_store_id : "";
$qtc_view     = !empty($qtc_view) ? $qtc_view : "category";
$qtc_layout   = !empty($qtc_layout) ? $qtc_layout : "default";

$menu           = $app->getMenu();
$activeMenuItem = $menu->getActive();

// If product category not found in URL then assign product category according menu
$categoryProductsCount = $productHelper->getCategoryProductsCount($qtc_store_id, 1);

$qtc_linkparam = array();

if (!empty($qtc_view))
{
	$qtc_linkparam[] = "view=" . $qtc_view;
}

if (!empty($qtc_layout))
{
	$qtc_linkparam[] = "layout=" . $qtc_layout;
}

if (!empty($qtc_store_id))
{
	$qtc_linkparam[] = "store_id=" . $qtc_store_id;
}

$qtc_linkparam = implode("&", $qtc_linkparam);

if (!empty($viewReleated_cats))
{
	// CHANGE DEFAULT LAGUATE CONST
	// FOR STORE VIEW , WE SHOULD SHOW ONLY STORE CATEGORY
	$cats = $viewReleated_cats;
}
else
{
	// JUGAD fix for error #20162
	$path = JPATH_SITE . '/libraries/joomla/html/html/category.php';

	if (!class_exists('JHtmlCategory'))
	{
		JLoader::register('JHtmlCategory', $path);
		JLoader::load('JHtmlCategory');
	}

	// Get categories manually
	$cats = $comquick2cartHelper->getQ2cCats(0, 0);
}

// GETTING ITEM ID
$catItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&' . $qtc_linkparam . "&prod_cat=");
?>
<div class="row categories-list">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="row cardsCont">
			<?php
			$selectedcat = $input->get($qtc_catname, 0, 'INTEGER');

			if (empty($selectedcat) && (!empty($activeMenuItem->params)))
			{
				$selectedcat = $activeMenuItem->params->get('defaultCatId', '', 'INT');
			}

			// Store owner call then add
			$option         = $input->get('option', '', 'STRING');
			$qtc_storeOwner = $input->get('qtcStoreOwner', 0, 'INTEGER');
			$itsStoreOwner  = ($qtc_storeOwner == 1) ? "&qtcStoreOwner=1&qtcCatCall=1" : "";

			// Get Itemid for all products view
			$menuItems = $menu->getItems('link', 'index.php?option=com_quick2cart&view=category&layout=default');

			if (!empty($menuItems))
			{
				foreach ($menuItems as $menuItem)
				{
					if (empty($menuItem->getParams()->get('defaultCatId')) && empty($menuItem->getParams()->get('defaultCatId')))
					{
						$allProductsMenuItemId = $menuItem->id;
					}
				}

				if (empty($allProductsMenuItemId))
				{
					$allProductsMenuItemId = $catItemid;
				}
			}

			$allcatlink   = Uri::root() . substr(Route::_('index.php?option=com_quick2cart&' . $qtc_linkparam . '&' . $qtc_catname . '=0&Itemid=' . $allProductsMenuItemId . $itsStoreOwner) , strlen(Uri::base(true)) + 1);
			$allactivecat = ($selectedcat == 0) ? "active" : "";

			$layout = new FileLayout('categories_pin_view', JPATH_ROOT . '/components/com_quick2cart/layouts/categories');

			$displayData = array(
				'items' => $cats, 
				'qtc_linkparam' => $qtc_linkparam,
				'qtc_catname' => $qtc_catname,
				'catItemid' => $catItemid,
				'itsStoreOwner' => $itsStoreOwner,
				'selectedcat' => $selectedcat,
			);
			$output = $layout->render($displayData);

			echo $output;
			?>
		</div>
	</div>
</div>
