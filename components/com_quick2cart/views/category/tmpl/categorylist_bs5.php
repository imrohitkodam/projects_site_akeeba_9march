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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\FileLayout;

$comquick2cartHelper = new comquick2cartHelper;
$productHelper       = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/product.php", "ProductHelper");
$app                 = Factory::getApplication();
$input               = $app->input;

// Start vars
$qtc_catname  = !empty($qtc_catname)  ? $qtc_catname  : "prod_cat";
$qtc_store_id = !empty($qtc_store_id) ? $qtc_store_id : "";
$qtc_view     = !empty($qtc_view)     ? $qtc_view     : "category";
$qtc_layout   = !empty($qtc_layout)   ? $qtc_layout   : "default";

// Load the JMenuSite Object
$menu           = $app->getMenu();
$activeMenuItem = $menu->getActive();

// If product category not found in URL then assign product category according menu
if (!empty($activeMenuItem))
{
	$show_subcat_products = $activeMenuItem->getParams()->get('show_subcat_products', '0', 'INT');

	if (empty($show_subcat_products))
	{
		$categoryProductsCount = $productHelper->getCategoryProductsCount($qtc_store_id, 0);
	}
	else
	{
		$categoryProductsCount = $productHelper->getCategoryProductsCount($qtc_store_id, 1);
	}
}
else
{
	$categoryProductsCount = $productHelper->getCategoryProductsCount($qtc_store_id, 1);
}

$classes = !empty($qtc_classes) ? $classes : '';
$max_scroll_ht = !empty($qtc_mod_scroll_height) ? trim($qtc_mod_scroll_height) . 'px' : '412px';
$scroll_style = "overflow-y:auto; max-height:" . $max_scroll_ht . "; overflow-x:hidden;"
// End vars
?>

<?php
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

// If VIEW RELEATED CATS (viewReleated_cats) found then use that, otherwise generate cats
$options = array();

if (!empty($viewReleated_cats))
{
	// CHANGE DEFAULT LAGUATE CONST
	// FOR STORE VIEW , WE SHOULD SHOW ONLY STORE CATEGORY
	$cats = $viewReleated_cats;
}
else
{
	$options = (array) $options;
	$comp_option = $input->get("option");

	// JUGAD fix for error #20162
	$path = JPATH_SITE . '/libraries/joomla/html/html/category.php';

	if (!class_exists('JHtmlCategory'))
	{
		JLoader::register('JHtmlCategory', $path );
		JLoader::load('JHtmlCategory');
	}

	// JUGAD fix for error #20162
	if (!empty($comp_option))
	{
		$qtc_cat_options = HTMLHelper::_('category.options', 'com_quick2cart', array('filter.published' => array(1)));
	}
	else
	{
		// Get categories manually
		$qtc_cat_options = $comquick2cartHelper->getQ2cCats(1);
	}

	$cats = array_merge($options, $qtc_cat_options);
}

// GETTING ITEM ID
$catItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&' . $qtc_linkparam . "&prod_cat=");
?>

<div class="row qtc_category_list <?php echo $classes;?>" style="<?php echo $scroll_style;?>">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="tj-list-group">
			<strong class="tj-list-group-item"><?php echo  !empty($catListHeader) ? $catListHeader : Text::_('QTC_PROD_SEL_CAT_HEADER'); ?></strong>

			<?php
			$selectedcat = $input->get($qtc_catname, 0, 'INTEGER');

			if (empty($selectedcat))
			{
				if (!empty($activeMenuItem))
				{
					$selectedcat = $activeMenuItem->getParams()->get('defaultCatId', '', 'INT');
				}
			}

			// Store owner call then add
			$option = $input->get( 'option','','STRING' );
			$itsStoreOwner = "";
			$qtc_storeOwner = $input->get('qtcStoreOwner', 0, 'INTEGER');

			if ($qtc_storeOwner == 1)
			{
				$itsStoreOwner = "&qtcStoreOwner=1&qtcCatCall=1";
			}

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

			$allcatlink = Uri::root().substr(Route::_('index.php?option=com_quick2cart&' . $qtc_linkparam . '&' . $qtc_catname . '=0&Itemid=' . $allProductsMenuItemId . $itsStoreOwner), strlen(Uri::base(true))+1);

			$allactivecat = "";

			if ($selectedcat == 0)
			{
				$allactivecat = "active";
			}

			if (empty($storeHomePage))
			{
				// DONT SHOW ALL PRODUCT ON STORE HOME PAGE
				?>
				<a class="tj-list-group-item <?php echo $allactivecat;?>" href="<?php echo $allcatlink ;?>">
					<?php echo Text::_('QTC_ALL_PROD'); ?>
				</a>
				<?php
			}

			// Added by manoj
			if (!empty($viewReleated_cats))
			{
				// Unset first select option - Select Category
				unset($cats[0]);
				?>
				<a class="tj-list-group-item <?php echo $allactivecat;?>" href="<?php echo $allcatlink ;?>">
					<?php echo Text::_('QTC_ALL_PROD'); ?>
				</a>
				<?php
			}

			$layout = new FileLayout('categories_list_view', JPATH_ROOT . '/components/com_quick2cart/layouts/categories');

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
