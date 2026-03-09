<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access.
defined('_JEXEC') or die();

use Joomla\CMS\HTML\HTMLHelper;
HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css');

$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
JLoader::register('comquick2cartHelper', $path);
JLoader::load('comquick2cartHelper');

$comquick2cartHelper = new comquick2cartHelper();
$view = $comquick2cartHelper->getViewpath('category','categorypin');
?>
<div class="techjoomla-bootstrap <?php echo $moduleclass_sfx; ?>" >
	<div class="row-fluid">
	<?php
		ob_start();
		include($view);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
	?>
	</div>
</div>
