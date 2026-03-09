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
$document  = Factory::getDocument();
HTMLHelper::_('stylesheet','components/com_quick2cart/assets/css/quick2cart.css' );

$comquick2cartHelper = new comquick2cartHelper();

$bsVersion = $comparams->get('bootstrap_version', '', 'STRING');

if (empty($bsVersion))
{
	$bsVersion = (JVERSION > '4.0.0') ? 'bs5' : 'bs3';
}

?>
<div class="techjoomla-bootstrap <?php echo $params->get('moduleclass_sfx','');?>"  >
	<div  class='row-fluid'>
		<?php
		if($qtc_modViewType=="qtc_listView")
		{
			$comquick2cartHelper = new comquick2cartHelper();
			$view = $comquick2cartHelper->getViewpath('vendor','storelist_' . $bsVersion);
			$options = $target_data;
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;
		}
		else
		{
			$max_scroll_ht = (!empty($qtc_mod_scroll_height)) ? trim($qtc_mod_scroll_height) : 250;
			$scroll_style  = "overflow-y:auto;max-height:" . $max_scroll_ht . "px;overflow-x:hidden;"
			?>
			<ul class="thumbnails" style="<?php echo $scroll_style;?>" >
				<?php
				foreach($target_data as $data)
				{
					$data = (array)$data;
					$path = $comquick2cartHelper->getViewpath('vendor', 'thumbnail', "SITE", "SITE");
					ob_start();
					include($path);
					$html = ob_get_contents();
					ob_end_clean();
					echo $html;
				}
				?>
			</ul>
			<?php
		} ?>
	</div>
</div>
