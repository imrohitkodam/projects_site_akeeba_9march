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
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for editing coupon.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewCouponform extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->comquick2cartHelper = new comquick2cartHelper;
		$this->params              = ComponentHelper::getParams('com_quick2cart');

		// $this->state = $this->get('State');
		$this->item = $this->get('Data');

		// $this->form = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Get toolbar path
		$comquick2cartHelper = new comquick2cartHelper;
		$bsVersion               = $this->params->get('bootstrap_version', 'bs3', 'STRING');

		if ($bsVersion == 'bs5')
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
		}
		else
		{
			$this->toolbar_view_path = $comquick2cartHelper->getViewpath('vendor', 'toolbar_bs3');
		}

		// $this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */

	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user  = Factory::getUser();
		$isNew = ($this->item->id == 0);

		if ($isNew)
		{
			$viewTitle = Text::_('COM_QUICK2CART_ADD_COUPON');
		}
		else
		{
			$viewTitle = Text::_('COM_QUICK2CART_EDIT_COUPON');
		}

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->id);
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = Quick2cartHelper::getActions();

		ToolbarHelper::title($viewTitle, 'pencil-2');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolbarHelper::apply('coupon.apply', 'JTOOLBAR_APPLY');
			ToolbarHelper::save('coupon.save', 'JTOOLBAR_SAVE');
		}
		/*
		 * if (!$checkedOut && ($canDo->get('core.create'))){
		 * JToolbarHelper::custom('coupon.save2new', 'save-new.png',
		 * 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false); }
		 */
		/* If an existing item, can save to a copy.*/
		/*
		 * if (!$isNew && $canDo->get('core.create')) {
		 * JToolbarHelper::custom('coupon.save2copy', 'save-copy.png',
		 * 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false); }
		 */
		if (empty($this->item->id))
		{
			ToolbarHelper::cancel('coupon.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolbarHelper::cancel('coupon.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
