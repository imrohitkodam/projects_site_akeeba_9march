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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Router\Route;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\Plugin\PluginHelper;

$lang = Factory::getLanguage();

/*$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);*/

jimport('joomla.application.component.controlleradmin');

/**
 * Proucts list controller class.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartControllerCategory extends quick2cartController
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

		$this->my_products_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=cp');

		parent::__construct($config);
	}

	/**
	 * This function delete item / product
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function deleteProduct()
	{
		$productHelper = new productHelper;
		$jinput        = Factory::getApplication()->input;
		$item_id       = $jinput->get('item_id', 0, 'INTEGER');
		$res           = $productHelper->deleteWholeProduct($item_id);
		$productHelper = new productHelper;
		$productHelper->deleteNotReqProdImages($item_id, '');

		if (!empty($res))
		{
			echo 1;
		}
		else
		{
			echo 0;
		}

		jexit();
	}

	/**
	 * This function change state of products
	 *
	 * @since	2.2
	 *
	 * @return void
	 */
	public function changeState()
	{
		$prod_model    = $this->getModel('product');
		$jinput        = Factory::getApplication()->input;
		$item_id       = $jinput->get('item_id', '', 'INTEGETR');
		$item_id       = (array) $item_id;
		$current_state = $jinput->get('current_state', 0, 'INTEGETR');

		// Find out new state
		$new_state = ($current_state == 0)?1:0;
		$prod_model->setItemState($item_id, $new_state);
		echo $new_state;
		jexit();
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
		$cid  = Factory::getApplication()->input->get('cid', array(), 'array');
		$data = array(
				'publish' => 1,
				'unpublish' => 0
		);
		$task  = $this->getTask();
		$value = ArrayHelper::getValue($data, $task, 0, 'int');

		// Get some variables from the request
		if (empty($cid))
		{
			Log::add(Text::_('COM_QUICK2CART_NO_PRODUCT_SELECTED'), Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('category');

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
						$ntext = 'COM_QUICK2CART_N_PRODUCTS_PUBLISHED';

						PluginHelper::importPlugin('finder');
						Factory::getApplication()->triggerEvent('onFinderChangeState', array('com_quick2cart.product', $cid, $value));
					}
					elseif ($value === 0)
					{
						$ntext = 'COM_QUICK2CART_N_PRODUCTS_UNPUBLISHED';
					}

					$this->setMessage(Text::plural($ntext, count($cid)));
				}
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}

		$link = Route::_('index.php?option=com_quick2cart&view=category&layout=my&qtcStoreOwner=1&Itemid=' . $this->my_products_itemid, false);

		$this->setRedirect($link);
	}

	/**
	 * Method to unpublish records.
	 *
	 * @return void
	 *
	 * @since 3.0
	 */
	public function unpublish()
	{
		$this->publish();
	}

	/**
	 * Method to publish records.
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
			Log::add(Text::_('COM_QUICK2CART_NO_PRODUCT_SELECTED'), Log::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel('category');

			// Make sure the item ids are integers
			ArrayHelper::toInteger($cid);

			// Delete the items.
			try
			{
				$successCount = $model->delete($cid);

				if ($successCount)
				{
					$ntext = 'COM_QUICK2CART_N_PRODUCTS_DELETED';
					$this->setMessage(Text::plural($ntext, count($cid)));
				}
			}
			catch (Exception $e)
			{
				$this->setMessage(Text::_('COM_QUICK2CART_DB_EXCEPTION_WARNING_MESSAGE'), 'error');
			}
		}

		$link = Route::_('index.php?option=com_quick2cart&view=category&layout=my&qtcStoreOwner=1&Itemid=' . $this->my_products_itemid, false);

		$this->setRedirect($link);
	}
	
	/**
	 * Method to Fetch product Names records.
	 *
	 * @return void
	 *
	 * @since 5.0
	 */
	public function fetchProducts()
	{

		try {
			// Get database object
			$db = Factory::getDBO();

			// Get the query parameter from the input
			$queryParam = trim(Factory::getApplication()->input->get('query', '', 'STRING'));

			if (!$queryParam) {
				throw new Exception(Text::_('COM_QUICK2CART_MISS_PARAMETER'), 400);
			}

			// Build the query
			$query = $db->getQuery(true)
			->select([
				$db->quoteName('name'),
				$db->quoteName('item_id') // 'id' is used to construct the product detail URL for redirection.
			])
			->from($db->quoteName('#__kart_items'))
			->where($db->quoteName('name') . ' LIKE ' . $db->quote('%' . $queryParam . '%'))
			->setLimit(10);

			// Execute the query
			$db->setQuery($query);
			$results = $db->loadAssocList();

			// Add product detail URLs to results
			foreach ($results as &$product) {
				$product['url'] = Route::_('index.php?option=com_quick2cart&view=productpage&layout=default&item_id=' . $product['item_id']);
			}

			// Return JSON response
			echo json_encode($results);
		} catch (Exception $e) {
			// Return error message
			header('HTTP/1.1 ' . $e->getCode());
			echo json_encode(['error' => $e->getMessage()]);
		}
	}

}
