<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * Payment model class.
 *
 * @package  Quick2cart
 * @since    2.7
 */
class Quick2cartControllerpayment extends BaseController
{
	/**
	 * THis method is used to return payment form
	 *
	 * @return  string  Payemnt form
	 *
	 * @since   2.0
	 */
	public function getHTML()
	{
		$model     = $this->getModel('payment');
		$jinput    = Factory::getApplication()->input;
		$pg_plugin = $jinput->get('processor');
		$user      = Factory::getUser();
		$session   = Factory::getSession();
		$order_id  = $jinput->get('order');
		$html      = $model->getHTML($pg_plugin, $order_id);

		if (!empty($html[0]))
		{
			echo $html[0];
		}

		jexit();
	}

	/**
	 * THis method is to handle payment notification (generally onsite)
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function confirmpayment()
	{
		$model    = $this->getModel('payment');
		$session  = Factory::getSession();
		$jinput   = Factory::getApplication()->input;
		$order_id = $session->get('order_id');

		if (empty($order_id))
		{
			$order_id = $jinput->get('order_id', '', 'STRING');
		}

		if (empty($order_id))
		{
			$order_id = $jinput->get('orderid', '', 'STRING');
		}

		$pg_plugin = $jinput->get('processor');
		$model->confirmpayment($pg_plugin, $order_id);
	}

	/**
	 * THis method is to handle payment notification (generally off site payment gateway)
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function processpayment()
	{
		$app       = Factory::getApplication();
		$jinput    = $app->input;
		$session   = Factory::getSession();

		if ($session->has('payment_submitpost'))
		{
			$post = $session->get('payment_submitpost');
			$session->clear('payment_submitpost');
		}
		else
		{
			$post    = $jinput->post->getArray();
		}

		$pg_plugin = $jinput->get('processor');
		$model     = $this->getModel('payment');
		$order_id  = $jinput->get('orderid', '', 'STRING');

		if ($pg_plugin == 'razorpay') 
		{
			// Get the JSON payload from Razorpay
			$input = file_get_contents('php://input');
			$event = json_decode($input, true);
			$entity = $event['payload']['payment']['entity'];
			$notes = $event['payload']['payment']['entity']['notes'];

			if ($notes['client'] == 'com_quick2cart')
			{
				$order_id = $notes['order_id'];
				$post = $entity;
			}
		}

		if (empty($order_id))
		{
			$order_id = $jinput->get('order_id', '', 'STRING');
		}

		if (empty($post) || empty($pg_plugin))
		{
			$app->enqueueMessage(Text::_('SOME_ERROR_OCCURRED'), 'error');

			return;
		}

		$response = $model->processPayment($post, $pg_plugin, $order_id);

		if (!empty($response['msg']))
		{
			$app->enqueueMessage(trim($response['msg']));
			$app->redirect($response['return']);
		}
		elseif (empty($response['return']))
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_ORDER_PLACED_SOME_ERROR_OCCURRED'), 'error');

			return;
		}
		else
		{
			$app->redirect($response['return']);
		}
	}
}
