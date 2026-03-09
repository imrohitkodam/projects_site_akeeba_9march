<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Global Colors traits
 */
trait JPageBuilderFrameworkGlobalColors {
	private function getGlobalColors() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( [ 
				'id',
				'name',
				'colors'
		] )->from ( $db->quoteName ( '#__jpagebuilder_colors' ) )->where ( $db->quoteName ( 'published' ) . ' = 1' );
		$db->setQuery ( $query );

		$colors = [ ];

		try {
			$colors = $db->loadObjectList ();
		} catch ( \Exception $e ) {
			return [ ];
		}

		if (! empty ( $colors )) {
			foreach ( $colors as &$color ) {
				$color->colors = \json_decode ( $color->colors );
			}

			unset ( $color );
		}

		$this->sendResponse ( $colors );
	}
	public function globalColors() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PUT',
				'PATCH',
				'DELETE'
		], $method );

		if ($method === 'GET') {
			$this->getGlobalColors ();
		}
	}
}
