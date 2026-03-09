<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die;
use Joomla\CMS\Uri\Uri;
?>

<div class="q2c-wrapper">

	<div id="q2c-ajax-call-fade-content" ></div>
	<div id="q2c-ajax-call-loader-modal">
		<img id="q2c-ajax-loader" src="<?php echo Uri::root() . 'components/com_quick2cart/assets/images/ajax.gif';?>" />
	</div>
<?php
echo $data->amazonCheckoutButtonHTML;
?>
<script type="text/javascript">

	function openModal(fadeDiv, loaderDiv)
	{
		document.getElementById(fadeDiv).style.display = 'block';
		document.getElementById(loaderDiv).style.display = 'block';
	}

	function closeModal(fadeDiv, loaderDiv) {
		document.getElementById(fadeDiv).style.display = 'none';
		document.getElementById(loaderDiv).style.display = 'none';
	}

	openModal("q2c-ajax-call-fade-content", "q2c-ajax-call-loader-modal");
	qtc_isLoadedAmazonCheckoutBtb()

	function qtc_isLoadedAmazonCheckoutBtb()
	{
		if (techjoomla.jQuery("#CBAWidgets0").length )
		{
			techjoomla.jQuery('#CBAWidgets0').click();
		}
		else
		{
			setTimeout(qtc_isLoadedAmazonCheckoutBtb, 10);
		}
	}
</script>
</div>
