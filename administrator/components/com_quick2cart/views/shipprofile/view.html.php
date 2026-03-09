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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

/**
 * View class for a Shipprofile.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewShipprofile extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

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
		$this->params        = ComponentHelper::getParams('com_quick2cart');
		$comquick2cartHelper = new comquick2cartHelper;
		$zoneHelper          = new zoneHelper;

		// Check whether view is accessible to user
		if (!$zoneHelper->isUserAccessible())
		{
			return;
		}

		$qtcshiphelper = new qtcshiphelper;
		$app           = Factory::getApplication();
		$jinput        = $app->input;
		$user          = Factory::getUser();
		$layout        = $jinput->get('layout', 'edit');
		$model         = BaseDatabaseModel::getInstance('Shipprofile', 'Quick2cartModel', array('ignore_request' => true));

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		if ($layout == 'edit')
		{
			$this->state = $this->get('State');
			$this->item  = $this->get('Data');
			$this->form  = $this->get('Form');

			// Check whether user is authorized for this zone ?
			if (!empty($this->item->store_id))
			{
				$status = $comquick2cartHelper->store_authorize('shipprofileform_default', $this->item->store_id);

				if (!$status)
				{
					$zoneHelper->showUnauthorizedMsg();

					return false;
				}
			}

			// Get store name while edit view
			if (!empty($this->item->id) && !empty($this->item->store_id))
			{
				$comquick2cartHelper = new comquick2cartHelper;
				$this->storeDetails  = $comquick2cartHelper->getSoreInfo($this->item->store_id);
				$this->shipPluglist  = $model->getShipPluginListSelect();
			}

			// Get shipping profile_id
			$shipprofile_id = $app->input->get('id', 0, 'INT');

			// Getting saved tax rules.
			if (!empty($shipprofile_id))
			{
				$this->shipMethods = $model->getShipMethods($shipprofile_id);
			}

			$this->addToolbar();
		}
		else
		{
			$this->qtcShipProfileId = $jinput->get('id');
			$this->shipmethId       = $jinput->get('shipmethId', 0);
			$shipProfileDetail      = $this->shipProfileDetail = $qtcshiphelper->getShipProfileDetail($this->qtcShipProfileId);

			// Getting saved tax rules.
			if (!empty($this->shipmethId) && !empty($shipProfileDetail['store_id']))
			{
				// GET PLUGIN DETAIL
				$this->plgDetail    = $qtcshiphelper->getPluginDetailByShipMethId($this->shipmethId);
				$this->shipPluglist = $model->getShipPluginListSelect($this->plgDetail['extension_id']);

				// Get plugin shipping methods
				$qtcshiphelper  = new qtcshiphelper;
				$this->response = $qtcshiphelper->qtcLoadShipPlgMethods(
				$this->plgDetail['extension_id'], $shipProfileDetail['store_id'], $this->plgDetail['methodId']
				);
			}
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		/*  Because the application sets a default page title,
			we need to get it from the menu item itself
		*/
		$menu = $menus->getActive();

		if ($menu)
		{
			$this->params->def('page_heading', $this->params->get('page_title', $menu->title));
		}
		else
		{
			$this->params->def('page_heading', Text::_('COM_QUICK2CART_DEFAULT_PAGE_TITLE'));
		}

		$title = $this->params->get('page_title', '');

		if (empty($title))
		{
			$title = $app->get('sitename');
		}
		elseif ($app->get('sitename_pagetitles', 0) == 1)
		{
			$title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
		}
		elseif ($app->get('sitename_pagetitles', 0) == 2)
		{
			$title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
		}

		$this->document->setTitle($title);

		if ($this->params->get('menu-meta_description'))
		{
			$this->document->setDescription($this->params->get('menu-meta_description'));
		}

		if ($this->params->get('menu-meta_keywords'))
		{
			$this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
		}

		if ($this->params->get('robots'))
		{
			$this->document->setMetadata('robots', $this->params->get('robots'));
		}
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
		$app    = Factory::getApplication();
		$input  = $app->input;
		$layout = $input->get('layout', 'edit');

		if ($layout == "edit")
		{
			$viewTitle = Text::_('COM_QUICK2CART_SHIPPROFILE');
			$isNew     = $input->get('id', 0);
			ToolBarHelper::back('QTC_HOME', 'index.php?option=com_quick2cart&view=shipprofiles');
			ToolBarHelper::save('shipprofile.save', 'QTC_SAVE');

			if ($isNew)
			{
				ToolbarHelper::save('shipprofile.saveAndClose');
			}

			ToolBarHelper::cancel('shipprofile.cancel', 'QTC_CLOSE');
			ToolBarHelper::title($viewTitle, 'pencil-2');
		}
	}
}
