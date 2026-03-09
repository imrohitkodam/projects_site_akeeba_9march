<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\Filesystem\Path;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\String\StringHelper;

/**
 * Media trait files for managing the CRUD operation.
 *
 * @version 4.1.0
 */
trait JPageBuilderFrameworkAiContent {

	/**
	 * Get AI generated Content
	 *
	 * @return void
	 * @version 5.0.8
	 */
	private function getUrlToBase64Image() {
		$input = Factory::getApplication ()->getInput();
		$imageUrl = $input->get ( 'image_uri', '', 'string' );

		if (! isset ( $imageUrl ) || empty ( $imageUrl )) {
			$this->sendResponse ( [ 
					'status' => false,
					'message' => 'Image URL is missing.'
			], 400 );
		}

		$base64Image = $this->imageUrlToBase64 ( $imageUrl );

		$this->sendResponse ( [ 
				'status' => true,
				'dataUrl' => $base64Image
		], 200 );
	}

	/**
	 * Get AI generated Content
	 *
	 * @return void
	 * @version 5.0.8
	 */
	private function getAiGeneratedContent() {
		$cParams = ComponentHelper::getParams ( 'com_jpagebuilder' );
		$input = Factory::getApplication ()->getInput();
		$prompt = $input->get ( 'prompt', '', 'string' );
		$type = $input->get ( 'type', 'text', 'string' );
		$imageUri = $input->get ( 'image_uri', '', 'string' );
		$numberOfImages = $input->get ( 'number_of_images', 4, 'int' );
		$imageSize = $input->get ( 'image_size', '512x512', 'string' );
		$maxTokens = ( int ) $input->get ( 'max_tokens', '250', 'string' );

		// Set your OpenAI API key here
		$apiKey = $cParams->get ( 'openai_api_key', '' );
		$model = $cParams->get('openai_model', 'gpt-3.5-turbo');

		if (! isset ( $apiKey ) || empty ( $apiKey )) {
			$this->sendResponse ( [ 
					'status' => false,
					'message' => Text::_ ( 'COM_JPAGEBUILDER_AI_API_KEY_MISSING_MESSAGE' )
			], 400 );
		}

		$endpoint = 'https://api.openai.com/v1/chat/completions';

		// Check if the model is gpt-4.1 or above (new models)
		$isNewModel = preg_match('/^gpt-(4|4\.1|5)/', $model);
		
		// Decide which token key to use
		$tokenKeyName = $isNewModel ? 'max_completion_tokens' : 'max_tokens';
		
		// Calculate token margin if it's a new model
		if ($isNewModel) {
			$promptLen = StringHelper::strlen($prompt);
			$promptTokens = ceil($promptLen / 3);
			$safeTokens = (int) ceil($maxTokens * 10 + $promptTokens * 2);
			
			if (preg_match('/^gpt-5/', $model)) {
				$maxTokensByModel = 128000; // GPT-5
			} elseif (preg_match('/^gpt-4\.1/', $model)) {
				$maxTokensByModel = 32768; // GPT-4.1
			} else { // GPT-4 standard
				$maxTokensByModel = 16384;
			}
			
			$maxTokens = min($safeTokens, $maxTokensByModel);
		}
		
		// Request data
		$data = [ 
				"model" => $model,
				$tokenKeyName => $maxTokens,
				"messages" => [ 
						[ 
							"role" => "user",
							"content" => $prompt
						]
				]
		];

		if ($type === 'image') {
			$endpoint = 'https://api.openai.com/v1/images/generations';

			$data = [ 
					'prompt' => $prompt,
					'size' => $imageSize,
					'n' => ( int ) $numberOfImages,
					'user' => 'username'
			];
		}

		// Create JSON payload
		$payload = json_encode ( $data );

		if ($type === 'variation') {
			if (empty ( $imageUri )) {
				$this->sendResponse ( [ 
						'status' => false,
						'message' => 'Image url is Required.'
				], 400 );
			}

			$endpoint = 'https://api.openai.com/v1/images/variations';

			if (str_starts_with ( $imageUri, 'http' )) {
				$base64Image = $this->imageUrlToBase64 ( $imageUri );
				$finalImageFile = $this->dataUrlToCurlFile ( $base64Image );
			} else {
				$finalImageFile = $this->processImageForOpenAi ( $imageUri );
			}

			$data = [ 
					'image' => $finalImageFile,
					'size' => $imageSize,
					'n' => ( int ) $numberOfImages,
					'user' => 'username'
			];

			$payload = $data;
		}

		if ($type === 'generative_fill') {
			if (empty ( $imageUri ) || empty ( $prompt )) {
				$this->sendResponse ( [ 
						'status' => false,
						'message' => 'Image or Prompt is missing.'
				], 400 );
			}

			$endpoint = 'https://api.openai.com/v1/images/edits';

			$finalMaskImage = $this->dataUrlToCurlFile ( $imageUri );

			$data = [ 
					'prompt' => $prompt,
					'image' => $finalMaskImage,
					'size' => '512x512',
					'n' => 4,
					'user' => 'username'
			];

			$payload = $data;
		}

		// Initialize cURL session
		$ch = curl_init ();

		$httpHeader = [ 
				'Content-Type: application/json',
				'Authorization: Bearer ' . $apiKey
		];

		// Set cURL options
		curl_setopt ( $ch, CURLOPT_URL, $endpoint );
		curl_setopt ( $ch, CURLOPT_POST, 1 );
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $payload );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $type === 'text' || $type === 'image' ? $httpHeader : [ 
				'Authorization: Bearer ' . $apiKey
		] );

		// Execute cURL session and get the response
		$response = curl_exec ( $ch );
		$error = null;

		if ($response === false) {
			$error = curl_error ( $ch );
		}

		if ($error !== null) {
			$this->sendResponse ( [ 
					'status' => false,
					'message' => $error
			], 500 );
		}

		// Decode and print the response
		$responseArray = json_decode ( $response, true );

		if (($type !== 'text') && $responseArray && isset ( $responseArray ['data'] )) {
			$this->sendResponse ( $responseArray );
		}

		if ($type === 'text' && $responseArray && isset ( $responseArray ['choices'] [0] ['message'] ['content'] )) {
			$this->sendResponse ( $responseArray );
		}

		if ($responseArray && isset ( $responseArray ['error'] ) && isset ( $responseArray ['error'] ['message'] )) {
			$message = $responseArray ['error'] ['message'];

			if (strpos ( $message, 'maximum context length' )) {
				$message = Text::_ ( 'COM_JPAGEBUILDER_AI_MAXIMUM_CHARACTER_LIMIT_EXCEEDED_MESSAGE' );
			}

			$this->sendResponse ( [ 
					'status' => false,
					'message' => $message
			], 400 );
		}

		$this->sendResponse ( [ 
				'status' => false,
				'message' => Text::_ ( "COM_JPAGEBUILDER_GLOBAL_SOMETHING_WENT_WRONG" )
		], 500 );
	}
	private function makeCurlFile($file) {
		$mime = mime_content_type ( $file );
		$info = pathinfo ( $file );
		$name = $info ['basename'];
		$output = new CURLFile ( $file, $mime, $name );
		return $output;
	}
	private function processImageForOpenAi($image_uri) {
		$imagePath = Path::clean ( JPATH_ROOT . '/' . $image_uri );

		// Get temp cache folder
		$cachebase = Factory::getApplication ()->get ( 'cache_path', JPATH_CACHE ) . '/jpagebuilder_ai/';
		if (! is_dir ( $cachebase )) {
			mkdir ( $cachebase, 0777, true );
		}
		
		$formattedTempSquareImagePath = tempnam ( $cachebase, time () );
		// $tempFile = fopen($formattedTempSquareImagePath, 'wb');

		$imageMimeType = mime_content_type ( $imagePath );

		if ($imageMimeType !== 'image/png') {
			$isConvertSuccess = BuilderMediaHelper::changeAspectRatio ( '1:1', $imagePath, $formattedTempSquareImagePath, 'png' );

			if (! $isConvertSuccess) {
				$this->sendResponse ( [ 
						'status' => false,
						'message' => 'Image conversion failed. Please try again.'
				], 500 );
			}

			$finalImageFile = $this->makeCurlFile ( $formattedTempSquareImagePath );
		} else {
			$finalImageFile = $this->makeCurlFile ( $imagePath );
		}

		return $finalImageFile;
	}
	private function imageUrlToBase64($imageUrl) {
		$imageData = @file_get_contents ( $imageUrl );

		if ($imageData === false) {
			$this->sendResponse ( [ 
					'status' => false,
					'message' => 'Unable to fetch the image. Please try again.'
			], 500 ); // Unable to fetch the image
		}

		// Encode the image data as Base64
		$base64Data = base64_encode ( $imageData );

		if ($base64Data === false) {
			$this->sendResponse ( [ 
					'status' => false,
					'message' => 'Failed to encode as Base64. Please try again.'
			], 500 ); // Failed to encode as Base64
		}

		// Create a data URL with the Base64-encoded image data
		$dataUrl = 'data:image/png;base64,' . $base64Data;

		return $dataUrl;
	}
	private function dataUrlToCurlFile($dataUrlImage) {
		$mimeRegex = '/^data:([^;]+);base64,([a-zA-Z0-9\/+]+=*)$/';

		if (! preg_match ( $mimeRegex, $dataUrlImage, $matches )) {
			$this->sendResponse ( [ 
					'message' => "Invalid data URL format."
			], 400 );
		}

		$base64Data = $matches [2];

		$decodedData = base64_decode ( $base64Data );

		// Get temp cache folder
		$cachebase = Factory::getApplication ()->get ( 'cache_path', JPATH_CACHE ) . '/jpagebuilder_ai/';
		if (! is_dir ( $cachebase )) {
			mkdir ( $cachebase, 0777, true );
		}
		
		// Create a temporary file
		$tempFileName = tempnam ( $cachebase, 'maskimage' );
		if(!$tempFileName) {
			return '';
		}
		$tempFile = fopen ( $tempFileName, 'wb' );
		fwrite ( $tempFile, $decodedData );
		fclose ( $tempFile );

		$tempSquareImageDest = tempnam ( $cachebase, 'square' );

		$isConvertSuccess = BuilderMediaHelper::changeAspectRatio ( '1:1', $tempFileName, $tempSquareImageDest, 'png' );

		if (! $isConvertSuccess) {
			$this->sendResponse ( [ 
					'status' => false,
					'message' => 'Image conversion failed. Please try again.'
			], 500 );
		}

		$finalImageFile = $this->makeCurlFile ( $tempSquareImageDest );

		return $finalImageFile;
	}

	/**
	 * OpenAi endpoint for the API.
	 *
	 * @return void
	 * @version 4.1.0
	 */
	public function aiContent() {
		$method = $this->getInputMethod ();
		$this->checkNotAllowedMethods ( [ 
				'PUT',
				'DELETE'
		], $method );

		switch ($method) {
			case 'POST' :
				$this->getAiGeneratedContent ();
				break;
			case 'PATCH' :
				$this->getUrlToBase64Image ();
				break;
		}
	}
}
