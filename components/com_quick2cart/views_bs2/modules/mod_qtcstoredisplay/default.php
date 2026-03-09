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
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

//LOAD LANG FILE
$lang = Factory::getLanguage();
$lang->load('mod_qtcstoredisplay', JPATH_ROOT);

//LOAD CSS FILES AND JS FILE
$comparams = ComponentHelper::getParams( 'com_quick2cart' );
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/quick2cart.css' );
$comquick2cartHelper = new comquick2cartHelper();
//$Itemid = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=cart');
?>
<div class="<?php echo Q2C_WRAPPER_CLASS . ' ' . $params->get('moduleclass_sfx');?>" >
	<div  class=''>
		<?php
		if($qtc_modViewType=="qtc_listView")
		{
			$comquick2cartHelper = new comquick2cartHelper();
			$view = $comquick2cartHelper->getViewpath('vendor','storelist');
			$options = $target_data;
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
		}
		else
		{
				$max_scroll_ht=!empty($qtc_mod_scroll_height)?trim($qtc_mod_scroll_height):250;
				$scroll_style="overflow-y:auto;max-height:".$max_scroll_ht."px;overflow-x:hidden;"
			?>
			<ul class="thumbnails" style="<?php echo $scroll_style;?>" >
			<?php
			foreach($target_data as $data)
			{
				$data = (array)$data;
				$path = $comquick2cartHelper->getViewpath('vendor', 'thumbnail', "SITE", "SITE");
				//$path = JPATH_SITE . '/components/com_quick2cart/views/vendor/tmpl/thumbnail.php';
					//@TODO  condition vise mod o/p
				ob_start();
				include($path);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
				//break;
			}
				?>
			</ul>
			<?php
		} ?>
	</div>
</div>
