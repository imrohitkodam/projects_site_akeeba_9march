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
defined('_JEXEC') or die();

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;

$document = Factory::getDocument();
$core_js  = Uri::root() . 'media/system/js/core.js';
$flg      = 0;

foreach ($document->_scripts as $name => $ar)
{
	if ($name == $core_js )
	{
		$flg = 1;
	}
}

if ($flg == 0)
{
	echo "<script type='text/javascript' src='".$core_js."'></script>";
}

if (!empty($this->orderinfo[0]->id))
{
	$params                    = ComponentHelper::getParams('com_quick2cart');
	$this->comquick2cartHelper = new comquick2cartHelper;
	$this->productHelper       = new productHelper();
	$user                      = Factory::getUser();
	$jinput                    = Factory::getApplication()->input;
	$guest_email               = $jinput->get('email', '', 'STRING');
	$coupon_code               = $this->orderinfo[0]->coupon_code;

	// Get billing and shipping info
	if (!empty($this->orderinfo[0]->address_type) && $this->orderinfo[0]->address_type == 'BT')
	{
		$billinfo = $this->orderinfo[0];
	}
	elseif (!empty($this->orderinfo[1]->address_type) && $this->orderinfo[1]->address_type == 'BT')
	{
		$billinfo = $this->orderinfo[1];
	}

	if ($params->get('shipping') == '1')
	{
		if (!empty($this->orderinfo[0]->address_type) && $this->orderinfo[0]->address_type == 'ST')
		{
			$shipinfo = $this->orderinfo[0];
		}
		elseif (isset($this->orderinfo[1]))
		{
			if (!empty($this->orderinfo[1]->address_type) && $this->orderinfo[1]->address_type == 'ST')
			{
				$shipinfo = $this->orderinfo[1];
			}
		}
	}

	$this->orderinfo    = $this->orderinfo[0];
	$orders_site        = (isset($this->orders_site)) ? $this->orders_site : 0;
	$orders_email       = (isset($this->orders_email)) ? $this->orders_email : 0;
	$emailstyle         = "style='background-color: #cccccc'";
	$vendor_order_view  = (!empty($this->store_id)) ? 1 : 0;
	$order_currency_sym = $order_currency = $this->orderinfo->currency;

	if ($order_currency)
	{
		$order_currency_sym = $this->comquick2cartHelper->getCurrencySymbol($order_currency);
	}

	if (isset($this->order_blocks))
	{
		$order_blocks = $this->order_blocks;
	}
	else
	{
		$order_blocks = array(
			'0' => 'shipping',
			'1' => 'billing',
			'2' => 'cart',
			'3' => 'order',
			'4' => 'order_status'
		);
	}
	
	HTMLHelper::_('script', 'components/com_quick2cart/assets/js/bootstrap-tooltip.js');
	HTMLHelper::_('script', 'components/com_quick2cart/assets/js/bootstrap-popover.js');
	?>
	<script type="text/javascript">
		function qtc_showpaymentgetways()
		{
			document.getElementById("qtc_paymentmethods").style.display='block';
		}
	</script>

	<?php
	$this->wrapperDivStyle      =  '';
	$this->emailTable           = "width:100%;";
	$this->email_table_bordered = '';
	?>

	<div style="clear: both;"></div>
	<div class="<?php echo Q2C_WRAPPER_CLASS; ?> container-fluid" id="qtcMainOrderWrapper">
		<?php
		if (in_array('order', $order_blocks))
		{ ?>
			<div class="row-fluid">
				<?php
					$view = $this->comquick2cartHelper->getViewpath('orders', 'default_basicdetails_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
					ob_start();
					include($view);
					$html = ob_get_contents();
					ob_end_clean();
				echo $html;
				?>
			</div>
		<?php
		}

		PluginHelper::importPlugin('system');
		$result              = Factory::getApplication()->triggerEvent('onAddHtmlOrderDetailPage', array(
			$this->orderinfo->order_id,
			$this->orderinfo,
			$this->orderitems
		));

		// Call the plugin and get the result
		$orderDetailPageHtml = (!empty($result)) ? $result[0] : '';
		$addTabPlace         = !empty($result[0]['tabPlace']) ? $result[0]['tabPlace'] : '';

		// TRIGGER HTML addHtmlOnOrderDetailPage
		if (!empty($orderDetailPageHtml['html']))
		{
			echo $orderDetailPageHtml['html'];
		}
		// END - Q2C Sample development -
		?>
		<!-- Display cart detail -->
		<div class="row-fluid">
			<?php
			$view = $this->comquick2cartHelper->getViewpath('orders', 'default_cartdetail_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;	?>
		</div>
		<div class="row-fluid no-print">
			<?php
			$view = $this->comquick2cartHelper->getViewpath('orders', 'statushistrory_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;	?>
		</div>
		<!-- Display billing and shipping info -->
		<div class="row-fluid">
			<?php
			$view = $this->comquick2cartHelper->getViewpath('orders', 'default_billing_bs2', "ADMINISTRATOR", "ADMINISTRATOR");
			ob_start();
			include($view);
			$html = ob_get_contents();
			ob_end_clean();
			echo $html;	?>
		</div>
		<?php
		if ($orders_email && $this->orderinfo->status == 'P' && !$user->id && $params->get('guest'))
		{
			$Itemid = $this->comquick2cartHelper->getitemid('index.php?option=com_quick2cart&view=orders');?>
			<div>
				<a href="<?php echo Uri::base() . substr(Route::_('index.php?option=com_quick2cart&view=orders&layout=order&email=' . $guest_email . '&orderid=' . $this->orderinfo->id . '&paybuttonstatus=1' . '&Itemid=' . $Itemid), strlen(Uri::base(true)) + 1);?>">
					<?php echo Text::_('QTC_ORDER_PROCES_GUEST_LINK');?>
				</a>
			</div>
			<?php
		}?>
	</div> <!-- End of wrapper class-->
	<?php
}
else
{
	?>
	<div class="alert alert-error">
		<?php echo Text::_('COM_QUICK2CART_ERROR_ORDER_NOT_FOUND'); ?>
	</div>
	<?php
}
?>
<script>
	Joomla.submitbutton = function(action)
	{
		if(action === 'printOrder')
		{
			var restorepage = document.body.innerHTML;
			var printcontent = document.getElementById('qtcMainOrderWrapper').innerHTML;
			document.body.innerHTML = printcontent;
			window.print();
			document.body.innerHTML = restorepage;
			return false;
		}

		Joomla.submitform(action )

	}

	jQuery(document).ready(function () {
		if (jQuery('#system-message-container').length)
		{
			jQuery('#system-message-container.j-toggle-main') . addClass('span12');
			jQuery('#system-message-container.j-toggle-main') . removeClass('span10');
		}
	});
</script>
