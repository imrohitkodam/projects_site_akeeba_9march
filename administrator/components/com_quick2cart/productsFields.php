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
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;

/**
 * Trait to handle extra functionality
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       __DEPLOY_VERSION__
 */
trait Quick2cartProductsFields
{
	/**
	 * Method to set item as out of stock
	 *
	 * @param   integer  $item_id  Product primary key
	 *
	 * @return	boolean true or false
	 *
	 * @since	__DEPLOY_VERSION__
	 */
	public function makeItemOutOfStock($itemId)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('item_id');
		$query->from($db->qn('#__kart_items', 'i'));
		$query->where($db->qn('i.parent_id') . ' = ' . (int) $itemId);
		$db->setQuery($query);
		$childProductsOfItem = $db->loadColumn();

		$productTable = Table::getInstance('Product', 'Quick2cartTable', array());

		// Make child product out of stock 
		if (!empty($childProductsOfItem))
		{
			foreach($childProductsOfItem as $key => $childItem)
			{
				$productTable->load($childItem);
				$productTable->stock = 0;

				if (!$productTable->store())
				{
					return false;
				}
			}
		}

		// Make main product out of stock 
		$productTable->load($itemId);

		// Set item stock as 0 to make as out of stock
		$productTable->stock = 0;

		if (!$productTable->store())
		{
			return false;
		}

		return true;
	}
}
