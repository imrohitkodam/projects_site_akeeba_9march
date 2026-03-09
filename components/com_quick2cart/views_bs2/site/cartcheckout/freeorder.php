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

use Joomla\CMS\Language\Text;
?>

<form method="post" name="adminForm" class="" id="adminForm">
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>" >


	<input type="hidden" name="option" value="com_quick2cart">
	<input type="hidden" id="task" name="task" value="cartcheckout.processFreeOrder">
	<input type="hidden" name="orderid" value="<?php echo $order_id; ?>">

	<div class="form-actions qtc_formActionAlign" >
			<!--<a class="btn btn-success bth-large" href="<?php //echo $link?>" >
			<?php echo Text::_('QTC_CONFORM_ORDER'); ?>
			</a>  -->
			<input type="submit" class="btn btn-success btn-large" value="<?php echo Text::_('QTC_CONFORM_ORDER'); ?>">
	</div >


</div>
</form>
