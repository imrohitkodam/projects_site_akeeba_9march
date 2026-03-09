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
use Joomla\CMS\Toolbar\Toolbar;

/**
 * View class for a list of Quick2cart.
 *
 * @since  1.6
 */
class Quick2cartViewPromotions extends HtmlView
{
	protected $items, $promotionsItemId;

	protected $pagination;

	protected $state;

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
		$this->comquick2cartHelper = new comquick2cartHelper;
		$storeHelper               = $this->comquick2cartHelper->loadqtcClass(JPATH_SITE . "/components/com_quick2cart/helpers/storeHelper.php", "storeHelper");
		$this->comquick2cartHelper->loadqtcClass(JPATH_ADMINISTRATOR . "/components/com_quick2cart/models/products.php", 'Quick2cartModelProducts');
		$this->Quick2cartModelProducts = new Quick2cartModelProducts;
		$this->params        = ComponentHelper::getParams('com_quick2cart');

		// Get toolbar path
		$bsVersion               = $this->params->get('bootstrap_version', 'bs3', 'STRING');

		if ($bsVersion == 'bs5')
		{
			$this->toolbar_view_path = $this->comquick2cartHelper->getViewpath('vendor', 'toolbar_bs5');
		}
		else
		{
			$this->toolbar_view_path = $this->comquick2cartHelper->getViewpath('vendor', 'toolbar_bs3');
		}

		$link = 'index.php?option=com_quick2cart&view=promotions&layout=default';
		$this->promotionsItemId = $this->comquick2cartHelper->getitemid($link);

		$user = Factory::getUser();
		$userId = $user->id;
		$this->storeList = (array) $storeHelper->getUserStore($userId);

		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		// Setup TJ toolbar
		$this->addTJtoolbar();

		if (JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Method to order fields
	 *
	 * @return void
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => Text::_('JGRID_HEADING_ID'),
			'a.`state`' => Text::_('COM_QUICK2CART_PROMOTIONS_PUBLISHED'),
			'a.`name`' => Text::_('COM_QUICK2CART_PROMOTIONS_NAME'),
			'a.`description`' => Text::_('COM_QUICK2CART_PROMOTIONS_DESCRIPTION'),
			'a.`from_date`' => Text::_('COM_QUICK2CART_PROMOTIONS_FROM_DATE'),
			'a.`exp_date`' => Text::_('COM_QUICK2CART_PROMOTIONS_EXP_DATE'),
			'a.`store_id`' => Text::_('COM_QUICK2CART_PROMOTIONS_STORE_ID'),
		);
	}

	/**
	 * Setup ACL based tjtoolbar
	 *
	 * @return  void
	 *
	 * @since   2.2
	 */
	protected function addTJtoolbar ()
	{
		require_once JPATH_ADMINISTRATOR . '/components/com_quick2cart/helpers/quick2cart.php';
		$canDo = Quick2cartHelper::getActions();

		// Add toolbar buttons
		jimport('techjoomla.tjtoolbar.toolbar');
		$tjbar = TJToolbar::getInstance('tjtoolbar', 'pull-right float-end');

		if ($canDo->get('core.create'))
		{
			$tjbar->appendButton('promotion.add', 'TJTOOLBAR_NEW', QTC_ICON_PLUS, 'class="btn btn-sm btn-success"');
		}

		if ($canDo->get('core.edit') && isset($this->items[0]))
		{
			$tjbar->appendButton('promotion.edit', 'TJTOOLBAR_EDIT', QTC_ICON_EDIT, 'class="btn btn-sm btn-success"');
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				$tjbar->appendButton('promotions.publish', 'TJTOOLBAR_PUBLISH', QTC_ICON_PUBLISH, 'class="btn btn-sm btn-success"');
				$tjbar->appendButton('promotions.unpublish', 'TJTOOLBAR_UNPUBLISH', QTC_ICON_UNPUBLISH, 'class="btn btn-sm btn-warning"');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]))
			{
				$tjbar->appendButton('promotions.delete', 'TJTOOLBAR_DELETE', Q2C_ICON_TRASH, 'class="btn btn-sm btn-danger"');
			}
		}

		$this->toolbarHTML = $tjbar->render();
	}
}
