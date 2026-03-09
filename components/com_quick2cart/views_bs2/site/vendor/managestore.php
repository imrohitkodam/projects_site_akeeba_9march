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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('bootstrap.tooltip');

// 1.check user is logged or not
$app      = Factory::getApplication();
$input    = $app->input;
$store_id = $input->get( 'store_id','0' );
$user     = Factory::getUser();

if (!$user->id)
{
	$return = base64_encode(Uri::getInstance());
	$login_url_with_return = Route::_('index.php?option=com_users&view=login&return=' . $return);
	$app->enqueueMessage(Text::_('QTC_LOGIN'), 'notice');
	$app->redirect($login_url_with_return, 403);
}

$document = Factory::getDocument();
?>
<script type="text/javascript">
function submitAction(action)
{
		var form = document.adminForm;
		console.log(action);
		if(action=='publish' || action=='unpublish' || action=='delete' || action=="edit")
		{
				if (document.adminForm.boxchecked.value==0)
				{
					alert("<?php echo $this->escape(Text::_('QTC_MAKE_SEL')); ?>");
					return;
				}
				switch(action)
				{
					case 'publish': form.task.value='publish';
					break

					case 'unpublish': form.task.value='unpublish';
					break

					case 'delete':
						var r=confirm("<?php echo Text::_('QTC_DELETE_CONFIRM_VENDER');?>");
						if (r==true)
						{
							var aa;
							form.task.value='delete';
						}
						else
						{
							return false;
						}

					break
					case "edit":

						if(document.adminForm.boxchecked.value > 1)
						{
							alert("<?php echo Text::_('QTC_MAKE_ONE_SEL');?>");
							return;
						}
						form.task.value='edit';
					break;


				}	//switch end
			//Joomla.submitform(action);
		}
		else if(action=="addNew")
		{
			form.task.value='addNew';
		}
		else
		{
			window.location = 'index.php?option=com_quick2cart&view=vendor';
		}
form.submit();
	return;

 }
</script>

<div class="<?php echo Q2C_WRAPPER_CLASS; ?>">
<form  method="post" name="adminForm" id="adminForm" class="form-validate">
	<legend><?php echo sprintf(Text::_('QTC_MANAGE_SPECIFIC_STORE'),"");?></legend>
	<table class="  table table-striped table-condensed">
		<thead>
		<tr>
			<th colspan="7">
			<div style="float:right;" >

			<button type="button" class="btn btn-info  btn_margin" onclick="window.open('<?php echo Route::_('index.php?option=com_zoo&view=submission&layout=submission&Itemid=1919&store_id='.$store_id	);?>','_self')" > <i class="<?php echo QTC_ICON_PLUS; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i> <?php echo Text::_( 'QTC_MANAGE_STORE_ADD_PROD' ); ?></button>
			<button type="button" class="btn btn-info  btn_margin" onclick="window.open('<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=storeorder');?>','_self')" > <i class="<?php echo QTC_ICON_CART;?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i> <?php echo Text::_( 'QTC_MANAGE_STORE_ORDERS' ); ?></button>
			<button type="button" class="btn btn-info  btn_margin" onclick="window.open('<?php echo Route::_('index.php?option=com_quick2cart&view=orders&layout=mycustomer');?>','_self')" > <i class="icon-user <?php echo Q2C_ICON_WHITECOLOR; ?>"></i> <?php echo Text::_( 'QTC_MANAGE_STORE_CUSTOMER' ); ?></button>
			<button type="button" class="btn btn-info  btn_margin" onclick="window.open('<?php echo Route::_('index.php?option=com_quick2cart&view=managecoupon&layout=default');?>','_self')" > <i class="<?php echo Q2C_ICON_ARROW_CHEVRON_RIGH; ?> <?php echo Q2C_ICON_WHITECOLOR; ?>"></i> <?php echo Text::_( 'QTC_MANAGE_STORE_COUPON' ); ?></button>

			</div>
			</th>
		</tr>
		</thead>

	</table>
	<!-- Providing store info -->
	<div class="row-fluid" > <!-- store info div starts -->
	<?php
		if(!empty($this->storeDetailInfo))
		{
			$sinfo=$this->storeDetailInfo;
			$comquick2cartHelper = new comquick2cartHelper;
			echo $comquick2cartHelper->getStoreDetailHTML($sinfo);
	 ?>

		<?php }
		//print"<pre>";print_r($this->storeDetailInfo);
		?>

	</div> <!-- store info div END -->

	<input type="hidden" name="option" value="com_quick2cart" />
	<input type="hidden" name="view" value="vendor" />
	<input type="hidden" name="task" value="" />
	<?php if(!empty($this->site))
	{  // called from site
	?>
	<!-- 	<input type="hidden" name="layout" value="mystores" /> -->
	<?php
	}?>
	<?php if(empty($this->site))  // called from admin
	{
	?>
	<!-- 	<input type="hidden" name="controller" value="vendor" /> -->
	<?php
	}?>

	<input type="hidden" name="boxchecked" value="0" />

	<?php echo HTMLHelper::_( 'form.token' ); ?>
</form>
</div>

