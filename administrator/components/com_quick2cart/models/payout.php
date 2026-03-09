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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Table\Table;

/**
 * Item Model for an Payout.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartModelPayout extends AdminModel
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_QUICK2CART';

	/**
	 * Returns a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable    A database object
	 */
	public function getTable($type = 'Payout', $prefix = 'Quick2cartTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional ordering field.
	 * @param   boolean  $loadData  An optional direction (asc|desc).
	 *
	 * @return  JForm    $form      A JForm object on success, false on failure
	 *
	 * @since   2.2
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_quick2cart.payout', 'payout', array('control' => 'jform', 'load_data' => $loadData));

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	$data  The data for the form.
	 *
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_quick2cart.edit.payout.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  $item  Object on success, false on failure.
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			// Do any procesing on fields here if needed
		}

		return $item;
	}

	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__tj_payout');
				$max = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}

	/**
	 * Method to save the form data.
	 *
	 * @param   Object  $data  The form data.
	 *
	 * @return   mixed		The user id on success, false on failure.
	 *
	 * @since	1.6
	 */
	public function save($data)
	{
		/*([payee_name] => admin
		[payee_options] => 732
		[user_id] => 732
		[paypal_email] => user1_june3x@mailinator.com
		[transaction_id] => PO1
		[payout_date] => 2014-07-14
		[payment_amount] => 5
		[payment_comment] => Hola
		[status] => 1
		[9ba4b8054b8258941564cd0fae9afb3b] => 1
		[task] => payout.apply
		*/

		$repId    = '';
		$action   = 'insertObject';
		$payoutId = $data->get('id', '', 'INT');
		$app      = Factory::getApplication();

		if (!empty($payoutId))
		{
			$repId  = $data->get('id', '', 'INT');
			$action = 'updateObject';
		}

		$obj = new stdClass;
		$obj->id             = $repId;
		$obj->user_id        = $data->get('user_id', '', 'INT');
		$obj->payee_name     = $data->get('payee_name', '', 'STRING');
		$obj->email_id       = $data->get('paypal_email', '', 'STRING');
		$obj->transaction_id = $data->get('transaction_id', '', 'STRING');
		$obj->amount         = $data->get('payment_amount', '', 'STRING');
		$obj->date           = $data->get('payout_date', '', 'STRING');
		$obj->status         = $data->get('status', '', 'INT');
		$obj->comment        = $data->get('payment_comment', '', 'STRING');

		// On before payout save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$payoutData = (array) $obj;
		$isNew      = ($action == 'insertObject') ? true : false;
		$app->triggerEvent('onBeforeQ2cSavePayout', array($payoutData, $isNew));

		// Insert object
		if (!$this->_db->$action( '#__kart_payouts', $obj, 'id'))
		{
			echo $this->_db->stderr();

			return false;
		}

		// On after payout save
		PluginHelper::importPlugin('system');
		PluginHelper::importPlugin('actionlog');
		$app->triggerEvent('onAfterQ2cSavePayout', array($payoutData, $isNew));

		$this->sendmail($obj->payee_name, $obj->email_id, $obj->transaction_id, $obj->amount, $obj->date, $obj->status, $obj->comment);

		return $this->_db->insertid();
	}

	/**
	 * Function to sendmail
	 *
	 * @return  boolean
	 */
	public function getPayoutFormData()
	{
		// GETTING ALL COMMISSION should be deducted from user including all store
		$query = 'SELECT SUM( fee ) AS fee, `owner` as user_id
		 FROM  `#__kart_store`
		 GROUP BY `owner`';
		$this->_db->setQuery($query);
		$payouts = $this->_db->loadObjectList();

		foreach ($payouts as $index => $key)
		{
			// Calculate toatl total sales

			// HERE	USER_ID= STORE OWNER USERID
			$query1 = "SELECT SUM(i.product_final_price) AS total_amount, s.`store_email`
			 FROM `#__kart_orders` AS o
			 LEFT JOIN `#__kart_order_item` AS i ON o.id = i.order_id
			 LEFT JOIN `#__kart_store` AS s ON s.id = i.store_id
			 WHERE (o.status='C' OR o.status='S')
			 AND s.owner = " . $key->user_id;

			$this->_db->setQuery($query1);
			$userPriceDetail = $this->_db->loadAssoc();

			// Calculate old payouts given

			// Owner as user_id
			if (!empty($userPriceDetail['total_amount']))
			{
				$query = "SELECT SUM(amount)
				 FROM #__kart_payouts
				 WHERE user_id = " . $key->user_id . " AND status = 1";
				$this->_db->setQuery($query);

				$paid_amount = $this->_db->loadresult();

				$key->total_amount = $userPriceDetail['total_amount'] - $paid_amount;
				$key->email = $userPriceDetail['store_email'];
			}
			else
			{
				unset($payouts[$index]);
			}
		}

		return $payouts;
	}

	/**
	 * Function to sendmail
	 *
	 * @param   STRING  $payee_name      payee name
	 * @param   STRING  $email_id        email id
	 * @param   INT     $transaction_id  transaction id
	 * @param   INT     $amount          amount
	 * @param   ARRAY   $date            data
	 * @param   INT     $status          status
	 * @param   STRING  $comment         comment
	 *
	 * @return  boolean
	 */
	public function sendmail($payee_name,$email_id,$transaction_id,$amount,$date,$status,$comment)
	{
		$app  = Factory::getApplication();
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);

		$status_msg = ($status == 0) ? 'Unpaid' : 'Paid';
		$body       = Text::_('BALANCERL');
		$subject    = Text::_('PAYOUT_DETAILS_CHANGED');
		$body       = str_replace('{username}', $payee_name, $body);
		$html       = '<br/><div>' . Text::_("CHANGED") . '<br/><table><tr><td>'
		. Text::_("COM_QUICK2CART_TRANSACTION_ID") . '</td><td> ' . $transaction_id . '</td></tr>
		<tr><td>' . Text::_("EMAIL_CASHBACK_AMOUNT") . '</td><td>'
		. $amount . ' USD</td></tr>
		<tr><td>' . Text::_("EMAILS_PAYOUT_DATE") . '</td><td>' . $date . '</td></tr>
		<tr><td>' . Text::_("EMAIL_STATUS") . ' </td><td>' . $status_msg . '</td></tr>
		<tr><td>' . Text::_("EMAIL_PAYOUT_COMMENT") . ' </td><td>' . $comment . '</td></tr></div>';

		$body .= $html;
		$from        = $app->get('mailfrom');
		$fromname    = $app->get('fromname');
		$recipient[] = $email_id;
		$body        = nl2br($body);
		$mode        = 1;
		$cc          = null;
		$bcc         = null;
		$bcc         = null;
		$attachment  = null;
		$replyto     = null;
		$replytoname = null;

		if ($app->get('mailonline') == true)
		{
			Factory::getMailer()->sendMail($from, $fromname, $recipient, $subject, $body, $mode, $cc, $bcc, $attachment, $replyto, $replytoname);
		}
	}

	/**
	 * Method to delete rows.
	 *
	 * @param   array  &$pks  An array of item ids.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   2.9.14
	 */
	public function delete(&$pks)
	{
		$app   = Factory::getApplication();
		$user  = Factory::getUser();
		$table = $this->getTable();
		$pks   = (array) $pks;

		// Check if I am a Super Admin
		$iAmSuperAdmin = $user->authorise('core.admin');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk))
			{
				if ($iAmSuperAdmin)
				{
					$data = $table->getProperties();

					// On before store delete
					PluginHelper::importPlugin("actionlog");
					$app->triggerEvent("onBeforeQ2cDeletePayout", array($data));

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());

						return false;
					}
					else
					{
						// On after store delete
						PluginHelper::importPlugin("actionlog");
						$app->triggerEvent("onAfterQ2cDeletePayout", array($data));
					}
				}
				else
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					$this->setMessage(Text::_('JERROR_CORE_DELETE_NOT_PERMITTED'), 'error');
				}
			}
			else
			{
				$this->setError($table->getError());

				return false;
			}
		}

		return true;
	}
}
