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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Component\ComponentHelper;

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
		$this->state  = $this->get('State');
		$this->item   = $this->get('Item');
		$this->form   = $this->get('Form');
		$model        = $this->getModel();
		$this->params = ComponentHelper::getParams('com_quick2cart');

		$comquick2cartHelper = new comquick2cartHelper;
		$storeHelper         = $comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
		$user                = Factory::getUser();
		$userId              = $user->id;
		$this->storeList     = (array) $storeHelper->getUserStore($userId);

		if (empty($this->storeList))
		{
			$app = Factory::getApplication();
			$app->enqueueMessage(Text::_('COM_QUICK2CART_CREATE_ORDER_AUTHORIZATION_ERROR'), 'Warning');

			return false;
		}

		if ($this->item->id)
		{
			$this->conditionList     = $model->getRuleConditions($this->item->id);
			$this->conditionMaxCount = $model->getConditionsMax($this->item->id);
			$this->discount          = $model->getDiscountRecords($this->item->id);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->discount_type = array(
									'flat' => Text::_('C_FLAT'),
									'percentage' => Text::_('C_PER')
								);

		$this->condition_type = array(
									'AND' => Text::_('COM_QUICK2CART_CONDITION_TRUE_ALL'),
									'OR' => Text::_('COM_QUICK2CART_CONDITION_TRUE_ANY')
								);

		$this->promotionDescription = $model->generatePromotionDescription($this->item);

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = Quick2cartHelper::getActions();

		ToolBarHelper::title(Text::_('COM_QUICK2CART_TITLE_PROMOTION'), 'tag-2');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolBarHelper::apply('promotion.apply', 'JTOOLBAR_APPLY');
			ToolBarHelper::save('promotion.save', 'JTOOLBAR_SAVE');
		}

		if (!$checkedOut && ($canDo->get('core.create')))
		{
			ToolBarHelper::custom('promotion.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		if (empty($this->item->id))
		{
			ToolBarHelper::cancel('promotion.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolBarHelper::cancel('promotion.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
