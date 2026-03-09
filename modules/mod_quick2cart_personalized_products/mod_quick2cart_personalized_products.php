<?php
/**
 * @package    Quick2Cart
 * @author     Techjoomla
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Factory;

// Get the current product ID to exclude from suggestions
$excludeProductId = (int) Factory::getApplication()->input->get('item_id');

// Load module helper
require_once __DIR__ . '/helper.php';
$helper = new ModQuick2CartPersonalizedProductHelper;

$no_of_products = (int) $params->get('no_of_products_show', 5);

HTMLHelper::stylesheet(Uri::root() . 'media/techjoomla_strapper/vendors/font-awesome/css/font-awesome.min.css');

// Init user and app
$app  = Factory::getApplication();
$user = Factory::getUser();
$data = [];

// If user is logged in and has purchase history, fetch personalized suggestions
if (!$user->guest)
{
	if ($helper->hasPurchasedProducts($user->id))
	{
		$data = $helper->getSuggestedProducts($excludeProductId, $no_of_products);
	}
	else
	{
		return 0; // No purchases, nothing to suggest
	}
}
else
{
	return 0; // Guest users don't get personalized products
}

// Load module layout
require ModuleHelper::getLayoutPath('mod_quick2cart_personalized_products', $params->get('layout', 'default'));