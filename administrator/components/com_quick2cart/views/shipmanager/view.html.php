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
defined('_JEXEC') or die(';)');
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a Shipmanager.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewshipmanager extends HtmlView
{
	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse;
	 *                        automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$this->_setToolBar();

		$app = Factory::getApplication();
		$jinput = $app->input;

		// $layout		= $jinput->get( 'layout', '' );
		// $this->setLayout('list');
		$option = $jinput->get('option');
		$model = $this->getModel('shipmanager');
		$country = $model->getCountry();
		$this->country = $country;

		$model = $this->getModel('shipmanager');
		$shippinglist = $model->getshippinglist();
		$this->shippinglist = $shippinglist;

		// FOR DISPLAY SIDE FILTER
		if (JVERSION < '4.0.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	// Function display ends here

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function _setToolBar()
	{
		// Get the toolbar object instance
		// $delmsg = JText::_('C_ORDER_DELETE_CONF');
		$bar = ToolBar::getInstance('toolbar');
		ToolBarHelper::title(Text::_('QTC_SHIPMANAGER'), 'icon-48-quick2cart.png');
		$delmsg = Text::_('C_ORDER_DELETE_CONF');
		$app = Factory::getApplication();
		$jinput = $app->input;
		$layout		= $jinput->get('layout', '');

		if ($layout == 'list')
		{
			ToolBarHelper::addNew();
			ToolbarHelper::deleteList($delmsg, 'remove', 'JTOOLBAR_DELETE');
		}

		// JToolBarHelper::cancel( 'cancel', 'Close' );
	}
}
