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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

/**
 * View class for a list of products.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewCategory extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->state      = $this->get('State');
		$this->items      = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->params     = ComponentHelper::getParams('com_quick2cart');

		$model            = $this->getModel('category');
		$this->searchkey  = $model->getState('filter.search');

		$this->product_sorting = array(
		'' => Text::_('COM_QUICK2CART_SELECT_SORTING_FILTER'),
		'PRICE_ASC'     => Text::_('COM_QUICK2CART_SORTING_PRICE_LOW_TO_HIGH'),
		'PRICE_DESC'    => Text::_('COM_QUICK2CART_SORTING_PRICE_HIGH_TO_LOW'),
		'CREATED_DESC'  => Text::_('COM_QUICK2CART_SORTING_LATEST_FIRST'),
		'CREATED_ASC'   => Text::_('COM_QUICK2CART_SORTING_OLDEST_FIRST'),
		'FEATURED'      => Text::_('COM_QUICK2CART_SORTING_FEATURED'),
		);

		$user                = Factory::getUser();
		$this->logged_userid = $user->id;

		// Check for errors.
		$errors = $this->get('Errors');
		if (count($errors))
		{
			throw new Exception(implode("\n", $errors));
		}

		$app        = Factory::getApplication();
		$jinput     = $app->input;
		$layout     = $jinput->get('layout', 'default', 'STRING');
		$option     = $jinput->get('option', '', 'STRING');
		$storeOwner = $jinput->get('qtcStoreOwner', 0, 'INTEGER');

		$comquick2cartHelper = new comquick2cartHelper;

		// Get all stores.
		$this->store_details = $comquick2cartHelper->getAllStoreDetails();
		$this->categoryPage  = 1;

		// Sstore_id is changed from  STORE view
		$change_storeto = $app->getUserStateFromRequest('$option.current_store', 'current_store', '', 'INTEGER');
		$storeOwner     = $jinput->get('qtcStoreOwner', 0, 'INTEGER');
		$this->qtcShowCatStoreList = $app->getParams()->get('qtcShowCatStoreList', '0', "INT");

		// FOR STORE OWNER
		if (!empty($storeOwner))
		{
			$storehelper    = new storehelper;
			$change_storeto = $storehelper->isVendorsStoreId($change_storeto);
		}

		$this->change_prod_cat = $jinput->get('prod_cat', 0, 'INTEGER');
		$this->storeId = $jinput->get('storeId', 0, 'INTEGER');

		// Retrun store_id,role etc with order by role,store_id
		$this->store_role_list = $store_role_list = $comquick2cartHelper->getStoreIds();
		$this->store_list      = array();

		foreach ($this->store_role_list as $store)
		{
			$this->store_list[] = $store['store_id'];
		}

		$this->products = $this->items = $this->get('Items');

		// When chage store,get latest storeid otherwise( on first load) set first storeid as default
		$this->store_id   = $store_id = (!empty($change_storeto)) ? $change_storeto : '';
		$pagination       = $model->getPagination();

		// ALL FETCH ALL CATEGORIES
		$this->cats       = $comquick2cartHelper->getQ2cCatsJoomla($this->change_prod_cat);
		$this->pagination = $pagination;

		// Added by Sneha
		$filter_state         = $app->getUserStateFromRequest($option . 'search_list', 'search_list', '', 'string');
		$lists['search_list'] = $filter_state;
		$this->lists          = $lists;

		// Get toolbar path
		$bsVersion               = $this->params->get('bootstrap_version', 'bs3', 'STRING');

		if ($bsVersion == 'bs5')
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
		}
		else
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs3');
		}

		if ($layout == 'my')
		{
			if (!$this->logged_userid)
			{
				$return = base64_encode(Uri::getInstance());
				$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
				$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
				$app->redirect($login_url_with_return, 403);
			}

			// Creating status filter.
			$statuses       = array();
			$statuses[]     = HTMLHelper::_('select.option', '', Text::_('COM_QUICK2CART_SELONE'));
			$statuses[]     = HTMLHelper::_('select.option', 1, Text::_('COM_QUICK2CART_PUBLISH'));
			$statuses[]     = HTMLHelper::_('select.option', 0, Text::_('COM_QUICK2CART_UNPUBLISH'));
			$this->statuses = $statuses;

			// Setup toolbar
			$this->addTJtoolbar();
		}
		$this->_prepareDocument();
		$this->productPageTitle = $this->getTitle();
		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  null
	 *
	 * @since  2.0
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$menu  = $menus->getActive();
		$title = null;

		// Getting menu Param
		$menuParam = $app->getParams();

		// @TODO Need to comment this if when a menu for single product item can be created.
		// Getting menu Param
		$menuParam = $app->getParams();
		$title     = $menuParam->get('page_title');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		// Setting meta description
		$meta_description = $menuParam->get('metadesc');

		if ($meta_description)
		{
			$meta_description = $menuParam->get('metadesc');
		}
		elseif ($menuParam->get('menu-meta_description'))
		{
			$meta_description = $menuParam->get('menu-meta_description');
		}

		$this->document->setDescription($meta_description);

		// Setting meta_keywords
		$meta_keywords = $menuParam->get('menu-meta_keywords');
		$this->document->setMetadata('keywords', $meta_keywords);

		if ($menuParam->get('robots'))
		{
			$this->document->setMetadata('robots', $menuParam->get('robots'));
		}
	}

	/**
	 * Setup ACL based tjtoolbar
	 *
	 * @return  void
	 *
	 * @since   2.2
	 */
	protected function addTJtoolbar()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/helpers/quick2cart.php';
		$canDo = Quick2cartHelper::getActions();

		// Add toolbar buttons
		jimport('techjoomla.tjtoolbar.toolbar');
		$tjbar = TJToolbar::getInstance('tjtoolbar', 'pull-right float-end');

		if ($canDo->get('core.create'))
		{
			$tjbar->appendButton('product.addNew', 'TJTOOLBAR_NEW', QTC_ICON_PLUS, 'class="btn btn-sm btn-success"');
		}

		if ($canDo->get('core.edit') && isset($this->items[0]))
		{
			$tjbar->appendButton('product.edit', 'TJTOOLBAR_EDIT', QTC_ICON_EDIT, 'class="btn btn-sm btn-success"');
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				$tjbar->appendButton('category.publish', 'TJTOOLBAR_PUBLISH', QTC_ICON_PUBLISH, 'class="btn btn-sm btn-success"');
				$tjbar->appendButton('category.unpublish', 'TJTOOLBAR_UNPUBLISH', QTC_ICON_UNPUBLISH, 'class="btn btn-sm btn-warning"');
				$tjbar->appendButton('products.csvExport', 'COM_QUICK2CART_SALES_CSV_EXPORT', 'fa fa-download', 'class="btn btn-sm btn-info"');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]))
			{
				$tjbar->appendButton('category.delete', 'TJTOOLBAR_DELETE', Q2C_ICON_TRASH, 'class="btn btn-sm btn-danger"');
			}
		}

		$this->toolbarHTML = $tjbar->render();
	}

	/**
	 * Function to get title for product page according to category selected
	 *
	 * @return  product page title
	 *
	 * @since  2.5
	 * */
	public function getTitle()
	{
		$app   = Factory::getApplication();
		$input = $app->input;

		// Get cat from URL
		$prod_cat = $input->get('prod_cat', 0, 'int');

		// Get all Quick2cart categorys in array
		$all_categorys = HTMLHelper::_('category.options', 'com_quick2cart', array('filter.published' => array(1)));

		// Load the JMenuSite Object
		$menu = $app->getMenu();

		// Load the Active Menu Item as an stdClass Object
		$activeMenuItem    = $menu->getActive();

		// If product category not found in URL then assign product category according menu
		if (empty($prod_cat) && !empty($activeMenuItem))
		{
			$prod_cat = $activeMenuItem->getParams()->get('defaultCatId', 0, 'INT');
		}

		$flag = 0;
		$lagend_title = '';

		foreach ($all_categorys as $cats)
		{
			if ($prod_cat == $cats->value)
			{
				$lagend_title = str_replace("-", "", $cats->text);
				$flag = 1;
			}
		}

		if ($flag == 0)
		{
			$lagend_title = ($activeMenuItem == null) ? "QTC_PRODUCTS_CATEGORY_ALL_BLOG_VIEW" : $activeMenuItem->title;
		}

		return ucfirst($lagend_title);
	}
}
