<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

/**
 * Trait for managing app configs
 */
trait JPageBuilderFrameworkAppConfig {
	private function getAppConfig() {
		if (! \class_exists ( 'JpagebuilderBase' )) {
			require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/base.php';
		}

		if (! \class_exists ( 'JpagebuilderHelper' )) {
			require_once JPATH_ROOT . '/administrator/components/com_jpagebuilder/helpers/jpagebuilder.php';
		}

		$mediaParams = ComponentHelper::getParams ( 'com_media' );
		$cParams = ComponentHelper::getParams ( 'com_jpagebuilder' );

		$model = $this->getModel ( 'Appconfig' );

		$pages = $model->getPageList ();
		$menus = $model->getMenus ();
		$categories = $model->getCategories ();
		$accessLevels = $model->getAccessLevels ();
		$languages = $model->getLanguages ();
		$modules = JpagebuilderBase::getModuleAttributes ();

		$languageOptions = $this->convertToOptions ( $languages );
		$allLanguage = ( object ) [ 
				'label' => Text::_ ( 'JALL' ),
				'value' => '*'
		];

		array_unshift ( $languageOptions, $allLanguage );

		$version = JpagebuilderHelper::getVersion (false, true);

		$googleFontCategories = [ 
				( object ) [ 
						'label' => Text::_ ( 'COM_JPAGEBUILDER_FONT_CATEGORY_SERIF' ),
						'value' => 'serif'
				],
				( object ) [ 
						'label' => Text::_ ( 'COM_JPAGEBUILDER_FONT_CATEGORY_SANS_SERIF' ),
						'value' => 'sans-serif'
				],
				( object ) [ 
						'label' => Text::_ ( 'COM_JPAGEBUILDER_FONT_CATEGORY_DISPLAY' ),
						'value' => 'display'
				],
				( object ) [ 
						'label' => Text::_ ( 'COM_JPAGEBUILDER_FONT_CATEGORY_HANDWRITING' ),
						'value' => 'handwriting'
				],
				( object ) [ 
						'label' => Text::_ ( 'COM_JPAGEBUILDER_FONT_CATEGORY_MONOSPACE' ),
						'value' => 'monospace'
				]
		];

		$version = new Version ();
		$JoomlaVersion = $version->getShortVersion ();

		$response = ( object ) [ 
				'pages' => $this->convertToOptions ( $pages ),
				'menus' => $this->convertToOptions ( $menus ),
				'categories' => $this->convertCategoriesToOptions ( $categories ),
				'modules' => $modules ['moduleName'] ?? [ ],
				'module_positions' => $modules ['modulePosition'] ?? [ ],
				'access_levels' => $this->convertToOptions ( $accessLevels ),
				'article_categories' => JpagebuilderBase::getArticleCategories (),
				'languages' => $languageOptions,
				'font_awesome_icons' => JpagebuilderBase::getIconList (),
				'version' => JpagebuilderHelper::getVersion (false, true),
				'editor' => ( object ) [ 
						'theme' => $JoomlaVersion < 4 ? 'modern' : 'silver'
				],
				'media_path' => '/' . $mediaParams->get ( 'file_path', 'files' ),
				'media_upload_max_size' => $mediaParams->get ( 'upload_maxsize', 0 ) * 1024 * 1024,
				'google_font_categories' => $googleFontCategories,
				'has_google_font_api_key' => ! empty ( $cParams->get ( 'google_font_api_key', 'AIzaSyDksiuBas8gVxA8_nxLUw69sq3FmJQR1wU' ) ),
				'is_google_fonts_disabled' => ( bool ) $cParams->get ( 'disable_google_fonts', 0 ),
				'enable_frontend_editing' => ( bool ) $cParams->get ( 'enable_frontend_editing', 1 ),
				'add_items_first' => ( bool ) $cParams->get ( 'add_items_first', 0 ),
				'dark_mode' => ( bool ) $cParams->get ( 'dark_mode', 1 ),
				'enable_ai' => ( bool ) $cParams->get ( 'enable_ai', 0 ),
				'permissions' => $model->getUserPermissions (),
				'user_id' => Factory::getApplication ()->getIdentity ()->id ?? null
		];

		$this->sendResponse ( $response );
	}
	private function convertToOptions(array $values) {
		$options = [ ];

		foreach ( $values as $value ) {
			$option = ( object ) [ 
					'label' => $value->title,
					'value' => $value->id
			];

			$options [] = $option;
		}

		return $options;
	}
	private function convertCategoriesToOptions(array $categories) {
		$options = [ ];

		foreach ( $categories as $category ) {
			$option = ( object ) [ 
					'label' => str_repeat ( '- ', max ( 0, $category->level - 1 ) ) . $category->title,
					'value' => $category->id
			];

			$options [] = $option;
		}

		return $options;
	}
	public function getPermissions() {
		$pageId = $this->getInput ( 'page_id', 0, 'int' );
		$model = $this->getModel ( 'Appconfig' );

		$this->sendResponse ( $model->getUserPermissions ( $pageId ) );
	}

	/**
	 * App config
	 *
	 * @TODO: will be implemented later.
	 *
	 * @return void
	 */
	public function appConfig() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PUT',
				'DELETE',
				'PATCH'
		], $method );

		$this->getAppConfig ();
	}
}
