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
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\Session\Session;

$traits = [ 
		'Page.php',
		'PageDuplicate.php',
		'IconsList.php',
		'ParentItems.php',
		'Integration.php',
		'SavedAddons.php',
		'SavedPresets.php',
		'SavedAddonsOrder.php',
		'PageContentById.php',
		'PageTemplate.php',
		'SavedSections.php',
		'SavedSectionsOrder.php',
		'MenuByPageId.php',
		'LayoutImport.php',
		'Addons.php',
		'AppConfig.php',
		'Settings.php',
		'Media.php',
		'AiContent.php',
		'AiContentUpload.php',
		'MediaFolder.php',
		'Icons.php',
		'IconProviders.php',
		'MenuList.php',
		'SectionLibrary.php',
		'AddToMenu.php',
		'PageOrder.php',
		'Import.php',
		'BulkImport.php',
		'Export.php',
		'BulkExport.php',
		'ApplicationSettings.php',
		'GlobalColors.php',
		'ImageShapes.php',
		'PurgeCss.php',
		'SaveIgToken.php',
		'Fonts.php',
		'UploadFont.php',
		'AllFonts.php'
];

foreach ( $traits as $trait ) {
	$filePath = dirname ( __FILE__ ) . '/../builder/framework/' . $trait;

	if (\file_exists ( $filePath )) {
		require_once $filePath;
	}
}
class JpagebuilderControllerEditor extends AdminController {
	use JPageBuilderFrameworkPage;
	use JPageBuilderFrameworkPageDuplicate;
	use JPageBuilderFrameworkIntegration;
	use JPageBuilderFrameworkParentItems;
	use JPageBuilderFrameworkIconsList;
	use JPageBuilderFrameworkPageContentById;
	use JPageBuilderFrameworkPageTemplate;
	use JPageBuilderFrameworkSavedAddons;
	use JPageBuilderFrameworkSavedPresets;
	use JPageBuilderFrameworkSavedAddonsOrder;
	use JPageBuilderFrameworkSavedSections;
	use JPageBuilderFrameworkSavedSectionsOrder;
	use JPageBuilderFrameworkMenuList;
	use JPageBuilderFrameworkMenuByPageId;
	use JPageBuilderFrameworkLayoutImport;
	use JPageBuilderFrameworkAddToMenu;
	use JPageBuilderFrameworkAddons;
	use JPageBuilderFrameworkAppConfig;
	use JPageBuilderFrameworkSettings;
	use JPageBuilderFrameworkMedia;
	use JPageBuilderFrameworkAiContent;
	use JPageBuilderFrameworkAiContentUpload;
	use JPageBuilderFrameworkMediaFolder;
	use JPageBuilderFrameworkIcons;
	use JPageBuilderFrameworkIconProviders;
	use JPageBuilderFrameworkSectionLibrary;
	use JPageBuilderFrameworkPageOrder;
	use JPageBuilderFrameworkImport;
	use JPageBuilderFrameworkBulkImport;
	use JPageBuilderFrameworkExport;
	use JPageBuilderFrameworkBulkExport;
	use JPageBuilderFrameworkApplicationSettings;
	use JPageBuilderFrameworkGlobalColors;
	use JPageBuilderFrameworkImageShapes;
	use JPageBuilderFrameworkPurgeCss;
	use JPageBuilderFrameworkSaveIgToken;
	use JPageBuilderFrameworkFonts;
	use JPageBuilderFrameworkUploadFont;
	use JPageBuilderFrameworkAllFonts;
	protected $app = null;

	/**
	 * Send JSON Response to the client.
	 * {"success":true,"message":"ok","messages":null,"data":[{"key":"value"}]}
	 *
	 * @param mixed $response
	 *        	The response array or data.
	 * @param int $statusCode
	 *        	The status code of the HTTP response.
	 *        	
	 * @return void
	 * @since 4.1.0
	 */
	private function sendResponse($response, int $statusCode = 200) {
		$this->app->setHeader ( 'Content-Type', 'application/json' );

		$this->app->setHeader ( 'status', $statusCode, true );

		$this->app->sendHeaders ();

		echo new JsonResponse ( $response );

		$this->app->close ();
	}

