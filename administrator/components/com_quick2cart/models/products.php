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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\Utilities\ArrayHelper;

JLoader::import('productsFields', JPATH_ADMINISTRATOR . '/components/com_quick2cart/');

/**
 * Methods supporting a list of all products.
 *
 * @package  Quick2Cart
 *
 * @since    1.0
 */
class Quick2cartModelProducts extends ListModel
{
	use Quick2cartProductsFields;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   2.2
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'item_id', 'a.item_id',
				'name', 'a.name',
				'state', 'a.state',
				'featured', 'a.featured',
				'parent', 'a.parent',
				'category', 'a.category',
				'store_id', 'a.store_id',
				'cdate', 'a.cdate',
				'item_id', 'a.item_id',
				'published', 'a.state',
				'store', 'a.store_id',
				'client', 'a.parent',
				'ordering', 'a.ordering'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = Factory::getApplication('administrator');

		// List state information.
		$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'));
		$this->setState('list.limit', $limit);

		$limitstart = Factory::getApplication()->input->getInt('limitstart', 0);

		if ($limit == 0)
		{
			$this->setState('list.start', 0);
		}
		else
		{
			$this->setState('list.start', $limitstart);
		}

		// Set ordering.
		$orderCol = $app->getUserStateFromRequest($this->context . 'filter_order', 'filter_order', "a.item_id");

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.item_id';
		}

		$this->setState('list.ordering', $orderCol);

		// Set ordering direction.
		$listOrder = $app->getUserStateFromRequest($this->context . 'filter_order_Dir', 'filter_order_Dir', 'DESC');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}

		$this->setState('list.direction', $listOrder);
		$app->setUserState($this->context . '.list.direction', $listOrder);

		// Load the filter state.
		$search = $app->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		$published = $app->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		// Filter client.
		$client = $app->getUserStateFromRequest($this->context . '.filter.client', 'filter_client', '', 'string');
		$this->setState('filter.client', $client);

		// Filter category.
		$category = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'string');
		$this->setState('filter.category', $category);

		// Filter store.
		$store = $app->getUserStateFromRequest($this->context . '.filter.store', 'filter_store', '', 'string');
		$this->setState('filter.store', $store);

		// Load the parameters.
		$params = ComponentHelper::getParams('com_quick2cart');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($orderCol, $listOrder);
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
	 * @return  string  A store id.
	 *
	 * @since   1.6
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
	 * @return  JDatabaseQuery
	 *
	 * @since   1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->from('`#__kart_items` AS a');
		$query->where('a.display_in_product_catlog = 1');

		// Filter by search in title.
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.item_id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
				$query->where('( a.name LIKE ' . $search . ' )');
			}
		}

		// Filter by published state.
		$published = $this->getState('filter.published');

		if (is_numeric($published))
		{
			$query->where('a.state = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by category.
		$filter_client = $this->state->get("filter.client");

		if ($filter_client)
		{
			$query->where("a.parent = '" . $db->escape($filter_client) . "'");
		}

		// Filter by category.
		$filter_category = $this->state->get("filter.category");

		if ($filter_category)
		{
			$query->where("a.category = '" . $db->escape($filter_category) . "'");
		}

		// Filter by store.
		$filter_store = $this->state->get("filter.store");

		if ($filter_store)
		{
			$query->where("a.store_id = '" . $db->escape($filter_store) . "'");
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
	 * Method to get a list of products.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 *
	 * @since   1.6.1
	 */
	public function getItems()
	{
		$items = parent::getItems();
		$comquick2cartHelper             = new comquick2cartHelper;
		$quick2cartBackendProductsHelper = new quick2cartBackendProductsHelper;
		$store_details = $comquick2cartHelper->getAllStoreDetails();

		foreach ($items as $item)
		{
			// Get product category
			$item->category_id = $item->category;
			$catname           = $comquick2cartHelper->getCatName($item->category);
			$item->category    = !empty($catname) ? $catname : $item->category;

			// Get store name
			$item->store_name = (!empty($store_details[$item->store_id])) ? $store_details[$item->store_id]['title'] : '';

			// Get store owner
			$item->store_owner     = '';
			$item->store_owner_id  = 0;
			$item->store_vendor_id = 0;

			if (!empty($store_details[$item->store_id]))
			{
				$item->store_owner     = $store_details[$item->store_id]['firstname'];
				$item->store_owner_id  = $store_details[$item->store_id]['owner'];
				$item->store_vendor_id = $store_details[$item->store_id]['vendor_id'];
			}

			$item->edit_link = $quick2cartBackendProductsHelper->getProductLink($item->item_id, 'editLink');
			$item->parent    = $quick2cartBackendProductsHelper->getProductParentName($item->item_id);

			$productCurrencyWisePrices = $comquick2cartHelper->getProductAllPrices($item->item_id);
			$item->multi_cur           = $productCurrencyWisePrices['multi_cur'];
			$item->multi_dis_cur       = $productCurrencyWisePrices['multi_dis_cur'];

			// Don't remove it, this will need lated for import/export products
			//$item->att_detail          = $this->getItemAttributeData($item->item_id);
		}

		return $items;
	}

	/**
	 * Method to edit list
	 *
	 * @param   string  $zoneid  An optional ordering field.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function Editlist($zoneid)
	{
		unset($this->_data);
		$query       = "SELECT * from #__kart_coupon where id=$zoneid";
		$this->_data = $this->_getList($query);

		return $this->_data;
	}

	/**
	 * Method to toggle the featured setting of products.
	 *
	 * @param   Array    $pks    PKS
	 * @param   Integer  $value  Value
	 *
	 * @return  boolean  True on success.
	 */
	public function featured($pks, $value = 0)
	{
		// Sanitize the ids.
		$pks = (array) $pks;
		ArrayHelper::toInteger($pks);

		try
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true)
						->update($db->quoteName('#__kart_items'))
						->set('featured = ' . (int) $value)
						->where('item_id IN (' . implode(',', $pks) . ')');
			$db->setQuery($query);
			$db->execute();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Method to get store name
	 *
	 * @param   string  $store_id  An optional store_id
	 *
	 * @return  string  $exists
	 *
	 * @since   1.6
	 */
	public function getStoreNmae($store_id)
	{
		if (!empty($store_id))
		{
			$db  = Factory::getDBO();
			$qry = "SELECT `title` FROM #__kart_store WHERE id=" . $store_id;
			$db->setQuery($qry);

			return $exists = $db->loadResult();
		}
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__kart_items');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 *
	 * @link    http://docs.joomla.org/JTable/delete
	 * @since   11.1
	 * @throws  UnexpectedValueException
	 */
	public function delete ($pk = null)
	{
		$table = $this->getTable();
		$table->delete($pk);
	}

	/**
	 * Method to get Item attribute data
	 *
	 * @param   integer  $item_id  Product table primary key or item id
	 *
	 * @return  Array  Item Attribute detail
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getItemAttributeData($item_id)
	{
		$att_detail = array();

		if (isset($item_id))
		{
			$comquick2cartHelper = new comquick2cartHelper;
			JLoader::import('product', JPATH_SITE. '/components/com_quick2cart/helpers');
			$productHelper = new ProductHelper;
			$attributes = (array) $productHelper->getAttributes($item_id);

			foreach ($attributes as $key => $value)
			{
				$att_detail[$key]['global_attribute_set'] = $value->itemattribute_name;
				$att_detail[$key]['attri_name']           = $value->itemattribute_name;
				$att_detail[$key]['attri_id']             = $value->itemattribute_id;
				$att_detail[$key]['global_atrri_id']      = $value->global_attribute_id;
				$att_detail[$key]['fieldType']            = $value->attributeFieldType;
				$att_detail[$key]['attri_opt']            = array();

				$attributeOptionsrawdata = $comquick2cartHelper->getAttributeOption($value->itemattribute_id);

				$attri_opt = array();

				if (!empty($attributeOptionsrawdata))
				{
					foreach ($attributeOptionsrawdata as $index => $attOption)
					{
						$attri_opt[$index]['id']                     = $attOption->itemattributeoption_id;
						$attri_opt[$index]['globalOptionId']         = $attOption->global_option_id;
						$attri_opt[$index]['child_product_item_id']  = $attOption->child_product_item_id;
						$attri_opt[$index]['name']                   = $attOption->itemattributeoption_name;
						$attri_opt[$index]['state']                  = $attOption->state;
						$attri_opt[$index]['prefix']                 = $attOption->itemattributeoption_prefix;
						$attri_opt[$index]['currency']               = $this->getAttributeOptionCurrencyData($attOption->itemattributeoption_id);
						$attri_opt[$index]['order']                  = $attOption->ordering;

						$att_detail[$key]['attri_opt'] = $attri_opt;
					}
				}
			}
		}

		return $att_detail;
	}

	/**
	 * Method to get each Attribute option currency details
	 *
	 * @param   integer  $itemattributeoption_id  Attribute option id
	 *
	 * @return  Array  attOptionCurrencyPrice Attribute option currency detail
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getAttributeOptionCurrencyData($itemattributeoption_id)
	{
		$attOptionCurrencyPrice = array();
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__kart_option_currency', 'oc'));
		$query->where($db->qn('oc.itemattributeoption_id') . ' = ' . (int) $itemattributeoption_id);
		$db->setQuery($query);
		$attributeOptionCurrencyData = $db->loadAssocList();

		if (!empty($attributeOptionCurrencyData))
		{
			foreach ($attributeOptionCurrencyData as $key => $attOptionCurrencyData)
			{
				$attOptionCurrencyPrice[$attOptionCurrencyData['currency']] = $attOptionCurrencyData['price'];
			}
		}

		return $attOptionCurrencyPrice;
	}

	/**
	 * Method to return csv file of products
	 *
	 * @param   Object  $items  items list
	 *
	 * @param   String  $headerFlag  By using this we can generate dynamically sample csv import file
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function productCsvExport($items, $headerFlag = '0')
	{
		$app       = Factory::getApplication();
		$input     = $app->input;
		$seperator = ',';
		$enclosure = '"';
		$fileName  = substr($input->get('option'), 4) . "_" . $input->get('view') . "_" . date("Y-m-d_H-i-s", time()) . '_' . rand() . '.' . 'csv';
		$path      = JPATH_SITE . '/tmp/' . $fileName;
		$file      = fopen($path, 'a');

		$config   = Factory::getConfig();
		$usedData = array('item_id', 'name', 'stock', 'min_quantity', 'max_quantity', 'category_id', 'multi_cur', 'multi_dis_cur');

		if (!empty($items))
		{
			$header     = array();
			$getHeaders = (array) $items[0];
			$params     = ComponentHelper::getParams('com_quick2cart');

			// Get Quick2cart configuration currency values 
			$addcurrency         = explode(",", $params->get('addcurrency' ,'', 'String'));
			$currencyHeaderArray = $addcurrency;

			foreach ($getHeaders as $key => $value)
			{
				if (!in_array($key, $usedData))
				{
					continue;
				}

				if (is_array($value))
				{
					if ($key == 'multi_cur')
					{
						$header[] = ucfirst('prod_price' . '|'. implode("|",array_values($currencyHeaderArray)));
					}
					elseif ($key == 'multi_dis_cur')
					{
						$header[] = ucfirst('prod_dic_price' . '|'. implode("|",array_values($currencyHeaderArray)));
					}
				}
				else
				{
					$header[] = ucfirst($key);
				}
			}

			fputcsv($file, $header, $seperator, $enclosure);

			if ($headerFlag == '0')
			{
				foreach ($items as $item)
				{
					$rec = $item;
					$csvRow     = array();

					if (is_object($item))
					{
						$rec = (array) $item;
					}

					foreach ($rec as $key => $v)
					{
						if (!in_array($key, $usedData))
						{
							continue;
						}

						if (is_array($v))
						{
							if ($key == 'multi_cur')
							{
								$updatedMultiCurr = array();

								foreach ($currencyHeaderArray as $confcurr)
								{
									foreach ($v as $multiCurrKey => $multiCurrVal)
									{
										if ($multiCurrKey == $confcurr)
										{
											$updatedMultiCurr[$multiCurrKey] =  $multiCurrVal;
										}
									}
								}

								if (!empty($updatedMultiCurr))
								{
									$csvRow[] = implode("|",array_values($updatedMultiCurr));
								}
							}
							elseif ($key == 'multi_dis_cur')
							{
								$updatedMultiDisCurr = array();

								foreach ($currencyHeaderArray as $confcurrency)
								{
									foreach ($v as $multiDisKey => $multiDisVal)
									{
										if ($multiDisKey == $confcurrency)
										{
											$updatedMultiDisCurr[$multiDisKey] =  $multiDisVal;
										}
									}
								}

								if (!empty($updatedMultiDisCurr))
								{
									$csvRow[] = implode("|",array_values($updatedMultiDisCurr));
								}
							}
						}
						else
						{
							$csvRow[] = $v;
						}
					}

					fputcsv($file, $csvRow, $seperator, $enclosure);
				}
			}

			fclose($file);

			$this->download($fileName);
			$app->close();
			jexit();
		}
	}

	/**
	 * Common function to download the csv file.
	 *
	 * @param   string  $file  File path
	 *
	 * @return  void|boolean On success void on failure false
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function download($file)
	{
		$config = Factory::getConfig();

		if (empty($file))
		{
			return false;
		}

		$file = $config->get('tmp_path') . '/' . $file;

		if (fopen($file, "r"))
		{
			$fsize = filesize($file);
			$path_parts = pathinfo($file);

			header("Cache-Control: public, must-revalidate");
			header('Cache-Control: pre-check=0, post-check=0, max-age=0');
			header("Expires: 0");
			header("Content-Description: File Transfer");
			header("Content-Type: text/csv");
			header("Content-Length: " . (string) $fsize);
			header("Content-Disposition: filename=\"" . $path_parts["basename"] . "\"");
			$fd = fopen($file, "r");

			if (empty($fd))
			{
				return false;
			}

			while (!feof($fd))
			{
				$buffer = fread($fd, 2048);
				echo $buffer;
			}

			fclose($fd);
		}

		ignore_user_abort(true);
		unlink($file);
	}
}
