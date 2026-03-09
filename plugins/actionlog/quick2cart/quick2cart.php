<?php
/**
 * @package     Quick2Cart
 * @subpackage  Plg_Actionlog_Quick2Cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (c) 2009-2018 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

JLoader::register('ActionlogsHelper', JPATH_ADMINISTRATOR . '/components/com_actionlogs/helpers/actionlogs.php');

/**
 * Quick2Cart Actions Logging Plugin.
 *
 * @since  2.9.14
 */
class PlgActionlogQuick2Cart extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  2.9.14
	 */
	protected $app;

	/**
	 * Database object.
	 *
	 * @var    JDatabaseDriver
	 * @since  2.9.14
	 */
	protected $db;

	/**
	 * Load plugin language file automatically so that it can be used inside component
	 *
	 * @var    boolean
	 * @since  2.9.14
	 */
	protected $autoloadLanguage = true;

	/**
	 * Proxy for ActionlogsModelUserlog addLog method
	 *
	 * This method adds a record to #__action_logs contains (message_language_key, message, date, context, user)
	 *
	 * @param   array   $messages            The contents of the messages to be logged
	 * @param   string  $messageLanguageKey  The language key of the message
	 * @param   string  $context             The context of the content passed to the plugin
	 * @param   int     $userId              ID of user perform the action, usually ID of current logged in user
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	protected function addLog($messages, $messageLanguageKey, $context, $userId = null)
	{
		if (JVERSION >= '4.0.0')
		{
			$model = $this->app->bootComponent('com_actionlogs')->getMVCFactory()->createModel('Actionlog', 'Administrator', ['ignore_request' => true]);
		}
		else
		{
		    JLoader::register('ActionlogsModelActionlog', JPATH_ADMINISTRATOR . '/components/com_actionlogs/models/actionlog.php');

		    /* @var ActionlogsModelActionlog $model */
		    $model = BaseDatabaseModel::getInstance('Actionlog', 'ActionlogsModel', array('ignore_request' => true));
		}

		$model->addLog($messages, $messageLanguageKey, $context, $userId);
	}

	/**
	 * On order place
	 *
	 * Method is called after an order is placed
	 *
	 * @param   OBJECT  $orderData  Order data
	 * @param   OBJECT  $post       post data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cOrderPlace($orderData, $post)
	{
		if (!$this->params->get('logActionForOrderPlaced', 1))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'placeorder';
		$userId      = $user->id;
		$userName    = $user->username;

		$createdBy      = $orderData['order']->created_by;
		$createdFor     = $orderData['order']->user_info_id;
		$createdForUser = Factory::getUser($createdFor);

		if (!empty($createdBy) && $createdBy != $createdFor)
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_PLACE_ORDER_FOR_CUSTOMER';
		}
		else
		{
			$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_PLACE_ORDER';
		}

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'orderId'          => $orderData['order']->prefix . $orderData['order']->id,
			'orderLink'        => 'index.php?option=com_quick2cart&view=orders&layout=order&orderid=' . $orderData['order']->id,
			'userName'         => $createdForUser->username,
			'userAccountLink'  => 'index.php?option=com_users&task=user.edit&id=' . $createdForUser->id
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On orders delete
	 *
	 * Method is called after orders are deleted
	 *
	 * @param   Object  $orderData  holds order related datat
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cOrderDelete($orderData)
	{
		if (!$this->params->get('logActionForOrderDelete', 1))
		{
			return;
		}

		$option             = $this->app->input->getCmd('option');
		$action             = 'orderdelete';
		$user               = Factory::getUser();
		$userId             = $user->id;
		$userName           = $user->username;
		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_ORDER';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'orderId'          => $orderData->id
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On order place
	 *
	 * Method is called after an order is placed
	 *
	 * @param   ARRAY  $fileData  File data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDownloadFile($fileData)
	{
		if (!$this->params->get('logActionForDigitalFileDownload', 1) || empty($fileData['file_id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'downloaddigitalfile';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DOWNLOAD_DIGITAL_FILE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'fileName'         => $fileData['file_display_name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save zone
	 *
	 * Method is called after zone save
	 *
	 * @param   ARRAY    $data   zone data
	 * @param   BOOLEAN  $isNew  flag for new zone
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSaveZone($data, $isNew)
	{
		if (!$this->params->get('logActionForCreateZone', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createzone';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_ZONE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'zoneName'         => $data['name'],
			'zoneLink'         => 'index.php?option=com_quick2cart&view=zone&layout=edit&id=' . $data['id']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete zone
	 *
	 * @param   ARRAY  $data  zone data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeleteZone($data)
	{
		if (!$this->params->get('logActionForDeleteZone', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deletezone';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_ZONE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'zoneName'         => $data['name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save tax rate
	 *
	 * Method is called after tax rate save
	 *
	 * @param   ARRAY    $data   tax rate data
	 * @param   BOOLEAN  $isNew  flag for new tax rate
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSaveTaxRate($data, $isNew)
	{
		if (!$this->params->get('logActionForCreateTaxRate', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createtaxrate';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_TAX_RATE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'taxRateName'      => $data['name'],
			'taxRateLink'      => 'index.php?option=com_quick2cart&view=taxrate&layout=edit&id=' . $data['id'],
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete tax rate
	 *
	 * @param   ARRAY  $data  tax rate data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeleteTaxRate($data)
	{
		if (!$this->params->get('logActionForDeleteTaxRate', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deletetaxrate';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_TAX_RATE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'taxRateName'      => $data['name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save tax profile
	 *
	 * Method is called after tax profile save
	 *
	 * @param   ARRAY    $data   tax profile data
	 * @param   BOOLEAN  $isNew  flag for new tax profile
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSaveTaxProfile($data, $isNew)
	{
		if (!$this->params->get('logActionForCreateTaxProfile', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createtaxprofile';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_TAX_PROFILE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'taxProfileName'   => $data['name'],
			'taxProfileLink'   => 'index.php?option=com_quick2cart&view=taxprofile&layout=edit&id=' . $data['id']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete tax profile
	 *
	 * @param   ARRAY  $data  tax profile data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeleteTaxProfile($data)
	{
		if (!$this->params->get('logActionForDeleteTaxProfile', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deletetaxprofile';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_TAX_PROFILE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'taxProfileName'   => $data['name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save shipping method
	 *
	 * Method is called after shipping method save
	 *
	 * @param   ARRAY    $data   shipping method data
	 * @param   BOOLEAN  $isNew  flag for new shipping method
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSaveShippingMethod($data, $isNew)
	{
		if (!$this->params->get('logActionForCreateShippingMethod', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createshippingmethod';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_SHIPPING_METHOD';

		// Get extension id for q2c zone shipping plugin
		BaseDatabaseModel::addIncludePath(JPATH_SITE . '/administrator/components/com_quick2cart/models');
		$shippingModel = BaseDatabaseModel::getInstance('Shipping', 'Quick2cartModel');
		$result = $shippingModel->getItems();
		$extensionId = isset($result[0]->extension_id) ? $result[0]->extension_id : 0;
		$baseUrl = 'index.php?option=com_quick2cart&view=shipping&layout=list&plugview=createshipmeth&extension_id=';

		$message = array(
			'action'             => $action,
			'actorAccountLink'   => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'          => $userName,
			'shippingMethodName' => $data['name'],
			'shippingMethodLink' => $baseUrl . $extensionId . '&methodId=' . $data['id']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete shipping method
	 *
	 * @param   ARRAY  $data  shipping method data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeleteShippingMethod($data)
	{
		if (!$this->params->get('logActionForDeleteShippingMethod', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deleteshippingmethod';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_SHIPPING_METHOD';

		$message = array(
			'action'             => $action,
			'actorAccountLink'   => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'          => $userName,
			'shippingMethodName' => $data['name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save shipping profile
	 *
	 * Method is called after shipping profile save
	 *
	 * @param   ARRAY    $data   shipping profile data
	 * @param   BOOLEAN  $isNew  flag for new shipping profile
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSaveShippingProfile($data, $isNew)
	{
		if (!$this->params->get('logActionForCreateShippingProfile', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createshippingprofile';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_SHIPPING_PROFILE';

		$message = array(
			'action'              => $action,
			'actorAccountLink'    => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'           => $userName,
			'shippingProfileName' => $data['name'],
			'shippingProfileLink' => 'index.php?option=com_quick2cart&view=shipprofile&layout=edit&id=' . $data['id']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete shipping profile
	 *
	 * @param   ARRAY  $data  shipping profile data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeleteShippingProfile($data)
	{
		if (!$this->params->get('logActionForDeleteShippingProfile', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deleteshippingprofile';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_SHIPPING_PROFILE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'shippingProfileName'      => $data['name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save store
	 *
	 * Method is called after store save
	 *
	 * @param   ARRAY    $data   store data
	 * @param   BOOLEAN  $isNew  Flag for new store
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSaveStore($data, $isNew)
	{
		if (!$this->params->get('logActionForCreateStore', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createstore';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_STORE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'storeName'        => (is_object($data)) ? $data->title : $data['title'],
			'storeLink'        => 'index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . (is_object($data)) ? $data->id: $data['id']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete store
	 *
	 * @param   ARRAY  $data  store data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeleteStore($data)
	{
		if (!$this->params->get('logActionForDeleteStore', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deletestore';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_STORE';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'storeName'        => $data['title']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save product
	 *
	 * Method is called after product save
	 *
	 * @param   ARRAY    $data   product data
	 * @param   BOOLEAN  $isNew  Flag for new store
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSaveProduct($data, $isNew)
	{
		if (!$this->params->get('logActionForCreateProduct', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createproduct';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_PRODUCT';

		JLoader::import('components.com_quick2cart.helpers.products', JPATH_ADMINISTRATOR);
		$quick2cartBackendProductsHelper = new Quick2cartBackendProductsHelper;
		$editLink = $quick2cartBackendProductsHelper->getProductLink($data['item_id'], 'editLink');
		$editLink = substr($editLink, strlen(Uri::base()));

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'productName'      => $data['item_name'],
			'productLink'      => $editLink
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete product
	 *
	 * @param   ARRAY  $data  product data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeleteProduct($data)
	{
		if (!$this->params->get('logActionForDeleteProduct', 1) || empty($data['item_id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deleteproduct';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_PRODUCT';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'productName'      => $data['name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save promotion
	 *
	 * Method is called after promotion save
	 *
	 * @param   ARRAY    $data   promotion data
	 * @param   BOOLEAN  $isNew  Flag for new promotion
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSavePromotion($data, $isNew)
	{
		if (!$this->params->get('logActionForCreatePromotion', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'createpromotion';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_PROMOTION';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'promotionName'    => $data['name'],
			'promotionLink'    => 'index.php?option=com_quick2cart&view=promotion&layout=edit&id=' . $data['id']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete promotion
	 *
	 * @param   ARRAY  $data  promotion data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeletePromotion($data)
	{
		if (!$this->params->get('logActionForDeletePromotion', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();

		$action      = 'deletepromotion';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_PROMOTION';

		$message = array(
			'action'           => $action,
			'actorAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'        => $userName,
			'promotionName'    => $data['name']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after save payout
	 *
	 * Method is called after payout save
	 *
	 * @param   ARRAY    $data   payout data
	 * @param   BOOLEAN  $isNew  Flag for new payout
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cSavePayout($data, $isNew)
	{
		if (!$this->params->get('logActionForCreatePayout', 1) || !$isNew)
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();
		$payoutFor   = Factory::getUser($data['user_id']);

		$action      = 'createpayout';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_CREATE_PAYOUT';

		$message = array(
			'action'                   => $action,
			'actorAccountLink'         => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'                => $userName,
			'payoutForUser'            => $payoutFor->username,
			'payoutForUserAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $payoutFor->id
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}

	/**
	 * On after delete payout
	 *
	 * @param   ARRAY  $data  payout data
	 *
	 * @return  void
	 *
	 * @since   2.9.14
	 */
	public function onAfterQ2cDeletePayout($data)
	{
		if (!$this->params->get('logActionForDeletePayout', 1) || empty($data['id']))
		{
			return;
		}

		$option      = $this->app->input->getCmd('option');
		$user        = Factory::getUser();
		$payoutFor   = Factory::getUser($data['user_id']);
		$action      = 'deletepayout';
		$userId      = $user->id;
		$userName    = $user->username;

		$messageLanguageKey = 'PLG_ACTIONLOG_QUICK2CART_DELETE_PAYOUT';

		$message = array(
			'action'                   => $action,
			'actorAccountLink'         => 'index.php?option=com_users&task=user.edit&id=' . $userId,
			'actorName'                => $userName,
			'payoutForUser'            => $payoutFor->username,
			'payoutForUserAccountLink' => 'index.php?option=com_users&task=user.edit&id=' . $payoutFor->id,
			'payoutId'                 => $data['id']
		);

		$this->addLog(array($message), $messageLanguageKey, $option, $userId);
	}
}
