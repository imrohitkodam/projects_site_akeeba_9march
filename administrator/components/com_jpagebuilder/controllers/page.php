<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\File;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\ArrayHelper;
use Joomla\CMS\MVC\Controller\FormController;

require_once (JPATH_ROOT . '/components/com_jpagebuilder/helpers/route.php');
class JpagebuilderControllerPage extends FormController {
	public function __construct($config = array ()) {
		parent::__construct ( $config );
	}

	/**
	 * Get the page model
	 *
	 * @param string $name
	 * @param string $prefix
	 * @param array $config
	 * @return JpagebuilderModelPage
	 */
	public function getModel($name = 'form', $prefix = '', $config = array (
			'ignore_request' => true
	)) {
		$model = parent::getModel ( $name, $prefix, $config );

		return $model;
	}
	protected function allowAdd($data = array ()) {
		$categoryId = ArrayHelper::getValue ( $data, 'catid', $this->getInput ()->getInt ( 'filter_category_id' ), 'int' );
		$allow = null;
		if ($categoryId) {
			// If the category has been passed in the data or URL check it.
			$user = Factory::getApplication ()->getIdentity ();
			$allow = $user->authorise ( 'core.create', 'com_jpagebuilder.category.' . $categoryId );
		}
		if ($allow === null) {
			// In the absense of better information, revert to the component permissions.
			return parent::allowAdd ();
		}
		return $allow;
	}
	protected function allowEdit($data = array (), $key = 'id') {
		$recordId = ( int ) isset ( $data [$key] ) ? $data [$key] : 0;
		$user = Factory::getApplication ()->getIdentity ();
		// Zero record (id:0), return component edit permission by calling parent controller method
		if (! $recordId) {
			return parent::allowEdit ( $data, $key );
		}
		// Check edit on the record asset (explicit or inherited)
		if ($user->authorise ( 'core.edit', 'com_jpagebuilder.page.' . $recordId )) {
			return true;
		}
		// Check edit own on the record asset (explicit or inherited)
		if ($user->authorise ( 'core.edit.own', 'com_jpagebuilder.page.' . $recordId )) {
			// Existing record already has an owner, get it
			$record = $this->getModel ()->getItem ( $recordId );
			if (empty ( $record )) {
				return false;
			}
			// Grant if current user is owner of the record
			return $user->id == $record->created_by;
		}
		return false;
	}
	public function save($key = null, $urlVar = null) {
		$output = array ();
		// Check for request forgeries.
		$output ['status'] = false;
		$output ['message'] = Text::_ ( 'JINVALID_TOKEN' );
		Session::checkToken () or die ( json_encode ( $output ) );

		/** @var CMSApplication */
		$app = Factory::getApplication ();
		$user = $app->getIdentity ();
		$model = $this->getModel ();
		$data = $this->getInput ()->post->get ( 'jform', array (), 'Array' );
		$task = $this->getTask ();
		$context = 'com_jpagebuilder.edit.page';
		$recordId = isset ( $data ['id'] ) ? $data ['id'] : 0;
		$isNew = ($recordId == 0) ? true : false;

		$table = $model->getTable ();

		$table->bind ( $data );

		if (! $table->check ()) {
			$output ['status'] = false;
			$output ['message'] = Text::_ ( 'JLIB_CMS_WARNING_PROVIDE_VALID_NAME' );
			echo json_encode ( $output );
			die ();
		}

		// Pass text if empty
		$data ['text'] = isset ( $data ['text'] ) ? $data ['text'] : '';

		// Authorized
		if (empty ( $recordId )) {
			$authorised = $user->authorise ( 'core.create', 'com_jpagebuilder' ) || (count ( ( array ) $user->getAuthorisedCategories ( 'com_jpagebuilder', 'core.create' ) ));
		} else {
			$authorised = $user->authorise ( 'core.edit', 'com_jpagebuilder' ) || $user->authorise ( 'core.edit', 'com_jpagebuilder.page.' . $recordId ) || $user->authorise ( 'core.edit', 'com_jpagebuilder.page.' . $recordId ) || ($user->authorise ( 'core.edit.own', 'com_jpagebuilder.page.' . $recordId ) && $data ['created_by'] == $user->id);
		}

		if ($authorised !== true) {
			$output ['status'] = false;
			$output ['message'] = Text::_ ( 'JERROR_ALERTNOAUTHOR' );
			echo json_encode ( $output );
			die ();
		}

		$output ['status'] = true;
		$output ['new'] = $isNew;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy') {
			// Check-in the original row.
			if ($model->checkin ( $data ['id'] ) === false) {
				// Check-in failed, go back to the item and display a notice.
				$output ['status'] = false;
				$output ['message'] = Text::sprintf ( 'JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError () );
				echo json_encode ( $output );
				die ();
			}

			// Reset the ID and then treat the request as for Apply.
			$output ['title'] = $model->pageGenerateNewTitle ( $data ['title'] );
			$data ['id'] = 0;
			$task = 'apply';
		}

		// Validate the posted data.
		// This post is made up of two forms, one for the item and one for params.
		$form = $model->getForm ( $data );

		if (! $form) {
			$output ['status'] = false;
			$output ['message'] = $model->getError ();
			$output ['redirect'] = false;
			echo json_encode ( $output );
			die ();
		}

		$data = $model->validate ( $form, $data );

		// Check for validation errors.
		if ($data === false) {
			// Get the validation messages.
			$errors = $model->getErrors ();

			$output ['status'] = false;
			$output ['message'] = '';

			// Push up to three validation messages out to the user.
			for($i = 0, $n = count ( ( array ) $errors ); $i < $n && $i < 3; $i ++) {
				if ($errors [$i] instanceof Exception) {
					$output ['message'] .= $errors [$i]->getMessage ();
				} else {
					$output ['message'] .= $errors [$i];
				}
			}

			// Save the data in the session.
			$app->setUserState ( 'com_jpagebuilder.edit.page.data', $data );

			// Redirect back to the edit screen.
			$output ['redirect'] = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend ( $recordId );
			echo json_encode ( $output );
			die ();
		}

		// Attempt to save the data.
		if (! $model->save ( $data )) {

			// Save the data in the session.
			$app->setUserState ( 'com_jpagebuilder.edit.page.data', $data );

			// Redirect back to the edit screen.
			$output ['status'] = false;
			$output ['message'] = Text::sprintf ( 'JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError () );
			$output ['redirect'] = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend ( $recordId );
			echo json_encode ( $output );
			die ();
		}

		// Save succeeded, check-in the row.
		if ($model->checkin ( $data ['id'] ) === false) {

			// Check-in failed, go back to the row and display a notice.
			$output ['status'] = false;
			$output ['message'] = Text::sprintf ( 'JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError () );
			$output ['redirect'] = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend ( $recordId );
			echo json_encode ( $output );
			die ();
		}

		$output ['status'] = true;
		$output ['message'] = Text::_ ( 'COM_JPAGEBUILDER_PAGE_SAVE_SUCCESS' );

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply' :
				// Set the row data in the session.
				$recordId = $model->getState ( $this->context . '.id' );
				$this->holdEditId ( $context, $recordId );
				$app->setUserState ( 'com_jpagebuilder.edit.page.data', null );

				// Delete generated CSS file
				$css_folder_path = JPATH_ROOT . '/media/com_jpagebuilder/css';
				$css_file_path = $css_folder_path . '/page-' . $recordId . '.css';
				if (file_exists ( $css_file_path )) {
					File::delete ( $css_file_path );
				}

				// Redirect back to the edit screen.
				$output ['redirect'] = 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend ( $recordId );

				// Language
				$lang_code = (isset ( $data ['language'] ) && $data ['language'] && explode ( '-', $data ['language'] ) [0]) ? explode ( '-', $data ['language'] ) [0] : '';

				$output ['preview_url'] = JpagebuilderHelperRoute::getPageRoute ( $recordId, $lang_code );
				$output ['frontend_editor_url'] = JpagebuilderHelperRoute::getFormRoute ( $recordId, $lang_code );

				$output ['id'] = $recordId;

				break;

			default :
				// Clear the row id and data in the session.
				$this->releaseEditId ( $context, $recordId );
				$app->setUserState ( 'com_jpagebuilder.edit.page.data', null );

				// Redirect to the list screen.
				$output ['redirect'] = Route::_ ( 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend (), false );
				break;
		}

		if (isset ( $output ['id'] ) && $output ['id']) {
			$css_file_path = JPATH_ROOT . "/media/com_jpagebuilder/css/page-{$output['id']}.css";
			if (file_exists ( $css_file_path )) {
				unlink ( $css_file_path );
			}
		}

		echo json_encode ( $output );
		die ();
	}
	public function getMySections() {
		/** @var JpagebuilderModelPage */
		$model = $this->getModel ();
		die ( $model->getMySections () );
	}
	public function deleteSection() {
		/** @var JpagebuilderModelPage */
		$model = $this->getModel ();
		$app = Factory::getApplication ();
		$input = $app->getInput ();

		$id = $input->get ( 'id', '', 'int' );

		die ( $model->deleteSection ( $id ) );
	}
	public function saveSection() {
		/** @var JpagebuilderModelPage */
		$model = $this->getModel ();
		$app = Factory::getApplication ();
		$input = $app->getInput ();

		$title = htmlspecialchars ( $input->get ( 'title', '', 'string' ) );
		$section = $input->get ( 'section', '', 'raw' );

		if ($title && $section) {
			$section_id = $model->saveSection ( $title, $section );
			echo $section_id;
			die ();
		} else {
			die ( 'Failed' );
		}
	}
	public function getMyAddons() {
		$model = $this->getModel ();
		die ( $model->getMyAddons () );
	}
	public function saveAddon() {
		$model = $this->getModel ();
		$app = Factory::getApplication ();
		$input = $app->getInput ();

		$title = htmlspecialchars ( $input->get ( 'title', '', 'string' ) );
		$addon = $input->get ( 'addon', '', 'raw' );

		if ($title && $addon) {
			$addon_id = $model->saveAddon ( $title, $addon );
			echo $addon_id;
			die ();
		} else {
			die ( 'Failed' );
		}
	}
	public function deleteAddon() {
		$model = $this->getModel ();
		$app = Factory::getApplication ();
		$input = $app->getInput ();

		$id = $input->get ( 'id', '', 'int' );

		die ( $model->deleteAddon ( $id ) );
	}
	public function createNew() {
		$pageId = 0;
		$model = $this->getModel ( 'Page' );
		$output = array ();
		$output ['status'] = false;
		$app = Factory::getApplication ();
		$input = $app->getInput ();

		$user = Factory::getApplication ()->getIdentity ();
		$authorised = $user->authorise ( 'core.create', 'com_jpagebuilder' );

		if (! $authorised) {
			$output ['message'] = Text::_ ( 'JERROR_ALERTNOAUTHOR' );
			die ( json_encode ( $output ) );
		}

		$title = trim ( htmlspecialchars ( $input->post->get ( 'title', '', 'string' ) ) );
		$extension = htmlspecialchars ( $input->post->get ( 'extension', '', 'string' ) );
		$extension_view = htmlspecialchars ( $input->post->get ( 'extension_view', '', 'string' ) );
		$view_id = $input->post->get ( 'view_id', 0, 'int' );
		$editor = $input->post->get ( 'editor', '', 'string' );

		if ($view_id && $title) {
			$id = $model->createBrandNewPage ( $title, $extension, $extension_view, $view_id );
			$pageId = $id;

			$front_link = 'index.php?option=com_jpagebuilder&view=form&tmpl=component&layout=edit&extension=' . $extension . '&extension_view=' . $extension_view . '&id=' . $pageId;
			$backend_link = 'index.php?option=com_jpagebuilder&view=editor&tmpl=component&extension=' . $extension . '&extension_view=' . $extension_view . '#/editor/' . $pageId;

			$sefURI = ($editor === 'front') ? str_replace ( '/administrator', '', JpagebuilderHelperRoute::buildRoute ( $front_link ) ) : $backend_link;

			$output ['status'] = true;
			$output ['url'] = $sefURI;
			die ( json_encode ( $output ) );
		}

		die ( json_encode ( $output ) );
	}
	public function module_save() {
		$pageId = 0;
		$model = $this->getModel ( 'Page' );
		$output = array ();
		$output ['status'] = false;
		$app = Factory::getApplication ();
		$input = $app->getInput ();

		$user = Factory::getApplication ()->getIdentity ();
		$authorised = $user->authorise ( 'core.create', 'com_jpagebuilder' );

		if (! $authorised) {
			$output ['message'] = Text::_ ( 'JERROR_ALERTNOAUTHOR' );
			die ( json_encode ( $output ) );
		}

		$id = ( int ) $input->post->get ( 'id', '', 'int' );
		$title = trim ( htmlspecialchars ( $input->post->get ( 'title', '', 'string' ) ) );
		$content = $input->post->get ( 'content', '[]', 'RAW' );

		if ($id && $title) {
			if ($view_id = $model->get_module_page_data ( $id )) {
				$model->update_module_data ( $view_id, $id, $title, $content );
			} else {
				$model->save_module_data ( $id, $title, $content );
			}
			$output ['status'] = true;
			die ( json_encode ( $output ) );
		}

		$output ['message'] = 'Error';
		die ( json_encode ( $output ) );
	}
}
