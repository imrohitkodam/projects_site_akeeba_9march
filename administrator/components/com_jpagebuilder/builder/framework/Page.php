<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing page list
 */
trait JPageBuilderFrameworkPage {
	public function pages() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'DELETE'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->getPageList ();
				break;
			case 'PUT' :
				$this->savePage ();
				break;
			case 'PATCH' :
				$this->applyBulkActions ();
				break;
			case 'POST' :
				$this->createPage ();
				break;
		}
	}
	public function getPageList() {
		$pageData = ( object ) [ 
				'limit' => $this->getInput ( 'limit', 10, 'int' ),
				'offset' => $this->getInput ( 'offset', 0, 'int' ),
				'search' => $this->getInput ( 'search', '', 'string' ),
				'sortBy' => $this->getInput ( 'sortBy', '', 'string' ),
				'access' => $this->getInput ( 'access', '', 'string' ),
				'category' => $this->getInput ( 'category', 0, 'int' ),
				'language' => $this->getInput ( 'language', '', 'string' ),
				'status' => $this->getInput ( 'status', '', 'string' ),
				'extension' => 'com_jpagebuilder',
				'extension_view' => $this->getInput ( 'page_type', 'page', 'string' )
		];

		$model = $this->getModel ( 'Editor' );
		$response = $model->getPages ( $pageData );

		if (is_array ( $response->results ) && count ( $response->results ) > 0) {
			foreach ( $response->results as $key => $page ) {
				$page->permissions = $this->getPagePermissions ( $page->id );
			}
		}

		$this->sendResponse ( $response, $response->code );
	}
	public function getPagePermissions($pageId) {
		$model = $this->getModel ( 'Appconfig' );

		return $model->getUserPermissions ( $pageId );
	}
	public function savePage() {
		$model = $this->getModel ( 'Editor' );

		$id = $this->getInput ( 'id', 0, 'int' );
		$title = $this->getInput ( 'title', '', 'string' );
		$text = $this->getInput ( 'text', '[]', 'RAW' );
		$published = $this->getInput ( 'published', 0, 'int' );
		$language = $this->getInput ( 'language', '*', 'string' );
		$catid = $this->getInput ( 'catid', 0, 'int' );
		$access = $this->getInput ( 'access', 1, 'int' );
		$attributes = $this->getInput ( 'attribs', '', 'string' );
		$openGraphTitle = $this->getInput ( 'og_title', '', 'string' );
		$openGraphDescription = $this->getInput ( 'og_description', '', 'string' );
		$openGraphImage = $this->getInput ( 'og_image', '', 'string' );
		$customCss = $this->getInput ( 'css', '', 'RAW' );
		$version = JpagebuilderHelper::getVersion ();

		$pageCreator = $model->getPageCreator ( $id );

		$user = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise ( 'core.edit', 'com_jpagebuilder' );
		$canEditOwn = $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );
		$canEditState = $user->authorise ( 'core.edit.state', 'com_jpagebuilder' );

		$canEditPage = $canEdit || ($canEditOwn && $user->id === $pageCreator);

		if (! $canEditPage) {
			$this->sendResponse ( [ 
					'message' => Text::_ ( 'COM_JPAGEBUILDER_EDITOR_INVALID_EDIT_ACCESS' )
			], 403 );
		}

		$content = ! empty ( $text ) ? $text : '[]';
		$content = json_encode ( json_decode ( $content ) );

		$data = [ 
				'id' => $id,
				'title' => $title,
				// 'content' => !empty($text) ? JpagebuilderEditorUtils::cleanXSS($text) : '[]',
				'content' => $content,
				'published' => $published,
				'language' => $language,
				'catid' => $catid,
				'access' => $access,
				'attribs' => $attributes,
				'og_title' => $openGraphTitle,
				'og_description' => $openGraphDescription,
				'og_image' => $openGraphImage,
				'css' => $customCss ?? '',
				'version' => $version,
				'modified' => Factory::getDate ()->toSql (),
				'modified_by' => $user->id
		];

		if (! $canEditState) {
			unset ( $data ['published'] );
		}

		try {
			$model->savePage ( $data );
		} catch ( Exception $error ) {
			$this->sendResponse ( [ 
					'message' => $error->getMessage ()
			], 500 );
		}

		$this->sendResponse ( $id );
	}
	public function applyBulkActions() {
		$params = ( object ) [ 
				'ids' => $this->getInput ( 'ids', '', 'string' ),
				'type' => $this->getInput ( 'type', '', 'string' ),
				'value' => $this->getInput ( 'value', '', 'string' )
		];

		$user = Factory::getApplication()->getIdentity();
		$canEditState = $user->authorise ( 'core.edit.state', 'com_jpagebuilder' );
		$canDelete = $user->authorise ( 'core.delete', 'com_jpagebuilder' );

		$stateTypes = [ 
				'published',
				'unpublished',
				'check-in',
				'rename'
		];
		$deleteTypes = [ 
				'trash',
				'delete'
		];

		if (in_array ( $params->type, $stateTypes ) && ! $canEditState) {
			$this->sendResponse ( [ 
					'message' => Text::_ ( 'COM_JPAGEBUILDER_EDITOR_INVALID_EDIT_STATE_ACCESS' )
			], 403 );
		}

		if (in_array ( $params->type, $deleteTypes ) && ! $canDelete) {
			$this->sendResponse ( [ 
					'message' => Text::_ ( 'COM_JPAGEBUILDER_EDITOR_INVALID_DELETE_STATE' )
			], 403 );
		}

		$model = $this->getModel ( 'Editor' );
		$response = $model->applyBulkActions ( $params );

		$this->sendResponse ( $response );
	}
	public function createPage() {
		$title = $this->getInput ( 'title', '', 'string' );
		$type = $this->getInput ( 'type', '', 'string' );

		$model = $this->getModel ( 'Editor' );
		$data = [ ];
		$user = Factory::getApplication()->getIdentity();
		$version = JpagebuilderHelper::getVersion ();

		$user = Factory::getApplication()->getIdentity();
		$canCreate = $user->authorise ( 'core.create', 'com_jpagebuilder' );

		if (! $canCreate) {
			$this->sendResponse ( [ 
					'message' => Text::_ ( 'COM_JPAGEBUILDER_EDITOR_INVALID_CREATE_ACCESS' )
			], 403 );
		}

		$extension = 'com_jpagebuilder';
		$extensionView = 'page';

		if (! empty ( $type )) {
			if ($type === 'popup') {
				$extension = 'com_jpagebuilder';
				$extensionView = 'popup';
			}
		}

		$data = [ 
				'id' => 0,
				'title' => $title,
				'text' => '[]',
				'css' => '',
				'catid' => 0,
				'language' => '*',
				'access' => 1,
				'published' => 1,
				'extension' => $extension,
				'extension_view' => $extensionView,
				'created_on' => Factory::getDate ()->toSql (),
				'created_by' => $user->id,
				'modified' => Factory::getDate ()->toSql (),
				'version' => $version
		];

		$result = $model->createPage ( $data );

		if (! empty ( $result ['message'] )) {
			$this->sendResponse ( $result, 500 );
		}

		$response = ( object ) [ 
				'id' => $result
		];

		$this->sendResponse ( $response, 201 );
	}
	public function isStorePageExist($extension, $view) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'id' )->from ( $db->quoteName ( '#__jpagebuilder' ) )->where ( $db->quoteName ( 'extension' ) . ' = ' . $db->quote ( $extension ) )->where ( $db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( $view ) );
		$db->setQuery ( $query );

		try {
			return $db->loadResult ();
		} catch ( Exception $error ) {
			return false;
		}

		return false;
	}
}
