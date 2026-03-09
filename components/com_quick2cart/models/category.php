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
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

/**
 * Methods supporting a list of stores records.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       1.0
 */
class Quick2cartModelCategory extends ListModel
{
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
				'id', 'a.item_id',
				'name', 'a.name',
				'state', 'a.state',
				'featured', 'a.featured',
				'parent', 'a.parent',
				'category', 'a.category',
				'store_id', 'a.store_id',
				'cdate', 'a.cdate',
				'item_id', 'a.item_id',
				'published', 'a.state',
				'store', 'a.store_id'
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
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$layout = $jinput->get('layout', 'default', 'STRING');

		// Variable to store prices for price filter
		$min_limit = $jinput->get('min_price', '', 'int');
		$max_limit = $jinput->get('max_price', '', 'int');

		$this->context = $this->context . $layout;

		if (!empty($min_limit) && !empty($max_limit))
		{
			if ($min_limit > $max_limit)
			{
				$temp      = $min_limit;
				$min_limit = $max_limit;
				$max_limit = $temp;
			}
		}

		if (!empty($min_limit))
		{
			$this->setState('filter.min_limit', $min_limit);
		}

		if (!empty($max_limit))
		{
			$this->setState('filter.max_limit', $max_limit);
		}

		// List state information.
		parent::populateState('a.item_id', 'desc');

		// Initialise variables.
		$app = Factory::getApplication('site');

		// Set pagination limit according from menu settings
		$itemid    = $app->input->get('Itemid', 0, 'int');
		$capplimit = $app->getParams()->get('cat_all_prod_pagination_limit');
		$limit     = $this->getUserStateFromRequest('com_quick2cart.category.list' . $itemid . '.limit', 'limit', $capplimit, 'uint');
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
		$orderCol = $this->getUserStateFromRequest($this->context . 'filter_order', 'filter_order');

		if (!in_array($orderCol, $this->filter_fields))
		{
			$orderCol = 'a.ordering';
		}

		$this->setState('list.ordering', $orderCol);

		// Set ordering direction.
		$listOrder = $this->getUserStateFromRequest($this->context . 'filter_order_Dir', 'filter_order_Dir');

		if (!in_array(strtoupper($listOrder), array('ASC', 'DESC', '')))
		{
			$listOrder = 'DESC';
		}

		$this->setState('list.direction', $listOrder);

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search', '', 'string');
		$this->setState('filter.search', $search);

