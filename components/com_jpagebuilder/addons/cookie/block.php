<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;

// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddoncookie extends JpagebuilderAddons {
	public function render() {
	}
	public function scripts() {
		$base_path = 'components/com_jpagebuilder/addons/cookie/assets/js/';
		return [
				$base_path . 'cookieconsent.min.js'
		];
	}
	public function stylesheets() {
		$base_path = 'components/com_jpagebuilder/addons/cookie/assets/css/';
		return [
				$base_path . 'cookieconsent.min.css'
		];
	}
	public function js() {
		$settings = $this->addon->settings;
		$target = (isset ( $settings->target ) && $settings->target) ? $settings->target : '';
		$message = (isset ( $settings->message ) && $settings->message) ? $settings->message : '';
		$dismiss = (isset ( $settings->dismiss ) && $settings->dismiss) ? $settings->dismiss : '';
		$link = (isset ( $settings->link ) && $settings->link) ? $settings->link : '';
		$position = (isset ( $settings->position ) && $settings->position) ? $settings->position : 'left';

		$cookie_background = (isset ( $settings->cookie_background ) && $settings->cookie_background) ? $settings->cookie_background : '';
		$cookie_button_background = (isset ( $settings->cookie_button_background ) && $settings->cookie_button_background) ? $settings->cookie_button_background : '';

		if ($position == 'left') {
			$position_class = '
			"position": "bottom-left",';
		}
		if ($position == 'right') {
			$position_class = '
			"position": "bottom-right",';
		}
		if ($position == 'top') {
			$position_class = '
			"position": "top",
  		"static": true,';
		}
		if ($position == 'bottom') {
			$position_class = '';
		}
		$url = (isset ( $settings->url ) && $settings->url) ? $settings->url : '';
		$js = 'jQuery(function($){
			window.addEventListener("load", function(){
			window.cookieconsent.initialise({
			  "palette": {
			    "popup": {
			      "background": "' . $cookie_background . '"
			    },
			    "button": {
			      "background": "' . $cookie_button_background . '"
			    }
			  },
			  ' . $position_class . '
			  "content": {
			    "message": "' . $message . '",
			    "dismiss": "' . $dismiss . '",
			    "link": "' . $link . '",
				"href": "' . $url . '",
				target: "' . $target . '",
			  }
			})});
		})';
		return $js;
	}
	/**
	 * Generate the lodash template string for the frontend editor.
	 *
	 * @return string The lodash template string.
	 * @since 1.0.0
	 */
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );
		$base = Uri::base(true);
		$output = '<style type="text/css">';
		$output .= 'div.cookiebar-block{min-height:40px;border:1px solid #CCC;display: flex;align-items: center;padding: 0 10px}img.cookiebar-icon{width:20px;margin-right:10px}';
		$output .= '</style>';
		
		$output .= '<div class="cookiebar-block">' .
				'<span data-addon-icon="true">' .
				'<img class="cookiebar-icon" src="' . $base . '/components/com_jpagebuilder/addons/cookie/assets/images/icon.svg" alt="cookiebar"></span>' .
				'Cookie Bar
					<# if ( !_.isEmpty( data.admin_label ) ) { #>
						- {{ data.admin_label }}
					<# } #>
				  </div>';
		return $output;
	}
}
