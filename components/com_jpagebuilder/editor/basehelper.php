<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */

/**
 * No direct access.
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * The Base migrator class.
 *
 * @since 4.0.0
 */
class JpagebuilderHelperBase {
	/**
	 * The addon ID selector.
	 *
	 * @var string
	 * @since 4.0.0
	 */
	protected $id;

	/**
	 * The device lists array.
	 *
	 * @var array
	 * @since 4.0.0
	 */
	protected $deviceList = [ 
			"xl",
			"lg",
			"md",
			"sm",
			"xs"
	];
	private function sanitizeID(string $id): string {
		return \strpos ( $id, '#' ) !== 0 ? '#' . $id : $id;
	}

	/**
	 * Get the addon ID selector.
	 *
	 * @return string The addon ID selector provided.
	 * @since 4.0.0
	 */
	public function getID(): string {
		return $this->id;
	}

	/**
	 * Set/update the addon ID selector.
	 *
	 * @param string $id
	 *        	The new addon ID selector.
	 * @param bool $force
	 *        	Set the ID whatever the id is provided, no
	 *        	sanitization is required.
	 *        	
	 *        	
	 * @since 4.0.0
	 */
	public function setID(string $id, bool $force = false) {
		if ($force) {
			$this->id = $id;
		} else {
			$this->id = $this->sanitizeID ( $id );
		}
	}

	/**
	 * Get the device list all except the default one.
	 *
	 * @return array
	 * @since 4.0.0
	 * @todo Currently the `xl` device is excluded. It will be included
	 *       while deciding about the desktop/large devices.
	 */
	protected function getDeviceListExcludeDefault(): array {
		return array_filter ( $this->deviceList, function ($device) {
			return $device !== JpagebuilderBase::$defaultDevice;
		} );
	}

	/**
	 * Generate The Selector from the raw selector and the ID selector.
	 *
	 * @param string $selector
	 *        	The selectors provided.
	 * @return string
	 * @since 4.0.0
	 */
	protected function generateSelector(string $selector): string {
		$selector = preg_replace ( "@\s+@", " ", trim ( $selector ) );

		if (empty ( $selector ) || $selector === ':id' || $selector === ':self') {
			return $this->getID ();
		}

		$selectorArray = array_map ( function ($item) {
			$item = preg_replace ( "@\s+@", " ", trim ( $item ) );
			if (! $item)
				return;

			if (strpos ( $item, '&' ) !== false && strpos ( $item, '&' ) === 0) {
				return $this->getID () . trim ( substr ( $item, 1 ) );
			}

			return $this->getID () . ' ' . $item;
		}, explode ( ',', $selector ) );

		return implode ( ',', $selectorArray );
	}

	/**
	 * The CSS migration constructor.
	 *
	 * @param string $id
	 *        	The ID selector.
	 * @param bool $force
	 *        	Flag to set the ID as whatever it is provided, no sanitization is required.
	 *        	
	 * @since 4.0.0
	 */
	public function __construct(string $id, bool $force = false) {
		$this->setID ( $id, $force );
	}
}
