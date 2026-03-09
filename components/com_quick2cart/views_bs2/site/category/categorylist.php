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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

$comquick2cartHelper = new comquick2cartHelper;
$productHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/productHelper.php", "productHelper");
$input = Factory::getApplication()->input;

// Start vars
$qtc_catname  = !empty($qtc_catname)  ? $qtc_catname  : "prod_cat";
$qtc_store_id = !empty($qtc_store_id) ? $qtc_store_id : "";
$qtc_view     = !empty($qtc_view)     ? $qtc_view     : "category";
$qtc_layout   = !empty($qtc_layout)   ? $qtc_layout   : "";

$app       = Factory::getApplication();
// Load the JMenuSite Object
$menu      = $app->getMenu();

// Load the Active Menu Item as an stdClass Object
$activeMenuItem    = $menu->getActive();

// If product category not found in URL then assign product category according menu
if (!empty($activeMenuItem))
{
	$show_subcat_products = $activeMenuItem->params->get('show_subcat_products', '0', 'INT');

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

	if (!class_exists('HTMLHelperCategory'))
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

<div class="row-fluid qtc_category_list <?php echo $classes;?>" style="<?php echo $scroll_style;?>">
	<div class="span12">
		<div class="tj-list-group">
			<strong class="tj-list-group-item"><?php echo  !empty($catListHeader) ? $catListHeader : Text::_('QTC_PROD_SEL_CAT_HEADER'); ?></strong>

			<?php
			$selectedcat = $input->get($qtc_catname, 0, 'INTEGER');

			if (empty($selectedcat))
			{
				if ($activeMenuItem->params)
				{
					$selectedcat = $activeMenuItem->params->get('defaultCatId', '', 'INT');
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
					if (empty($menuItem->params->get('defaultCatId')) && empty($menuItem->params->get('defaultCatId')))
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

			foreach ($cats as $cat)
			{
				if (!empty($categoryProductsCount[$cat->value]['count']))
				{
					// Making value = '' to value = 0 for all product
					$cat->value = !empty ($cat->value) ? $cat->value : 0;

					// GETTING ITEM ID
					$catItemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&' . $qtc_linkparam . "&prod_cat=" . $cat->value);

					$catlink = Uri::root().substr(Route::_('index.php?option=com_quick2cart&' . $qtc_linkparam . '&' . $qtc_catname . '=' . $cat->value . '&Itemid=' . $catItemid . $itsStoreOwner), strlen(Uri::base(true)) + 1);

					$activecat = "";

					if ($selectedcat == $cat->value)
					{
						$activecat = "active";
					}
					?>

					<a class="tj-list-group-item <?php echo $activecat;?>" href="<?php echo $catlink ;?>">
						<?php if (isset($categoryProductsCount[$cat->value]['count'])): ?>
							<span class="badge"><?php echo $categoryProductsCount[$cat->value]['count'];?></span>
						<?php endif; ?>
						<?php echo $cat->text; ?>
					</a>
				<?php
				}
			}
		?>
		</div>
	</div>
</div>
