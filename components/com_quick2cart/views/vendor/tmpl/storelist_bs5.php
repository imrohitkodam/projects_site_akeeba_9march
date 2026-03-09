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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

// DECLARATION SECTION
$classes       = !empty($qtc_classes) ? $classes : '';
$max_scroll_ht = !empty($qtc_mod_scroll_height) ? trim($qtc_mod_scroll_height) . 'px' : '412px';
$scroll_style  = "overflow-y:auto; max-height:" . $max_scroll_ht . "; overflow-x:hidden;";

$app                 = Factory::getApplication();
$storeHelper         = new storeHelper();
$comquick2cartHelper = new comquick2cartHelper;
$options             = json_decode(json_encode($options), false);
$menu_itemid         = $comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=vendor&layout=default');
?>
<div class="row qtc_store_list <?php echo $classes;?>" style="<?php echo $scroll_style;?>">
	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
		<div class="tj-list-group">
			<strong class="tj-list-group-item"><?php echo Text::_('QTC_SEL_VENDOR');?></strong>
			<?php
			$selected_storeid       = $app->getUserStateFromRequest('store_id', 'store_id', '', 'INTEGER' );
			$selected_current_store = $app->getUserStateFromRequest('current_store', 'current_store', '', 'INTEGER' );
			$selected               = !empty($selected_storeid)?$selected_storeid : $selected_current_store;

			if (!empty($options))
			{
				foreach ($options as $op)
				{
					$storeLink    = $storeHelper->getStoreLink($op->id);
					$activeoption = ($selected == $op->id) ? "active" : "";

					if ($op->live == 1)
					{
						?>
						<a class="tj-list-group-item <?php echo $activeoption;?>" href="<?php echo $storeLink ;?>">
							<?php echo htmlspecialchars(ucfirst($op->title), ENT_COMPAT, 'UTF-8'); ?>
						</a>
						<?php
					}
				}
			}
			?>
		</div>
	</div>
</div>

