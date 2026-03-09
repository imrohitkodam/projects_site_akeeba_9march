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
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

$lang = Factory::getLanguage();

/**
 * Quick2cartControllerVendor controller.
 *
 * @since  1.6
 */
class Quick2cartControllerVendor extends quick2cartController
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

		$this->my_stores_itemid    = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=stores&layout=my');
		$this->create_store_itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=createstore');

		parent::__construct($config);
	}

	/**
	 * Method Add New.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function addNew()
	{
		$link = Route::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&Itemid=' . $this->create_store_itemid, false);

		$this->setRedirect($link);
	}

	/**
	 * Method Edit.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function edit()
	{
		$input = Factory::getApplication()->input;
		$cid   = $input->get('cid', '', 'array');
		ArrayHelper::toInteger($cid);

		$link = Route::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $cid[0] . '&Itemid=' . $this->create_store_itemid, false);
		$this->setRedirect($link);
	}

	/**
	 * Method Save.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function save()
	{
		$app          = Factory::getApplication();
		$jinput       = $app->input;
		$post         = $jinput->post;
		$btnAction    = $post->get('btnAction');
		$store_id     = $post->get('id');
		$qtcadminCall = $jinput->get('qtcadminCall');

		$storeHelper        = new storeHelper;		
		$allowToCreateStore = $storeHelper->isAllowedToCreateNewStore();

		$comquick2cartHelper = new comquick2cartHelper;
		$itemid = $comquick2cartHelper->getItemId('index.php?option=com_quick2cart&view=vendor&layout=createstore');
		$link   = Route::_('index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $store_id . '&Itemid=' . $itemid, false);

		if (!is_numeric($post->get('phone')))
		{
			$app->enqueueMessage(Text::_('COM_QUICK2CART_INVALID_CONTACT_NO'), 'error');
			$this->setRedirect($link);

			return false;
		}

		if ($allowToCreateStore == 0 && empty( $store_id ))
		{
			$userStoreCount = $storeHelper->getUserStoreCount();
			$msg            = Text::sprintf('QTC_ALREADY_YOU_HAVE_STORES', $userStoreCount);
			$this->setRedirect('index.php?option=com_quick2cart&view=stores&layout=my&Itemid=' . $this->my_stores_itemid, $msg, 'error');
		}
		else
		{
			$result       = $storeHelper->saveVendorDetails($post);
			$qtcadminCall = $jinput->get('qtcadminCall');

			if ($btnAction == 'vendor.saveAndClose')
			{
				$link = Route::_('index.php?option=com_quick2cart&view=stores&layout=my&Itemid=' . $this->my_stores_itemid, false);
			}
			else
			{
				$link = $comquick2cartHelper->quick2CartRoute('index.php?option=com_quick2cart&view=vendor&layout=createstore&store_id=' . $result['store_id']);
			}

			if (!empty($qtcadminCall))
			{
				$link = Uri::root() . 'administrator/index.php?option=com_quick2cart';
			}

			$this->setRedirect($link, $result['msg']);
		}
	}

	/**
	 * Method Cancel.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function cancel()
	{
		$link = Route::_('index.php?option=com_quick2cart&view=stores&layout=my&Itemid=' . $this->my_stores_itemid, false);

		$this->setRedirect($link);
	}

	/**
	 * Method refreshVendorDashboard.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function refreshVendorDashboard()
	{
		$app      = Factory::getApplication();
		$jinput   = $app->input;
		$fromDate = $jinput->get('fromDate', '', 'STRING');
		$toDate   = $jinput->get('toDate', '', 'STRING');

		if ($fromDate)
		{
			$fromDate = date('Y-m-d H:i:s', strtotime($fromDate));
		}

		if ($fromDate)
		{
			$toDate = date('Y-m-d H:i:s', strtotime($toDate));
		}

		$app->setUserState('from', $fromDate);
		$app->setUserState('to', $toDate);
		echo 1;
		jexit();
	}

	/**
	 * refreshStoreView
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function refreshStoreView()
	{
		$app           = Factory::getApplication();
		$jinput        = $app->input;
		$post          = $jinput->post;
		$store_id      = $jinput->get('store_id');
		$current_store = $post->get('current_store');
		$app->setUserState('store_cat', $current_store);

		if (!empty($current_store))
		{
			$app->setUserState('current_store', $current_store);
		}
		
		$this->setRedirect(Uri::base() . 'index.php?option=com_quick2cart&view=vendor&layout=vendor&layout=store&store_id=' . $store_id);
	}

	/**
	 * This fuction will send customer email to store owner
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function contactUsEmail()
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_SITE);

		$jinput  = Factory::getApplication()->input;
		$storeid = $jinput->get('store_id');
		$post    = $jinput->post;
		$model   = $this->getModel('vendor');
		$model->sendcontactUsEmail($post);
		$msgsent = Text::_('QTC_MSG_SENT');

		$this->setRedirect(Uri::base() . "index.php?option=com_quick2cart&view=vendor&layout=contactus&store_id=" .	$storeid . '&tmpl=component', $msgsent);
	}

	/**
	 * Method to ckUniqueVanityURL.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function ckUniqueVanityURL()
	{
		$jinput    = Factory::getApplication()->input;
		$vanityURL = $jinput->get('vanityURL', '', 'RAW');
		$model     = $this->getModel('vendor');
		$status    = $model->ckUniqueVanityURL($vanityURL);

		if (!empty($status))
		{
			// Present vanity URL
			echo 1;
		}
		else
		{
			echo 0;
		}

		jexit();
	}

	/**
	 * Method to Get region.
	 *
	 * @return bool
	 *
	 * @since 1.6
	 */
	public function getRegions()
	{
		$app        = Factory::getApplication();
		$input      = $app->input;
		$country_id = $input->get('country_id', '0', 'int');

		// Flag to show default value of state select box
		$defaultValue        = $input->get('default_value', '0', 'int');
		$Quick2cartModelZone = $this->getModel('zone');
		$Quick2cartModelZone = new Quick2cartModelZone;

		if (!empty($country_id))
		{
			$stateList = $Quick2cartModelZone->getRegionList($country_id);

			$options = array();

			if ($defaultValue == 1)
			{
				$options[] = HTMLHelper::_('select.option', '', Text::_('QTC_BILLIN_SELECT_STATE'));
			}
			else
			{
				$options[] = HTMLHelper::_('select.option', 0, Text::_('COM_QUICK2CART_ZONE_ALL_STATES'));
			}

			if ($stateList)
			{
				foreach ($stateList as $state)
				{
					// This is only to generate the <option> tag inside select tag
					$options[] = HTMLHelper::_('select.option', $state->region_id, $state->region);
				}
			}

			// Now generate the select list and echo that
			$stateList = HTMLHelper::_('select.genericlist', $options, 'qtcstorestate', ' class="qtc_store_state"', 'value', 'text');
			echo $stateList;
		}

		$app->close();
	}

	/**
	 * Called to validate captcha
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function isCaptchaCorrect()
	{
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
		header('Content-type: application/json');

		$app   = Factory::getApplication();
		$input = $app->input;
		$post  = $input->post;

		PluginHelper::importPlugin('captcha');
		$input->set('recaptcha_response_field', $post->get('recaptcha_response_field', '', 'STRING'));
		$res     = $app->triggerEvent('onCheckAnswer');
		$content = '';

		if (empty($res[0]))
		{
			$content = 2;
		}
		else
		{
			$content = 1;
		}

		header('Content-type: application/json');

		echo json_encode($content);
		jexit();
	}
}
