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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Version;

require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/autoload.php';
require_once JPATH_ROOT . '/components/com_jpagebuilder/helpers/integrations.php';
JpagebuilderAutoload::loadClasses ();
JpagebuilderAutoload::loadHelperClasses ();

final class JpagebuilderHelper {
	public static $extension = 'com_jpagebuilder';
	private static function checkPage($extension, $extension_view, $view_id = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id'
		) ) );
		$query->from ( $db->quoteName ( '#__jpagebuilder' ) );
		$query->where ( $db->quoteName ( 'extension' ) . ' = ' . $db->quote ( $extension ) );
		$query->where ( $db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( $extension_view ) );
		$query->where ( $db->quoteName ( 'view_id' ) . ' = ' . $db->quote ( $view_id ) );
		$db->setQuery ( $query );
		
		return $db->loadResult ();
	}
	private static function insertPage($content = array ()) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		
		$columns = array (
				'title',
				'text',
				'content',
				'extension',
				'extension_view',
				'view_id',
				'active',
				'published',
				'catid',
				'access',
				'created_on',
				'created_by',
				'modified',
				'modified_by',
				'language',
				'css',
				'version'
		);
		
		$query->insert ( $db->quoteName ( '#__jpagebuilder' ) )
		->columns ( $db->quoteName ( $columns ) )
		->values ( implode ( ',', $content ) );
		
		$db->setQuery ( $query );
		$db->execute ();
	}
	private static function updatePage($view_id, $content, $extension_view = '') {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$condition = array (
				$db->quoteName ( 'view_id' ) . ' = ' . $db->quote ( $view_id )
		);
		
		if ($extension_view != '') {
			array_push ( $condition, $db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( $extension_view ) );
		}
		
		$query->update ( $db->quoteName ( '#__jpagebuilder' ) )->set ( $content )->where ( $condition );
		
		$db->setQuery ( $query );
		$db->execute ();
	}
	private static function deleteArticlePage($params) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		
		$conditions = array (
				$db->quoteName ( 'extension' ) . ' = ' . $db->quote ( $params ['option'] ),
				$db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( $params ['view'] ),
				$db->quoteName ( 'view_id' ) . ' = ' . $db->quote ( $params ['id'] )
		);
		
		$query->delete ( $db->quoteName ( '#__jpagebuilder' ) );
		$query->where ( $conditions );
		$db->setQuery ( $query );
		$db->execute ();
	}
	public static function getVersion($md5 = false, $remoteVersionChecker = false) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'e.manifest_cache' )
			  ->select ( $db->quoteName ( 'e.manifest_cache' ) )
			  ->from ( $db->quoteName ( '#__extensions', 'e' ) )
			  ->where ( $db->quoteName ( 'e.element' ) . ' = ' . $db->quote ( 'com_jpagebuilder' ) );

		$db->setQuery ( $query );
		$manifest_cache = json_decode ( $db->loadResult () );

		if (isset ( $manifest_cache->version ) && $manifest_cache->version) {

			if ($md5) {
				return md5 ( $manifest_cache->version );
			}

			// Updates server remote URI, try to get informations
			if($remoteVersionChecker) {
				try {
					$url = 'https://storejextensions.org/dmdocuments/updates/com_jpagebuilder.json';
					
					// Set stream context with timeout in seconds
					$context = stream_context_create([
							'http' => [
									'timeout' => 2, // 3 seconds timeout
							]
					]);
					
					$response = @file_get_contents($url, false, $context);
					if($response) {
						$decodedUpdateInfos = json_decode($response);
						$manifest_cache->version .= '/' . $decodedUpdateInfos->latest . '/' . $decodedUpdateInfos->relevance;
					}
				} catch(Exception $e) {
				}
			}

			return $manifest_cache->version;
		}

		return '1.0';
	}

	// 3rd party
	public static function onAfterSavingModule($attribs) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$attribs ['css'] = '';

		$content = $attribs ['content'] ?? $attribs ['text'];

		$values = array (
				$db->quote ( $attribs ['title'] ),
				$db->quote ( '[]' ),
				$db->quote ( $content ),
				$db->quote ( $attribs ['option'] ),
				$db->quote ( $attribs ['view'] ),
				$db->quote ( $attribs ['id'] ),
				$db->quote ( $attribs ['active'] ),
				$db->quote ( $attribs ['published'] ),
				$db->quote ( $attribs ['catid'] ),
				$db->quote ( $attribs ['access'] ),
				$db->quote ( $attribs ['created_on'] ),
				$db->quote ( $attribs ['created_by'] ),
				$db->quote ( $attribs ['modified'] ),
				$db->quote ( $attribs ['modified_by'] ),
				$db->quote ( $attribs ['language'] ),
				$db->quote ( $attribs ['css'] ),
				$db->quote ( $attribs ['version'] )
		);

		self::insertPage ( $values );

		return true;
	}
	public static function onAfterIntegrationSave($attribs) {
		if (! self::getIntegration ( $attribs ['option'] ))
			return;

		$attribs ['css'] = '';

		$db = Factory::getContainer()->get('DatabaseDriver');

		if (self::checkPage ( $attribs ['option'], $attribs ['view'], $attribs ['id'] ) || $attribs ['action'] == 'delete' || $attribs ['action'] == 'stateChange') {

			if ($attribs ['action'] == 'stateChange') {
				$fields = array (
						$db->quoteName ( 'published' ) . ' = ' . $db->quote ( $attribs ['published'] )
				);
				self::updatePage ( $attribs ['id'], $fields );
			} elseif ($attribs ['action'] == 'delete') {
				self::deleteArticlePage ( $attribs );
			} else {
				$fields = array (
						$db->quoteName ( 'title' ) . ' = ' . $db->quote ( $attribs ['title'] ),
						// $db->quoteName('text') . ' = ' . $db->quote($attribs['text']),
						$db->quoteName ( 'published' ) . ' = ' . $db->quote ( $attribs ['published'] ),
						$db->quoteName ( 'catid' ) . ' = ' . $db->quote ( $attribs ['catid'] ),
						$db->quoteName ( 'access' ) . ' = ' . $db->quote ( $attribs ['access'] ),
						$db->quoteName ( 'modified' ) . ' = ' . $db->quote ( $attribs ['modified'] ),
						$db->quoteName ( 'modified_by' ) . ' = ' . $db->quote ( $attribs ['modified_by'] ),
						$db->quoteName ( 'active' ) . ' = ' . $db->quote ( $attribs ['active'] )
				);

				self::updatePage ( $attribs ['id'], $fields, $attribs ['view'] );
			}
		} else {
			$content = $attribs ['content'] ?? $attribs ['text'];
			$values = array (
					$db->quote ( $attribs ['title'] ),
					$db->quote ( '[]' ),
					$db->quote ( $content ),
					$db->quote ( $attribs ['option'] ),
					$db->quote ( $attribs ['view'] ),
					$db->quote ( $attribs ['id'] ),
					$db->quote ( $attribs ['active'] ),
					$db->quote ( $attribs ['published'] ),
					$db->quote ( $attribs ['catid'] ),
					$db->quote ( $attribs ['access'] ),
					$db->quote ( $attribs ['created_on'] ),
					$db->quote ( $attribs ['created_by'] ),
					$db->quote ( $attribs ['modified'] ),
					$db->quote ( $attribs ['modified_by'] ),
					$db->quote ( $attribs ['language'] ),
					$db->quote ( $attribs ['css'] ),
					$db->quote ( $attribs ['version'] )
			);

			self::insertPage ( $values );
		}

		return true;
	}
	public static function onIntegrationPrepareContent($text, $option, $view, $id = 0) {
		if (! self::getIntegration ( $option )) {
			return $text;
		}

		$pageName = $view . '-' . $id;

		$page_content = self::getPageContent ( $option, $view, $id );

		if ($page_content) {
			$page_content = JpagebuilderApplicationHelper::preparePageData ( $page_content );
			$page_content->text = ! is_string ( $page_content->text ) ? json_encode ( $page_content->text ) : $page_content->text;

			require_once JPATH_ROOT . '/components/com_jpagebuilder/editor/addonparser.php';
			$doc = Factory::getApplication ()->getDocument ();
			$wa = $doc->getWebAssetManager();
			$params = ComponentHelper::getParams ( 'com_jpagebuilder' );

			if ($params->get ( 'fontawesome', 1 )) {
				$wa->registerAndUseStyle('jpagebuilder.faw5', 'components/com_jpagebuilder/assets/css/font-awesome-5.min.css');
				$wa->registerAndUseStyle('jpagebuilder.faw4shim', 'components/com_jpagebuilder/assets/css/font-awesome-v4-shims.css');
			}

			if (! $params->get ( 'disableanimatecss', 0 )) {
				$wa->registerAndUseStyle('jpagebuilder.animate', 'components/com_jpagebuilder/assets/css/animate.min.css');
			}

			if (! $params->get ( 'disablecss', 0 )) {
				$wa->registerAndUseStyle('jpagebuilder.pagebuildersite', 'components/com_jpagebuilder/assets/css/jpagebuilder.css');
			}

			$wa->useScript('jquery');
			$wa->registerAndUseScript('jpagebuilder.jqueryparallax', 'components/com_jpagebuilder/assets/js/jquery.parallax.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], []);
			$wa->registerAndUseScript('jpagebuilder.pagebuilder', 'components/com_jpagebuilder/assets/js/jpagebuilder.js', ['version' => JpagebuilderHelperSite::getVersion ( true )], ['defer' => true], ['jpagebuilder.jqueryparallax']);
			
			$page_content->text = JpagebuilderHelperSite::sanitizeImportJSON ( $page_content->text );
			return '<div id="jpagebuilder" class="jpagebuilder jpb-' . $view . '-page-wrapper"><div class="page-content">' . JpagebuilderAddonParser::viewAddons ( json_decode ( $page_content->text ), 0, $pageName ) . '</div></div>';
		}

		return $text;
	}
	public static function getPageContent($extension, $extension_view, $view_id = 0) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( '*' );
		$query->from ( $db->quoteName ( '#__jpagebuilder' ) );
		$query->where ( $db->quoteName ( 'extension' ) . ' = ' . $db->quote ( $extension ) );
		$query->where ( $db->quoteName ( 'extension_view' ) . ' = ' . $db->quote ( $extension_view ) );
		$query->where ( $db->quoteName ( 'view_id' ) . ' = ' . $db->quote ( $view_id ) );
		$query->where ( $db->quoteName ( 'active' ) . ' = 1' );
		$db->setQuery ( $query );
		$result = $db->loadObject ();

		if (count ( ( array ) $result )) {
			return $result;
		}

		return false;
	}
	public static function getIntegration($option) {
		$group = str_replace ( 'com_', '', $option );
		$integrations = BuilderIntegrations::getIntegrations ();

		if (! isset ( $integrations [$group] )) {
			return false;
		}

		$integration = $integrations [$group];
		$name = $integration ['name'];

		$enabled = PluginHelper::isEnabled ( $group, $name );

		if ($enabled) {
			return $integration;
		}

		return false;
	}
	public static function getMenuId($pageId) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( $db->quoteName ( array (
				'id'
		) ) );
		$query->from ( $db->quoteName ( '#__menu' ) );
		$query->where ( $db->quoteName ( 'link' ) . ' LIKE ' . $db->quote ( '%option=com_jpagebuilder&view=page&id=' . $pageId . '%' ) );
		$query->where ( $db->quoteName ( 'published' ) . ' = ' . $db->quote ( '1' ) );
		$db->setQuery ( $query );
		$result = $db->loadResult ();

		if ($result) {
			return '&Itemid=' . $result;
		}

		return '';
	}
	public static function formatSavedAddon($code) {
		$code = is_string ( $code ) ? json_decode ( $code ) : $code;

		if (! isset ( $code->addon )) {
			$mockSection = self::createMockSection ( $code );
			$parseMockSection = JpagebuilderHelperSite::sanitize ( $mockSection );
			$parseMockSection = json_decode ( $parseMockSection );
			$addonData = $parseMockSection [0]->columns [0]->addons [0];

			$savedAddon = ( object ) [ 
					'name' => $code->name ?? '',
					'rows' => [ ],
					'addon' => [ 
							$addonData
					]
			];

			return json_encode ( $savedAddon );
		}

		return json_encode ( $code );
	}
	public static function createMockSection($addon) {
		$section = ( object ) [ 
				'id' => '',
				'visibility' => false,
				'collapse' => false,
				'settings' => new stdClass (),
				'columns' => [ 
						( object ) [ 
								'id' => "",
								'class_name' => "row-column",
								'visibility' => true,
								'settings' => new stdClass (),
								'addons' => [ 
										$addon
								]
						]
				],
				'layout' => '12',
				'parent' => false
		];

		return json_encode ( [ 
				$section
		] );
	}
	public static function formatSavedSection($section) {
		$section = is_string ( $section ) ? json_decode ( $section ) : $section;
		$section = ! is_array ( $section ) ? [ 
				$section
		] : $section;

		return JpagebuilderHelperSite::sanitize ( json_encode ( $section ) );
	}
}
