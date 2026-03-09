<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;

/**
 * Icon Model for managing the custom icons.
 *
 * @version 4.1.0
 */
class JpagebuilderModelIcon extends ListModel {
	/**
	 * This is the __construct function.
	 *
	 * @param mixed $config
	 * @version 4.1.0
	 */
	public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?FormFactoryInterface $formFactory = null) {
		parent::__construct ( $config );
		
		$app = Factory::getApplication();
		$dispatcher = $app->getDispatcher();
		$this->setDispatcher($dispatcher);
	}

	/**
	 * Method to get an array of the result set rows from the database query where each row is an object.
	 * The array
	 * of objects can optionally be keyed by a field name, but defaults to a sequential numeric array.
	 *
	 * @return mixed The return value or null if the query failed.
	 * @version 4.1.0
	 */
	public function getAllIcons($status = null) {
		return [ ];
	}

	/**
	 * Summary of getAssetByName
	 *
	 * @param string $name
	 * @return mixed
	 * @version 4.1.0
	 */
	public function getAssetByName(string $name) {
	}

	/**
	 * Delete custom icon by ID.
	 *
	 * @param int $id
	 *        	The icon id to remove.
	 *        	
	 * @return bool True on success, false otherwise.
	 * @since 4.1.0
	 */
	public function deleteCustomIcon(int $id): bool {
	}
	public function getAssetById(int $id) {
	}
	public function changeCustomIconStatus(int $id, int $status): bool {
	}
	public function getIconList($name) {
	}
	public function getAssetProviders() {
	}
}
