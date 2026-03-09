<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\Http;

/**
 * No direct access
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );
final class JpagebuilderApplicationHelper {
	public static function generateSiteClassName($addonName) {
		if (empty ( $addonName )) {
			return '';
		}

		return 'JpagebuilderAddon' . ucfirst ( $addonName );
	}
	public static function hasPageContent(int $id) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->select ( 'content' )
			  ->from ( $db->quoteName ( '#__jpagebuilder' ) )
			  ->where ( $db->quoteName ( 'id' ) . ' = ' . $id );

		$db->setQuery ( $query );

		try {
			$result = $db->loadResult ();

			if (\is_string ( $result ) && ! empty ( $result )) {
				$result = \json_decode ( $result );
			}

			return ! empty ( $result );
		} catch ( \Exception $e ) {
			return false;
		}
	}
	public static function sanitizePageText($text) {
		$text = $text ?? '[]';
		$text = ! \is_string ( $text ) ? json_encode ( $text ) : $text;
		$parsed = JpagebuilderAddon::__ ( $text );
		$parsed = JpagebuilderHelperSite::sanitize ( $parsed );

		return json_decode ( $parsed );
	}
	public static function preparePageData($pageData) {
		if (empty ( $pageData )) {
			return ( object ) [ 
					'text' => new stdClass ()
			];
		}

		$content = [ ];

		if (is_null ( $pageData->content )) {
			$pageData->text = JpagebuilderHelperSite::prepareSpacingData ( $pageData->text );
			$pageData->text = self::sanitizePageText ( $pageData->text );

			return $pageData;
		}

		if (\is_string ( $pageData->content )) {
			$content = \json_decode ( $pageData->content );
		}

		if (is_null ( $content )) {
			$pageData->text = JpagebuilderHelperSite::prepareSpacingData ( $pageData->text );
			$pageData->text = self::sanitizePageText ( $pageData->text );

			return $pageData;
		}

		$version = JpagebuilderHelper::getVersion ();
		$storedVersion = $pageData->version;
		$pageData->text = $content;
		$pageData->text = JpagebuilderHelperSite::prepareSpacingData ( $pageData->text );
		$pageData->text = json_decode ( $pageData->text );

		if ($version !== $storedVersion) {
			$pageData->text = self::sanitizePageText ( json_encode ( $pageData->text ) );
		}

		return $pageData;
	}
}
