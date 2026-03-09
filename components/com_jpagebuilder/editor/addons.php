<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\Filesystem\File;
use Joomla\Filesystem\Path;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;

/**
 * Addons class
 *
 * @since 1.0.0
 */
abstract class JpagebuilderAddons {
	/**
	 * The addon information
	 *
	 * @var object|null
	 */
	protected $addon = null;

	/**
	 * Check placeholder file path for each media image
	 *
	 * @return mixed
	 *
	 * @since 1.0.0
	 */
	protected function get_image_placeholder($src) {
		$config = ComponentHelper::getParams ( 'com_jpagebuilder' );
		$lazyload = $config->get ( 'lazyloadimg', '0' );

		if ($lazyload) {
			$filename = basename ( $src );
			$mediaPath = 'media/com_jpagebuilder/placeholder';
			$basePath = JPATH_ROOT . '/' . $mediaPath . '/' . $filename;
			$defaultImg = 'https://storejextensions.org/cdn/addons/image1.jpg';

			if (file_exists ( $basePath )) {
				return $mediaPath . '/' . $filename;
			} elseif ($src == $defaultImg) {
				return $src;
			} else {
				$placeholderUrl = $config->get ( 'lazyplaceholder', '/components/com_jpagebuilder/assets/images/lazyloading-placeholder.svg' );

				$pattern = '/^https?:\/\//';
				if (preg_match ( $pattern, $placeholderUrl )) {
					return $placeholderUrl;
				}

				return Uri::root ( true ) . $placeholderUrl;
			}
		}

		return false;
	}

	/**
	 * Get any valid image dimension
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	protected function get_image_dimension($src) {
		$src = JPATH_BASE . Path::clean ( $src );

		if (! file_exists ( $src )) {
			return [ ];
		}

		preg_match ( '/\__(.*?)\./', $src, $match );

		if (count ( $match ) > 1) {
			$dimension = explode ( 'x', $match [1] );

			return [ 
					'width="' . $dimension [0] . '"',
					'height="' . $dimension [1] . '"'
			];
		}

		$validImageExtensions = [ 
				'jpg',
				'jpeg',
				'png'
		];
		$extension = strtolower ( pathinfo ( $src, PATHINFO_EXTENSION ) );

		if (\in_array ( $extension, $validImageExtensions )) {
			$dimension = \getimagesize ( $src );

			if (! empty ( $dimension )) {
				return [ 
						'width="' . $dimension [0] . '"',
						'height="' . $dimension [1] . '"'
				];
			}
		}

		return [ ];
	}

	/**
	 * Constructor function
	 *
	 * @param array $addon
	 *
	 * @return mixed
	 * @since 1.0.0
	 */
	public function __construct($addon) {
		if (! $addon) {
			return false;
		}

		$this->addon = $addon;
	}
}
