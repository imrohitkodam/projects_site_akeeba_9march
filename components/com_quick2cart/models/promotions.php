<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Methods supporting a list of Quick2cart records.
 *
 * @since  1.6
 */
class Quick2cartModelPromotions extends ListModel
{
/**
	* Constructor.
	*
	* @param   array  $config  An optional associative array of configuration settings.
	*
	* @see    JController
	* @since  1.6
	*/
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.`id`',
				'store_id', 'a.`store_id`',
				'state', 'a.`state`',
				'name', 'a.`name`',
				'description', 'a.`description`',
				'from_date', 'a.`from_date`',
				'exp_date', 'a.`exp_date`',
				'code', 'a.`code`',
				'value', 'a.`value`',
				'val_type', 'a.`val_type`',
				'max_use', 'a.`max_use`',
				'max_per_user', 'a.`max_per_user`',
				'max_discounts', 'a.`max_discounts`',
				'extra_params', 'a.`extra_params`',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function populateState($ordering = 'a.id', $direction = 'desc')
	{
		// Initialise variables.
		$app = Factory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'STRING');
		$this->setState('filter.search', $search);

		$state = $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'INT');
		$this->setState('filter.state', $state);

		$store_filter = $this->getUserStateFromRequest($this->context . '.filter.store', 'filter_store', '', 'INT');
		$this->setState('filter.store', $state);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_quick2cart');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return   string A store id.
	 *
	 * @since    1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.state');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		$jinput     = Factory::getApplication();
		$baseUrl    = $jinput->input->server->get('REQUEST_URI', '', 'STRING');
		$calledFrom = (strpos($baseUrl, 'administrator'))?'backend':'frontend';

		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'DISTINCT a.*'));
		$query->from('`#__kart_promotions` AS a');

		// Only promotions of stores owned by users should be visible
		$user   = Factory::getUser();
		$userId = $user->id;
		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
		$userStores  = (array) $storeHelper->getUserStore($userId);

		if (!empty($userStores))
		{
			$storeIds = array();

			foreach ($userStores as $store)
			{
				$storeIds[] = $store['id'];
			}

			if (!empty($storeIds) && $calledFrom  == 'frontend')
			{
				$query->where($db->quoteName('store_id') . 'IN (' . implode(',', $storeIds) . ')');
			}
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		$store  = $this->getState('filter.store');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.name LIKE ' . $search . ')');
			}
		}

		if (!empty($store))
		{
			$query->where('a.store_id = ' . $store);
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Get an array of data items
	 *
	 * @return mixed Array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		return $items;
	}

	/**
	 * Function to delete promotion related data
	 *
	 * @param   ARRAY  $cid  array of promotion ids
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	public function delete($cid)
	{
		if (!empty($cid))
		{
			$app = Factory::getApplication();
			$db  = Factory::getDbo();

			// Load tables
			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

			foreach ($cid as $id)
			{
				if (!empty($id))
				{
					$promotionTable = Table::getInstance('promotion', 'Quick2cartTable', array('dbo', $db));
					$promotionTable->load(array('id' => $id));
					$data = $promotionTable->getProperties();

					// On before promotion delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeletePromotion", array($data));

					$query = $db->getQuery(true);
					$query->delete($db->quoteName('#__kart_promotions_rules'));
					$query->where($db->quoteName('promotion_id') . " = " . $id);
					$db->setQuery($query);
					$db->execute();

					$query = $db->getQuery(true);
					$query->delete($db->quoteName('#__kart_promotion_discount'));
					$query->where($db->quoteName('promotion_id') . " = " . $id);
					$db->setQuery($query);
					$db->execute();

					// On after promotion delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onAfterQ2cDeletePromotion", array($data));
				}
			}

			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Fetch eligible users for one or more promotions based on their total order amount.
	 *
	 * For each given promotion ID, this function checks if specific user promotions are allowed,
	 * and then identifies users whose total spending meets or exceeds the defined order threshold.
	 * Also fetches relevant promotion metadata like discount, coupon requirement, and expiry.
	 *
	 * @param   array  $promotionIds  Array of promotion IDs to evaluate eligibility for
	 *
	 * @return  array  Associative array with promotion ID as key, and value as:
	 * 
	 */
	function getEligibleUsersForPromotions(array $promotionIds)
	{
		$db = Factory::getDbo();
		$results = [];

		if (empty($promotionIds)) {
			return $results;
		}

		// Step 1: Get all promotion details
		$quotedIds = array_map('intval', $promotionIds);
		$idsString = implode(',', $quotedIds);

		$query = $db->getQuery(true)
			->select([
				'a.id',
				'a.orderamount',
				'a.allowspecificuserpromotion',
				'a.exp_date',
				'a.coupon_code',
				'a.coupon_required',
				'a.discount_type',
				'b.discount'
			])
			->from($db->quoteName('#__kart_promotions', 'a'))
			->join('LEFT', $db->quoteName('#__kart_promotion_discount', 'b') . ' ON ' . $db->quoteName('a.id') . ' = ' . $db->quoteName('b.promotion_id'))
			->where('a.id IN (' . $idsString . ')');

		$db->setQuery($query);

		try {
			$promotions = $db->loadAssocList('id');
		} catch (RuntimeException $e) {
			Factory::getApplication()->enqueueMessage('COM_QUICK2CART_ERROR_FETCHING_PROMOTIONS' . $e->getMessage(), 'error');
			return $results;
		}

		// Step 2: For each promotion, find eligible users
		foreach ($promotions as $promotionId => $promotion)
		{
			$threshold     = (int) $promotion['orderamount'];
			$allowSpecific = (int) $promotion['allowspecificuserpromotion'];

			if ((int)$promotion['coupon_required'] !== 1) {
				unset($promotion['coupon_code']);
			}

			// Skip if not allowed for specific users
			if ($allowSpecific !== 1) {
				$results[$promotionId] = [
					'users' => [],
					'promotion' => $promotion
				];
				continue;
			}

			$query = $db->getQuery(true)
				->select('user_info_id, SUM(amount) AS total_spent')
				->from($db->quoteName('#__kart_orders'))
				->where($db->quoteName('status') . ' = ' . $db->quote('C'))
				->group('user_info_id')
				->having('total_spent >= ' . (int) $threshold);

			$db->setQuery($query);

			try {
				$userResults = $db->loadAssocList();
				$userIds = array_column($userResults, 'user_info_id');
			} catch (RuntimeException $e) {
				Factory::getApplication()->enqueueMessage('COM_QUICK2CART_ERROR_FETCHING_ELIGIBLE_USERS' . $e->getMessage(), 'error');
				$userIds = [];
			}

			$results[$promotionId] = [
				'users' => $userIds,
				'promotion' => $promotion
			];
		}

		return $results;
	}
}
