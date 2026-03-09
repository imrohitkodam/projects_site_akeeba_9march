<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$lang = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);
?>
<div class=" <?php echo Q2C_WRAPPER_CLASS; ?>">
	<div id="j-main-container">
	<?php
		// Shipping is disabled msg
		if ($this->isShippingEnabled == 0)
		{
			?>
			<div class="alert alert-danger">
				<?php echo Text::_('COM_QUICK2CART_U_HV_DISABLED_SHIPPING_OPTION_HELP_MSG'); ?>
			</div>
			<?php

			return false;
		}

		if (!empty($this->form))
		{
			echo $this->form;
		}
	?>
	</div>
</div>
