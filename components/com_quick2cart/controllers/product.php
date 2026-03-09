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
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

$lang = Factory::getLanguage();

/**
 * Quick2cartControllerProduct
 *
 * @package     Com_Quick2cart
 * @subpackage  site
 * @since       2.2
 */
class Quick2cartControllerProduct extends quick2cartController
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$this->productHelper = new productHelper;

		$this->baseUrl            = 'index.php?option=com_quick2cart&view=';
		$this->my_products_itemid = $comquick2cartHelper->getitemid($this->baseUrl . 'category&layout=my');
		$this->add_product_itemid = $comquick2cartHelper->getitemid($this->baseUrl . 'product&layout=default');
		$this->vDashItemid        = $comquick2cartHelper->getitemid($this->baseUrl . 'vendor&layout=cp');

		parent::__construct($config);
	}

	/**
	 * For add new
	 *
	 * @return  ''
	 *
	 * @since	2.2
	 */
	public function addNew()
	{
		$link = Route::_('index.php?option=com_quick2cart&view=product&layout=default&Itemid=' . $this->add_product_itemid, false);

		$this->setRedirect($link);
	}

	/**
	 * For Edit
	 *
	 * @return  ''
	 *
	 * @since	2.2
	 */
	public function edit()
	{
		$input = Factory::getApplication()->input;
		$cid   = $input->get('cid', array(), 'array');
		ArrayHelper::toInteger($cid);
		$comquick2cartHelper = new comquick2cartHelper;
		$edit_link           = $comquick2cartHelper->getProductLink($cid[0], 'editLink');

		$this->setRedirect($edit_link);
	}

	/**
	 * For Save
	 *
	 * @param   integer  $saveClose  action
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function save($saveClose = 0)
	{
		$params   = ComponentHelper::getParams('com_quick2cart');
		$app      = Factory::getApplication();
		$jinput   = $app->input;
		$cur_post = $jinput->post;
		$item_id       = $jinput->get('item_id', '', 'INTEGER');
		$Itemid        = $jinput->get('Itemid', '', 'INTEGER');

		$link = Uri::base() . "index.php?option=com_quick2cart&view=product&item_id=" . $item_id . "&Itemid=" . $Itemid;

		$sku        = $cur_post->get('sku', '', "RAW");
		$sku        = trim($sku);
		$att_detail = $jinput->get('att_detail', '', 'array');

		foreach ($att_detail as $i => $att)
		{
			$name = $att['attri_name'];

			foreach ($att['attri_opt'] as $key => $val)
			{
				$att_detail[$i]['attri_opt'][$key]['name'] = filter_var($val['name'], FILTER_SANITIZE_STRING);

				if ($att_detail[$i]['attri_opt'][$key]['name'] && !$att_detail[$i]['attri_opt'][$key]['order'])
				{
					$app->enqueueMessage(Text::_('COM_QUICK2CART_ORDER_VALUE_ERROR'), 'error');
					$this->setRedirect($link);

					return false;
				}
			}

			$att_detail[$i]['attri_name'] = filter_var($att['attri_name'], FILTER_SANITIZE_STRING);
		}

		$cur_post->set('att_detail', $att_detail);
		$current_store = $cur_post->get('current_store');

		if (!empty($current_store))
		{
			$app->setUserState('current_store', $current_store);
		}

		$item_name     = $jinput->get('item_name', '', 'STRING');
		$pid           = $jinput->get('pid', 0, 'INT');
		$client        = 'com_quick2cart';
		$stock         = $jinput->get('stock', '', 'INTEGER');
		$min_qty       = $jinput->get('min_item');
		$max_qty       = $jinput->get('max_item');
		$multi_cur     = $jinput->get('multi_cur');
		$multi_dis_cur = $jinput->get('multi_dis_cur');

		// To avoid invalid video links
		$youtube_link = $cur_post->get('youtube_link', '', 'RAW');

		if ($youtube_link != '')
		{
			preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $youtube_link, $matches);

			if ($matches == null)
			{
				$app->enqueueMessage(Text::_('COM_QUICK2CART_INVALID_VIDEO_LINK'), 'error');
				$this->setRedirect($link);

				return false;
			}
		}

		foreach ($multi_cur as $key => $val)
		{
			if ($multi_cur[$key] < $multi_dis_cur[$key])
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_QUICK2CART_DISC_PRICE_SHOULD_BE_LESS_THAN_PRODUCT_PRICE'), 'error');
				$this->setRedirect($link);

				return false;
			}
		}

		for ($i = 0; $i <= count($multi_cur); $i++)
		{
			if (($multi_dis_cur[$i]) > ($multi_cur[$i]))
			{
				$app->enqueueMessage(Text::_('COM_QUICK2CART_DISC_PRICE_SHOULD_BE_LESS_THAN_PRODUCT_PRICE'), 'error');
				$this->setRedirect($link);

				return false;
			}
		}

		if ($min_qty > $max_qty)
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_QUANTITY_ERROR'), 'error');
			$this->setRedirect($link);

			return false;
		}

		$cat       = $jinput->get('prod_cat', '', 'INTEGER');
		$on_editor = $params->get('enable_editor', 0);

		if (empty($on_editor))
		{
			$des = $jinput->get('description', '', 'STRING');
		}
		else
		{
			$des_data = $jinput->get('description', array(), "ARRAY");
			$des      = $des_data["data"];
		}

		$youtubleLink = $jinput->get('youtube_link', '', "RAW");
		$store_id     = $jinput->get('store_id');
		$data         = array();

		// Get currency field count
		$multi_curArray = $cur_post->get('multi_cur', array(), 'ARRAY');
		$originalCount  = count($multi_curArray);

		//  Remove empty currencies from multi_curr
		$filtered_curr = array_filter($multi_curArray, 'strlen');

		// Get currency field count after filter empty allow 0
		$filter_count = count($filtered_curr);

		if ($item_name && $originalCount == $filter_count)
		{
			$model = $this->getModel('attributes');

			// @TODO REMOVE ALL PARAMETER AND SEND FORMATEED POST DATEA
			$comquick2cartHelper = new comquick2cartHelper;

			// Whether have to save attributes or not
			$cur_post->set('saveAttri', 1);
			$cur_post->set('saveMedia', 1);

			$item_id = $comquick2cartHelper->saveProduct($cur_post);

			if (is_numeric($item_id))
			{
				if ($saveClose == 1)
				{
					return 1;
				}

				$app->setUserState('item_id', $item_id);

				$redirectUrl = Route::_('index.php?option=com_quick2cart&view=product&layout=default&item_id=' . $item_id . '&Itemid=' . $this->add_product_itemid, false);
				$this->setRedirect($redirectUrl, Text::_('C_SAVE_M_S'));
			}
			else
			{
				$redirectUrl = Route::_('index.php?option=com_quick2cart&view=product&Itemid=' . $this->vDashItemid, false);
				$this->setRedirect($redirectUrl, Text::_('C_SAVE_M_NS'));
			}
		}
		else
		{
			$redirectUrl = Route::_('index.php?option=com_quick2cart&view=product&Itemid=' . $this->vDashItemid, false);
			$this->setRedirect($redirectUrl, Text::_('C_FILL_COMPULSORY_FIELDS'), 'error');
		}
	}

	/**
	 * For checkSku
	 *
	 * @param   STRING  $sku  product sku
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function checkSku($sku = '')
	{
		$ajaxCall = 0;

		if (empty($sku))
		{
			$ajaxCall = 1;
		}

		if (empty($sku))
		{
			$jinput = Factory::getApplication()->input;
			$sku    = $jinput->get('sku', '', 'RAW');
		}

		$model  = $this->getModel('product');
		$itemid = $model->getItemidFromSku($sku);

		$return = '';

		if (!empty($itemid))
		{
			$return = '1';
		}

		if ($ajaxCall == 1)
		{
			// Ajax call.
			echo $return;
			jexit();
		}

		return $return;
	}

	/**
	 * For saveAndClose
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function saveAndClose()
	{
		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		$Quick2cartControllerProduct->save(1);

		$redirectUrl = Route::_('index.php?option=com_quick2cart&view=category&layout=my&qtcStoreOwner=1&Itemid=' . $this->vDashItemid, false);
		$this->setRedirect($redirectUrl, Text::_('C_SAVE_M_S'));
	}

	/**
	 * For save and new
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function saveAndNew()
	{
		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		$Quick2cartControllerProduct->save(1);

		$redirectUrl = Route::_('index.php?option=com_quick2cart&view=product&item_id=' . $item_id . '&Itemid=' . $this->vDashItemid, false);
		$this->setRedirect($redirectUrl, Text::_('C_SAVE_M_S'));
	}

	/**
	 * For Cancel action
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function cancel()
	{
		$redirectUrl = Route::_('index.php?option=com_quick2cart&view=category&qtcStoreOwner=1&layout=my&Itemid=' . $this->vDashItemid, false);
		$this->setRedirect($redirectUrl);
	}

	/**
	 * This functio upload product media file called via ajax
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function mediaUpload()
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$path                = JPATH_SITE . "/components/com_quick2cart/helpers/media.php";
		$mediaHelper         = $comquick2cartHelper->loadqtcClass($path, 'qtc_mediaHelper');
		$mediaHelper->uploadProdFiles();
	}

	/**
	 * This function starts download
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function downStart()
	{
		$app                         = Factory::getApplication();
		$comquick2cartHelper         = new comquick2cartHelper;
		$Quick2cartControllerProduct = new Quick2cartControllerProduct;
		$productHelper               = new productHelper;
		$myDonloadItemid             = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=downloads');

		$jinput          = $app->input;
		$file_id         = $jinput->get('fid', 0, 'INTEGER');
		$strorecall      = $jinput->get('strorecall', 0, 'INTEGER');
		$guest_email     = $jinput->get('guest_email', '', 'STRING');
		$orderid         = $jinput->get('orderid', 0, 'INTEGER');
		$order_item_id   = $jinput->get('order_item_id', 0, 'INTEGER');
		$authorize       = $productHelper->mediaFileAuthorise($file_id, $strorecall, $guest_email, $order_item_id);
		$downloadStatus  = 0;

		$db        = Factory::getDbo();
		$user      = Factory::getUser();
		$isroot    = $user->authorise('core.admin');
		$params    = ComponentHelper::getParams('com_quick2cart');
		$watermark = false;

		if (!empty($authorize['validDownload']) && $authorize['validDownload'] == 1)
		{
			$productHelper = new productHelper;
			$filepath      = $productHelper->getFilePathToDownload($file_id);

			Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');
			$itemFileTable = Table::getInstance('ItemFile', 'Quick2cartTable', array('dbo', $db));
			$itemFileTable->load(array('file_id' => $file_id));
			$fileData = $itemFileTable->getProperties();

			// On before digital product
			PluginHelper::importPlugin("system");
			PluginHelper::importPlugin("actionlog");
			$app->triggerEvent("onBeforeQ2cDownloadFile", array($fileData));

			if ($params->get('wm_for_paid_downloads', 0, 'INT') && $fileData['purchase_required'])
			{
				$watermark = true;
			}

			if ($params->get('wm_for_free_downloads', 0, 'INT') && !$fileData['purchase_required'])
			{
				$watermark = true;
			}

			// Download will start
			try 
			{
				$downloadStatus = $productHelper->download($filepath, '', '', 0, $watermark);
			}
			catch(Exception $e)
			{
				$app->enqueueMessage($e->getMessage(), 'error');
				$redirect_base_url = 'index.php?option=com_quick2cart&view=downloads&Itemid=';
				$app->redirect(Uri::root() . substr(Route::_($redirect_base_url . $myDonloadItemid), strlen(Uri::base(true)) + 1));
			}

			if ($downloadStatus === 2)
			{
				// If filepath not exists The requested download file does not exists
				$app->enqueueMessage(Text::_('QTC_DOWNLOAD_FILEPATH_NOTEXISTS'), 'error');
				$redirect_base_url = 'index.php?option=com_quick2cart&view=downloads&Itemid=';
				$app->redirect(Uri::root() . substr(Route::_($redirect_base_url . $myDonloadItemid), strlen(Uri::base(true)) + 1));
			}

			// Update file details  ( not for free files)
			elseif (!empty($authorize['orderItemFileId']) && (!$isroot))
			{
				// YOU WILL GET FOR THIS FIELD ONLY FOR PURCHASE REQUIRED FILE
				$productHelper->updateFileDownloadCount($authorize['orderItemFileId']); // kart_orderItemFiles tables primary key
			}

			// On after digital product
			PluginHelper::importPlugin("system");
			PluginHelper::importPlugin("actionlog");
			$app->triggerEvent("onAfterQ2cDownloadFile", array($fileData));

			// Exit tab
			return;
		}
		else
		{
			$app->enqueueMessage(Text::_('QTC_DOWNLOAD_NOT_AUTHORIZED'), 'error');
			$redirect_base_url = 'index.php?option=com_quick2cart&view=downloads&Itemid=';
			$app->redirect(Uri::root() . substr(Route::_($redirect_base_url . $myDonloadItemid), strlen(Uri::base(true)) + 1));
		}

		$app->enqueueMessage(Text::_('SOME_ERROR_OCCURRED'), 'error');
		$redirect_base_url = 'index.php?option=com_quick2cart&view=downloads&Itemid=';

		$app->redirect(Uri::root() . substr(Route::_($redirect_base_url . $myDonloadItemid), strlen(Uri::base(true)) + 1));
	}

	/**
	 * This function gives tax profile list
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function getTaxprofileList()
	{
		$jinput   = Factory::getApplication()->input;
		$store_id = $jinput->get('store_id');
		$selected = $jinput->get('selected');

		$storeHelper    = new storeHelper;
		$tax_listSelect = $storeHelper->getStoreTaxProfilesSelectList($store_id, $selected, 'taxprofile_id', $fieldClass = '', 'taxprofile_id');

		$html = '';

		if (!empty($tax_listSelect))
		{
			$html = $tax_listSelect;
		}
		else
		{
			$html .= ' <label>' . Text::_('COM_QUICK2CART_NO_TAXPROFILE_FOR_STORE') . '</label>';
		}

		$data['html'] = $html;
		echo json_encode($html);
		jexit();
	}

	/**
	 * Method to give availale shipprofile against store.
	 *
	 * @return  Json Plugin shipping methods list.
	 *
	 * @since	2.5
	 */
	public function qtcUpdateShipProfileList()
	{
		$app      = Factory::getApplication();
		$jinput   = $app->input;
		$store_id = $jinput->get('store_id', 0, "INTEGER");

		$qtcshiphelper          = new qtcshiphelper;
		$response['selectList'] = $qtcshiphelper->qtcLoadShipProfileSelectList($store_id, '');
		echo json_encode($response);

		$app->close();
	}

	/**
	 * Method to get globle option for global attribute
	 *
	 * @return  json formatted option data
	 *
	 * @since	2.5
	 */
	public function loadGlobalAttriOptions()
	{
		$app                      = Factory::getApplication();
		$post                     = $app->input->post;
		$response                 = array();
		$response['error']        = 0;
		$response['goption']      = '';
		$response['errorMessage'] = '';

		$globalAttId = $post->get("globalAttId", '', "INTEGER");

		// Get global options
		$goptions = $this->productHelper->getGlobalAttriOptions($globalAttId);

		// Generate option select box
		$layout = new FileLayout('addproduct.attribute_global_options');
		$response['goptionSelectHtml'] = $layout->render($goptions);

		if (empty($goptions))
		{
			$response['error']        = 1;
			$response['errorMessage'] = Text::_('COM_QUICK2CART_GLOBALOPTION_NOT_FOUND');
		}
		else
		{
			$response['goption'] = $goptions;
		}

		echo json_encode($response);
		$app->close();
	}

	/**
	 * Function to load products details
	 *
	 * @return  null
	 *
	 * @since	2.5.5
	 */
	public function loadProductDetails()
	{
		$userData           = array();
		$userData[]         = 'Textbox';
		$app                = Factory::getApplication();
		$item_id            = $app->input->get('prodId', '', 'int');
		$prod_container_num = $app->input->get('prod_container_num', '', 'int');

		$model         = new Quick2cartModelcart;
		$productDetail = $model->getItemRec($item_id);
		$price         = $model->getPrice($item_id, 1);
		$prod_price    = $model->getPrice($item_id, 1);

		$productHelper        = new productHelper;
		$comquick2cartHelper  = new comquick2cartHelper;
		$productData['price'] = $comquick2cartHelper->getFromattedPrice($prod_price['price']);
		$curr                 = $comquick2cartHelper->getCurrencySession();
		$curr_sym             = $comquick2cartHelper->getCurrencySymbol($curr);
		$productData['curr_sym'] = $curr_sym;

		if (!is_null($price['discount_price']))
		{
			$productData['discount_price'] = $comquick2cartHelper->getFromattedPrice($price['discount_price']);
		}

		$productData['min'] = $productDetail->min_quantity;
		$productData['max'] = $productDetail->max_quantity;
		$productData['lot'] = $productDetail->slab;

		if (!empty($productDetail))
		{
			// Get attributes

			$attributes = $productHelper->getItemCompleteAttrDetail($item_id);

			if (!empty($attributes))
			{
				$attributeCount = 1;

				foreach ($attributes as $attribute)
				{
					$data['extraHiddenFields']    = array();
					$data['itemattribute_id']     = $attribute->itemattribute_id;
					$data['fieldType']            = $attribute->attributeFieldType;
					$data['parent']               = $productDetail->parent;
					$data['product_id']           = $productDetail->item_id;
					$data['attribute_compulsary'] = $attribute->attribute_compulsary;
					$data['attributeDetail']      = $attribute;

					// This field is used to give name to attribute select box
					$data['field_name'] = "qtcorder_productdetails[" . $prod_container_num . '][att_option][' . $attribute->itemattribute_id . ']';

					// If textbox then change field name in array format
					if (in_array($data['fieldType'], $userData))
					{
						// For Text box field name
						$data['field_name'] = "qtcorder_productdetails[" . $prod_container_num . '][att_option][' . $data['itemattribute_id'] . '][value]';

						// For textbox's option: we need to add hidden field
						$tmpArray          = array();
						$tmpArray["name"]  = "qtcorder_productdetails[" . $prod_container_num . '][att_option][' . $data['itemattribute_id'] . '][option_id]';
						$tmpArray["value"] = $attribute->optionDetails[0]->itemattributeoption_id;
						$data['extraHiddenFields'][] = $tmpArray;
					}

					// This field is used to give onchange event to attribute select box
					$data['fieldOnChange'] = "qtc_update_product_price('" . $productDetail->item_id . "','" . $prod_container_num . "')";
					$layout = new FileLayout('attribute_option_display', $basePath = JPATH_ROOT . '/components/com_quick2cart/layouts/productpage');

					$attributesData["attribute" . $attributeCount]['html'] = $layout->render($data);
					$attributesData["attribute" . $attributeCount]['name'] = $attribute->itemattribute_name;

					$attributeCount++;
				}

				$productData['attribute_html'] = $attributesData;
			}
		}

		echo json_encode($productData);

		jexit();
	}

	/**
	 * Function to load products shipping details
	 *
	 * @return  null
	 *
	 * @since	2.5.5
	 */
	public function loadProductShippingDetails()
	{
		$app             = Factory::getApplication();
		$qtcshiphelper   = new qtcshiphelper;
		$params          = ComponentHelper::getParams('com_quick2cart');
		$shippingEnabled = $params->get('shipping', 0);

		if ($shippingEnabled)
		{
			$prod_id = $app->input->get('prodId', '', 'int');

			if (!empty($prod_id))
			{
				$profieId = $qtcshiphelper->getItemsShipProfileId($prod_id);
			}
			else
			{
				$itemWiseShipDetail = array();

				echo $itemWiseShipDetail;

				jexit();
			}

			$tamt     = $app->input->get('tamt', '', 'int');
			$qty      = $app->input->get('qty', '', 'int');
			$shipping = $app->input->get('shipping', '', 'int');
			$billing  = $app->input->get('billing', '', 'int');

			if (empty($shipping) || empty($billing) || empty($qty) || empty($tamt))
			{
				return false;
			}

			// Load customer_addressform Model
			if (!class_exists("Quick2cartModelcustomer_addressform"))
			{
				JLoader::register("Quick2cartModelcustomer_addressform", JPATH_SITE . "/components/com_quick2cart/models/customer_addressform.php");
				JLoader::load("Quick2cartModelcustomer_addressform");
			}

			$customer_addressFormModel = new Quick2cartModelcustomer_addressform;

			// Load CreateOrderHelper
			if (!class_exists("CreateOrderHelper"))
			{
				JLoader::register("CreateOrderHelper", JPATH_SITE . "/components/com_quick2cart/helpers/createorder.php");
				JLoader::load("CreateOrderHelper");
			}

			$createOrderHelper = new CreateOrderHelper;

			$address = new stdclass;
			$address->ship_chk = 0;

			if (!empty($shipping))
			{
				$address->shipping_address = $customer_addressFormModel->getAddress($shipping);
				$address->shipping_address = $createOrderHelper->mapUserAddress($address->shipping_address);
			}

			if (!empty($billing))
			{
				$address->billing_address = $customer_addressFormModel->getAddress($billing);
				$address->billing_address = $createOrderHelper->mapUserAddress($address->billing_address);
			}

			if (!empty($profieId))
			{
				// Get shipping methods list.
				$shipMeths = $qtcshiphelper->getShipProfileMethods($profieId);

				if (!empty($shipMeths))
				{
					$shipDetail            = array();
					$shipMethsDetail       = array();
					$shipDetail['item_id'] = $prod_id;

					// Add current cart item detail
					$citem            = array();
					$citem['item_id'] = $prod_id;
					$citem['qty']     = $qty;
					$citem['tamt']    = $tamt;

					$comquick2cartHelper      = new comquick2cartHelper;
					$curr                     = $comquick2cartHelper->getCurrencySession();
					$citem['currency']        = $curr;
					$shipDetail['itemDetail'] = $citem;

					foreach ($shipMeths as $meth)
					{
						$methodId = $meth['methodId'];
						$shipDetail['shippingMeths'][$methodId] = $qtcshiphelper->getItemsShipMethods($prod_id, $address, $citem, $meth);
					}

					$itemWiseShipDetail[] = $shipDetail;
				}

				echo json_encode($itemWiseShipDetail);
			}
		}

		jexit();
	}
}
