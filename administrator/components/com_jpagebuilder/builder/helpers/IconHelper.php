<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Summary of JpagebuilderIconHelper
 */
class JpagebuilderIconHelper {
	private static $instance;

	/**
	 * Get the parsed class list
	 *
	 * @param string $css
	 *        	css file for parsing
	 * @return string
	 * @since 4.0.0
	 */
	public static function getClassName($css, $prefix, $force = false) {
		if (empty ( trim ( $css ) )) {
			return false;
		}

		if (self::$instance === null) {
			self::$instance = new JpagebuilderIconHelper ();
		}

		$parsedClassName = self::$instance->parseCssSelectors ( $css, $prefix, $force );

		return $parsedClassName;
	}

	/**
	 * Parse CSS Selectors
	 *
	 * @param [type] $css
	 *        	CSS file to parse.
	 * @param [type] $prefix
	 *        	Prefix.
	 * @param boolean $force
	 *        	Add extra prefix.
	 * @return string
	 */
	private function parseCssSelectors($css, $prefix, $force = false) {
		$result = [ ];
		/**
		 * Check css comment
		 * preg_match_all('/([\/][\*]).*./',$css,$match);
		 */
		preg_match_all ( '/([^\{\}]+)\{([^\}]*)\}|([\/\*])/ims', $css, $match );

		foreach ( $match [0] as $i => $x ) {
			$selector = trim ( $match [1] [$i] );
			if (preg_match_all ( "/(^\." . $prefix . "\-)\w+(\-\w+)?(\-\w+)?(:before)/m", $selector )) {
				if ($force) {
					$result [] = $prefix . ' ' . (explode ( ':', explode ( '.', $selector ) [1] ) [0]);
				} else {
					$result [] = explode ( ':', explode ( '.', $selector ) [1] ) [0];
				}
			}
		}

		return json_encode ( $result );
	}
	public static function loadAssets() {
		$doc = Factory::getApplication ()->getDocument ();

		$wa = $doc->getWebAssetManager();
		$wa->registerAndUseStyle('jpagebuilder.faw5', 'components/com_jpagebuilder/assets/css/font-awesome-5.min.css');
		$wa->registerAndUseStyle('jpagebuilder.faw4shim', 'components/com_jpagebuilder/assets/css/font-awesome-v4-shims.css');
	}
	public static function getVersion($md5 = false) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( 'e.manifest_cache' )->select ( $db->quoteName ( 'e.manifest_cache' ) )->from ( $db->quoteName ( '#__extensions', 'e' ) )->where ( $db->quoteName ( 'e.element' ) . ' = ' . $db->quote ( 'com_jpagebuilder' ) );

		$db->setQuery ( $query );
		$manifest_cache = json_decode ( $db->loadResult () );

		if (isset ( $manifest_cache->version ) && $manifest_cache->version) {

			if ($md5) {
				return md5 ( $manifest_cache->version );
			}

			return $manifest_cache->version;
		}

		return '1.0';
	}
}
