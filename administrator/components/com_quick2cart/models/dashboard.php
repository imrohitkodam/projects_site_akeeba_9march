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
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;

/**
 * Dashboard Model for an Q2C.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelDashboard extends BaseDatabaseModel
{
		protected $downloadid;

		protected $extensionsDetails;

	/**
	 * construtor function
	 *
	 */
	public function __construct()
	{
		$this->db = Factory::getDBO();

		// Get download id
		$params     = ComponentHelper::getParams('com_quick2cart');
		$this->downloadid = $params->get('downloadid');

		// Setup vars
		$this->extensionsDetails = new stdClass;
		$this->extensionsDetails->extension        = 'com_quick2cart';
		$this->extensionsDetails->extensionElement = 'pkg_quick2cart';
		$this->extensionsDetails->extensionType    = 'package';
		$this->extensionsDetails->updateStreamName = 'Quick2cart';
		$this->extensionsDetails->updateStreamType = 'extension';
		$this->extensionsDetails->updateStreamUrl  = 'https://techjoomla.com/updates/stream/quick2cart.xml?format=xml';
		$this->extensionsDetails->downloadidParam  = 'downloadid';

		parent::__construct();
		global $option;
	}

	/**
	 * Refreshes the Joomla! update sites for this extension as needed
	 *
	 * @return  void
	 */
	public function refreshUpdateSite()
	{
		// Trigger plugin
		PluginHelper::importPlugin('system', 'tjupdates');
		Factory::getApplication()->triggerEvent('refreshUpdateSite', array($this->extensionsDetails));
	}

	/**
	 * Function to get latest version of Quick2Cart
	 *
	 * @return  void
	 */
	public function getLatestVersion()
	{
		// Trigger plugin
		PluginHelper::importPlugin('system', 'tjupdates');
		$latestVersion = Factory::getApplication()->triggerEvent('getLatestVersion', array($this->extensionsDetails));

		return (isset($latestVersion[0]) ? $latestVersion[0] : false);
	}

	/**
	 * Method to get title of dashboard
	 *
	 * @param   string  $title    Title of box
	 * @param   string  $content  Content of box
	 * @param   object  $type     type of data
	 *
	 * @return  html  $html  title of dashboard
	 *
	 * @since   2.2
	 */
	public function getbox($title, $content, $type = null)
	{
		$html = '
			<div class="row-fluid">
				<div class="span12"><h5>' . $title . '</h5></div>
			</div>
			<div class="row-fluid">
				<div class="span12">' . $content . '</div>
			</div>';

		return $html;
	}

	/**
	 * Returns overall total income amount
	 *
	 * @return  float  get overall income
	 *
	 * @since   2.2
	 */
	public function getAllOrderIncome()
	{
		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();
		$query = "SELECT FORMAT(SUM(amount), 2)
		 FROM #__kart_orders
		 WHERE (status='C' OR status='S') AND currency='" . $currency . "'
		 AND (processor NOT IN('jomsocialpoints', 'alphapoints') OR extra='points')";

		$this->_db->setQuery($query);
		$result = $this->_db->loadResult();

		return $result;
	}

	/**
	 * Returns overall total income per month
	 *
	 * @return  float  get total income per month
	 *
	 * @since   2.2
	 */
	public function getMonthIncome()
	{
		$db = Factory::getDBO();

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		// $backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' - 30 days'));

		$curdate    = date('Y-m-d');
		$back_year  = date('Y') - 1;
		$back_month = date('m') + 1;
		$backdate   = $back_year . '-' . $back_month . '-' . '01';

		/* Query echo $query = "SELECT FORMAT(SUM(amount),2) FROM #__kart_orders
		WHERE status ='C' AND cdate between (".$curdate.",".$backdate." )
		GROUP BY YEAR(cdate), MONTH(cdate) order by YEAR(cdate), MONTH(cdate)
		*/

		$query = "SELECT FORMAT( SUM( amount ) , 2 ) AS amount, MONTH( cdate ) AS MONTHSNAME, YEAR( cdate ) AS YEARNM
		FROM `#__kart_orders`
		WHERE DATE(cdate)
		BETWEEN  '" . $backdate . "'
		AND  '" . $curdate . "'
		AND ( STATUS =  'C' OR STATUS =  'S') AND currency='" . $currency . "'
		GROUP BY YEARNM, MONTHSNAME
		ORDER BY YEAR( cdate ) , MONTH( cdate ) ASC";

		// @TODO WE HAVE TO CHECK WHETHER WE HAVE TO INCLUDE OR NOT

		// AND (processor NOT IN ('payment_jomsocialpoints',  'payment_alphapoints'))
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * Returns overall total income per month
	 *
	 * @return  float  get total income per month
	 *
	 * @since   2.2
	 */
	public function getAllmonths()
	{
		$date2      = date('Y-m-d');

		// Get one year back date
		$date1 = date('Y-m-d', strtotime(date("Y-m-d", time()) . " - 365 day"));

		// Convert dates to UNIX timestamp
		$time1 = strtotime($date1);
		$time2 = strtotime($date2);
		$tmp   = date('mY', $time2);
		$year  = date('Y', $time1);

		// $months[] = array("month" => date('F', $time1), "year" => date('Y', $time1));

		while ($time1 < $time2)
		{
			$month31 = array(1,3,5,7,8,10,12);
			$month30 = array(4,6,9,11);

			$month = date('m', $time1);

			if (array_search($month, $month31))
			{
				$time1 = strtotime(date('Y-m-d', $time1) . ' +31 days');
			}
			elseif (array_search($month, $month30))
			{
				$time1 = strtotime(date('Y-m-d', $time1) . ' +30 days');
			}
			else
			{
				if (((0 == $year % 4) && (0 != $year % 100)) || (0 == $year % 400))
				{
					$time1 = strtotime(date('Y-m-d', $time1) . ' +29 days');
				}
				else
				{
					$time1 = strtotime(date('Y-m-d', $time1) . ' +28 days');
				}
			}

			if (date('mY', $time1) != $tmp && ($time1 < $time2))
			{
				$months[] = array(
					"month" => date('F', $time1),
					"year" => date('Y', $time1)
				);
			}
		}

		$months[] = array("month" => date('F', $time2),"year" => date('Y', $time2));

		return $months;
	}

	/**
	 * Function for pie chart
	 *
	 * @return  array  Get data for pie chart
	 *
	 * @since   2.2
	 */
	public function statsforpie()
	{
		$db                  = Factory::getDBO();
		$session             = Factory::getSession();

		// Getting current currency
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		$qtc_graph_from_date = $session->get('qtc_graph_from_date');
		$socialads_end_date  = $session->get('socialads_end_date');

		$where   = "AND currency='" . $currency . "'";
		$groupby = '';

		if ($qtc_graph_from_date)
		{
			// For graph
			$where .= " AND DATE(mdate) BETWEEN DATE('" . $qtc_graph_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}
		else
		{
			$day         = date('d');
			$month       = date('m');
			$year        = date('Y');
			$statsforpie = array();

			$backdate = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s') . ' - 30 days'));
			$groupby  = "";
		}

		// Pending order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'P'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		// Confirmed order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'C'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		// Rejected order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'RF'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		// Shipped order
		$query = " SELECT COUNT(id) AS orders FROM #__kart_orders WHERE status= 'S'
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$statsforpie[] = $db->loadObjectList();

		return $statsforpie;
	}

	/**
	 * Returns periodic income based on session data
	 *
	 * @return  INT  periodic income based on session data
	 *
	 * @since   2.2
	 */
	public function getperiodicorderscount()
	{
		$db      = Factory::getDBO();
		$session = Factory::getSession();

		$qtc_graph_from_date = $session->get('qtc_graph_from_date');
		$socialads_end_date  = $session->get('socialads_end_date');
		$where               = '';

		if ($qtc_graph_from_date)
		{
			$where = " AND DATE(mdate) BETWEEN DATE('" . $qtc_graph_from_date . "') AND DATE('" . $socialads_end_date . "')";
		}
		else
		{
			$qtc_graph_from_date = date('Y-m-d');
			$backdate            = date('Y-m-d', strtotime(date('Y-m-d') . ' - 30 days'));
			$where               = " AND DATE(mdate) BETWEEN DATE('" . $backdate . "') AND DATE('" . $qtc_graph_from_date . "')";
		}

		$query = "SELECT FORMAT(SUM(amount),2) FROM #__kart_orders WHERE (status ='C' OR status ='S')
		AND (processor NOT IN('payment_jomsocialpoints','payment_alphapoints') OR extra='points') " . $where;
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

	/**
	 * Returns periodic income based on session data
	 *
	 * @return  INT  periodic income based on session data
	 *
	 * @since   2.2
	 */
	public function notShippedDetails()
	{
		$where   = array();
		$where[] = ' o.`status`="C" ';
		$where   = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');

		$db    = Factory::getDBO();
		$query = 'SELECT o.id,o.prefix,o.`name`,amount FROM `#__kart_orders` AS o ' . $where . ' ORDER BY o.`mdate` LIMIT 0,7';
		$db->setQuery($query);
		$result       = $db->loadAssocList();

		return $result;
	}

	/**
	 * Returns periodic income based on session data
	 *
	 * @return  INT  periodic income based on session data
	 *
	 * @since   2.2
	 */
	public function getpendingPayouts()
	{
		if (!class_exists('Quick2cartModelPayouts'))
		{
			JLoader::register('Quick2cartModelPayouts', JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/payouts.php');
			JLoader::load('Quick2cartModelPayouts');
		}

		$Quick2cartModelPayouts = new Quick2cartModelPayouts;

		return $Quick2cartModelPayouts->getPayoutFormData();
	}

	/**
	 * Returns orders count
	 *
	 * @return  INT  $ordersCount  orders count
	 *
	 * @since   2.2
	 */
	public function getOrdersCount()
	{
		$db    = Factory::getDBO();
		$query = "SELECT COUNT(id) FROM #__kart_orders";
		$db->setQuery($query);
		$ordersCount = $db->loadResult();

		return $ordersCount;
	}

	/**
	 * Returns products count
	 *
	 * @return  INT  products count
	 *
	 * @since   2.2
	 */
	public function getProductsCount()
	{
		$db    = Factory::getDBO();
		$query = $db->getQuery(true);

		$query->select('COUNT(item_id)')
		->from($db->quoteName('#__kart_items'))
		->where($db->quoteName('display_in_product_catlog') . ' = 1');

		$db->setQuery($query);
		$productsCount = $db->loadResult();

		return $productsCount;
	}

	/**
	 * Returns stores count
	 *
	 * @return  INT  stores count
	 *
	 * @since   2.2
	 */
	public function getStoresCount()
	{
		$db    = Factory::getDBO();
		$query = "SELECT COUNT(id) FROM #__kart_store";
		$db->setQuery($query);
		$storesCount = $db->loadResult();

		return $storesCount;
	}
}
