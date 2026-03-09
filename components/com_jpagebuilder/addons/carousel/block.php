<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;

/**
 * Carousel addon class
 *
 * @since 1.0.0
 */
class JpagebuilderAddonCarousel extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->addon->settings;
		$class = (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		// Addons option
		$autoplay = (isset ( $settings->autoplay ) && $settings->autoplay) ? 1 : 0;
		$controllers = (isset ( $settings->controllers ) && $settings->controllers) ? $settings->controllers : 0;
		$arrows = (isset ( $settings->arrows ) && $settings->arrows) ? $settings->arrows : 0;
		$interval = (isset ( $settings->interval ) && $settings->interval) ? (( int ) $settings->interval * 1000) : 5000;
		$carousel_autoplay = ($autoplay) ? ' data-jpb-ride="jpb-carousel"' : '';
		if ($autoplay == 0) {
			$interval = 'false';
		}
		$output = '<div id="jpb-carousel-' . $this->addon->id . '" data-interval="' . $interval . '" class="jpb-carousel jpb-slide' . $class . '"' . $carousel_autoplay . '>';

		if ($controllers) {
			$output .= '<ol class="jpb-carousel-indicators">';
			foreach ( $settings->jp_carousel_item as $key1 => $value ) {
				$output .= '<li data-jpb-target="#jpb-carousel-' . $this->addon->id . '" ' . (($key1 == 0) ? ' class="active"' : '') . '  data-jpb-slide-to="' . $key1 . '"></li>' . "\n";
			}
			$output .= '</ol>';
		}

		$output .= '<div class="jpb-carousel-inner">';

		if (isset ( $settings->jp_carousel_item ) && count ( ( array ) $settings->jp_carousel_item )) {
			foreach ( $settings->jp_carousel_item as $key => $value ) {
				list ( $button_url, $button_target ) = JpagebuilderAddonHelper::parseLink ( $value, 'button_url', [ 
						'url' => 'button_url',
						'new_tab' => 'button_target'
				] );

				$bg_image = (isset ( $value->bg ) && $value->bg) ? $value->bg : '';
				$bg_image_src = isset ( $bg_image->src ) ? $bg_image->src : $bg_image;
				$alt_text_fallback = isset ( $value->title ) ? $value->title : '';
				$alt_text = isset ( $bg_image->alt ) ? $bg_image->alt : $alt_text_fallback;

				$output .= '<div class="jpb-item jpb-item-' . $this->addon->id . $key . ' ' . ($bg_image_src ? ' jpb-item-has-bg' : '') . (($key == 0) ? ' active' : '') . '">';

				$output .= $bg_image_src ? '<img src="' . $bg_image_src . '" alt="' . $alt_text . '">' : '';

				$output .= '<div class="jpb-carousel-item-inner">';
				$output .= '<div class="jpb-carousel-caption">';
				$output .= '<div class="jpb-carousel-text">';

				if ((isset ( $value->title ) && $value->title) || (isset ( $value->content ) && $value->content)) {
					$output .= (isset ( $value->title ) && $value->title) ? '<h2 id="addon-title-' . $this->addon->id . "-" . $key . '">' . $value->title . '</h2>' : '';
					$output .= (isset ( $value->content ) && $value->content) ? '<div id="addon-content-' . $this->addon->id . "-" . $key . '" class="jpb-carousel-content">' . $value->content . '</div>' : '';

					if (isset ( $value->button_text ) && $value->button_text) {
						$button_class = (isset ( $settings->button_type ) && $settings->button_type) ? ' jpb-btn-' . $settings->button_type : ' jpb-btn-default';
						$button_class .= (isset ( $settings->button_size ) && $settings->button_size) ? ' jpb-btn-' . $settings->button_size : '';
						$button_class .= (isset ( $settings->button_shape ) && $settings->button_shape) ? ' jpb-btn-' . $settings->button_shape : ' jpb-btn-rounded';
						$button_class .= (isset ( $settings->button_appearance ) && $settings->button_appearance) ? ' jpb-btn-' . $settings->button_appearance : '';
						$button_class .= (isset ( $settings->button_block ) && $settings->button_block) ? ' ' . $settings->button_block : '';
						$button_icon = (isset ( $value->button_icon ) && $value->button_icon) ? $value->button_icon : '';
						$button_icon_position = (isset ( $value->button_icon_position ) && $value->button_icon_position) ? $value->button_icon_position : 'left';

						$icon_arr = array_filter ( explode ( ' ', $button_icon ) );

						if (count ( $icon_arr ) === 1) {
							$button_icon = 'fa ' . $button_icon;
						}

						if ($button_icon_position == 'left') {
							$value->button_text = ($button_icon) ? '<i aria-hidden="true" class="' . $button_icon . '" aria-hidden="true"></i> ' . $value->button_text : $value->button_text;
						} else {
							$value->button_text = ($button_icon) ? $value->button_text . ' <i aria-hidden="true" class="' . $button_icon . '" aria-hidden="true"></i>' : $value->button_text;
						}

						$href = ! empty ( $button_url ) ? 'href="' . $button_url . '" ' : '';

						$output .= '<a ' . $href . ' ' . $button_target . ' id="btn-' . ($this->addon->id . $key) . '" class="jpb-btn' . $button_class . '">' . $value->button_text . '</a>';
					}
				}

				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
			}
		}

		$output .= '</div>';

		if ($arrows) {
			$output .= '<a href="#jpb-carousel-' . $this->addon->id . '" class="jpb-carousel-arrow left jpb-carousel-control" data-slide="prev" aria-label="' . Text::_ ( 'COM_JPAGEBUILDER_ARIA_PREVIOUS' ) . '"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>';
			$output .= '<a href="#jpb-carousel-' . $this->addon->id . '" class="jpb-carousel-arrow right jpb-carousel-control" data-slide="next" aria-label="' . Text::_ ( 'COM_JPAGEBUILDER_ARIA_NEXT' ) . '"><i class="fa fa-chevron-right" aria-hidden="true"></i></a>';
		}

		$output .= '</div>';

		return $output;
	}

	/**
	 * Generate the CSS string for the frontend page.
	 *
	 * @return string The CSS string for the page.
	 * @since 1.0.0
	 */
	public function css() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$layout_path = JPATH_ROOT . '/components/com_jpagebuilder/layouts';

		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );

		$settings = $this->addon->settings;

		$settings->alignment = JpagebuilderCSSHelper::parseAlignment ( $settings, 'alignment' );

		$css = '';

		// Buttons style
		foreach ( $this->addon->settings->jp_carousel_item as $key => $value ) {
			$options = new stdClass ();
			$options->button_type = (isset ( $settings->button_type ) && $settings->button_type) ? $settings->button_type : '';
			$options->button_appearance = (isset ( $settings->button_appearance ) && $settings->button_appearance) ? $settings->button_appearance : '';
			$options->button_color = (isset ( $settings->button_color ) && $settings->button_color) ? $settings->button_color : '';
			$options->button_color_hover = (isset ( $settings->button_color_hover ) && $settings->button_color_hover) ? $settings->button_color_hover : '';
			$options->button_background_color = (isset ( $settings->button_background_color ) && $settings->button_background_color) ? $settings->button_background_color : '';
			$options->button_background_color_hover = (isset ( $settings->button_background_color_hover ) && $settings->button_background_color_hover) ? $settings->button_background_color_hover : '';
			$options->button_padding = null;

			if (isset ( $settings->button_padding_original )) {
				$options->button_padding = $settings->button_padding_original;
			} elseif (isset ( $settings->button_padding )) {
				$options->button_padding = $settings->button_padding;
			}

			$options->button_size = ! empty ( $settings->button_size ) ? $settings->button_size : null;
			$options->button_typography = ! empty ( $settings->button_typography ) ? $settings->button_typography : null;

			$css_path = new FileLayout ( 'addon.css.button', $layout_path );
			$css .= $css_path->render ( array (
					'addon_id' => $addon_id,
					'options' => $options,
					'id' => 'btn-' . ($this->addon->id . $key)
			) );

			// Title Margin
			$title_id_selector = '#addon-title-' . $this->addon->id . '-' . $key;
			$itemTitleMarginStyle = $cssHelper->generateStyle ( $title_id_selector, $value, [ 
					'title_margin' => 'margin',
					'title_padding' => 'padding'
			], [ 
					'title_margin' => false,
					'title_padding' => false
			], [ 
					'title_margin' => 'spacing',
					'title_padding' => 'spacing'
			] );
			$css .= $itemTitleMarginStyle;

			// Content Margin
			$content_id_selector = '#addon-content-' . $this->addon->id . '-' . $key;
			$itemContentMarginStyle = $cssHelper->generateStyle ( $content_id_selector, $value, [ 
					'content_margin' => 'margin',
					'content_padding' => 'padding'
			], [ 
					'content_margin' => false,
					'content_padding' => 'padding'
			], [ 
					'content_margin' => 'spacing',
					'content_padding' => 'spacing'
			] );
			$css .= $itemContentMarginStyle;
		}

		$speed = $cssHelper->generateStyle ( '.jpb-carousel-inner > .jpb-item', $settings, [ 
				'speed' => '-webkit-transition-duration',
				'speed' => 'transition-duration'
		], 'ms' );

		$css .= $speed;

		// Title Color
		$itemAlignment = $cssHelper->generateStyle ( '.jpb-carousel-text', $settings, [ 
				'alignment' => 'text-align'
		], [ 
				'alignment' => false
		] );
		$css .= $itemAlignment;

		// Title
		$titleFontStyle = $cssHelper->typography ( '.jpb-carousel-text h2', $settings, 'item_title_typography' );
		$css .= $titleFontStyle;

		// Title Color
		$itemTitleColorStyle = $cssHelper->generateStyle ( '.jpb-carousel-text h2', $settings, [ 
				'item_title_color' => 'color'
		], [ 
				'item_title_color' => false
		] );
		$css .= $itemTitleColorStyle;

		// Content
		$contentTypography = $cssHelper->typography ( '.jpb-carousel-text .jpb-carousel-content', $settings, 'item_content_typography' );
		$css .= $contentTypography;

		// Content Color
		$itemContentColorStyle = $cssHelper->generateStyle ( '.jpb-carousel-text .jpb-carousel-content', $settings, [ 
				'item_content_color' => 'color'
		], [ 
				'item_content_color' => false
		] );
		$css .= $itemContentColorStyle;

		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-carousel', $settings, 'transform' );
		$css .= $transformCss;

		return $css;
	}

	/**
	 * Generate the lodash template string for the frontend editor.
	 *
	 * @return string The lodash template string.
	 * @since 1.0.0
	 */
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( '#jpb-addon-{{ data.id }}' );
		$output = '
		<#
		let interval = data.interval ? parseInt(data.interval) * 1000 : 5000;
		if (data.autoplay==0)
		{
			interval = "false";
		}
		let autoplay = data.autoplay ? \'data-jpb-ride="jpb-carousel"\' : "";
		#>
		<style type="text/css">';
		// Alignment
		$output .= $lodash->alignment ( 'text-align', '.jpb-carousel-caption .jpb-carousel-text', 'data.alignment' );

		$output .= '
			#jpb-addon-{{ data.id }} .jpb-carousel-inner > .jpb-item{
				-webkit-transition-duration: {{ data.speed }}ms;
				transition-duration: {{ data.speed }}ms;
			}
			<# _.each(data.jp_carousel_item, function (carousel_item, key){ #>';
		// Custom
		$output .= '<# if (data.button_type == "custom") { #>';
		$output .= '<# if (data.button_appearance == "outline") { #>';
		$output .= $lodash->border ( 'border-color', '.jpb-btn-custom', 'data.button_background_color' );
		$output .= $lodash->border ( 'border-color', '.jpb-btn-custom:hover', 'data.button_background_color_hover' );
		$output .= '<# } else if (data.button_appearance == "gradient") { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-btn-custom { border: none; }';
		$output .= $lodash->color ( 'background-color', '.jpb-btn-custom', 'data.button_background_gradient' );
		$output .= $lodash->color ( 'background-color', '.jpb-btn-custom:hover', 'data.button_background_gradient_hover' );
		$output .= '<# } else { #>';
		$output .= $lodash->color ( 'background-color', '.jpb-btn-custom', 'data.button_background_color' );
		$output .= '<# } #>';

		$output .= $lodash->color ( 'color', '.jpb-btn-custom', 'data.button_color' );
		$output .= $lodash->color ( 'color', '.jpb-btn-custom:hover', 'data.button_color_hover' );
		$output .= $lodash->color ( 'background-color', '.jpb-btn-custom:hover', 'data.button_background_color_hover' );
		$output .= '<# } #>';

		// Typography
		$output .= $lodash->typography ( '.jpb-btn-{{ data.button_type }}', 'data.button_typography' );

		$output .= $lodash->typography ( '.jpb-carousel-caption h2', 'data.item_title_typography' );
		$output .= $lodash->typography ( '.jpb-carousel-caption .jpb-carousel-content', 'data.item_content_typography' );

		// Color
		$output .= $lodash->color ( 'color', '.jpb-carousel-caption h2', 'data.item_title_color ' );
		$output .= $lodash->color ( 'color', '.jpb-carousel-caption .jpb-carousel-content', 'data.item_content_color' );

		// Spacing
		$output .= $lodash->spacing ( 'margin', '#addon-title-{{data.id}}-{{key}}', 'carousel_item.title_margin' );
		$output .= $lodash->spacing ( 'margin', '#addon-content-{{data.id}}-{{key}}', 'carousel_item.content_margin' );
		$output .= $lodash->spacing ( 'padding', '#addon-title-{{data.id}}-{{key}}', 'carousel_item.title_padding' );
		$output .= $lodash->spacing ( 'padding', '#addon-content-{{data.id}}-{{key}}', 'carousel_item.content_padding' );
		$output .= '<# if (data.button_size == "custom") { #>';
		$output .= $lodash->spacing ( 'padding', '.jpb-btn-{{ data.button_type }}', 'data.button_padding' );
		$output .= '<# } #>';
		$output .= $lodash->generateTransformCss ( '.jpb-carousel', 'data.transform' );

		$output .= '		
			<# }); #>
		</style>
		<div class="jpb-carousel jpb-slide {{ data.class }}" id="jpb-carousel-{{ data.id }}" data-interval="{{ interval }}" {{{ autoplay }}}>
			<# if(data.controllers){ #>
				<ol class="jpb-carousel-indicators">
				<# _.each(data.jp_carousel_item, function (carousel_item, key){ #>
					<# var active = (key == 0) ? "active" : ""; #>
					<li data-jpb-target="#jpb-carousel-{{ data.id }}"  class="{{ active }}"  data-jpb-slide-to="{{ key }}"></li>
				<# }); #>
				</ol>
			<# } #>
			<div class="jpb-carousel-inner">
				<#
				_.each(data.jp_carousel_item, function (carousel_item, key){
					var carouselBg = {}
					if (typeof carousel_item.bg !== "undefined" && typeof carousel_item.bg.src !== "undefined") {
						carouselBg = carousel_item.bg
					} else {
						carouselBg = {src: carousel_item.bg}
					}
					var classNames = (key == 0) ? "active" : "";
					classNames += carouselBg.src ? " jpb-item-has-bg" : "";
					classNames += " jpb-item-"+data.id+""+key;

					let alt_text = carousel_item?.bg?.alt ?? carousel_item?.title;
				#>
					<div class="jpb-item {{ classNames }}">
						<# if(carouselBg.src && carouselBg.src.indexOf("http://") == -1 && carouselBg.src.indexOf("https://") == -1){ #>
							<img src=\'{{ pagebuilder_base + carouselBg.src }}\' alt="{{ alt_text }}">
						<# } else if(carouselBg.src){ #>
							<img src=\'{{ carouselBg.src }}\' alt="{{ alt_text }}">
						<# } #>
						<div class="jpb-carousel-item-inner">
							<div class="jpb-carousel-caption">
								<div class="jpb-carousel-text">
									<# if(carousel_item.title || carousel_item.content) { #>
										<# if(carousel_item.title) { #>
											<h2 class="jp-editable-content" id="addon-title-{{data.id}}-{{key}}" data-id={{data.id}} data-fieldName="jp_carousel_item-{{key}}-title">{{ carousel_item.title }}</h2>
										<# } #>
										<div class="jpb-carousel-content jp-editable-content" id="addon-content-{{data.id}}-{{key}}" data-id={{data.id}} data-fieldName="jp_carousel_item-{{key}}-content">{{{ carousel_item.content }}}</div>
										<# if(carousel_item.button_text) { #>
											<#
												var btnClass = "";
												btnClass += data.button_type ? " jpb-btn-"+data.button_type : " jpb-btn-default" ;
												btnClass += data.button_size ? " jpb-btn-"+data.button_size : "" ;
												btnClass += data.button_shape ? " jpb-btn-"+data.button_shape : " jpb-btn-rounded" ;
												btnClass += data.button_appearance ? " jpb-btn-"+data.button_appearance : "" ;
												btnClass += data.button_block ? " "+data.button_block : "" ;
												var button_text = carousel_item.button_text;

												let icon_arr = (typeof carousel_item.button_icon !== "undefined" && carousel_item.button_icon) ? carousel_item.button_icon.split(" ") : "";
												let icon_name = icon_arr.length === 1 ? "fa "+carousel_item.button_icon : carousel_item.button_icon;

												if(carousel_item.button_icon_position == "left"){
													button_text = (carousel_item.button_icon) ? \'<i class="\'+icon_name+\'"></i> \'+carousel_item.button_text : carousel_item.button_text ;
												}else{
													button_text = (carousel_item.button_icon) ? carousel_item.button_text+\' <i class="\'+icon_name+\'"></i>\' : carousel_item.button_text ;
												}
											#>

											<#

											 const {button_url} =  carousel_item;
											 const isUrlObject = _.isObject(button_url) &&  (!!button_url.url || !!button_url.menu || !!button_url.page);
											 const isUrlString = _.isString(button_url) && button_url !== "";
											 
											 let target;
											 let href;
											 let rel;
											 let relData="";
											
											 if(isUrlObject || isUrlString){
												const urlObj = button_url?.url ? button_url : window.getSiteUrl(button_url, data.button_target);
												const {url, new_tab, nofollow, noopener, noreferrer, type} = urlObj;

										   		const buttonUrl = (type === "url" && url) || (type === "menu" && urlObj.menu) || ( (type === "page" && !!urlObj.page)  && "index.php/component/jpagebuilder/index.php?option=com_jpagebuilder&view=page&id=" + urlObj.page) || "";
												target = new_tab ? `target=_blank` : "";
												
												relData += nofollow ? "nofollow" : "";
												relData += noopener ? " noopener" : "";
												relData += noreferrer ? " noreferrer" : "";

												rel = `rel="${relData}"`;
											
												href = buttonUrl ? `href=${buttonUrl}`: "";
											 }
											 #>

											 <a {{href}} {{target}} rel="{{rel}}" id="btn-{{ data.id + "" + key}}" class="jpb-btn{{ btnClass }}">{{{ button_text }}}</a>
											
										<# } #>
									<# } #>
								</div>
							</div>
						</div>
					</div>
				<# }); #>
			</div>
			<# if(data.arrows) { #>
				<a href="#jpb-carousel-{{ data.id }}" class="jpb-carousel-arrow left jpb-carousel-control" data-slide="prev"><i class="fa fa-chevron-left"></i></a>
				<a href="#jpb-carousel-{{ data.id }}" class="jpb-carousel-arrow right jpb-carousel-control" data-slide="next"><i class="fa fa-chevron-right"></i></a>
			<# } #>
		</div>
		';

		return $output;
	}
}
