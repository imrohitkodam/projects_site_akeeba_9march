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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/zone.php';
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjprivacy/tables');

JLoader::import('fronthelper', JPATH_SITE . '/components/com_tjvendors/helpers');
JLoader::import('vendorclientxref', JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
include_once JPATH_SITE . '/components/com_tjvendors/includes/tjvendors.php';

/**
 * View class for vendor.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewVendor extends HtmlView
{
	protected $params;

	protected $directPaymentConfig;

	protected $adminCall;

	protected $storeinfo;

	protected $countrys;

	protected $orders_site;

	protected $allowToCreateStore;

	protected $store_authorize;

	protected $editview;

	protected $legthList;

	protected $weigthList;

	protected $OnBeforeCreateStore;

	protected $storeDetailInfo;

	protected $catpage_Itemid;

	protected $orders_itemid;

	protected $store_customers_itemid;

	protected $store_role_list;

	protected $store_id;

	protected $prodcountprodCount;

	protected $getPeriodicIncomeGrapthData;

	protected $getPeriodicIncome;

	protected $totalSales;

	protected $totalOrdersCount;

	protected $last5orders;

	protected $storeCustomersCount;

	protected $topSellerProducts;

	protected $change_prod_cat;

	protected $cats;

	protected $allStoreProd;

	protected $pagination;

	protected $item_id;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void|boolean result.
	 */
	public function display($tpl = null)
	{
		$Quick2cartModelZone        = new Quick2cartModelZone;
		$this->params               = ComponentHelper::getParams('com_quick2cart');
		$this->directPaymentConfig  = $this->params->get('send_payments_to_store_owner', 0, 'INTEGER');
		$this->silentVendor         = $this->params->get('silent_vendor', 1, 'INTEGER');
		$this->adaptivePayment      = $this->params->get('gateways', array(), 'ARRAY');

		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper         = new storeHelper;

		$model     = $this->getModel('vendor');
		$app       = Factory::getApplication();
		$input     = $app->input;

		$layout          = $input->get('layout', 'cp');
		$this->adminCall = $adminCall = $input->get('adminCall', 0, 'INTEGER');
		$store_id        = $input->get('store_id', '0', 'INTEGER');
		$this->storeinfo = '';
		$user            = Factory::getUser();

		if ($layout != "contactus")
		{
			$specialAccess = 0;

			if ($layout == "createstore")
			{
				$this->countrys = $Quick2cartModelZone->getCountry();

				if (!$user->id)
				{
					$return = base64_encode(Uri::getInstance());
					$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
					$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
					$app->redirect($login_url_with_return, 403);
				}

				if (!empty($adminCall))
				{
					$specialAccess = $comquick2cartHelper->isSpecialAccess();
				}
			}

			if ($layout == "createstore" || $layout == "managestore" || $layout == "cp")
			{
				// Check for multivender COMPONENT PARAM
				$isMultivenderOFFmsg = $comquick2cartHelper->isMultivenderOFF();
			}

			if (!empty($isMultivenderOFFmsg))
			{
				if (!empty($adminCall))
				{
					// CALLED FROM ADMIN
					if ($specialAccess == 0)
					{
						echo $this->specialAccessMsg();

						return false;
					}
				}
				else
				{
					print $isMultivenderOFFmsg;

					return false;
				}
			}
		}

		if ($layout == "createstore")
		{
			$tjvendorFrontHelper       = new TjvendorFrontHelper;
			$this->vendorCheck         = $tjvendorFrontHelper->checkVendor('', 'com_quick2cart');
			$this->checkGatewayDetails = $tjvendorFrontHelper->checkGatewayDetails($user->id, 'com_quick2cart');
			$vendorXrefTable           = Table::getInstance('vendorclientxref', 'TjvendorsTable', array());

			$vendorXrefTable->load(
				array(
					'vendor_id' => $this->vendorCheck,
					'client' => 'com_quick2cart'
				)
			);

			$this->checkVendorApproval = $vendorXrefTable->approved;

			if (($this->vendorCheck && $this->silentVendor == 0) || $this->silentVendor == 1)
			{
				$this->allowed = 1;
			}
			else
			{
				$this->allowed = 0;
			}

			$this->orders_site        = 1;
			$store_id                 = $input->get('store_id', 0, 'INTEGER');

			// DEFAULT ALLOW TO CREAT STORE
			$this->allowToCreateStore = 1;

			// Means edit task
			if (!empty($store_id))
			{
				$this->store_authorize = $comquick2cartHelper->store_authorize("vendor_createstore", $store_id);
				$this->editview        = 1;
				$this->storeinfo       = $comquick2cartHelper->editstore($store_id);

				// Get weight and length select box
				$this->legthList  = $storeHelper->getLengthClassSelectList($storeid = 0, $this->storeinfo[0]->length_id);
				$this->weigthList = $storeHelper->getWeightClassSelectList($storeid = 0, $this->storeinfo[0]->weight_id);

				$userPrivacyTable = Table::getInstance('tj_consent', 'TjprivacyTable', array());
				$userPrivacyData = $userPrivacyTable->load(
											array(
													'client' => 'com_quick2cart.store',
													'client_id' => $this->storeinfo[0]->id,
													'user_id' => $this->storeinfo[0]->owner
												)
										);

				if ($userPrivacyData == true)
				{
					$this->storeinfo[0]->privacy_terms_condition = 1;
				}
			}
			else
			{
				// NEW STORE TASK:: CK FOR WHETHER WE HV TO ALLOW OR NOT
				$this->allowToCreateStore = $storeHelper->isAllowedToCreateNewStore();

				// Get weight and length select box
				$this->legthList  = $storeHelper->getLengthClassSelectList($storeid = 0, 0);
				$this->weigthList = $storeHelper->getWeightClassSelectList($storeid = 0, 0);
			}

			// START Q2C Sample development
			PluginHelper::importPlugin('system');

			// @DEPRICATED
			$result = $app->triggerEvent('onBeforeQ2cEditStore', array($store_id));

			// Call the plugin and get the result
			$OnBeforeCreateStore = '';

			if (!empty($result))
			{
				// If more than one plugin returns
				$OnBeforeCreateStore = trim(implode("\n", $result));
			}

			$result = $app->triggerEvent('onBeforeQ2cStoreEdit', array($store_id));

			if (!empty($result))
			{
				// If more than one plugin returns
				$OnBeforeCreateStore .= trim(implode("\n", $result));
			}

			$this->OnBeforeCreateStore = $OnBeforeCreateStore;
		}
		elseif ($layout == "managestore")
		{
			$this->storeDetailInfo = $comquick2cartHelper->getSoreInfoInDetail($store_id);
		}
		elseif ($layout == "cp")
		{
			$this->catpage_Itemid         = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=category');
			$this->orders_itemid          = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders');
			$this->store_customers_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders&layout=mycustomer');
			$user                         = Factory::getUser();

			if ($user->id)
			{
				// Chck whetere there is any product or not
				// Retrun store_id,role etc with order by role,store_id
				$this->store_role_list = $store_role_list = $comquick2cartHelper->getStoreIds();

				// Store_id is changed from manage storeorder view
				$change_storeto = $input->get('change_store', 0, 'INTEGER');

				// When chage store,get latest storeid otherwise( on first load) set first storeid as default
				$firstStore     = (!empty($store_role_list[0]['store_id']) ? $store_role_list[0]['store_id'] : '');
				$this->store_id = $store_id = (!empty($change_storeto)) ? $change_storeto : $firstStore;
			}

			if (!empty($this->store_id))
			{
				$this->prodcountprodCount = $model->storeProductCount($this->store_id);
				$this->getPeriodicIncomeGrapthData = $model->getPeriodicIncomeGrapthData($store_id);

				// Get revenue ,total order, and qty
				$this->getPeriodicIncome = $model->getPeriodicIncome($store_id);

				// GETTING TOATL SALES
				$this->totalSales = $model->getTotalSales($store_id);

				// GETTING TOtal orders
				$this->totalOrdersCount = $model->getTotalOrdersCount($store_id);

				// GETTING LAST 5 ORDERS
				$this->last5orders     = $model->getLast5orders($store_id);

				// Getting store detail
				$this->storeDetailInfo = $comquick2cartHelper->getSoreInfoInDetail($store_id);

				// Get customer count for store.
				$this->storeCustomersCount = $model->getStoreCustomersCount($store_id);

				// Get top seller products.
				$product_path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

				if (!class_exists('productHelper'))
				{
					JLoader::register('productHelper', $product_path);
					JLoader::load('productHelper');
				}

				$productHelper           = new productHelper;
				$this->topSellerProducts = $productHelper->getTopSellerProducts($store_id, 0, 5);
			}
		}
		elseif ($layout == "store")
		{
			$jinput    = $app->input;

			// Store_id is changed from  STORE view
			$this->change_prod_cat = $jinput->get('store_cat', 0, 'INTEGER');
			$this->store_id        = $store_id = $input->get('store_id', 0, 'INTEGER');

			if (!empty($this->store_id))
			{
				$this->storeDetailInfo = $comquick2cartHelper->getSoreInfoInDetail($store_id);

				require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
				$tjGeoHelper           = TjGeoHelper::getInstance('TjGeoHelper');
				$this->storeDetailInfo = $comquick2cartHelper->getSoreInfoInDetail($this->store_id);

				if ($this->storeDetailInfo["country"])
				{
					$this->storeDetailInfo["country"] = $tjGeoHelper->getCountryNameFromId($this->storeDetailInfo["country"]);
				}

				if ($this->storeDetailInfo["region"])
				{
					$this->storeDetailInfo["region"] = $tjGeoHelper->getRegionNameFromId($this->storeDetailInfo["region"]);
				}

				// ALL FETCH ALL CATEGORIES
				$this->cats = $storeHelper->getStoreCats($this->store_id, $this->change_prod_cat, 1, 'store_cat');

				// FETCH ALL STORE PRODUCT
				JLoader::import('store', JPATH_SITE . '/components/com_quick2cart/models');
				$model              = new Quick2cartModelstore;
				$fetchFeatured      = !empty($this->change_prod_cat) ? 1 : 0;
				$this->allStoreProd = $model->getAllStoreProducts('com_quick2cart', $this->store_id, $fetchFeatured);
				$pagination         = $model->getPagination('com_quick2cart', $this->store_id);
				$this->pagination   = $pagination;
			}
		}
		elseif ($layout == "contactus")
		{
			$this->store_id = $input->get('store_id', '0', 'INTEGER');
			$this->item_id  = $input->get('item_id', '0', 'INTEGER');
		}
		elseif ($layout == "storeinfo")
		{
			$this->store_id = $input->get('store_id');

			if (!empty($this->store_id))
			{
				$this->storeDetailInfo = $comquick2cartHelper->getSoreInfoInDetail($this->store_id);
			}
		}

		$this->_setToolBar();

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function _setToolBar()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('QTC_VENDOR_PAGE'));
	}

	/**
	 * SpecialAccessMsg
	 *
	 * @return  string message.
	 *
	 * @since   1.6
	 */
	public function specialAccessMsg()
	{
		return "<div class=\"techjoomla-bootstrap\" >
					<div class=\"well\" >
						<div class=\"alert alert-danger\">
							<span >" . Text::_('QTC_SPECAIL_ACCESS_MSG') . " </span>
						</div>
					</div>
				</div>";
	}
}
