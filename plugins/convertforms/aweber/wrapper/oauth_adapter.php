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

// No direct access
defined('_JEXEC') or die('Restricted access');

interface AWeberOAuthAdapter
{
	public function request($method, $uri, $data = array());
	public function getRequestToken($callbackUrl = false);
}

?>
