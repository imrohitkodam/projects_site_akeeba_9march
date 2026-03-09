<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);
$data = $displayData;

Text::script('COM_QUICK2CART_ADD_PROD_GATTRIBUTE_OPTION_ALREADY_PRESENT', true);
?>
<div class="">
	<span class="qtcAddGlobalOption qtc_float_right">
		<button type="button" class="qtcHandPointer btn btn-primary" onclick="qtcLoadAttributeOption(this)" title="<?php echo Text::_('COM_QUICK2CART_ADD_GOLB_ATTROPTIONS_DESC'); ?>">
			<?php echo Text::_('COM_QUICK2CART_ADD_GOLB_ATTROPTIONS') ?>
		</button>
	</span>
	<select class="globalOptionSelect qtc_float_right form-select">
		<option value="" ><?php echo Text::_('COM_QUICK2CART_ADDPROD_LOADALL_GOLB_ATTROPTIONS') ?></option>
		<?php
		if (!empty($data))
		{
			foreach ($data as $op_key => $option)
			{
				?>
				<option value="<?php echo $option->id ?>" > <?php echo $option->option_name ?></option>
				<?php
			}
		}
		?>
	</select>
	<div class="clearfix"></div>
</div>

