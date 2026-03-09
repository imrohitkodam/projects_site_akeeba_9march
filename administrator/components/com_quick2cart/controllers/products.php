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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Table\Table;
use Joomla\Utilities\ArrayHelper;
Use Joomla\CMS\Helper\TagsHelper;

// Load Quick2cart Controller for list views
require_once __DIR__ . '/q2clist.php';

Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_quick2cart/tables');

/**
 * Products list controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerProducts extends Quick2cartControllerQ2clist
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
		$this->setRedirect('index.php?option=com_quick2cart&view=products&layout=new');
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
	 * Products Csv export
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function csvExport()
	{
		$app           = Factory::getApplication();
		$headerFlag    = $app->input->get('headerFlag', '0', 'String');
		$productsModel = BaseDatabaseModel::getInstance('Products', 'Quick2cartModel', array('ignore_request' => true));
		$items         = $productsModel->getItems();
		$productsModel->productCsvExport($items, $headerFlag);
	}

	/**
	 * Method to import csv and update products
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public function csvImport()
	{
		$app       = Factory::getApplication();
		$fileArray = $app->input->files->get('csvfile');
		$fileName  = File::stripExt($fileArray['name']);
		$params    = ComponentHelper::getParams('com_quick2cart');

		File::makeSafe($fileName);
		$uploadsDir = $app->get('tmp_path') . '/' . $fileArray['name'];

		if (!isset($fileArray['tmp_name']) || empty($fileArray) || !$fileArray['tmp_name']) 
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_ERROR_IN_MOVING'), 'warning');
			$app->redirect(Route::_('index.php?option=com_quick2cart&view=products', false));

			return;
		}

		if (!File::upload($fileArray['tmp_name'], $uploadsDir))
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_ERROR_IN_MOVING'), 'warning');
			$app->redirect(Route::_('index.php?option=com_quick2cart&view=products', false));

			return false;
		}

		if ($file = fopen($uploadsDir, "r"))
		{
			$ext = File::getExt($uploadsDir);

			if ($ext != 'csv')
			{
				$app->enqueueMessage(Text::_('COM_QUICK2CART_NOT_CSV_FILE_MSG'), 'warning');
				$app->redirect(Route::_('index.php?option=com_quick2cart&view=products', false));

				return false;
			}

			$rowNum = 0;

			while (($data = fgetcsv($file)) !== false)
			{
				if ($rowNum == 0)
				{
					$headers = array();

					foreach ($data as $d)
					{
						$headers[] = $d;
					}
				}
				else
				{
					$rowData = array();

					foreach ($data as $d)
					{
						$rowData[] = $d;
					}

					if (isset($headers))
					{
						$productData[] = array_combine($headers, $rowData);
					}
				}

				$rowNum++;
			}

			fclose($file);
		}
		else
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_SOME_ERROR_OCCURRED'), 'error');
			$app->redirect(Route::_('index.php?option=com_quick2cart&view=products', false));

			return;
		}

		$output               = array();
		$output['return']     = 1;

		$currency    = $params->get('addcurrency', '', 'string');
		$currencyArr = explode(",", $currency);

		$totalProducts = (!empty($productData)) ? count($productData) : 0;
		$emptyFile     = 0;
		$miss_col      = 0;

		if (!empty($productData))
		{
			foreach ($productData as $eachProduct)
			{
				$productTable = Table::getInstance('Product', 'Quick2cartTable');
				$productTable->load(array('item_id' => $eachProduct['Item_id']));

				// Added condition for import csv support only update the products
				if ($productTable && $productTable->item_id)
				{
					$app = Factory::getApplication()->input;

					foreach ($eachProduct as $key => $value)
					{
						if (!array_key_exists('Name', $eachProduct))
						{
							$miss_col = 1;
							break;
						}

						switch ($key)
						{
							case 'Name' :
								$app->set('item_name', $value);
							break;

							case 'Category_id' :
								$app->set('prod_cat', $value);
							break;

							case 'Stock' :
								$app->set('stock', $value);
							break;

							case 'Min_quantity' :
								$app->set('min_item', $value);
							break;

							case 'Max_quantity' :
								$app->set('max_item', $value);
							break;

							case 'Item_id' :
								$app->set('pid', $value);
							break;
						}

						if (strpos($key, 'Prod_price') !== false)
						{
							$multiCurrencyKeyArray   = array();
							$multiCurrencyValueArray = array();
							$multiCurrencyKeyArray   = explode("|", $key);
							$multiCurrencyValueArray = explode("|", $value);

							unset($multiCurrencyKeyArray[0]);

							// Reindexed multiCurrencyKeyArray array
							$multiCurrencyKeyArray = array_values($multiCurrencyKeyArray);

							// Checking here, if currency key count(i.e. USD,INR,EUR) is greater than currency value(100,200) of each item then append empty value to multiCurrencyValueArray for combining both key pair array
							if (count($multiCurrencyKeyArray) > count($multiCurrencyValueArray))
							{
								$extraMultiCurrencyIndexCount = count($multiCurrencyKeyArray) - count($multiCurrencyValueArray);

								for($i=0; $i<$extraMultiCurrencyIndexCount; $i++)
								{
									array_push($multiCurrencyValueArray, "");
								}
							}

							$multi_cur_arr = array_combine($multiCurrencyKeyArray, $multiCurrencyValueArray);
							$app->set('multi_cur', $multi_cur_arr);
						}

						if (strpos($key, 'Prod_dic_price') !== false)
						{
							$multiDisCurrencyKeyArray   = array();
							$multiDisCurrencyValueArray = array();
							$multiDisCurrencyKeyArray   = explode("|", $key);
							$multiDisCurrencyValueArray = explode("|", $value);

							unset($multiDisCurrencyKeyArray[0]);
							$multiDisCurrencyKeyArray = array_values($multiDisCurrencyKeyArray);

							if (count($multiDisCurrencyKeyArray) > count($multiDisCurrencyValueArray))
							{
								$extraMultiDisCurrencyIndexCount = count($multiDisCurrencyKeyArray) - count($multiDisCurrencyValueArray);

								for($j=0; $j<$extraMultiDisCurrencyIndexCount; $j++)
								{
									array_push($multiDisCurrencyValueArray, "");
								}
							}

							$multi_dis_cur_arr = array_combine($multiDisCurrencyKeyArray, $multiDisCurrencyValueArray);
							$app->set('multi_dis_cur', $multi_dis_cur_arr);
						}
					}

					$productTag = new JHelperTags;
					$productTag->getTagIds($productTable->item_id, 'com_quick2cart.product');

					if (isset($productTag->tags) && (!empty($productTag->tags)))
					{
						$assignProductTag['tags'] = explode(",",$productTag->tags);
						$app->set('jform', $assignProductTag);
					}

					$descriptionArray['data'] = $productTable->description;
					$app->set('description', $descriptionArray);
					$app->set('youtube_link', $productTable->video_link);
					$app->set('featured', $productTable->featured);
					$app->set('metakey', $productTable->metakey);
					$app->set('metadesc', $productTable->metadesc);
					$app->set('store_id', $productTable->store_id);

					$app->set('sku', $productTable->sku);
					$app->set('state', $productTable->state);
					$app->set('qtc_product_type', $productTable->product_type);
					$app->set('item_slab', $productTable->slab);
					$app->set('length_class_id', $productTable->item_length_class_id);
					$app->set('weigth_class_id', $productTable->item_weight_class_id);
					$app->set('qtc_shipProfile', $productTable->shipProfileId);
					$app->set('client', 'com_quick2cart');
					$comquick2cartHelper = new comquick2cartHelper;
					$itemId = $comquick2cartHelper->saveProduct($app);
				}
			}
		}
		else
		{
			$emptyFile ++;
		}

		if ($emptyFile == 1)
		{
			$output['errormsg'] = Text::sprintf('COM_QUICK2CART_IMPORT_BLANK_FILE');
		}
		else
		{
			if ($miss_col)
			{
				$output['errormsg'] = Text::_('COM_QUICK2CART_CSV_IMPORT_COLUMN_MISSING');
			}
			else
			{
				$output['successmsg'] = Text::sprintf('COM_QUICK2CART_PRODUCTS_IMPORT_TOTAL_ROWS_CNT_MSG', $totalProducts) . "<br />";
			}
		}

		$msg = '';

		if ($output['errormsg'])
		{
			$msg = $output['errormsg'];
		}
		elseif ($output['successmsg'])
		{
			$msg = $output['successmsg'];
		}

		$this->setRedirect(Route::_('index.php?option=com_quick2cart&view=products', false), $msg);
	}



	/**
	 * Method to publish records.
	 *
	 * @return void
	 *
	 * @since 4.0.1
	 */
	public function publish()
	{
		$cid  = Factory::getApplication()->input->get('cid', array(), 'array');
		$data = array(
				'publish' => 1,
				'unpublish' => 0
		);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');
		$app     = Factory::getApplication();
		$stateId = $app->input->get('task', '', 'STRING');

		// Get some variables from the request
		if (empty($cid))
		{
			Log::add(Text::_('COM_QUICK2CART_NO_PRODUCT_SELECTED'), Log::WARNING, 'jerror');
		}
		else
		{

			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);

			// Publish the items.
			try
			{
				$state = $stateId == 'publish' ? 0 : 1;
				$ids = $cid;

				$db    = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->select('COUNT(*)');
				$query->from($db->quoteName('#__kart_items'));
				$query->where($db->quoteName('state') . ' = ' . $state);
				$query->where($db->quoteName('item_id') . ' IN (' . implode(',', $db->quote($ids)) .  ')');
				$db->setQuery($query);
				$count = $db->loadResult();

				parent::publish();

				if ($value === 1)
				{
					$ntext = 'COM_QUICK2CART_N_PRODUCTS_PUBLISHED';
				}
				else
				{
					$ntext = 'COM_QUICK2CART_N_PRODUCTS_UNPUBLISHED';
				}

				$this->setMessage(Text::plural($ntext, $count));
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$link = Route::_('index.php?option=com_quick2cart&view=products', false);

		$this->setRedirect($link);
	}

	/**
	 * Method to unpublish records.
	 *
	 * @return void
	 *
	 * @since 4.0.1
	 */
	public function unpublish()
	{
		$this->publish();
	}
}
