<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die();

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

// Import the list field type
jimport('joomla.form.helper');
FormHelper::loadFieldClass('list');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

/**
 * Form field for Quick2cart
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class JFormFieldQuick2cart extends JFormFieldList
{
	/**
	 * The field type.
	 *
	 * @var string
	 */
	protected $type = 'Quick2cart';

	/**
	 * [getInput description]
	 *
	 * @return  [type]  [description]
	 */
	public function getInput()
	{
		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		$path = JPATH_SITE . '/components/com_quick2cart/helper.php';

		if (! class_exists('comquick2cartHelper'))
		{
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$fieldName           = $this->fieldname;
		$html                = '';
		$client              = "com_content";
		$app                 = Factory::getApplication();
		$jinput              = $app->input;
		$isAdmin             = $app->isClient('administrator');
		$pid                 = (!$isAdmin) ? $jinput->get('a_id') : $jinput->get('id');

		if ($pid)
		{
			/* If someone has already created the article and admin is adding product details the the product
			should be owned by the content creator */
			JLoader::import('components.com_content.models.article', JPATH_ADMINISTRATOR);
			$contentModel  = BaseDatabaseModel::getInstance('Article', 'ContentModel');
			$contentDetail = $contentModel->getItem($pid);
			$owner         = $contentDetail->created_by;
		}

		// For admin, no need of bs-3 layout. Check override in admin template if not present then take from site->com_quick2cart->layout
		if ($isAdmin)
		{
			if (JVERSION < '4.0.0')
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs2', 'JPATH_ADMINISTRATOR', 'JPATH_ADMINISTRATOR');
			}
			else
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs5', 'JPATH_ADMINISTRATOR', 'JPATH_ADMINISTRATOR');
			}
		}
		else
		{
			if (JVERSION < '4.0.0')
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs3', 'SITE', 'SITE');
			}
			else
			{
				$path = $comquick2cartHelper->getViewpath('attributes', 'default_bs5', 'SITE', 'SITE');
			}
		}

		BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_quick2cart/models');
		$Quick2cartModelWeights = BaseDatabaseModel::getInstance('Weights', 'Quick2cartModel');
		$Quick2cartModelLengths = BaseDatabaseModel::getInstance('Lengths', 'Quick2cartModel');

		$this->weightClasses = $Quick2cartModelWeights->getItems();
		$this->lengthClasses = $Quick2cartModelLengths->getItems();

		ob_start();
		include $path;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}
