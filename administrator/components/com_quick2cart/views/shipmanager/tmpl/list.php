<?php 
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die( 'Restricted access' );
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('behavior.formvalidator');
$document = Factory::getDocument();

$js_key1="Joomla.submitbutton = function(task)
{
	if(task=='add')
	{
		window.location = 'index.php?option=com_quick2cart&view=shipmanager&layout=default';
	}
	else if(task=='remove')
	{
		Joomla.submitform(task);
	} 
	else
		window.location = 'index.php?option=com_quick2cart';
}";

$document->addScriptDeclaration($js_key1);
?>

<div class="techjoomla-bootstrap" >
	<form action="" name="adminForm" id="adminForm" class="form-validate" method="post">
		<?php
		// @ sice version 3.0 Jhtmlsidebar for menu
		if (!empty( $this->sidebar)) : ?>
			<div id="j-sidebar-container" class="span2">
				<?php echo $this->sidebar; ?>
			</div>
			<div id="j-main-container" class="span10">
		<?php else : ?>
			<div id="j-main-container">
		<?php endif;
		?>
		<table class="table table-condensed">
			<th title='<?php echo Text::_('DELET');?>'><?php echo Text::_('DELETE'); ?></th>
			<th title='<?php echo Text::_('KEY');?>'><?php echo Text::_('KEY'); ?></th>
			<th title='<?php echo Text::_('VALUE');?>' ><?php echo Text::_('VALUE'); ?></th>
			<th title='<?php echo Text::_('SHIP_VALUE');?>'><?php echo Text::_('SHIP_VALUE'); ?></th>
			<?php
			$i = 0;

			foreach($this->shippinglist as $key)
			{	
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center"><?php echo HTMLHelper::_('grid.id', $i, $key->id); ?></td>
					<td><?php echo $key->key; ?></td>
					<td><?php echo $key->value; ?></td>	
					<td>							
						<?php 
							$arr = array();
							foreach($key->shipcharges as $charges)
							{
								array_push($arr,$charges->shipprice);
							} 
								
							$arr_str=implode(', ',$arr);
							echo $arr_str;	
						?>						
					</td>
				</tr>
				<?php
				$i++;	
			}
		?>
		</table>
		<input type="hidden" name="option" value="com_quick2cart" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="controller" value="shipmanager" />
		<input type="hidden" name="view" value="shipmanager" /> 
		<input type="hidden" name="jversion" value="<?php echo Text::_( 'JVERSION'); ?>" />
	</form>	
</div>
