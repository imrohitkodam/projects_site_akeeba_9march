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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for list view of products.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewProduct extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	public $tags;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->params              = ComponentHelper::getParams('com_quick2cart');
		$app                       = Factory::getApplication();
		$input                     = $app->input;
		$this->product_types       = array();

		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->productHelper       = new productHelper;
		$storeHelper               = new storeHelper;

		// Load component models
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_quick2cart/models');
		$Quick2cartModelWeights = BaseDatabaseModel::getInstance('Weights', 'Quick2cartModel');
		$Quick2cartModelLengths = BaseDatabaseModel::getInstance('Lengths', 'Quick2cartModel');
		$this->weightClasses    = $Quick2cartModelWeights->getItems();
		$this->lengthClasses    = $Quick2cartModelLengths->getItems();

		// Load Attributes model
		$path        = '/components/com_quick2cart/models/attributes.php';
		$attri_model = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . $path, "quick2cartModelAttributes");

		$this->product_types[1] = HTMLHelper::_('select.option', 1, Text::_('QTC_PROD_TYPE_SIMPLE'));
		$this->product_types[2] = HTMLHelper::_('select.option', 2, Text::_('QTC_PROD_TYPE_VARIABLE'));

		// @TODO ADD CONDITION :: LOGGED IN USER MUST HV STORE

		// Gettting store id if store is changed
		$user           = Factory::getUser();
		$change_storeto = $app->getUserStateFromRequest('current_store', 'current_store', 0, 'INTEGER');

		// Get item_id from request from GET/POST
		$item_id = $app->getUserStateFromRequest('item_id', 'item_id', '', 'STRING');

		// REMOVE FROM REQUEST
		$app->setUserState('item_id', '');
		$this->client = $client = "com_quick2cart";
		$this->pid    = 0;

		// If item_id NOT found then SET TO ''
		$this->item_id = '';

		// If edit task then fetch item DETAILS
		if (!empty($item_id))
		{
			$input->set("content_id", $item_id);

			// Check whether called from backend
			$admin_call = $app->getUserStateFromRequest('admin_call', 'admin_call', 0, 'INTEGER');

			if (!empty($admin_call))
			{
				// CHECK SPECIAL ACCESS
				$special_access = $this->comquick2cartHelper->isSpecialAccess();
			}

			// GET ITEM DETAIL
			$this->itemDetail = $itemDetail = $attri_model->getItemDetail(0, '', $item_id);

			// Load category_attribute_set_mapping detail
			$this->attributeSetList = $this->productHelper->getProductGlobalAttributeSet($this->itemDetail);

			// Getting attribure
			$this->item_id        = !empty($this->itemDetail) ? $itemDetail['item_id'] : '';
			$this->allAttribues   = $attri_model->getItemAttributes($this->item_id);

			$this->getMediaDetail = $this->productHelper->getMediaDetail((int) $item_id);
			$this->isAllowedtoChangeProdCategory = $this->productHelper->isAllowedtoChangeProdCategory($item_id);

			if ($this->isAllowedtoChangeProdCategory)
			{
				$this->catName = $this->comquick2cartHelper->getCatName($this->itemDetail['category']);
			}

			$this->store_id = $store_id = $this->store_role_list = $this->itemDetail['store_id'];

			$this->form_extra       = array();
			$Quick2cartModelProduct = $this->getModel('product');

			// Call to extra fields
			$this->form_extra = $Quick2cartModelProduct->getFormExtra(
				array(
					"category" => $this->itemDetail['category'],
					"clientComponent" => 'com_quick2cart',
					"client" => 'com_quick2cart.product',
					"view" => 'product',
					"layout" => 'new'
				)
			);
		}
		else
		{
			//$storeHelper    = new storeHelper;
			$storeList      = (array) $storeHelper->getUserStore($user->id);
			$this->store_id = !empty($storeList[0]['id'])?$storeList[0]['id']:'';
		}

		// IF ITEM_ID AND SPECIAL ACCESS EG ADMIN THEN FETCH STORE ID // means edit task
		// Else :
		if (!empty($item_id) && !empty($special_access))
		{
			// WE DONT WANT TO SHOW STORE SELECT LIST
			$this->store_id = $store_id = $this->store_role_list = $this->itemDetail['store_id'];
		}
		else
		{
			// As no NEED TO CHECK AUTHORIZATION AT ADMINSIDE
			$this->store_role_list = $store_role_list = $this->comquick2cartHelper->getStoreIds();
			//$storeHelper           = new storeHelper;

			// Get all store ids of vendor
			$this->defaultStoreId  = $defaultStoreId = $storeHelper->getAdminDefaultStoreId();

			/*	$this->authorized_store_id = $comquick2cartHelper->store_authorize(
			"managecoupon_default",isset($change_storeto)?$change_storeto:$store_role_list[0]['store_id']);*/
			$this->store_id       = $store_id = (!empty($change_storeto)) ? $change_storeto : $defaultStoreId;
			$this->selected_store = $store_id;

			if (!$this->store_id)
			{
				$storeHelper = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
				$storeList      = (array) $storeHelper->getUserStore($user->id);
				$this->store_id = $storeList[0]['id'];
			}
		}

		// ALL FETCH ALL CATEGORIES
		$itemCategoryDetails = (!empty($this->itemDetail['category'])) ? $this->itemDetail['category'] : '';
		$this->cats          = $this->comquick2cartHelper->getQ2cCatsJoomla($itemCategoryDetails, 0, 'prod_cat', ' required ');

		$this->tags    = $attri_model->getTags();
		$comTagsParams = ComponentHelper::getParams('com_tags');
		$minTermLength = (int) $comTagsParams->get('min_term_length', 3);

		$this->tagParamData = array(
			'minTermLength' => $minTermLength,
			'selector'      => '#jform_tags',
			'allowCustom'   => $user->authorise('core.create', 'com_tags') ? true : false,
		);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function addToolbar()
	{
		ToolBarHelper::save('product.save', 'QTC_SAVE');
		ToolbarHelper::save('product.saveAndClose');
		ToolbarHelper::save2new('product.saveAndNew');
		ToolbarHelper::cancel('product.cancel', 'JTOOLBAR_CLOSE');

		$isNew     = ($this->item_id == 0);
		$viewTitle = Text::_('COM_QUICK2CART_EDIT_PRODUCT');

		if ($isNew)
		{
			$viewTitle = Text::_('COM_QUICK2CART_ADD_PRODUCT');
		}

		ToolBarHelper::title($viewTitle, 'pencil-2');
		ToolbarHelper::preferences('com_quick2cart');
	}
}
