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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;

/**
 * Stores list controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerStores extends AdminController
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JController
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$comquick2cartHelper = new comquick2cartHelper;

		$this->my_stores_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=stores&layout=my');

		parent::__construct($config);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the PHP class name.
	 * @param   array   $config  A named array of configuration variables.
	 *
	 * @return  JModel
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Store', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);

		return $model;
	}

	/**
	 * Method to publish records.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function publish()
	{
		$cid   = Factory::getApplication()->input->get('cid', array(), 'array');
		$data  = array('publish' => 1, 'unpublish' => 0);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		// Get some variables from the request
		if (empty($cid))
		{
			Log::add(Text::_('COM_QUICK2CART_NO_STORE_SELECTED'), Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('stores');

			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);

			// Publish the items.
			try
			{
				$successCount = $model->setItemState($cid, $value);

				if ($successCount)
				{
					if ($value === 1)
					{
						$ntext = 'COM_QUICK2CART_N_STORES_PUBLISHED';
					}
					elseif ($value === 0)
					{
						$ntext = 'COM_QUICK2CART_N_STORES_UNPUBLISHED';
					}

					$this->setMessage(Text::plural($ntext, count($cid)));
				}
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$link = Route::_('index.php?option=com_quick2cart&view=stores&layout=my&Itemid=' . $this->my_stores_itemid, false);

		$this->setRedirect($link);
	}

	/**
	 * Method to delete records.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function delete()
	{
		$cid = Factory::getApplication()->input->get('cid', array(), 'array');

		// Get some variables from the request
		if (empty($cid))
		{
			Log::add(Text::_('COM_QUICK2CART_NO_STORE_SELECTED'), Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('stores');

			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);

			// Delete the items.
			try
			{
				$model->delete($cid);
				$ntext = 'COM_QUICK2CART_N_STORES_DELETED';
				$this->setMessage(Text::plural($ntext, count($cid)));
			}
			catch (Exception $e)
			{
				$this->setMessage(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 'error');
			}
		}

		$link = Route::_('index.php?option=com_quick2cart&view=stores&layout=my&Itemid=' . $this->my_stores_itemid, false);
		$this->setRedirect($link);
	}

	/**
	 * Method getAllStoreProducts.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function getAllStoreProducts()
	{
		$model    = $this->getModel('store');
		$store_id = Factory::getApplication()->input->get('storeId', '', 'INT');

		// FETCH ALL STORE PRODUCT
		$model = new Quick2cartModelstore;
		$model->getAllStoreProducts('', $store_id);
	}

	/**
	 * Method getAllProductsFromStore.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function getAllProductsFromStore()
	{
		$store_id = Factory::getApplication()->input->get('storeId', '', 'INT');
		$model    = $this->getModel('store');

		if (!empty($store_id))
		{
			$items     = $model->getAllProductsFromStore($store_id);
			$options   = array();
			$options[] = HTMLHelper::_('select.option', '', Text::_('COM_QUICK2CART_SELECT_PRODUCT'));

			if (!empty($items))
			{
				foreach ($items as $item)
				{
					// This is only to generate the <option> tag inside select tag
					$options[] = HTMLHelper::_('select.option', $item->item_id, $item->name);
				}
			}

			// Now generate the select list and echo that
			$productList = HTMLHelper::_('select.genericlist', $options, 'qtcstorestate', ' class="qtc_store_products"', 'value', 'text');

			echo $productList;
		}

		jexit();
	}
}
