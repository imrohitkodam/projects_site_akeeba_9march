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
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;

require_once JPATH_ADMINISTRATOR . '/components/com_k2/elements/base.php';

/**
 * Form field for Quick2cart
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class K2ElementQuick2cart extends K2Element
{
	/**
	 * [fetchElement description]
	 *
	 * @param   [type]  $name          [description]
	 * @param   [type]  $value         [description]
	 * @param   [type]  $node          [description]
	 * @param   [type]  $control_name  [description]
	 *
	 * @return  [type]                 [description]
	 */
	function fetchElement ($name, $value, $node, $control_name)
	{
		$app    = Factory::getApplication();
		$input  = $app->input;
		$option = $input->get('option', '');

		if ($option != 'com_k2')
		{
			return;
		}

		if (! File::exists(JPATH_SITE . '/components/com_quick2cart/quick2cart.php'))
		{
			return true;
		}

		$lang = Factory::getLanguage();
		$lang->load('com_quick2cart', JPATH_ADMINISTRATOR);
		HTMLHelper::_('bootstrap.renderModal', 'a.modal');
		$html = '';
		$client = "com_k2";
		$pid = Factory::getApplication()->input->get('cid');

		/* prefill k2 title */
		$db = Factory::getDBO();
		$q = "SELECT `title` FROM `#__k2_items` WHERE `id` =" . (int) $pid;
		$db->setQuery($q);
		$k2item = $db->loadResult();
		$jinput = $app->input;
		$jinput->set('qtc_article_name', $k2item);
		/* prefill k2 title */

		if (!class_exists('comquick2cartHelper'))
		{
			// Require_once $path;
			$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
			JLoader::register('comquick2cartHelper', $path);
			JLoader::load('comquick2cartHelper');
		}

		$comquick2cartHelper = new comquick2cartHelper;
		$isAdmin = $app->isClient('administrator');

		if ($isAdmin)
		{
			$path = $comquick2cartHelper->getViewpath('attributes', '', 'JPATH_ADMINISTRATOR', 'JPATH_ADMINISTRATOR');
		}
		else
		{
			$path = $comquick2cartHelper->getViewpath('attributes', '', 'SITE', 'SITE');
		}

		ob_start();
		include $path;
		$html = ob_get_contents();
		ob_end_clean();

		return $html;
	}
}

/**
 * Form field for Quick2cart
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class FormFieldQuick2cart extends K2ElementQuick2cart
{
	var $type = 'Quick2cart';
}

/**
 * Form field for Quick2cart
 *
 * @package     Com_Quick2cart
 *
 * @subpackage  site
 *
 * @since       2.2
 */
class JElementQuick2cart extends K2ElementQuick2cart
{
	var $_name = 'Quick2cart';
}
