<?php
/**
 * @version    SVN: <svn_id>
 * @package    quick2cart
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;

/**
 * View for Categories
 *
 * @package     quick2cart
 * @subpackage  component
 * @since       1.0
 */
class Quick2cartViewCategories extends HtmlView
{
	protected $items;

	protected $pagination;

	protected $state;

	protected $params;

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
		$this->state         = $this->get('State');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$app  = Factory::getApplication();
		$user = Factory::getUser();
		$menus = $app->getMenu();
		$model = $this->getModel('categories');
		$this->params        = $app->getParams('com_quick2cart');
		$this->items         = $this->get('Items');
		$menuParams = $app->getParams('com_quick2cart');
		$show_child_categories = $menuParams->get('show_child_categories');

		$comquick2cartHelper = new comquick2cartHelper;
		$this->itemId  = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=categories&layout=default');

		if (!$show_child_categories)
		{
			$catId       = $app->input->get('cat_id', '', 'INT');
			if (empty($this->items) && $catId)
			{
				// Load helper file if not exist
				if (!class_exists('comquick2cartHelper'))
				{
					$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
					JLoader::register('comquick2cartHelper', $path);
					JLoader::load('comquick2cartHelper');
				}

				$comquick2cartHelper = new comquick2cartHelper;
				$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cart');
				$app->redirect(Route::_('index.php?option=com_quick2cart&view=category&layout=default&prod_cat=' . $catId . '&Itemid=' . $itemId, false));
			}
		}

		parent::display($tpl);
	}
}
