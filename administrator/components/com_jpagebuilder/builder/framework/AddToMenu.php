<?php
use Joomla\CMS\Table\Table;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Filter\OutputFilter;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

/**
 *
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing add to menu API endpoint.
 */
trait JPageBuilderFrameworkAddToMenu {
	private function addMenuItem() {
		$model = $this->getModel ( 'Editor' );

		$pageId = $this->getInput ( 'page_id', 0, 'int' );
		$menuId = $this->getInput ( 'menu_id', 0, 'int' );
		$parentId = $this->getInput ( 'parent_id', 0, 'int' );
		$menuType = $this->getInput ( 'menu_type', 'mainmenu', 'string' );
		$title = $this->getInput ( 'title', '', 'string' );
		$alias = $this->getInput ( 'alias', OutputFilter::stringURLSafe ( $title ), 'string' );
		$menuOrdering = $this->getInput ( 'ordering', 0, 'int' );

		$componentId = ComponentHelper::getComponent ( 'com_jpagebuilder' )->id;

		$menu = $model->getMenuById ( $menuId );
		$home = (isset ( $menu->home ) && $menu->home) ? $menu->home : 0;
		$link = 'index.php?option=com_jpagebuilder&view=page&id=' . ( int ) $pageId;

		BaseDatabaseModel::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_menus/models' );
		Table::addIncludePath ( JPATH_ADMINISTRATOR . '/components/com_menus/tables' );

		$menuModel = $this->getModel ( 'Item', 'MenusModel' );

		$menuData = [ 
				'id' => ( int ) $menuId,
				'link' => $link,
				'parent_id' => ( int ) $parentId,
				'menutype' => htmlspecialchars ( $menuType ),
				'title' => htmlspecialchars ( $title ),
				'alias' => htmlspecialchars ( $alias ),
				'type' => 'component',
				'published' => 1,
				'language' => '*',
				'component_id' => $componentId,
				'menuordering' => ( int ) $menuOrdering,
				'home' => ( int ) $home
		];

		$response = new stdClass ();

		try {
			$menuModel->save ( $menuData );
			$response->data = Text::_ ( "COM_JPAGEBUILDER_SUCCESS_MSG_FOR_PAGE_ADDED" );

			$this->sendResponse ( $response );
		} catch ( Exception $e ) {
			$this->sendResponse ( [ 
					'message' => $e->getMessage ()
			], 500 );
		}
	}
	public function addToMenu() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'GET',
				'DELETE',
				'PATCH'
		], $method );

		if ($method === "PUT") {
			$this->addMenuItem ();
		}
	}
}
