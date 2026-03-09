<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Image Shapes traits
 */
trait JPageBuilderFrameworkImageShapes {
	private function getImageShapes() {
		$response = $this->processGetImageShapes ();

		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
	private function addImageShape() {
		$shape = $this->getInput ( 'shape', '', 'base64' );

		if (empty ( $shape )) {
			$response ['message'] = Text::_ ( 'COM_JPAGEBUILDER_EDITOR_SVG_INFORMATION_MISSING' );
			$this->sendResponse ( $response, 400 );
		}

		$response = $this->processAddImageShape ( $shape );
		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
	private function deleteImageShape() {
		$id = $this->getInput ( 'id', '', 'int' );

		if (empty ( $id )) {
			$response ['message'] = Text::_ ( 'COM_JPAGEBUILDER_EDITOR_SVG_INFORMATION_MISSING' );
			$this->sendResponse ( $response, 404 );
		}

		$response = $this->processDeleteImageShape ( $id );
		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
	private function updateImageShape() {
		$id = $this->getInput ( 'id', '', 'string' );
		$shape = $this->getInput ( 'shape', '', 'string' );

		if (empty ( $id ) || empty ( $shape )) {
			$response ['message'] = Text::_ ( 'COM_JPAGEBUILDER_EDITOR_SVG_INFORMATION_MISSING' );
			$this->sendResponse ( $response, 404 );
		}

		$response = $this->processUpdateImageShape ( $id, $shape );
		$this->sendResponse ( $response ['response'], $response ['statusCode'] );
	}
	public function imageShapes() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'PUT'
		], $method );

		switch ($method) {
			case 'POST' :
				$this->addImageShape ();
				break;

			case 'PATCH' :
				$this->updateImageShape ();
				break;

			case 'DELETE' :
				$this->deleteImageShape ();
				break;

			default :
				$this->getImageShapes ();
				break;
		}
	}
	public function processGetImageShapes() {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->select ( [ 
				'id',
				'name',
				'shape'
		] )->from ( $db->quoteName ( '#__jpagebuilder_image_shapes' ) );

		$db->setQuery ( $query );

		$shapes = [ ];

		try {
			$shapes = $db->loadObjectList ();
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			return [ 
					'response' => $response,
					'statusCode' => 500
			];
		}

		return [ 
				'response' => $shapes,
				'statusCode' => 200
		];
	}
	public function processAddImageShape($shape) {
		$decoded_shape = base64_decode ( $shape );
		$pattern = '/<path\b[^>]*>/s';
		preg_match ( $pattern, $decoded_shape, $matches );
		$is_valid_svg = $matches && count ( $matches ) === 1;

		if (! $is_valid_svg) {
			$response ['message'] = Text::_ ( 'COM_JPAGEBUILDER_EDITOR_INVALID_SVG_SHAPE' );
			return [ 
					'response' => $response,
					'statusCode' => 400
			];
		}

		$random_id = uniqid ( mt_rand (), true );
		$data = new stdClass ();
		$data->name = $random_id;
		$data->shape = $shape;
		$data->created = Factory::getDate ()->toSql ();
		$data->created_by = Factory::getApplication ()->getIdentity ()->id;

		try {
			$db = Factory::getContainer()->get('DatabaseDriver');
			$db->insertObject ( '#__jpagebuilder_image_shapes', $data, 'id' );

			return [ 
					'response' => $data,
					'statusCode' => 201
			];
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			return [ 
					'response' => $response,
					'statusCode' => 500
			];
		}
	}
	public function processUpdateImageShape($id, $shape) {
		$db = Factory::getContainer()->get('DatabaseDriver');
		$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
		$query->update ( $db->quoteName ( '#__jpagebuilder_image_shapes' ) )->set ( $db->quoteName ( 'shape' ) . ' = ' . $db->quote ( $shape ) )->where ( $db->quoteName ( 'id' ) . ' = ' . $db->quote ( $id ) );

		$db->setQuery ( $query );

		try {
			$db->execute ();
			return [ 
					'response' => Text::_ ( 'COM_JPAGEBUILDER_EDITOR_SVG_IMAGE_SHAPE_UPDATED_SUCCESSFULLY' ),
					'statusCode' => 200
			];
		} catch ( \Exception $e ) {
			$response ['message'] = $e->getMessage ();
			return [ 
					'response' => $response,
					'statusCode' => 500
			];
		}
	}
	public function processDeleteImageShape($id) {
		try {
			$db = Factory::getContainer()->get('DatabaseDriver');

			$query = method_exists ( $db, 'createQuery' ) ? $db->createQuery () : $db->getQuery ( true );
			$query->delete ( $db->quoteName ( '#__jpagebuilder_image_shapes' ) );
			$query->where ( $db->quoteName ( 'id' ) . ' = ' . $db->quote ( $id ) );

			$db->setQuery ( $query );
			$db->execute ();

			return [ 
					'response' => Text::_ ( 'COM_JPAGEBUILDER_EDITOR_SVG_IMAGE_SHAPE_DELETED_SUCCESSFULLY' ),
					'statusCode' => 200
			];
		} catch ( Exception $e ) {
			$response ['message'] = $e->getMessage ();
			return [ 
					'response' => $response,
					'statusCode' => 500
			];
		}
	}
}
