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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\FormModel;
use Joomla\CMS\Object\CMSObject;
use Joomla\CMS\Table\Table;

require_once JPATH_SITE . '/components/com_quick2cart/models/cartcheckout.php';
BaseDatabaseModel::addIncludePath(JPATH_SITE . '/components/com_tjprivacy/models');
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjprivacy/tables');

use Joomla\Utilities\ArrayHelper;
/**
 * Quick2cart model.
 *
 * @since  1.6
 */
class Quick2cartModelCustomer_AddressForm extends FormModel
{
	private $item = null;

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @since  1.6
	 */
	protected function populateState()
	{
		$app             = Factory::getApplication();
		$comQuick2catApp = Factory::getApplication('com_quick2cart');

		// Load state from the request userState on edit or from the passed variable on default
		if ($app->input->get('layout') == 'edit')
		{
			$id = $app->getUserState('com_quick2cart.edit.customer_address.id');
		}
		else
		{
			$id = $app->input->get('id');
			$app->setUserState('com_quick2cart.edit.customer_address.id', $id);
		}

		$this->setState('customer_address.id', $id);

		// Load the parameters.
		$params       = $comQuick2catApp->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('customer_address.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an ojbect.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return Object|boolean Object on success, false on failure.
	 *
	 * @throws Exception
	 */
	public function &getData($id = null)
	{
		if ($this->item === null)
		{
			$this->item = false;
			$user = Factory::getUser();

			if (empty($id))
			{
				$id = $this->getState('customer_address.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table !== false && $table->load($id))
			{
				$id   = $table->id;
				$canEdit = $user->authorise('core.edit', 'com_quick2cart') || $user->authorise('core.create', 'com_quick2cart');

				if (!$canEdit && $user->authorise('core.edit.own', 'com_quick2cart'))
				{
					$canEdit = $user->id == $table->created_by;
				}

				if (!$canEdit)
				{
					throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 500);
				}

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if ($table->state != $published)
					{
						return $this->item;
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->item  = ArrayHelper::toObject($properties, 'JObject');

				if (empty($this->item->firstname))
				{
					$this->item->firstname = $user->name;
				}

				if (empty($this->item->user_email))
				{
					$this->item->user_email = $user->email;
				}
			}
		}

		return $this->item;
	}

	/**
	 * Method to get the table
	 *
	 * @param   string  $type    Name of the JTable class
	 * @param   string  $prefix  Optional prefix for the table class name
	 * @param   array   $config  Optional configuration array for JTable object
	 *
	 * @return  JTable|boolean JTable if found, boolean false on failure
	 */
	public function getTable($type = 'Customer_address', $prefix = 'Quick2cartTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_quick2cart/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Get an item by alias
	 *
	 * @param   string  $alias  Alias string
	 *
	 * @return int Element id
	 */
	public function getItemIdByAlias($alias)
	{
		$table = $this->getTable();
		$table->load(array('alias' => $alias));

		return $table->id;
	}

	/**
	 * Method to get the profile form.
	 *
	 * The base form is loaded from XML
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return    JForm    A JForm object on success, false on failure
	 *
	 * @since    1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.customer_address', 'customer_addressform', array('control'   => 'jform',	'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return    mixed    The data for the form.
	 *
	 * @since    1.6
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.customer_address.data', array());

		if (empty($data))
		{
			$data = $this->getData();
		}

		return $data;
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data
	 *
	 * @return bool
	 *
	 * @throws Exception
	 * @since 1.6
	 */
	public function save($data)
	{
		$db                  = Factory::getDbo();
		$app                 = Factory::getApplication('com_quick2cart');
		$params              = $app->getParams();
		$userPrivacyAccepted = 0;

		if (!empty($params->get('addressTermsConditons', 0)) && !empty($params->get('addressTermsConditonsArtId', 0)))
		{
			$jinput = Factory::getApplication()->input;

			if (empty($jinput->get('address_terms_condition')) && $jinput->get('address_terms_condition') != 'on')
			{
				return false;
			}
			else
			{
				$userPrivacyAccepted = 1;
			}
		}

		$object = (object) $data;

		if (empty($object->user_id))
		{
			$object->user_id = Factory::getUser()->id;
		}

		// Update their details in the users table using id as the primary key.
		if (!empty($object->id))
		{
			$result = $db->updateObject('#__kart_customer_address', $object, 'id');

			if ($result == 1)
			{
				$msg = Text::_("COM_QUICK2CART_CUSTOMER_ADDRESS_UPDATE_MSG");
			}
		}
		else
		{
			$result     = $db->insertObject('#__kart_customer_address', $object);
			$object->id = $db->insertid();

			if ($result == 1)
			{
				$msg = Text::_("COM_QUICK2CART_CUSTOMER_ADDRESS_ADD_MSG");
			}
		}

		// User terms and condtions
		if ($object->id)
		{
			// Checking here if configuration is enable and article is added then only add the the consent in tj_constent table
			if (!empty($params->get('addressTermsConditons', 0)) && !empty($params->get('addressTermsConditonsArtId', 0)))
			{
				// Save User Privacy Terms and conditions Data
				$userPrivacyTable = Table::getInstance('tj_consent', 'TjprivacyTable', array());
				$userPrivacyData  = $userPrivacyTable->load(
					array(
						'client' => 'com_quick2cart.address',
						'client_id' => $object->id ,
						'user_id' => $object->user_id
					)
				);

				// Added check for avoid duplicate entry in tj_consent table
				if ($userPrivacyData == false)
				{
					$userPrivacyDataArr              = array();
					$userPrivacyDataArr['client']    = 'com_quick2cart.address';
					$userPrivacyDataArr['client_id'] = $object->id;
					$userPrivacyDataArr['user_id']   = $object->user_id;
					$userPrivacyDataArr['purpose']   = Text::_('COM_QUICK2CART_USER_PRIVACY_TERMS_PURPOSE_FOR_USER_ADDRESS');
					$userPrivacyDataArr['accepted']  = $userPrivacyAccepted;
					$userPrivacyDataArr['date']      = Factory::getDate('now')->toSQL();

					$tjprivacyModelObj = BaseDatabaseModel::getInstance('tjprivacy', 'TjprivacyModel');
					$tjprivacyModelObj->save($userPrivacyDataArr);
				}
			}
		}

		$fieldHtml = $this->getAddressHtml($object->id);

		return $fieldHtml;
	}

	/**
	 * Check if data can be saved
	 *
	 * @return bool
	 */
	public function getCanSave()
	{
		$table = $this->getTable();

		return $table !== false;
	}

	/**
	 * Method to get address stored aginst provided user id
	 *
	 * @param   INT  $uid  user id
	 *
	 * @return List of addresses
	 */
	public function getUserAddressList($uid)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__kart_customer_address');
		$query->where('user_id = ' . $uid);
		$db->setQuery($query);
		$address = $db->loadObjectList();

		$fieldHtml = "";

		if (!empty($uid))
		{
			$cartCheckoutModel = new Quick2cartModelcartcheckout;
			$userCountry       = array();
			$userState         = array();

			$billing_flag  = 0;
			$shipping_flag = 0;

			// Check if address is used as billing or shipping order
			if (!empty($address))
			{
				foreach ($address as $item)
				{
					if (!empty($item->last_used_for_shipping))
					{
						$shipping_flag = 1;
					}

					if (!empty($item->last_used_for_billing))
					{
						$billing_flag = 1;
					}
				}

				// Pre select first address as shipping address
				if (empty($shipping_flag))
				{
					$address[0]->last_used_for_shipping = 1;
				}

				// Pre select first address as billing address
				if (empty($billing_flag))
				{
					$address[0]->last_used_for_billing = 1;
				}

				// To take user address key if user address zipcode matched with module location added address(zip code matched here)
				$modLocationAddInUserAddress = array_search($_COOKIE['q2cModLocationPincode'], array_column($address, 'zipcode'));

				if (isset($modLocationAddInUserAddress) && $modLocationAddInUserAddress != false)
				{
					function updateUserActiveAddress($value,$key, $modLocationAddInUserAddress)
					{
						$value->last_used_for_billing  = ($key == $modLocationAddInUserAddress) ? 1 : 0;
						$value->last_used_for_shipping = ($key == $modLocationAddInUserAddress) ? 1 : 0;
					}

					array_walk($address, "updateUserActiveAddress", $modLocationAddInUserAddress);
				}

				foreach ($address as $item)
				{
					if (!array_key_exists($item->country_code, $userCountry))
					{
						if (!empty($item->country_code))
						{
							$userCountry[$item->country_code] = $cartCheckoutModel->getCountryName($item->country_code);
						}
					}

					$item->country_name = $userCountry[$item->country_code];

					if (!array_key_exists($item->state_code, $userState))
					{
						if (!empty($item->state_code))
						{
							$userState[$item->state_code] = $cartCheckoutModel->getStateName($item->state_code);
						}
					}

					if (isset($userState[$item->state_code]))
					{
						$item->state_name = $userState[$item->state_code];
					}
					else
					{
						$item->state_name = '';
					}

					$layout = new FileLayout('address.customer_address');
					$fieldHtml .= $layout->render($item);
				}
			}
		}

		return $fieldHtml;
	}

	/**
	 * Method to get address div html
	 *
	 * @param   INT  $id  address id
	 *
	 * @return address
	 */
	public function getAddressHtml($id)
	{
		$address   = $this->getAddress($id);
		$fieldHtml = "";

		if (!empty($address))
		{
			$cartCheckoutModel     = new Quick2cartModelcartcheckout;
			$address->country_name = $cartCheckoutModel->getCountryName($address->country_code);
			$address->state_name   = $cartCheckoutModel->getStateName($address->state_code);

			$layout = new FileLayout('customer_address', $basePath = JPATH_ROOT . '/components/com_quick2cart/layouts/address');
			$fieldHtml = $layout->render($address);
		}

		return $fieldHtml;
	}

	/**
	 * Method to get address
	 *
	 * @param   INT  $id  address id
	 *
	 * @return address
	 */
	public function getAddress($id)
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__kart_customer_address');
		$query->where('id = ' . $id);
		$db->setQuery($query);

		return $db->loadObject();
	}

	/**
	 * Method to delete address data
	 *
	 * @param   INT  $addressId  ID of address to be deleted
	 *
	 * @return bool|int If success returns the id of the deleted item, if not false
	 *
	 * @throws Exception
	 */
	public function delete($addressId)
	{
		$app         = Factory::getApplication();
		$db          = Factory::getDbo();
		$query       = $db->getQuery(true);
		$addressData = $this->getData($addressId);
		$user        = Factory::getUser();

		if ($user->id == $addressData->user_id)
		{
			$conditions = array($db->quoteName('id') . ' = ' . $addressId);
			$query->delete($db->quoteName('#__kart_customer_address'));
			$query->where($conditions);
			$db->setQuery($query);
			$result = $db->execute();

			return $result;
		}
		else
		{
			$app->enqueueMessage(Text::_('JERROR_ALERTNOAUTHOR'), 'error');
		}
	}
}
