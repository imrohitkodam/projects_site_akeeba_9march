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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');

// Import CSS
HTMLHelper::_('stylesheet', 'administrator/components/com_quick2cart/assets/css/quick2cart.css');
HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/q2c-tables.css');

$user      = Factory::getUser();
$userId    = $user->id;
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');
?>
<script>
	Joomla.submitbutton = function(task)
	{
		if (task == 'promotions.delete')
		{
			var confirmdelete = confirm("<?php echo Text::_('COM_QUICK2CART_PROMOTIONS_DELETE_POPUP');?>");

			if( confirmdelete == false )
			{
				return false;
			}
			else
			{
				Joomla.submitform(task, document.getElementById('adminForm'));
			}
		}
		else
		{
			Joomla.submitform(task, document.getElementById('adminForm'));
		}
	}
</script>
<div class="<?php echo Q2C_WRAPPER_CLASS; ?> my_promotions">
	<form action="<?php echo Route::_('index.php?option=com_quick2cart&view=promotions&Itemid='. $this->promotionsItemId); ?>" method="post" name="adminForm" id="adminForm">
		<?php
		$active = 'promotions';
		ob_start();
		include($this->toolbar_view_path);
		$html = ob_get_contents();
		ob_end_clean();
		echo $html;
		?>
		<div id="j-main-container container">
			<h1><strong><?php echo Text::_("COM_QUICK2CART_PROMOTIONS");?></strong></h1>
				<div class="clearfix">&nbsp;</div>
				<div><?php echo $this->toolbarHTML; ?></div>
				<div class="clearfix">&nbsp;</div>
			<hr>
			<?php
				$comquick2cartHelper = new Comquick2cartHelper();
				$view = $comquick2cartHelper->getViewpath('promotions', 'list_bs5');
				ob_start();
				include($view);
				$html = ob_get_contents();
				ob_end_clean();
				echo $html;
			?>
			<input type="hidden" name="task" value=""/>
			<input type="hidden" name="boxchecked" value="0"/>
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
			<?php echo HTMLHelper::_('form.token'); ?>
		</div>
	</form>
</div>
