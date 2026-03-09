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

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Joomla\CMS\Object\CMSObject;

require_once JPATH_SITE . '/components/com_quick2cart/helpers/media.php';
/**
 * Quick2cart main helper
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class Comquick2cartHelper
{
	/**
	 * Constructor
	 *
	 * @since   2.2
	 */
	public function __construct()
	{
		// Load js assets

		// Add social library according to the social integration
		$qtcParams         = ComponentHelper::getParams('com_quick2cart');
		$socialintegration = $qtcParams->get('integrate_with', 'none');

		// Load main file
		jimport('techjoomla.jsocial.jsocial');
		jimport('techjoomla.jsocial.joomla');

		if ($socialintegration != 'none')
		{
			if ($socialintegration == 'JomSocial')
			{
				jimport('techjoomla.jsocial.jomsocial');
			}
			elseif ($socialintegration == 'EasySocial')
			{
				jimport('techjoomla.jsocial.easysocial');
			}
		}
	}

	/**
	 * Load Assets which are require for quick2cart.
	 *
	 * @return  null.
	 *
	 * @since   12.2
	 */
	public static function loadQuicartAssetFiles()
	{
		$qtcParams = ComponentHelper::getParams('com_quick2cart');

		// Define wrapper class
		if (!defined('Q2C_WRAPPER_CLASS'))
		{
			$wrapperClass   = "q2c-wrapper";
			$currentBSViews = $qtcParams->get('bootstrap_version', "bs3");

			$wrapperClass = ($currentBSViews == "bs3") ? " q2c-wrapper tjBs3 " : " q2c-wrapper techjoomla-bootstrap tjBs5";

			define('Q2C_WRAPPER_CLASS', $wrapperClass);
		}

		// Load js assets
		$tjStrapperPath = JPATH_SITE . '/media/techjoomla_strapper/tjstrapper.php';

		if (File::exists($tjStrapperPath))
		{
			require_once $tjStrapperPath;
			TjStrapper::loadTjAssets('com_quick2cart');
		}

		// According to component option load boostrap3 css file and chagne the wrapper
	}

	/**
	 * Get social lib object
	 *
	 * @param   string  $integration_option  integration_option.
	 *
	 * @since   2.2
	 *
	 * @return   object
	 */
	public function getQtcSocialLibObj($integration_option = '')
	{
		$this->qtcParams    = ComponentHelper::getParams('com_quick2cart');
		$integration_option = $this->qtcParams->get('integrate_with', 'none');

		if ($integration_option == 'Community Builder')
		{
			$SocialLibraryObject = new JSocialCB;
		}
		elseif ($integration_option == 'JomSocial')
		{
			$SocialLibraryObject = new JSocialJomsocial;
		}
		elseif ($integration_option == 'Jomwall')
		{
			$SocialLibraryObject = new JSocialJomwall;
		}
		elseif ($integration_option == 'EasySocial')
		{
			$SocialLibraryObject = new JSocialEasysocial;
		}
		elseif ($integration_option == 'none')
		{
			$SocialLibraryObject = new JSocialJoomla;
		}

		return $SocialLibraryObject;
	}

	/**
	 * Save order history
	 *
	 * @param   integer  $orderId     Order Id.
	 * @param   integer  $OItem_id    order item id.
	 * @param   string   $status      status.
	 * @param   string   $note        note.
	 * @param   integer  $notify_chk  Have to notify or not.
	 * @param   integer  $store_id    Store id.
	 *
	 * @since   2.2
	 *
	 * @return   null
	 */
	public function saveOrderStatusHistory($orderId, $OItem_id, $status, $note, $notify_chk = 0, $store_id = '')
	{
		$user = Factory::getUser();
		$db                      = Factory::getDBO();
		$order_history           = new stdclass;
		$order_history->id       = '';
		$order_history->order_id = $orderId;
		$order_history->order_item_id = $OItem_id;
		$order_history->creater_id = $user->id;

		if ($status != -1 || $status == '')
		{
			$order_history->order_item_status = $status;
		}

		$order_history->note              = $note;
		$order_history->customer_notified = $notify_chk;

		if (!$db->insertObject('#__kart_orders_history', $order_history, 'id'))
		{
			echo $db->stderr();
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('COM_QUICK2CART_ERROR_WHILE_SAVING_ORDER_HISTORY'), 'error');

			return false;
		}

		return true;
	}

	/**
	 * public function to insert/update store info table
	 *
	 * @param   MIXED  $data  array of fields for store
	 * table array('owner' => '423','title' => 'test','description' => 'test desc','address' => 'Nagar42','phone' =>
	 * '9822330707','store_email' => 'dipti_j@mailcom','store_avatar' => 'images/we.png');
	 *
	 * @return boolean
	 */
	public function saveStore($data)
	{
		JLoader::import('store', JPATH_SITE . '/components/com_quick2cart/models');
		$model  = new Quick2cartModelstore;
		$result = $model->saveStore($data);

		return $result;
	}

	/**
	 * public function to insert/update store role info table
	 *
	 * @param   MIXED  $data  array of fields for store role
	 * table array('store_id' => '2','user_id' => '423','role' => 'manager'); if 'id' is in array then update on id else insert
	 *
	 * @return  boolean
	 */
	public function saveStoreRole($data)
	{
		JLoader::import('store', JPATH_SITE . '/components/com_quick2cart/models');
		$model  = new Quick2cartModelstore;
		$result = $model->saveStoreRole($data);

		return $result;
	}

	/**
	 * ublic function to insert/update item info table ie product table. TODO REMOVE ALL PARAMETER
	 * AND SEND FORMATEED POST DATA SO THAT IT will be managable
	 *
	 * @param   MIXED  $cur_post  Current post object for product
	 *
	 * @return  product id
	 */
	public function saveProduct($cur_post, $saveExtraField = true)
	{
		$app = Factory::getApplication();

		// Load language file as require from backend add product.
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);
		$item_name = $cur_post->get('item_name', '', 'STRING');

		if ($item_name)
		{
			// OnBeforeq2cProductSave
			PluginHelper::importPlugin('system');
			$sku    = '';
			$client = '';
			$app->triggerEvent('onBeforeQ2cProductSave', array(&$cur_post));

			$att_detail = $cur_post->get('att_detail', array(), 'ARRAY');
			$multi_cur  = $cur_post->get('multi_cur', array(), 'ARRAY');

			// If called from content there will not find att_detail
			if (empty($att_detail))
			{
				$att_detail = array();
			}

			// Get currency field count && // Remove empty currencies from multi_curr
			$originalCount = count($multi_cur);
			$filtered_curr = array_filter($multi_cur, 'strlen');

			// Get currency field count after filter enpty allow 0
			$filter_count        = count($filtered_curr);
			$comquick2cartHelper = new comquick2cartHelper;
			$path                = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';
			$attri_model         = $comquick2cartHelper->loadqtcClass($path, "quick2cartModelAttributes");

			// Save products basic option
			$pid = $cur_post->get('pid', '', 'STRING');

			if (empty($pid))
			{
				// For native product manager
				$pid = $cur_post->get('item_id', '', 'STRING');
			}

			$client = $cur_post->get('client', '', 'STRING');
			$isUpdadateItemOperation = $attri_model->getitemid($pid, $client);

			// Save basic product details
			$item_id             = $attri_model->storecurrency($cur_post);
			$saveAttri           = $cur_post->get('saveAttri');

			if (is_numeric($item_id) && !empty($saveAttri))
			{
				$path      = JPATH_SITE . '/components/com_quick2cart/models/product.php';

				$prodmodel = $this->loadqtcClass($path, 'quick2cartModelProduct');
				$prodmodel->StoreAllAttribute($item_id, $att_detail, $sku, $client);
			}

			// SAVE PRODUCT MEDIA FILE
			$media_detail = $cur_post->get('prodMedia', array(), 'ARRAY');
			$saveMedia    = $cur_post->get('saveMedia');

			if (is_numeric($item_id) && !empty($saveMedia))
			{
				$productHelper = new productHelper;
				$productHelper->saveProdMediaDetails($media_detail, $item_id);
			}

			if ($app->isClient('site'))
			{
				$this->sendApprovalMail($cur_post, $item_id, $isUpdadateItemOperation);
			}

			if ($saveExtraField)
			{
				$extra_jform_data = $cur_post->get('jform', array(), 'array');
				$filesData        = $app->input->files->get('jform', array(), 'ARRAY');
				$extra_jform_data = array_merge_recursive($extra_jform_data, $filesData);

				if (!class_exists('Quick2cartModelProduct'))
				{
					JLoader::register('Quick2cartModelProduct', JPATH_SITE . '/components/com_quick2cart/models/product.php');
					JLoader::load('Quick2cartModelProduct');
				}

				JLoader::import('components.com_quick2cart.helpers.storeHelper', JPATH_SITE);

				$Quick2cartModelProduct = new Quick2cartModelProduct;
				$storeHelper         = new StoreHelper;
				$storeOwner = $cur_post->get('store_id', '', 'INT') ? $storeHelper->getStoreOwner($cur_post->get('store_id', '', 'INT')) : 0;

				$data                = array();
				$data['content_id']  = $item_id;
				$data['client']      = 'com_quick2cart.product';
				$data['fieldsvalue'] = $extra_jform_data;
				$data['created_by'] = $storeOwner;

				$Quick2cartModelProduct->saveExtraFields($data);
			}

			// TART Q2C Sample development
			PluginHelper::importPlugin('system');
			$sku    = '';
			$client = '';
			$app->triggerEvent('onAfterQ2cProductSave', array($item_id, $att_detail, $sku, $client));

			// Depricated
			$app->triggerEvent('onAfterQ2cProductSave', array($item_id, $att_detail, $sku, $client));

			// add finder type search index
			PluginHelper::importPlugin('finder');
			$isNew = $cur_post->get('pid', '', 'STRING') ? false : true;
			$row = new stdclass;
			$row->item_id = $item_id;
			$row->state = 1;

			Factory::getApplication()->triggerEvent('onFinderAfterSave', array('com_quick2cart.product', $row, $isNew));

			return $item_id;
		}
	}

	/**
	 * Send approval mail
	 *
	 * @param   Object   $cur_post                 Post object
	 * @param   integer  $item_id                  Unique product id
	 * @param   string   $isUpdadateItemOperation  isUpdadateItemOperation
	 *
	 * @return  OBJECT.
	 *
	 * @since   1.6
	 */
	public function sendApprovalMail($cur_post, $item_id, $isUpdadateItemOperation)
	{
		$params    = ComponentHelper::getParams('com_quick2cart');
		$path      = JPATH_SITE . '/components/com_quick2cart/models/product.php';
		$prodmodel = $this->loadqtcClass($path, "quick2cartModelProduct");
		$admin_app = $params->get('admin_approval');
		$on_edit   = $params->get('mail_on_edit');

		if ($admin_app == 1 && empty($isUpdadateItemOperation))
		{
			// While saving new product and admin approval set to 1
			$prodmodel->SendMailToAdminApproval($cur_post, $item_id, $newProduct = 1);
			$prodmodel->SendMailToOwner($cur_post);
		}

		// While editing product
		if ($on_edit == 1 && !empty($isUpdadateItemOperation))
		{
			// While editing product and admin approval set to 0
			$prodmodel->SendMailToAdminApproval($cur_post, $item_id, $newProduct = 0);
		}
	}

	/**
	 * Save attribute
	 *
	 * @param   integer  $item_id    item_id
	 * @param   string   $allAttrib  allAttrib
	 * @param   string   $sku        sku
	 * @param   string   $client     client
	 *
	 * @return  OBJECT.
	 *
	 * @since   1.6
	 */
	public function saveAttribute($item_id, $allAttrib, $sku, $client)
	{
		JLoader::import('product', JPATH_SITE . '/components/com_quick2cart/models');
		$model  = new quick2cartModelProduct;
		$result = $model->StoreAllAttribute($item_id, $allAttrib, $sku, $client);

		return $result;
	}

	/**
	 * Coupon store dropdown
	 *
	 * @return  OBJECT.
	 *
	 * @since   1.6
	 */
	public function getAllStoreIds()
	{
		$db    = Factory::getDBO();
		$user  = Factory::getUser();
		$query = "select * from `#__kart_store`
		 where live = '1'
		 AND owner = " . $user->id . "
		 order by id";
		$db->setQuery($query);

		return $res = $db->loadAssocList();
	}

	/**
	 * for associated product access
	 *
	 * @return  OBJECT.
	 *
	 * @since   1.6
	 */
	public function getEventsproduct()
	{
		$db    = Factory::getDBO();
		$query = "SELECT  i.*  FROM `#__jrob_product_xref`as x , `#__kart_items` as i
					WHERE x.`product_id` = i.item_id and `eventid` = '" . Factory::getApplication()->input->get('id') . "'";
		$db->setQuery($query);
		$balance = $db->loadObjectList();

		return $balance;
	}

	/*Sanjivani END*/

	/**
	 * addToCartAPI
	 * (
	 * [itemattributeoption_id] => 89
	 * [type] => Textbox
	 * [value] => Techjoomla
	 * )			)
	 *
	 * @param   STRING  $item      Array (	[id] => 5
	 * [parent] => com_quick2cart			// cliend
	 * [count] => 1										// qty
	 * [options] => 89,91	)
	 * @param   STRING  $userData  Array ([89] => Array // 89  is attribute option id
	 * 		 (
	 * 		 [itemattributeoption_id] => 89
	 * 			 [type] => Textbox
	 * 			 [value] => vm
	 * 			 )
	 *
	 * @return  STRING.
	 *
	 * @since   1.6
	 */
	public function addToCartAPI($item, $userData)
	{
		// Load cart model
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_quick2cart/models');
		$Quick2cartModelcart = BaseDatabaseModel::getInstance('cart', 'Quick2cartModel');

		// Return message
		$msg = array();
		$msg["item_id"] = $item['id'];

		// Makre cart
		$cartId = $Quick2cartModelcart->makeCart();

		// TART Q2C Sample development
		PluginHelper::importPlugin('system');
		$result = Factory::getApplication()->triggerEvent('onAfterQ2cProductAddingToCart', array($cartId, $item));

		if (!empty($result))
		{
			$item_obj = $result[0];
		}

		// Depricated start
		$result = Factory::getApplication()->triggerEvent('onBeforeQ2cAdd2Cart', array($cartId, $item));

		if (!empty($result))
		{
			$item_obj = $result[0];
		}
		// Depricated end

		if (!empty($item_obj))
		{
			if ($item_obj[0] == false)
			{
				// $msg['success'] = FALSE;
				$msg['message'] = $item_obj[1];

				// Return json_encode($msg);
				// As we r encoding in main controller
				return $msg;
			}
			else
			{
				$item = $item_obj[1];
			}
		}

		// END Q2C Sample development
		// Get Current item details. (Detail like item basic detail, attribute detail, option detail)
		$prod_details = $Quick2cartModelcart->getProd($item);

		if (empty($item['params']))
		{
			$item['params'] = '';
		}

		// If option present
		if (!empty($prod_details[1]))
		{
			// Add user field detail to options
			$AttrOptions     = $prod_details[1];
			$prod_details[1] = $this->AddUserFieldDetail($AttrOptions, $userData);
		}

		// Put item detail to cart
		$result               = $Quick2cartModelcart->putCartitem($cartId, $prod_details, $item['params']);

		if (is_array($result) && (array_key_exists('cart_item_id',$result) || array_key_exists('product_quantity',$result)))
		{
			$msg["cart_item_id"]  = $result['cart_item_id'];
			$msg["cart_item_qty"] = $result['product_quantity'];
		}

		// Validate Result. If added successfully.
		if (is_array($result) && $result["cart_item_id"] > 0)
		{
			$msg['status']      = true;
			$msg['successCode'] = 1;
			$params             = ComponentHelper::getParams('com_quick2cart');
			$popupBuyNow        = $params->get('popup_buynow', 1);

			// TO OPEN SMALL POP UP -
			if ($popupBuyNow == 2)
			{
				$toastrConfig                      = array();
				$toastrConfig["closeButton"]       = true;
				$toastrConfig["debug"]             = false;
				$toastrConfig["newestOnTop"]       = false;
				$toastrConfig["progressBar"]       = false;
				$toastrConfig["positionClass"]     = $params->get('toastrPositionClass', 'toast-top-right');
				$toastrConfig["preventDuplicates"] = true;
				$toastrConfig["onclick"]         = null;
				$toastrConfig["showDuration"]    = '3000';
				$toastrConfig["hideDuration"]    = '1000';
				$toastrConfig["timeOut"]         = $params->get('toastrTimeOut', 5000);
				$toastrConfig["extendedTimeOut"] = '1000';
				$toastrConfig["showEasing"]      = 'swing';
				$toastrConfig["hideEasing"]      = 'linear';
				$toastrConfig["showMethod"]      = 'fadeIn';
				$toastrConfig["hideMethod"]      = 'fadeOut';

				$item_count          = $Quick2cartModelcart->countCartitems();
				$msg['message']      = Text::sprintf('COM_QUICK2CART_COMPACT_MSG', $prod_details[0]['name'], $item_count);
				$comquick2cartHelper = new Comquick2cartHelper;
				$actionLink          = $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=cartcheckout');
				$q2cBaseUrl          = $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=category&layout=default');

				$bsVersion = $params->get('bootstrap_version', '', 'STRING');

				if (empty($bsVersion))
				{
					$bsVersion = (JVERSION > '4.0.0') ? 'bs5' : 'bs3';
				}

				$btnSmallClass = ($bsVersion == 'bs3') ? 'pull-left' : 'float-start';
				$msg['message'] .= "<a class='btn btn-danger " . $btnSmallClass ."' href=" . $actionLink . ">"
				. Text::_('COM_QUICK2CART_CHECKOUT') . "</a><a class='btn btn-warning pull-right float-start mt-2' href=" . $q2cBaseUrl . ">" . Text::_('QTC_BACK') . "</a>";

				$msg['toastrConfig'] = $toastrConfig;
			}

			$cartItem         = $Quick2cartModelcart->getCartitems();
			$msg['itemCount'] = count($cartItem);
		}
		elseif (is_numeric($result) && $result == 2)
		{
			// Single store checkout is enabled and try to buy the product from other store.
			$msg['successCode'] = 2;
			$msg['status']      = false;
			$msg['message']     = Text::_('COM_QUICK2CART_SINGLE_STORE_CKOUT_ALLOWED');
		}
		else
		{
			$msg['successCode'] = 0;
			$msg['success']     = false;
			$msg['message']     = $result;
		}

		return $msg;
	}

	/**
	 * This function copies(modify) user entered detail to attribute option name
	 * $userData array like Array	(	[89] => Array   // 89  is attribute option id
	 * (
	 * [itemattributeoption_id] => 89
	 * [type] => Textbox
	 * [value] => Techjoomla)			)
	 *
	 * @param   iterable|object  $AttrOptions  AttrOptions
	 * @param   string  $userData     userData
	 *
	 * @return  html.
	 *
	 * @since   1.6
	 */
	public function AddUserFieldDetail($AttrOptions, $userData)
	{
		if (!empty($AttrOptions) && !empty($userData))
		{
			// For each user option, compare option_id with Db attribure option. If found then change option value to user value
			foreach ($AttrOptions as $key => $option)
			{
				$index = $option['itemattributeoption_id'];

				if (!empty($userData[$index]))
				{
					$AttrOptions[$key]['itemattributeoption_name'] = $userData[$index]['value'];
				}
			}
		}

		return $AttrOptions;
	}

	/**
	 * get_module
	 *
	 * @param   integer  $layout_type  layout_type
	 * @param   string   $ckout_text   ckout_text
	 *
	 * @return  html.
	 *
	 * @since   1.6
	 */
	public function get_module($layout_type = "", $ckout_text = '')
	{
		$module = ModuleHelper::getModule('mod_quick2cart');

		return ModuleHelper::renderModule($module);
	}

	/**
	 * This function used to show product's detail's like price,attribute,qty and buy now button for CCK
	 *
	 * @param   integer  $pId            product id.
	 * @param   string   $parent         client eg com_content etc.
	 * @param   array    $qtcExtraParam  eg array('hideFreeDdownloads'=>true,'hideOriginalPrice'=>true, 'hideDiscountPrice'=>true,'hideAttributes'=>true)
	 *
	 * @return  string
	 *
	 * @since   1.6
	 */
	public function getBuynow($pId, $parent, $qtcExtraParam = array())
	{
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

		if (!class_exists('productHelper'))
		{
			JLoader::register('productHelper', $path);
			JLoader::load('productHelper');
		}

		$app       = Factory::getApplication();
		$comparams = ComponentHelper::getParams('com_quick2cart');
		$bsVersion = $comparams->get('bootstrap_version', 'bs3', 'STRING');
		$html      = '';

		JLoader::register("quick2cartViewcart", JPATH_SITE . "/components/com_quick2cart/views/cart/view.html.php");

		$layout = 'pushtocart_bs3';

		if ($bsVersion == 'bs5')
		{
			$layout = 'pushtocart_bs5';
		}

		$view   = new quick2cartViewcart;
		$view->addTemplatePath(JPATH_SITE . '/components/com_quick2cart/views/cart/tmpl');
		$view->addTemplatePath(JPATH_SITE . '/templates/' . $app->getTemplate() . '/html/com_quick2cart/cart');

		JLoader::import('cart', JPATH_SITE . '/components/com_quick2cart/models');
		$model          = new Quick2cartModelcart;
		$itemidAndState = $model->getitemidAndState($parent, $pId);

		// # if entry is not present in kart_item
		if (empty($itemidAndState['item_id']) || empty($itemidAndState['state']))
		{
			return false;
		}

		$item_id             = $itemidAndState['item_id'];

		// Return array of price
		$price               = $model->getPrice($item_id, 1);
		$itemAttrDetail      = $model->getItemCompleteAttrDetail($item_id);
		$comquick2cartHelper = new comquick2cartHelper;
		$productHelper       = new productHelper;
		$hasprice            = $this->hasAttributePrice($itemAttrDetail);

		// Get free products media file
		$mediaFiles          = $productHelper->getProdmediaFiles($item_id);
		$orderWithZeroPrice  = $comparams->get('orderWithZeroPrice', 0);
		$qtcShowBuyNow       = 0;

		if (!class_exists('Quick2cartModelProductpage'))
		{
			JLoader::register('Quick2cartModelProductpage', JPATH_SITE . '/components/com_quick2cart/models/productpage.php');
			JLoader::load('Quick2cartModelProductpage');
		}

		$quick2cartModelProductpage = new Quick2cartModelProductpage;

		$extra_field_data = $quick2cartModelProductpage->getDataExtra($item_id);

		if ($orderWithZeroPrice == 0 && ($price > 0 && $hasprice))
		{
			// Product final price has some price and attribute has currency values
			$qtcShowBuyNow = 1;
		}

		// @As discussed we should not restrict to show buy button on price bz some product may be free( download or etc)

		$view->price = $price;
		$view->set('_basePath', JPATH_SITE . '/components/com_quick2cart');
		$view->set('_controller', 'cart');
		$view->set('_view', 'cart');
		$view->set('_doTask', true);
		$view->set('hidemenu', true);
		$view->setModel($model, true);
		$view->setLayout($layout);
		$itemdetail          = $model->getItemRec($item_id);
		$showBuyNowBtn       = $productHelper->isInStockProduct($itemdetail);
		$view->showBuyNowBtn = $showBuyNowBtn;
		$view->stock         = $itemdetail->stock;
		$view->min_quantity  = $itemdetail->min_quantity;
		$view->max_quantity  = $itemdetail->max_quantity;
		$view->slab          = $itemdetail->slab;
		$view->product_id    = $pId;
		$view->item_id       = $item_id;
		$view->parent        = $parent;

		$attributes             = $productHelper->getItemCompleteAttrDetail($item_id);
		$view->attributes       = $attributes;
		$view->mediaFiles       = $mediaFiles;
		$view->params           = $comparams;
		$view->qtcExtraParam    = $qtcExtraParam;
		$view->extra_field_data = $extra_field_data;

		ob_start();
		$view->display();
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}

	/**
	 * getAttributeOption
	 *
	 * @param   INTEGER  $attr_id  attr_id
	 *
	 * @since   2.2
	 *
	 * @return   Object  Attribute Option Data
	 */
	public function getAttributeOption($attr_id)
	{
		$db    = Factory::getDBO();

		// Dont use itemattributeoption_price price . Take from #_kart_option_currency
		$query = 'SELECT * FROM #__kart_itemattributeoptions AS opt WHERE opt.itemattribute_id=' . (int) $attr_id . ' ORDER BY opt.ordering';
		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}

	/**
	 * To fetch attribute  option's  price and curreny according attr_id
	 *
	 * @param   INTEGER  $attr_id     attr_id
	 * @param   INTEGER  $currencies  currencies
	 *
	 * @since   2.2
	 *
	 * @return   iterable|object
	 */
	public function getAttributeOptionCurrPrice($attr_id, $currencies = "")
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select("*");
		$query->from('#__kart_itemattributeoptions AS opt');
		$query->where('opt.itemattribute_id=' . (int) $attr_id);
		$query->where('opt.state=1');
		$query->order('opt.ordering');
		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (empty($result))
		{
			return;
		}

		$itemattributeoption = array();

		foreach ($result as $key => $rec)
		{
			$itemattributeoption[$key] = $rec->itemattributeoption_id;
		}

		$attributeoption_id = implode(',', $itemattributeoption);

		// Getting  option price
		$query2 = "
			SELECT koc.id , koc.currency, koc.price as itemattributeoption_currency_price, iao . *
			FROM  `#__kart_itemattributeoptions` AS iao
			LEFT JOIN  `#__kart_option_currency` AS koc ON iao.itemattributeoption_id = koc.itemattributeoption_id
			WHERE koc.itemattributeoption_id
			IN ( " . $attributeoption_id . " ) AND ";

		if ($currencies == "")
		{
			// To get selected currency
			$att_currency = comquick2cartHelper::getCurrencySession();
			$query2 .= " `koc`.`currency`='" . $att_currency . "'";
		}
		else
		{
			$query2 .= " `koc`.`currency` IN('" . $currencies . "')";
		}

		$query2 .= " ORDER BY  `iao`.`ordering` ASC ";
		$db->setQuery($query2);
		$result2 = $db->loadObjectList();

		return $result2;
	}

	/**
	 * calamt
	 *
	 * @param   int|float  $amt  amt
	 * @param   int|float  $val  val
	 *
	 * @since   2.2
	 *
	 * @return   float
	 */
	public function calamt($amt, $val)
	{
		$amt = $amt + $val;

		return $amt;
	}

	/**
	 * This function return the order and order item details
	 *
	 * @param   INTEGER  $orderid   orderid
	 * @param   INTEGER  $store_id  store_id.  if store id is passed then only store item detail are return
	 *
	 * @since   2.2
	 *
	 * @return   array|integer|boolean|string
	 */
	public function getorderinfo($orderid = 0, $store_id = 0)
	{
		$db     = Factory::getDBO();
		$user   = Factory::getUser();
		$jinput = Factory::getApplication()->input;

		if ($orderid == 0)
		{
			$orderid = $jinput->get('orderid', 0, 'INT');
		}

		if (empty($orderid))
		{
			return 0;
		}

		// In result id field  belongs to order table not user table(due to same column name in both table last column is selected)
		$query = "SELECT u.* ,o.processor, o.original_amount,o.amount,o.coupon_discount, o.coupon_code,
				  o.couponDetails, o.transaction_id,o.ip_address,o.cdate, o.payee_id,o.status,o.id,
				  o.order_tax,o.order_tax_details,o.order_shipping,o.order_shipping_details,
				  o.currency,o.customer_note,o.prefix,o.extra,o.payment_note, o.deliveryfromdate, o.deliverytodate
				  FROM #__kart_orders as o JOIN #__kart_users as u ON o.id = u.order_id
				  WHERE o.id=" . $orderid . " order by u.id DESC";

				// ." AND o.payee_id=".$user->id;

		$db->setQuery($query);
		$order_result = $db->loadObjectList();

		// Change for backward compatiblity for user info not saving order id against it
		if (empty($order_result))
		{
			$query = "SELECT u.* ,o.processor,o.original_amount, o.amount,o.coupon_discount, o.coupon_code ,o.couponDetails ,
			o.transaction_id,o.ip_address,o.cdate, o.payee_id,o.status,
					o.id,o.order_tax,o.order_tax_details,o.order_shipping,o.order_shipping_details,
					o.currency,o.customer_note,o.prefix,o.extra
					FROM #__kart_orders as o LEFT JOIN #__kart_users as u ON o.id = u.order_id
					WHERE o.id=" . $orderid;

					// . " AND u.order_id IS NULL";

			$db->setQuery($query);
			$order_result = $db->loadObjectList();
		}

		// NOTE :: for mystore :: get all order releated to store  IF store_id is found
		$where = " i.order_id=" . $orderid;

		if (!empty($store_id))
		{
			$where = $where . " AND i.store_id=" . $store_id;
		}

		$orderlist["order_info"] = $order_result;
		
		if (isset($orderlist['order_info'][0]->customer_note) && (!empty($orderlist['order_info'][0]->customer_note)))
		{
			@$orderlist["order_info"][0]->customer_note = preg_replace('/\<br(\s*)?\/?\>/i', " ", $orderlist['order_info'][0]->customer_note);
		}
		$query = "SELECT i.order_item_id,i.item_id, i.`variant_item_id`,store_id,i.order_item_name,
					i.product_attribute_names,i.product_quantity,i.product_item_price,
					i.product_attributes_price, i.product_attributes, i.product_final_price,i.params,i.`item_tax`,
					i.`item_tax_detail`,i.`item_shipcharges`,i.`item_shipDetail`,i.`discount`,i.`discount_detail`, i.`coupon_code`
					FROM #__kart_order_item as i
					WHERE " . $where . " ORDER BY store_id";

					// ." AND o.payee_id=".$user->id;

		$db->setQuery($query);
		$orderlist['items']  = $db->loadObjectList();
		$comquick2cartHelper = new comquick2cartHelper;

		if (!empty($orderlist['order_info'][0]->status))
		{
			if (!empty($orderlist) && $orderlist['order_info'][0]->status == 'P')
			{
				$comquick2cartHelper->syncOrderItems($orderlist["items"], $order_result[0]->user_id, $orderid);
			}
		}

		$orderlist = $this->addCountryRegionNames($orderlist);

		return $orderlist;
	}

	/**
	 * Method to add country name, region name.
	 *
	 * @param   iterable|object  &$orderlist  orderlist
	 *
	 * @since   2.2
	 *
	 * @return   array|string
	 */
	public function addCountryRegionNames(&$orderlist)
	{
		require_once JPATH_SITE . '/components/com_tjfields/helpers/geo.php';
		$tjGeoHelper = TjGeoHelper::getInstance('TjGeoHelper');

		if (!empty($orderlist['order_info']))
		{
			foreach ($orderlist['order_info'] as $key => $info)
			{
				// If country code contain text.
				if (!empty($info->country_code))
				{
					$countryName = $info->country_code;

					if (is_numeric($info->country_code))
					{
						$countryName = $tjGeoHelper->getCountryNameFromId($info->country_code);
					}

					if ($countryName)
					{
						// Generate new field
						$orderlist['order_info'][$key]->country_name = $countryName;
					}
				}

				// Get Region code.
				if (!empty($info->state_code))
				{
					$stateName = $info->state_code;

					if (is_numeric($info->state_code))
					{
						$stateName = $tjGeoHelper->getRegionNameFromId($info->state_code);
					}

					if ($stateName)
					{
						// Generate new field
						$orderlist['order_info'][$key]->state_name = $stateName;
					}
				}
			}
		}

		return $orderlist;
	}

	/**
	 * public function sendordermail sends Order email to the user and the email in config
	 *
	 * @param   INTEGER  $orderid  orderid
	 *
	 * @return  array
	 */
	public function sendordermail($orderid)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$params              = ComponentHelper::getParams('com_quick2cart');
		$jinput              = Factory::getApplication()->input;
		$jinput->set('orderid', $orderid);
		$order                  = $order_bk = $comquick2cartHelper->getorderinfo($orderid);
		$this->orderinfo        = $order['order_info'];
		$this->orderitems       = $order['items'];
		$this->orders_site      = 1;
		$this->orders_email     = 1;
		$this->order_authorized = 1;

		if ($this->orderinfo[0]->address_type == 'BT')
		{
			$billemail = $this->orderinfo[0]->user_email;
		}
		elseif ($this->orderinfo[1]->address_type == 'BT')
		{
			$billemail = $this->orderinfo[1]->user_email;
		}

		$fullorder_id = $order['order_info'][0]->prefix . $orderid;

		if (!Factory::getUser()->id && $params->get('guest'))
		{
			$jinput->set('email', md5($billemail));
		}

		// Allow facility to send the order email different than order detail page
		$app = Factory::getApplication();
		$override = JPATH_BASE . '/templates/' . $app->getTemplate() . '/html/com_quick2cart/orders/orderemail.php';

		if (File::exists($override))
		{
			$view = $comquick2cartHelper->getViewpath('orders', 'orderemail');
		}
		else
		{
			$view = $comquick2cartHelper->getViewpath('orders', 'order_' . QUICK2CART_LOAD_BOOTSTRAP_VERSION);
		}

		ob_start();
		include $view;
		$html = ob_get_contents();
		ob_end_clean();

		$app  = Factory::getApplication();
		$site = $app->get('sitename');
		$html = '<div>' . Text::sprintf('QTC_ORDER_MAIL_MSG', $site) . '</div>' . $html;
		$body = $html;

		$find = array('{ORDERNO}','{SITENAME}');
		$replace = array($fullorder_id,$site);
		$subject = str_replace($find, $replace, Text::_('QTC_ORDER_MAIL_SUB'));

		// Remove 	COMMENT:: COMMENTED FOR AVOID MAIL
		$comquick2cartHelper->sendmail($billemail, $subject, $body, $params->get('sale_mail'));

		// When multivendior is OFF:: dont send vendor Email
		$params             = ComponentHelper::getParams('com_quick2cart');
		$multivendor_enable = $params->get('multivendor', 0);

		if (!empty($multivendor_enable))
		{
			$comquick2cartHelper->vendorEmail($order_bk, $orderid);
		}
	}

	/**
	 * vendorEmail
	 *
	 * @param   integer|array|string  $order    order
	 * @param   string                $orderid  orderid
	 *
	 * @return  Void
	 */
	public function vendorEmail($order, $orderid)
	{
		$fullorder_id            = $order['order_info'][0]->prefix . $orderid;

		// Getting orderitem infomation
		$comquick2cartHelper     = new comquick2cartHelper;
		$order_details           = $comquick2cartHelper->getOrderitems($orderid);

		// GETTTING STORE INFORMATION
		$store_array             = array();
		$storeinfo               = array();

		foreach ($order_details as $cart)
		{
			if (!in_array($cart['store_id'], $store_array))
			{
				$store_array[]            = $cart['store_id'];
				$qtc_store_id             = $cart['store_id'];
				$storeinfo[$qtc_store_id] = $comquick2cartHelper->getSoreInfo($cart['store_id']);
			}
		}

		$original_order_data = $order;

		foreach ($storeinfo as $key => $sinfo)
		{
			// Check for view override
			$view                = $comquick2cartHelper->getViewpath('orders', 'order_' . QUICK2CART_LOAD_BOOTSTRAP_VERSION);
			$this->orders_site       = 1;
			$this->orders_email      = 1;
			$this->vendor_email      = 1;
			$this->order_authorized  = 1;

			// STORE RELEATED VIEW
			$this->storeReleatedView = 1;

			$html  = '';
			$temp = $original_order_data['items'];

			// 3. REMOVE other verder store item  information
			foreach ($temp as $k => $order_item)
			{
				if ($order_item->store_id != $key)
				{
					unset($temp[$k]);
				}
			}

			$this->orderinfo       = $original_order_data['order_info'];
			$this->vendor_email    = 1;
			$this->vendor_store_id = $key;
			$this->orderitems      = $temp;
			$app = Factory::getApplication();
			$site      = $app->get('sitename');
			$html      = '<div>' . Text::sprintf('QTC_ORDER_VENDER_MAIL_MSG', $sinfo['title']) . '</div>';
			$find = array('{ORDERNO}','{SITENAME}');
			$replace = array($fullorder_id,$site);
			$subject = str_replace($find, $replace, Text::_('QTC_ORDER_MAIL_SUB'));

			ob_start();
			include $view;
			$html = ob_get_contents();
			ob_end_clean();

			$body = $html;
			$comquick2cartHelper->sendmail($sinfo['store_email'], $subject, $body, '');
		}
	}

	/**
	 * getViewpath
	 *
	 * @param   string  $viewname       name of view
	 * @param   string  $layout         layout name eg order
	 * @param   string  $searchTmpPath  it may be admin or site. it is side(admin/site) where to search override view
	 * @param   string  $useViewpath    it may be admin or site. it is side(admin/site) which VIEW shuld be use IF OVERRIDE IS NOT FOUND
	 *
	 * @return :: if exit override view then return path
	 */
	public function getViewpath($viewname, $layout = "", $searchTmpPath = 'SITE', $useViewpath = 'SITE')
	{
		// $searchTmpPath=($searchTmpPath=='SITE')?JPATH_SITE:JPATH_BASE;

		// $useViewpath=($useViewpath=='SITE')?JPATH_SITE:JPATH_BASE;
		$searchTmpPath = ($searchTmpPath == 'SITE') ? JPATH_SITE : JPATH_ADMINISTRATOR;
		$useViewpath   = ($useViewpath == 'SITE') ? JPATH_SITE : JPATH_ADMINISTRATOR;
		$app           = Factory::getApplication();

		if (!empty($layout))
		{
			$layoutname = $layout . '.php';
		}
		else
		{
			$layoutname = "default.php";
		}

		// Get templates from override folder
		$comquick2cartHelper     = new comquick2cartHelper;

		if ($searchTmpPath == JPATH_SITE)
		{
			$defTemplate = $this->getSiteDefaultTemplate(0);
		}
		else
		{
			$defTemplate = $this->getSiteDefaultTemplate(1);
		}

		// @TODO GET TEMPLATE MANUALLY as  $app->getTemplate() is not working
		// $searchTmpPath . '/templates/' . $app->getTemplate() . '/html/com_quick2cart/' . $viewname . '/' . $layoutname;
		$overide_basepath = $override = $searchTmpPath . '/templates/' . $defTemplate . '/html/com_quick2cart/' . $viewname . '/' . $layoutname;

		if (File::exists($override))
		{
			return $view = $override;
		}
		else
		{
			return $view = $useViewpath . '/components/com_quick2cart/views/' . $viewname . '/tmpl/' . $layoutname;
		}
	}

	/**
	 * sendmail
	 *
	 * @param   STRING  $recipient       recipient.
	 * @param   STRING  $subject         subject
	 * @param   STRING  $body            body
	 * @param   STRING  $bcc_string      bcc_string
	 * @param   STRING  $defaultBcc      defaultBcc, if $bcc_string is emapty and defaultBcc == 1 then email copy bcc's to site app email
	 * @param   STRING  $attachmentPath  attachmentPath
	 *
	 * @return  boolean
	 */
	public function sendmail($recipient, $subject, $body, $bcc_string = '', $defaultBcc = 0, $attachmentPath = "")
	{
		$app = Factory::getApplication();

		// Before sending email check is site email sending configuration is enabled and configured it
		if ($app->get('mailonline') == true)
		{
			if (empty($recipient))
			{
				return false;
			}

			$from      = $app->get('mailfrom');
			$fromname  = $app->get('fromname');
			$recipient = trim($recipient);
			$mode      = 1;
			$cc        = null;
			$bcc       = array();

			if ($bcc_string)
			{
				$bcc = explode(',', $bcc_string);
			}
			else
			{
				if ($defaultBcc == 1)
				{
					$bcc = array('0' => $app->get('mailfrom'));
				}
			}

			$attachment = null;

			if (!empty($attachmentPath))
			{
				$attachment = $attachmentPath;
			}

			$replyto     = null;
			$replytoname = null;

			try
			{
				return Factory::getMailer()->sendMail(
					$from, $fromname, $recipient, $subject, $body, $mode,
					$cc, $bcc, $attachment, $replyto, $replytoname
				);
			}
			catch (Exception $e)
			{
				$debug = $app->get('debug');

				if($debug == 1)
				{
					echo $e->getMessage() . "\n";
				}
			}
		}
	}

	/**
	 * public function to get the inline css html code from the emogrifier
	 *
	 * @param   STRING  $prev     prev.
	 * @param   STRING  $cssdata  cssdata
	 *
	 * @return  STRING
	 */
	public function getEmogrify($prev, $cssdata)
	{
		$path = JPATH_SITE . "/components/com_quick2cart/models/emogrifier.php";

		if (!class_exists('Emogrifier'))
		{
			JLoader::register('Emogrifier', $path);
			JLoader::load('Emogrifier');
		}

		// Condition to check if mbstring is enabled
		if (!function_exists('mb_convert_encoding'))
		{
			echo Text::_("MB_EXT");

			return $prev;
		}

		$emogr      = new Emogrifier($prev, $cssdata);
		$emorg_data = $emogr->emogrify();

		return $emorg_data;
	}

	/*
	 *  Array
	(  //IN-PROGRESSS
		[order] => Array  // FOR order level
			(
				[transaction_id] => 404-9786504-2989925
				[extra] => // THis will contain json formated data for key and value
				* {"OrderReadyToShipNotification":{"NotificationReferenceId":"64b20440-cf92-4e3c-b872-02fdc21a341a""}}}
			)

	)*/

	/**
	 * public function to update status of order
	 *
	 * @param   INTEGER  $order_id   int id of order
	 * @param   STRING   $status     string status of order
	 * @param   STRING   $comment    string default='' comment added if any
	 * @param   INTEGER  $send_mail  Int default=1 weather to send status change mail or not.
	 * @param   INTEGER  $store_id   INTEGER (1/0) if we are updating store product status
	 * @param   array    $extraData  This field contain the extra fields what you have to add on order leve or item level
	 *
	 * @return  void
	 */
	public function updatestatus($order_id, $status, $comment = '', $send_mail = 1, $store_id = 0, $extraData = array())
	{
		// Load language file as require from backend add product.
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$params              = ComponentHelper::getParams('com_quick2cart');
		$comquick2cartHelper = new comquick2cartHelper;
		$productHelper       = new productHelper;

		$app       = Factory::getApplication();
		$order_oldstatus = '';
		$db              = Factory::getDBO();

		// For changing store product order
		if (!empty($store_id))
		{
			$query = 'SELECT o.status FROM `#__kart_order_item` as o WHERE o.order_id =' . $order_id .
			' AND o.`store_id`=' . $store_id . ' order by `order_item_id`';
		}
		else
		{
			$query = "SELECT o.status FROM #__kart_orders as o WHERE o.id =" . $order_id;
		}

		$db->setQuery($query);
		$order_oldstatus = $db->loadResult();

		// Check if order new status is valid or not
		$oStatuses   = array();
		$oStatuses[] = HTMLHelper::_('select.option', 'P', Text::_('QTC_PENDIN'));
		$oStatuses[] = HTMLHelper::_('select.option', 'C', Text::_('QTC_CONFR'));
		$oStatuses[] = HTMLHelper::_('select.option', 'S', Text::_('QTC_SHIP'));
		$oStatuses[] = HTMLHelper::_('select.option', 'RF', Text::_('QTC_REFUN'));
		$oStatuses[] = HTMLHelper::_('select.option', 'E', Text::_('QTC_ERR'));

		$ordersModel = BaseDatabaseModel::getInstance('Orders', 'Quick2cartModel', array('ignore_request' => true));
		$oStatuses = $ordersModel->getValidOrderStatusList($order_oldstatus, $oStatuses);
		$canChangeOrderStatus = false;

		foreach ($oStatuses as $oStatus)
		{
			if ($oStatus->value == $status)
			{
				$canChangeOrderStatus = true;

				break;
			}
		}

		if ($canChangeOrderStatus == false)
		{
			return false;
		}

		switch ($status)
		{
	/*		case 'P':
					@$comquick2cartHelper->sendordermail($order_id);
				break;
*/
			case 'C':
				// To reduce stock
				$usestock             = $params->get('usestock');
				$outofstock_allowship = $params->get('outofstock_allowship');

				// $outofstock_allowship==1)
				if ($order_oldstatus != $status)
				{
					if ($usestock == 1)
					{
						$comquick2cartHelper->updateItemStock($order_id);
					}

					/*$comquick2cartHelper->updateStoreFee($order_id);*/
					$productHelper->addEntryInOrderItemFiles($order_id);
					$productHelper->addPoint($order_id);
				}

				break;
		}

		$res = new stdClass;

		// UPDATING STORE ORDER CHANGES
		if (!empty($store_id))
		{
			// Change ORDER_ITEM STATUS// here i want order_item_id to update status of all order item releated to store
			$isOrderStatusChanged = $comquick2cartHelper->updateOrderItemStatus($order_id, $store_id, $status);

			// 1 for order status change, 0 for order item change
			if (empty($isOrderStatusChanged))
			{
				// $return ;
			}
		}
		else
		{
			// IF admin changes ORDER status
			if (empty($status) || $status == -1)
			{
				$res->status = $status;
			}

			$res->id = $order_id;

			// If order level extra data received like transcation detail or extra field details
			if (!empty($extraData['order']))
			{
				if (!empty($extraData['order']['transaction_id']))
				{
					$res->transaction_id = $extraData['order']['transaction_id'];
				}

				if (!empty($extraData['order']['extra']))
				{
					// You will get key and value array/ Key will be update index and value will be detail
					$extrfieldsArray = json_decode($extraData['order']['extra'], true);

					$finalData = "";
					$query = "SELECT  `extra` FROM  `#__kart_orders` WHERE `id` =" . $order_id;

					foreach ($extrfieldsArray as $key => $data)
					{
						$finalData .= $this->appendExtraFieldData($data, $query, $key);
					}

					// If data present then only update the column
					if (!empty($finalData))
					{
						$res->extra = $finalData;
					}
				}
			}

			if (!$db->updateObject('#__kart_orders', $res, 'id'))
			{
				return 2;
			}

			$isOrderStatusChanged = $comquick2cartHelper->updateOrderItemStatus($order_id, 0, $status);

			// UPDATE ORDER ITEM STATUS ALSO
		}

		$params = ComponentHelper::getParams('com_quick2cart');
		$shippingMode = $params->get('shippingMode', 'itemLevel');
		$shippingEnabled = $params->get('shipping');

		// Call the plugin and get the result
		$query = "SELECT o.* FROM #__kart_orders as o WHERE o.id =" . $order_id;
		$db->setQuery($query);
		$orderobj = $db->loadObject();

		$orderIitemInfo = $this->getorderinfo($orderobj->id);

		// Default trigger for normal integration
		PluginHelper::importPlugin('system');
		$result = Factory::getApplication()->triggerEvent('onAfterQ2cOrderUpdate', array($orderobj, $orderIitemInfo));

		// Depricated
		$result = Factory::getApplication()->triggerEvent('onQ2cOrderUpdate', array($orderobj, $orderIitemInfo));

		// If  anone want to play with shipping at that time below code is required

		/*
		if ($shippingEnabled == 1)
		{
			if ($shippingMode == "orderLeval")
			{
				PluginHelper::importPlugin('qtcshipping');
				$result = Factory::getApplication()->triggerEvent('onQ2cOrderUpdate', array($orderobj, $orderIitemInfo));
			}
			elseif($shippingMode == "itemLevel" )
			{
				PluginHelper::importPlugin('tjshipping');
				$result = Factory::getApplication()->triggerEvent('onQ2cOrderUpdate', array($orderobj, $orderIitemInfo));
			}
		}
		*/
		// END Q2C Sample development

		// If ($send_mail == 1 && $order_oldstatus != $status)
		if ($send_mail == 1)
		{
			$params = ComponentHelper::getParams('com_quick2cart');

			// $adminemails = comquick2cartHelper::adminMails();
			$query  = "SELECT ou.user_id,ou.user_email,ou.firstname FROM #__kart_users as ou WHERE ou.address_type='BT' AND ou.order_id = " . $order_id;
			$db->setQuery($query);
			$orderuser = $db->loadObjectList();

			// Change for backward compatiblity for user info not saving order id against it
			if (empty($orderuser))
			{
				$query = "SELECT ou.user_id,ou.user_email,ou.firstname
							FROM #__kart_users as ou
							WHERE ou.address_type='BT' AND ou.order_id IS NULL
							AND ou.user_id = (SELECT o.user_info_id FROM #__kart_orders as o WHERE o.id =" . $order_id . ")";
				$db->setQuery($query);
				$orderuser = $db->loadObjectList();
			}

			$orderuser = $orderuser[0];

			switch ($status)
			{
				case 'C':
					$orderstatus = Text::_('QTC_CONFR');
					/*for invoice*/
					$useinvoice  = $params->get('useinvoice', '1');

					// If ($useinvoice == '1')
					{
						$this->invoice = 1;
						$jinput        = Factory::getApplication()->input;
						$jinput->set('orderid', $order_id);
						$order                  = $order_bk = $comquick2cartHelper->getorderinfo($order_id);
						$this->orderinfo        = $order['order_info'];
						$this->orderitems       = $order['items'];
						$this->orders_site      = 1;
						$this->orders_email     = 1;
						$this->order_authorized = 1;

						if ($this->orderinfo[0]->address_type == 'BT')
						{
							$billemail = $this->orderinfo[0]->user_email;
						}
						elseif ($this->orderinfo[1]->address_type == 'BT')
						{
							$billemail = $this->orderinfo[1]->user_email;
						}

						$fullorder_id          = $order['order_info'][0]->prefix . $order_id;
						$this->qtcSystemEmails = 1;

						// We will confirm the payment from online payment plugin sometime so no need to check guest condition here
						if (!Factory::getUser()->id)
						{
							$jinput->set('email', md5($billemail));
						}

						// Getting the site info for site invoice layout
						$this->siteInvoiceInfo = $this->getSiteInvoiceInfo();

						// Check for view override
						$view = $comquick2cartHelper->getViewpath('orders', 'order_' . QUICK2CART_LOAD_BOOTSTRAP_VERSION);
						ob_start();
						include $view;
						$invoicehtml = ob_get_contents();
						ob_end_clean();
					}
					/*for invoice*/
					break;
				case 'RF':
					$orderstatus = Text::_('QTC_REFUN');
					break;
				case 'S':
					$orderstatus = Text::_('QTC_SHIP');
					break;
				case 'E':
					$orderstatus = Text::_('QTC_ERR');
					break;
				case 'P':
					$orderstatus = Text::_('QTC_PENDIN');
					break;
				default:
					$orderstatus = $status;
					break;
			}

			$fullorder_id = $orderobj->prefix . $order_id;

			if (!empty($store_id))
			{
				$productStatus = $comquick2cartHelper->getProductStatus($order_id);
				$body          = Text::sprintf('QTC_STORE_PRODUCT_STATUS_CHANGE_BODY', $productStatus);
			}
			else
			{
				$body = Text::_('QTC_STATUS_CHANGE_BODY');
			}

			$site = $app->get('sitename');

			if ($comment)
			{
				$comment = str_replace('{COMMENT}', $comment, Text::_('QTC_COMMENT_TEXT'));
			}
			else
			{
				$comment = '';
			}

			$find    = array('{ORDERNO}', '{STATUS}', '{SITENAME}', '{NAME}', '{COMMENTTEXT}');
			$replace = array($fullorder_id, $orderstatus, $site, $orderuser->firstname, $comment);
			$body        = str_replace($find, $replace, $body);
			$guest_email = '';

			if (!$orderuser->user_id && $params->get('guest'))
			{
				$guest_email = "&email=" . md5($orderuser->user_email);
			}

			$Itemid     = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cartcheckout');
			$tempLink = Route::_('index.php?option=com_quick2cart&view=orders&layout=order' . $guest_email . '&orderid=' . $order_id . '&Itemid=' . $Itemid);
			$link       = Uri::root() . substr($tempLink, strlen(Uri::base(true)) + 1);

			if ($app->isClient('administrator'))
			{
				$link = substr($link, strlen(Uri::root()));
				$appInstance = CMSApplication::getInstance('site');
				$router = $appInstance->getRouter();
				$uri = $router->build($link);
				$link = $uri->toString();
				$link = Uri::root() . substr($link, strlen(Uri::root(true)) + 1);
			}

			$order_link = '<a href="' . $link . '">' . Text::_('QTC_ORDER_GUEST_LINK') . '</a>';
			$body       = str_replace('{LINK}', $order_link, $body);
			$body       = nl2br($body);

			// GETTING BODY AND MAIL SUBJECT
			if (!empty($invoicehtml))
			{
				// $body    = $body . '<div>' . Text::_('QTC_ORDER_INVOICE_IN_MAIL') . '</div> <br/>' . $invoicehtml;
				$body    = $body . '<br/> <hr/>' . $invoicehtml;
				$subject = Text::sprintf('QTC_STATUS_CHANGE_SUBJECT', $fullorder_id);
			}
			else
			{
				$subject = Text::sprintf('QTC_STATUS_CHANGE_SUBJECT', $fullorder_id);
			}

			// Call the plugin and get the result
			PluginHelper::importPlugin('system');
			$result = Factory::getApplication()->triggerEvent('onBeforeQ2cOrderEmailSend', array($orderobj, $subject, $body));

			if (!empty($result[0]))
			{
				$subject = $result[0][0];
				$body    = $result[0][1];
			}

			// Depricated
			$result = Factory::getApplication()->triggerEvent('onBeforeQ2cOrderUpdateEmail', array($orderobj, $subject, $body));

			if (!empty($result[0]))
			{
				$subject = $result[0][0];
				$body    = $result[0][1];
			}

			$multivendor_enable = $params->get('multivendor', 0);

			// Send mail to store owner about order status change
			if (!empty($multivendor_enable))
			{
				$comquick2cartHelper->vendorEmail($orderIitemInfo, $orderobj->id);
			}

			// Send mail
			if ($params['send_email_to_customer'] != 0)
			{
				$comquick2cartHelper->sendmail($orderuser->user_email, $subject, $body, $params->get('sale_mail'));
			}
		}

		$this->addPassbookEntry($orderIitemInfo, $status);
		$this->addPayoutEntry($orderIitemInfo, $status);
		$this->addOrderParams($orderIitemInfo, $status);
	}

	/**
	 * Add TJ-Vendors passbook Entry for the order
	 *
	 * @param   ARRAY   $orderInfo  Oder Information
	 * @param   STRING  $status     status
	 *
	 * @return  void
	 *
	 * @since   2.9.22
	 */
	public function addPassbookEntry($orderInfo, $status)
	{
		if (!in_array($status, array('C', 'RF')))
		{
			return;
		}

		JLoader::import('fronthelper', JPATH_SITE . '/components/com_tjvendors/helpers');
		$tjvendorFrontHelper = new TjvendorFrontHelper;

		$orderId  = isset($orderInfo['order_info'][0]->order_id) ? $orderInfo['order_info'][0]->order_id : '';
		$currency = isset($orderInfo['order_info'][0]->currency) ? $orderInfo['order_info'][0]->currency : '';

		$orderStores = array();

		foreach ($orderInfo['items'] as $orderItem)
		{
			$productCartPrice = ($orderItem->product_item_price + $orderItem->product_attributes_price) * $orderItem->product_quantity;

			if (!array_key_exists($orderItem->store_id, $orderStores))
			{
				$orderStores[$orderItem->store_id] = $productCartPrice;
			}
			else
			{
				$orderStores[$orderItem->store_id] = $orderStores[$orderItem->store_id] + $productCartPrice;
			}
		}

		foreach ($orderStores as $storeId => $storeOrderAmount)
		{
			$storeDetails = $this->getSoreInfoInDetail($storeId);
			$vendorId     = $storeDetails['vendor_id'];
			$vendorFee    = $this->getVendorFee($vendorId, $currency);
			$fee          = 0;

			if (!empty($vendorFee['percent']))
			{
				$fee += (($storeOrderAmount * $vendorFee['percent']) / 100);
			}

			if (!empty($vendorFee['flat']))
			{
				$fee += $vendorFee['flat'];
			}

			$orderDetails = array();
			$orderDetails['vendor_id']     = $vendorId;
			$orderDetails['status']        = $status;
			$orderDetails['client']        = "com_quick2cart";
			$orderDetails['client_name']   = Text::_('COM_QUICK2CART');
			$orderDetails['order_id']      = $orderId;
			$orderDetails['amount']        = $storeOrderAmount;
			$orderDetails['fee_amount']    = $fee;
			$orderDetails['customer_note'] = "";
			$orderDetails['currency']      = $currency;

			$tjvendorFrontHelper->addEntry($orderDetails);
		}
	}

	/**
	 * Add TJ-Vendors passbook Entry for the order
	 *
	 * @param   ARRAY   $orderInfo  Oder Information
	 * @param   STRING  $status     status
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function addOrderParams($orderInfo, $status)
	{
		if (!in_array($status, array('C', 'RF')))
		{
			return;
		}

		JLoader::import('fronthelper', JPATH_SITE . '/components/com_tjvendors/helpers');
		$tjvendorFrontHelper = new TjvendorFrontHelper;

		$orderId  = isset($orderInfo['order_info'][0]->order_id) ? $orderInfo['order_info'][0]->order_id : '';
		$currency = isset($orderInfo['order_info'][0]->currency) ? $orderInfo['order_info'][0]->currency : '';

		$orderStores = array();

		foreach ($orderInfo['items'] as $orderItem)
		{
			$productCartPrice = ($orderItem->product_item_price + $orderItem->product_attributes_price) * $orderItem->product_quantity;

			if (!array_key_exists($orderItem->store_id, $orderStores))
			{
				$orderStores[$orderItem->store_id] = $productCartPrice;
			}
			else
			{
				$orderStores[$orderItem->store_id] = $orderStores[$orderItem->store_id] + $productCartPrice;
			}
		}

		$orderParams  = array();
		JLoader::import('components.com_quick2cart.tables.order', JPATH_ADMINISTRATOR);
		$orderTable = Table::getInstance('Order', 'Quick2cartTable', array('dbo', Factory::getDbo()));

		foreach ($orderStores as $storeId => $storeOrderAmount)
		{
			$storeOrderParams = array();

			$storeDetails = $this->getSoreInfoInDetail($storeId);
			$vendorId     = $storeDetails['vendor_id'];
			$vendorFee    = $this->getVendorFee($vendorId, $currency);
			$fee          = 0;

			if (!empty($vendorFee['percent']))
			{
				$fee += (($storeOrderAmount * $vendorFee['percent']) / 100);
			}

			if (!empty($vendorFee['flat']))
			{
				$fee += $vendorFee['flat'];
			}

			$storeOrderParams['store_id'] = $storeId;
			$storeOrderParams['vendor_id'] = $vendorId;
			$storeOrderParams['order_percent_fee'] = $vendorFee['percent'];
			$storeOrderParams['order_flat_fee'] = $vendorFee['flat'];
			$storeOrderParams['fee_amount'] = $fee;
			$orderParams[] = $storeOrderParams;
		}

		$orderParams = json_encode($orderParams);
		$orderTable->load($orderId);
		$orderTable->params = $orderParams;
		$orderTable->store();
	}

	/**
	 * Method to calculate fee
	 *
	 * @param   array  $vendorId  Vendor Id
	 * @param   array  $currency  Currency
	 *
	 * @return  ARRAY  fee.
	 *
	 * @since   2.9.22
	 */
	public function getVendorFee($vendorId, $currency)
	{
		$params = ComponentHelper::getParams('com_quick2cart');
		$vendorCommissionPercent = $params->get('commission');
		$vendorCommissionFlat = 0;

		Table::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');
		$vendorCommissionTable  = Table::getInstance('vendorfee', 'TjvendorsTable', array());
		$vendorCommissionTable->load(array('vendor_id' => $vendorId, 'client' => 'com_quick2cart', 'currency' => $currency));

		if (!empty($vendorCommissionTable->id))
		{
			$vendorCommissionFlat = $vendorCommissionTable->flat_commission;
			$vendorCommissionPercent = $vendorCommissionTable->percent_commission;
		}

		return array('flat' => $vendorCommissionFlat, 'percent' => $vendorCommissionPercent);
	}

	/**
	 * Add TJ-Vendors Payout Entry
	 *
	 * @param   ARRAY   $orderInfo  Oder Information
	 * @param   STRING  $status     status
	 *
	 * @return  void
	 *
	 * @since   2.9.22
	 */
	public function addPayoutEntry($orderInfo, $status)
	{
		if (!in_array($status, array('C', 'RF')))
		{
			return;
		}

		$params              = ComponentHelper::getParams('com_quick2cart');
		$sendPaymentsToOwner = $params->get('send_payments_to_store_owner');

		$pgPlugin = isset($orderInfo['order_info'][0]->processor) ? $orderInfo['order_info'][0]->processor : '';
		$currency = isset($orderInfo['order_info'][0]->currency) ? $orderInfo['order_info'][0]->currency : '';

		$orderStores = array();

		foreach ($orderInfo['items'] as $orderItem)
		{
			$productCartPrice = ($orderItem->product_item_price + $orderItem->product_attributes_price) * $orderItem->product_quantity;

			if (!array_key_exists($orderItem->store_id, $orderStores))
			{
				$orderStores[$orderItem->store_id] = $productCartPrice;
			}
			else
			{
				$orderStores[$orderItem->store_id] = $orderStores[$orderItem->store_id] + $productCartPrice;
			}
		}

		// Get Payment plugin params
		$plugin       = PluginHelper::getPlugin('payment', $pgPlugin);
		$pluginParams = json_decode($plugin->params);

		if ($pgPlugin == 'adaptive_paypal' || ($pgPlugin == 'stripe' && $pluginParams->enableconnect == 1) || ($pgPlugin == 'paypal' && $sendPaymentsToOwner == 1))
		{
			$tjvendorsHelper = new TjvendorsHelper;

			foreach ($orderStores as $storeId => $storeOrderAmount)
			{
				$storeDetails = $this->getSoreInfoInDetail($storeId);
				$vendorId     = $storeDetails['vendor_id'];
				$vendorFee    = $this->getVendorFee($vendorId, $currency);
				$fee          = 0;

				if (!empty($vendorFee['percent']))
				{
					$fee += (($storeOrderAmount * $vendorFee['percent']) / 100);
				}

				if (!empty($vendorFee['flat']))
				{
					$fee += $vendorFee['flat'];
				}

				$newPayoutData          = array();

				if ($status == 'C')
				{
					$newPayoutData['debit']  = $storeOrderAmount - $fee;
					$payableAmount           = $tjvendorsHelper->getTotalAmount($vendorId, $currency, 'com_quick2cart');
					$newPayoutData['total']  = $payableAmount['total'] - $newPayoutData['debit'];
					$newPayoutData['credit'] = 0;
				}
				else
				{
					$newPayoutData['debit']  = 0;
					$newPayoutData['credit'] = $storeOrderAmount - $fee;
					$payableAmount           = $tjvendorsHelper->getTotalAmount($vendorId, $currency, 'com_quick2cart');
					$newPayoutData['total']  = $payableAmount['total'] + $newPayoutData['debit'];
				}

				$newPayoutData['transaction_time'] = Factory::getDate()->toSql();
				$newPayoutData['client']           = 'com_quick2cart';
				$newPayoutData['currency']         = $currency;
				$newPayoutData['transaction_id']   = "Q2C" . '-' . $currency . '-' . $vendorId . '-';
				$newPayoutData['id']               = '';
				$newPayoutData['vendor_id']        = $vendorId;
				$newPayoutData['status']           = 1;
				$newPayoutData['adaptive_payout']  = 1;

				if ($pgPlugin == 'paypal')
				{
					$customerNote = Text::_("COM_QUICK2CART_DIRECT_PAYMENT_VENDOR_PAYPAL");
				}

				if ($pgPlugin == 'adaptive_paypal')
				{
					$customerNote = Text::_("COM_QUICK2CART_DIRECT_PAYMENT_VENDOR_ADAPTIVE_PAYPAL");
				}

				if ($pgPlugin == 'stripe')
				{
					$customerNote = Text::_("COM_QUICK2CART_DIRECT_PAYMENT_VENDOR_STRIPE_CONNECT");
				}

				$payoutParams = array("customer_note" => $customerNote, "entry_status" => "debit_payout");
				$newPayoutData['params'] = json_encode($payoutParams);

				BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tjvendors/models', 'payout');
				$tjvendorsModelPayout = BaseDatabaseModel::getInstance('Payout', 'TjvendorsModel');
				$tjvendorsModelPayout->save($newPayoutData);
			}
		}
	}

	/**
	 * public function to build Invoice HTML & send it to buyer
	 *
	 * @param   INTEGER  $order_id   order_id
	 * @param   STRING   $status     status
	 * @param   STRING   $comment    comment
	 * @param   INTEGER  $send_mail  send_mail
	 * @param   INTEGER  $store_id   store_id
	 *
	 * @return  boolean
	 */
	public function sendInvoice($order_id, $status, $comment = '', $send_mail = 1, $store_id = 0)
	{
		$app           = Factory::getApplication();
		$db                  = Factory::getDBO();
		$params              = ComponentHelper::getParams('com_quick2cart');
		$comquick2cartHelper = new comquick2cartHelper;

		// Load language file as require from backend add product.
		Factory::getLanguage()->load('com_quick2cart', JPATH_SITE);

		// START Q2C Sample development
		$query = "SELECT o.* FROM #__kart_orders as o WHERE o.id =" . $order_id;
		$db->setQuery($query);
		$orderobj = $db->loadObject();

		// Get user detail
		$query    = "SELECT ou.user_id,ou.user_email,ou.firstname FROM #__kart_users as ou
		WHERE ou.address_type='BT' AND ou.order_id = " . $order_id;
		$db->setQuery($query);
		$orderuser = $db->loadObjectList();

		// Change for backward compatiblity for user info not saving order id against it
		if (empty($orderuser))
		{
			$query = "SELECT ou.user_id,ou.user_email,ou.firstname
			FROM #__kart_users as ou
			WHERE ou.address_type='BT' AND ou.order_id IS NULL
			AND ou.user_id = (SELECT o.user_info_id FROM #__kart_orders as o
			WHERE o.id =" . $order_id . ")";
			$db->setQuery($query);
			$orderuser = $db->loadObjectList();
		}

		$orderuser = $orderuser[0];
		$jinput    = Factory::getApplication()->input;
		$jinput->set('orderid', $order_id);
		$this->orders = $order                  = $order_bk = $comquick2cartHelper->getorderinfo($order_id, $store_id);
		$this->orderinfo        = $order['order_info'];
		$this->orderitems       = $order['items'];
		$this->orders_site      = 1;
		$this->orders_email     = 1;
		$this->order_authorized = 1;

		if ($this->orderinfo[0]->address_type == 'BT')
		{
			$billemail = $this->orderinfo[0]->user_email;
		}
		elseif ($this->orderinfo[1]->address_type == 'BT')
		{
			$billemail = $this->orderinfo[1]->user_email;
		}

		$fullorder_id = $order['order_info'][0]->prefix . $order_id;

		if (!Factory::getUser()->id && $params->get('guest'))
		{
			$jinput->set('email', md5($billemail));
			$this->calledFromOnePageCkout = 1;
		}

		// Check for view override
		$view = $comquick2cartHelper->getViewpath('orders', 'invoice_' . QUICK2CART_LOAD_BOOTSTRAP_VERSION);
		ob_start();
		include $view;
		$invoicehtml = ob_get_contents();
		ob_end_clean();
		$fullorder_id = $orderobj->prefix . $order_id;

		if (!empty($store_id))
		{
			$productStatus = $comquick2cartHelper->getProductStatus($order_id);
			$body          = Text::sprintf('QTC_STORE_PRODUCT_STATUS_CHANGE_BODY', $productStatus);
		}
		else
		{
			$body = Text::_('COM_QUICK2CART_INVOICE_BODY');
		}

		$site = $app->get('sitename');

		if ($comment)
		{
			$comment = str_replace('{COMMENT}', $comment, Text::_('QTC_COMMENT_TEXT'));
		}
		else
		{
			$comment = '';
		}

		$find    = array(
				'{ORDERNO}',
				'{STATUS}',
				'{SITENAME}',
				'{NAME}',
				'{COMMENTTEXT}'
			);

		$replace = array($fullorder_id, Text::_('QTC_CONFR'), $site, $orderuser->firstname, $comment);

		$body        = str_replace($find, $replace, $body);
		$guest_email = '';

		if (!$orderuser->user_id && $params->get('guest'))
		{
			$guest_email = "&email=" . md5($orderuser->user_email);
		}

		$Itemid     = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cartcheckout');
		$tempLink   = Route::_('index.php?option=com_quick2cart&view=orders&layout=order' . $guest_email . '&orderid=' . $order_id . '&Itemid=' . $Itemid);
		$link       = Uri::root() . substr($tempLink, strlen(Uri::base(true)) + 1);

		if ($app->isClient('administrator'))
		{
			$link = substr($link, strlen(Uri::root()));
			$appInstance = CMSApplication::getInstance('site');
			$router = $appInstance->getRouter();
			$uri = $router->build($link);
			$link = $uri->toString();
			$link = Uri::root() . substr($link, strlen(Uri::root(true)) + 1);
		}

		$order_link = '<a href="' . $link . '">' . Text::_('QTC_ORDER_GUEST_LINK') . '</a>';
		$body       = str_replace('{LINK}', $order_link, $body);
		$body       = nl2br($body);

		// GETTING BODY AND MAIL SUBJECT
		if (!empty($invoicehtml))
		{
			$body    = $body . '<br/><div>' . Text::_('QTC_ORDER_INVOICE_IN_MAIL') . '</div>';
			$body    = $body . $invoicehtml;
			$subject = Text::sprintf('QTC_INVOICE_MAIL_SUB', $site, $fullorder_id);
		}
		else
		{
			$subject = Text::sprintf('QTC_STATUS_CHANGE_SUBJECT', $fullorder_id);
		}

		// Call the plugin and get the result
		PluginHelper::importPlugin('system');
		$result = Factory::getApplication()->triggerEvent('onBeforeQ2cOrderEmailSend', array($orderobj, $subject, $body));

		if (!empty($result[0]))
		{
			$subject = $result[0][0];
			$body    = $result[0][1];
		}

		// Depricated
		$result = Factory::getApplication()->triggerEvent('onBeforeQ2cOrderUpdateEmail', array($orderobj, $subject, $body));

		if (!empty($result[0]))
		{
			$subject = $result[0][0];
			$body    = $result[0][1];
		}

		// Send mail
		$comquick2cartHelper->sendmail($orderuser->user_email, $subject, $body, $params->get('sale_mail'));
	}

	/**
	 * This public function check whether order contain only item releated to that store or not
	 * IF only releated to that store then status of order_item as well as order is changes
	 * else status of only releated order iitem will change
	 *
	 * @param   INTEGER  $order_id  order_id
	 * @param   INTEGER  $store_id  store_id
	 * @param   STRING   $status    status
	 *
	 * @return  boolean
	 */
	public function updateOrderItemStatus($order_id, $store_id, $status)
	{
		if (empty($status) || $status == -1)
		{
			// Invalid status then
			return -1;
		}

		$params              = ComponentHelper::getParams('com_quick2cart');
		$comquick2cartHelper = new comquick2cartHelper;

		// $productStatus = $comquick2cartHelper->getProductStatus($order_id);
		$where[]             = ' order_id=' . $order_id . ' ';

		if (!empty($store_id))
		{
			$where[] = ' store_id=' . $store_id . ' ';
		}

		$where = (count($where) ? ' WHERE ' . implode(' AND ', $where) : '');
		$db    = Factory::getDBO();
		$query = 'UPDATE #__kart_order_item SET `status`=\'' . $status . '\' ' . $where;
		$db->setQuery($query);
		$db->execute();

		// Function to add product purchase activity
		$path = JPATH_SITE . '/components/com_quick2cart/helpers/product.php';

		if (!class_exists('ProductHelper'))
		{
			JLoader::register('ProductHelper', $path);
			JLoader::load('ProductHelper');
		}

		$productHelper = new ProductHelper;
		$productHelper->pushProductPurchaseActivity($order_id, $status);

		// Vm: removed as per task: #20434

		// Get count of orderitem against order id
		$query = 'SELECT count(*) FROM #__kart_order_item WHERE order_id=' . $order_id;
		$db->setQuery($query);
		$totalOrderItems = $db->loadResult();

		// Get count __kart_order_item with current status.
		$query = 'SELECT count(*) FROM #__kart_order_item WHERE order_id=' . $order_id . ' AND status=\'' . $status . '\'';
		$db->setQuery($query);
		$StoreOrderItems = $db->loadResult();

		// All Order items having same status
		if ($totalOrderItems == $StoreOrderItems)
		{
			// Change order status
			$query = 'UPDATE #__kart_orders SET `status`=\'' . $status . '\' WHERE id=' . $order_id;
			$db->setQuery($query);
			$db->execute();

			return 1;
		}

		return 0;
	}

	/**
	 * getProductStatus
	 *
	 * @param   INTEGER  $orderid  orderid
	 *
	 * @return  STRING
	 */
	public function getProductStatus($orderid)
	{
		$db    = Factory::getDBO();
		$query = 'SELECT order_item_name,status FROM #__kart_order_item WHERE order_id=' . $orderid;
		$db->setQuery($query);
		$totalOrderItems = $db->loadAssocList();
		$data            = "<table>";

		foreach ($totalOrderItems as $items)
		{
			$status = empty($items['status']) ? "P" : $items['status'];

			switch ($status)
			{
				case 'P':
					$status = Text::_("QTC_PENDIN");
					break;
				case 'C':
					$status = Text::_("QTC_CONFR");
					break;
				case 'RF':
					$status = Text::_("QTC_REFUN");
					break;
				case 'S':
					$status = Text::_("QTC_SHIP");
					break;
				default:
				case 'E':
					$status = Text::_("QTC_ERR");
					break;
			}

			$data .= "<tr><td>" . $items['order_item_name'] . "</td> <td><strong> : " . $status . "</strong></td></tr>";
		}

		return $data .= "</table>";
	}

	/**
	 * getStoreItemStatus
	 *
	 * @param   INTEGER  $orderid  orderid
	 * @param   INTEGER  $storeid  storeid
	 *
	 * @return  STRING
	 */
	public function getStoreItemStatus($orderid, $storeid)
	{
		$db    = Factory::getDBO();

		// Get count with status is C
		$query = 'SELECT status FROM #__kart_order_item WHERE order_id=' . $orderid . ' AND store_id=' . $storeid;
		$db->setQuery($query);

		return $status = $db->loadResult();
	}

	/**
	 * Function to get Item id
	 *
	 * @param   INTEGER  $parsedLinke  parsed link
	 *
	 * @return  INT  Item id
	 *
	 * @since  1.0.0
	 */
	public function getCurrentAllProductMenuItemid($parsedLinke)
	{
		$app      = Factory::getApplication();
		$menu     = $app->getMenu();

		if ($activeMenu = $app->getMenu()->getActive())
		{
			$menuParams = $activeMenu->getParams();

			if (!empty($menuParams))
			{
				$categoryMenu = $menuParams->get('defaultCatId', '0', 'INT');
				$storeMenu = $menuParams->get('defaultStoreId', '0', 'INT');

				if ($categoryMenu || $storeMenu)
				{
					return $activeMenu->id;
				}
			}
		}

		$itemid = 0;

		if ($parsedLinke['view'] == "category")
		{
			/* Get the itemid of the menu which is pointed to individual category URL*/
			$menuItems = $menu->getItems('link', 'index.php?option=com_quick2cart&view=category&layout=default');
			$prod_cat = !empty($parsedLinke['prod_cat']) ? $parsedLinke['prod_cat'] : '';

			if (!empty($menuItems))
			{
				foreach ($menuItems as $menuItem)
				{
					if ($menuItem->getParams()->get('defaultCatId') == $prod_cat)
					{
						return $menuItem->id;
					}
				}

				if ($prod_cat)
				{
					// Check one level up and check whether menu is available or not
					foreach ($menuItems as $menuItem)
					{
						$cat_details = $this->getCatDetail($prod_cat);

						if ($menuItem->getParams()->get('defaultCatId') == $cat_details['parent_id'])
						{
							return $menuItem->id;
						}
					}
				}
			}

			/*Get the itemid of the menu which is pointed to product URL*/
			$allProductURL = 'index.php?option=com_quick2cart&view=category&layout=default';
			$menuItem = $menu->getItems('link', $allProductURL, true);

			if ($menuItem)
			{
				return $menuItem->id;
			}

			if (!$itemid && $app->isClient('administrator'))
			{
				$db		= Factory::getDbo();
				$query 	= $db->getQuery(true);
				$query->select($db->qn('id'));
				$query->from($db->qn('#__menu'));
				$query->where($db->qn('link') . ' LIKE ' . $db->Quote('%index.php?option=com_quick2cart&view=category&layout=default%'));
				$query->where($db->qn('published') . '=' . $db->Quote(1));
				$query->setLimit(1);
				$db->setQuery($query);
				$itemid = $db->loadResult();

				if ($itemid)
				{
					return $itemid;
				}
			}
		}

		return $itemid;
	}

	/**
	 * Get Itemid for menu links
	 *
	 * @param   string   $link          URL to find itemid for
	 *
	 * @param   integer  $skipIfNoMenu  return 0 if no menu is found
	 *
	 * @return  integer  $itemid
	 */
	public function getItemId($link, $skipIfNoMenu = 0)
	{
		$app = Factory::getApplication();

		static $qtcitemids = array();

		// Check in itemids array
		if (!empty($qtcitemids[$link]))
		{
			return $qtcitemids[$link];
		}

		$itemid = 0;
		parse_str($link, $parsedLinked);

		if (isset($parsedLinked['view']))
		{
			// For all product menu link
			if ($parsedLinked['view'] == 'category')
			{
				$itemid = $this->getCurrentAllProductMenuItemid($parsedLinked);
			}

			if (!empty($parsedLinked['view']) && $parsedLinked['view'] == 'vendor' && !empty($parsedLinked['layout']) && $parsedLinked['layout'] == 'store')
			{
				if (!empty($parsedLinked['store_id']))
				{
					$menu = $app->getMenu();
					$menuItems = $menu->getItems('link', "index.php?option=com_quick2cart&view=category&layout=default");

					foreach ($menuItems as $menuItem)
					{
						$menuParams = $menuItem->getParams();

						if (!empty($menuParams))
						{
							$categoryMenu = $menuParams->get('defaultCatId', '0', 'INT');
							$storeMenu = $menuParams->get('defaultStoreId', '0', 'INT');

							if ($storeMenu && empty($categoryMenu))
							{
								if ($storeMenu == $parsedLinked['store_id'])
								{
									return $menuItem->id;
								}
							}
						}
					}

					$menuItem = $menu->getItems('link', "index.php?option=com_quick2cart&view=category&layout=default", true);

					if (!empty($menuItem->id))
					{
						return $menuItem->id;
					}
				}
			}

			if (isset($parsedLinked['layout']) && $parsedLinked['layout'] == 'order')
			{
				$menu = $app->getMenu();

				// Get the itemid of the menu which is pointed to orders URL
				$ordersUrl = 'index.php?option=com_quick2cart&view=cartcheckout';
				$menuItem = $menu->getItems('link', $ordersUrl, true);

				if ($menuItem)
				{
					$itemid = $menuItem->id;
				}

				if (empty($itemid))
				{
					// Get the itemid of the menu which is pointed to orders URL
					$ordersUrl = 'index.php?option=com_quick2cart&view=orders';
					$menuItem = $menu->getItems('link', $ordersUrl, true);
				}

				if ($menuItem)
				{
					$itemid = $menuItem->id;
				}
			}
		}

		if (!$itemid)
		{
			if ($app->isClient('site'))
			{
				$menu = $app->getMenu();
				$menuItem = $menu->getItems('link', $link, true);

				if ($menuItem)
				{
					$itemid = $menuItem->id;
				}
			}

			if (!$itemid)
			{
				$db = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->select($db->quoteName('id'));
				$query->from($db->quoteName('#__menu'));
				$query->where($db->quoteName('link') . ' LIKE ' . $db->Quote($link));
				$query->where($db->quoteName('published') . '=' . $db->Quote(1));
				$query->where($db->quoteName('type') . '=' . $db->Quote('component'));
				$db->setQuery($query);
				$itemid = $db->loadResult();
			}

			if (!$itemid)
			{
				$input = Factory::getApplication()->input;
				$itemid = $input->get('Itemid', 0);
			}
		}

		// Add Itemid and link mapping
		if (empty($qtcitemids[$link]))
		{
			if (!empty($itemid))
			{
				$qtcitemids[$link] = $itemid;
			}
		}

		return $itemid;
	}

	/**
	 * Wrapper to Route to handle itemid We need to try and capture the correct itemid for different view
	 *
	 * @param   string   $url    Absolute or Relative URI to Joomla resource.
	 * @param   boolean  $xhtml  Replace & by &amp; for XML compliance.
	 * @param   integer  $ssl    Secure state for the resolved URI.
	 *
	 * @return  string with Itemid
	 *
	 * @since  1.0
	 */
	public function quick2CartRoute($url, $xhtml = true, $ssl = null)
	{
		static $q2cItemId = array();

		$app = Factory::getApplication();
		$jinput = $app->input;

		if (empty($q2cItemId[$url]))
		{
			$q2cItemId[$url] = $this->getitemid($url);
		}

		$pos = strpos($url, '#');

		if ($pos === false)
		{
			if (isset($q2cItemId[$url]))
			{
				if (strpos($url, 'Itemid=') === false && strpos($url, 'com_quick2cart') !== false)
				{
					$url .= '&Itemid=' . $q2cItemId[$url];
				}
			}
		}
		else
		{
			if (isset($q2cItemId[$url]))
			{
				$url = str_ireplace('#', '&Itemid=' . $q2cItemId[$view] . '#', $url);
			}
		}

		$routedUrl = Route::_($url, $xhtml, $ssl);

		return $routedUrl;
	}

	/**
	 * this will load any javascript only once @TODO input list of js files and inlude them
	 *
	 * @param   STRING  $script  script
	 *
	 * @return  void
	 */
	public function loadScriptOnce($script)
	{
		$doc = Factory::getDocument();
		$flg = 0;

		foreach ($doc->_scripts as $name => $ar)
		{
			if ($name == $script)
			{
				$flg = 1;
			}
		}

		if ($flg == 0)
		{
			HTMLHelper::_('script',$script);
		}
	}

	/**
	 * THIS FUNCTION RETURN SYMBOL IF PRESENT ELSE RETRUN CURRENCEY
	 *
	 * @param   STRING  $currency  formatting
	 *
	 * @return  string
	 */
	public function getCurrencySymbol($currency = '')
	{
		$comquick2cartHelper = new comquick2cartHelper;

		if (empty($currency))
		{
			$currency = $comquick2cartHelper->getCurrencySession();
		}

		$params   = ComponentHelper::getParams('com_quick2cart');
		$curr_sym = $params->get('addcurrency_sym');

		if (!empty($curr_sym))
		{
			$curr_syms   = explode(",", $curr_sym);
			$multi_curr  = $params->get('addcurrency');
			$multi_currs = explode(",", $multi_curr);
			$curkey      = array_search($currency, $multi_currs);

			if (is_numeric($curkey) && !empty($curr_syms[$curkey]))
			{
				$currency = $curr_syms[$curkey];
			}
		}

		return $currency;
	}

	/**
	 * Get current session currency
	 *
	 * @return  string
	 */
	public static function getCurrencySession()
	{
		$params      = ComponentHelper::getParams('com_quick2cart');
		$multi_curr  = $params->get('addcurrency');
		$multi_currs = explode(",", $multi_curr);

		if (count($multi_currs) == 1)
		{
			return $multi_currs[0];
		}

		if (!isset($_COOKIE['qtc_currency']))
		{
			$currency = $multi_currs[0];
		}
		else
		{
			$currency = $_COOKIE['qtc_currency'];
		}

		return $currency;
	}

	/**
	 * Get formatted price according to the quick2crt option
	 *
	 * @param   int  $price       price
	 * @param   int  $curr        curr
	 * @param   int  $formatting  formatting
	 *
	 * @return string formatted price-currency string
	 */
	public function getFromattedPrice($price, $curr = null, $formatting = 1)
	{
		$comquick2cartHelper = new comquick2cartHelper;

		if (empty($curr))
		{
			$curr = $comquick2cartHelper->getCurrencySession();
		}

		$curr_sym                   = $comquick2cartHelper->getCurrencySymbol($curr);
		$params                     = ComponentHelper::getParams('com_quick2cart');
		$currency_display_format    = $params->get('currency_display_format', "{SYMBOL} {AMOUNT}");

		$price                      = (double) (str_replace(',', '', $price));

		$whole = floor($price);
		$fraction = $price - $whole;

		if ($fraction > 0)
		{
			$price = number_format($price, 2);
		}

		$currency_display_formatstr = str_replace('{AMOUNT}', $price, $currency_display_format);
		$currency_display_formatstr = str_replace('{SYMBOL}', $curr_sym, $currency_display_formatstr);
		$currency_display_formatstr = str_replace('{CURRENCY}', $curr, $currency_display_formatstr);
		$html                       = '';

		if ($formatting == 1)
		{
			$html = "<span>" . $currency_display_formatstr . " </span>";
		}
		else
		{
			$html = $currency_display_formatstr;
		}

		return $html;
	}

	/*This function create select box with zoo applications as options
	 *
	 * */
	/*	function selectZooApps()
	{
	$dirs=comquick2cartHelper::getZooApps();
	$html="<select name='selectzooapps'>";
	foreach($dirs as $key=>$app)
	{
	$html.="<option value='$app'>$app</option>";
	}

	$html.="</select>";
	return $html;
	}
	*/

	/**
	 * This function return array of zoo apps
	 *
	 * @return  return array of zoo apps
	 */
	public function getZooApps()
	{
		$basepath = JPATH_SITE . '/media/zoo/applications';

		$dirs     = array();

		if ($handle = @opendir($basepath))
		{
			while (false !== ($entry = readdir($handle)))
			{
				if ($entry != "." && $entry != "..")
				{
					array_push($dirs, $entry);
				}
			}

			closedir($handle);
		}

		return $dirs;
	}

	/**
	 * This function add quick2cart custom element in application.xml file
	 *
	 * @param   STRING  $path  path of application.xml file
	 *
	 * @return  Void
	 */

	public function updateApplicationXml($path)
	{
		// Like /home/vidyasagar/html/Joomla2/components/com_zoo
		$find = JPATH_SITE . '/components/com_zoo';

		$status = file_exists($find);

		if ($status)
		{
			$nodefound = -1;
			$xml       = Factory::getXML($path);

			if ($xml)
			{
				foreach ($xml->params as $var)
				{
					if ((string) $var->attributes()->group == 'item-content')
					{
						if ($var->Children())
						{
							foreach ($var->param as $par)
							{
								$arr = $par->attributes();

								// Required node already exist
								if (trim($arr['name']) == 'qtc_params' && trim($arr['type']) == 'quick2cart')
								{
									$nodefound = 1;
									break;
								}
							}
						}

						// Create node
						if ($nodefound == -1)
						{
							$newnode = $var->addChild('param');
							$newnode->addAttribute('name', 'qtc_params');
							$newnode->addAttribute('type', 'quick2cart');
							$newnode->addAttribute('label', 'Quick2Cart Options');
							$newnode->addAttribute('description', 'Quick2Cart Options for displaying buy button');

							// Store object in xml format
							$xml->asXML($path);
						}
					}
				}
			}
			else
			{
				echo " Error ::Invalid file content";
			}
		}
		else
		{
			echo " Component is not installed !!!";
		}
	}

	/**
	 * getMinMax
	 *
	 * @param   INTEGER  $item_id  (integer) itemid  from Kart_items table
	 *
	 * @return array() of min and max  quantity
	 */
	public function getMinMax($item_id)
	{
		$db    = Factory::getDBO();
		$query = "SELECT min_quantity,max_quantity,slab FROM #__kart_items WHERE `item_id`=" . $item_id;
		$db->setQuery($query);

		return $itemid = $db->loadAssoc();
	}

	/**
	 *retrun LAST cart_id  depending upon userid
	 *
	 * @param   ARRAY  $userid  userid
	 *
	 * @return  INT
	 */
	public function getcartidForuser($userid)
	{
		$db    = Factory::getDBO();
		$query = "Select cart_id FROM #__kart_cart WHERE user_id = $userid ORDER BY cart_id DESC ";
		$db->setQuery($query);
		$cart_id = $db->loadResult();

		return $cart_id;
	}

	/**
	 * retrun LAST cart_id  depending upon Session
	 *
	 * @param   ARRAY  $sessionid  sessionid
	 *
	 * @return  INT
	 */
	public function guestCartId($sessionid)
	{
		$db    = Factory::getDBO();
		$query = "Select cart_id FROM #__kart_cart WHERE `session_id`='$sessionid' And user_id=0 ORDER BY cart_id DESC ";
		$db->setQuery($query);
		$cart_id = $db->loadResult();

		return $cart_id;
	}

	/**
	 * Delete all cart with ids are in cartids = array() 1-D or 1 leval array
	 *
	 * @param   ARRAY  $cart_ids  cart_ids
	 *
	 * @return  void
	 */
	public function deleteCartItemRec($cart_ids)
	{
		$db           = Factory::getDBO();
		$cart_ids_str = implode(',', $cart_ids);
		$query        = "DELETE FROM `#__kart_cartitems` WHERE cart_id IN ($cart_ids_str)";
		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * applyShipManegertrigger
	 *
	 * @param   STRING  $keyname  contain one of state,county,city
	 * @param   STRING  $value    value
	 *
	 * @return id if present
	 */
	public function getShippingManagerId($keyname, $value)
	{
		$db    = Factory::getDBO();

		// Select * from #__kart_city where lower(city)=lower("Ra's al Khaymah")
		$query = "select id from `#__kart_ship_manager` where lower(value)=lower(\"" . $value . "\") AND `key`='$keyname' ";
		$db->setQuery($query);
		$id = $db->loadResult();

		return $id;
	}

	/**
	 * applyShipManegertrigger
	 *
	 * @param   INTEGER  $shipid  ship_manager's table id
	 *
	 * @return  shipvalue:: price of shipid from  __kart_ship_manager_currency
	 */
	public function getShipCurrencyPrice($shipid)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();
		$db                  = Factory::getDBO();
		$query               = "select `shipvalue` from `#__kart_ship_manager_currency` where `ship_manager_id`='$shipid' AND `currency`='$currency' ";
		$db->setQuery($query);

		return $shipvalue = $db->loadResult();
	}

	/**
	 * applyShipManegertrigger
	 *
	 * @param   INTEGER  $totalamt     totalamt
	 * @param   INTEGER  $shipcharges  shipcharges
	 * @param   INTEGER  $methodname   methodname
	 *
	 * @return  array() of Attribute details
	 */
	public function applyShipManegertrigger($totalamt, $shipcharges, $methodname)
	{
		$shipdetails             = array();
		$shipdetails['charges']  = $shipcharges;
		$shipdetails['totalamt'] = $totalamt;

		// Call the plugin and get the result @TODO:need to check plugim type..
		PluginHelper::importPlugin('qtcshipping');
		$afteresults = Factory::getApplication()->triggerEvent($methodname, array($shipcharges,$totalamt));

		if (!empty($afteresults))
		{
			$shipcharges = $afteresults[0]['charges'];
			$totalamt    = $afteresults[0]['totalamt'];
		}

		$shipdetails['totalamt'] = $totalamt;
		$shipdetails['charges']  = $shipcharges;

		return $shipdetails;
	}

	/**
	 * Call this function after conform payment status
	 * This funtion deduct stock
	 *
	 * @param   INTEGER  $order_id  orderid
	 *
	 * @return  array() of Attribute details
	 */
	public function updateItemStock($order_id)
	{
		$db = Factory::getDBO();
		$q  = "SELECT `item_id`,`product_quantity`,`variant_item_id`
			FROM  `#__kart_order_item`
			WHERE `order_id` =" . (int) $order_id;
		$db->setQuery($q);
		$result = $db->loadObjectList();

		foreach ($result as $res)
		{
			$childItem_id = !empty($res->variant_item_id) ? $res->variant_item_id : $res->item_id;
			$minus_qty = "-" . $res->product_quantity;
			$minus_qty = (int) $minus_qty;
			$query     = "UPDATE  `#__kart_items`
						SET   `stock` =
						(
							CASE
							 WHEN
								  `stock` IS NOT NULL
							  THEN
								  (`stock` - " . $res->product_quantity . ")
							END
						)
						WHERE  `item_id`=" . $childItem_id;
			$db->setQuery($query);
			$db->execute();

			/* @LOW STOCK NOTIFICATION Low stock notification
			$q = "SELECT  stock
				FROM  `#__kart_items`
				WHERE `item_id` =" . (int) $childItem_id;
			$db->setQuery($q);
			$resultstock = $db->loadResult();
			$params      = ComponentHelper::getParams('com_quick2cart');

			if ($params->get('usestock') == 0 && $resultstock <= 0)
			{
				$q = "SELECT  name,store_id
					FROM  `#__kart_items`
					WHERE `item_id` =" . (int) $res->item_id;
				$db->setQuery($q);
				$itemresult           = $db->loadAssoc();
				$commented_by_userid  = Factory::getUser()->id;
				$comquick2cartHelper  = new comquick2cartHelper;
				$prodLink             = '<a class="" href="' . $comquick2cartHelper->getProductLink($res->item_id) . '">' .
												$itemresult['name'] .
										'</a>';
				$store_info  = $comquick2cartHelper->getSoreInfo($itemresult['store_id']);

				$tempStoreLink = 'index.php?option=com_quick2cart&view=vendor&layout=store&store_id=' . $itemresult['store_id'];
				$storeLink = '<a class="" href="' . Uri::root() .
								substr(Route::_($tempStoreLink), strlen(Uri::base(true)) + 1) . '">' .
									$store_info['title'] .
							'</a>';

				$notification_subject = Text::sprintf('QTC_NOTIFIY_LOW_STOCK', $prodLink, $storeLink);

				$comquick2cartHelper->addJSnotify(
										$commented_by_userid, $store_info['owner'],
										$notification_subject, 'notif_system_messaging', '0', ''
										);
			}*/
		}
	}

	/**
	 * getVersion
	 *
	 * @return  string
	 */
	public function getVersion()
	{
		$recdata = @file_get_contents('http://techjoomla.com/vc/index.php?key=abcd1234&product=quick2cart');

		return $recdata;
	}

	/**
	 * checkmailhash
	 *
	 * @param   INTEGER  $orderid   orderid
	 * @param   STRING   $mailhash  mailhash
	 *
	 * @return  array() of Attribute details
	 */
	public function checkmailhash($orderid, $mailhash)
	{
		$db = Factory::getDBO();
		$q  = "SELECT  id
			FROM  `#__kart_orders`
			WHERE md5(email) = '$mailhash' AND `id` =" . (int) $orderid;
		$db->setQuery($q);

		return $result = $db->loadResult();
	}

	/**
	 * This function take attribute id and  return all detail about attribute
	 *
	 * @param   INTEGER  $att_id  att_id
	 *
	 * @return  Object of Attribute details
	 */
	public function getAttributeDetails($att_id)
	{
		$helper      = new comquick2cartHelper;
		$att_options = $helper->getAttributeOption($att_id);
		$modelsPath = JPATH_SITE . '/components/com_quick2cart/models/cart.php';

		foreach ($att_options as $op)
		{
			$op->itemattribute_id = $att_id;
			$opdetails            = $helper->getOptionCurrency($op->itemattributeoption_id);

			// Adding currency,price details in $att_option detail array
			foreach ($opdetails as $opd)
			{
				$curr = $opd['currency'];
				$op->$curr = $opd['price'];
			}

			// Child_product_item_id
			if (!empty($op->child_product_item_id))
			{
				$cart_model         = $helper->loadqtcClass($modelsPath, "Quick2cartModelcart");

				// Fetch Child product detail
				$child_product_detail = $cart_model->getItemRec($op->child_product_item_id);
				$op->child_product_detail = !empty($child_product_detail) ? $child_product_detail : new stdClass;
			}
		}

		return $att_options;
	}

	/**
	 * getOptionCurrency
	 *
	 * @param   MIXED  $option_id  option_id
	 *
	 * @return  array() of option details
	 */
	public function getOptionCurrency($option_id)
	{
		$db = Factory::getDBO();
		$q  = "SELECT  id as option_currency_id,itemattributeoption_id ,currency,price
			FROM  `#__kart_option_currency`
			WHERE itemattributeoption_id  =" . $option_id;
		$db->setQuery($q);

		return $result = $db->loadAssocList();
	}

	/**
	 * This function check  whether all option has price respect to curreny
	 *
	 * @param   MIXED  $itemDetail  itemDetail
	 *
	 * @return  boolean
	 */
	public function hasAttributePrice($itemDetail)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$currency            = $comquick2cartHelper->getCurrencySession();

		foreach ($itemDetail as $attributs)
		{
			foreach ($attributs->optionDetails as $option)
			{
				// Does have currency price for attribute
				if (!property_exists($option, $currency))
				{
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * getModuleParams
	 *
	 * @param   STRING  $module_name  module_name
	 *
	 * @return  STRING
	 */
	public function getModuleParams($module_name)
	{
		$db = Factory::getDBO();
		$q  = "SELECT  params
			FROM  `#__modules`
			WHERE module  ='" . $module_name . "'";
		$db->setQuery($q);

		return $result = $db->loadResult();
	}

	/**
	 * getExtentionparam
	 *
	 * @param   STRING  $extention_name  extention_name
	 *
	 * @return  STRING
	 */
	public function getExtentionparam($extention_name)
	{
		$db = Factory::getDBO();
		$q  = "SELECT  params
			FROM  `#__extensions`
			WHERE `element` = '" . $extention_name . "'";
		$db->setQuery($q);

		return $result = $db->loadResult();
	}

	/**
	 * getSoreInfo
	 *
	 * @param   INTEGER  $item_id  item_id
	 *
	 * @return  STRING
	 */
	public function getSoreID($item_id)
	{
		$db = Factory::getDBO();
		$q  = "SELECT  `store_id`
			FROM  `#__kart_items`
			WHERE 	`item_id` = '" . $item_id . "'";
		$db->setQuery($q);

		return $result = $db->loadResult();
	}

	/**
	 * getSoreInfo
	 *
	 * @param   INTEGER  $store_id  store_id
	 *
	 * @return  ARRAY
	 */
	public function getSoreInfo($store_id)
	{
		$db = Factory::getDBO();
		$q  = "SELECT  id,`title`,owner,store_email
			FROM  `#__kart_store`
			WHERE 	`id` = '" . $store_id . "'";
		$db->setQuery($q);
		$result = $db->loadAssoc();

		// Return $store_info="Vendor info : ".$result;
		return $result;
	}

	/**
	 * getSoreInfoInDetail
	 *
	 * @param   INTEGER  $store_id  store_id
	 *
	 * @return  ARRAY
	 */
	public function getSoreInfoInDetail($store_id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__kart_store'));

		if (!empty($store_id))
		{
			$query->where($db->quoteName('id') . ' = ' . $db->quote($store_id));
		}

		$db->setQuery($query);
		$result = $db->loadAssoc();

		// Return $store_info="Vendor info : ".$result;
		return $result;
	}

	/**
	 * getColumns
	 *
	 * @param   STRING  $table  table
	 *
	 * @return  ARRAY
	 */
	public function getColumns($table)
	{
		$db          = Factory::getDBO();
		$field_array = array();
		$query       = "SHOW COLUMNS FROM " . $table;
		$db->setQuery($query);
		$columns = $db->loadobjectlist();

		for ($i = 0; $i < count($columns); $i++)
		{
			$field_array[] = $columns[$i]->Field;
		}

		return $field_array;
	}

	/**
	 * getOrderitems
	 *
	 * @param   STRING  $orderid  from_Currency
	 *
	 * @return  iterable|object
	 */
	public function getOrderitems($orderid)
	{
		$db    = Factory::getDBO();
		$query = "Select i.order_item_id as id,i.order_id,i.store_id,
				i.order_item_name as title,o.user_info_id,
				i.cdate,						i.mdate,
				i.product_quantity as qty, 		i.product_attribute_names as options,
				i.item_id, i.product_item_price,i.product_attributes_price,i.product_final_price,
				i.product_attributes,o.currency,i.original_price
	 FROM `#__kart_order_item` AS i LEFT JOIN  `#__kart_orders` AS o
	  ON i.order_id=o.id
	 WHERE order_id='" . $orderid . "' order by `store_id`";
		$db->setQuery($query);
		$cart = $db->loadAssocList();

		return $cart;
	}

	/**
	 * If store_id = present mean is not empty then get ROLE against store_id and user_id
	 *  else get highest authority  role
	 *
	 * @param   STRING        $view_layout  from_Currency
	 * @param   INTEGER|NULL  $store_id     contain orderid,to,from,and currency exchange rate
	 *
	 * @return  void|boolean|integer
	 */
	public function store_authorize($view_layout, $store_id = "")
	{
		// 1 STEP :: IF STORE OWNER THEN ALL VIEWS ARE ACCESSIBLE
		$user = Factory::getUser();
		$db   = Factory::getDBO();

		// STEP 2 :check for user role

		// Forsome  view $user->id=0 when guest checkout is present

		if (empty($user->id))
		{
			return false;
		}

		$store_id_condition = "";

		if (!empty($store_id))
		{
			$store_id_condition = " AND `store_id`=" . $store_id . " ";
		}

		$query = "Select role FROM `#__kart_role` WHERE user_id=" . $user->id . " " . $store_id_condition . " order by role";

		// ."  AND store_id=".$store_id;

		$db->setQuery($query);
		$ur_role = $db->loadResult($query);

		// Get $rolearray array() including file
		$path    = JPATH_SITE . "/components/com_quick2cart/authorizeviews.php";
		include  $path;

		// All access
		if ($ur_role == $universal_role)
		{
			return 1;
		}

		if (!empty($rolearray[$ur_role]))
		{
			return in_array($view_layout, $rolearray[$ur_role]);
		}
		else
		{
			return 0;
		}
	}

	/**
	 * getCurrencyExchange
	 *
	 * @param   STRING  $from_Currency  from_Currency
	 * @param   STRING  $to_Currency    contain orderid,to,from,and currency exchange rate
	 * @param   STRING  $amount         amount
	 *
	 * @return  VOID
	 */
	public function getCurrencyExchange($from_Currency = "USD", $to_Currency = "INR", $amount = 1)
	{
		$amount        = urlencode($amount);
		$from_Currency = urlencode($from_Currency);
		$to_Currency   = urlencode($to_Currency);
		$url           = "http://www.google.com/ig/calculator?hl=en&q = $amount$from_Currency=?$to_Currency";

		// 1.create a new cURL resource
		$ch            = curl_init();
		$timeout       = 0;

		// 2. set URL and other appropriate options
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		// Curl_setopt($ch,  CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

		// 3. grab URL and pass it to the browser
		$rawdata = curl_exec($ch);

		// 4. close cURL resource, and free up system resources
		curl_close($ch);
		$data = explode('"', $rawdata);
		$data = explode(' ', $data['3']);
		$var  = $data['0'];

		return round($var, 2);
	}

	/**
	 * This function add currency exchange info in order's extra field
	 *
	 * @param   ARRAY  $data  contain orderid,to,from,and currency exchange rate
	 *
	 * @return  VOID
	 */
	public function currencyExchangeMsg($data)
	{
		if (!empty($data))
		{
			// Load payment model
			$path = JPATH_SITE . '/components/com_quick2cart/model/payment.php';

			if (!class_exists('Quick2cartModelpayment'))
			{
				JLoader::load('Quick2cartModelpayment');
			}

			$Quick2cartModelpayment = new Quick2cartModelpayment;
			$order_id               = $Quick2cartModelpayment->extract_prefix($data['order_id']);

			// As we dont want to store( see next) in DB
			unset($data['order_id']);
			$comquick2cartHelper = new comquick2cartHelper;
			$q                   = "SELECT  `extra` FROM  `#__kart_orders` WHERE `id` =" . $order_id;
			$extraFieldData      = $comquick2cartHelper->appendExtraFieldData($data, $q, 1);
			$res                 = new stdClass;
			$res->id             = $order_id;
			$res->extra          = $extraFieldData;
			$db                  = Factory::getDBO();

			// Get previous data if exist
			if (!$db->updateObject('#__kart_orders', $res, 'id'))
			{
				echo $this->_db->stderr();

				return false;
			}
		}
	}

	/**
	 * THIS public function take orderid and array of data to be store in extra field of order table
	 *
	 * @param   array   $data   data to be store in extra field
	 * @param   STRING  $query  to get extra field data from DB
	 * @param   string  $key    string ,numeric 0 for payment_response and numeric 1 for currency_exchange, other than this will be used as key field
	 *
	 * @return json string  :: json_encoded extra field data
	 */
	public function appendExtraFieldData($data, $query, $key = 0)
	{
		$db = Factory::getDBO();

		// $q="SELECT  `extra` FROM  `#__kart_orders` WHERE `id` =".$order_id;
		$db->setQuery($query);
		$oldres = $db->loadResult();

		if (empty($oldres))
		{
			$olddata = array();
		}
		else
		{
			// Take already exist extra data
			$olddata = json_decode($oldres, true);
		}

		if (is_numeric($key) && $key == 1)
		{
			// Called from currecy exchange function
			$olddata['currency_exchange'] = $data;
		}
		elseif (is_numeric($key) && $key == 0)
		{
			// Mean we are going to save payment response msg
			$olddata['payment_response'] = $data;
		}
		elseif (!empty($key))
		{
			// Update the if key exist or add
			$olddata[$key] = $data;
		}

		return json_encode($olddata);
	}

	/**
	 *  IF store id is present then we have allow to view store releated order.
	 * 	else
	 * 	  checking order user_id and current logged user is same or not
	 *
	 * @param   INTEGER  $store_id  store_id.
	 * @param   INTEGER  $order_id  order_id.
	 *
	 * @return  STRING
	 */
	public function getStoreOrdereAuthorization($store_id, $order_id)
	{
		if (!empty($store_id))
		{
			$db    = Factory::getDBO();
			$query = "Select `order_item_id` FROM `#__kart_order_item` WHERE store_id=" . $store_id . "  AND order_id=" . $order_id;
			$db->setQuery($query);

			return $db->loadResult($query);
		}
	}

	/**
	 * check whether
	 * 1. authority to store
	 * 2.logged in
	 * 3 if coupon code is present then check first 2 conditon and also check whether coupon_id and store_id assocation is present or not
	 *
	 * @param   INTEGER  $store_id   store_id.
	 * @param   INTEGER  $coupon_id  coupon_id.
	 *
	 * @return  STRING
	 */
	public function createCouponAuthority($store_id, $coupon_id = 0)
	{
		$user = Factory::getUser();

		// If owner or has role then only return 1;

		// If logged in
		if ($user->id)
		{
			$db = Factory::getDBO();

			if (!empty($coupon_id))
			{
				$query = " select c.id
			FROM `#__kart_role` AS r LEFT JOIN `#__kart_coupon` AS c
			ON r.`store_id`=c.`store_id`
			WHERE c.id=" . $coupon_id . " AND r.`user_id`=" . $user->id . " AND  r.`store_id`=" . $store_id . " AND r.`role` < 3 Order by r.`role`";
			}
			else
			{
				$query = "Select id FROM `#__kart_role` WHERE `user_id`=" . $user->id . " AND  `store_id`=" . $store_id . " AND `role` < 3 Order by `role`";
			}

			$db->setQuery($query);

			return $db->loadResult($query);
		}

		return 0;
	}

	/**
	 *  According to commission % specified in component parameter,
	 *  fee/commission is calculated on total amount and added in kart_store table against respected store id
	 *
	 * @param   INTEGER  $orderid  orderid.
	 *
	 * @return  VOID
	 */
	/*public function updateStoreFee($orderid)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$db                  = Factory::getDBO();
		$pricedetails        = $comquick2cartHelper->getStoreProdPrice($orderid);

		if (!empty($pricedetails))
		{
			$params = ComponentHelper::getParams('com_quick2cart');
			$per    = $params->get('commission');

			foreach ($pricedetails as $key => $tprice)
			{
				$fee   = '';
				$fee   = ((float) $tprice['totalprice'] * $per) / 100;
				$query = "UPDATE  `#__kart_store` SET `fee`=`fee` + " . $fee . "  where `id`=" . $tprice['store_id'];
				$db->setQuery($query);
				$db->execute();
			}
		}
	}*/

	/**
	 * This function return array containing store id and SUM(final_product_price of all product against store_id)
	 *
	 * @param   INTEGER  $orderid  orderid.
	 *
	 * @return  OBJECT
	 */
	public function getStoreProdPrice($orderid)
	{
		$db    = Factory::getDBO();
		$query = "SELECT i.store_id, SUM( i.product_final_price ) as totalprice
			FROM  `#__kart_order_item` AS i
			LEFT JOIN  `#__kart_orders` AS o ON i.order_id = o.id
			WHERE order_id =  '" . $orderid . "'
			GROUP BY i.`store_id`
			ORDER BY  `store_id` ";
		$db->setQuery($query);

		return $prices = $db->loadAssocList();
	}

	/**
	 * getAllStoreDetails
	 *
	 * @return  array with  s.id,s.title,s.owner,u.firstname()CREATED BY (here storeid is aassociative index)
	 */
	public function getAllStoreDetails()
	{
		$db    = Factory::getDBO();
		$query = "SELECT DISTINCT (s.id), s.title, s.owner, u.username as firstname, s.vendor_id
		FROM #__kart_store AS s
		LEFT JOIN #__users AS u ON s.owner = u.id
		ORDER BY s.`id`";
		$db->setQuery($query);

		return $sdetails = $db->loadAssocList('id');
	}

	/**
	 * This public function give all store list detail with specific ROLE
	 *  if no parametes are passed then provide all store detail against user_id
	 *
	 * @return  OBJECT
	 */
	public function getStoreDetail()
	{
		$db      = Factory::getDBO();
		$user    = Factory::getUser();
		$where   = array();
		$where[] = " r.user_id=" . $user->id;
		$where   = !empty($where) ? " WHERE " . implode($where, " AND ") : "";

		$query = " select s.`id`, `owner`, `pincode`, `title`, `description`,`address`,
		 `phone`, `store_email`, `store_avatar`,`fee`, `live` AS published, `cdate`,
		 `mdate`, `extra`,company_name,vanityurl,r.role
			FROM `#__kart_role` AS r INNER JOIN `#__kart_store` AS s ON r.store_id=s.id
		" . $where;

		$db->setQuery($query);
		$authorized_stores = $db->loadObjectList();

		return $authorized_stores;
	}

	/**
	 * for editstore
	 *
	 * @param   INTEGER  $store_id  store_id.
	 *
	 * @return  OBJECT
	 */
	public function editstore($store_id)
	{
		$db = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__kart_store'));
		$query->where($db->quoteName('id') . ' = ' . (int) $store_id);
		$db->setQuery($query);

		return $db->loadObjectList();
	}

	/**
	 * for image upload
	 *
	 * @param   STRING   $file_field             file_field.
	 * @param   object   $img_dimensions_config  img_dimensions_config.
	 * @param   INTEGER  $upload_orig            upload_orig.
	 *
	 * @return  STRING
	 */
	public function imageupload($file_field, $img_dimensions_config, $upload_orig = '1')
	{
		require_once JPATH_SITE . '/components/com_quick2cart/helpers/media.php';

		// Create object of media helper class
		$media           = new qtc_mediaHelper;
		$params          = ComponentHelper::getParams('com_quick2cart');
		$app = Factory::getApplication();

		// Get uploaded media details

		// Orginal file name
		$file_name       = $_FILES[$file_field]['name'];

		if (empty($file_name))
		{
			return false;
		}

		// Convert name to lowercase
		$file_name       = File::makeSafe(strtolower($_FILES[$file_field]['name']));

		// Replace "spaces" with "_" in filename
		$file_name       = preg_replace('/\s/', '_', $file_name);
		$file_type       = $_FILES[$file_field]['type'];
		$file_tmp_name   = $_FILES[$file_field]['tmp_name'];
		$file_size       = $_FILES[$file_field]['size'];
		$file_error      = $_FILES[$file_field]['error'];
		$file_extension  = pathinfo($file_name, PATHINFO_EXTENSION);

		// Set error flag, if any error occurs set this to 1
		$error_flag      = 0;

		// Check for max media size allowed for upload
		$max_size_exceed = $media->check_max_size($file_size);

		if ($max_size_exceed)
		{
			$mediaType = explode("/", $file_type);
			$maxUploadSizeOfMedia = $params->get('max_size') . 'KB';

			if ($mediaType[0] == 'video')
			{
				$maxUploadSizeOfMedia = $params->get('max_video_file_size') . 'MB';
			}

			$msg = Text::sprintf('COM_QUICK2CART_FILE_SIZE_EXCEED_ERROR', $maxUploadSizeOfMedia);
			$app->enqueueMessage($msg, 'error');
			$error_flag  = 1;
		}

		if (!$error_flag)
		{
			// Detect media group type image/video/flash
			$media_type_group = $media->check_media_type_group($file_type, $file_extension);

			if (!empty($file_type))
			{
				if (!$media_type_group['allowed'])
				{
					$supportedMediaExtension = 'gif, jpeg, jpg, png';

					if ($params->get('video_gallery') && !empty($params->get('videoExtensions')))
					{
						$supportedMediaExtension = $supportedMediaExtension . ', ' . $params->get('videoExtensions');
					}

					$msg = JText::sprintf('COM_QUICK2CART_FILE_TYPE_NOT_ALLOWED', $supportedMediaExtension);
					$app->enqueueMessage($msg, 'error');
					$error_flag  = 1;
				}
			}

			if (!$error_flag)
			{
				$media_extension                      = $media->get_media_extension($file_name);

				// Upload original img
				$file_name_without_extension          = $media->get_media_file_name_without_extension($file_name);
				$timestamp                            = time();
				$original_file_name_without_extension = $file_name_without_extension . '_' . $timestamp;
				$original_file_name                   = $original_file_name_without_extension . '.' . $media_extension;

				// Always use constants when making file paths, to avoid the possibilty of remote file inclusion
				$fullPath                             = JPATH_SITE . '/images/quick2cart/';
				$relPath                              = 'images/quick2cart/';

				// If folder is not present create it
				if (!Folder::exists(JPATH_SITE . '/images/quick2cart'))
				{
					@mkdir(JPATH_SITE . '/images/quick2cart');
				}

				// $upload_path = $upload_path_folder.$original_file_name;

				// $image_upload_path_for_db. = $original_file_name;

				// Upload original img

				// Determine if resizing is needed for images

				// $adzone_media_dimnesions = $media->get_adzone_media_dimensions($adzone);

				if ($media_type_group['media_type_group'] == "image")
				{
					foreach ($img_dimensions_config as $config)
					{
						$media_dimnesions = new stdClass;
						$max_zone_width   = $params->get($config . '_width');
						$max_zone_height  = $params->get($config . '_height');

						switch ($config)
						{
							case 'small':
								$file_name_without_extension = $original_file_name_without_extension . "_S";
								break;
							case 'medium':
								$file_name_without_extension = $original_file_name_without_extension . "_M";
								break;
							case 'large':
								$file_name_without_extension = $original_file_name_without_extension . "_L";
								break;
							default:
								$file_name_without_extension = $original_file_name_without_extension;
								break;
						}

						$media_size_info = $media->check_media_resizing_needed($media_dimnesions, $file_tmp_name);
						$resizing        = 0;

						if ($media_size_info['resize'])
						{
							$resizing = 1;
						}

						switch ($resizing)
						{
							case 0:
								$new_media_width  = $media_size_info['width_img'];
								$new_media_height = $media_size_info['height_img'];

								// @TODO not sure abt this
								$top_offset       = 0;

								// @TODO not sure abt this
								$blank_height     = $new_media_height;

								break;
							case 1:
								$new_dimensions   = $media->get_new_dimensions($max_zone_width, $max_zone_height, 'auto');
								$new_media_width  = $new_dimensions['new_calculated_width'];
								$new_media_height = $new_dimensions['new_calculated_height'];
								$top_offset       = $new_dimensions['top_offset'];
								$blank_height     = $new_dimensions['blank_height'];
								break;
						}

						$colorR       = 255;
						$colorG       = 255;
						$colorB       = 255;
						$upload_image = $media->uploadImage(
							$file_field, $max_zone_width, $fullPath, $relPath,
							$colorR, $colorG, $colorB, $new_media_width, $new_media_height,
							$blank_height, $top_offset, $media_extension, $file_name_without_extension, $max_zone_height
						);
					}
				}
				elseif ($media_type_group['media_type_group'] == "video")
				{
					// As we skipped resizing for video , we will use zone dimensions
					$new_media_width  = $params->get('medium_width');
					$new_media_height = $params->get('medium_height');

					// @TODO not sure abt this
					$top_offset       = 0;
					$blank_height     = $new_media_height;

					$colorR       = 255;
					$colorG       = 255;
					$colorB       = 255;
					$upload_image = $media->uploadImage(
						$file_field, $new_media_width, $fullPath, $relPath,
						$colorR, $colorG, $colorB, $new_media_width, $new_media_height,
						$blank_height, $top_offset, $media_extension, $file_name_without_extension, $new_media_height
					);
				}

				// This should work for image case to moveing original image
				if ($upload_orig == '1' && $media_type_group['media_type_group'] == "image")
				{
					$upload_path = $fullPath . $original_file_name;

					if (!File::upload($file_tmp_name, $upload_path))
					{
						echo Text::_('COM_QUICK2CART_ERROR_MOVING_FILE');

						return false;
					}
				}

				return $original_file_name;
			}
		}

		return false;
	}

	/**
	 * for store avatar
	 *
	 * @param   STRING  $file_field  file_field.
	 * @param   STRING  $oldImgPath  oldImgPaths.
	 *
	 * @return  STRING
	 */
	public function uploadImage($file_field, $oldImgPath = "")
	{
		$db = Factory::getDBO();

		// Save uploaded image

		// Check the file extension is ok

		// $file_field="avatar";  // name of file field in from view eg <input type="file" name="avatar"  id="avatar" />
		$file_name             = $_FILES[$file_field]['name'];
		$media_info            = pathinfo($file_name);
		$uploadedFileName      = $media_info['filename'];
		$uploadedFileExtension = $media_info['extension'];
		$validFileExts         = explode(',', 'jpeg,jpg,png,gif');

		// Assume the extension is false until we know its ok
		$extOk                 = false;

		// Go through every ok extension, if the ok extension matches the file extension (case insensitive)

		// Then the file extension is ok

		foreach ($validFileExts as $key => $value)
		{
			if (preg_match("/$value/i", $uploadedFileExtension))
			{
				$extOk = true;
			}
		}

		if ($extOk == false)
		{
			echo Text::_('COM_QUICK2CART_INVALID_IMAGE_EXTENSION');

			return;
		}

		// The name of the file in PHP's temp directory that we are going to move to our folder
		$file_temp = $_FILES[$file_field]['tmp_name'];

		// For security purposes, we will also do a getimagesize on the temp file (before we have moved it

		// To the folder) to check the MIME type of the file, and whether it has a width and height
		$image_info = getimagesize($file_temp);

		// We are going to define what file extensions/MIMEs are ok, and only let these ones in (whitelisting), rather than try to scan for bad

		// Types, where we might miss one (whitelisting is always better than blacklisting)
		$okMIMETypes    = 'image/jpeg,image/pjpeg,image/png,image/x-png,image/gif';
		$validFileTypes = explode(",", $okMIMETypes);

		// If the temp file does not have a width or a height, or it has a non ok MIME, return
		if (!is_int($image_info[0]) || !is_int($image_info[1]) || !in_array($image_info['mime'], $validFileTypes))
		{
			echo Text::_('COM_QUICK2CART_INVALID_IMAGE_EXTENSION');

			return;
		}
		// Clean up filename to get rid of strange characters like spaces etc

		$file_name                = File::makeSafe($uploadedFileName);

		// Lose any special characters in the filename
		$file_name                = preg_replace("/[^A-Za-z0-9]/i", "-", $file_name);

		// Use lowercase
		$file_name                = strtolower($file_name);

		// Add timestamp to file name
		$timestamp                = time();
		$file_name                = $file_name . '_' . $timestamp . '.' . $uploadedFileExtension;

		// Always use constants when making file paths, to avoid the possibilty of remote file inclusion
		$upload_path_folder       = JPATH_SITE . '/images/quick2cart';
		$image_upload_path_for_db = 'images/quick2cart';

		// If folder is not present create it
		if (!Folder::exists($upload_path_folder))
		{
			@mkdir($upload_path_folder);
		}

		$upload_path = $upload_path_folder . "/" . $file_name;
		$image_upload_path_for_db .= "/" . $file_name;

		if (!File::upload($file_temp, $upload_path))
		{
			echo Text::_('COM_QUICK2CART_ERROR_MOVING_FILE');

			return false;
		}

		// DELETE OLD IMAGE IF EXIST
		if (!empty($oldImgPath))
		{
			$oldImgPath = JPATH_SITE . "/" . $oldImgPath;

			if (File::exists($oldImgPath))
			{
				$status2 = File::delete($oldImgPath);
			}
		}

		return $image_upload_path_for_db;
	}

	/**
	 * getRole
	 *
	 * @param   integer  $roleno  roleno.
	 *
	 * @return  void
	 */
	public function getRole($roleno)
	{
		$path = JPATH_SITE . "/components/com_quick2cart/authorizeviews.php";
		include $path;

		return $role[$roleno];
	}

	/**
	 * Return all store for which user is accessible.
	 *
	 * @param   INTEGER|NULL|STRING  $user_id  userid.
	 * @param   INTEGER|NULL         $state    paramenter = 1 then show published only, . => gives unpublished otherwise both
	 *
	 * @return  void|array
	 */
	public function getStoreIds($user_id = '', $state = '')
	{
		$db = Factory::getDBO();
		$app = Factory::getApplication();

		if (empty($user_id))
		{
			$user    = Factory::getUser();
			$user_id = $user->id;
		}

		$live = '';

		$query	= $db->getQuery(true);
		$query->select($db->quoteName(array('store_id', 'role', 'title')));
		$query->join('INNER', $db->quoteName('#__kart_store', 's') . 'ON' . $db->quoteName('r.store_id') . '=' . $db->quoteName('s.id'));
		$query->from($db->quoteName('#__kart_role', 'r'));
		$query->where($db->quoteName('user_id') . ' = ' . $user_id);

		if ($state != '')
		{
			$query->where($db->quoteName('s.live') . ' = ' . $state);
		}

		$query->where($db->quoteName('role') . ' != 3');
		$query->order($db->quoteName('role'));
		$query->order($db->quoteName('store_id'));
		$db->setQuery($query);

		return $db->loadAssocList();
	}

	/**
	 * getCouponDetail
	 *
	 * @param   integer  $coupon_code  coupon_code
	 *
	 * @since   2.2.2
	 *
	 * @return  Object list.
	 */
	public function getCouponDetail($coupon_code)
	{
		$db    = Factory::getDBO();
		$query = "select `value`,`val_type` as type from  `#__kart_coupon` where `code`='" . $coupon_code . "'";
		$db->setQuery($query);

		return $res = $db->loadAssoc();
	}

	/**
	 * syncOrderItems
	 *
	 * @param   MIXED    $orderitems  primary key of kart_items table.
	 * @param   integer  $user_id     id of user
	 * @param   integer  $order_id    order id
	 *
	 * @since   2.2.2
	 *
	 * @return  Object list.
	 */
	public function syncOrderItems($orderitems, $user_id, $order_id)
	{
		$final_order_price = 0;

		foreach ($orderitems as $item)
		{
			$finalOrderItemPrice = 0;

			if (!empty($item->params))
			{
				// Coupon coupon exist
				$c_code = json_decode($item->params, true);

				if (!empty($c_code['coupon_code']))
				{
					$path = JPATH_SITE . "/components/com_quick2cart/models/cartcheckout.php";

					if (!class_exists("Quick2cartModelcartcheckout"))
					{
						JLoader::register("Quick2cartModelcartcheckout", $path);
						JLoader::load("Quick2cartModelcartcheckout");
					}

					$Quick2cartModelcartcheckout = new Quick2cartModelcartcheckout;
					$valid = $Quick2cartModelcartcheckout->getcoupon($c_code['coupon_code'], $user_id, "order", $order_id);

					if (empty($valid))
					{
						unset($c_code['coupon_code']);
						$params = (!empty($c_code)) ? json_encode($c_code) : '';

						// INVALID COUPON
						$db    = Factory::getDBO();
						$query = " UPDATE  `#__kart_order_item` SET  `product_final_price`=`original_price` ,`params`='" . $params
								. "' WHERE  `order_item_id` =" . $item->order_item_id;
						$db->setQuery($query);

						try
						{
							$db->execute();
						}
						catch(\RuntimeException $e)
						{
							$this->setError($e->getMessage());
							return false;
						}
					}
					else
					{
						// VALID COUPON:UPDATE WITH NEW COP #20712
					}
				}
			}

			// Getting orderitems final price
			$db    = Factory::getDBO();
			$query = " SELECT  `product_final_price` FROM `#__kart_order_item` WHERE  `order_item_id` =" . $item->order_item_id;
			$db->setQuery($query);
			$finalOrderItemPrice = $db->loadResult();
			$final_order_price   = (int) $final_order_price + (int) $finalOrderItemPrice;
		}

		$comquick2cartHelper = new comquick2cartHelper;

		// Vm:commented bz : we r doing after release q2cv2.0
		// $comquick2cartHelper->AfterSyncUpdateOrderDetails($allOrderitemsPrice, $order_id);
	}

	/*
	public function AfterSyncUpdateOrderDetails($allOrderitemsPrice, $order_id)
	{
	 vm:update shipping val,taxval,order_tax_details,order_ship_details (jsondata) as we are using this detail on order detail view
	UPDATE ORDER original_amount
	if (!empty($allOrderitemsPrice) && !empty($order_id))
	{

	$db   =Factory::getDBO();
	$query=' UPDATE  `#__kart_orders` SET  `original_amount`='.$allOrderitemsPrice.' WHERE `id`='.$order_id;

	$db->setQuery($query);
	if (!$db->execute() )
	{
	echo $img_ERROR.Text::_("Unable to Alter #__kart_orders").$BR;
	echo $db->getErrorMsg();
	return FALSE;
	}
	}
	} */

	/**
	 * This public function  give product link.
	 *
	 * @param   integer  $item_id   primary key of kart_items table.
	 * @param   string   $linkType  Product link while displaying product or edit product.
	 * @param   integer  $absolute  Link type.
	 *
	 * @since   2.2.2
	 * @return  Object list.
	 */
	public function getProductLink($item_id, $linkType = 'detailsLink', $absolute = 0)
	{
		$helperobj = new comquick2cartHelper;
		$db        = Factory::getDBO();
		$query     = "SELECT `product_id`, `parent`,`category` FROM `#__kart_items` WHERE item_id=" . $item_id;
		$db->setQuery($query);
		$res  = $db->loadAssoc();
		$link = "";
		$uri  = Uri::getInstance();

		switch ($res["parent"])
		{
			case "com_content":
				if ($linkType == 'detailsLink')
				{
					if (JVERSION < '4.0.0')
					{
						require_once JPATH_SITE . '/components/com_content/helpers/route.php';
					}
					else
					{
						require_once JPATH_SITE . '/components/com_content/src/Helper/RouteHelper.php';
					}
					$query = 'SELECT a.id, '
							. ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'
							. ' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'
							. ' FROM #__content AS a '
							. ' INNER JOIN #__categories AS cc ON cc.id = a.catid' . ' WHERE a.id=' . $res["product_id"];

					$db->setQuery($query);
					$article = $db->loadObject();
					$link    = Route::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug), false);
				}
				elseif ($linkType == 'editLink')
				{
					$link = Route::_('index.php?option=com_content&task=article.edit&a_id=' . $res["product_id"] . '&return=' . base64_encode($uri), false);
				}

				break;
			case "com_zoo":
				if ($linkType == 'detailsLink')
				{
					$Itemid = $helperobj->getitemid('index.php?option=com_zoo&task=item');
					$link   = "index.php?option=com_zoo&task=item&item_id=" . $res["product_id"] . "&Itemid=" . $Itemid;
					$link   = Route::_($link, false);
				}
				elseif ($linkType == 'editLink')
				{
					$zooConfigFile = JPATH_ADMINISTRATOR . '/components/com_zoo/config.php';

					if (File::exists($zooConfigFile))
					{
						require_once $zooConfigFile;

						$zooApp = App::getInstance('zoo');
						$item   = $zooApp->table->item->get($res["product_id"]);

						// Get submission type of item
						$type   = $item->type;

						/*// Here is the exact hash for the record
						$hashRelated = $zooApp->submission->getSubmissionHash(1, $type, $res["product_id"]);

						@TODO - get and use submission_id insted of using hardcoded value 1
						Construct link as zoo
							$link = Route::_('index.php?option=com_zoo&view=submission&layout=submission&submission_id=1&type_id='
							. $type. '&item_id=' . $res["product_id"] . '&submission_hash=' . $hashRelated, false);*/

						$link = $zooApp->route->submission(
							$item->getApplication()->getItemEditSubmission(), $item->type, null, $item->id, 'itemedit'
						);
					}
				}
				break;

			case "com_k2":
				require_once JPATH_SITE . '/components/com_k2/helpers/route.php';
				$Itemid = $helperobj->getitemid('index.php?option=com_k2&view=item');

				if ($linkType == 'detailsLink')
				{
					$query = "SELECT a.id, a.alias, a.catid,
					b.alias as categoryalias
					FROM #__k2_items as a
					LEFT JOIN #__k2_categories AS b ON b.id = a.catid
					WHERE a.id = " . $res["product_id"];
					$db->setQuery($query);
					$k2item = $db->loadObject();
					$link   = K2HelperRoute::getItemRoute($k2item->id . ':' . urlencode($k2item->alias), $k2item->catid . ':' . urlencode($k2item->categoryalias));
				}
				elseif ($linkType == 'editLink')
				{
					$Itemid = $helperobj->getitemid('index.php?option=com_quick2cart&view=product');
					$link   = 'index.php?option=com_k2&view=item&layout=itemform&task=edit&cid=' . $res["product_id"] . '&Itemid=' . $Itemid;
				}

				$link = Route::_($link, false);

				break;

			case "com_flexicontent":
				$Itemid = $helperobj->getitemid('index.php?option=com_flexicontent&view=item');

				if ($linkType == 'detailsLink')
				{
					$link = 'index.php?option=com_flexicontent&view=item&id=' . $res["product_id"] . "&Itemid=" . $Itemid;
				}
				elseif ($linkType == 'editLink')
				{
					// @TODO - add catid, alias etc here
					$link = 'index.php?option=com_flexicontent&view=item&task=edit&id=' . $res["product_id"] . "&Itemid=" . $Itemid;
				}

				$link = Route::_($link, false);
			break;

			case "com_cobalt":
				if (File::exists(JPATH_ROOT . '/components/com_cobalt/api.php'))
				{
					require_once JPATH_ROOT . '/components/com_cobalt/api.php';

					if ($linkType == 'detailsLink')
					{
						$link = Route::_(Url::record($res["product_id"]), true, -1);
					}
					elseif ($linkType == 'editLink')
					{
						$link = Route::_(Url::edit($res["product_id"]), true, -1);
					}

					$link = str_replace('administrator/', '', $link);
				}
			break;

			default:
			case "com_quick2cart":

				if ($linkType == 'detailsLink')
				{
					$catpage_Itemid = $helperobj->getitemid('index.php?option=com_quick2cart&view=category&prod_cat=' . $res['category']);
					$link           = 'index.php?option=com_quick2cart&view=productpage&layout=default&item_id=' . $res["product_id"] . "&Itemid=" . $catpage_Itemid;
					$link           = Route::_($link, false);
				}
				elseif ($linkType == 'editLink')
				{
					$add_product_itemid = $helperobj->getitemid('index.php?option=com_quick2cart&view=product&layout=default');
					$link = 'index.php?option=com_quick2cart&view=product&layout=default&item_id=' . $res["product_id"] . '&Itemid=' . $add_product_itemid;
					$link  = Route::_($link, false);
				}
		}

		if ($absolute == 1)
		{
			$link = Uri::root() . substr($link, strlen(Uri::base(true)) + 1);
		}

		return $link;
	}

	/**
	 * getLineChartFormattedData
	 *
	 * @param   ARRAY  $data  data
	 *
	 * @return  Chart array
	 */
	public function getLineChartFormattedData($data)
	{
		$app        = Factory::getApplication();
		$backdate   = $app->getUserStateFromRequest('from', 'from', '', 'string');
		$todate     = $app->getUserStateFromRequest('to', 'to', '', 'string');
		$backdate   = !empty($backdate) ? $backdate : (date('Y-m-d', strtotime(date('Y-m-d') . ' - 30 days')));
		$todate     = !empty($todate) ? $todate : date('Y-m-d');
		$incomedata = "[
	";
		$ordersData = "[
	";
		$firstdate  = $backdate;

		// Will be
		$keydate    = "";

		foreach ($data as $key => $income)
		{
			$keydate = date('Y-m-d', strtotime($key));

			if ($firstdate < $keydate)
			{
				while ($firstdate < $keydate)
				{
					$incomedata .= " { period:'" . $firstdate . "', amount:0 },
				";
					$ordersData .= " { period:'" . $firstdate . "', orders:0 },
				";
					$firstdate = $this->add_date($firstdate, 1);
				}
			}

			$incomedata .= " { period:'" . $income->cdate . "', amount:" . $income->amount . "},
		";
			$ordersData .= " { period:'" . $income->cdate . "', orders:" . $income->orders_count . "},
		";
			$firstdate = $keydate;
		}

		// Vm: remaing date to last date
		while ($keydate < $todate)
		{
			$keydate = $this->add_date($keydate, 1);
			$incomedata .= " { period:'" . $keydate . "', amount:0 },
		";
			$ordersData .= " { period:'" . $keydate . "', orders:0 },
		";
		}

		$incomedata .= '
	]';
		$ordersData .= '
	]';
		$returnArray    = array();
		$returnArray[0] = $incomedata;
		$returnArray[1] = $ordersData;

		return $returnArray;
	}

	/**
	 * add_date
	 *
	 * @param   String  $givendate  givendate
	 * @param   INT     $day        day
	 * @param   INT     $mth        month
	 * @param   INT     $yr         year
	 *
	 * @return html
	 */
	public function add_date($givendate, $day = 0, $mth = 0, $yr = 0)
	{
		$cd      = strtotime($givendate);

		$newdate = date('Y-m-d H:i:s',
					mktime(
						date('H', $cd),
						date('i', $cd), date('s', $cd), date('m', $cd) + $mth, date('d', $cd) + $day, date('Y', $cd) + $yr
						)
					);

		// Convert to y-m-d format
		$newdate = date('Y-m-d H:i:s', strtotime($newdate));

		return $newdate;
	}

	/**
	 * This public function retur sotre detail
	 *
	 * @param   ARRAY   $sinfo      store info
	 * @param   INT     $viewall    view all param
	 * @param   INT     $storeid    id of the store
	 * @param   STRING  $spanclass  store info
	 *
	 * @return html
	 */
	public function getStoreDetailHTML($sinfo, $viewall = 1, $storeid = 0, $spanclass = "span12")
	{
		$storeHTML = "
			<div class=\"row-fluid " . $spanclass . " well \" >
			<fieldset >
			<legend> " . Text::_('QTC_VENDER_STORE_INFO');

		if ($viewall == 0)
		{
			// Route::_('index.php?option=com_quick2cart&view=orders&layout=mycustomer'),'_self'
			if (!empty($storeid))
			{
				$qtc_icon_edit = " icon-pencil-2 ";
				$storeHTML .= "<button type='button' class='btn btn-info btn_margin' style=\"float:right;\" onclick=\"window.open('"
				. Route::_("index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id = $storeid")
				. "')\" >
						<i class='con-user icon-white " . $qtc_icon_edit . "'></i>"
						. Text::_('SA_EDIT')
						. "</button>";
			}
		}

		$storeHTML .= "
			</legend>
			<table class='table table-condensed adminlist ' >
			<tbody>
				<tr>
					<td> " . Text::_('VENDER_TITLE') . "</td>
					<td> " . $sinfo['title'] . " </td>
				</tr>
				<tr>";

		if ($viewall == 1)
		{
			$storeHTML .= "<td> " . Text::_('VENDER_DESCRIPTION') . " </td>
				<td> " . $sinfo['description'] . " </td>
			</tr>
			<tr>
				<td> " . Text::_('COMPANY_NAME') . " </td>
				<td>" . $sinfo['company_name'] . "</td>
			</tr>";
		}

		$storeHTML .= "
			<tr>
				<td> " . Text::_('VENDER_EMAIL') . "</td>
				<td>" . $sinfo['store_email'] . "</td>
			</tr>
			<tr>
				<td> " . Text::_('VENDER_ADDRESS') . "</td>
				<td>" . $sinfo['address'] . "</td>
			</tr>
			<tr>
				<td> " . Text::_('VENDER_PHONE') . "</td>
				<td>" . $sinfo['phone'] . "</td>
			</tr>
			";

		if ($viewall == 1)
		{
			$storeHTML .= "
			<tr>
				<td> " . Text::_('STORE_VANITY_URL') . "</td>
				<td>" . $sinfo['header'] . "</td>
			</tr>";
		}

			return $storeHTML .= "</tbody></table></fieldset></div> ";
	}

	/**
	 * Checkif provided item is featured
	 *
	 * @param   INTEGER  $item_id  ID of an item
	 *
	 * @return  STRING
	 *
	 * @since   2.2.5
	 */
	public function isFeatured($item_id)
	{
		$db    = Factory::getDBO();
		$query = 'SELECT featured FROM `#__kart_items` WHERE `item_id` = ' . $item_id;
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result = (empty($result)) ? 0 : $result;
	}

	/**
	 * THIS public function return option list where category==q2c
	 *
	 * @param   INTEGER  $getOptionsOnly  only options
	 * @param   INTEGER  $onlyparents     only parents
	 *
	 * @return  This function return category name
	 *
	 * @since   2.2.5
	 */
	public function getQ2cCats($getOptionsOnly = 0, $onlyparents = 1)
	{
		$db     = Factory::getDBO();
		$parent = "";

		if (!empty($onlyparents))
		{
			$parent = " && parent_id=1 ";
		}

		$query = "SELECT id,title FROM #__categories WHERE extension='com_quick2cart' " . $parent;
		$db->setQuery($query);
		$categories = $db->loadObjectList();

		if (empty($getOptionsOnly))
		{
			$options[] = HTMLHelper::_('select.option', '', Text::_('QTC_PROD_SEL_CAT'));
		}

		foreach ($categories as $cat_obj)
		{
			$options[] = HTMLHelper::_('select.option', $cat_obj->id, $cat_obj->title);
		}

		return $options;
	}

	/**
	 * Get cat name
	 *
	 * @param   INTEGER  $catid  categoru id
	 *
	 * @return  String  This function return category name
	 *
	 * @since   2.2.5
	 */
	public function getCatName($catid)
	{
		if (!empty($catid))
		{
			$db    = Factory::getDBO();
			$query = "SELECT title FROM #__categories WHERE id=" . $catid;
			$db->setQuery($query);

			return $db->loadResult();
		}
	}

	/**
	 * This function return category detail
	 *
	 * @param   INT     $catid      The cat id whose child categories are to be taken
	 *
	 * @param   STRING  $extension  The extension whose cats are to be taken
	 *
	 * @return  array
	 *
	 * @since   1.0.0
	 */
	public static function getCatDetail($catid, $extension = 'com_quick2cart')
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select("id,title,`parent_id`,`path`")
		->from('#__categories')
		->where(" extension='" . $extension . "'")
		->where(" id=" . $catid);
		$db->setQuery($query);

		return $db->loadAssoc();
	}

	/**
	 *  get Joomla categories
	 *
	 * @param   INTEGER  $catid               categoru id
	 * @param   INTEGER  $onchangeSubmitForm  WHETHER WE HAVE Submit for or not 1:submit (default)  0:Dont submit
	 * @param   STRING   $name                Select box name
	 * @param   STRING   $class               class to be add
	 * @param   INTEGER  $getOptionsOnly      if set give only options
	 *
	 * @return  This public function return category name
	 *
	 * @since   2.2.5
	 */
	public function getQ2cCatsJoomla($catid = '', $onchangeSubmitForm = 1, $name = 'prod_cat', $class = '', $getOptionsOnly = 0)
	{
		$options         = array();
		$options[]       = HTMLHelper::_('select.option', '', Text::_('QTC_PROD_SEL_CAT'));

		// Static public function options($extension, $config = array('filter.published' => array(0,1)))
		$qtc_cat_options = HTMLHelper::_('category.options', 'com_quick2cart', array('filter.published' => array(1)));

		foreach ($qtc_cat_options as $k => $catDetails)
		{
			$catDetails->text = Text::_(trim($catDetails->text));
		}

		if ($getOptionsOnly == 1)
		{
			return $qtc_cat_options;
		}

		$cats               = array_merge($options, $qtc_cat_options);
		$onchangeSubmitForm = !empty($onchangeSubmitForm) ? 'onchange="document.adminForm.submit();"' : '';
		$classAndOther      = "' class=\" form-select q2c_product_category_select " . $class . "\" " . $onchangeSubmitForm . "'";
		$dropdown           = HTMLHelper::_('select.genericlist', $cats, $name, $classAndOther, 'value', 'text', $catid);

		return $dropdown;
	}

	/**
	 * check if logged in user has special access
	 *
	 * @return  string
	 *
	 * @since   2.2.5
	 */
	public function isSpecialAccess()
	{
		$user           = Factory::getUser();
		$special_access = 0;

		if (isset($user->groups['8']) || isset($user->groups['7'])
			|| isset($user->groups['Super Users']) || isset($user->groups['Administrator'])
			|| isset($user->groups['Super Users']) || isset($user->groups['Administrator']))
		{
			$special_access = 1;
		}

		return $special_access;
	}

	/**
	 * check if provided image is present
	 *
	 * @param   STRING  $imgname  image name
	 *
	 * @return  string
	 *
	 * @since   2.2.5
	 */
	public function isValidImg($imgname)
	{
		$img = '';

		if (!empty($imgname))
		{
			$media           = new qtc_mediaHelper();
			$qtcParams       = ComponentHelper::getParams('com_quick2cart');
			$media_extension = $media->get_media_extension($imgname);
			$imgfilepath     = '';

			$imageSupportedExtension = array('gif', 'png', 'pjpeg', 'jpeg', 'jpg');
			$videoSupportedExtension = explode(",", $qtcParams->get('videoExtensions'));

			if (in_array($media_extension, $imageSupportedExtension))
			{
				$img         = Uri::root() . 'images/quick2cart/' . $imgname;
				$imgfilepath = JPATH_SITE . '/images/quick2cart/' . $imgname;
			}
			elseif (in_array($media_extension, $videoSupportedExtension))
			{
				$img         = Uri::root() . 'images/quick2cart/vids/' . $imgname;
				$imgfilepath = JPATH_SITE . '/images/quick2cart/vids/' . $imgname;
			}

			if (!File::exists($imgfilepath))
			{
				$img = '';
			}
		}

		return $img;
	}

	/**
	 * populate the Jomsocial activity table
	 *
	 * @param   INT     $actor    actorid
	 * @param   INT     $target   targetid
	 * @param   STRING  $title    title of the activity
	 * @param   STRING  $content  content of the activity
	 * @param   STRING  $api      type of the notification
	 * @param   INT     $cid      optional paramas
	 *
	 * @return  void
	 *
	 * @since   2.2.5
	 */
	public function addJSstream($actor, $target, $title, $content, $api, $cid)
	{
		$com_core_file = JPATH_SITE . '/components/com_community/libraries/core.php';

		if (File::exists($com_core_file))
		{
			require_once $com_core_file;
			$act          = new stdClass;
			$act->cmd     = 'quick2cart.write';
			$act->actor   = $actor;

			// No target
			$act->target  = 0;
			$act->title   = $title;
			$act->content = $content;
			$act->app     = 'quick2cart' . '.' . $api;
			$act->cid     = $cid;
			CActivityStream::add($act);
		}
	}

	/**
	 * push into the Jomsocial notification table
	 *
	 * @param   INT     $from     actorid
	 * @param   INT     $to       targetid
	 * @param   STRING  $content  notification message
	 * @param   STRING  $cmd      commond to be used
	 * @param   STRING  $type     type of the notification
	 * @param   ARRAY   $params   optional paramas
	 *
	 * @return  void
	 *
	 * @since   2.2.5
	 */
	public function addJSnotify($from, $to, $content, $cmd = '', $type = '', $params = '')
	{
		$com_core_file = JPATH_SITE . '/components/com_community/libraries/core.php';

		if (File::exists($com_core_file))
		{
			require_once $com_core_file;
			$model = CFactory::getModel('Notification');
			$model->add($from, $to, $content, $cmd, $type, $params);
		}
	}

	/**
	 * Get Component Menu Alias
	 *
	 * @param   STRING  $link  menu link
	 *
	 * @return  html string
	 *
	 * @since   2.2.5
	 */
	public function getComponentMenuAlias($link)
	{
		$alias     = '';
		$db        = Factory::getDBO();
		$query     = "SELECT `alias` FROM #__menu WHERE link LIKE '%" . $link . "%' AND published = 1 LIMIT 1";
		$db->setQuery($query);
		$alias = $db->loadResult();

		return $alias;
	}

	/**
	 * check if myltivendor if ON/OFF
	 *
	 * @return  html string
	 *
	 * @since   2.2.5
	 */
	public function isMultivenderOFF()
	{
		$params             = ComponentHelper::getParams('com_quick2cart');
		$multivendor_enable = $params->get('multivendor');

		if (empty($multivendor_enable))
		{
			$qtc_back = QTC_ICON_BACK;

			$html = "<div class='techjoomla-bootstrap' >" .
						"<div class='row'>" .
							"<div class='col-md-12'>" .
								"<button type='button'  title='" . Text::_('COM_QUICK2CART_BACK') .
								"' class='btn btn-sm btn-primary pull-right' onclick='javascript:history.back();'>
									<i class='" . $qtc_back . " icon-white'></i>&nbsp;" . Text::_('QTC_BACK_BTN') . 
								"</button>" .
							"</div>" .
							"<div class='col-md-12 mt-2'>" .
								"<div class='well' >" .
									"<div class='alert alert-error alert-danger'>" .
										"<span >" . Text::_('QTC_MULTIVENDER_OFF_MSG') . " " .
										"</span>" .
									"</div>" .
								"</div>" .
							"</div>" .
						"</div>" .
					"</div>";

			return $html;
		}
		else
		{
			return 0;
		}
	}

	/**
	 * This public function return super user id
	 *
	 * @return  string
	 *
	 * @since   2.2.5
	 */
	public function getSuperUserId()
	{
		$db    = Factory::getDBO();
		$query = 'SELECT `user_id` FROM `#__user_usergroup_map` WHERE `group_id`=8 ORDER BY `user_id`';
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * This function used to get the name of the user
	 *
	 * @param   INT  $userid  ID of the user
	 *
	 * @return  string
	 *
	 * @since   2.2.5
	 */
	public function getUserName($userid)
	{
		$db    = Factory::getDBO();
		$query = 'SELECT `username` FROM `#__users` WHERE `id`=' . $userid;
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * This function used to load mentioned QTC classes
	 *
	 * @param   STRING  $path       Path of the class
	 * @param   STRING  $classname  Class name
	 *
	 * @return  Object|boolean
	 *
	 * @since   2.2.5
	 */
	public function loadqtcClass($path, $classname)
	{
		if (!class_exists($classname))
		{
			JLoader::register($classname, $path);
			JLoader::load($classname);
		}

		return new $classname;
	}

	/**
	 * This function used to push activities
	 *
	 * @param   ARRAY  $contentdata  Dtaa tu be pushed in activity
	 *
	 * @return  boolean
	 *
	 * @since   2.2.5
	 */
	public function pushtoactivitystream($contentdata)
	{
		jimport('activity.integration.stream');
		jimport('activity.socialintegration.profiledata');

		// @MUNDHE please check if this is written correct
		if (File::exists(JPATH_SITE . '/administrator/components/com_easysocial/includes/foundry.php'))
		{
			return;
		}

		$actor_id                  = $contentdata['user_id'];
		$integration_option        = $contentdata['integration_option'];
		$act_description           = $contentdata['act_description'];
		$act_type                  = '';
		$act_subtype               = '';
		$act_link                  = '';
		$act_title                 = '';
		$act_access                = 0;

		$act    = new activityintegrationstream;
		$result = $act->pushActivity($actor_id, $act_type, $act_subtype, $act_description, $act_link, $act_title, $act_access, $integration_option);

		if (!$result)
		{
			return false;
		}

		return true;
	}

	/**
	 * This function returns the ourder id of an item
	 *
	 * @param   INT  $order_item_id  Order id of the item
	 *
	 * @return  string
	 *
	 * @since   2.2.5
	 */
	public function getOrderId($order_item_id)
	{
		$db    = Factory::getDBO();
		$query = 'SELECT `order_id` FROM `#__kart_order_item` WHERE `order_item_id`=' . $order_item_id;
		$db->setQuery($query);

		return $db->loadResult();
	}

	/**
	 * This function return the country name.
	 *
	 * @param   INT  $countryId  Id of the country
	 *
	 * @return  string
	 *
	 * @since   2.2.5
	 */
	public function getCountryName($countryId)
	{
		if (is_numeric($countryId))
		{
			$db    = Factory::getDBO();
			$query = "SELECT `country` FROM `#__tj_country` where id=" . $countryId;
			$db->setQuery($query);
			$rows = $db->loadResult();

			return $rows;
		}

		return '';
	}

	/**
	 * This function return the state name.
	 *
	 * @param   INT  $stateId  Id of the state
	 *
	 * @return  string
	 *
	 * @since   2.2.5
	 */
	public function getStateName($stateId)
	{
		if (is_numeric($stateId))
		{
			$db    = Factory::getDBO();
			$query = "SELECT `region` FROM `#__tj_region` where id=" . $stateId;
			$db->setQuery($query);
			$rows = $db->loadResult();

			return $rows;
		}

		return '';
	}

	/**
	 * This function return array of js files which is loaded from tjassesloader plugin.
	 *
	 * @param   array  &$jsFilesArray                  Js file's array.
	 * @param   array  &$firstThingsScriptDeclaration  javascript to be declared first.
	 *
	 * @return  void
	 *
	 * @since   2.2.5
	 */
	public function getQuick2cartJsFiles(&$jsFilesArray, &$firstThingsScriptDeclaration)
	{
		$app      = Factory::getApplication();
		$document = Factory::getDocument();
		$input    = $app->input;
		$option   = $input->get('option', '');
		$view     = $input->get('view', '');
		$layout   = $input->get('layout', '');

		$ccks   = array(
			'com_content',
			'com_k2',
			'com_flexicontent',
			'com_zoo',
			'com_seblod'
		);

		// Load css files
		$comparams       = ComponentHelper::getParams('com_quick2cart');
		$currentBSViews  = $comparams->get('bootstrap_version', "bs3");
		$laod_boostrap   = $comparams->get('qtcLoadBootstrap', 1);
		$popupStyleOnBuy = $comparams->get('popup_buynow', 1);

		if ($popupStyleOnBuy == 2)
		{
			HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/toastr.min.css');
			HTMLHelper::_('script', 'components/com_quick2cart/assets/js/toastr.min.js');
		}

		// Load bootstrap.min.js before loading other files
		if (!$app->isClient('administrator'))
		{
			if ($currentBSViews == "bs3")
			{
				// Load Css
				if (!empty($laod_boostrap))
				{
					HTMLHelper::_('stylesheet', 'media/techjoomla_strapper/bs3/css/bootstrap.min.css');
				}

				// Get plugin 'relatedarticles' of type 'content'
				$plugin = PluginHelper::getPlugin('system', 'qtc_sys');

				// Check if plugin is enabled
				if ($plugin)
				{
					// Get plugin params
					$pluginParams = new Registry($plugin->params);
					$load = $pluginParams->get('loadBS3js');

					if (!empty($load))
					{
						$jsFilesArray[] = 'media/techjoomla_strapper/bs3/js/bootstrap.min.js';
					}
				}
			}
			elseif ($currentBSViews == "bs2")
			{
				if (!empty($laod_boostrap))
				{
					HTMLHelper::_('stylesheet', 'media/jui/css/bootstrap.min.css');
				}
			}
		}

		// Backend Js files
		if ($app->isClient('administrator'))
		{
			if ($option == "com_quick2cart")
			{
				// Load the view specific js
				switch ($view)
				{
					// @TODO - get rid off two auto.js files
					// Admin coupon view
					case "vendor":
						if ($layout == "createstore")
						{
							$jsFilesArray[] = 'components/com_quick2cart/assets/js/qtc-store-setup.js';
						}
					break;

					case "product":
						if ($layout == "new")
						{
							$jsFilesArray[] = 'components/com_quick2cart/assets/js/qtc-store-setup.js';
						}
					break;

					case "coupon":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/jquery-ui-1.10.4.custom.min.js';
						$jsFilesArray[] = 'administrator/components/com_quick2cart/assets/js/auto.js';
					break;
					case "promotion":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/promotion.js';
					break;
					case "dashboard":
						// Morris chart js files
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/raphael.min.js';
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/morris.min.js';
						break;
				}
				// Load *required quick2cart js
				$jsFilesArray[] = 'components/com_quick2cart/assets/js/order.js';
				$jsFilesArray[] = 'administrator/components/com_quick2cart/assets/js/adminkart.js';
			}
			elseif (in_array($option, $ccks))
			{
				// Load *required quick2cart js
				$jsFilesArray[] = 'components/com_quick2cart/assets/js/order.js';
			}
		}
		// Frontend Js files
		else
		{
			// Frontend Js files
			if ($option == "com_quick2cart")
			{
				// Load the view specific js
				switch ($view)
				{
					case "cart":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/qtc_steps.js';
						break;
					case "product":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/qtc-store-setup.js';
						break;
					case "cartcheckout":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/createorder.js';
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/fuelux2.3loader.min.js';
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/qtc_steps.js';
						break;

					// @TODO - get rid off two auto.js files
					// frontend

					case "coupon":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/jquery-ui-1.10.4.custom.min.js';
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/auto.js';
						break;
					case "couponform":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/jquery-ui-1.10.4.custom.min.js';
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/auto.js';
						break;

					case "productpage":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/jquery.swipebox.min.js';
						break;

					case "vendor":
						if ($layout == "cp")
						{
							// Morris chart js files
							$jsFilesArray[] = 'components/com_quick2cart/assets/js/raphael.min.js';
							$jsFilesArray[] = 'components/com_quick2cart/assets/js/morris.min.js';
						}
						break;

					case "createorder":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/createorder.js';
						break;

					case "promotion":
						$jsFilesArray[] = 'components/com_quick2cart/assets/js/promotion.js';
						break;
				}
			}
			// Load *required quick2cart js
			$jsFilesArray[] = 'components/com_quick2cart/assets/js/order.js';
			$jsFilesArray[] = 'components/com_quick2cart/assets/js/masonry.pkgd.min.js';
		}

		$reqURI = Uri::root();

		// If host have wwww, but Config doesn't. && No 'Access-Control-Allow-Origin'
		if (isset($_SERVER['HTTP_HOST']))
		{
			if ((substr_count($_SERVER['HTTP_HOST'], "www.") != 0) && (substr_count($reqURI, "www.") == 0))
			{
				$reqURI = str_replace("://", "://www.", $reqURI);
			}
			elseif ((substr_count($_SERVER['HTTP_HOST'], "www.") == 0) && (substr_count($reqURI, "www.") != 0))
			{
				// Host do not have 'www' but Config does
				$reqURI = str_replace("www.", "", $reqURI);
			}
		}

		if (!defined('Q2C_IS_VARIABLE_DECLARED'))
		{
			// Defind first thing script declaration.
			$loadFirstDeclarations = " var qtc_token = '" . Session::getFormToken() . "';
			var qtc_base_url = '" . $reqURI . "';";
			$firstThingsScriptDeclaration[] = $loadFirstDeclarations;
			define('Q2C_IS_VARIABLE_DECLARED', "YES");
		}

		return $jsFilesArray;
	}

	/**
	 * This define the lanugage contant which you have use in js file.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public static function getLanguageConstantForJs()
	{
		$app    = Factory::getApplication();
		$jinput = $app->input;
		$view   = $jinput->get("view");

		// For Product Page
		Text::script('COM_QUICK2CART_COULD_NOT_CHANGE_CART_DETAIL_NOW', true);
		Text::script('QTC_SKU_EXIST', true);
		Text::script('COM_QUICK2CARET_LOT_VALUE_SHOULDNOT_BE_ZERO', true);
		Text::script('COM_QUICK2CARET_SLAB_MIN_QTY', true);
		Text::script('COM_QUICK2CARET_SLAB_SHOULD_BE_MULT_MIN_QTY', true);
		Text::script('COM_QUICK2CARET_SLAB_SHOULD_BE_MULT_MIN_QTY', true);
		Text::script('QTC_ENTER_POSITIVE_ORDER', true);
		Text::script('QTC_REMOVE_MORE_OPTION_TITLE', true);
		Text::script('COM_QUICK2CART_CHANGE_STOCKABLE_ATTRIBUTE_ALERT', true);
		Text::script('COP_NOT_ACCEPTABLE_ENTERY', true);
		Text::script('COM_QUICK2CART_SHIPPING_ADDRESS_ERROR_MSG', true);
		Text::script('COM_QUICK2CART_SELECT_SHIPPING_ADDRESS_ERROR_MSG', true);
		Text::script('COM_QUICK2CART_SELECT_BILLING_ADDRESS_ERROR_MSG', true);
		Text::script('COM_QUICK2CART_SELECT_CLIENT_ERROR_MSG', true);
		Text::script('COM_QUICK2CART_CUSTOMER_ADDRESS_DELETE_MSG', true);
		Text::script('COM_QUICK2CART_DELETE_ADDRESS', true);
		Text::script('COM_QUICK2CART_CREATE_ORDER_SELECT_ADDRESS', true);
		Text::script('COM_QUICK2CART_SELECT_ADDRESS', true);
		Text::script('COM_Q2C_REMOVE_TOOLTIP', true);
		Text::script('QTC_MIN_LIMIT_MSG', true);
		Text::script('QTC_MAX_LIMIT_MSG', true);
		Text::script('COM_QUICK2CART_CREATE_ORDER_ORDER_SUCCESS_MESSAGE', true);
		Text::script('QTC_U_R_SURE_TO_REMOVE_COP', true);
		Text::script('QTC_ENTER_NUMERICS', true);
		Text::script('COM_QUICK2CART_PROMOTION_CONDITION_IN', true);
		Text::script('COM_QUICK2CART_PROMOTION_CONDITION_IS', true);
		Text::script('COM_QUICK2CART_PROMOTION_CONDITION_REMOVE_QUANTITY_INFO', true);
		Text::script('COM_QUICK2CART_DATES_INVALID', true);
		Text::script('COM_QUICK2CART_USES_INVALID', true);
		Text::script('COM_QUICK2CART_PROMOTION_CONDITION_SELECT_STORE_ALERT', true);
		Text::script('COM_QUICK2CART_ADD_CONDITION', true);
		Text::script('JGLOBAL_VALIDATION_FORM_FAILED', true);
		Text::script('COM_QUICK2CART_NO_ADDRESS_ERROR', true);
		Text::script('COM_QUICK2CART_PROMOTION_CONDITION_MSG', true);
		Text::script('COM_QUICK2CART_DISCOUNT_VALUE_ERROR', true);
		Text::script('COM_QUICK2CRT_REMOVE_TOOLTIP', true);
		Text::script('COM_QUICK2CART_PROMOTION_PRODUCT_QUANTITY_TEXT', true);
		Text::script('COM_QUICK2CART_CHECK_USER_PRIVACY_TERMS', true);
		Text::script('COM_QUICK2CART_CUSTOMER_ADDRESS_ADD_MSG', true);
		Text::script('COM_QUICK2CART_CUSTOMER_ADDRESS_UPDATE_MSG', true);
		Text::script('COM_QUICK2CART_CUSTOMER_ADDRESS_WRONG_INPUT', true);
		Text::script('COM_QUICK2CART_NOT_ALLOWED_TO_CHANGE_SHIPPING_ADDRESS', true);
		Text::script('COM_QUICK2CART_CAPTCHA_ERROR_MSG', true);
		Text::script('COM_QUICK2CART_BLANK_CAPTCHA_MSG', true);
		Text::script('QTC_MISSING_FIELD', true);
		Text::script('QTC_INVALID_EMAIL_ADDRESS', true);
		Text::script('QTC_INVALID_CONTACT_NO', true);
		Text::script('QTC_INVALID_EMAIL_BODY', true);
		Text::script('QTC_ORDER_IS_REPEATED', true);
		Text::script('QTC_NOT_ACCEPTABLE_FORM', true);
		Text::script('COM_QUICK2CART_NEW_USER_CREATED_SUCCESSFULLY', true);
		Text::script('COM_QUICK2CART_UNABLE_TO_CREATE_USER_BZ_OF', true);
		Text::script('JLIB_DATABASE_ERROR_EMAIL_INUSE', true);
		Text::script('COM_QUICK2CART_SELECT_PAYMENT_OPTION', true);
		Text::script('COM_QUICK2CART_REGISTRATION_PASSWORD_MATCH_ERROR', true);
		Text::script('COM_QUICK2CART_ITEM_LEVEL_SHIP_METHODS_RESTRICTED', true);
		Text::script('COM_QUICK2CART_FILE_TYPE_NOT_ALLOWED', true);
		Text::script('COM_QUICK2CARET_ORIGINAL_PRICE_LESS_THAN_DISCOUNT_PRICE', true);
		Text::script('QTC_JERROR_ALERTNOAUTHORTODELETE', true);
		Text::script('COM_QUICK2CART_ERROR_UPDATE',true);
		Text::script('COM_QUICK2CART_SERVER_ERROR',true);
		Text::script('COM_QUICK2CART_LOGIN_ALERT',true);

		switch ($view)
		{
			case "products" || "product":
				Text::script('COM_QUICK2CART_ADD_PROD_SEL_ATTRIBUTE_OPTION', true);
				Text::script('COM_QUICK2CART_ADD_PROD_GATTRIBUTE_OPTION_ALREADY_PRESENT', true);
			break;
		}
	}

	/**
	 * This function return plugin name from plugin params.
	 *
	 * @param   string  $plgname  Plugin name.
	 * @param   string  $type     Plugin type eg payment.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getPluginName($plgname, $type = 'payment')
	{
		if (empty($plgname))
		{
			return $plgname;
		}

		$plugin = PluginHelper::getPlugin($type, $plgname);

		if (empty($plugin))
		{
			return $plgname;
		}

		$params = json_decode($plugin->params);

		if (!empty($params->plugin_name))
		{
			$plgname = $params->plugin_name;
		}

		return $plgname;
	}

	/**
	 * This function return basic site info, so that it can be used in invoice.
	 *
	 * @since   2.2
	 * @return   null
	 */
	public function getSiteInvoiceInfo()
	{
		$app    = Factory::getApplication();
		$params = ComponentHelper::getParams('com_quick2cart');

		$invoiceData                  = array();
		$invoiceData['companyName']   = $app->get('fromname');
		$invoiceData['address']       = $params->get('mainSiteAdress', '', 'RAW');
		$invoiceData['contactNumber'] = '';
		$invoiceData['fax']           = '';
		$invoiceData['email']         = $app->get('mailfrom');
		$invoiceData['vat_num']       = $params->get('vat_num');

		return $invoiceData;
	}

	/**
	 * To override the icon set for backend / front end view. Check whether icon define file is add in template.
	 * If added then use override file else use compoent's file
	 *
	 * @param   string  $seachLoc  Where to search.
	 *
	 * @return  mixed  The escaped value.
	 *
	 * @since   12.2
	 */
	public static function defineIcons($seachLoc = 'SITE')
	{
		$app         = Factory::getApplication();
		$location    = ($seachLoc == 'SITE') ? JPATH_SITE : JPATH_ADMINISTRATOR;
		$iconDefPath = $location . '/templates/' . $app->getTemplate() . '/html/com_quick2cart/defines.php';

		if (File::exists($iconDefPath))
		{
			require_once $iconDefPath;

			return;
		}

		require_once JPATH_SITE . '/components/com_quick2cart/defines.php';
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * If escaping mechanism is either htmlspecialchars or htmlentities, uses
	 * {@link $_encoding} setting.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 *
	 * @since   12.2
	 */
	public function escape($var)
	{
		$charset = ENT_COMPAT;
		$escape  = 'htmlspecialchars';

		return call_user_func($escape, $var);
	}

	/**
	 * Get sites/administrator default template
	 *
	 * @param   mixed  $client  0 for site and 1 for admin template
	 *
	 * @return  json
	 *
	 * @since   1.5
	 */
	public function getSiteDefaultTemplate($client = 0)
	{
		try
		{
			$db    = Factory::getDBO();

			// Get current status for Unset previous template from being default
			// For front end => client_id=0
			$query = $db->getQuery(true)
						->select('template')
						->from($db->quoteName('#__template_styles'))
						->where('client_id=' . $client)
						->where('home=1');
			$db->setQuery($query);

			return $db->loadResult();
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return '';
		}
	}

	/**
	 * Method to get allow rating to bought the product user
	 *
	 * @param   string  $option  component name. eg quick2cart for component com_quick2cart etc.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public static function isComponentEnabled($option)
	{
		$status = 0;

		if ($option)
		{
			// Load lib
			if (File::exists(JPATH_ROOT . '/components/com_' . $option . '/' . $option . '.php'))
			{
				if (ComponentHelper::isEnabled('com_' . $option, true))
				{
					$status = 1;
				}
			}
		}

		return $status;
	}

	/**
	 * Method to get maximum and minimum price range for price filter
	 *
	 * @return price range
	 *
	 * @since 2.5
	 */
	public function getFilterPriceRange()
	{
		$currency = $this->getCurrencySession();
		$jinput   = Factory::getApplication()->input;
		$prod_cat = $jinput->get('prod_cat', '0', 'int');
		$db       = Factory::getDbo();
		$query    = $db->getQuery(true);

		$selectQuery = "MAX(CASE WHEN (bc.discount_price IS NOT NULL) THEN bc.discount_price ELSE a.price END) as max,
		MIN(CASE WHEN (bc.discount_price IS NOT NULL) THEN bc.discount_price ELSE a.price END) as min";

		$query->select($selectQuery);
		$query->from('`#__kart_items` AS a');
		$query->JOIN('INNER', '`#__kart_base_currency` AS bc ON bc.item_id=a.item_id');
		$query->where('a.state = 1');

		if (!empty($currency))
		{
			$query->where("bc.currency ='" . $currency . "'");
		}

		if (!empty($prod_cat))
		{
			// Load model file
			if (!class_exists("Quick2cartModelCategory"))
			{
				JLoader::register("Quick2cartModelCategory", JPATH_SITE . "/components/com_quick2cart/models/category.php");
				JLoader::load("Quick2cartModelCategory");
			}

			$CategoryModel = new Quick2cartModelCategory;
			$categoryCondition = $CategoryModel->getWhereCategory($prod_cat);
			$query->join('LEFT', '`#__categories` AS c ON c.id=a.category');
			$query->where($categoryCondition);
		}

		$db->setQuery($query);
		$result = $db->loadAssoc();

		return $result;
	}

	/**
	 * Method to Allowed to visit particular view in multivendor seetting is off
	 *
	 * @return boolean
	 *
	 * @since 2.5
	 */
	public function isAllowedToVisitView()
	{
		$app                 = Factory::getApplication();
		$input               = $app->input;
		$isMultivenderOFFmsg = '';
		$view                = $input->get('view');
		$layout              = $input->get('layout', "default");

		if (empty($view))
		{
			return 1;
		}

		// Current view and layout for comparison
		$current_view_layout = $view . '_' . $layout;

		$mutivendorViews   = array();
		$mutivendorViews[] = "promotions_default";
		$mutivendorViews[] = "promotion_edit";
		$mutivendorViews[] = "category_my";
		$mutivendorViews[] = "orders_storeorder";
		$mutivendorViews[] = "orders_mycustomer";
		$mutivendorViews[] = "payouts_my";
		$mutivendorViews[] = "shipping_list";
		$mutivendorViews[] = "shipprofiles_default";
		$mutivendorViews[] = "shipprofileform_default";
		$mutivendorViews[] = "stores_my";
		$mutivendorViews[] = "taxrates_default";
		$mutivendorViews[] = "taxrateform_default";
		$mutivendorViews[] = "taxprofiles_default";
		$mutivendorViews[] = "taxprofileform_default";
		$mutivendorViews[] = "vendor_createstore";
		$mutivendorViews[] = "zones_default";
		$mutivendorViews[] = "zoneform_edit";

		// Check whether current view is related to multivendor or store owners view
		if (in_array($current_view_layout, $mutivendorViews))
		{
			$isMultivenderOFFmsg = $this->isMultivenderOFF();
		}

		if ($isMultivenderOFFmsg)
		{
			print $isMultivenderOFFmsg;

			return false;
		}
		else
		{
			return true;
		}
	}

	/**
	 * Method to check if extra fields against particular client is exist or not
	 *
	 * @return array
	 *
	 * @since 2.5
	 */
	public function checkExtraFieldIsExist()
	{
		$client = 'com_quick2cart.product';

		if (!class_exists('TjfieldsHelper'))
		{
			JLoader::register('TjfieldsHelper', JPATH_SITE . '/components/com_tjfields/helpers/tjfields.php');
			JLoader::load('TjfieldsHelper');
		}

		$tjfieldsHelperobj = new TjfieldsHelper;

		return $tjfieldsHelperobj->getUniversalFields($client);
	}

	/**
	 * Method to get child categorys of category
	 *
	 * @param   INT  $parentID  parent id
	 *
	 * @return boolean
	 *
	 * @since 2.5
	 */
	public function getChild($parentID)
	{
		if ($parentID)
		{
			$db    = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')->from('#__categories')->where('parent_id =' . $parentID);
			$db->setQuery($query);

			return $db->loadColumn();
		}
	}

	/**
	 * Method is used to display social toolbar according to the integration
	 *
	 * @return ''
	 *
	 * @since 2.9
	 */
	public function displaySocialToolbar()
	{
		$app = Factory::getApplication();

		if ($app->isClient('administrator'))
		{
			return '';
		}

		$sociallibraryobj = $this->getQtcSocialLibObj();
		$integrate_with   = $this->qtcParams->get("integrate_with");

		// Display js/es toolbar
		if ($integrate_with != "none"  && $this->qtcParams->get("displaySocialToolbar") == '1')
		{
			$jinput = $app->input;
			$view   = $jinput->get('view');
			$layout = $jinput->get('layout', "default");
			$tmpl   = $jinput->get('tmpl', "");

			if (empty($view) || !empty($tmpl))
			{
				return '';
			}

			// Current view and layout for comparison
			$current_view_layout = $view . '_' . $layout;
			$toolbarViews        = array();

			$toolbarViews[] = "category_default";
			$toolbarViews[] = "productpage_default";
			$toolbarViews[] = "vendor_store";
			$toolbarViews[] = "orders_default";
			$toolbarViews[] = "downloads_default";

			// Store related view
			$toolbarViews[] = "promotions_default";
			$toolbarViews[] = "promotion_edit";
			$toolbarViews[] = "category_my";
			$toolbarViews[] = "product_default";
			$toolbarViews[] = "orders_storeorder";
			$toolbarViews[] = "orders_mycustomer";
			$toolbarViews[] = "payouts_my";
			$toolbarViews[] = "shipping_list";
			$toolbarViews[] = "shipprofiles_default";
			$toolbarViews[] = "shipprofileform_default";
			$toolbarViews[] = "stores_my";
			$toolbarViews[] = "taxrates_default";
			$toolbarViews[] = "taxrateform_default";
			$toolbarViews[] = "taxprofiles_default";
			$toolbarViews[] = "taxprofileform_default";
			$toolbarViews[] = "vendor_createstore";
			$toolbarViews[] = "zones_default";
			$toolbarViews[] = "zoneform_edit";
			$toolbarViews[] = "vendor_cp";

			if (in_array($current_view_layout, $toolbarViews))
			{
				echo $sociallibraryobj->getToolbar();
			}
		}
	}

	/**
	 * This function return the the comma seperated parameters which is to be removed from URL on change of category.
	 * This function will be used by tj_field component for filter moduleoducts
	 *
	 * @return  string
	 *
	 * @since 1.0
	 */
	public function getParameterToRemoveOnChangeOfCategory()
	{
		// We have component wide category.so directly returned
		return "attributeoption,min_price,max_price";
	}

	/**
	 * Used to integrate the Q2C filter module with tj-field filter module. This function return the Q2c filter module html
	 *
	 * @return json string  :: json_encoded extra field data
	 */
	public function getComponentSpecificFilterHtml()
	{
		$document = Factory::getDocument();
		$rend     = $document->loadRenderer('module');
		$mod      = ModuleHelper::getModule('mod_q2cfilters');
		$params   = array('style' => "none");

		if (!empty($mod))
		{
			ob_start();
			echo $rend->render($mod, $params);
		}

		return ob_get_clean();
	}

	/**
	 * Chck ownership of the user
	 *
	 * @param   data  $storeOwner  storeOwner's User Id
	 *
	 * @return boolean
	 *
	 * @since    2.9.9
	 */
	public function checkOwnership($storeOwner)
	{
		$user = Factory::getUser();
		$app  = Factory::getApplication();

		if ((!empty($user->id) && $user->id == $storeOwner) || $app->isClient('administrator'))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/**
	 * Check for paypal emailid and get payment gateways
	 *
	 * @param   array    $gatewayParam  Gateway params
	 *
	 * @param   integer  $storeId       Store's Id
	 *
	 * @return  array
	 *
	 * @since    2.9.11
	 */
	public function getValidGateways($gatewayParam, $storeId)
	{
		// Get Quick2Cart Params
		$q2cParams           = ComponentHelper::getParams('com_quick2cart');
		$directPaymentConfig = $q2cParams->get('send_payments_to_store_owner', 0, 'INTEGER');

		// Get List of configured payment gateway plugins
		$gateways = Factory::getApplication()->triggerEvent('onTP_GetInfo', array($gatewayParam));

		// Get list of payment gateway plugins configured to be used in Q2C
		$comquick2cartHelper = new comquick2cartHelper;
		$q2cPaymentGateways  = $q2cParams->get('gateways', '', 'ARRAY');
		$storeDetailInfo     = $comquick2cartHelper->getSoreInfoInDetail($storeId);

		// If payment is to be directly sent to the vendor then check configured payment gateways in vendors profile
		if ($directPaymentConfig == 1)
		{
			JLoader::import('fronthelper', JPATH_SITE . '/components/com_tjvendors/helpers');
			JLoader::import('vendorclientxref', JPATH_ADMINISTRATOR . '/components/com_tjvendors/tables');

			// Get payment gateways configured in vendors profile
			$tjvendorFrontHelper = new TjvendorFrontHelper;
			$vendorId            = $tjvendorFrontHelper->checkVendor($storeDetailInfo['owner'], 'com_quick2cart');
			$vendorXrefTable     = Table::getInstance('vendorclientxref', 'TjvendorsTable', array());
			$vendorXrefTable->load(array('vendor_id' => $vendorId, 'client' => 'com_quick2cart'));
			$params = json_decode($vendorXrefTable->params);
			$vendorPaymentGateways = array();

			if (!empty($params->payment_gateway))
			{
				foreach ($params->payment_gateway as $paymentGateway)
				{
					$vendorPaymentGateways[] = $paymentGateway->payment_gateways;
				}

				$vendorPaymentGateways = array_merge($vendorPaymentGateways, array('alphauserpoints', 'bycheck', 'byorder', 'easysocialpoints', 'jomsocialpoints', 'alphauserpoints', 'altauserpoints', 'razorpay'));
			}

			// Check and unset adaptive paypal payment pulgin for direct payment to vendors
			if (in_array('adaptive_paypal', $q2cPaymentGateways))
			{
				$gateways = $this->unsetInvalidateways($gateways, 'adaptive_paypal');
			}

			// Check and unset stripe connect payment pulgin for direct payment to vendors
			if (in_array('stripe', $q2cPaymentGateways))
			{
				$plugin       = PluginHelper::getPlugin('payment', 'stripe');
				$pluginParams = json_decode($plugin->params);

				if ($pluginParams->enableconnect == 1)
				{
					$gateways = $this->unsetInvalidateways($gateways, 'stripe');
				}
			}

			foreach($q2cPaymentGateways as $q2cPaymentGateway)
			{
				if ($vendorPaymentGateways && count($vendorPaymentGateways) && !in_array($q2cPaymentGateway, $vendorPaymentGateways))
				{
					$gateways = $this->unsetInvalidateways($gateways, $q2cPaymentGateway);
				}
			}
		}

		return $gateways;
	}

	/**
	 * unset the gateways
	 *
	 * @param   array   $gateways      All gateways
	 *
	 * @param   string  $gatewayCheck  gateway to check and unset
	 *
	 * @return  array
	 *
	 * @since    2.9.11
	 */
	public function unsetInvalidateways($gateways, $gatewayCheck)
	{
		// Loop through the current payment gateways
		foreach ($gateways as $key => $gateway)
		{
			// Check for the correct payment gateway and unset
			if ($gateway->id == $gatewayCheck)
			{
				unset($gateways[$key]);
			}
		}

		return $gateways;
	}

	/**
	 * This method will return product price and discounted price as per currency
	 *
	 * @param   Integer  $item_id  product id
	 *
	 * @return  array
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function getProductAllPrices($item_id)
	{
		$result = array();
		$db     = Factory::getDbo();
		$query  = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__kart_base_currency', 'bc'));
		$query->where($db->qn('bc.item_id') . ' = ' . (int) $item_id);
		$db->setQuery($query);
		$productPrices = $db->loadAssocList();

		if (!empty($productPrices))
		{
			$result['multi_cur']     = array();
			$result['multi_dis_cur'] = array();

			foreach ($productPrices as $key => $price)
			{
				$result['multi_cur'][$price['currency']] = $price['price'];
				$result['multi_dis_cur'][$price['currency']] = $price['discount_price'];
			}
		}

		return $result;
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return JObject
	 *
	 * @since 1.6
	 */
	public function getActions()
	{
		$user   = Factory::getUser();
		$result = new CMSObject();

		$assetName = 'com_quick2cart';

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, $assetName));
		}

		return $result;
	}
}
