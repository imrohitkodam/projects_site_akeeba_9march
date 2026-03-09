<?php
/**
 * @package     Quick2cart
 * @subpackage  com_quick2cart
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2021 Techjoomla. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Uri\Uri;

$lang = Factory::getLanguage();
$lang->load('plg_discounts_share_for_discounts', JPATH_ADMINISTRATOR);

/**
 * Plg_share_for_discounts
 *
 * @package     Plgshare_For_Discounts
 * @subpackage  site
 * @since       1.0
 */
class PlgDiscountsShare_For_Discounts extends CMSPlugin
{
	/**
	 * Method to get facebook sdk js
	 *
	 * @return  null
	 *
	 * @since   1.0
	 */
	public function get_facebook_sdk_js()
	{
		ob_start();
		?>
		<script>
		<?php
		if ($this->params->get('load_fb_sdk_js') == 1)
		{
		?>
			(function(d, s, id){
				var js, fjs = d.getElementsByTagName(s)[0];
				if (d.getElementById(id)) {return;}
				js = d.createElement(s); js.id = id;
				js.src = "//connect.facebook.net/en_US/sdk.js";
				fjs.parentNode.insertBefore(js, fjs);
			}(document, 'script', 'facebook-jssdk'));
		<?php
		}
		?>

		window.fbAsyncInit = function()
			{
				FB.init({
					appId      : '<?php echo $this->params->get('facebook_app_id');?>',
					xfbml      : true,
					version    : 'v2.3'
				});
			};
		function sendFBRequest(productUrl)
		{
			FB.login(function(response) {
			if (response.authResponse) {
			FB.api('/me', function(response) {
					FB.ui({
					method: 'share',
					app_id:'<?php echo $this->params->get('facebook_app_id');?>',
					href: productUrl,
				}, function(response){

					if (response.post_id)
					{
						techjoomla.jQuery('#tj-sd-coupon').text('<?php echo $this->params->get('coupon_code')?>');
						techjoomla.jQuery("#tj-sd-coupon").removeClass("code-blur-wrapper");
					}
					else
					{
						alert("<?php echo Text::_("PLG_SHARE_FOR_DISCOUNTS_ERROR_MSG");?>");
					}
					});
				});
				}
				else
				{
					alert("<?php echo Text::_("PLG_SHARE_FOR_DISCOUNTS_AUTHENTICATION_ERROR_MSG");?>");
				}
			},{scope: 'publish_actions',return_scopes: true});
		}
		</script>
		<?php
		$script = ob_get_contents();
		ob_end_clean();

		return $script;
	}

	/**
	 * [Method to get CSS and HTML for plugin]
	 *
	 * @param   [type]  $productUrl  [description]
	 *
	 * @return  [type]               [description]
	 */
	public function onGetDiscountHtml($productUrl)
	{
		HTMLHelper::_('stylesheet','plugins/discounts/share_for_discounts/assets/css/sharefordiscounts.css');
		HTMLHelper::_('script', 'components/com_quick2cart/assets/js/bootstrap-tooltip.js');
		HTMLHelper::_('script', 'components/com_quick2cart/assets/js/bootstrap-popover.js');

		$content = '<div><div class="center discount-content-wrapper">'
					. Text::_($this->params->get('box_text')) . '</div>'
					. '<br><br><div class="center"><a class="btn btn-info" onclick=sendFBRequest("' . $productUrl . '")>FB Share</a></div><hr>'
					. '<div class="discount-text-wrapper">'
					. '<div class="code-coupon-wrapper code-blur-wrapper" id="tj-sd-coupon">' . Text::_("PLG_SHARE_FOR_DISCOUNTS_SHARE_MSG") . '</div>'
					. '</div></div>';

		echo $this->get_facebook_sdk_js();
		ob_start();
		?>
		<script>
			techjoomla.jQuery(function () {
					jQuery('#share_discount_bt').popover({
					placement : 'top',
					html : true,
					title : '<div class="discount-box-title-wrapper"><?php echo Text::_($this->params->get('box_title'));?></div>',
					content : '<?php echo $content;?>'
				});
			});
					jQuery('html').on('click', function(e) {
						if (jQuery('.popover').hasClass('in') && typeof jQuery(e.target).data('original-title') == 'undefined' &&
						 !jQuery(e.target).parents().is('.popover.in')){
					 jQuery('[data-original-title]').popover('hide');
				}
			});
		</script>
		<?php
		$script = ob_get_contents();
		ob_end_clean();

		echo $script;

		$buttonText = Text::_($this->params->get('button_text'));
		$buttonHtml = '<div class="' . $this->params->get('parent_css_class')
		. '"><button id="share_discount_bt" rel="popover"  class="btn '
		. $this->params->get('button_css_class')
		. '" type="button">' . Text::_($buttonText)
		. '</button></div>';

		return $buttonHtml;
	}
}
