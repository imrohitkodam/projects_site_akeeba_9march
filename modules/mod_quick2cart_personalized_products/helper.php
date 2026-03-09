<?php
/**
 * @version    SVN: <svn_id>
 * @package    Quick2Cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2025 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

class ModQuick2CartPersonalizedProductHelper
{
    /**
     * Retrieves a list of suggested products for the current user based on their purchase history.
     *
     * @param   int|null  $excludeProductId   (Optional) Product ID to exclude from suggestions (e.g. currently viewed product).
     * @param   int       $no_of_products     Number of products to return in the suggestion list.
     *
     * @return  array     List of suggested product objects, each including image path and product URL.
     *
     * @since   5.1.0
     */
    public static function getSuggestedProducts($excludeProductId = null, $no_of_products = 5)
    {
        $db     = Factory::getDbo();
        $user   = Factory::getUser();
        $userId = (int) $user->id;

        if (!$userId)
        {
            return [];
        }

        // Step 1: Get user’s purchased products and categories
        $query = $db->getQuery(true)
            ->select('DISTINCT p.category, p.item_id')
            ->from('#__kart_order_item AS oi')
            ->join('INNER', '#__kart_orders AS o ON oi.order_id = o.id')
            ->join('INNER', '#__kart_items AS p ON oi.item_id = p.item_id')
            ->where('o.payee_id = ' . $db->quote($userId));

        $db->setQuery($query);
        $rows = $db->loadObjectList();

        if (empty($rows))
        {
            return [];
        }

        $categories = [];
        $purchasedProductIds = [];

        foreach ($rows as $row)
        {
            $categories[] = (int) $row->category;
            $purchasedProductIds[] = (int) $row->item_id;
        }

        $categories = array_unique($categories);
        $purchasedProductIds = array_unique($purchasedProductIds);

        if (count($categories) === 0)
        {
            return [];
        }

        $limitPerCategory = ceil($no_of_products / count($categories));
        $featuredProducts = [];
        $nonFeaturedProducts = [];

        // Step 2: Fetch products from each category
        $query = $db->getQuery(true)
            ->select('p.*')
            ->from('#__kart_items AS p')
            ->where('p.state = 1')
            ->where('p.category IN (' . implode(',', $categories) . ')');

        if (!empty($purchasedProductIds))
        {
            $query->where('p.item_id NOT IN (' . implode(',', $purchasedProductIds) . ')');
        }

        if (!empty($excludeProductId))
        {
            $query->where('p.item_id != ' . (int) $excludeProductId);
        }

        $query->order('p.featured DESC, p.item_id DESC');
        $db->setQuery($query);
        $allEligibleProducts = $db->loadObjectList();

        // Split featured and non-featured
        $featuredProducts = [];
        $nonFeaturedProducts = [];

        foreach ($allEligibleProducts as $product) {
            if (!empty($product->featured)) {
                $featuredProducts[] = $product;
            } else {
                $nonFeaturedProducts[] = $product;
            }
        }

        // Shuffle only non-featured
        shuffle($nonFeaturedProducts);

        // Merge: featured first, then shuffled non-featured
        $allProducts = array_merge($featuredProducts, $nonFeaturedProducts);

        // Limit total output
        $allProducts = array_slice($allProducts, 0, $no_of_products);

        // Add image and product URL
        foreach ($allProducts as &$product)
        {
            $product->image = !empty($product->image) ? JUri::root() . ltrim($product->image, '/') : '';
            $product->product_url = JRoute::_('index.php?option=com_quick2cart&view=product&pid=' . (int) $product->item_id, true);
        }

        return $allProducts;
    }


    /**
     * Checks if the specified user has purchased any products.
     *
     * Useful for deciding whether to show personalized recommendations or fallback content.
     *
     * @param   int|null  $userId  (Optional) User ID to check. If null, currently logged-in user is used.
     *
     * @return  bool      True if the user has purchased at least one product, false otherwise.
     *
     * @since   5.1.0
     */
    public static function hasPurchasedProducts($userId = null)
    {
        $db = Factory::getDbo();

        // Use current logged-in user if no ID is passed
        if ($userId === null)
        {
            $user = Factory::getUser();
            $userId = (int) $user->id;
        } 
        else
        {
            $userId = (int) $userId;
        }

        // Invalid or guest user
        if ($userId <= 0)
        {
            return false;
        }

        // Count how many distinct products the user has purchased
        $query = $db->getQuery(true)
            ->select('COUNT(DISTINCT oi.item_id)')
            ->from('#__kart_order_item AS oi')
            ->join('INNER', '#__kart_orders AS o ON oi.order_id = o.id')
            ->where('o.payee_id = ' . $userId);

        $db->setQuery($query);
        $count = (int) $db->loadResult();

        return $count > 0;
    }
}
?>
