<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Uri\Uri;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Application Settings traits
 */
trait JPageBuilderFrameworkApplicationSettings {
	private function getComponentSettings() {
		$params = ComponentHelper::getParams ( 'com_jpagebuilder' );

		if ($params->exists ( 'ig_token' )) {
			$params->set ( 'ig_token', \json_decode ( $params->get ( 'ig_token' ) ) );
		}

		if (! $params->exists ( 'enable_frontend_editing' )) {
			$params->set ( 'enable_frontend_editing', '1' );
		}
		
		if (! $params->exists ( 'add_items_first' )) {
			$params->set ( 'add_items_first', '0' );
		}
		
		if (! $params->exists ( 'dark_mode' )) {
			$params->set ( 'dark_mode', '1' );
		}

		if (! $params->get ( 'lazyplaceholder' )) {
			$params->set ( 'lazyplaceholder', '/components/com_jpagebuilder/assets/images/lazyloading-placeholder.svg' );
		}

		$colors = $this->getColors ();
		$params->set ( 'colors', $colors );

		$this->sendResponse ( $params );
	}
	private function saveApplicationSettings() {
		$productionMode = $this->getInput ( 'production_mode', 0, 'int' );
		$gmapApi = $this->getInput ( 'gmap_api', '', 'string' );
		$turnstyleSitekey = $this->getInput ( 'turnstyle_sitekey', '', 'string' );
		$turnstyleSecretkey = $this->getInput ( 'turnstyle_secretkey', '', 'string' );
		$igToken = $this->getInput ( 'ig_token', '', 'RAW' );
		$fontAwesome = $this->getInput ( 'fontawesome', 1, 'int' );
		$disableGoogleFonts = $this->getInput ( 'disable_google_fonts', 0, 'int' );
		$lazyLoadimg = $this->getInput ( 'lazyloadimg', 0, 'int' );
		$lazyPlaceholder = $this->getInput ( 'lazyplaceholder', '', 'string' );
		$disableAnimateCSS = $this->getInput ( 'disableanimatecss', 0, 'int' );
		$integrationArticle = $this->getInput ( 'integrationarticle', 0, 'int' );
		$registrationEmail = $this->getInput ( 'registrationemail', '', 'string' );
		$disableCSS = $this->getInput ( 'disablecss', 0, 'int' );
		$disableOG = $this->getInput ( 'disable_og', 0, 'int' );
		$fbAppID = $this->getInput ( 'fb_app_id', '', 'string' );
		$disableTc = $this->getInput ( 'disable_tc', 0, 'int' );
		$colors = $this->getInput ( 'colors', '', 'RAW' );
		$googleFontsApiKey = $this->getInput ( 'google_font_api_key', 'AIzaSyDksiuBas8gVxA8_nxLUw69sq3FmJQR1wU', 'string' );
		$enableAI = $this->getInput ( 'enable_ai', 0, 'int' );
		$openaiApiKey = $this->getInput ( 'openai_api_key', '', 'string' );
		$openaiModel = $this->getInput ( 'openai_model', '', 'string' );
		$enableFrontendEditing = $this->getInput ( 'enable_frontend_editing', 1, 'int' );
		$addItemsFirst = $this->getInput ( 'add_items_first', 0, 'int' );
		$darkMode = $this->getInput ( 'dark_mode', 1, 'int' );
		$containerMaxWidth = $this->getInput ( 'container_max_width', 0, 'int' );
		$containerMaxWidth = max ( 1140, $containerMaxWidth );

		$params = ComponentHelper::getParams ( 'com_jpagebuilder' );
		$componentId = ComponentHelper::getComponent ( 'com_jpagebuilder' )->id;


		$params->set ( 'production_mode', $productionMode );
		$params->set ( 'gmap_api', trim ( $gmapApi ) );
		$params->set ( 'turnstyle_sitekey', trim ( $turnstyleSitekey ) );
		$params->set ( 'turnstyle_secretkey', trim ( $turnstyleSecretkey ) );
		$params->set ( 'ig_token', trim ( $igToken ) );
		$params->set ( 'fontawesome', $fontAwesome );
		$params->set ( 'disable_google_fonts', $disableGoogleFonts );
		$params->set ( 'lazyloadimg', $lazyLoadimg );
		$params->set ( 'lazyplaceholder', $lazyPlaceholder );
		$params->set ( 'disableanimatecss', $disableAnimateCSS );
		$params->set ( 'integrationarticle', $integrationArticle );
		$params->set ( 'registrationemail', $registrationEmail );
		$params->set ( 'disablecss', $disableCSS );
		$params->set ( 'disable_og', $disableOG );
		$params->set ( 'fb_app_id', $fbAppID );
		$params->set ( 'disable_tc', $disableTc );
		$params->set ( 'google_font_api_key', trim ( $googleFontsApiKey ) );
		$params->set ( 'enable_ai', $enableAI );
		$params->set ( 'openai_api_key', trim ( $openaiApiKey ) );
		$params->set ( 'openai_model', trim ( $openaiModel ) );
		$params->set ( 'enable_frontend_editing', $enableFrontendEditing );
		$params->set ( 'add_items_first', $addItemsFirst );
		$params->set ( 'dark_mode', $darkMode );
		$params->set ( 'container_max_width', $containerMaxWidth );

		if (! empty ( $colors )) {
			$this->saveColors ( $colors );
		}

		$db = Factory::getContainer()->get('DatabaseDriver');
		$table = new Joomla\CMS\Table\Extension($db);

		if (! $table->load ( $componentId )) {
			$response ['message'] = Text::_ ( "COM_JPAGEBUILDER_ERROR_MSG_FOR_FAILED_LOAD_EXTENSION" );
			$this->sendResponse ( $response, 500 );
		}

		$table->params = \json_encode ( $params );

		if (! $table->store ()) {
			$response ['message'] = Text::_ ( "COM_JPAGEBUILDER_ERROR_MSG_FOR_FAILED_STORE_EXTENSION" );
			$this->sendResponse ( $response, 500 );
		}

		$this->sendResponse ( true );
	}

