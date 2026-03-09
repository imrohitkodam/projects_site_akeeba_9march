<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Quick2cartModelProductpage for Product Details page
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       1.6.7
 */
class Quick2cartModelProductpage extends BaseDatabaseModel
{
	/**
	 * Method to get the extra fields information
	 *
	 * @param   array  $item_id  Id of the record
	 *
	 * @return	Countable|array field data
	 *
	 * @since	1.8.5
	 */
	public function getDataExtra($item_id = null)
	{
		if (empty($item_id))
		{
			$input   = Factory::getApplication()->input;
			$item_id = $input->get('item_id', '', 'INT');
		}

		if (empty($item_id))
		{
			return false;
		}

		$TjfieldsHelperPath = JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php';

		if (!class_exists('TjfieldsHelper'))
		{
			JLoader::register('TjfieldsHelper', $TjfieldsHelperPath);
			JLoader::load('TjfieldsHelper');
		}

		$tjFieldsHelper     = new TjfieldsHelper;
		$data               = array();
		$data['client']     = 'com_quick2cart.product';
		$data['content_id'] = $item_id;
		$extra_fields_data  = $tjFieldsHelper->FetchDatavalue($data);

		return $extra_fields_data;
	}

	/**
	 * Method to get product from database
	 *
	 * @param   $productId  Product ID
	 * @param   $userId     User ID
	 *
	 * @return	boolean     value if data exist or not in DB
	 * */
	function isFavorite($productId, $userId)
	{	
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
					->select('COUNT(*)')
					->from($db->quoteName('#__kart_favourite'))
					->where($db->quoteName('user_id') . ' = ' . (int) $userId)
					->where($db->quoteName('item_id') . ' = ' . (int) $productId);
		$db->setQuery($query);
		return (bool) $db->loadResult();
	}

	/**
	 * Method to Insert product As a Favourite
	 *
	 * @param   $productId  Product ID
	 * @param   $userId     User ID
	 *
	 * @return	status sql query
	 * */
	function addAsFavourite($productId, $userId)
	{	
		$db    = Factory::getDbo();
        $query = $db->getQuery(true);
		            // Add to favorites insert query
					$query->insert($db->quoteName('#__kart_favourite'))
					->columns(['item_id', 'user_id'])
					->values((int) $productId. ',' . (int) $userId);

					try {
						$db->setQuery($query);
						$db->execute();
						return json_encode(['success' => true]);
					} catch (Exception $e) {
						return json_encode(['success' => false, 'message' => 'Database error']);
					}
	}

	/**
	 * Method to Delet product from favourite
	 *
	 * @param   $productId  Product ID
	 * @param   $userId     User ID
	 *
	 * @return	status sql query
	 * */
	function removeFromFavourite($productId, $userId)
	{	
		$db    = Factory::getDbo();
        $query = $db->getQuery(true);
			   // Remove from favorites query
			   $query->delete($db->quoteName('#__kart_favourite'))
			   ->where('user_id = ' . (int) $userId)
			   ->where('item_id = ' . (int) $productId);

			   try {
				$db->setQuery($query);
				$db->execute();
				return json_encode(['success' => true]);
			} catch (Exception $e) {
				return json_encode(['success' => false, 'message' => 'Database error']);
			}
	}
}
