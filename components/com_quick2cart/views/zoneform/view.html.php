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

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;

/**
 * View to edit
 *
 * @since  1.0
 */
class Quick2cartViewZoneform extends HtmlView
{
	protected $state;

	protected $item;

	protected $form;

	protected $params;

	/**
	 * Display the view
	 *
	 * @param   STRING  $tpl  template
	 *
	 * @return  void
	 *
	 * @throws  Exception if there is an error in the form event.
	 */
	public function display($tpl = null)
	{
		$zoneHelper          = new zoneHelper;
		$comquick2cartHelper = new comquick2cartHelper;

		// Check whether view is accessible to user
		if (!$zoneHelper->isUserAccessible('zoneform', "default", 'form'))
		{
			return;
		}

		$app         = Factory::getApplication();
		$previousId  = $app->input->get('id');
		$user        = Factory::getUser();
		$jinput      = $app->input;
		$layout      = $jinput->get('layout', '');
		$this->state = $this->get('State');
		$this->item  = $this->get('Data');

		if (empty($this->item))
		{
			throw new Exception(Text::_('COM_QUICK2CART_ERROR_PAGE_NOT_FOUND'), 404);
		}

		$this->params = ComponentHelper::getParams('com_quick2cart');
		$this->form   = $this->get('Form');
		$model        = $this->getModel('zoneform');

		if ($this->item)
		{
			// Getting countries
			$country       = $model->getCountry();
			$this->country = $country;

			// Getting zone rules
			$this->geozonerules = $model->getZoneRules();

			// Check whether user is authorized for this zone ?
			if (!empty($this->item->store_id))
			{
				$status = $comquick2cartHelper->store_authorize('zoneform_default');

				if (!$status)
				{
					$zoneHelper->showUnauthorizedMsg();

					return false;
				}
			}
		}

		// For edit zone rules
		if ($layout === 'setrule' || $layout === 'setrule_' . QUICK2CART_LOAD_BOOTSTRAP_VERSION)
		{
			$this->rule_id = $jinput->get('zonerule_id');

			// Getting zone rule detail
			$this->ruleDetail = $model->getZoneRuleDetail($this->rule_id);

			if (!empty($this->ruleDetail->country_id))
			{
				// Getting Regions from country
				$this->getRegionList = $model->getRegionList($this->ruleDetail->country_id);
			}

			// Getting countries
			$country = $model->getCountry();
			$this->country = $country;
			$app->setUserState('com_quick2cart.edit.zone.id', $previousId);
		}

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->_prepareDocument();

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 *
	 * @return null
	 */
	protected function _prepareDocument()
	{
		$app = Factory::getApplication();
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
			$this->params->def('page_heading', Text::_('COM_Q2C_DEFAULT_PAGE_TITLE'));
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
