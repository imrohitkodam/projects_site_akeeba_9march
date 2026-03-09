<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Trait for managing integrations API endpoint.
 */
trait JPageBuilderFrameworkIntegration {

	/**
	 * Get the list of integrations
	 *
	 * @return array
	 * @since 4.0.0
	 */
	private function integrationList() {
		return [ 
				'content' => [ 
						'title' => Text::_ ( "COM_JPAGEBUILDER_JOOMLA_ARTICLE" ),
						'group' => 'content',
						'name' => 'jpagebuilder',
						'view' => 'article',
						'id_alias' => 'id',
						'thumbnail' => Uri::root () . 'components/com_jpagebuilder/assets/images/joomla_article.jpg',
						'enabled' => PluginHelper::isEnabled ( 'content', 'jpagebuilder' )
				]
		];
	}
	public function integrations() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'POST',
				'DELETE',
				'PUT'
		], $method );

		switch ($method) {
			case 'GET' :
				$this->loadIntegrations ();
				break;
			case 'PATCH' :
				$this->toggleIntegration ();
				break;
		}
	}

	/**
	 * Load Integrations from the list
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function loadIntegrations() {
		$response = new stdClass ();

		try {
			$results = $this->integrationList ();

			/**
			 *
			 * @TODO: this filter will be removed for getting all the integrations.
			 */
			$results = array_filter ( $results, function ($item) {
				return $item ['group'] !== 'k2';
			} );

			$this->sendResponse ( $results );
		} catch ( Exception $e ) {
			$response->data = $e->getMessage ();
			$this->sendResponse ( $response );
		}
	}

	/**
	 * Toggle integration
	 *
	 * @return void
	 * @since 4.0.0
	 */
	public function toggleIntegration() {
		$user = Factory::getApplication()->getIdentity();
		$model = $this->getModel ( 'Editor' );

		$response = new stdClass ();

		$integration_group = $this->getInput ( 'integration', '', 'string' );

		if (empty ( $integration_group )) {
			$response ['message'] = Text::_ ( "COM_JPAGEBUILDER_ERROR_MSG_FOR_INVALID_INTEGRATION" );
			$this->sendResponse ( $response, 400 );
		}

		$authorised = $user->authorise ( 'core.admin', 'com_jpagebuilder' ) || $user->authorise ( 'core.manage', 'com_jpagebuilder' );

		if (! $authorised) {
			$response ['message'] = Text::_ ( 'JERROR_ALERTNOAUTHOR' );
			$this->sendResponse ( $response, 403 );
		}

		$integrations = $this->integrationList ();

		if (isset ( $integrations [$integration_group] )) {
			$integration = $integrations [$integration_group];

			$result = $model->toggleIntegration ( $integration ['group'], $integration ['name'] );
			$integration ['enabled'] = $result;
			$response->data = $integration;
			$this->sendResponse ( $response, 200 );
		} else {
			$response ['message'] = Text::_ ( "COM_JPAGEBUILDER_ERROR_MSG_FOR_UNABLE_FIND_INTEGRATION" );
			$this->sendResponse ( $response, 404 );
		}

		$response ['message'] = Text::_ ( "COM_JPAGEBUILDER_ERROR_MSG_FOR_ENABLED_OR_DISABLE_INTEGRATION" );
		$this->sendResponse ( $response, 500 );
	}
}
