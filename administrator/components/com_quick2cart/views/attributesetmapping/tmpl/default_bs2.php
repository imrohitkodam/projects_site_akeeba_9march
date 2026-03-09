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

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');

$qtc_cat_options = HTMLHelper::_('category.options', 'com_quick2cart', array('filter.published' => array(1)));

HTMLHelper::_('stylesheet', 'components/com_quick2cart/assets/css/q2c-tables.css');
?>
<script type="text/javascript">
	techjoomla.jquery = jQuery.noConflict();

	Joomla.submitbutton = function(task)
	{
		if (task == 'attributesetmapping.cancel') {
			Joomla.submitform(task, document.getElementById('attributemapping'));
		}
		else
		{
			if (task != 'attributesetmapping.cancel' && document.formvalidator.isValid(document.getElementById('attributemapping')))
			{
				Joomla.submitform(task, document.getElementById('attributemapping'));
			}
			else {
				alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
			}
		}
	}
</script>
<form action="<?php echo Route::_('index.php?option=com_quick2cart&layout=edit');?>" method="post" enctype="multipart/form-data" name="adminForm" id="attributemapping" class="form-validate">
	<div class="alert alert-info">
		<?php echo Text::_('COM_QUICK2CART_ATTRIBUTE_SET_MAPPING_INFO');?>
	</div>
	<div id="no-more-tables">
		<table class="table table-responsive table-condensed table-bordered table-hover">
			<thead>
				<tr>
					<th width="2%"><?php echo Text::_('COM_QUICK2CART_CAT');?></th>
					<th width="2%"><?php echo Text::_('COM_QUICK2CART_GLOBAL_ATTRIBUTE_SET');?></th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach($qtc_cat_options as $category)
				{
					$attribute_id = $this->model->getAttributeSet($category->value);
					$count        = $this->model->checkForProductsInCategory($category->value);
					$disabled     = ($count > 0) ? 'class="inputbox input-medium" disabled="disabled"' : 'class="inputbox input-medium"';
					?>
					<tr>
						<td width="2%">
							<input
								type="text"
								class="disabled"
								disabled="disabled"
								name="cats[catid][<?php echo $category->value?>]"
								value ="<?php echo $category->text?>"/>
						</td>
						<td width="2%">
							<div>
								<?php echo HTMLHelper::_('select.genericlist', $this->attributeSetsList, "cat[".$category->value."][]", $disabled, "id", "global_attribute_set_name", $attribute_id);?>
							</div>
							<div>
								<?php
								if ($count > 0)
								{
									echo sprintf(Text::_('COM_QUICK2CART_MAPPING_DISABLED'),$category->text);
								}
								?>
							</div>
						</td>
					</tr>
				<?php
				}?>
			</tbody>
		</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="attributesetmapping" />
</form>
