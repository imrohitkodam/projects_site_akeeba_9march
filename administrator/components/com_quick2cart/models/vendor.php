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
use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Pagination\Pagination;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\String\StringHelper;

// Added by Sneha
require_once JPATH_SITE . '/components/com_quick2cart/helper.php';
JLoader::import('components.com_tjvendors.helpers.fronthelper', JPATH_SITE);
JLoader::import('components.com_tjvendors.helpers.tjvendors', JPATH_ADMINISTRATOR);
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjvendors/tables');
include_once JPATH_SITE . '/components/com_tjvendors/includes/tjvendors.php';

/**
 * Quick2cart vendor model.
 *
 * @since  2.2
 */
class Quick2cartModelVendor extends BaseDatabaseModel
{
	protected $protected_data;

	protected $protected_total = null;

	protected $protected_pagination = null;

	/**
	 * Constructor that retrieves the ID from the request
	 *
	 */
	public function __construct()
	{
		parent::__construct();
		global $option;
		$app        = Factory::getApplication();
		$jinput     = $app->input;

		// Get the pagination request variables
		$limit            = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->get('list_limit'), 'int');
		$limitstart       = $jinput->get('limitstart', 0, '', 'int');
		$filter_order     = $app->getUserStateFromRequest($option . 'filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
		$this->setState('filter_order', $filter_order);
		$this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	/**
	 * Fucntion to build query
	 *
	 * @return  query
	 */
	public function _buildQuery()
	{
		// Added by Sneha
		$query = "SELECT DISTINCT(a.`id`), u.`username` , a.`owner` , a.`title` , a.`description` , a.`address` , a.`phone`
		, a.`store_email` , a.`store_avatar` , a.`fee` , a.`live` AS published, a.`cdate` , a.`mdate` ,
		a.`extra` , a.`company_name`, a.`vanityurl`
		 FROM #__kart_store AS a
		 LEFT JOIN #__users AS u ON a.owner = u.id";

		$query .= $this->_buildContentWhere();

		return $query;
	}

	/**
	 * Function to delete vendor
	 *
	 * @param   ARRAY  $id  id
	 *
	 * @return  boolean
	 */
	public function deletevendor($id)
	{
		JLoader::import('helpers.storeHelper', JPATH_SITE . '/components/com_quick2cart/');
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		if (!empty($id))
		{
			// Store Ids run through for an ownership check
			foreach ($id as $storeId)
			{
				$storeOwner = $storeHelper->getStoreOwner($storeId);
				$isOwner    = $comquick2cartHelper->checkOwnership($storeOwner);

				// Check if owner is authorized to delete the store
				if (empty($isOwner))
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
				}
			}

			$id    = implode(',', $id);
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);

			$conditions = array(
			$db->quoteName('#__kart_store.id') . ' = ' . $db->quoteName('#__kart_role.store_id'),
			$db->quoteName('#__kart_store.id') . ' IN (' . $db->quote($id) . ' )');
			$query->delete($db->quoteName('#__kart_role'));
			$query->delete($db->quoteName('#__kart_store') . ' USING ' . $db->quoteName('#__kart_store'));
			$query->join('INNER', $db->quoteName('#__kart_role'));
			$query->where($conditions);
			$db->setQuery($query);

			try
			{
				$db->execute();
			}
			catch (\RuntimeException $e)
			{
				$this->setError($e->getMessage());

				return false;
			}
		}
	}

	/**
	 * Function to build content where
	 *
	 * @param   integer  $mystores  store id
	 *
	 * @return  query
	 */
	public function _buildContentWhere($mystores = 0)
	{
		global $option;
		$app    = Factory::getApplication();
		$search = $app->getUserStateFromRequest($option . 'search', 'search', '', 'string');
		$where  = array();

		if (!empty($mystores))
		{
			$user    = Factory::getUser();
			$where[] = "  owner=" . $user->id . " ";
		}

		if (trim($search) != '')
		{
			$where[] = "a.title LIKE '%" . $search . "%' OR u.username LIKE '%" . $search . "%' ";
		}

		return $where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
	}

	/**
	 * Function to get vendors
	 *
	 * @param   integer  $mystores  store id
	 *
	 * @return  array
	 */
	public function getVendors($mystores = 0)
	{
		$db = Factory::getDbo();
		global $option;
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$option    = $jinput->get('option');
		$query     = $this->_buildQuery();

		// Commented by Sneha, To get this result in buildQuery function for csv export
		$filter_order     = $app->getUserStateFromRequest($option . 'filter_order', 'filter_order', '', 'cmd');
		$filter_order_Dir = $app->getUserStateFromRequest($option . 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word');

		if ($filter_order)
		{
			$qry = "SHOW COLUMNS FROM #__kart_store";
			$db->setQuery($qry);
			$exists = $db->loadobjectlist();

			foreach ($exists as $key => $value)
			{
				$allowed_fields[] = $value->Field;
			}

			if (in_array($filter_order, $allowed_fields))
			{
				$query .= " ORDER BY $filter_order $filter_order_Dir";
			}
		}

		$this->protected_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

		return $this->protected_data;
	}

	/**
	 * Function to edit list
	 *
	 * @param   INT  $zoneid  zone id
	 *
	 * @return  null
	 */
	public function Editlist($zoneid)
	{
		unset($this->protected_data);
		$query       = "SELECT * from #__kart_store where id=$zoneid";
		$this->protected_data = $this->_getList($query);

		return $this->protected_data;
	}

	/**
	 * Function to get total
	 *
	 * @return  int
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn’t already exist
		if (empty($this->protected_total))
		{
			$query        = $this->_buildQuery();
			$this->protected_total = $this->_getListCount($query);
		}

		return $this->protected_total;
	}

	/**
	 * Function to get pagination
	 *
	 * @return  null
	 */
	public function getPagination()
	{
		if (empty($this->protected_pagination))
		{
			$this->protected_pagination = new Pagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->protected_pagination;
	}

	/**
	 * This function save the store detail
	 *
	 * @param   Object   $post    post
	 * @param   Object  $userid  user id
	 *
	 * @return  boolean
	 */
	public function store($post, $userid = '')
	{
		$app    = Factory::getApplication();
		$params = ComponentHelper::getParams('com_quick2cart');
		$email  = $post->get('email', '', 'STRING');
		$id     = $post->get('id', 0, 'INT');

		$helperPath = JPATH_SITE . '/components/com_quick2cart/helpers/storeHelper.php';

		if (!class_exists('StoreHelper'))
		{
			JLoader::register('StoreHelper', $helperPath);
			JLoader::load('StoreHelper');
		}

		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$canSetState         = true;

		if (!empty($id))
		{
			$storeOwner  = $storeHelper->getStoreOwner($id);
			$canSetState = $comquick2cartHelper->checkOwnership($storeOwner);
		}

		if ($canSetState)
		{
			$db    = Factory::getDbo();
			$user  = Factory::getUser();
			$owner = (!empty($userid)) ? $userid : $user->id;

			// Checked if the user is a vendor
			$tjvendorsHelper     = new TjvendorsHelper;
			$tjvendorFrontHelper = new TjvendorFrontHelper;
			$vendorId            = $tjvendorFrontHelper->checkVendor($owner, 'com_quick2cart');
			$table               = Table::getInstance('vendor', 'TJVendorsTable', array());
			$table->load(array('user_id' => $owner));

			// Check is user is vendor or not
			if (empty($table->vendor_id) || empty($vendorId))
			{
				if (!$app->isClient('administrator'))
				{
					if ($params->get('silent_vendor', 1, 'INT') == 0)
					{
						return 0;
					}
				}

				// Collecting vendor data
				$vendorData                  = array();
				$vendorData['vendor_client'] = "com_quick2cart";
				$vendorData['user_id']       = $owner;
				$vendorData['vendor_title']  = Factory::getUser($vendorData['user_id'])->name;
				$vendorData['state']         = "1";

				// Collecting payment gateway details
				$paymentDetails                    = array();
				$paymentDetails['payment_gateway'] = '';
				$vendorData['paymentDetails']      = json_encode($paymentDetails);
				$vendorId = $tjvendorsHelper->addVendor($vendorData);
				$post->set('vendor_id', $vendorId);
				$vendorData['vendor_id'] = $vendorId;
			}
			else
			{
				$post->set('vendor_id', $vendorId);
			}

			if (!empty($email))
			{
				$oldData = '';

				// Get old data if exists
				$id = $id;

				if (!empty($id))
				{
					$query = "SELECT `id`, store_avatar, header
					 FROM #__kart_store
					 WHERE `id`=" . $id;
					$db->setQuery($query);

					$oldData = $db->loadAssoc($query);
				}

				$oldAvtarPath  = !empty($oldData) ? $oldData['store_avatar'] : '';
				$oldHeaderPath = !empty($oldData) ? $oldData['header'] : '';

				$row        = new stdClass;
				$row->owner = $user->id;

				if ($userid != '')
				{
					$row->owner = $userid;
				}

				$safeHtmlFilter = InputFilter::getInstance(array(), array(), 1, 1);

				$row->vendor_id      = $post->get('vendor_id', 0, 'INTEGER');
				$row->description    = $safeHtmlFilter->clean($post->get('description', '', 'RAW'), 'string');
				$row->company_name   = $post->get('companyname', '', 'STRING');
				$row->address        = $post->get('address', '', 'STRING');
				$row->phone          = $post->get('phone', '', 'STRING');
				$row->store_email    = $post->get('email', '', 'STRING');
				$row->city           = $post->get('city', '', 'STRING');
				$row->land_mark      = $post->get('land_mark', '', 'STRING');
				$row->country        = $post->get('storecountry', '', 'INTEGER');
				$row->pincode        = $post->get('pincode', '', 'STRING');
				$row->region         = $post->get('qtcstorestate', '', 'INTEGER');
				$row->length_id      = $post->get('qtc_length_class', NULL, 'INTEGER');
				$row->weight_id      = $post->get('qtc_weight_class', NULL, 'INTEGER');
				$row->taxprofile_id  = $post->get('taxprofile_id', NULL, 'INTEGER');
				$row->shipprofile_id = $post->get('qtc_shipProfile', NULL, 'INTEGER');

				// Added by vbmundhe Dont remove as it is require on install script
				$helper_path = JPATH_SITE . '/components/com_quick2cart/helper.php';

				if (!class_exists('comquick2cartHelper'))
				{
					JLoader::register('comquick2cartHelper', $helper_path);
					JLoader::load('comquick2cartHelper');
				}

				$comquick2cartHelper = new comquick2cartHelper;

				// STORE LOGO IMGE
				$avatar = $post->get('avatar', '', 'STRING');

				if (!empty($avatar))
				{
					$avtar_path = $avatar;
				}
				else
				{
					$img_dimensions   = array();
					$img_dimensions[] = 'storeavatar';

					//  upload avtar

					//  name of file field
					$file_field = "avatar";
					$avtar_path = $comquick2cartHelper->imageupload($file_field, $img_dimensions, 0);
				}

				if (!empty($avtar_path))
				{
					// AVOID  IMAGE OVERWRITE TO NULL WHILE UPDATE
					$row->store_avatar = $avtar_path;
				}

				$header_path = '';
				$row->header = $header_path;
				$extra       = $post->get('extra', '', 'STRING');

				if (!empty($extra))
				{
					// While update , AVOID DONT MAKE EMPTY
					$row->extra = $extra;
				}

				$quick2cartModelVendor = new quick2cartModelVendor;

				$id             = "";
				$title          = $post->get('title', '', 'STRING');
				$storeVanityUrl = $post->get('storeVanityUrl', '', 'STRING');
				$id             = $post->get('id', '', 'INTEGER');

				// If already present then update
				if (!empty($oldData))
				{
					$row->title     = $title;
					$row->vanityurl = $quick2cartModelVendor->formatttedVanityURL($storeVanityUrl, $title, $id);
					$row->id        = $id = $oldData['id'];
					$row->mdate     = date("Y-m-d");

					try
					{
						$db->updateObject('#__kart_store', $row, 'id');
					}
					catch (RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return 0;
					}

					$mail_on_store_edit = (int) $params->get('mail_on_store_edit', 'INT');

					if ($mail_on_store_edit === 1)
					{
						// Send store edited email to admin
						$this->SendMailAdminOnStoreEdit($row);
					}

					$role = 1;
					$quick2cartModelVendor->addRoleEntry($id, $role, $row->owner, $row->vendor_id);

					return $id;
				}
				else
				{
					// Insert
					$row->title           = $title;
					$row->vanityurl       = $quick2cartModelVendor->formatttedVanityURL($storeVanityUrl, $title);
					$row->cdate           = date("Y-m-d");
					$row->mdate           = date("Y-m-d");
					$admin_approval       = (int) $params->get('admin_approval_stores');
					$mail_on_store_create = (int) $params->get('mail_on_store_create');

					if ($admin_approval == 1)
					{
						$row->live = 0;
					}

					try
					{
						$db->insertObject('#__kart_store', $row, 'id');
					}
					catch (\RuntimeException $e)
					{
						$this->setError($e->getMessage());

						return false;
					}

					$id = $db->insertid();
					$quick2cartModelVendor->addRoleEntry($row->id, $role = 1, $row->owner, $row->vendor_id);

					if ($mail_on_store_create === 1)
					{
						// Send Approval mail to admin
						$this->SendMailAdminOnCreateStore($row);
						$this->SendMailOwnerOnCreateStore($row);
					}

					$socialintegration  = $params->get('integrate_with', 'none');
					$streamOnCeateStore = $params->get('streamCeateStore', 1);

					// If (!$app->isAdmin() && $streamOnCeateStore && $socialintegration != 'none')
					if ($socialintegration != 'none')
					{
						$user     = Factory::getUser();
						$libclass = $comquick2cartHelper->getQtcSocialLibObj();

						// Add in activity.
						if ($streamOnCeateStore)
						{
							$action    = 'addstore';
							$link = 'index.php?option=com_quick2cart&view=vendor&layout=store&store_id=' . $id;
							$itemId    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category&layout=default');
							$slink = Uri::root() . substr(Route::_($link . '&Itemid=' . $itemId), strlen(Uri::base(true)) + 1);
							$storeLink = '<a class="" href="' . $slink . '">' . $title . '</a>';

							$originalMsg = Text::sprintf('QTC_ACTIVITY_ADD_STORE', $storeLink);
							$libclass->pushActivity($user->id, $act_type = '', $act_subtype = '', $originalMsg, $act_link = '', $title = '', $act_access = 0);
						}

						// Add points
						$point_system         = $params->get('point_system');
						$options['extension'] = 'com_quick2cart';

						if ($socialintegration == "EasySocial")
						{
							$options['command'] = 'create_store';
							$libclass->addpoints($user, $options);
						}
						elseif ($socialintegration == "JomSocial")
						{
							$options['command'] = 'CreateStore.points';
							$libclass->addpoints($user, $options);
						}
					}
				}

				return $id;
			}
			else
			{
				return 0;
			}
		}
	}

	/**
	 * Function to get code
	 *
	 * @param   STRING  $code  code
	 *
	 * @return  boolean
	 */
	public function getcode($code)
	{
		$db  = Factory::getDbo();
		$qry = "SELECT `id` FROM #__kart_coupon WHERE `code` = " . $db->quote($db->escape(trim($code)));
		$db->setQuery($qry);
		$exists = $db->loadResult();

		if ($exists)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to get selected code
	 *
	 * @param   STRING  $code  code
	 * @param   INT     $id    id
	 *
	 * @return  boolean
	 */
	public function getselectcode($code, $id)
	{
		$db  = Factory::getDbo();
		$qry = "SELECT `code` FROM #__kart_coupon WHERE id<>'{$id}' AND `code` = " . $db->quote($db->escape(trim($code)));
		$db->setQuery($qry);
		$exists = $db->loadResult();

		if ($exists)
		{
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to add roll entry
	 *
	 * @param   INT     $storeid   Store id
	 * @param   STRING  $role      role
	 * @param   INT     $userid    user id
	 * @param   INT     $vendorId  vendor id
	 *
	 * @return boolean
	 */
	public function addRoleEntry($storeid, $role, $userid, $vendorId)
	{
		// Get role table id having store id = $storeid
		$action = "insertObject";
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('id');
		$query->from('#__kart_role');
		$query->where('store_id=' . $storeid);
		$db->setQuery($query);
		$entry = $db->loadResult();

		$row            = new stdClass;
		$row->store_id  = $storeid;
		$row->role      = $role;
		$row->user_id   = $userid;
		$row->vendor_id = $vendorId;

		if ($entry)
		{
			$action  = "updateObject";
			$row->id = $entry;
		}

		if (!$db->$action('#__kart_role', $row, 'id'))
		{
			echo $db->stderr();

			return 0;
		}
	}

	/**
	 * This function provide data  which is require for line graph
	 *
	 * @param   INT     $storeid   Store id
	 * @param   string  $backdate  date
	 * @param   string  $currdate  date
	 *
	 * @return  boolean
	 */
	public function getPeriodicIncomeGrapthData($storeid, $backdate = '', $currdate = '')
	{
		$app      = Factory::getApplication();
		$backdate = $app->getUserStateFromRequest('from', 'from', '', 'string');
		$currdate = $app->getUserStateFromRequest('to', 'to', '', 'string');

		// Get date for 30 days before, in Y-m-d H:i:s format
		$thirtyDaysBefore = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 30 days'));
		$backdate         = !empty($backdate) ? $backdate : Factory::getDate($thirtyDaysBefore)->Format(Text::_('Y-m-d H:i:s'));

		// Get current date, in Y-m-d H:i:s format
		$currdate         = !empty($currdate) ? $currdate : Factory::getDate(date('Y-m-d H:i:s'))->Format(Text::_('Y-m-d H:i:s'));

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();
		$db                  = Factory::getDbo();
		$query               = 'SELECT SUM( i.product_final_price)  AS amount, DATE(i.cdate) as cdate, COUNT(o.id) AS orders_count
				FROM `#__kart_order_item` AS i
				LEFT JOIN #__kart_orders AS o ON i.`order_id` = o.id
				WHERE i.store_id=' . $storeid . ' AND DATE(i.cdate) >\'' . $backdate . '\' and DATE(i.cdate) <=\'' . $currdate . '\'
				AND (i.status=\'C\' OR i.status=\'S\')
				AND o.currency=\'' . $currency . '\'
				GROUP BY DAY( i.cdate )
				ORDER BY i.cdate';
		$db->setQuery($query);

		return $db->loadObjectList("cdate");
	}

	/**
	 * Function to get periodic income
	 *
	 * @param   INT     $storeid   Store id
	 * @param   string  $backdate  date
	 * @param   string  $currdate  date
	 *
	 * @return  array
	 */
	public function getPeriodicIncome($storeid, $backdate = '', $currdate = '')
	{
		$app      = Factory::getApplication();
		$backdate = $app->getUserStateFromRequest('from', 'from', '', 'string');
		$currdate = $app->getUserStateFromRequest('to', 'to', '', 'string');

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		// Get date for 30 days before, in Y-m-d H:i:s format
		$thirtyDaysBefore = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 30 days'));
		$backdate         = !empty($backdate) ? $backdate : Factory::getDate($thirtyDaysBefore)->Format(Text::_('Y-m-d H:i:s'));

		// Get current date, in Y-m-d H:i:s format
		$currdate         = !empty($currdate) ? $currdate : Factory::getDate(date('Y-m-d H:i:s'))->Format(Text::_('Y-m-d H:i:s'));

		$db    = Factory::getDbo();
		$query = 'SELECT SUM(i.product_final_price) AS amount, SUM(i.product_quantity) AS qty, COUNT( DISTINCT (i.order_id)) AS totorders
		 FROM `#__kart_order_item` AS i
		 LEFT JOIN #__kart_orders AS o ON i.`order_id`=o.id
		 WHERE i.store_id=' . $storeid . '
		 AND DATE(i.cdate) >"' . $backdate . '"
		 AND DATE(i.cdate) <="' . $currdate . '"
		 AND (i.status="C" OR i.status="S")
		 AND o.currency="' . $currency . '"';
		$db->setQuery($query);

		return $db->loadAssoc();
	}

	/**
	 * Function to get total sales
	 *
	 * @param   INT  $storeid  Store id
	 *
	 * @return  INT
	 */
	public function getTotalSales($storeid)
	{
		$db                  = Factory::getDbo();

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();
		$query               = 'SELECT SUM(i.product_final_price)
				FROM `#__kart_order_item` AS i
				LEFT JOIN #__kart_orders AS o ON i.`order_id` = o.id
				WHERE i.store_id=' . $storeid . ' AND (i.status=\'C\' OR i.status=\'S\') AND o.currency=\'' . $currency . '\'';
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to get last five orders
	 *
	 * @param   INT  $storeid  Store id
	 *
	 * @return  Array
	 */
	public function getLast5orders($storeid)
	{
		$db    = Factory::getDbo();
		$query = 'SELECT o.name, i.`status`, SUM( i.`product_final_price` ) AS price, o.id, o.`currency`, o.cdate, o.prefix
		 FROM  `#__kart_order_item` AS i
		 LEFT JOIN #__kart_orders AS o ON i.`order_id` = o.id
		 WHERE store_id=' . $storeid . '
		 GROUP BY o.id ORDER BY o.id DESC
		 LIMIT 0,5';
		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * Function to store product count
	 *
	 * @param   INT  $storeid  store id
	 *
	 * @return  boolean
	 */
	public function storeProductCount($storeid)
	{
		$db    = Factory::getDbo();
		$query = 'SELECT count(*) FROM  `#__kart_items` where store_id=' . $storeid;
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * This fuction will send customer email to store owner
	 *
	 * @param   Object  $post  post data
	 *
	 * @return  boolean
	 */
	public function sendcontactUsEmail($post)
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$app                 = Factory::getApplication();
		$comquick2cartHelper = new comquick2cartHelper;

		// GETTING CONFIGURATION PROPERTIES
		$from       = $app->get('mailfrom');
		$fromname   = $app->get('fromname');
		$sitename   = $app->get('sitename');
		$store_id   = $post->get("store_id", '', "INTEGER");
		$item_id    = $post->get("item_id", '', "INTEGER");
		$cust_email = $post->get("cust_email", '', "STRING");
		$contact_no = $post->get("contact_no", '', "STRING");
		$store_info = $comquick2cartHelper->getSoreInfo($store_id);
		$recipient  = $store_info['store_email'];

		// Get enquiry
		$enquiry = $post->get("message", '', "RAW");

		// Get prod detail
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name')->from('#__kart_items AS i')->where("i.item_id= " . $item_id);
		$db->setQuery($query);
		$itemresult = $db->loadAssoc();

		// Get Product link
		$prodLink = '<a class="" href="' . Uri::root() . $comquick2cartHelper->getProductLink($item_id) . '">'
		. $itemresult['name'] . '</a>';

		$body = Text::sprintf('COM_QUICK2CART_CONTACT_US_BODY', $cust_email, $contact_no, $prodLink, $enquiry);
		$body = str_replace('{sitename}', $sitename, $body);

		// $bcc = array('0'=>$app->get('mailfrom') );

		$cc  = null;
		$bcc = array();
		$attachment  = null;
		$replyto     = null;
		$replytoname = null;
		$subject     = Text::_('COM_QUICK2CART_CONTACT_US_SUBJECT');

		if ($app->get('mailonline') == true)
		{
			Factory::getMailer()->sendMail(
			$from, $fromname, $recipient, $subject, $body, $mode = 1, $cc, $bcc, $attachment, $cust_email, $replytoname
			);
		}
	}

	/**
	 * FUnction to get total orders count
	 *
	 * @param   INT  $storeid  store id
	 *
	 * @return  INT
	 */
	public function getTotalOrdersCount($storeid)
	{
		$db                  = Factory::getDbo();

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		$query = "SELECT COUNT( DISTINCT (o.`id`) )
							FROM  `#__kart_order_item` AS i, #__kart_orders AS o
							WHERE i.store_id =" . $storeid . "
							AND i.`order_id` = o.id
							AND (o.status =  'C' OR o.status =  'S' )
							AND o.currency =  '" . $currency . "'
							AND i.`order_id` = o.id ";
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * Function to check vanity url
	 *
	 * @param   STRING  $vanity       vanity url
	 * @param   string  $oldstore_id  old store id
	 *
	 * @return  boolean
	 */
	public function ckUniqueVanityURL($vanity, $oldstore_id = '')
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName('id'));
		$query->from($db->quoteName('#__kart_store'));
		$query->where($db->quoteName('vanityurl') . '=' . $db->quote($vanity));

		if (!empty($oldstore_id))
		{
			$query->where($db->quoteName('id') . '!=' . $oldstore_id);
		}

		$db->setQuery($query);
		$id = $db->loadResult();

		if (!empty($id))
		{
			// Present vanity URL
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * Function to get formmated vanity URL
	 *
	 * @param   STRING  $vanityurl    vanity URL
	 * @param   STRING  $storeTitle   store title
	 * @param   INT     $oldstore_id  old store id
	 *
	 * @return  STRING
	 */
	public function formatttedVanityURL($vanityurl, $storeTitle, $oldstore_id = '')
	{
		$quick2cartModelVendor = new quick2cartModelVendor;
		$user                  = Factory::getUser();
		$storeTitle            = trim($storeTitle);

		if (trim($vanityurl) == '')
		{
			$vanityurl = $storeTitle;
		}

		$final_vanity = $vanityurl;

		// Remove all space, tab, new line

		/*$final_vanity=preg_replace("/\s+/", "", $final_vanity);
		$final_vanity = preg_replace("/[^A-Za-z0-9\-]+$/", "", $final_vanity );*/

		$final_vanity = ApplicationHelper::stringURLSafe($final_vanity);

		if (trim(str_replace('-', '', $final_vanity)) == '')
		{
			$final_vanity = $user->id . '-' . Factory::getDate()->format('Y-m-d-H-i-s');
		}

		$i = 1;

		do
		{
			if ($i == 1)
			{
				$status = $quick2cartModelVendor->ckUniqueVanityURL($final_vanity, $oldstore_id);
			}
			else
			{
				// Remove userid: from vanity url if exist AS WE R GOING TO APPEND NEXT
				$final_vanity = StringHelper::increment($final_vanity, 'dash');

				$status = $quick2cartModelVendor->ckUniqueVanityURL($final_vanity);
			}

			$i++;
		}
		while ($status != 0);

		return $final_vanity;
	}

	/**
	 * Function to check if store title is qnique or not
	 *
	 * @param   STRING  $title        title
	 * @param   string  $oldstore_id  old store id
	 *
	 * @return  boolean
	 */
	public function ckUniqueStoretitle($title, $oldstore_id = '')
	{
		$db      = Factory::getDbo();
		$where   = array();
		$title   = $db->quote($db->escape(trim($title), true));
		$where[] = '`title`="' . $title . '"';

		if (!empty($oldstore_id))
		{
			$where[] = ' `id`!=\'' . $oldstore_id . '\' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$query = 'SELECT `id` FROM `#__kart_store` ' . $where;
		$db->setQuery($query);
		$id = $db->loadResult();

		if (!empty($id))
		{
			// Present title URL
			return 1;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * This function gives formatted vanity url
	 *
	 * @param   STRING  $title        title
	 * @param   STRING  $oldstore_id  old store id
	 *
	 * @return  boolean
	 */
	public function formatttedTitle($title, $oldstore_id = '')
	{
		$db                    = Factory::getDbo();
		$quick2cartModelVendor = new quick2cartModelVendor;
		$user                  = Factory::getUser();
		$i                     = 1;
		$final_title           = $title;

		do
		{
			if ($i == 1)
			{
				$status = $quick2cartModelVendor->ckUniqueStoretitle($title, $oldstore_id);
			}
			else
			{
				$final_title = $title . $i;
				$status      = $quick2cartModelVendor->ckUniqueStoretitle($final_title);
			}

			// Generate new vanity url
			$i++;
		}
		while ($status != 0);

		return $db->escape(trim($final_title), true);
	}

	/**
	 * Function added by Sneha Send email on editing store
	 *
	 * @param   Object  $post  post id
	 *
	 * @return  null
	 */
	public function SendMailAdminOnStoreEdit($post)
	{
		$app = Factory::getApplication();
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
		$db     = Factory::getDbo();
		$params = ComponentHelper::getParams('com_quick2cart');
		$sendto = $params->get('sale_mail');
		$adminMail = $app->get('mailfrom');

		$query = "SELECT store_avatar
		 FROM `#__kart_store`
		 WHERE id=" . $post->id;
		$db->setQuery($query);
		$image = $db->loadColumn();

		$path    = Uri::root() . 'images/quick2cart/' . $image[0];
		$subject = Text::_('COM_QUICK2CART_STORE_EDIT_SUBJECT');
		$subject = str_replace('{storename}', $post->title, $subject);

		$body = Text::_('COM_QUICK2CART_STORE_EDIT_BODY');
		$body = str_replace('{storename}', $post->title, $body);
		$body = str_replace('{companyname}', $post->company_name, $body);
		$body = str_replace('{address}', $post->address, $body);
		$body = str_replace('{phone}', $post->phone, $body);
		$body = str_replace('{email}', $post->store_email, $body);
		$body = str_replace('{img}', $path, $body);
		$comquick2cartHelper = new comquick2cartHelper;
		$comquick2cartHelper->sendmail($adminMail, $subject, $body);
	}

	/**
	 * Function added by Sneha Send email to admin on store creation
	 *
	 * @param   Object  $store  store id
	 *
	 * @return  null
	 */
	public function SendMailAdminOnCreateStore($store)
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$app                   = Factory::getApplication();
		$sitename              = $app->get('sitename');
		$params                = ComponentHelper::getParams('com_quick2cart');
		$adminMail             = $app->get('mailfrom');
		$admin_approval_stores = $params->get("admin_approval_stores", 0);
		$subject               = Text::_('COM_QUICK2CART_STORE_ADMIN_NORMAL_EMAIL_SUBJECT');
		$body                  = Text::_('COM_QUICK2CART_STORE_ADMIN_NORMAL_EMAIL_SUBJECT_BODY');
		$allStoreLink          = 'administrator/index.php?option=com_quick2cart&view=stores';

		if ($admin_approval_stores == 1)
		{
			$subject      = Text::_('COM_QUICK2CART_STORE_APPROVAL_SUBJECT');
			$body         = Text::_('COM_QUICK2CART_STORE_APPROVAL_BODY');
			$allStoreLink = 'administrator/index.php?option=com_quick2cart&view=stores&filter_published=0';
		}

		$subject             = str_replace('{sitename}', $sitename, $subject);
		$body                = str_replace('{title}', $store->title, $body);
		$body                = str_replace('{description}', $store->description, $body);
		$body                = str_replace('{link}', Uri::root() . $allStoreLink, $body);
		$body                = str_replace('{sitename}', $sitename, $body);
		$comquick2cartHelper = new comquick2cartHelper;
		$comquick2cartHelper->sendmail($adminMail, $subject, $body);
	}

	/**
	 * Function addded by Sneha to Send email to owner on store creation
	 *
	 * @param   Object  $store  Store id
	 *
	 * @return null
	 */
	public function SendMailOwnerOnCreateStore($store)
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
		$app                   = Factory::getApplication();
		$sitename              = $app->get('sitename');
		$adminMail             = $app->get('mailfrom');
		$params                = ComponentHelper::getParams('com_quick2cart');
		$admin_approval_stores = $params->get("admin_approval_stores", 0);
		$subject               = Text::_('COM_QUICK2CART_STORE_OWNER_NORMAL_MAIL_SUB');
		$body                  = Text::_('COM_QUICK2CART_STORE_OWNER_NORMAL_MAIL_BODY');

		if ($admin_approval_stores == 1)
		{
			$subject = Text::_('COM_QUICK2CART_STORE_APPROVAL_OWNER_SUBJECT');
			$body    = Text::_('COM_QUICK2CART_STORE_APPROVAL_OWNER_BODY');
		}

		$subject = str_replace('{store_name}', $store->item_name, $subject);
		$subject = str_replace('{sitename}', $sitename, $subject);
		$body    = str_replace('{title}', $store->title, $body);
		$body    = str_replace('{description}', $store->description, $body);
		$body    = str_replace('{sitename}', $sitename, $body);

		$comquick2cartHelper = new comquick2cartHelper;
		$comquick2cartHelper->sendmail($adminMail, $subject, $body);
	}

	/**
	 * Function to get result in csv
	 *
	 * @return  [type]  [description]
	 */
	public function getCsvexportData()
	{
		$query = $this->_buildQuery();
		$db    = Factory::getDbo();
		$query = $db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * [getStoreCustomersCount description]
	 *
	 * @param   [type]  $storeId  [description]
	 *
	 * @return  [type]            [description]
	 */
	public function getStoreCustomersCount($storeId)
	{
		$userCount = 0;

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('DISTINCT order_id');
		$query->from('#__kart_order_item AS a');
		$query->where('store_id=' . $storeId);
		$db->setQuery($query);
		$orderIds = $db->loadColumn();

		if ($orderIds)
		{
			$orderIds = implode(',', $orderIds);
			$query    = $db->getQuery(true);
			$query->select('DISTINCT o.email');
			$query->from('#__kart_orders AS o');
			$query->join('LEFT', '#__kart_users AS u ON u.user_id= o.user_info_id');
			$query->where("u.address_type='BT'");
			$query->where("o.id=u.order_id");
			$query->where("u.order_id IN (" . $orderIds . ")");
			$db->setQuery($query);
			$userIds   = $db->loadColumn();
			$userCount = count($userIds);
		}

		return $userCount;
	}
}
