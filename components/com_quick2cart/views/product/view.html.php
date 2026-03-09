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
use Joomla\CMS\Uri\Uri;

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
		$option                    = $input->get('option');
		$layout                    = $input->get('layout', 'default', 'string');
		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->productHelper       = new productHelper;
		$storeHelper               = new storeHelper;
		$productHelper             = new productHelper;

		// Load Attributes model
		$path        = '/components/com_quick2cart/models/attributes.php';
		$attri_model = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . $path, "quick2cartModelAttributes");

		// Load component models
		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_quick2cart/models');

		$Quick2cartModelWeights = BaseDatabaseModel::getInstance('Weights', 'Quick2cartModel');
		$Quick2cartModelLengths = BaseDatabaseModel::getInstance('Lengths', 'Quick2cartModel');

		$this->weightClasses = $Quick2cartModelWeights->getItems();
		$this->lengthClasses = $Quick2cartModelLengths->getItems();
		$this->product_types = array();

		$this->product_types[1] = HTMLHelper::_('select.option', 1, Text::_('QTC_PROD_TYPE_SIMPLE'));
		$this->product_types[2] = HTMLHelper::_('select.option', 2, Text::_('QTC_PROD_TYPE_VARIABLE'));

		if ($layout == 'default' || $layout == 'createstore')
		{
			// @TODO ADD CONDITION :: LOGGED IN USER MUST HV STORE

			// Gettting store id if store is changed
			$user    = Factory::getUser();
			$canEdit = $user->authorise('core.edit', 'com_quick2cart') || $user->authorise('core.create', 'com_quick2cart');

			if (!$canEdit)
			{
				Factory::getApplication()->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
				return false;
			}

			$change_storeto = $app->getUserStateFromRequest('current_store', 'current_store', 0, 'INTEGER');

			// Get item_id from request from GET/POST
			$item_id = $app->getUserStateFromRequest('item_id', 'item_id', '', 'STRING');

			// REMOVE FROM REQUEST
			$app->setUserState('item_id', '');
			$this->client = $client = "com_quick2cart";
			$this->pid    = 0;
			$this->cats = $this->comquick2cartHelper->getQ2cCatsJoomla('', 0, 'prod_cat', ' required ');

			// LOAD CART MODEL
			$Quick2cartModelcart = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/models/cart.php", "Quick2cartModelcart");

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
				$this->attributeSetList = $productHelper->getProductGlobalAttributeSet($this->itemDetail);

				// Getting attribure
				$this->item_id        = !empty($this->itemDetail) ? $itemDetail['item_id'] : '';
				$this->allAttribues   = $attri_model->getItemAttributes($this->item_id);
				$this->getMediaDetail = $productHelper->getMediaDetail($item_id);
				$this->isAllowedtoChangeProdCategory = $productHelper->isAllowedtoChangeProdCategory($item_id);

				if ($this->isAllowedtoChangeProdCategory)
				{
					$this->catName = $this->comquick2cartHelper->getCatName($this->itemDetail['category']);
				}

				// Code to get TJ-fileds filed form - start
				$this->form_extra = array();
				$Quick2cartModelProduct = $this->getModel('product');

				// Call to extra fields
				$this->form_extra = $Quick2cartModelProduct->getFormExtra(
				array("category" => $this->itemDetail['category'],
					"clientComponent" => 'com_quick2cart',
					"client" => 'com_quick2cart.product',
					"view" => 'product',
					"layout" => 'new')
					);

				// Code to get TJ-fileds filed form - end

				// Sort fields according to there field sets - start
				$filterFields = array();
				$this->filterFieldSet = array();

				foreach ($this->form_extra as $tjFieldForm)
				{
					if (!empty($tjFieldForm))
					{
						$fieldsArray = array();

						foreach ($tjFieldForm->getFieldsets() as $fieldsets => $fieldset)
						{
							foreach ($tjFieldForm->getFieldset($fieldset->name) as $field)
							{
								$fieldsArray[] = $field;
							}
						}

						if (array_key_exists($fieldset->name, $this->filterFieldSet))
						{
							$this->filterFieldSet[$fieldset->name] = array_merge($fieldsArray, $this->filterFieldSet[$fieldset->name]);
						}
						else
						{
							$this->filterFieldSet[$fieldset->name] = $fieldsArray;
						}
					}
				}
			}

			// IF ITEM_ID AND SPECIAL ACCESS EG ADMIN THEN FETCH STORE ID
			// Means edit task
			if (!empty($item_id) && !empty($special_access))
			{
				// WE DONT WANT TO SHOW STORE SELECT LIST
				$this->store_id = $store_id = $this->store_role_list = $this->itemDetail['store_id'];
			}
			else
			{
				// Get all store ids of vendor
				$this->store_role_list = $store_role_list = $this->comquick2cartHelper->getStoreIds();

				// If Edit ck AUTORIZATION
				$authorized = 0;

				if (!empty($itemDetail) && !empty($itemDetail['store_id']))
				{
					// Item store  ==  logged in user releated store
					foreach ($this->store_role_list as $srole)
					{
						if ($itemDetail['store_id'] == $srole['store_id'])
						{
							$authorized = 1;
							break;
						}
					}
				}

				if ($authorized == 0)
				{
					// Remove all item details
					$this->allAttribues = "";
					$this->item_id      = '';
					$this->itemDetail   = '';
				}

				$this->store_id       = $store_id = (!empty($this->itemDetail['store_id'])) ? $this->itemDetail['store_id'] : $store_role_list[0]['store_id'];
				$this->selected_store = $store_id;
			}
			// Get store's default settings
			$this->defaultStoreSettings = $storeHelper->getStoreDefaultSettings($this->store_id);

			// ALL FETCH ALL CATEGORIES
			if (!empty($this->itemDetail['category']))
			{
				$this->cats = $this->comquick2cartHelper->getQ2cCatsJoomla($this->itemDetail['category'], 0, 'prod_cat', ' required ');
			}
		}

		$this->tags    = $attri_model->getTags();
		$comTagsParams = ComponentHelper::getParams('com_tags');
		$minTermLength = (int) $comTagsParams->get('min_term_length', 3);

		$this->tagParamData = array(
			'minTermLength' => $minTermLength,
			'selector'      => '#jform_tags',
			'allowCustom'   => Factory::getUser()->authorise('core.create', 'com_tags') ? true : false,
		);

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
	private function _setToolBar()
	{
		$document = Factory::getDocument();
		$document->setTitle(Text::_('QTC_PRODUCT_PAGE'));
	}
}
