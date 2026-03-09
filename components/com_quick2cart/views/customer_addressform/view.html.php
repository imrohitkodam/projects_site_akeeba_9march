<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Table\Table;

require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/models/zone.php';
Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_tjprivacy/tables');

/**
 * View to edit
 *
 * @since  1.6
 */
class Quick2cartViewCustomer_Addressform extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	protected $canSave;

	protected $countrys;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		$app = Factory::getApplication();
		$jinput = $app->input;
		$user = Factory::getUser();

		$this->form    = $this->get('Form');
		$this->item    = $this->get('Data');

		if ($this->item->id)
		{
			$userPrivacyTable = Table::getInstance('tj_consent', 'TjprivacyTable', array());
			$userPrivacyData = $userPrivacyTable->load(
										array(
												'client' => 'com_quick2cart.address',
												'client_id' => $this->item->id,
												'user_id' => $user->id
											)
									);

			if ($userPrivacyData == true)
			{
				$this->item->privacy_terms_condition = 1;
			}
		}

		$this->params  = $app->getParams('com_quick2cart');
		$this->canSave = $this->get('CanSave');

		$Quick2cartModelZone = new Quick2cartModelZone;
		$this->countrys = $Quick2cartModelZone->getCountry();
		$this->params = ComponentHelper::getParams('com_quick2cart');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		parent::display($tpl);
	}
}
