<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Layout\FileLayout;
class JpagebuilderAddonPricing extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		$settings = $this->addon->settings;
		$class = (isset ( $settings->class ) && $settings->class) ? $settings->class : '';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'div';

		// Options
		$price_position = (isset ( $settings->price_position ) && $settings->price_position) ? $settings->price_position : 'before';
		$price = (isset ( $settings->price ) && $settings->price) ? $settings->price : '';
		$price_symbol = (isset ( $settings->price_symbol ) && $settings->price_symbol) ? $settings->price_symbol : '';
		$duration = (isset ( $settings->duration ) && $settings->duration) ? $settings->duration : '';
		$pricing_content = (isset ( $settings->pricing_content ) && $settings->pricing_content) ? $settings->pricing_content : '';
		$button_text = (isset ( $settings->button_text ) && $settings->button_text) ? $settings->button_text : '';
		$button_url = (isset ( $settings->button_url ) && $settings->button_url) ? $settings->button_url : '';
		$button_classes = (isset ( $settings->button_size ) && $settings->button_size) ? ' jpb-btn-' . $settings->button_size : '';
		$button_classes .= (isset ( $settings->button_type ) && $settings->button_type) ? ' jpb-btn-' . $settings->button_type : '';
		$button_classes .= (isset ( $settings->button_shape ) && $settings->button_shape) ? ' jpb-btn-' . $settings->button_shape : ' jpb-btn-rounded';
		$button_classes .= (isset ( $settings->button_appearance ) && $settings->button_appearance) ? ' jpb-btn-' . $settings->button_appearance : '';
		$button_classes .= (isset ( $settings->button_block ) && $settings->button_block) ? ' ' . $settings->button_block : '';
		$button_icon = (isset ( $settings->button_icon ) && $settings->button_icon) ? $settings->button_icon : '';
		$button_icon_position = (isset ( $settings->button_icon_position ) && $settings->button_icon_position) ? $settings->button_icon_position : 'left';
		$button_position = (isset ( $settings->button_position ) && $settings->button_position) ? $settings->button_position : 'bottom';

		$featured = (isset ( $settings->featured ) && $settings->featured) ? $settings->featured : '';

		list ( $button_url, $button_target ) = JpagebuilderAddonHelper::parseLink ( $settings, 'button_url', [ 
				'url' => 'button_url',
				'new_tab' => 'button_target'
		] );
		$button_attribs = (isset ( $button_target ) && $button_target) ? ' rel="noopener noreferrer" ' . $button_target : '';
		$button_attribs .= (isset ( $button_url ) && $button_url) ? ' href="' . $button_url . '"' : '';

		$icon_arr = array_filter ( explode ( ' ', $button_icon ) );
		if (count ( $icon_arr ) === 1) {
			$button_icon = 'fa ' . $button_icon;
		}

		if ($button_icon_position == 'left') {
			$button_text = ($button_icon) ? '<i class="' . $button_icon . '" aria-hidden="true"></i> ' . $button_text : $button_text;
		} else {
			$button_text = ($button_icon) ? $button_text . ' <i class="' . $button_icon . '" aria-hidden="true"></i>' : $button_text;
		}

		$btnAriaLabel = ! empty ( $settings->button_aria_label ) ? ' aria-label="' . $settings->button_aria_label . '"' : '';

		$button_output = ($button_text) ? '<a' . $button_attribs . ' ' . $btnAriaLabel . ' id="btn-' . $this->addon->id . '" class="jpb-btn' . $button_classes . '">' . $button_text . '</a>' : '';

		$pricesymbol = ($price_symbol) ? '<span class="jpb-pricing-price-symbol">' . $price_symbol . '</span>' : '';

		// Output
		$output = '<div class="jpb-addon jpb-addon-pricing-table ' . $class . '">';
		$output .= '<div class="jpb-pricing-box ' . $featured . '">';
		$output .= '<div class="jpb-pricing-header">';

		$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title jpb-pricing-title">' . $title . '</' . $heading_selector . '>' : '';
		if ($price_position == 'after') {
			$output .= '<div class="jpb-pricing-price-container">';
			$output .= ($price) ? '<span class="jpb-pricing-price">' . $pricesymbol . $price . '</span>' : '';
			$output .= ($duration) ? '<span class="jpb-pricing-duration">' . $duration . '</span>' : '';
			$output .= '</div>';
		}
		$output .= '</div>';

		if ($pricing_content) {
			$output .= '<div class="jpb-pricing-features">';
			if ($button_position === "top" || $button_position === "both") {
				$output .= $button_output;
			}
			$output .= '<ul>';

			$features = explode ( "\n", $pricing_content );

			foreach ( $features as $feature ) {
				$output .= '<li>' . $feature . '</li>';
			}

			$output .= '</ul>';
			$output .= '</div>';
		}

		if ($price_position == 'before') {
			$output .= '<div class="jpb-pricing-price-container">';
			$output .= ($price) ? '<span class="jpb-pricing-price after">' . $pricesymbol . $price . '</span>' : '';
			$output .= ($duration) ? '<span class="jpb-pricing-duration">' . $duration . '</span>' : '';
			$output .= '</div>';
		}

		$output .= '<div class="jpb-pricing-footer">';

		if ($button_position === "bottom" || $button_position === "both") {
			$output .= $button_output;
		}
		$output .= '</div>';
		$output .= '</div>';
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
		$settings = $this->addon->settings;
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );

		$settings->alignment = JpagebuilderCSSHelper::parseAlignment ( $settings, 'alignment' );
		$priceStyle = $cssHelper->generateStyle ( '.jpb-pricing-price', $settings, [ 
				'price_color' => 'color'
		], false );
		$priceTypographyStyle = $cssHelper->typography ( '.jpb-pricing-title', $settings, 'title_typography', [ 
				'font' => 'title_font_family',
				'size' => 'title_fontsize',
				'line_height' => 'title_lineheight',
				'letter_spacing' => 'title_letterspace',
				'uppercase' => 'title_font_style.uppercase',
				'italic' => 'title_font_style.italic',
				'underline' => 'title_font_style.underline',
				'weight' => 'title_font_style.weight'
		] );
		$priceFontStyle = $cssHelper->typography ( '.jpb-pricing-price', $settings, 'price_typography', [ 
				'font' => 'price_font_family',
				'size' => 'price_font_size',
				'weight' => 'price_font_weight'
		] );
		$priceSymbolStyle = $cssHelper->generateStyle ( '.jpb-pricing-price-symbol', $settings, [ 
				'price_symbol_color' => 'color',
				'price_symbol_alignment' => 'vertical-align',
				'price_symbol_font_size' => 'font-size'
		], [ 
				'price_symbol_color' => false,
				'price_symbol_alignment' => false
		] );
		$durationStyle = $cssHelper->generateStyle ( '.jpb-pricing-duration', $settings, [ 
				'duration_color' => 'color',
				'duration_font_size' => 'font-size'
		], [ 
				'duration_color' => false
		] );
		$pricingContentStyle = $cssHelper->generateStyle ( '.jpb-pricing-features ul li', $settings, [ 
				'pricing_content_gap' => 'margin-bottom'
		] );
		$pricingContentFontStyle = $cssHelper->typography ( '.jpb-pricing-features', $settings, 'pricing_content_typography', [ 
				'font' => 'pricing_content_font_family',
				'size' => 'pricing_content_font_size'
		] );
		$pricingContentParentStyle = $cssHelper->generateStyle ( '.jpb-pricing-features', $settings, [ 
				'pricing_content_margin_bottom' => 'margin-bottom'
		] );
		$priceContainerStyle = $cssHelper->generateStyle ( '.jpb-pricing-price-container', $settings, [ 
				'price_margin_bottom' => 'margin-bottom',
				'price_padding_bottom' => 'padding-bottom',
				'price_border_bottom' => 'border-style: solid; border-width: 0 0 %s',
				'price_border_bottom_color' => 'border-color'
		], [ 
				'price_border_bottom_color' => false
		] );

		$settings->pricing_hover_boxshadow = JpagebuilderCSSHelper::parseBoxShadow ( $settings, 'pricing_hover_boxshadow' );

		$pricingHoverStyle = $cssHelper->generateStyle ( '&:hover', $settings, [ 
				'pricing_hover_bg' => 'background-color',
				'pricing_hover_scale' => 'transform: scale(%s)',
				'pricing_hover_boxshadow' => 'box-shadow'
		], false );
		$pricingHoverColorStyle = $cssHelper->generateStyle ( '&:hover .jpb-pricing-header .jpb-pricing-duration,&:hover .jpb-pricing-header .jpb-pricing-price,&:hover .jpb-pricing-header .jpb-addon-title,&:hover .jpb-pricing-features ul li', $settings, [ 
				'pricing_hover_color' => 'color'
		], false );
		$pricingHoverBorderColorStyle = $cssHelper->generateStyle ( '&:hover', $settings, [ 
				'pricing_hover_border_color' => 'border-color'
		], false );

		$settings->pricing_transition_duration = '0.4s';
		$pricingTransitionDurationStyle = $cssHelper->generateStyle ( ':self', $settings, [ 
				'pricing_transition_duration' => "transition"
		], false );
		$priceAlignment = $cssHelper->generateStyle ( '.jpb-addon-pricing-table', $settings, [ 
				'alignment' => 'text-align'
		], false );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-pricing-table', $settings, 'transform' );

		$css = '';
		$css .= $priceStyle;
		$css .= $durationStyle;
		$css .= $priceAlignment;
		$css .= $priceFontStyle;
		$css .= $priceSymbolStyle;
		$css .= $transformCss;
		$css .= $pricingHoverStyle;
		$css .= $priceContainerStyle;
		$css .= $pricingContentStyle;
		$css .= $priceTypographyStyle;
		$css .= $pricingHoverColorStyle;
		$css .= $pricingContentFontStyle;
		$css .= $pricingContentParentStyle;
		$css .= $pricingHoverBorderColorStyle;
		$css .= $pricingTransitionDurationStyle;

		// Button css
		$layoutPath = JPATH_ROOT . '/components/com_jpagebuilder/layouts';
		$buttonLayout = new FileLayout ( 'addon.css.button', $layoutPath );
		$css .= $buttonLayout->render ( array (
				'addon_id' => $addon_id,
				'options' => $settings,
				'id' => 'btn-' . $this->addon->id
		) );

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
			let price_position = data.price_position || "before";

			var heading_selector = data.heading_selector || "div";

			let price_symbol = "";
			if(data.price_symbol){
				price_symbol = \'<span class="jpb-pricing-price-symbol">\'+data.price_symbol+\'</span>\';
			}

			let buttonText = "";

			const isMenu = _.isObject(data.button_url) && data.button_url.type === "menu" && data.button_url?.menu;
			const isPage = _.isObject(data.button_url) && data.button_url.type === "page" && data.button_url?.page;
			const isUrl = _.isObject(data.button_url) && data.button_url.type === "url" && data.button_url?.url;
			const isOldUrl = _.isString(data.button_url) && data.button_url !== "";

			const urlObj = _.isObject(data.button_url) ? data.button_url : window.getSiteUrl(data?.button_url || "", data?.button_target || "");
			const {url, page, menu, type, new_tab, nofollow, noopener, noreferrer} = urlObj;
			const target = new_tab ? "_blank" : "";
			
			let rel="";
			rel += nofollow ? "nofollow" : "";
			rel += noreferrer ? " noreferrer" : "";
			rel += noopener ? " noopener" : "";
		
			let newUrl = "";
			if(type === "url") newUrl = url;
			if(type === "menu") newUrl = menu;
			if(type === "page") newUrl = page ? `index.php?option=com_jpagebuilder&view=page&id=${page}` : "";

			let buttonAttribs = (new_tab)? " target=\"_blank\"":"";
			buttonAttribs += (isMenu || isPage || isUrl || isOldUrl)? " href=\""+ newUrl +"\"":"";
			buttonAttribs += (rel)? " rel=\""+ rel +"\"": "";

			let buttonClasses = (data.button_size)? " jpb-btn-"+ data.button_size : "";
			buttonClasses += (data.button_type)? " jpb-btn-"+ data.button_type : ""
			buttonClasses += (data.button_shape)? " jpb-btn-"+ data.button_shape : ""
			buttonClasses += (data.button_appearance)? " jpb-btn-"+ data.button_appearance : ""
			buttonClasses += (data.button_block)? " "+ data.button_block : ""

			let icon_arr = (typeof data.button_icon !== "undefined" && data.button_icon) ? data.button_icon.split(" ") : "";
			let icon_name = icon_arr.length === 1 ? "fa "+data.button_icon : data.button_icon;

			if ( data.button_icon_position == "left" ) {
				buttonText = ( data.button_icon )? ` <i class="${icon_name}"></i> ` + data.button_text : data.button_text
			} else {
				buttonText = ( data.button_icon )? data.button_text + ` <i class="${icon_name}"></i> ` : data.button_text
			}

			let buttonPosition = data?.button_position || "bottom";

			let btnAriaLabel = data?.button_aria_label || "";

			let buttonOutput = (buttonText)? "<a" + buttonAttribs + " id=\"btn-" + data.id + "\" class=\"jpb-btn" + buttonClasses + "\" aria-label=\"" + btnAriaLabel + "\">" + buttonText + "</a>"
			: "";
			
			var modern_font_style = false;
			var button_fontstyle = data.button_fontstyle || "";
			var button_font_style = data.button_font_style || "";

			
		#>
		<style type="text/css"> ';

		$output .= '
				#jpb-addon-{{ data.id }} .jpb-pricing-header .jpb-pricing-duration,
				#jpb-addon-{{ data.id }} .jpb-pricing-header .jpb-pricing-price,
				#jpb-addon-{{ data.id }} .jpb-pricing-header .jpb-addon-title,
				#jpb-addon-{{ data.id }} .jpb-pricing-features ul li,
				#jpb-addon-{{ data.id }} .jpb-pricing-price-container,
				#jpb-addon-{{ data.id }} {
					transition:.4s;
				}
			<# if(data.price_border_bottom || data.price_border_bottom_color) { #>
				#jpb-addon-{{ data.id }} .jpb-pricing-price-container {
					border-style: solid;
					border-width:0 0 0 0;		
				}
			<# } #>';

		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );

		$output .= $lodash->unit ( 'margin-bottom', '.jpb-pricing-price-container', 'data.price_margin_bottom', 'px' );
		$output .= $lodash->unit ( 'padding-bottom', '.jpb-pricing-price-container', 'data.price_padding_bottom', 'px' );
		$output .= $lodash->unit ( 'border-bottom-width', '.jpb-pricing-price-container', 'data.price_border_bottom', 'px' );
		$output .= $lodash->border ( 'border-color', '.jpb-pricing-price-container', 'data.price_border_bottom_color' );
		$output .= $lodash->color ( 'color', '.jpb-pricing-price', 'data.price_color' );
		$output .= $lodash->unit ( 'font-size', '.jpb-pricing-price', 'data.price_font_size', 'px' );
		$output .= $lodash->unit ( 'line-height', '.jpb-pricing-price', 'data.price_font_size', 'px' );
		$output .= $lodash->unit ( 'font-weight', '.jpb-pricing-price', 'data.price_font_weight' );
		$output .= $lodash->color ( 'color', '.jpb-pricing-price-symbol', 'data.price_symbol_color' );
		$output .= $lodash->unit ( 'vertical-align', '.jpb-pricing-price-symbol', 'data.price_symbol_alignment' );
		$output .= $lodash->unit ( 'font-size', '.jpb-pricing-price-symbol', 'data.price_symbol_font_size', 'px' );
		$output .= $lodash->unit ( 'line-height', '.jpb-pricing-price-symbol', 'data.price_symbol_font_size', 'px' );
		$output .= $lodash->color ( 'color', '.jpb-pricing-duration', 'data.duration_color' );
		$output .= $lodash->unit ( 'font-size', '.jpb-pricing-duration', 'data.duration_font_size', 'px' );
		$output .= $lodash->unit ( 'line-height', '.jpb-pricing-duration', 'data.duration_font_size', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-pricing-features ul li', 'data.pricing_content_gap', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-pricing-features', 'data.pricing_content_margin_bottom', 'px' );

		$output .= '<# if (data.button_type == "custom") { #>';
		$output .= $lodash->color ( 'color', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_color' );
		$output .= $lodash->color ( 'color', '#btn-{{ data.id }}.jpb-btn-custom:hover', 'data.button_color_hover' );
		$output .= $lodash->color ( 'background-color', '#btn-{{ data.id }}.jpb-btn-custom:hover', 'data.button_background_color_hover' );
		$output .= $lodash->spacing ( 'padding', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_padding' );
		$output .= $lodash->unit ( 'margin-top', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_margin_bottom', 'px' );

		$output .= '<# if (data.button_appearance == "outline") { #>';
		$output .= $lodash->border ( 'border-color', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_background_color' );
		$output .= $lodash->border ( 'border-color', '#btn-{{ data.id }}.jpb-btn-custom:hover', 'data.button_background_color_hover' );
		$output .= '<# } else if (data.button_appearance == "3d") { #>';
		$output .= $lodash->border ( 'border-bottom-color', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_background_color_hover' );
		$output .= $lodash->color ( 'background-color', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_background_color' );
		$output .= '<# } else if (data.button_appearance == "gradient") { #>';
		$output .= '#jpb-addon-{{ data.id }} #btn-{{ data.id }}.jpb-btn-custom { border: none; }';
		$output .= $lodash->color ( 'background-color', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_background_gradient' );
		$output .= $lodash->color ( 'background-color', '#btn-{{ data.id }}.jpb-btn-custom:hover', 'data.button_background_gradient_hover' );
		$output .= '<# } else { #>';
		$output .= $lodash->color ( 'background-color', '#btn-{{ data.id }}.jpb-btn-custom', 'data.button_background_color' );

		$output .= '<# } #>';
		$output .= '<# } #>';

		$buttonTypographyFallbacks = [ 
				'font' => 'data.button_font_family',
				'letter_spacing' => 'data.button_letterspace',
				'weight' => 'data.button_fontstyle?.weight',
				'italic' => 'data.button_fontstyle?.italic',
				'underline' => 'data.button_fontstyle?.underline',
				'uppercase' => 'data.button_fontstyle?.uppercase'
		];

		$output .= $lodash->typography ( '#btn-{{ data.id }}.jpb-btn-{{ data.button_type }}', 'data.button_typography', $buttonTypographyFallbacks );

		$titleFallbacks = [ 
				'font' => 'data.title_font_family',
				'size' => 'data.title_fontsize',
				'line_height' => 'data.title_lineheight',
				'letter_spacing' => 'data.title_letterspace',
				'uppercase' => 'data.title_font_style?.uppercase',
				'italic' => 'data.title_font_style?.italic',
				'underline' => 'data.title_font_style?.underline',
				'weight' => 'data.title_font_style?.weight'
		];

		$output .= $lodash->typography ( '.jpb-pricing-title', 'data.title_typography', $titleFallbacks );

		$priceContentFallbacks = [ 
				'font' => 'data.price_font_family',
				'size' => 'data.price_font_size',
				'weight' => 'data.price_font_weight'
		];

		$output .= $lodash->typography ( '.jpb-pricing-price', 'data.price_typography', $priceContentFallbacks );

		$priceFeaturesFallbacks = [ 
				'font' => 'data.pricing_content_font_family',
				'size' => 'data.pricing_content_font_size'
		];

		$output .= $lodash->typography ( '.jpb-pricing-features', 'data.pricing_content_typography', $priceFeaturesFallbacks );
		$output .= $lodash->transform ( 'scale', '&:hover', 'data.pricing_hover_scale' );
		$output .= $lodash->boxShadow ( '&:hover', 'data.pricing_hover_boxshadow' );
		$output .= $lodash->color ( 'background-color', '&:hover', 'data.pricing_hover_bg' );
		$output .= $lodash->color ( 'color', '&:hover .jpb-pricing-header .jpb-pricing-duration, &:hover .jpb-pricing-header .jpb-pricing-price, &:hover .jpb-pricing-header .jpb-addon-title, &:hover .jpb-pricing-features ul li', 'data.pricing_hover_color' );
		$output .= $lodash->border ( 'border-color', '&:hover', 'data.pricing_hover_border_color' );

		$output .= $lodash->alignment ( 'text-align', '.jpb-addon-pricing-table', 'data.alignment' );
		$output .= $lodash->generateTransformCss ( '.jpb-addon-pricing-table', 'data.transform' );

		$output .= '
		</style>

		<div class="jpb-addon jpb-addon-pricing-table {{ data.class }}">
			<div class="jpb-pricing-box {{ data.featured }}">
				<div class="jpb-pricing-header">
					<# if( data.title ) { #>
						<{{ heading_selector }} class="jpb-addon-title jpb-pricing-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</{{ heading_selector }}>
					<# } #>
					<# if( price_position == "after" ) { #>
						<div class="jpb-pricing-price-container">
							<# if( data.price ) { #>
								<span class="jpb-pricing-price">{{{ price_symbol }}}{{{ data.price }}}</span>
							<# } #>
							<# if( data.duration ) { #>
								<span class="jpb-pricing-duration">{{ data.duration }}</span>
							<# } #>
						</div>
					<# } #>
				</div>

				<# if(data.pricing_content) { #>
					<div class="jpb-pricing-features">
						<# if (buttonPosition === "top" || buttonPosition === "both") { #>
							{{{ buttonOutput }}}
						<# } #>
						<ul>
							<# let pContentArray = data.pricing_content?.split("\n") #>
							<# _.each(pContentArray,function(item,index){ #>
								<# if(item) { #> <li>{{{ item }}}</li><# } #>
							<# }) #>
						</ul>
					</div>
				<# } #>
				<# if( price_position == "before" ) { #>
					<div class="jpb-pricing-price-container">
						<# if( data.price ) { #>
							<span class="jpb-pricing-price">{{{ price_symbol }}}{{{ data.price }}}</span>
						<# } #>
						<# if( data.duration ) { #>
							<span class="jpb-pricing-duration">{{ data.duration }}</span>
						<# } #>
					</div>
				<# } #>
				<div class="jpb-pricing-footer">
				<# if (buttonPosition === "bottom" || buttonPosition === "both") { #>
					{{{ buttonOutput }}}
				<# } #>
				</div>
			</div>
		</div>
		';

		return $output;
	}
}