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
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.formvalidator');
HTMLHelper::_('bootstrap.renderModal', 'a.modal');

/*list of attributes of item*/
$params       = ComponentHelper::getParams('com_quick2cart');
$qtc_base_url = Uri::base();
$lang         = Factory::getLanguage();
$lang->load('com_quick2cart', JPATH_SITE);

// declaration section
$quick2cartModelAttributes = new quick2cartModelAttributes();
$path                      = JPATH_SITE.'/components/com_quick2cart/helpers/product.php';
$addMediaLink              = $qtc_base_url.'index.php?option=com_quick2cart&view=attributes&layout=extrafield_bs3&tmpl=component&item_id=' . $item_id . "&content_id=" . $item_id;
$fparam                    = "'" . (!empty($item_id) ? $item_id :0 ) . "'";

if (!class_exists('Quick2cartModelProductpage'))
{
	JLoader::register('Quick2cartModelProductpage', JPATH_SITE . '/components/com_quick2cart/models/productpage.php');
	JLoader::load('Quick2cartModelProductpage');
}

$quick2cartModelProductpage = new Quick2cartModelProductpage;
$extraData                  = $quick2cartModelProductpage->getDataExtra($fparam);
?>
<script>
function toggleAddExtraFieldModal()
{
	jQuery('#addExtraFieldModal').attr('data-width' , (window.innerWidth)/2);
	jQuery('#addExtraFieldModal').attr('data-height' , window.innerHeight);
	jQuery('#addExtraFieldModal').modal('show');
	jQuery('#addExtraFieldModal').attr('style' , 'display:block !important');
}
</script>
<?php
if(isset($extraData) && count($extraData))
{?>
	<table class="table table-striped table-bordered table-hover">
		<?php
		foreach($extraData as $f)
		{?>
			<tr>
				<td><strong><?php echo $f->label;?></strong></td>
				<td>
					<?php
					if (!is_array($f->value))
					{
						echo $f->value;
					}
					else
					{
						foreach($f->value as $option)
						{
							echo $option->options; ?>
							<br/>
							<?php 
						}
					} ?>
				</td>
			</tr>
		<?php 
		} ?>
	</table>
<?php
}?>

<button class="btn btn-sm btn-primary" type="button" data-toggle="modal" data-target="#addExtraFieldModal">
	<?php echo Text::_('QTC_FIELD_ADD_EXTRA_FIELD'); ?>
</button>
<?php
	echo HTMLHelper::_(
		'bootstrap.renderModal',
		'addExtraFieldModal',
		array(
			'title'      => Text::_('QTC_FIELD_ADD_EXTRA_FIELD'),
			'url'        => $addMediaLink,
			'modalWidth' => '180',
			'bodyHeight' => '70',
			'width'      => '800px',
			'height'     => '800px',
			'footer' => '<button type="button" class="btn btn-secondary" data-dismiss="modal">'. Text::_('COM_QUICK2CART_COMMON_CLOSE') .'</button>',
		)
	)
?>
