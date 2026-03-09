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
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View class for a list of stores.
 *
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 * @since       2.2
 */
class Quick2cartViewTaxprofileform extends HtmlView
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
	 *
	 * @throws  Exception if there is an error in the form event.
	 */
	public function display($tpl = null)
	{
		$comquick2cartHelper = new comquick2cartHelper;
		$zoneHelper          = new zoneHelper;

		// Check whether view is accessible to user
		if (!$zoneHelper->isUserAccessible())
		{
			return;
		}

		$app          = Factory::getApplication();
		$jinput       = $app->input;
		$user         = Factory::getUser();
		$this->params = ComponentHelper::getParams('com_quick2cart');
		$layout       = $jinput->get('layout', 'default');
		$model        = $this->getModel('taxprofileform');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		if ($layout == 'default' || $layout == 'default_bs3' || $layout == 'default_bs5')
		{
			$this->state = $this->get('State');
			$this->item  = $this->get('Data');

			if (empty($this->item))
			{
				throw new Exception(Text::_('COM_QUICK2CART_ERROR_PAGE_NOT_FOUND'), 404);
			}

			$this->form  = $this->get('Form');

			// Get taxprofile_id
			$taxprofile_id = $app->input->get('id', 0);

			// Getting saved tax rules.
			if (!empty($taxprofile_id))
			{
				$this->taxrules = $model->getTaxRules($taxprofile_id);
			}

			// Check whether user is authorized for this zone ?
			if (!empty($this->item->store_id))
			{
				$status = $comquick2cartHelper->store_authorize('taxprofileform_default', $this->item->store_id);

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
				$this->storeDetails = $comquick2cartHelper->getSoreInfo($this->item->store_id);

				// Getting tax rates and Adress types
				$this->taxrate = $model->getTaxRateListSelect($this->item->store_id, '');
				$this->address = $model->getAddressList();
			}

			// Check for errors.
			if (count($errors = $this->get('Errors')))
			{
				throw new Exception(implode("\n", $errors));
			}
		}
		else
		{
			$this->taxRule_id = $jinput->get('id');
			$defaultTaxRateId = '';
			$defaultAddressId = '';

			// Getting saved tax rules.
			if (!empty($this->taxRule_id))
			{
				$this->taxrules = $model->getTaxRules('', $this->taxRule_id);

				if (!empty($this->taxrules))
				{
					$defaultTaxRateId = $this->taxrules[0]->taxrate_id;
					$defaultAddressId = $this->taxrules[0]->address;
				}

				// Get store id of taxrule
				$taxHelper = new taxHelper;
				$store_id  = $taxHelper->getStoreIdFromTaxrule($this->taxRule_id);

				if (empty($store_id))
				{
					$this->qtcStoreNotFoundMsg();
				}

				// Getting tax rates and Adress types
				$this->taxrate = $model->getTaxRateListSelect($store_id, $defaultTaxRateId);
				$this->address = $model->getAddressList($defaultAddressId);
			}
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Function To Prepare Document
	 *
	 * @return  void
	 *
	 * @since   2.7
	 */
	protected function _prepareDocument()
	{
		$app   = Factory::getApplication();
		$menus = $app->getMenu();
		$title = null;

		// Because the application sets a default page title,
		// we need to get it from the menu item itself
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
}
