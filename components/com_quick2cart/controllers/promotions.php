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
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;

/**
 * Promotions list controller class.
 *
 * @since  1.6
 */
class Quick2cartControllerPromotions extends AdminController
{
	/**
	 * Method to clone existing Promotions
	 *
	 * @return void
	 */
	public function duplicate()
	{
		// Check for request forgeries
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(Text::_('COM_QUICK2CART_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->duplicate($pks);
			$this->setMessage(Text::_('COM_QUICK2CART_ITEMS_SUCCESS_DUPLICATED'));
		}
		catch (Exception $e)
		{
			Factory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$link = 'index.php?option=com_quick2cart&view=promotions&layout=default';
		$promotionsItemId = $comquick2cartHelper->getitemid($link);
		$this->setRedirect('index.php?option=com_quick2cart&view=promotions&Itemid=' . $promotionsItemId);
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    Optional. Model name
	 * @param   string  $prefix  Optional. Class prefix
	 * @param   array   $config  Optional. Configuration array for model
	 *
	 * @return  object	The Model
	 *
	 * @since    1.6
	 */
	public function getModel($name = 'promotion', $prefix = 'Quick2cartModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Method to save the submitted ordering values for records via AJAX.
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function saveOrderAjax()
	{
		// Get the input
		$app   = Factory::getApplication();
		$input = $app->input;
		$pks   = $input->post->get('cid', array(), 'array');
		$order = $input->post->get('order', array(), 'array');

		// Sanitize the input
		ArrayHelper::toInteger($pks);
		ArrayHelper::toInteger($order);

		// Get the model
		$model = $this->getModel();

		// Save the ordering
		$return = $model->saveorder($pks, $order);

		if ($return)
		{
			echo "1";
		}

		// Close the application
		$app->close();
	}

	/**
	 * Method to delete promotion rules
	 *
	 * @return  void
	 *
	 * @since   3.0
	 */
	public function delete()
	{
		$cid                 = Factory::getApplication()->input->get('cid', array(), 'array');
		$storeHelper         = new StoreHelper;
		$comquick2cartHelper = new comquick2cartHelper;
		$promotionModel      = $this->getmodel('promotion');

		// Running the promotion Ids for ownership checks
		foreach ($cid as $id)
		{
			$promotionDetails = $promotionModel->getItem($id);
			$storeOwner       = $storeHelper->getStoreOwner($promotionDetails->store_id);
			$isOwner          = $comquick2cartHelper->checkOwnership($storeOwner);

			if ($isOwner === false)
			{
				throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
			}
		}

		$promotionsModel = $this->getmodel('promotions');

		if ($promotionsModel->delete($cid))
		{
			parent::delete();
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$link = 'index.php?option=com_quick2cart&view=promotions&layout=default';
		$promotionsItemId = $comquick2cartHelper->getitemid($link);
		$this->setRedirect('index.php?option=com_quick2cart&view=promotions&Itemid=' . $promotionsItemId);
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
		parent::publish();
		$comquick2cartHelper = new comquick2cartHelper;
		$link = 'index.php?option=com_quick2cart&view=promotions&layout=default';
		$promotionsItemId = $comquick2cartHelper->getitemid($link);
		$this->setRedirect('index.php?option=com_quick2cart&view=promotions&Itemid=' . $promotionsItemId);
	}

	/**
	 * Check for any pending promotion with specific user targeting
	 * and send discount coupon emails to eligible users.
	 *
	 * This method finds the latest promotion where specific user promotion is enabled
	 * and coupon emails are not yet sent. If found, it triggers the email sending process
	 * via the helper and marks the promotion as processed by updating the `couponmailsent` flag.
	 *
	 * @return void
	 */
	function runPromotionEligibilityCheck()
	{
		// Register the mails helper
		JLoader::register('Quick2CartMailsHelper', JPATH_SITE . '/components/com_quick2cart/helpers/mails.php');
		$quick2CartMailsHelper = new Quick2CartMailsHelper;

		$db = Factory::getDbo();

		// Get the latest promotion that allows specific user promotion
		$query = $db->getQuery(true)
			->select('id')
			->from($db->quoteName('#__kart_promotions'))
			->where($db->quoteName('allowspecificuserpromotion') . ' = 1')
			->where($db->quoteName('couponmailsent') . ' = 0') // only fetch if mail not sent
			->order('id DESC');

		$db->setQuery($query);
		$promotionId = (int) $db->loadResult();

		// If no pending promotion found, exit early
		if (!$promotionId) {
			return;
		}

		// Send mails
		$quick2CartMailsHelper->sendEligibleUserDiscountEmails($promotionId);

		// Update couponmailsent to 1
		$query = $db->getQuery(true)
			->update($db->quoteName('#__kart_promotions'))
			->set($db->quoteName('couponmailsent') . ' = 1')
			->where($db->quoteName('id') . ' = ' . (int) $promotionId);

		$db->setQuery($query);
		$db->execute();
	}
}
