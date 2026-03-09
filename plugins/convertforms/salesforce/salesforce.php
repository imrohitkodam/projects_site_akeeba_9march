<?php

/**
 * @package         Convert Forms
 * @version         5.1.2 Pro
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

class plgConvertFormsSalesForce extends \ConvertForms\Plugin
{
	/**
	 *  Main method to store data to service
	 *
	 *  @return  void
	 */
	public function subscribe()
	{
		$options = [
			'api' => $this->lead->campaign->organizationID,
			'test_mode' => isset($this->lead->campaign->test_mode) ? $this->lead->campaign->test_mode : false
		];
		
		$api = new \NRFramework\Integrations\Salesforce($options);
		$api->subscribe(
			$this->lead->email,
			$this->lead->params
		);
		
		if (!$api->success())
		{
			throw new Exception($api->getLastError());
		}
	}
}