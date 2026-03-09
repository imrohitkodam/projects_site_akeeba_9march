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

$path = JPATH_SITE . '/components/com_quick2cart/helper.php';
//if(!class_exists('comquick2cartHelper'))
{
  //require_once $path;
   JLoader::register('comquick2cartHelper', $path );
   JLoader::load('comquick2cartHelper');
}
$comquick2cartHelper=new comquick2cartHelper();
$view=$comquick2cartHelper->getViewpath('category','categorylist');
$qtc_mod_scroll_height=$params->get('scroll_height');
?>
<div class="<?php echo Q2C_WRAPPER_CLASS . ' ' . $params->get('moduleclass_sfx'); ?>" >

	<div class="">
		<?php
		ob_start();
		include($view);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		?>

	</div>
</div>
