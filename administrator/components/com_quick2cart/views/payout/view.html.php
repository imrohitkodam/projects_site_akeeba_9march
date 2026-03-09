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
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for editing payout.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewPayout extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display ($tpl = null)
	{
		// $this->state = $this->get('State');

		$this->item = $this->get('Item');
		$comquick2cartHelper = new comquick2cartHelper;
		$getPayoutFormData = $this->get('PayoutFormData');
		$this->getPayoutFormData = $getPayoutFormData;
		$payee_options = array();
		$payee_options[] = HTMLHelper::_('select.option', '0', Text::_('COM_QUICK2CART_SELECT_PAYEE'));

		if (!empty($getPayoutFormData))
		{
			foreach ($getPayoutFormData as $payout)
			{
				$amt = round($payout->total_amount);

				if ($amt > 0)
				{
					$username = $comquick2cartHelper->getUserName($payout->user_id);
					$payee_options[] = HTMLHelper::_('select.option', $payout->user_id, $username);
				}
			}
		}

		$this->payee_options = $payee_options;

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */

	protected function addToolbar ()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$viewTitle = Text::_('COM_QUICK2CART_ADD_NEW_PAYOUT');
		}
		else
		{
			$viewTitle = Text::_('COM_QUICK2CART_EDIT_PAYOUT');
		}

		if (isset($this->item->checked_out))
		{
			$checkedOut = ! ($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = Quick2cartHelper::getActions();

		ToolbarHelper::title($viewTitle, 'pencil-2');

		// If not checked out, can save the item.
		if (! $checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolBarHelper::apply('payout.apply', 'JTOOLBAR_APPLY');
			ToolBarHelper::save('payout.save', 'JTOOLBAR_SAVE');
		}
		/*
		 * if (!$checkedOut && ($canDo->get('core.create'))){
		 * JToolBarHelper::custom('payout.save2new', 'save-new.png',
		 * 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false); }
		 */
		/* If an existing item, can save to a copy.*/
		/*
		 * if (!$isNew && $canDo->get('core.create')) {
		 * JToolBarHelper::custom('payout.save2copy', 'save-copy.png',
		 * 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false); }
		 */
		if (empty($this->item->id))
		{
			ToolBarHelper::cancel('payout.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolBarHelper::cancel('payout.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
