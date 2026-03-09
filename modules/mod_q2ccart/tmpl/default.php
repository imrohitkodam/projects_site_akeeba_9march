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
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.renderModal', 'a.modal');
HTMLHelper::_('stylesheet','modules/mod_q2ccart/assets/css/q2ccart.css');
?>
<div class="<?php echo Q2C_WRAPPER_CLASS . ' ' . $moduleclass_sfx;?> q2c-cart-mod">
	<div class="q2c-cart-mod__img">
		<a data-bs-target="#toggleCartModal" data-bs-toggle="modal" data-target="#toggleCartModal" data-toggle="modal">
			<img src="components/com_quick2cart/assets/images/cart.png" class="q2c-cart-mod__icon"/>
		</a>
		<div class="q2c-cart-mod__count"><?php echo $cartCount;?></div>
		<?php
			$cartlink = Uri::root() . "index.php?option=com_quick2cart&view=cart&tmpl=component";

			echo HTMLHelper::_(
				'bootstrap.renderModal',
				'toggleCartModal',
				array(
					'url'        => $cartlink,
					'modalWidth' => 80,
					'bodyHeight' => 70,
					'width' => '800px',
					'height' => '550px'
				)
			)
		?>
	</div>
</div>
