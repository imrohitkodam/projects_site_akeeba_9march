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
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

// Load Quick2cart Controller for list views
require_once __DIR__ . '/q2clist.php';

/**
 * Products list controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerProduct extends Quick2cartControllerQ2clist
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->productHelper = new productHelper;
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  The array of config values.
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Product', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * For add new
	 *
	 * @return  ''
	 *
	 * @since	2.2
	 */
	public function addnew()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=product&layout=new');
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

		// Get some variables from the request
		$cid = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);

		$quick2cartBackendProductsHelper = new quick2cartBackendProductsHelper;
		$edit_link                       = $quick2cartBackendProductsHelper->getProductLink($cid[0], 'editLink');

		$this->setRedirect($edit_link);
	}

	/**
	 * For cancel
	 *
	 * @return  ''
	 *
	 * @since	2.2
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_quick2cart&view=products');
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
		$app           = Factory::getApplication();
		$jinput        = $app->input;
		$task          = $jinput->get('task', '', 'STRING');
		$cur_post      = $jinput->post;
		$sku           = trim($cur_post->get('sku', '', "RAW"));
		$current_store = $cur_post->get('current_store');
		$multi_cur     = $jinput->get('multi_cur');
		$multi_dis_cur = $jinput->get('multi_dis_cur');
		$prod_cat      = $jinput->get('prod_cat');
		$description   = $jinput->get('description');
		$item_id   = $jinput->get('item_id', '', 'INTEGER');

		$link = Uri::base() . "index.php?option=com_quick2cart&view=product&layout=new&item_id=" . $item_id;


		// To remove tags and remove special characters.
		$att_detail    = $jinput->get('att_detail', '', 'array');

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

		if (!empty($current_store))
		{
			$app->setUserState('current_store', $current_store);
		}

		$item_name = $jinput->get('item_name', '', 'STRING');

		// $currencydata = $cur_post['multi_cur'];
		$pid       = $jinput->get('pid', 0, 'INT');
		$client    = 'com_quick2cart';
		$stock     = $jinput->get('itemstock', '', 'INTEGER');
		$min_qty   = $jinput->get('min_item');
		$max_qty   = $jinput->get('max_item');

		// To avoid invalid video links
		$youtube_link = $cur_post->get('youtube_link', '', 'RAW');

		if ($youtube_link != '')
		{
			preg_match('/[\\?\\&]v=([^\\?\\&]+)/', $youtube_link, $matches);

			if ($matches == null)
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_QUICK2CART_INVALID_VIDEO_LINK'), 'error');
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
				Factory::getApplication()->enqueueMessage(Text::_('COM_QUICK2CART_DISC_PRICE_SHOULD_BE_LESS_THAN_PRODUCT_PRICE'), 'error');
				$this->setRedirect($link);

				return false;
			}
		}

		if ($min_qty > $max_qty)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_QUICK2CART_QUANTITY_ERROR'), 'error');
			$this->setRedirect($link);

			return false;
		}

		$cat          = $jinput->get('prod_cat', '', 'INTEGER');

		// $sku=$jinput->get('sku');
		$params       = ComponentHelper::getParams('com_quick2cart');
		$on_editor    = $params->get('enable_editor', 0);
		$youtubleLink = $jinput->get('youtube_link', '', "RAW");
		$store_id     = $jinput->get('current_store');

		// @TODO hard coded for now store // @if store id is empty then calculate from item_id
		$data         = array();

		// Get currency field count
		$multi_curArray = $cur_post->get('multi_cur', array(), 'ARRAY');
		$originalCount  = count($multi_curArray);
		$filtered_curr  = array_filter($multi_curArray, 'strlen');

		// Get currency field count after filter enpty allow 0
		$filter_count   = count($filtered_curr);

		if ($item_name && $description && $originalCount == $filter_count)
		{
			$comquick2cartHelper = new comquick2cartHelper;
			$path                = JPATH_SITE . '/components/com_quick2cart/models/attributes.php';
			$attri_model         = $comquick2cartHelper->loadqtcClass($path, "quick2cartModelAttributes");

			// Whether have to save attributes or not
			$cur_post->set('saveAttri', 1);
			$cur_post->set('saveMedia', 1);

			$item_id = $comquick2cartHelper->saveProduct($cur_post);

			if (is_numeric($item_id))
			{
				// Load product model
				$path      = JPATH_SITE . '/components/com_quick2cart/models/product.php';
				$prodmodel = $comquick2cartHelper->loadqtcClass($path, 'quick2cartModelProduct');

				if ($saveClose == 1)
				{
					if($task == 'saveAndClose')
					{
						$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=products", Text::_('COM_QUICK2CART_SAVE_SUCCESS'), 'success');					
					}
					else
					{
						$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=product&layout=new", Text::_('COM_QUICK2CART_SAVE_SUCCESS'), 'success');
					}
				}	
				else
				{
					$app->setUserState('item_id', $item_id);
					$link = Uri::base() . "index.php?option=com_quick2cart&view=product&layout=new&item_id=" . $item_id;
					$this->setRedirect($link, Text::_('COM_QUICK2CART_SAVE_SUCCESS'));
				}
			}
			else
			{
				// Save  attribute if any $msg = JText::_( 'C_SAVE_M_NS' );
				$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=product&layout=new", Text::_('C_SAVE_M_NS'));
			}
		}
		else
		{
			$app->setUserState('item_id', $item_id);
			$link = Uri::base() . "index.php?option=com_quick2cart&view=product&layout=new&item_id=" . $item_id;
			$this->setRedirect($link, Text::_('C_FILL_COMPULSORY_FIELDS'), 'error');
		}
	}

	/**
	 * For checkSku
	 *
	 * @return  ''
	 *
	 * @since	2.5
	 */
	public function checkSku()
	{
		$jinput = Factory::getApplication()->input;
		$sku    = $jinput->get('sku', '', 'STRING');
		$model  = $this->getModel('product');
		$itemid = $model->getItemidFromSku($sku);

		if (!empty($itemid))
		{
			echo '1';
		}
		else
		{
			echo '0';
		}

		jexit();
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
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$item_id   = $jinput->get('item_id', '', 'INTEGER');
		$this->save(1);
	}

	/**
	 * For save and new
	 *
	 * @return  void
	 *
	 * @since	2.5
	 */
	public function saveAndNew()
	{
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$item_id   = $jinput->get('item_id', '', 'INTEGER');
		$this->save(1);
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
		$globalAttId              = $post->get("globalAttId", '', "INTEGER");

		// Get global options
		$goptions = $this->productHelper->getGlobalAttriOptions($globalAttId);

		// Generate option select box
		$layout                        = new FileLayout('addproduct.attribute_global_options');
		$response['goptionSelectHtml'] = $layout->render($goptions);
		$response['goption']           = $goptions;

		if (empty($goptions))
		{
			$response['error']        = 1;
			$response['errorMessage'] = Text::_('COM_QUICK2CART_GLOBALOPTION_NOT_FOUND');
		}

		echo json_encode($response);
		$app->close();
	}

	/**
	 * Method to save the extra fields data.
	 *
	 * @param   array  $data              data
	 * @param   array  $extra_jform_data  Extra fields data
	 * @param   INT    $item_id           Id of the record
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since  1.6
	 */
	public function saveExtraFields($data, $extra_jform_data, $item_id)
	{
		$modelProduct = $this->getModel();
		$modelProduct->saveExtraFields($data, $extra_jform_data, $item_id);
	}
}
