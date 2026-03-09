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

use Joomla\CMS\Component\ComponentHelper;

$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
JLoader::register('comquick2cartHelper', $path );
JLoader::load('comquick2cartHelper');

$comquick2cartHelper   = new comquick2cartHelper();
$comQuick2cartParams   = ComponentHelper::getParams('com_quick2cart');
$bsVersion             = $comQuick2cartParams->get('bootstrap_version', '', 'STRING');

if (empty($bsVersion))
{
	$bsVersion = (JVERSION > '4.0.0') ? 'bs5' : 'bs3';
}

$view                  = $comquick2cartHelper->getViewpath('category','categorylist_' . $bsVersion);
$qtc_mod_scroll_height = $params->get('scroll_height');
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
