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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\MVC\Controller\BaseController;

/**
 * JPageBuilder Base Controller class
 *
 * @since 1.0.0
 */
class JpagebuilderController extends BaseController {
	/**
	 * Display function
	 *
	 * @param boolean $cachable
	 * @param boolean $urlparams
	 * @return void
	 * @since 1.0.0
	 */
	public function display($cachable = false, $urlparams = false) {
		$apps = Factory::getApplication ();
		$viewStatus = false;

		$id = $this->input->getInt ( 'id' );
		$vName = $this->input->getCmd ( 'view' );

		$validViewNames = [ 
				'page',
				'form',
				'ajax',
				'media',
				'systemeditor'
		];
		$viewStatus = \in_array ( $vName, $validViewNames );

		if (! $viewStatus) {
			throw new Exception ( Text::_ ( 'COM_JPAGEBUILDER_ERROR_PAGE_NOT_FOUND' ), 404 );
		}

		$this->input->set ( 'view', $vName );

		if ($vName == 'page') {
			$cachable = true;
		}

		$safeURLParams = array (
				'catid' => 'INT',
				'id' => 'INT',
				'cid' => 'ARRAY',
				'return' => 'BASE64',
				'print' => 'BOOLEAN',
				'lang' => 'CMD',
				'Itemid' => 'INT'
		);

		$user = Factory::getApplication()->getIdentity();
		$isIgnoreView = ($this->input->getMethod () === 'POST' && (($vName === 'form' && ($this->input->get ( 'layout' ) !== 'edit') || $this->input->get ( 'layout' ) !== 'edit-iframe')));

		if ($user->get ( 'id' ) || $isIgnoreView) {
			$cachable = false;
		}

		if ($vName === 'page') {
			$model = $this->getModel ( $vName );
			$model->hit ();
		}

		parent::display ( $cachable, $safeURLParams );
	}

	/**
	 * Export template layout.
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function export() {
		$input = Factory::getApplication ()->getInput();

		$pageId = $input->get ( 'pageId', '', 'string' );
		$isSeoChecked = $input->get ( 'isSeoChecked', '', 'string' );

		// check have access
		$user = Factory::getApplication()->getIdentity();
		$canEdit = $user->authorise ( 'core.edit', 'com_jpagebuilder' );

		$canEditOwn = $user->authorise ( 'core.edit.own', 'com_jpagebuilder' );

		if ($canEditOwn) {
			require_once (JPATH_ROOT . '/administrator/components/com_jpagebuilder/models/page.php');
			$item_info = JpagebuilderModelPage::getPageInfoById ( $pageId );
			$canEditOwn = $item_info->created_by == $user->id;
		}

		if (! $canEdit && ! $canEditOwn) {
			die ( 'Restricted Access' );
		}

		$model = $this->getModel ( 'page' );
		$content = $model->getItem ( $pageId );

		if (empty ( $content )) {
			die ( 'Requesting page not found!' );
		}

		$content = JpagebuilderApplicationHelper::preparePageData ( $content );

		$seoSettings = [ ];

		$decodedAttribs = isset ( $content->attribs ) ? json_decode ( $content->attribs ) : null;

		if ($isSeoChecked) {
			$seoSettings = [ 
					'og_description' => isset ( $content->og_description ) ? $content->og_description : '',
					'og_image' => '',
					'og_title' => isset ( $content->og_title ) ? $content->og_title : '',
					'meta_title' => isset ( $decodedAttribs ) && isset ( $decodedAttribs->meta_title ) ? $decodedAttribs->meta_title : '',
					'meta_description' => isset ( $decodedAttribs ) && isset ( $decodedAttribs->meta_description ) ? $decodedAttribs->meta_description : '',
					'meta_keywords' => isset ( $decodedAttribs ) && isset ( $decodedAttribs->meta_keywords ) ? $decodedAttribs->meta_keywords : '',
					'og_type' => isset ( $decodedAttribs ) && isset ( $decodedAttribs->og_type ) ? $decodedAttribs->og_type : '',
					'robots' => isset ( $decodedAttribs ) && isset ( $decodedAttribs->robots ) ? $decodedAttribs->robots : '',
					'seo_spacer' => isset ( $decodedAttribs ) && isset ( $decodedAttribs->seo_spacer ) ? $decodedAttribs->seo_spacer : ''
			];
		}

		$pageContent = ( object ) [ 
				'template' => isset ( $content->content ) ? $content->content : $content->text,
				'css' => isset ( $content->css ) ? $content->css : '',
				'seo' => json_encode ( $seoSettings ),
				'title' => $content->title,
				'language' => isset ( $content->language ) ? $content->language : '*'
		];

		$filename = 'template' . rand ( 10000, 99999 ) . '.json';
		$filename = strlen ( $filename ) <= PHP_MAXPATHLEN ? $filename : 'template' . JpagebuilderHelperSite::nanoid ( 6 ) . '.json';

		header ( "Pragma: public" );
		header ( "Expires: 0" );
		header ( "Cache-Control: must-revalidate, post-check=0, pre-check=0" );
		header ( "Content-Type: application/force-download" );
		header ( "Content-Type: application/octet-stream" );
		header ( "Content-Type: application/download" );
		header ( "Content-Disposition: attachment;filename=$filename" );
		header ( "Content-Type: application/json" );
		header ( "Content-Transfer-Encoding: binary " );

		echo json_encode ( $pageContent );
		die ();
	}

	/**
	 * AJAX function
	 *
	 * @return void
	 * @since 1.0.0
	 */
	public function ajax() {
		$app = Factory::getApplication ();
		$input = $app->getInput();
		$format = ($input->getWord ( 'format' )) ? strtolower ( $input->getWord ( 'format' ) ) : '';
		$results = null;
		$addon = $input->get ( 'addon', '', 'string' );

		if ($addon) {
			$function = 'jp_' . $addon . '_get_ajax';
			$addon_class = JpagebuilderApplicationHelper::generateSiteClassName ( $addon );
			$method = $input->get ( 'method', 'get', 'string' );

			require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/addonparser.php';

			$core_path = JPATH_ROOT . '/components/com_jpagebuilder/addons/' . $input->get ( 'addon' ) . '/block.php';
			$template_path = JPATH_ROOT . '/templates/' . JpagebuilderHelperSite::getTemplateName () . '/jpagebuilder/addons/' . $input->get ( 'addon' ) . '/block.php';

			if (file_exists ( $template_path )) {
				require_once $template_path;
			} else {
				require_once $core_path;
			}

			if (class_exists ( $addon_class )) {

				if (method_exists ( $addon_class, $method . 'Ajax' )) {
					try {
						$results = call_user_func ( $addon_class . '::' . $method . 'Ajax' );
					} catch ( Exception $e ) {
						$results = $e;
					}
				} else {
					$results = new LogicException ( Text::sprintf ( 'COM_AJAX_METHOD_NOT_EXISTS', $method . 'Ajax' ), 404 );
				}
			} else {
				if (function_exists ( $function )) {
					try {
						$results = call_user_func ( $function );
					} catch ( Exception $e ) {
						$results = $e;
					}
				} else {
					$results = new LogicException ( Text::sprintf ( 'Function %s does not exist', $function ), 404 );
				}
			}
		}

		echo new JsonResponse ( $results, null, false, $input->get ( 'ignoreMessages', true, 'bool' ) );
		die ();
	}
}