	private function getColors() {
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

		return $colors;
	}
	private function saveColors(string $colorGroups) {
		if (! empty ( $colorGroups )) {
			$colorGroups = \json_decode ( $colorGroups );
		}

		$savedColors = $this->getColors ();

		if (! empty ( $savedColors )) {
			$savedColorsIds = array_map ( function ($item) {
				return $item->id;
			}, $savedColors );

			$payloadIds = array_map ( function ($item) {
				return $item->id;
			}, $colorGroups );

			$removedColorsIds = array_filter ( $savedColorsIds, function ($item) use ($payloadIds) {
				return ! \in_array ( $item, $payloadIds );
			} );

			if (! empty ( $removedColorsIds )) {
				$this->removeColor ( array_values ( $removedColorsIds ) );
			}
		}

		if (! empty ( $colorGroups )) {
			foreach ( $colorGroups as $group ) {
				$this->updateOrCreateColor ( $group, 'id' );
			}
		}
	}
	private function updateOrCreateColor($data, string $primaryKey) {
		$isNew = true;

		if (! empty ( $data->$primaryKey )) {
			$isNew = false;
		}

		$name = $data->name;
		$colors = ! empty ( $data->colors ) ? json_encode ( $data->colors ) : '';
		$record = ( object ) [ 
				'id' => ! $isNew ? $data->$primaryKey : null,
				'name' => $name,
				'colors' => $colors,
				'created_by' => Factory::getApplication()->getIdentity()->id,
				'created' => Factory::getDate ()->toSql (),
				'published' => 1
		];

		if ($isNew) {
			try {
				$db = Factory::getContainer()->get('DatabaseDriver');
				return $db->insertObject ( '#__jpagebuilder_colors', $record, 'id' );
			} catch ( \Exception $e ) {
				return false;
			}
		} else {
			try {
				$db = Factory::getContainer()->get('DatabaseDriver');
				return $db->updateObject ( '#__jpagebuilder_colors', $record, 'id', true );
			} catch ( \Exception $e ) {
				return false;
			}
		}
	}
	private function removeColor(array $ids) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );

		$query->delete ( $db->quoteName ( '#__jpagebuilder_colors' ) )->where ( $db->quoteName ( 'id' ) . ' IN (' . implode ( ',', $ids ) . ')' );

		$db->setQuery ( $query );

		try {
			$db->execute ();
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}
	public function applicationSettings() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'PATCH',
				'DELETE'
		], $method );

		if ($method === 'GET') {
			$this->getComponentSettings ();
		} else if ($method === 'PUT') {
			$this->saveApplicationSettings ();
		}
	}
}
