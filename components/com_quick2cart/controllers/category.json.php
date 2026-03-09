<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 * Category controller class
 *
 * @since  __DEPLOY_VERSION__
 */
class Quick2cartControllerCategory extends Quick2cartController
{
	/**
	 * This method returns the closest matching product details
	 *
	 * @return  json
	 *
	 * since __DEPLOY_VERSION__
	 */
	public function getProductData()
	{
		$result        = array();
		$input         = Factory::getApplication()->input;
		$filterSearch  = $input->post->get('filter_search', '', 'String');
		$listLimit     = $input->post->get('list_limit', 10, 'Integer');

		if (!empty($filterSearch))
		{
			$categoryModel = BaseDatabaseModel::getInstance('category', 'Quick2cartModel', array('ignore_request' => true));
			$categoryModel->setState('filter.search', $filterSearch);
			$categoryModel->setState('list.limit', $listLimit);
			$result = $categoryModel->getItems();
		}

		echo json_encode($result);
		jexit();
	}
}
