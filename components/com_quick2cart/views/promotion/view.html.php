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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Uri\Uri;

/**
 * View to edit
 *
 * @since  1.6
 */
class Quick2cartViewPromotion extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

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
		HTMLHelper::_('stylesheet', 'administrator/components/com_quick2cart/assets/css/quick2cart.css');

		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		$this->form   = $this->get('Form');
		$this->params = ComponentHelper::getParams('com_quick2cart');
		$model        = $this->getModel();

		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
		$user = Factory::getUser();
		$userId = $user->id;
		$this->storeList = (array) $storeHelper->getUserStore($userId);

		// Get toolbar path
		$bsVersion               = $this->params->get('bootstrap_version', 'bs3', 'STRING');

		if ($bsVersion == 'bs5')
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
		}
		else
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs3');
		}

		if (empty($this->storeList))
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('COM_QUICK2CART_CREATE_ORDER_AUTHORIZATION_ERROR'), 'Warning');

			return false;
		}

		if ($this->item->id)
		{
			$this->conditionList = $model->getRuleConditions($this->item->id);
			$this->conditionMaxCount = $model->getConditionsMax($this->item->id);
			$this->discount = $model->getDiscountRecords($this->item->id);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->discount_type = array(
		'flat' => Text::_('C_FLAT'), 'percentage' => Text::_('C_PER'));

		$this->condition_type = array('AND' => Text::_('COM_QUICK2CART_CONDITION_TRUE_ALL'),'OR' => Text::_('COM_QUICK2CART_CONDITION_TRUE_ANY'));

		$this->promotionDescription = $model->generatePromotionDescription($this->item);

		parent::display($tpl);
	}
}
