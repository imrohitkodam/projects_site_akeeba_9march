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

use Joomla\CMS\HTML\HTMLHelper;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('behavior.keepalive');
HTMLHelper::_('bootstrap.renderModal');

$comquick2cartHelper = new comquick2cartHelper
?>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
	<form id="adminForm" name="adminForm" method="post" class="form-validate" enctype="multipart/form-data">
		<?php
			$actionViewName       = 'shipprofile';
			$actionControllerName = 'shipprofile';
			$formName             = 'adminForm';

			// Check for view override
			$att_list_path = $comquick2cartHelper->getViewpath('shipprofile', 'shipprofiledata_bs5', "ADMINISTRATOR", "ADMINISTRATOR");
			ob_start();
			include($att_list_path);
			$item_options = ob_get_contents();
			ob_end_clean();
			echo $item_options;
		?>
	</form>
</div>
