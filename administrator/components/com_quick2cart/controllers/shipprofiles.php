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
defined('_JEXEC') or die;
use Joomla\CMS\Factory;

// Load Quick2cart Controller for list views
require_once __DIR__ . '/q2clist.php';

/**
 * Shipprofiles list controller class.
 *
 * @since  2.2
 */
class Quick2cartControllerShipprofiles extends  Quick2cartControllerQ2clist
{
	/**
	 * construcor.
	 *
	 * @param   ARRAY  $config  config
	 *
	 * @since 2.2
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		$lang = Factory::getLanguage();
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   STRING  $name    model name
	 * @param   STRING  $prefix  model prefix
	 *
	 * @since	1.6
	 *
	 * @return  model object
	 */
	public function getModel($name = 'Shipprofiles', $prefix = 'Quick2cartModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
