<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Access\Access;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\String\StringHelper;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

require_once __DIR__ . '/addons.php';
require_once __DIR__ . '/../helpers/helper.php';
require_once __DIR__ . '/../helpers/addonhelper.php';

require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/base.php';
require_once JPATH_ROOT . '/components/com_jpagebuilder/builder/classes/config.php';

/**
 * Addon Parser Class.
 *
 * @since 1.0.0
 */
class JpagebuilderAddonParser {
	private static $JpagebuilderAddonTags = array ();
	private static $template = '';
	private static $contents = array ();
	private static $module_contents = array ();
	private static $deepAddonList = [ 
			'accordion' => 'jp_accordion_item',
			'tab' => 'jp_tab_item'
	];
	public static $loaded_addon = array ();
	public static $css_content = array ();
	public static $module_css_content = array ();
	public static $js_content = '';
	public static $authorised = array ();
	public static $addon_interactions = array ();

	// Get list of plugin addons
	private static function getPluginsAddons() {
		$path = JPATH_PLUGINS . '/jpagebuilder';

		if (! is_dir ( $path ))
			return;

		$plugins = Folder::folders ( $path );
		if (! count ( ( array ) $plugins ))
			return;

		$elements = array ();
		$addonPaths = [ ];

		foreach ( $plugins as $plugin ) {
			if (PluginHelper::isEnabled ( 'jpagebuilder', $plugin )) {
				$addonPaths [] = $path . '/' . $plugin . '/addons';
			}
		}

		foreach ( $addonPaths as $addonsPath ) {
			if (is_dir ( $addonsPath )) {
				$addons = Folder::folders ( $addonsPath );

				foreach ( $addons as $addon ) {
					$addonPath = $addonsPath . '/' . $addon;

					if (file_exists ( $addonPath . '/block.php' )) {
						$elements [$addon] = $addonPath;
					}
				}
			}
		}

		return $elements;
	}
	private static function getTemplateName() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'template'
		) ) );
		$query->from ( $db->quoteName ( '#__template_styles' ) );
		$query->where ( $db->quoteName ( 'client_id' ) . ' = 0' );
		$query->where ( $db->quoteName ( 'home' ) . ' = 1' );
		$db->setQuery ( $query );

		return $db->loadObject ()->template;
	}
	private static function getAddonRegex() {
		$tagnames = array_keys ( self::$JpagebuilderAddonTags );
		$tagregexp = join ( '|', array_map ( 'preg_quote', $tagnames ) );
		// WARNING! Do not change this regex without changing do_addon_tag() and strip_addon_tag()
		// Also, see addon_unautop() and shortcode.js.
		return '\\[' . // Opening bracket
		'(\\[?)' . // 1: Optional second opening bracket for escaping shortcodes: [[tag]]
		"($tagregexp)" . // 2: Shortcode name
		'(?![\\w-])' . // Not followed by word character or hyphen
		'(' . // 3: Unroll the loop: Inside the opening shortcode tag
		'[^\\]\\/]*' . // Not a closing bracket or forward slash
		'(?:' . '\\/(?!\\])' . // A forward slash not followed by a closing bracket
		'[^\\]\\/]*' . // Not a closing bracket or forward slash
		')*?' . ')' . '(?:' . '(\\/)' . // 4: Self closing tag ...
		'\\]' . // ... and closing bracket
		'|' . '\\]' . // Closing bracket
		'(?:' . '(' . // 5: Unroll the loop: Optionally, anything between the opening and closing shortcode tags
		'[^\\[]*+' . // Not an opening bracket
		'(?:' . '\\[(?!\\/\\2\\])' . // An opening bracket not followed by the closing shortcode tag
		'[^\\[]*+' . // Not an opening bracket
		')*+' . ')' . '\\[\\/\\2\\]' . // Closing shortcode tag
		')?' . ')' . '(\\]?)'; // 6: Optional second closing brocket for escaping shortcodes: [[tag]]
	}
	private static function doAddonTag($m) {
		// allow [[foo]] syntax for escaping a tag
		if ($m [1] == '[' && $m [6] == ']') {
			return substr ( $m [0], 1, - 1 );
		}
		$tag = $m [2];
		$attr = self::addonParseAtts ( $m [3] );
		if (isset ( $m [5] )) {
			// enclosing tag - extra parameter
			return $m [1] . call_user_func ( self::$JpagebuilderAddonTags [$tag], $attr, $m [5], $tag ) . $m [6];
		} else {
			// self-closing tag
			return $m [1] . call_user_func ( self::$JpagebuilderAddonTags [$tag], $attr, null, $tag ) . $m [6];
		}
	}
	private static function addonParseAtts($text) {
		$atts = array ();
		$pattern = '/(\w+)\s*=\s*"([^"]*)"(?:\s|$)|(\w+)\s*=\s*\'([^\']*)\'(?:\s|$)|(\w+)\s*=\s*([^\s\'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|(\S+)(?:\s|$)/';
		$text = preg_replace ( "/[\x{00a0}\x{200b}]+/u", " ", $text );
		if (preg_match_all ( $pattern, $text, $match, PREG_SET_ORDER )) {
			foreach ( $match as $m ) {
				if (! empty ( $m [1] ))
					$atts [strtolower ( $m [1] )] = stripcslashes ( $m [2] );
				elseif (! empty ( $m [3] ))
					$atts [strtolower ( $m [3] )] = stripcslashes ( $m [4] );
				elseif (! empty ( $m [5] ))
					$atts [strtolower ( $m [5] )] = stripcslashes ( $m [6] );
				elseif (isset ( $m [7] ) and strlen ( $m [7] ))
					$atts [] = stripcslashes ( $m [7] );
				elseif (isset ( $m [8] ))
					$atts [] = stripcslashes ( $m [8] );
			}
		} else {
			$atts = ltrim ( $text );
		}
		return $atts;
	}
	/**
	 * Print interaction css and javascript object
	 */
	private static function parseInteractions($addonId, &$addonSettings, $selectors) {
		foreach ( $selectors as $selector ) {
			$interactions = isset ( $addonSettings->$selector ) ? $addonSettings->$selector : [ ];

			if (! empty ( $interactions )) {
				$interactions = $interactions [0];
				$animationCollection = new \stdClass ();
				$animationCollection->addonId = $addonId;
				$animationCollection->enable_mobile = isset ( $interactions->enable_mobile ) && $interactions->enable_mobile;
				$animationCollection->scrolling_options = isset ( $addonSettings->scrolling_options ) ? $addonSettings->scrolling_options : 'viewport';

				$animationCollection->enable_tablet = isset ( $interactions->enable_tablet ) && $interactions->enable_tablet;

				if ($selector === 'while_scroll_view' && $interactions->enable_while_scroll_view) {
					$animation = isset ( $interactions->on_scroll_actions ) ? $interactions->on_scroll_actions : [ ];

					if (count ( $animation ) > 1) {
						usort ( $animation, function ($x, $y) {
							return $x->keyframe - $y->keyframe;
						} );
					}

					$animationCollection->animation = $animation;
					$animationCollection->name = 'custom';

					$addonSettings_transition_origin_x = isset ( $addonSettings->transition_origin_x ) ? $addonSettings->transition_origin_x : '';
					$addonSettings_transition_origin_y = isset ( $addonSettings->transition_origin_y ) ? $addonSettings->transition_origin_y : '';
					$interaction_transition_origin_x = isset ( $interactions->transition_origin_x ) ? $interactions->transition_origin_x : '';
					$interaction_transition_origin_y = isset ( $interactions->transition_origin_y ) ? $interactions->transition_origin_y : '';

					$xOffset = ! empty ( $addonSettings_transition_origin_x ) ? $addonSettings_transition_origin_x : $interaction_transition_origin_x;
					$yOffset = ! empty ( $addonSettings_transition_origin_y ) ? $addonSettings_transition_origin_y : $interaction_transition_origin_y;

					$animationCollection->origin = [ 
							'x_offset' => $xOffset,
							'y_offset' => $yOffset
					];

					if (isset ( self::$addon_interactions [$selector] )) {
						array_push ( self::$addon_interactions [$selector], $animationCollection );
					} else {
						self::$addon_interactions [$selector] = array (
								$animationCollection
						);
					}
				}

				if ($selector === 'mouse_movement' && $interactions->enable_tilt_effect) {
					$animationCollection->animation = $interactions;

					if (isset ( self::$addon_interactions [$selector] )) {
						array_push ( self::$addon_interactions [$selector], $animationCollection );
					} else {
						self::$addon_interactions [$selector] = array (
								$animationCollection
						);
					}
				}
			}
		}
	}
	private static function shortByKeyFrame($x, $y) {
		return $x ['keyframe'] - $y ['keyframe'];
	}
	private function checkObjectKeyValue($object) {
		if (! is_object ( $object )) {
			return false;
		}
	}
	private static function getRowById($id, $pageName) {
		$newContents = $pageName == "module" ? self::$module_contents : self::$contents;

		if (empty ( $newContents )) {
			return null;
		}

		for($i = count ( $newContents ) - 1; $i >= 0; $i --) {
			if ($newContents [$i]->id === $id) {
				return $newContents [$i];
			}
		}

		return null;
	}
	private static function getAddonById($addons, $id) {
		foreach ( $addons as $index => $addon ) {
			if ($addon->id === $id) {
				return [ 
						$index,
						$addon
				];
			}
		}

		return [ 
				- 1,
				null
		];
	}
	private static function generateDivCSS($addon, $pageName, $layouts) {
		if (empty ( $addon->name )) {
			return '';
		}

		$addonPath = JpagebuilderAddonParser::getAddonPath ( $addon->name );
		$output = '';

		if (file_exists ( $addonPath . '/block.php' )) {
			require_once $addonPath . '/block.php';

			$addonClassName = JpagebuilderApplicationHelper::generateSiteClassName ( $addon->name );
			$addonInstance = new $addonClassName ( $addon );

			$addonCss = $layouts->addon_css->render ( array (
					'addon' => $addon
			) );
			self::$css_content [] = $addonCss;

			if (method_exists ( $addonClassName, 'css' )) {
				if ($pageName === 'module') {
					$output .= '<style type="text/css">' . $addonCss . '</style>';
					$output .= '<style type="text/css">' . $addonInstance->css () . '</style>';
				} else {
					$cssContent = $addonInstance->css ();
					self::$css_content [] = $cssContent;
				}
			}
		}

		return $output;
	}
	public static function addAddon($tag, $func) {
		if (is_callable ( $func ))
			self::$JpagebuilderAddonTags [$tag] = $func;
	}
	public static function runAddon($content) {
		if (false === strpos ( $content, '[' )) {
			return $content;
		}

		if (empty ( self::$JpagebuilderAddonTags ) || ! is_array ( self::$JpagebuilderAddonTags ))
			return $content;
		$pattern = self::getAddonRegex ();

		return preg_replace_callback ( "/$pattern/s", array (
				'JpagebuilderAddonParser',
				'doAddonTag'
		), $content );
	}

	/**
	 * Import/Include addon file
	 *
	 * @param string $file_name
	 *        	The addon name. Optional
	 *        	
	 * @since 1.0.8
	 */
	public static function getAddonPath($addon_name = '') {
		$template_path = JPATH_ROOT . '/templates/' . self::$template;
		$plugins = self::getPluginsAddons ();

		if (file_exists ( $template_path . '/jpagebuilder/addons/' . $addon_name . '/block.php' )) {
			return $template_path . '/jpagebuilder/addons/' . $addon_name;
		} elseif (file_exists ( JPATH_ROOT . '/components/com_jpagebuilder/addons/' . $addon_name . '/block.php' )) {
			return JPATH_ROOT . '/components/com_jpagebuilder/addons/' . $addon_name;
		} else {
			// Load from plugin
			if (isset ( $plugins [$addon_name] ) && $plugins [$addon_name]) {
				return $plugins [$addon_name];
			}
		}
	}
	public static function getAddons() {
		self::$template = self::getTemplateName ();

		require_once JPATH_ROOT . '/components/com_jpagebuilder/addons/module/block.php'; // include module manually

		$template_path = JPATH_ROOT . '/templates/' . self::$template;
		$tmpl_folders = array ();
		if (file_exists ( $template_path . '/jpagebuilder/addons' )) {
			$tmpl_folders = Folder::folders ( $template_path . '/jpagebuilder/addons' );
		}

		$folders = Folder::folders ( JPATH_ROOT . '/components/com_jpagebuilder/addons' );
		if ($tmpl_folders) {
			$merge_folders = array_merge ( $folders, $tmpl_folders );
			$folders = array_unique ( $merge_folders );
		}

		if (count ( ( array ) $folders )) {
			foreach ( $folders as $folder ) {
				$tmpl_file_path = $template_path . '/jpagebuilder/addons/' . $folder . '/block.php';
				$com_file_path = JPATH_ROOT . '/components/com_jpagebuilder/addons/' . $folder . '/block.php';
				if ($folder != 'module') {
					if (file_exists ( $tmpl_file_path )) {
						require_once $tmpl_file_path;
					} else if (file_exists ( $com_file_path )) {
						require_once $com_file_path;
					}
				}
			}
		}
	}
	public static function viewAddons($content, $fluid = 0, $pageName = 'none', $level = 1, $newModule = true, $storeData = [ ]) {
		JpagebuilderBase::loadAddons ();
		$addon_list = JpagebuilderConfig::$addons;

		if (! \class_exists ( 'JpagebuilderHelperSite' )) {
			require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/helper.php';
		}

		if ($newModule && $pageName == 'module') {
			self::$module_contents = $content;
		}

		if (empty ( self::$contents ) && $pageName != 'module') {
			self::$contents = $content;
		}

		self::$authorised = Access::getAuthorisedViewLevels ( Factory::getApplication ()->getIdentity ()->id );

		$layout_path = JPATH_ROOT . '/components/com_jpagebuilder/layouts';

		$layouts = new stdClass ();

		$layouts->row_start = new FileLayout ( 'row.start', $layout_path );
		$layouts->row_end = new FileLayout ( 'row.end', $layout_path );
		$layouts->row_css = new FileLayout ( 'row.css', $layout_path );

		$layouts->column_start = new FileLayout ( 'column.start', $layout_path );
		$layouts->column_end = new FileLayout ( 'column.end', $layout_path );
		$layouts->column_css = new FileLayout ( 'column.css', $layout_path );

		$layouts->addon_start = new FileLayout ( 'addon.start', $layout_path );
		$layouts->addon_end = new FileLayout ( 'addon.end', $layout_path );
		$layouts->addon_css = new FileLayout ( 'addon.css', $layout_path );

		$doc = Factory::getApplication ()->getDocument ();
		$content = is_object ( $content ) ? ( array ) $content : $content;

		if (is_array ( $content )) {
			$output = '';

			foreach ( $content as $row ) {
				if (! isset ( $row ))
					break;

				$row->settings->dynamicId = $row->id;

				// Row Visibility and ACL
				if (isset ( $row->visibility ) && ! $row->visibility) {
					continue;
				}

				/**
				 * Check for the ACL
				 */
				if (! self::checkAddonACL ( $row )) {
					continue;
				}

				if ($level <= 1 && ! empty ( $row->parent ) && $row->parent !== false) {
					continue;
				}

				if ($fluid === 1 || ! empty ( $row->parent )) {
					$row->settings->fullscreen = 1;
				}

				$row_css = $layouts->row_css->render ( array (
						'options' => $row->settings
				) );

				if ($pageName === 'module') {
					array_push ( self::$module_css_content, $row_css );
				} else {
					array_push ( self::$css_content, $row_css );
				}

				$row->settings->isNestedRow = isset ( $row->parent ) && $row->parent !== false;

				$output .= $layouts->row_start->render ( array (
						'options' => $row->settings
				) );

				foreach ( $row->columns as $column ) {
					if (! \is_object ( $column->settings )) {
						$column->settings = ! empty ( $column->settings ) && \is_array ( $column->settings ) ? ( object ) $column->settings : new \stdClass ();
					}

					$column->settings->cssClassName = $column->class_name;
					$column->settings->cssClassName = str_replace ( 'column-parent ', '', $column->settings->cssClassName );
					$column->settings->cssClassName = str_replace ( 'active-column-parent', '', $column->settings->cssClassName );
					$column->settings->dynamicId = $column->id;

					// Column Visibility and ACL
					if (isset ( $column->visibility ) && ! $column->visibility) {
						continue;
					}

					/**
					 * Inject the column width to the column settings.
					 */
					if (! isset ( $column->settings->width )) {
						$width = ! empty ( $column->width ) ? $column->width : JpagebuilderHelperSite::getColumnWidth ( $column );
						$column->settings->width = $width;

						if (isset ( $column->settings->width->unit )) {
							unset ( $column->settings->width->unit );
						}
					}

					$column->settings->width = JpagebuilderHelperSite::purifyColumnWidth ( $column->settings->width );

					$column_css = $layouts->column_css->render ( array (
							'options' => $column->settings
					) );

					if ($pageName === 'module') {
						array_push ( self::$module_css_content, $column_css );
					} else {
						array_push ( self::$css_content, $column_css );
					}

					$output .= $layouts->column_start->render ( array (
							'options' => $column->settings
					) );

					foreach ( $column->addons as $key => $addon ) {
						// Interaction
						if (isset ( $addon->settings->mouse_movement ) || isset ( $addon->settings->while_scroll_view )) {
							$selectors = [ 
									'while_scroll_view',
									'mouse_movement'
							];

							if (! isset ( $addon->id )) {
								continue;
							}

							self::parseInteractions ( $addon->id, $addon->settings, $selectors );
						}

						/**
						 * Addon Visibility
						 */
						if (isset ( $addon->visibility ) && ! $addon->visibility) {
							continue;
						}

						/**
						 * Check for the ACL
						 */
						if (! self::checkAddonACL ( $addon )) {
							continue;
						}

						if (isset ( $addon->type ) && $addon->type === 'nested_row') {
							$newPageName = $pageName === 'module' ? 'module' : 'none';
							$nestedRow = self::getRowById ( $addon->id, $newPageName );
							$output .= self::viewAddons ( [ 
									$nestedRow
							], 0, $newPageName, 2, false, $storeData );
						} elseif (! empty ( $addon->name ) && $addon->name === 'div' && empty ( $addon->parent )) {
							$output .= self::getDivHTMLView ( $addon, $column->addons, $layouts, $pageName, $storeData, 0, [ 
									'rowId' => $row->id,
									'colId' => $column->id
							] );
						}
						else {
							$addon->pageName = $pageName;
							$addon->layouts = $layouts;
							$addon->storeData = $storeData;
							$addon->settings->row_id = $row->id;
							$addon->settings->column_id = $column->id;

							$output .= self::getAddonHtmlView ( $addon, $layouts, $pageName, false, $storeData );
						}
					}

					$output .= $layouts->column_end->render ( array (
							'options' => $column->settings
					) );
				}

				$output .= $layouts->row_end->render ( array (
						'options' => $row->settings
				) );
			}

			// interaction js
			if (count ( self::$addon_interactions ) > 0 && $pageName != 'none' && $pageName != 'module') {
				$wa = $doc->getWebAssetManager();
				$wa->addInlineScript ( 'var addonInteraction = ' . json_encode ( self::$addon_interactions ) . ';' );
			}

			if ($pageName === 'module') {
				return JpagebuilderAddonParser::runAddon ( $output ) . '<style type="text/css">' . self::convertCssArrayToString ( self::minifyCss ( self::$module_css_content ) ) . '</style>';
			} else {
				if ($pageName !== 'none') {
					$app = Factory::getApplication ();
					$wa = $doc->getWebAssetManager();
					$params = $app->getParams ( 'com_jpagebuilder' );
					$production_mode = $params->get ( 'production_mode', 0 );

					$inline_css = self::convertCssArrayToString ( self::minifyCss ( self::$css_content ) );

					if ($production_mode) {
						$css_folder_path = JPATH_ROOT . '/media/com_jpagebuilder/css';
						$css_file_path = $css_folder_path . '/' . $pageName . '.css';
						$css_file_url = 'media/com_jpagebuilder/css/' . $pageName . '.css';

						if (! is_dir ( $css_folder_path )) {
							Folder::create ( $css_folder_path );
						}

						file_put_contents ( $css_file_path, $inline_css );

						if (file_exists ( $css_file_path )) {
							$wa->registerAndUseStyle('jpagebuilder.' . $pageName, $css_file_url);
						} else {
							if($inline_css) {
								$wa->addInlineStyle ( $inline_css );
							}
						}
					} else {
						if($inline_css) {
							$wa->addInlineStyle ( $inline_css );
						}
					}
				}

				return JpagebuilderAddonParser::runAddon ( $output );
			}
		} else {
			return '<p>' . $content . '</p>';
		}
	}
	public static function generateCollectionCSS($addon, $pageName, $layouts) {
		if (empty ( $addon->name )) {
			return '';
		}

		$addonPath = JpagebuilderAddonParser::getAddonPath ( $addon->name );
		$output = '';

		if (file_exists ( $addonPath . '/block.php' )) {
			require_once $addonPath . '/block.php';

			$addonClassName = JpagebuilderApplicationHelper::generateSiteClassName ( $addon->name );
			$addonInstance = new $addonClassName ( $addon );

			$addonCss = $layouts->addon_css->render ( array (
					'addon' => $addon
			) );
			self::$css_content [] = $addonCss;

			if (method_exists ( $addonClassName, 'css' )) {
				if ($pageName === 'module') {
					$output .= '<style type="text/css">' . $addonCss . '</style>';
					$output .= '<style type="text/css">' . $addonInstance->css () . '</style>';
				} else {
					$cssContent = $addonInstance->css ();
					self::$css_content [] = $cssContent;
				}
			}
		}

		return $output;
	}
	public static function generateAnimation($addon) {
		$animationClass = '';
		$animationAttributes = '';
		$settings = $addon->settings;

		if (! empty ( $settings->global_use_animation ) && ! empty ( $settings->global_animation )) {
			$animationClass .= ' jpb-wow ' . $settings->global_animation;
		}

		if (! empty ( $settings->global_animationduration )) {
			$animationAttributes .= ' data-jpb-wow-duration="' . $settings->global_animationduration . 'ms"';
		}

		if (! empty ( $settings->global_animationdelay )) {
			$animationAttributes .= ' data-jpb-wow-delay="' . $settings->global_animationdelay . 'ms"';
		}

		return [ 
				'class' => $animationClass,
				'attributes' => $animationAttributes
		];
	}
	public static function getCollectionHTMLView($addon, $layouts, $pageName, $storeData = [ ]) {
		$collectionElement = '';

		if (isset ( $addon->visited ) && $addon->visited) {
			return $collectionElement;
		}

		if (! empty ( $addon->name )) {
			$collectionElement .= self::getAddonHtmlView ( $addon, $layouts, $pageName, true, $storeData );
		}

		$list = [ ];
		$page = 0;
		$itemsPerPage = $addon->settings->items_per_page ?? 10;
		$startItem = $page * $itemsPerPage;
		$slicedList = array_slice ( $list, $startItem, $startItem + $itemsPerPage );

		if (! empty ( $addon->items )) {
			foreach ( $slicedList as $index => $_ ) {
				$collectionElement .= '<div class="jpb-collection-item">';

				foreach ( $addon->items [0] as $itemAddon ) {
					if (! empty ( $itemAddon->parent )) {
						continue;
					}
					$collectionElement .= self::getDivHTMLView ( $itemAddon, $addon->items [0], $layouts, $pageName, $storeData, $index );
				}

				$collectionElement .= '</div>';
			}
		}

		return $collectionElement;
	}

	/**
	 * Generate the HTML structure of the nested DIV addon.
	 * The DIV addon can accepts other addons as a child addon,
	 * and the nesting could be more than one.
	 *
	 * @param stdClass $addon
	 *        	The addon object with all the addon settings.
	 * @param array $addonList
	 *        	The list of addons inside the parent column.
	 * @param stdClass $layouts
	 *        	The layout object containing the addon_start, addon_end etc.
	 * @param string $pageName
	 *        	The flag for defining is the addon is rendering inside a module or page.
	 *        	
	 * @return string The generated HTML for the DIV addon(s).
	 * @since 4.0.0
	 */
	public static function getDivHTMLView($addon, &$addonList, $layouts, $pageName, $storeData = [ ], $storeListIndex = 0, $ids = [ ]) {
		$divElement = '';

		if (isset ( $addon->visited ) && $addon->visited) {
			return $divElement;
		}

		$rowId = ! empty ( $ids ['rowId'] ) ? $ids ['rowId'] : null;
		$colId = ! empty ( $ids ['colId'] ) ? $ids ['colId'] : null;

		if (! empty ( $addon->name )) {
			if ($addon->name === 'div') {
				$animation = self::generateAnimation ( $addon );

				$responsive_hidden_class = "";
				$responsive_hidden_class .= (isset ( $addon->settings->hidden_xl ) && filter_var ( $addon->settings->hidden_xl, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-xl ' : '';
				$responsive_hidden_class .= (isset ( $addon->settings->hidden_lg ) && filter_var ( $addon->settings->hidden_lg, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-lg ' : '';
				$responsive_hidden_class .= (isset ( $addon->settings->hidden_md ) && filter_var ( $addon->settings->hidden_md, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-md ' : '';
				$responsive_hidden_class .= (isset ( $addon->settings->hidden_sm ) && filter_var ( $addon->settings->hidden_sm, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-sm ' : '';
				$responsive_hidden_class .= (isset ( $addon->settings->hidden_xs ) && filter_var ( $addon->settings->hidden_xs, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-xs ' : '';

				$global_section_z_index = (isset ( $addon->settings->global_section_z_index ) && $addon->settings->global_section_z_index) ? $addon->settings->global_section_z_index : '';
				$global_addon_z_index = (isset ( $addon->settings->global_addon_z_index ) && $addon->settings->global_addon_z_index) ? $addon->settings->global_addon_z_index : '';
				$global_custom_position = (isset ( $addon->settings->global_custom_position ) && $addon->settings->global_custom_position) ? $addon->settings->global_custom_position : '';
				$global_select_position = (isset ( $addon->settings->global_seclect_position ) && $addon->settings->global_seclect_position) ? $addon->settings->global_seclect_position : '';

				$divElement .= self::generateDivCSS ( $addon, $pageName, $layouts );
				$divElement .= '<div id="jpb-addon-wrapper-' . $addon->id . '" class="jpb-addon-wrapper ' . $responsive_hidden_class . '">';
				$divElement .= '<div id="jpb-addon-' . $addon->id . '" class="jpb-div-addon ' . $addon->settings->class . $animation ['class'] . ($global_custom_position && $global_select_position != '' ? ' jpb-positioned-addon ' : '') . '" ' . $animation ['attributes'] . ($global_section_z_index ? 'data-zindex="' . $global_section_z_index . '"' : '') . ' ' . ($global_addon_z_index ? 'data-col-zindex="' . $global_addon_z_index . '"' : '') . ' ' . ($global_custom_position && $rowId ? 'data-rowid="' . $rowId . '"' : '') . ' ' . ($global_custom_position && $colId ? 'data-colid="' . $colId . '"' : '') . '>';
			} else {
				$divElement .= self::getAddonHtmlView ( $addon, $layouts, $pageName, true, $storeData, $storeListIndex );
			}
		}

		if (! empty ( $addon->children )) {
			foreach ( $addon->children as $child ) {
				list ( $index, $childAddon ) = self::getAddonById ( $addonList, $child );

				if (! ($index > - 1)) {
					continue;
				}

				/**
				 * Addon Visibility
				 */
				if (isset ( $childAddon->visibility ) && ! $childAddon->visibility) {
					$addonList [$index]->visited = true;
					continue;
				}

				/**
				 * Check for the ACL
				 */
				if (! self::checkAddonACL ( $childAddon )) {
					$addonList [$index]->visited = true;
					continue;
				}

				$divElement .= self::getDivHTMLView ( $childAddon, $addonList, $layouts, $pageName, $storeData, $storeListIndex, $ids );
				$addonList [$index]->visited = true;
			}
		}

		if (! empty ( $addon->name ) && $addon->name === 'div') {
			$divElement .= '</div></div>';
		}

		return $divElement;
	}
	public static function getDivHTMLViewForCollection($addon, &$addonList, $layouts, $pageName, $storeData = [ ], $storeListIndex = 0) {
		$divElement = '';

		if (! empty ( $addon->name )) {
			if ($addon->name === 'div') {
				$animation = self::generateAnimation ( $addon );

				$custom_class = "";
				$custom_class .= (isset ( $addon->settings->hidden_md ) && filter_var ( $addon->settings->hidden_md, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-md jpb-hidden-lg ' : '';
				$custom_class .= (isset ( $addon->settings->hidden_sm ) && filter_var ( $addon->settings->hidden_sm, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-sm ' : '';
				$custom_class .= (isset ( $addon->settings->hidden_xs ) && filter_var ( $addon->settings->hidden_xs, FILTER_VALIDATE_BOOLEAN )) ? 'jpb-hidden-xs ' : '';

				// $addonId = JpagebuilderHelperSite::nanoid();
				$divElement .= self::generateDivCSS ( $addon, $pageName, $layouts );
				$divElement .= '<div id="jpb-addon-wrapper-' . $addon->id . '" class="jpb-addon-wrapper">';
				$divElement .= '<div id="jpb-addon-' . $addon->id . '" class="jpb-div-addon ' . $addon->settings->class . $custom_class . $animation ['class'] . '" ' . $animation ['attributes'] . '>';

				if (! empty ( $addon->children )) {
					foreach ( $addon->children as $child ) {
						list ( $index, $childAddon ) = self::getAddonById ( $addonList, $child );

						if (! ($index > - 1)) {
							continue;
						}

						/**
						 * Addon Visibility
						 */
						if (isset ( $childAddon->visibility ) && ! $childAddon->visibility) {
							continue;
						}

						/**
						 * Check for the ACL
						 */
						if (! self::checkAddonACL ( $childAddon )) {
							continue;
						}

						$divElement .= self::getDivHTMLViewForCollection ( $childAddon, $addonList, $layouts, $pageName, $storeData, $storeListIndex );
					}
				}
			} else {
				$divElement .= self::getAddonHtmlView ( $addon, $layouts, $pageName, true, $storeData, $storeListIndex );
			}
		}

		if (! empty ( $addon->name ) && $addon->name === 'div') {
			$divElement .= '</div></div>';
		}

		return $divElement;
	}
	public static function getAddonHtmlView($addon, $layouts, $pageName = 'none', $isChild = false, $storeData = [ ], $storeListIndex = 0) {
		/**
		 * If the addons ia a DIV addon then skip rendering the addon.
		 */
		if (isset ( $addon->name ) && $addon->name === 'div') {
			return '';
		}

		/**
		 * Addon Visibility
		 */
		if (isset ( $addon->visibility ) && ! $addon->visibility) {
			return;
		}

		/**
		 * If the addon has parent property and not forced to render
		 * i.e.
		 * the addon is not a child of a parent addon then skip.
		 */
		if (isset ( $addon->parent ) && $addon->parent && ! $isChild) {
			return '';
		}

		if (! isset ( $addon->name )) {
			return '';
		}

		$addon_list = JpagebuilderConfig::$addons;

		$addon_name = $addon->name;
		$class_name = JpagebuilderApplicationHelper::generateSiteClassName ( $addon_name );
		$addon_path = JpagebuilderAddonParser::getAddonPath ( $addon_name );

		$doc = Factory::getApplication ()->getDocument ();

		$output = '';

		if (file_exists ( $addon_path . '/block.php' )) {
			$addon_options = array ();

			if (isset ( $addon_list [$addon->name] ['attr'] ) && $addon_list [$addon->name] ['attr']) {
				$addon_groups = $addon_list [$addon->name] ['attr'];

				if (is_array ( $addon_groups )) {
					foreach ( $addon_groups as $addon_group ) {
						$addon_options += $addon_group;
					}
				}
			}

			$store = new \stdClass ();

			foreach ( $addon->settings as $key => &$setting ) {
				$deviceObject = ( object ) [ 
						'xl' => '',
						'lg' => '',
						'md' => '',
						'sm' => '',
						'xs' => ''
				];

				/**
				 * If the data is old one, then-
				 */
				if (isset ( $setting->md ) && ! isset ( $setting->xl )) {
					$deviceObject->xl = $setting->md;
					$deviceObject->lg = $setting->md;
				}

				if (\is_object ( $setting )) {
					$original = \json_decode ( \json_encode ( $setting ) );
				}

				$originalKey = $key . '_original';

				if (JpagebuilderHelperSite::hasMultiDeviceSettings ( $setting )) {
					foreach ( $deviceObject as $device => $_ ) {
						if (isset ( $setting->$device )) {
							$deviceObject->$device = $setting->$device;
						}
					}

					$unit = (isset ( $setting->unit ) && $addon_name !== 'clients') ? $setting->unit : "";

					if (! empty ( $unit )) {
						$deviceObject->unit = $unit;
					}

					$store->$originalKey = $deviceObject;
					$defaultDevice = JpagebuilderBase::$defaultDevice;
					$store->$key = $deviceObject->$defaultDevice;

					$keySm = $key . '_sm';
					$keyXs = $key . '_xs';
					$keyLg = $key . '_lg';
					$keyXl = $key . '_xl';
					$keyMd = $key . '_md';

					$addon->settings->$keySm = \is_string ( $deviceObject->sm ) ? $deviceObject->sm . $unit : $deviceObject->sm;
					$addon->settings->$keyXs = \is_string ( $deviceObject->xs ) ? $deviceObject->xs . $unit : $deviceObject->xs;
					$addon->settings->$keyXl = \is_string ( $deviceObject->xl ) ? $deviceObject->xl . $unit : $deviceObject->xl;
					$addon->settings->$keyLg = \is_string ( $deviceObject->lg ) ? $deviceObject->lg . $unit : $deviceObject->lg;
					$addon->settings->$keyMd = \is_string ( $deviceObject->md ) ? $deviceObject->md . $unit : $deviceObject->md;
				}

				if (isset ( $addon_options [$key] ['selector'] )) {
					$addon_selector = $addon_options [$key] ['selector'];
					if (isset ( $addon->settings->$key ) && ! empty ( $addon->settings->$key )) {
						$selector_value = $addon->settings->$key;
						$keySelector = $key . '_selector';
						$addon->settings->$keySelector = str_replace ( '{{ VALUE }}', $selector_value, $addon_selector );
					}
				}
			}

			unset ( $setting );

			foreach ( $store as $key => $value ) {
				$addon->settings->$key = $value;
			}

			$output .= $layouts->addon_start->render ( array (
					'addon' => $addon
			) ); // start addon
			require_once $addon_path . '/block.php';

			$hasRepeatableItems = self::checkRepeatableItems ( $addon->settings, $addon->name );

			if (! empty ( $hasRepeatableItems )) {
				self::processRepeatableItems ( $hasRepeatableItems, $addon->name, $layouts, $pageName, $storeData );
			}

			if (class_exists ( $class_name )) {

				$addon->listIndex = $storeListIndex;

				// Instantiate addon class
				$addon_obj = new $class_name ( $addon );
				$output .= $addon_obj->render ();

				$wa = $doc->getWebAssetManager();
				
				// Scripts
				if (method_exists ( $class_name, 'scripts' )) {
					$scripts = $addon_obj->scripts ();

					if (! empty ( $scripts )) {
						foreach ( $scripts as $key => $script ) {
							$fileName = StringHelper::str_ireplace('.', '-', pathinfo($script, PATHINFO_FILENAME));
							$wa->registerAndUseScript('jpagebuilder.' . $fileName, ltrim($script, '/'), [], [], ['jquery']); 
						}
					}
				}

				// JS
				if (method_exists ( $class_name, 'js' )) {
					if (! empty ( $addon_obj->js () )) {
						$wa->addInlineScript ( $addon_obj->js () );
					}
				}

				// Stylesheets
				if (method_exists ( $class_name, 'stylesheets' )) {
					$stylesheets = $addon_obj->stylesheets ();

					if (! empty ( $stylesheets )) {
						foreach ( $stylesheets as $key => $stylesheet ) {
							$fileName = StringHelper::str_ireplace('.', '-', pathinfo($stylesheet, PATHINFO_FILENAME));
							$wa->registerAndUseStyle('jpagebuilder.' . $fileName, ltrim($stylesheet, '/')); 
						}
					}
				}

				$addon_css = $layouts->addon_css->render ( array (
						'addon' => $addon
				) );

				if ($pageName == 'module') {
					$output .= '<style type="text/css">' . $addon_css . '</style>';
				} else if ($storeListIndex === 0) {
					// array_unshift(self::$css_content, $addon_css);
					array_push ( self::$css_content, $addon_css );
				}

				// css
				if (method_exists ( $class_name, 'css' )) {
					if ($pageName == 'module') {
						$output .= '<style type="text/css">' . $addon_obj->css () . '</style>';
					} else if ($storeListIndex === 0) {
						$cssContent = $addon_obj->css ();
						// array_unshift(self::$css_content, $cssContent);
						array_push ( self::$css_content, $cssContent );
					}
				}
			} else {
				$output .= htmlspecialchars_decode ( JpagebuilderAddonParser::runAddon ( JpagebuilderAddonParser::generateShortcode ( $addon ) ) );
			}

			$output .= $layouts->addon_end->render ();
		}

		return $output;
	}
	public static function checkRepeatableItems($settings, $addon_name) {
		$repeatableItems = [ ];
		$itemKey = 'jp_' . $addon_name . '_item';
		$minimalItemKey = $addon_name . '_item';

		$isRepeatable = isset ( $settings->$itemKey ) && ! empty ( $settings->$itemKey );

		if ($isRepeatable) {
			array_push ( $repeatableItems, $settings->$itemKey );
		}

		if (isset ( $settings->$minimalItemKey ) && ! empty ( $settings->$minimalItemKey )) {
			array_push ( $repeatableItems, $settings->$minimalItemKey );
		}

		return $repeatableItems;
	}
	public static function processRepeatableItems($repeatableItems, $addon_name, $layouts, $pageName, $storeData = [ ]) {
		$items = $repeatableItems;
		$newContent = '';

		foreach ( $items as &$settings ) {
			if (! empty ( $settings )) {
				foreach ( $settings as $key => &$item ) {
					if (isset ( $item->content ) && is_array ( $item->content )) {
						$newContent = '';

						foreach ( $item->content as $contentAddon ) {
							// Addon Visibility and ACL
							if (isset ( $contentAddon->visibility ) && ! $contentAddon->visibility) {
								continue;
							}

							// Check for ACL
							$access = self::checkAddonACL ( $contentAddon );

							if (! $access) {
								continue;
							}

							if (isset ( $contentAddon->type ) && $contentAddon->type === 'nested_row') {
								$newPageName = $pageName === 'module' ? 'module' : 'none';
								$nestedRow = self::getRowById ( $contentAddon->id, $newPageName );
								$newContent .= self::viewAddons ( [ 
										$nestedRow
								], 0, $newPageName, 2, false, $storeData );
							} else {
								$newContent .= self::getAddonHtmlView ( $contentAddon, $layouts, $pageName, $storeData );
							}
						}

						$item->content = $newContent;
					} else {
						$repeatableItems = self::checkRepeatableItems ( $item, $addon_name );

						if (! empty ( $repeatableItems )) {
							$repeatableItems = self::processRepeatableItems ( $repeatableItems, $addon_name, $layouts, $pageName, $storeData );
						}
					}
				}

				unset ( $item );
			}
		}

		unset ( $settings );

		return $items;
	}
	public static function minifyCss($cssCode) {
		// Remove comments
		$cssCode = preg_replace ( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $cssCode );

		// Remove space after colons
		$cssCode = str_replace ( ': ', ':', $cssCode );

		// Remove whitespace
		$cssCode = str_replace ( array (
				"\r\n",
				"\r",
				"\n",
				"\t",
				'  ',
				'    ',
				'    '
		), '', $cssCode );

		// Remove Empty Selectors without any properties
		$cssCode = preg_replace ( '/(?:(?:[^\r\n{}]+)\s?{[\s]*})/', '', $cssCode );

		// Remove Empty Media Selectors without any properties or selector
		$cssCode = preg_replace ( '/@media\s?\((?:[^\r\n,{}]+)\s?{[\s]*}/', '', $cssCode );

		return $cssCode;
	}
	public static function generateShortcode($addon) {
		if (! empty ( $addon->settings )) {
			$addon->settings->dynamicId = $addon->id;
			$ops = JpagebuilderAddonParser::generateShortcodeOps ( $addon->settings );
		}

		$output = '[jp_' . $addon->name;
		if (isset ( $ops ['default'] )) {
			$output .= $ops ['default'];
		}
		$output .= ']';
		if (isset ( $ops ['repeat'] )) {
			$output .= $ops ['repeat'];
		}
		$output .= '[/jp_' . $addon->name . ']';

		return $output;
	}
	public static function generateShortcodeOps($ops) {
		$default = '';
		$repeat = '';

		foreach ( $ops as $key => $val ) {
			if (is_string ( $val )) {
				$default .= ' ' . $key . '="' . htmlspecialchars ( $val ) . '"';
			} elseif (is_array ( $val )) {
				$temp = '';
				foreach ( $val as $innerKey => $innerVal ) {
					$temp .= '[' . $key;
					foreach ( $innerVal as $inner_key => $inner_val ) {
						if (is_string ( $inner_val )) {
							$temp .= ' ' . $inner_key . '="' . htmlspecialchars ( $inner_val ) . '"';
						}
					}
					$temp .= '][/' . $key . ']';
				}
				$repeat .= $temp;
			}
		}

		if ($default)
			$result ['default'] = $default;
		if ($repeat)
			$result ['repeat'] = $repeat;

		return $result;
	}
	public static function convertCssArrayToString($cssArray = array ()) {
		$cssString = '';
		if (count ( ( array ) $cssArray ) > 0) {
			foreach ( $cssArray as $cssItem ) {
				$cssString .= $cssItem;
			}
		}

		return $cssString;
	}
	public static function checkAddonACL($addon) {
		$access = true;
		if (isset ( $addon->settings->acl ) && $addon->settings->acl) {
			$access_list = $addon->settings->acl;
			$access = false;

			if (is_string ( $access_list )) {
				$access_list = [ 
						$addon->settings->acl
				];
			}

			foreach ( $access_list as $acl ) {
				if (in_array ( $acl, self::$authorised )) {
					$access = true;
				}
			}
			unset ( $addon->settings->acl );
		}

		return $access;
	}
}
function jpagebuilderAddonAtts($pairs, $atts, $shortcode = '') {
	$atts = ( array ) $atts;
	$out = array ();
	foreach ( $pairs as $name => $default ) {
		if (array_key_exists ( $name, $atts ))
			$out [$name] = $atts [$name];
		else
			$out [$name] = $default;
	}

	return $out;
}

JpagebuilderAddonParser::getAddons ();