		$prod_sorting = $this->getUserStateFromRequest($this->context . '.sort_products', 'sort_products', '', 'string');
		$this->setState('sort_products', $prod_sorting);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '', 'string');
		$this->setState('filter.published', $published);

		if ($layout == "my" || $layout == "select_product")
		{
			// Filter category.
			$category = $this->getUserStateFromRequest($this->context . '.filter.category', 'filter_category', '', 'string');
			$this->setState('filter.category', $category);
		}
		else
		{
			// Category filter 2
			$prod_cat = $jinput->get('prod_cat', 0, 'INTEGER');
			$this->setState('filter.category', $prod_cat);

			// Category menu
			$menu_category = $app->getParams()->get('defaultCatId');
			$this->setState('filter.menu_category', $menu_category);

			// Tag menu field
			$menu_tag = $app->getParams()->get('defaultTag');
			$this->setState('filter.menu_tag', $menu_tag);

			// Store menu
			$menu_store = $app->getParams()->get('defaultStoreId');
			$this->setState('filter.menu_store', $menu_store);

			// Category search
			$menu_category_search = $app->getParams()->get('qtcCategorySearch');
			$this->setState('filter.qtcCategorySearch', $menu_category_search);

			// Show subcategory prodcuts
			$show_subcat_products = $app->getParams()->get('show_subcat_products');
			$this->setState('filter.show_subcat_products', $show_subcat_products);
		}

		// Filter store.
		$store = $this->getUserStateFromRequest($this->context . '.filter.store', 'current_store', '', 'string');
		$this->setState('filter.store', $store);

		// Load the parameters. Merge Global and Menu Item params into new object
		$params     = $app->getParams();
		$menuParams = new Registry;

		//$params = Factory::getApplication()->getMenu()->getActive()->getParams();
		if ($menu = $app->getMenu()->getActive())
		{
			//$menuParams->loadString($menu->params);
			$menuParams->loadString($menu->getParams());
		}

		$mergedParams = clone $menuParams;
		$mergedParams->merge($params);

		$this->setState('params', $mergedParams);
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
		$app     = Factory::getApplication();
		$jinput  = $app->input;
		$db      = $this->getDbo();
		$query   = $db->getQuery(true);
		$TjfieldsHelperPath = JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php';

		if (!class_exists('TjfieldsHelper'))
		{
			JLoader::register('TjfieldsHelper', $TjfieldsHelperPath);
			JLoader::load('TjfieldsHelper');
		}

		$TjfieldsHelper  = new TjfieldsHelper;
		$tjfieldItem_ids = $TjfieldsHelper->getFilterResults();

		$client = $app->input->get('client', '', 'string');

		if (!empty($client))
		{
			if ($tjfieldItem_ids != '-2')
			{
				$query->where(" a.item_id IN (" . $tjfieldItem_ids . ") ");
			}
		}

		$user   = Factory::getUser();
		$jinput = $app->input;
		$filter = InputFilter::getInstance();

		// Sanitized the attribute option
		$attributeoption        = $jinput->get('attributeoption', '', 'string');
		$attributeFilterOptions = explode(',', $attributeoption);

		foreach ($attributeFilterOptions as $k => $attributeFilterOption)
		{
			$attributeFilterOption      = $filter->clean($attributeFilterOption, 'INT');
			$attributeFilterOptions[$k] = '';

			if (!empty($attributeFilterOption))
			{
				$attributeFilterOptions[$k] = $attributeFilterOption;
			}
		}

		// To remove null values from array
		$attributeFilterOptions = array_filter($attributeFilterOptions, 'strlen');
		$layout                 = $jinput->get('layout', 'default', 'STRING');

		// Select the required fields from the table.
		$query->select($this->getState('list.select', 'a.*'));
		$query->select('CASE WHEN (bc.discount_price IS NOT NULL) THEN bc.discount_price
						ELSE a.price
						END as fprice');
		$query->from('`#__kart_items` AS a');
		$query->JOIN('LEFT', '`#__categories` AS c ON c.id=a.category');
		$query->JOIN('INNER', '`#__kart_base_currency` AS bc ON bc.item_id=a.item_id');
		$query->join('LEFT', $db->qn('#__contentitem_tag_map', 'tagmap') . ' ON ' . $db->qn('tagmap.content_item_id') . ' = ' . $db->quoteName('a.item_id') . ' AND ' . $db->qn('tagmap.type_alias') . ' = ' . $db->quote('com_quick2cart.product'));
		$query->join('LEFT', $db->qn('#__tags', 't') . ' ON ' . $db->qn('tagmap.tag_id') . ' = ' . $db->quoteName('t.id'));

		// Added now
		if ($layout == 'default')
		{
			$path = JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/globalattribute.php';

			if (!class_exists('Quick2cartModelglobalAttribute'))
			{
				JLoader::register('Quick2cartModelglobalAttribute', $path);
				JLoader::load('Quick2cartModelglobalAttribute');
			}

			$globalAttributeModel = new Quick2cartModelglobalAttribute;
			$filtersArray         = array();

			foreach ($attributeFilterOptions as $attributeFilterOption)
			{
				$attributeId                  = $globalAttributeModel->getOptionsAttributeId($attributeFilterOption);
				$filtersArray[$attributeId][] = $attributeFilterOption;
			}

			$i = 1;

			foreach ($filtersArray as $attribute => $option)
			{
				if (!empty($option))
				{
					$query->JOIN('INNER', '`#__kart_itemattributes` AS ia' . $i . ' ON ia' . $i . '.item_id=a.item_id');
					$query->JOIN('INNER', '`#__kart_itemattributeoptions` AS iao' . $i .
					' ON iao' . $i . '.itemattribute_id=ia' . $i . '.itemattribute_id');
					$query->where('iao' . $i . '.global_option_id IN (' . implode(',', $option) . ')');
					$i++;
				}
			}

			$min_limit = $this->getState('filter.min_limit');
			$max_limit = $this->getState('filter.max_limit');

			if (!empty($min_limit))
			{
				$query->where('a.price >= ' . $min_limit);
			}

			if (!empty($max_limit))
			{
				$query->where('a.price <= ' . $max_limit);
			}
		}

		// Show product only if display_in_product_catlog set to 1
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
				$query->where('( a.name LIKE ' . $search . ' OR a.description LIKE ' . $search . ' OR a.metakey LIKE ' . $search . ')');
			}
		}

		if ($layout == 'default')
		{
			/*$query->where('(a.state = 1)');*/

			// Show only the native products and published category,
			$query->where('(a.state = 1)');
			$query->where(" c.published = 1");
			$query->where(" a.parent = 'com_quick2cart'");
			$storeHelper = new storeHelper;
			$storeIds = $storeHelper->getStoreIds(1);

			if (!empty($storeIds))
			{
				$storeidStr = implode(',', $storeIds);
				$query->where(" a.store_id IN (" . $storeidStr . ')');
			}
			else
			{
				// If all stores are unpublished then dont show
				$query->where(" a.store_id = -1");
			}

			$filter_menu_tag = $this->state->get("filter.menu_tag");

			if (!empty($filter_menu_tag))
			{
				$tagIds = array();
				require_once JPATH_SITE . '/components/com_quick2cart/helper.php';
				$comquick2cartHelper = new comquick2cartHelper;
				$path                = JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/attributes.php';
				$attributesModel     = $comquick2cartHelper->loadqtcClass($path, "Quick2cartModelAttributes");
				$tags                = $attributesModel->getTags();

				if (!empty($tags))
				{
					$tagIds = array_column($tags, 'id');
				}

				if (!empty($tagIds))
				{
					$tagIds = implode(',', $tagIds);
					$query->where("t.id IN (" . $tagIds . ')');
				}

				$query->where($db->qn('tagmap.tag_id') . ' = ' . (int) $filter_menu_tag);
			}

			// When prod_cat is found in URL
			$filter_category = $this->state->get("filter.category");

			if ($filter_category)
			{
				// Show from decedor category
				$catWhere = $this->getWhereCategory($filter_category);

				if ($catWhere)
				{
					foreach ($catWhere as $cw)
					{
						$query->where($cw);
					}
				}
			}
			else
			{
				$filter_menu_category        = $this->state->get("filter.menu_category");
				$filter_menu_category_search = $this->state->get("filter.qtcCategorySearch");
				$filter_menu_store           = $this->state->get("filter.menu_store");

				if ($filter_menu_category)
				{
					$filter_show_subcat_products = $this->state->get("filter.show_subcat_products");

					if ($filter_show_subcat_products)
					{
						$catWhere = $this->getWhereCategory($filter_menu_category);

						if ($catWhere)
						{
							foreach ($catWhere as $cw)
							{
								$query->where($cw);
							}
						}
					}
					else
					{
						$query->where("a.category = '" . $db->escape($filter_menu_category) . "'");
					}
				}

				// If menu with search keyword created
				if (!empty($filter_menu_category_search))
				{
					$filter_menu_category_search = $db->Quote('%' . $db->escape($filter_menu_category_search, true) . '%');
					$query->where('( a.name LIKE ' . $filter_menu_category_search
					. ' OR a.description LIKE ' . $filter_menu_category_search . ' OR a.metakey LIKE ' . $filter_menu_category_search . ')');
				}

				if ($filter_menu_store)
				{
					$query->where($db->quoteName("a.store_id") . "=" . (INT) $db->escape($filter_menu_store));
				}
			}

			$productSorting = $this->getState('sort_products');

			// For sorting products according to price
			if ($productSorting != '')
			{
				if ($productSorting == 'PRICE_DESC')
				{
					$query->order('fprice DESC');
				}
				elseif ($productSorting == 'PRICE_ASC')
				{
					$query->order('fprice ASC');
				}
				elseif ($productSorting == 'FEATURED')
				{
					$query->order('a.featured DESC');
				}
				elseif ($productSorting == 'CREATED_DESC')
				{
					$query->order('a.cdate DESC');
				}
				elseif ($productSorting == 'CREATED_ASC')
				{
					$query->order('a.cdate ASC');
				}
			}
			else
			{
				$query->order($db->qn('a.ordering') . 'ASC');
			}
		}
		else
		{
			$filter_category = $this->state->get("filter.category");

			if ($filter_category)
			{
				$catWhere = $this->getWhereCategory($filter_category);

				if ($catWhere)
				{
					foreach ($catWhere as $cw)
					{
						$query->where($cw);
					}
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
				if ($layout == 'my')
				{
					$query->where('(a.state IN (0, 1))');
				}
				else
				{
					$query->where('(a.state = 1)');
				}
			}

			// My stores view.
			// Filter by store.
			$filter_store = $this->state->get("filter.store");

			// Get all published stores by logged in user
			$comquick2cartHelper = new comquick2cartHelper;

			if ($layout != 'select_product')
			{
				$my_stores = $comquick2cartHelper->getStoreIds($user->id);

				if (count($my_stores))
				{
					$stores = array();

					// Get all store ids
					foreach ($my_stores as $value)
					{
						$stores[] = $value["store_id"];
					}

					// If store filter is selected, check it in my stores array
					if ($filter_store)
					{
						if (in_array($filter_store, $stores))
						{
							$query->where("a.store_id = '" . $db->escape($filter_store) . "'");
						}
					}
					else
					{
						// If selected store filter is not found in my stores array, show products from all stores for logged in user
						$stores = implode(',', $stores);

						if (!empty($stores))
						{
							$query->where(" a.store_id IN (" . $stores . ")");
						}
					}
				}
				else
				{
					// Unauthorized access
					$query->where(" a.store_id=0");
				}
			}
			else
			{
				$store_id = $jinput->get('store_id', '0', 'INT');
				$query->where(" a.store_id=" . $store_id);
			}
		}

		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}
		else
		{
			if ($layout = "my")
			{
				$query->order('a.item_id DESC');
			}
		}

		$query->group('a.item_id');

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
		$items               = parent::getItems();
		$comquick2cartHelper = new comquick2cartHelper;
		$store_details       = $comquick2cartHelper->getAllStoreDetails();

		foreach ($items as $item)
		{
			$item->category_id = $item->category;
			$item->store_name  = (!empty($store_details[$item->store_id])) ? $store_details[$item->store_id]['title'] : '';

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

			$productCurrencyWisePrices = $comquick2cartHelper->getProductAllPrices($item->item_id);
			$item->multi_cur           = $productCurrencyWisePrices['multi_cur'];
			$item->multi_dis_cur       = $productCurrencyWisePrices['multi_dis_cur'];

			$productsModel    = BaseDatabaseModel::getInstance('Products', 'Quick2cartModel', array('ignore_request' => true));
			$item->att_detail = $productsModel->getItemAttributeData($item->item_id);
		}

		if (!empty($items))
		{
			$Quick2cartModelProduct = BaseDatabaseModel::getInstance('Product', 'Quick2cartModel', array('ignore_request' => true));

			foreach ($items as $item)
			{
				$item->form_extra = $Quick2cartModelProduct->getFormExtra(
					array(
						"clientComponent" => 'com_quick2cart',
						"client"          => 'com_quick2cart.product',
						"view"            => 'product',
						"layout"          => 'default',
						"content_id"      => $item->item_id,
						"category"        => $item->category)
					);

				if (!empty($item->form_extra))
				{
					$xmlFileName   = $item->category . "productform_extra" . ".xml";
					$item->formXml = simplexml_load_file(JPATH_SITE . "/components/com_quick2cart/models/forms/" . $xmlFileName);
				}
			}
		}

		return $items;
	}

	/**
	 * Method to set model state variables
	 *
	 * @param   array    $items  The array of record ids.
	 * @param   integer  $state  The value of the property to set or null.
	 *
	 * @return  integer  The number of records updated.
	 *
	 * @since   2.2
	 */
	public function setItemState($items, $state)
	{
		$db                  = Factory::getDbo();
		$app                 = Factory::getApplication();
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$productHelper       = new ProductHelper;
		$user      = Factory::getUser();
		$isAdmin    = $user->authorise('core.admin');


		if ($state === 1)
		{
			$params         = ComponentHelper::getParams('com_quick2cart');
			$admin_approval = (int) $params->get('admin_approval');

			// If admin approval is on for stores
			if ($admin_approval === 1 && !$isAdmin)
			{
				$app->enqueueMessage(Text::_('COM_QUICK2CART_ERR_MSG_ADMIN_APPROVAL_NEEDED_PRODUCTS'), 'error');

				return 0;
			}
		}

		$count = 0;

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$productData = $productHelper->getItemCompleteDetail($id);
				$storeOwner  = $storeHelper->getStoreOwner($productData->store_id);
				$isOwner     = $comquick2cartHelper->checkOwnership($storeOwner);

				if ($isOwner === true)
				{
					$query = $db->getQuery(true);

					// Update the reset flag
					$query->update($db->quoteName('#__kart_items'))->set($db->quoteName('state') . ' = ' . $state)->where($db->quoteName('item_id') . ' = ' . $id);
					$db->setQuery($query);

					try
					{
						$db->execute();
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return 0;
					}

					$count++;
				}
				else
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
				}
			}
		}

		return $count;
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   array  $items  An array of primary key value to delete.
	 *
	 * @return  int  Returns count of success
	 */
	public function delete($items)
	{
		$app           = Factory::getApplication();
		$productHelper = new productHelper;

		$count = 0;

		if (is_array($items))
		{
			foreach ($items as $id)
			{
				$res = $productHelper->deleteWholeProduct($id);
				$productHelper->deleteNotReqProdImages($id, '');

				if (!empty($res))
				{
					$count++;
				}
				else
				{
					$app->enqueueMessage(Text::_('COM_QUICK2CART_MSG_ERROR_DELETE_PRODUCT'), 'error');

					return 0;
				}
			}
		}

		return $count;
	}

	/**
	 * Get sub cateogry.
	 *
	 * @param   integer  $categoryId  category Id.
	 *
	 * @return  array    Store ids.
	 */
	public function getWhereCategory($categoryId)
	{
		$db    = Factory::getDBO();
		$where = array();
		$cat_tbl = Table::getInstance('Category', 'JTable');
		$cat_tbl->load($categoryId);
		$rgt = $cat_tbl->rgt;
		$lft = $cat_tbl->lft;
		$baselevel = (int) $cat_tbl->level;
		$where[] = 'c.lft >= ' . (int) $lft;
		$where[] = 'c.rgt <= ' . (int) $rgt;

		return $where;
	}
}
