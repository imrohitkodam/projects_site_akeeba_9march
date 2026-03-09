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
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\String\StringHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;

if (! \class_exists ( 'JpagebuilderEditorUtils' )) {
	require_once __DIR__ . './../builder/helpers/EditorUtils.php';
}
require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/tables/addon.php');

class JpagebuilderModelAddon extends AdminModel {
	public function __construct($config = [], ?MVCFactoryInterface $factory = null, ?FormFactoryInterface $formFactory = null) {
		parent::__construct ( $config );
		
		$app = Factory::getApplication();
		$dispatcher = $app->getDispatcher();
		$this->setDispatcher($dispatcher);
	}

	/**
	 * Method for getting a form.
	 *
	 * @param array $data
	 *        	Data for the form.
	 * @param bool $loadData
	 *        	True if the form is to load its own data (default case), false if not.
	 * @return void
	 */
	public function getForm($data = array (), $loadData = true) {
	}
	public function getTable($name = 'Addon', $prefix = 'JpagebuilderTable', $options = array ()) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$addonTable = new JpagebuilderTableAddon($db);
		return $addonTable;
	}
}
