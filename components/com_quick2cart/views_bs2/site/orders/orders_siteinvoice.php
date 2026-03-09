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
<div class="row-fluid " >

	<div style="float:left;">
		<?php
		if (!empty($this->siteInvoiceInfo['companyName']) )
		{ ?>
			<h2><?php echo $this->siteInvoiceInfo['companyName'] . '<br/>'; ?></h2>
		<?php
		}
		?>

	</div>
	<div style="float:right;">
			<b><i><?php echo Text::_('QTC_INVOICE_CONT_INFO'); ?></i></b> <br/>
		<?php

		if (!empty($this->siteInvoiceInfo['address']) )
		{ ?>
			<b><?php echo Text::_('QTC_INVOICE_ADDR');?></b> :
			<?php echo nl2br($this->siteInvoiceInfo['address']) . '<br/>';
		}

		if (!empty($this->siteInvoiceInfo['contactNumber']) )
		{ ?>
			<b><?php echo Text::_('COM_QUICK2CART_INVOICE_SITE_CONTACT_NO');?></b> :
			<?php echo $this->siteInvoiceInfo['contactNumber'] . '<br/>';
		}

		if (!empty($this->siteInvoiceInfo['fax']) )
		{ ?>
			<b><?php echo Text::_('COM_QUICK2CART_INVOICE_SITE_FAX');?></b> :
			<?php echo $this->siteInvoiceInfo['fax'] . '<br/>';
		}

		if (!empty($this->siteInvoiceInfo['email']) )
		{ ?>
			<b><?php echo Text::_('QTC_INVOICE_EMAIL');?></b> :
			<?php echo $this->siteInvoiceInfo['email'] . '<br/>';
		}

		if (!empty($this->siteInvoiceInfo['vat_num']))
		{
		?>
			<b><?php echo Text::_('QTC_INVOICE_VAT');?></b> :
			<?php echo $this->siteInvoiceInfo['vat_num'] . '<br/>';
		}
		?>
	</div>

</div>
<div style="clear:both;"></div>