	/**
	 * Check given HTTP method is allowed or not
	 *
	 * @param array $notAllowedMethods
	 * @param string $method
	 * @return void
	 */
	private function checkNotAllowedMethods(array $notAllowedMethods, string $method) {
		if (in_array ( $method, $notAllowedMethods )) {
			$response ['message'] = Text::_ ( 'COM_JPAGEBUILDER_EDITOR_METHOD_NOT_ALLOWED' );
			$this->sendResponse ( $response, 405 );
		}
	}

	/**
	 * An abstraction of the $input->get() method.
	 * Here we are just checking the null, true, false values those are coming as string.
	 * If we found those values then return the respective values,
	 * otherwise return the original filtered value.
	 *
	 * @param string $name
	 *        	The request field name.
	 * @param mixed $default
	 *        	Any default value.
	 * @param string $filter
	 *        	The filter similar to the ->get() method.
	 *        	
	 * @return mixed
	 */
	private function getInput(string $name, $default = null, string $filter = 'cmd') {
		$input = Factory::getApplication ()->getInput();
		$value = $input->get ( $name );

		if (empty ( $value )) {
			return $input->get ( $name, $default, $filter );
		}

		if (is_array ( $value )) {
			return $input->get ( $name, $default, $filter );
		}

		switch (strtolower ( $value )) {
			case 'null' :
				return null;
			case 'true' :
				return 1;
			case 'false' :
				return 0;
		}

		return $input->get ( $name, $default, $filter );
	}
	private function getInputMethod() {
		$input = Factory::getApplication ()->getInput();
		$method = $input->getString ( '_method', 'GET' );

		return \strtoupper ( $method );
	}

	/**
	 * Method to decode a data array.
	 *
	 * @param array $data
	 *        	The data array to decode.
	 *        	
	 * @return array
	 *
	 * @since 1.0
	 */
	protected function decodeData(array $data) {
		$result = [ ];

		if (\is_array ( $data [0] )) {
			foreach ( $data [0] as $k => $_ ) {
				$result [$k] = $this->decodeData ( [ 
						$data [0] [$k],
						$data [1] [$k],
						$data [2] [$k],
						$data [3] [$k],
						$data [4] [$k]
				] );
			}

			return $result;
		}

		return [ 
				'name' => $data [0],
				'type' => $data [1],
				'tmp_name' => $data [2],
				'error' => $data [3],
				'size' => $data [4]
		];
	}
	public function getFilesInput($name, $default = null, $filter = 'cmd') {
		$data = $_FILES;

		if (isset ( $data [$name] )) {
			$results = $this->decodeData ( [ 
					$data [$name] ['name'],
					$data [$name] ['type'],
					$data [$name] ['tmp_name'],
					$data [$name] ['error'],
					$data [$name] ['size']
			] );

			return $results;
		}

		return $default;
	}
	public function getModel($name = 'Editor', $prefix = 'JpagebuilderModel', $config = array (
			'ignore_request' => true
	)) {
		return parent::getModel ( $name, $prefix, $config );
	}
	public function __construct($config = [ ]) {
		parent::__construct ( $config );

		$this->app = Factory::getApplication ();

		$user = Factory::getApplication()->getIdentity();
		$authorised = $user->authorise ( 'core.admin', 'com_jpagebuilder' ) || $user->authorise ( 'core.manage', 'com_jpagebuilder' );

		if (! $authorised) {
			$response ['message'] = Text::_ ( 'COM_JPAGEBUILDER_EDITOR_ADMIN_ACCESS_REQUIRED' );

			$this->sendResponse ( $response, 403 );
		}

		if (! $user->id) {
			$response ['message'] = Text::_ ( 'COM_JPAGEBUILDER_EDITOR_LOGIN_SESSION_EXPIRED' );
			$this->sendResponse ( $response, 401 );
		}
	}
}
