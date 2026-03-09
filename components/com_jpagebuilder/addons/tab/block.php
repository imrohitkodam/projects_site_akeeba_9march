<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
class JpagebuilderAddonTab extends JpagebuilderAddons {
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
		$style = (isset ( $settings->style ) && $settings->style) ? $settings->style : '';
		$title = (isset ( $settings->title ) && $settings->title) ? $settings->title : '';
		$nav_icon_postion = (isset ( $settings->nav_icon_postion ) && $settings->nav_icon_postion) ? $settings->nav_icon_postion : '';
		$nav_image_postion = (isset ( $settings->nav_image_postion ) && $settings->nav_image_postion) ? $settings->nav_image_postion : '';
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$nav_text_align = (isset ( $settings->nav_text_align ) && $settings->nav_text_align) ? $settings->nav_text_align : 'jpb-text-left';

		$nav_position = (isset ( $settings->nav_position ) && $settings->nav_position) ? $settings->nav_position : 'nav-left';

		// Output
		$output = '<div class="jpb-addon jpb-addon-tab ' . $class . '">';
		$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>' : '';
		$output .= '<div class="jpb-addon-content jpb-tab jpb-' . $style . '-tab jpb-tab-nav-position">';
		// $output .= '<div class="jpb-addon-content jpb-tab jpb-' . $style . '-tab jpb-tab-' . $nav_position . ' jpb-tab-nav-position">';

		// Tab Title
		$output .= '<ul class="jpb-nav jpb-nav-' . $style . '" role="tablist">';

		if(isset($settings->jp_tab_item) && is_array($settings->jp_tab_item)) {
			foreach ( $settings->jp_tab_item as $key => $tab ) {
				$icon_top = '';
				$icon_bottom = '';
				$icon_right = '';
				$icon_left = '';
				$icon_block = '';
	
				// Image
				$image_top = '';
				$image_bottom = '';
				$image_right = '';
				$image_left = '';
	
				// Lazy load image
				$tab_image = isset ( $tab->image ) && $tab->image ? $tab->image : '';
				$tab_image_src = isset ( $tab_image->src ) ? $tab_image->src : $tab_image;
				$tab_image_width = (isset ( $tab_image->width ) && $tab_image->width) ? $tab_image->width : '';
				$tab_image_height = (isset ( $tab_image->height ) && $tab_image->height) ? $tab_image->height : '';
	
				$placeholder = $tab_image_src == '' ? false : $this->get_image_placeholder ( $tab_image_src );
	
				if (strpos ( $tab_image_src, "http://" ) !== false || strpos ( $tab_image_src, "https://" ) !== false) {
					$tab_image_src = $tab_image_src;
				} else {
					if ($tab_image_src) {
						$tab_image_src = Uri::base () . $tab_image_src;
					}
				}
	
				$title = (isset ( $tab->title ) && $tab->title) ? ' ' . $tab->title . ' ' : '';
				$subtitle = (isset ( $tab->subtitle ) && $tab->subtitle) ? '<span class="jpb-tab-subtitle">' . $tab->subtitle . '</span>' : '';
	
				if (isset ( $tab->image_or_icon ) && $tab->image_or_icon == 'image') {
					if ($tab_image_src && $nav_image_postion == 'top') {
						$image_top = '<img class="jpb-tab-image tab-image-block' . ($placeholder ? ' jpb-element-lazy' : '') . '" src="' . ($placeholder ? $placeholder : $tab_image_src) . '" alt="' . trim ( strip_tags ( $title ) ) . '" ' . ($placeholder ? 'data-large="' . $tab_image_src . '"' : '') . ' ' . ($tab_image_width ? 'width="' . $tab_image_width . '"' : '') . ' ' . ($tab_image_height ? 'height="' . $tab_image_height . '"' : '') . ' loading="lazy"/>';
					} elseif ($tab_image_src && $nav_image_postion == 'bottom') {
						$image_bottom = '<img class="jpb-tab-image tab-image-block' . ($placeholder ? ' jpb-element-lazy' : '') . '" src="' . ($placeholder ? $placeholder : $tab_image_src) . '" alt="' . trim ( strip_tags ( $title ) ) . '" ' . ($placeholder ? 'data-large="' . $tab_image_src . '"' : '') . ' ' . ($tab_image_width ? 'width="' . $tab_image_width . '"' : '') . ' ' . ($tab_image_height ? 'height="' . $tab_image_height . '"' : '') . ' loading="lazy"/>';
					} elseif ($tab_image_src && $nav_image_postion == 'right') {
						$image_right = '<img class="jpb-tab-image' . ($placeholder ? ' jpb-element-lazy' : '') . '" src="' . ($placeholder ? $placeholder : $tab_image_src) . '" alt="' . trim ( strip_tags ( $title ) ) . '" ' . ($placeholder ? 'data-large="' . $tab_image_src . '"' : '') . ' ' . ($tab_image_width ? 'width="' . $tab_image_width . '"' : '') . ' ' . ($tab_image_height ? 'height="' . $tab_image_height . '"' : '') . ' loading="lazy"/>';
					} else {
						$image_left = $tab_image_src ? '<img class="jpb-tab-image' . ($placeholder ? ' jpb-element-lazy' : '') . '" src="' . ($placeholder ? $placeholder : $tab_image_src) . '" alt="' . trim ( strip_tags ( $title ) ) . '" ' . ($placeholder ? 'data-large="' . $tab_image_src . '"' : '') . ' ' . ($tab_image_width ? 'width="' . $tab_image_width . '"' : '') . ' ' . ($tab_image_height ? 'height="' . $tab_image_height . '"' : '') . ' loading="lazy"/>' : '';
					}
				} else {
	
					if (isset ( $tab->icon ) && $tab->icon) {
						$icon_arr = array_filter ( explode ( ' ', $tab->icon ) );
	
						if (count ( $icon_arr ) === 1) {
							$tab->icon = 'fa ' . $tab->icon;
						}
	
						if ($tab->icon && $nav_icon_postion === 'top') {
							$icon_top = '<span class="jpb-tab-icon tab-icon-block" aria-label="' . trim ( strip_tags ( $title ) ) . '"><i class="' . $tab->icon . '" aria-hidden="true"></i></span>';
						} elseif ($tab->icon && $nav_icon_postion === 'bottom') {
							$icon_bottom = '<span class="jpb-tab-icon tab-icon-block" aria-label="' . trim ( strip_tags ( $title ) ) . '"><i class="' . $tab->icon . '" aria-hidden="true"></i></span>';
						} elseif ($tab->icon && $nav_icon_postion === 'right') {
							$icon_right = '<span class="jpb-tab-icon" aria-label="' . trim ( strip_tags ( $title ) ) . '"><i class="' . $tab->icon . '" aria-hidden="true"></i></span>';
						} else {
							$icon_left = '<span class="jpb-tab-icon" aria-label="' . trim ( strip_tags ( $title ) ) . '"><i class="' . $tab->icon . '" aria-hidden="true"></i></span>';
						}
					}
				}
	
				if ($nav_icon_postion === 'top' || $nav_icon_postion === 'bottom' || $nav_image_postion === 'top' || $nav_image_postion === 'bottom') {
					$icon_block = 'tab-img-or-icon-block-wrap';
				}
	
				$output .= '<li role="presentation" class="' . (($key === 0) ? "active" : "") . '">';
				$output .= '<a data-toggle="jpb-tab" id="jpb-content-' . ($this->addon->id . $key) . '" class="' . $icon_block . '" href="#jpb-tab-' . ($this->addon->id . $key) . '" role="tab" aria-controls="jpb-tab-' . ($this->addon->id . $key) . '" aria-selected="' . (($key == 0) ? "true" : "false") . '">' . $image_top . $image_left . $icon_top . $icon_left . $title . $image_right . $image_bottom . $icon_right . $icon_bottom . $subtitle . '</a>';
				$output .= '</li>';
			}
		}

		$output .= '</ul>';

		// Tab Content
		$output .= '<div class="jpb-tab-content jpb-tab-' . $style . '-content">';

		if(isset($settings->jp_tab_item) && is_array($settings->jp_tab_item)) {
			foreach ( $settings->jp_tab_item as $key => $tab ) {
				$output .= '<div id="jpb-tab-' . ($this->addon->id . $key) . '" class="jpb-tab-pane jpb-fade' . (($key == 0) ? " active in" : "") . '" role="tabpanel" aria-labelledby="jpb-content-' . ($this->addon->id . $key) . '">' . $tab->content . '</div>';
			}
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
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$settings = $this->addon->settings;
		$cssHelper = new JpagebuilderCSSHelper ( $addon_id );
		$css = '';

		$nav_position = (isset ( $settings->nav_position_original ) && $settings->nav_position_original) ? $settings->nav_position_original : 'nav-top';

		$settings->nav_padding = $settings->nav_padding_original ?? $settings->nav_padding ?? '';
		$settings->nav_margin = $settings->nav_margin_original ?? $settings->nav_margin ?? '';

		$tab_style = (isset ( $settings->style ) && $settings->style) ? $settings->style : '';

		$settings->nav_gutter = empty ( $settings->nav_gutter ) ? 0 : $settings->nav_gutter;

		$selector = '';
		$props = [ 
				'active_tab_color' => 'color',
				'active_tab_border_width' => 'border-width',
				'active_tab_border_color' => 'border-color'
		];
		$units = [ 
				'active_tab_color' => false,
				'active_tab_border_color' => false,
				'active_tab_bg' => false,
				'active_tab_border_width' => false
		];

		switch ($tab_style) {
			case 'pills' :
				$selector = '.jpb-nav-pills > li.active > a, .jpb-nav-pills > li.active > a:hover, .jpb-nav-pills > li.active > a:focus';
				$props ['active_tab_bg'] = 'background';
				break;
			case 'lines' :
				$selector = '.jpb-nav-lines > li.active > a, .jpb-nav-lines > li.active > a:hover, .jpb-nav-lines > li.active > a:focus';
				$props ['active_tab_bg'] = 'border-bottom-color';
				break;
			case 'custom' :
				$selector = '.jpb-nav-custom > li.active > a, .jpb-nav-custom > li.active > a:hover, .jpb-nav-custom > li.active > a:focus';
				$props ['active_tab_bg'] = 'background';
				break;
		}

		$tabStyle = $cssHelper->generateStyle ( $selector, $settings, $props, $units );
		$navFontStyle = $cssHelper->typography ( '.jpb-nav-custom a', $settings, 'nav_typography', [ 
				'font' => 'nav_font_family',
				'size' => 'nav_fontsize',
				'line_height' => 'nav_lineheight',
				'uppercase' => 'nav_font_style.uppercase',
				'italic' => 'nav_font_style.italic',
				'underline' => 'nav_font_style.underline',
				'weight' => 'nav_font_style.weight'
		] );

		$nav_border = (isset ( $settings->nav_border ) && trim ( $settings->nav_border )) ? $settings->nav_border : '';

		$isUnitPresent = preg_match ( '/(px|rem|em|%)\b/', $nav_border );

		$navStyle = $cssHelper->generateStyle ( '.jpb-nav-custom a', $settings, [ 
				'nav_border' => $isUnitPresent ? 'border-style: solid; border-width' : 'border: %spx solid',
				'nav_border_color' => 'border-color',
				'nav_color' => 'color',
				'nav_bg_color' => 'background',
				'nav_border_radius' => 'border-radius',
				'nav_padding' => 'padding',
				'nav_margin' => 'margin'
		], [ 
				'nav_border' => $isUnitPresent ? false : 'px',
				'nav_border_color' => false,
				'nav_color' => false,
				'nav_bg_color' => false,
				'nav_padding' => false,
				'nav_margin' => false
		], [ 
				'nav_padding' => 'spacing',
				'nav_margin' => 'spacing'
		] );

		$customNavPadding = $cssHelper->generateStyle ( '.jpb-nav-custom li', $settings, [ 
				'nav_padding' => 'padding',
				'nav_margin' => 'margin'
		], false, [ 
				'nav_padding' => 'spacing',
				'nav_margin' => 'spacing'
		] );

		$contentStyle = $cssHelper->generateStyle ( '.jpb-tab-custom-content > div', $settings, [ 
				'content_backround' => 'background',
				'content_border' => 'border: %spx solid',
				'content_color' => 'color',
				'content_border_color' => 'border-color',
				'content_border_radius' => 'border-radius',
				'content_margin' => 'margin',
				'content_padding' => 'padding'
		], [ 
				'content_backround' => false,
				'content_border' => false,
				'content_color' => false,
				'content_border_color' => false,
				'content_margin' => false,
				'content_padding' => false
		], [ 
				'content_margin' => 'spacing',
				'content_padding' => 'spacing'
		] );

		$contentFontStyle = $cssHelper->typography ( '.jpb-tab-custom-content > div', $settings, 'content_typography', [ 
				'font' => 'content_font_family',
				'size' => 'content_fontsize',
				'line_height' => 'content_lineheight',
				'uppercase' => 'content_font_style.uppercase',
				'italic' => 'content_font_style.italic',
				'underline' => 'content_font_style.underline',
				'weight' => 'content_font_style.weight'
		] );

		$show_boxshadow = (isset ( $settings->show_boxshadow ) && $settings->show_boxshadow) ? $settings->show_boxshadow : '';
		$box_shadow = '';

		if ($show_boxshadow) {
			$box_shadow .= (isset ( $settings->shadow_horizontal ) && $settings->shadow_horizontal) ? $settings->shadow_horizontal . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_vertical ) && $settings->shadow_vertical) ? $settings->shadow_vertical . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_blur ) && $settings->shadow_blur) ? $settings->shadow_blur . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_spread ) && $settings->shadow_spread) ? $settings->shadow_spread . 'px ' : '0 ';
			$box_shadow .= (isset ( $settings->shadow_color ) && $settings->shadow_color) ? $settings->shadow_color : 'rgba(0, 0, 0, .5)';
		}

		$settings->dummy_box_shadow = $box_shadow;
		$boxShadowStyle = $cssHelper->generateStyle ( '.jpb-tab-custom-content > div, .jpb-nav-custom a', $settings, [ 
				'dummy_box_shadow' => 'box-shadow'
		], false );
		$iconStyle = $cssHelper->generateStyle ( '.jpb-tab-icon', $settings, [ 
				'icon_fontsize' => 'font-size',
				'icon_margin' => 'margin',
				'icon_color' => 'color'
		], [ 
				'icon_margin' => false,
				'icon_color' => false
		], [ 
				'icon_margin' => 'spacing'
		] );
		$imageStyle = $cssHelper->generateStyle ( '.jpb-tab-image', $settings, [ 
				'image_height' => 'height',
				'image_width' => 'width',
				'image_margin' => 'margin'
		], [ 
				'image_margin' => false
		], [ 
				'image_margin' => 'spacing'
		] );
		$customHoverStyle = $cssHelper->generateStyle ( '.jpb-nav-custom > li > a:hover, .jpb-nav-custom > li > a:focus', $settings, [ 
				'hover_tab_color' => 'color',
				'hover_tab_border_width' => 'border-width',
				'hover_tab_border_color' => 'border-color',
				'hover_tab_bg' => 'background-color'
		], [ 
				'hover_tab_color' => false,
				'hover_tab_border_color' => false,
				'hover_tab_bg' => false,
				'hover_tab_border_width' => false
		] );
		$customIconHoverStyle = $cssHelper->generateStyle ( '.jpb-nav-custom > li > a:hover  > .jpb-tab-icon,.jpb-nav-custom > li > a:focus > .jpb-tab-icon', $settings, [ 
				'icon_color_hover' => 'color'
		], false );
		$customIconActiveStyle = $cssHelper->generateStyle ( '.jpb-nav-custom > li.active > a > .jpb-tab-icon, .jpb-nav-custom > li.active > a:hover  > .jpb-tab-icon, .jpb-nav-custom > li.active > a:focus > .jpb-tab-icon', $settings, [ 
				'icon_color_active' => 'color'
		], false );

		if ($tab_style == "custom") {
			$customContentWidth = $cssHelper->generateStyle ( '.jpb-tab-content.jpb-tab-custom-content', $settings, [ 
					'content_width' => 'max-width'
			], '%' );
		}

		$mediaQueries = [ 
				"xl" => "@media ( min-width: 1199.99px ) and ( max-width:1399.99px)",
				"lg" => "@media ( min-width: 991.99px ) and ( max-width: 1199.98px )",
				"md" => "@media ( min-width: 767.99px ) and ( max-width: 991.98px )",
				"sm" => "@media ( min-width: 575.99px ) and ( max-width: 767.98px )",
				"xs" => "@media ( max-width: 575.98px )"
		];

		$navGutter = $settings->nav_gutter_original ?? $settings->nav_gutter ?? 0;
		$navJustified = $settings->nav_justified_original ?? $settings->nav_justified ?? false;
		$navWidth = $settings->nav_width_original ?? $settings->nav_width ?? 30;

		$loopOutput = '';
		foreach ( $mediaQueries as $size => $media ) {

			$loopOutput .= "$media {";
			if (is_object ( $nav_position ) && isset ( $nav_position->$size )) {
				if ($nav_position->$size == "nav-left" || $nav_position->$size == "nav-right") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-tab-custom-content 
					{
						flex-basis: 0;
						flex-grow: 1;
					}";
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
				 	{
						flex-direction: column !important;
					}";
				}

				if ($nav_position->$size == "nav-right") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position
				 	{
						flex-direction: row-reverse !important;
					}";
				}

				if ($nav_position->$size == "nav-top") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
					{
						flex-direction: row !important;
					}";
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position 
					{
						flex-direction: column !important;
					}";
				}

				if ($nav_position->$size == "nav-bottom") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
					{
						flex-direction: row !important;
					}";
					$loopOutput .= ".jpb-custom-tab.jpb-tab-nav-position 
					{
						flex-direction: column-reverse !important;
					}";
				}

				$loopOutput .= "$addon_id .jpb-tab-content {";

				$newGutter = is_object ( $navGutter ) ? (empty ( $navGutter->$size ) ? 0 : $navGutter->$size) : $navGutter;
				$newNavWidth = is_object ( $navWidth ) ? (empty ( $navWidth->$size ) ? 30 : $navWidth->$size) : $navWidth;

				if ($nav_position->$size == "nav-top") {
					$loopOutput .= 'padding: ' . $newGutter . 'px 0 0 0 !important';
				} else if ($nav_position->$size == "nav-bottom") {
					$loopOutput .= 'padding: 0 0 ' . $newGutter . 'px 0 !important';
				} else if ($nav_position->$size == "nav-right") {
					$loopOutput .= 'padding: 0 ' . $newGutter . 'px 0 0 !important';
				} else {
					$loopOutput .= 'padding: 0 0 0 ' . $newGutter . 'px !important';
				}
				$loopOutput .= "}";

				$isNavJustified = is_object ( $navJustified ) ? (empty ( $navJustified->$size ) ? false : $navJustified->$size) : $navJustified;

				if (($nav_position->$size == "nav-top" || $nav_position->$size == "nav-bottom") && $isNavJustified) {
					$loopOutput .= "$addon_id .jpb-nav > li {flex: 1 1 auto !important;} $addon_id .jpb-nav {width: 100% !important;}";
				}

				if (($nav_position->$size == "nav-top" || $nav_position->$size == "nav-bottom") && ! $isNavJustified) {
					$loopOutput .= "$addon_id .jpb-nav {width: fit-content !important;}";
				}

				if ($tab_style == "custom" && ($nav_position->$size == "nav-left" || $nav_position->$size == "nav-right")) {
					$loopOutput .= $addon_id . '.jpb-nav { width: ' . $newNavWidth . '% !important;}';
				}
			} else {
				if ($nav_position == "nav-left" || $nav_position == "nav-right") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-tab-custom-content 
					{
						flex-basis: 0;
						flex-grow: 1;
					}";
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
				 	{
						flex-direction: column !important;
					}";
				}

				if ($nav_position == "nav-right") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position
				 	{
						flex-direction: row-reverse !important;
					}";
				}

				if ($nav_position == "nav-top") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
					 {
						flex-direction: row !important;
					}";
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position 
					{
						flex-direction: column !important;
					}";
				}

				if ($nav_position == "nav-bottom") {
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
					 {
						flex-direction: row !important;
					}";
					$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position 
					{
						flex-direction: column-reverse !important;
					}";
				}

				$loopOutput .= "$addon_id .jpb-tab-content {";

				$newGutter = is_object ( $navGutter ) ? (empty ( $navGutter->$size ) ? 0 : $navGutter->$size) : $navGutter;
				$newNavWidth = is_object ( $navWidth ) ? (empty ( $navWidth->$size ) ? 30 : $navWidth->$size) : $navWidth;

				if ($nav_position == "nav-top") {
					$loopOutput .= 'padding: ' . $newGutter . 'px 0 0 0 !important';
				} else if ($nav_position == "nav-bottom") {
					$loopOutput .= 'padding: 0 0 ' . $newGutter . 'px 0 !important';
				} else if ($nav_position == "nav-right") {
					$loopOutput .= 'padding: 0 ' . $newGutter . 'px 0 0 !important';
				} else {
					$loopOutput .= 'padding: 0 0 0 ' . $newGutter . 'px !important';
				}
				$loopOutput .= "}";

				$isNavJustified = is_object ( $navJustified ) ? (empty ( $navJustified->$size ) ? false : $navJustified->$size) : $navJustified;

				if (($nav_position == "nav-top" || $nav_position == "nav-bottom") && $isNavJustified) {
					$loopOutput .= "$addon_id .jpb-nav > li {flex: 1 1 auto !important;} $addon_id .jpb-nav {width: 100% !important;}";
				}

				if (($nav_position == "nav-top" || $nav_position == "nav-bottom") && ! $isNavJustified) {
					$loopOutput .= "$addon_id .jpb-nav {width: fit-content !important;}";
				}

				if ($tab_style == "custom" && ($nav_position == "nav-left" || $nav_position == "nav-right")) {
					$loopOutput .= "$addon_id .jpb-nav { width: $newNavWidth% !important;}";
				}
			}
			$loopOutput .= "}";
		}

		// default device style
		if (is_object ( $nav_position )) {
			if ($nav_position->xl == "nav-left" || $nav_position->xl == "nav-right") {
				$loopOutput .= ".jpb-custom-tab.jpb-tab-nav-position .jpb-tab-custom-content 
				{
					flex-basis: 0;
					flex-grow: 1;
				}";
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
				 {
					flex-direction: column;
				}";
			}

			if ($nav_position->xl == "nav-right") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position
				 {
					flex-direction: row-reverse;
				}";
			}

			if ($nav_position == "nav-top" || $nav_position == "nav-bottom") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
				 {
					flex-direction: row;
				}";
			}

			if ($nav_position->xl == "nav-top") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position 
				{
					flex-direction: column;
				}";
			}

			if ($nav_position->xl == "nav-bottom") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position 
				{
					flex-direction: column-reverse;
				}";
			}

			$loopOutput .= "$addon_id .jpb-tab-content {";

			$newGutter = is_object ( $navGutter ) ? (empty ( $navGutter->xl ) ? 0 : $navGutter->xl) : $navGutter;
			$newNavWidth = is_object ( $navWidth ) ? (empty ( $navWidth->xl ) ? 30 : $navWidth->xl) : $navWidth;

			if ($nav_position->xl == "nav-top") {
				$loopOutput .= 'padding-top: ' . $newGutter . 'px';
			} else if ($nav_position->xl == "nav-bottom") {
				$loopOutput .= 'padding-bottom: ' . $newGutter . 'px';
			} else if ($nav_position->xl == "nav-right") {
				$loopOutput .= 'padding-right: ' . $newGutter . 'px';
			} else {
				$loopOutput .= 'padding-left: ' . $newGutter . 'px';
			}
			$loopOutput .= "}";

			$isNavJustified = is_object ( $navJustified ) ? (empty ( $navJustified->xl ) ? false : $navJustified->xl) : $navJustified;

			if (($nav_position->xl == "nav-top" || $nav_position->xl == "nav-bottom") && $isNavJustified) {
				$loopOutput .= "$addon_id .jpb-nav > li {flex: 1 1 auto;} $addon_id .jpb-nav {width: 100%;}";
			}

			if (($nav_position->xl == "nav-top" || $nav_position->xl == "nav-bottom") && ! $isNavJustified) {
				$loopOutput .= "$addon_id .jpb-nav {width: fit-content;}";
			}

			if ($tab_style == "custom" && ($nav_position->xl == "nav-left" || $nav_position->xl == "nav-right")) {
				$loopOutput .= "$addon_id .jpb-nav { width: $newNavWidth%;}";
			}
		} else {
			if ($nav_position == "nav-left" || $nav_position == "nav-right") {
				$loopOutput .= ".jpb-custom-tab.jpb-tab-nav-position .jpb-tab-custom-content 
				{
					flex-basis: 0;
					flex-grow: 1;
				}";
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
				{
					flex-direction: column;
				}";
			}

			if ($nav_position == "nav-right") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position
				 {
					flex-direction: row-reverse;
				}";
			}

			if ($nav_position == "nav-top" || $nav_position == "nav-bottom") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position .jpb-nav.jpb-nav-custom
				 {
					flex-direction: row;
				}";
			}

			if ($nav_position == "nav-top") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position 
				{
					flex-direction: column;
				}";
			}

			if ($nav_position == "nav-bottom") {
				$loopOutput .= "$addon_id .jpb-custom-tab.jpb-tab-nav-position 
				{
					flex-direction: column-reverse;
				}";
			}

			$loopOutput .= "$addon_id .jpb-tab-content {";

			$newGutter = is_object ( $navGutter ) ? (empty ( $navGutter->xl ) ? 0 : $navGutter->xl) : $navGutter;
			$newNavWidth = is_object ( $navWidth ) ? (empty ( $navWidth->xl ) ? 30 : $navGutter->xl) : $navWidth;

			if ($nav_position == "nav-top") {
				$loopOutput .= 'padding-top: ' . $newGutter . 'px';
			} else if ($nav_position == "nav-bottom") {
				$loopOutput .= 'padding-bottom: ' . $newGutter . 'px';
			} else if ($nav_position == "nav-right") {
				$loopOutput .= 'padding-right: ' . $newGutter . 'px';
			} else {
				$loopOutput .= 'padding-left: ' . $newGutter . 'px';
			}
			$loopOutput .= "}";

			$isNavJustified = is_object ( $navJustified ) ? (empty ( $navJustified->xl ) ? false : $navJustified->xl) : $navJustified;

			if (($nav_position == "nav-top" || $nav_position == "nav-bottom") && $isNavJustified) {
				$loopOutput .= "$addon_id .jpb-nav > li {flex: 1 1 auto !important;} $addon_id .jpb-nav {width: 100%;}";
			}

			if (($nav_position == "nav-top" || $nav_position == "nav-bottom") && ! $isNavJustified) {
				$loopOutput .= "$addon_id .jpb-nav {width: fit-content;}";
			}

			if ($tab_style == "custom" && ($nav_position == "nav-left" || $nav_position == "nav-right")) {
				$loopOutput .= "$addon_id .jpb-nav { width: $newNavWidth%;}";
			}
		}

		$imgOrIconStyles = '';
		$imgOrIconStyles .= $cssHelper->generateStyle ( '.jpb-nav > li > a', $settings, [ 
				'nav_text_align_original' => 'text-align'
		], false );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-addon-tab', $settings, 'transform' );

		if ($tab_style === 'custom') {
			$css .= $imgOrIconStyles;
			$css .= $navStyle;
			$css .= $navFontStyle;
			$css .= $contentStyle;
			$css .= $boxShadowStyle;
			$css .= $customHoverStyle;
			$css .= $contentFontStyle;
			$css .= $customNavPadding;
			$css .= $customIconHoverStyle;
			$css .= $customIconActiveStyle;
			$css .= $customContentWidth;
			$css .= $loopOutput;
		}

		$css .= $tabStyle;
		$css .= $iconStyle;
		$css .= $transformCss;
		$css .= $imageStyle;
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
		<style type="text/css">
            <# 

                let box_shadow = ""; 
                if (data.show_boxshadow) {
                    box_shadow += (!_.isEmpty(data.shadow_horizontal) && data.shadow_horizontal) ?  data.shadow_horizontal + \'px \' : "0 ";
                    box_shadow += (!_.isEmpty(data.shadow_vertical) && data.shadow_vertical) ?  data.shadow_vertical + \'px \' : "0 ";
                    box_shadow += (!_.isEmpty(data.shadow_blur) && data.shadow_blur) ?  data.shadow_blur + \'px \' : "0 ";
                    box_shadow += (!_.isEmpty(data.shadow_spread) && data.shadow_spread) ?  data.shadow_spread + \'px \' : "0 ";
                    box_shadow += (!_.isEmpty(data.shadow_color) && data.shadow_color) ?  data.shadow_color : "rgba(0, 0, 0, .5)";
                }

				const mediaQueries = {
					lg: "@media ( min-width: 991.99px ) and ( max-width: 1199.98px )",
					md: "@media ( min-width: 767.99px ) and ( max-width: 991.98px )",
					sm: "@media ( min-width: 575.99px ) and ( max-width: 767.98px )",
					xs: "@media ( max-width: 575.98px )",
				};
				
				let navPosition = data?.nav_position  || "nav-top";
            #>';

		// title
		$titleTypographyFallbacks = [ 
				'font' => 'data.title_font_family',
				'size' => 'data.title_fontsize',
				'line_height' => 'data.title_lineheight',
				'letter_spacing' => 'data.title_letterspace',
				'uppercase' => 'data.title_font_style?.uppercase',
				'italic' => 'data.title_font_style?.italic',
				'underline' => 'data.title_font_style?.underline',
				'weight' => 'data.title_font_style?.weight'
		];
		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );
		$output .= $lodash->color ( 'color', '.jpb-addon-title', 'data.title_text_color' );
		$output .= $lodash->typography ( '.jpb-addon-title', 'data.title_typography', $titleTypographyFallbacks );

		// pills
		$output .= '<# if (data.style == "pills") { #>';
		$output .= $lodash->color ( 'color', '.jpb-nav-pills > li.active > a,.jpb-nav-pills > li.active > a:hover,.jpb-nav-pills > li.active > a:focus', 'data.active_tab_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-nav-pills > li.active > a,.jpb-nav-pills > li.active > a:hover,.jpb-nav-pills > li.active > a:focus', 'data.active_tab_bg' );
		$output .= '<# } #>';

		// lines
		$output .= '<# if (data.style == "lines") { #>';
		$output .= $lodash->color ( 'color', '.jpb-nav-lines > li.active > a,.jpb-nav-lines > li.active > a:hover,.jpb-nav-lines > li.active > a:focus', 'data.active_tab_color' );
		$output .= $lodash->unit ( 'border-bottom-color', '.jpb-nav-lines > li.active > a,.jpb-nav-lines > li.active > a:hover,.jpb-nav-lines > li.active > a:focus', 'data.active_tab_bg' );
		$output .= '<# } #>';

		// custom
		$output .= '<# if (data.style == "custom") { #>';
		$output .= $lodash->color ( 'color', '.jpb-nav-custom > li.active > a,.jpb-nav-custom > li.active > a:hover,.jpb-nav-custom > li.active > a:focus', 'data.active_tab_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-nav-custom > li.active > a,.jpb-nav-custom > li.active > a:hover,.jpb-nav-custom > li.active > a:focus', 'data.active_tab_bg' );
		$output .= $lodash->unit ( 'border-width', '.jpb-nav-custom > li.active > a,.jpb-nav-custom > li.active > a:hover,.jpb-nav-custom > li.active > a:focus', 'data.active_tab_border_width' );
		$output .= $lodash->unit ( 'border-color', '.jpb-nav-custom > li.active > a,.jpb-nav-custom > li.active > a:hover,.jpb-nav-custom > li.active > a:focus', 'data.active_tab_border_width' );

		// hover
		$output .= $lodash->color ( 'color', '.jpb-nav-custom > li > a:hover,.jpb-nav-custom > li > a:focus', 'data.hover_tab_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-nav-custom > li > a:hover,.jpb-nav-custom > li > a:focus', 'data.hover_tab_bg' );
		$output .= $lodash->unit ( 'border-width', '.jpb-nav-custom > li > a:hover,.jpb-nav-custom > li > a:focus', 'data.hover_tab_border_width' );
		$output .= $lodash->unit ( 'border-color', '.jpb-nav-custom > li > a:hover,.jpb-nav-custom > li > a:focus', 'data.hover_tab_border_color' );
		// icon
		$output .= $lodash->color ( 'color', '.jpb-nav-custom > li > a:hover > .jpb-tab-icon, .jpb-nav-custom > li > a:focus > .jpb-tab-icon', 'data.icon_color_hover' );
		$output .= $lodash->color ( 'color', '.jpb-nav-custom > li.active > a > .jpb-tab-icon,.jpb-nav-custom > li.active > a:hover > .jpb-tab-icon,.jpb-nav-custom > li.active > a:focus > .jpb-tab-icon', 'data.icon_color_active' );

		$output .= $lodash->spacing ( 'padding', '.jpb-nav-custom li', 'data.nav_margin' );
		$output .= $lodash->spacing ( 'padding', '.jpb-nav-custom li a', 'data.nav_padding' );

		$output .= $lodash->spacing ( 'margin', '.jpb-tab-icon', 'data.icon_margin' );
		$output .= $lodash->color ( 'color', '.jpb-tab-icon', 'data.icon_color' );
		$output .= $lodash->unit ( 'font-size', '.jpb-tab-icon', 'data.icon_fontsize', 'px' );

		$output .= '#jpb-addon-{{ data.id }} .jpb-tab-custom-content > div {border-style: solid;}';
		$output .= $lodash->color ( 'background-color', '.jpb-tab-custom-content > div', 'data.content_backround' );
		$output .= $lodash->color ( 'color', '.jpb-tab-custom-content > div', 'data.content_color' );
		$output .= $lodash->unit ( 'border-color', '.jpb-tab-custom-content > div', 'data.content_border_color' );
		$output .= $lodash->unit ( 'border-radius', '.jpb-tab-custom-content > div', 'data.content_border_radius' );
		$output .= $lodash->unit ( 'border', '.jpb-tab-custom-content > div', 'data.content_border' );
		$output .= $lodash->spacing ( 'padding', '.jpb-tab-custom-content > div', 'data.content_padding' );
		$output .= $lodash->spacing ( 'margin', '.jpb-tab-custom-content > div', 'data.content_margin' );

		$output .= '<# } #>';

		// pills, lines & custom
		$output .= '<# if (data.style == "pills" || data.style == "lines" || data.style == "custom") { #>';
		$output .= $lodash->color ( 'color', '.jpb-nav > li > a', 'data.nav_color' );
		$output .= $lodash->color ( 'color', '.jpb-nav > li:hover > a', 'data.hover_tab_color' );
		$output .= $lodash->color ( 'color', '.jpb-nav > li.active > a', 'data.active_tab_color' );

		$output .= $lodash->color ( 'background-color', '.jpb-tab-pane', 'data.content_backround' );
		$output .= $lodash->color ( 'color', '.jpb-tab-pane', 'data.content_color' );
		$output .= $lodash->unit ( 'border-color', '.jpb-tab-pane', 'data.content_border_color', '', false );
		$output .= '<# } #>';

		// pills & custom
		$output .= '<# if (data.style == "pills" || data.style == "custom") { #>';
		$output .= $lodash->color ( 'background-color', '.jpb-nav > li > a', 'data.nav_bg_color' );
		$output .= $lodash->color ( 'background-color', '.jpb-nav > li:hover > a', 'data.hover_tab_bg' );
		$output .= $lodash->color ( 'background-color', '.jpb-nav > li.active > a', 'data.active_tab_bg' );

		$output .= $lodash->unit ( 'border-radius', '.jpb-nav > li > a', 'data.nav_border_radius', 'px' );
		$output .= '<# } #>';

		// lines only
		$output .= '<# if (data.style == "lines") { #>';
		$output .= $lodash->unit ( 'border-bottom-color', '.jpb-nav-lines', 'data.nav_border_color', '', false );
		$output .= $lodash->unit ( 'border-bottom-color', '.jpb-nav > li:hover > a', 'data.hover_tab_border_color', '', false );
		$output .= $lodash->unit ( 'border-bottom-color', '.jpb-nav > li.active > a', 'data.active_tab_border_color', '', false );
		$output .= '<# } #>';

		// Custom
		$output .= '<# if (data.style == "custom") { #>';
		$output .= '<# if (!_.isEmpty(data.nav_border)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li > a {border-style: solid;}';
		$output .= $lodash->unit ( 'border-width', '.jpb-nav > li > a', 'data.nav_border' );
		$output .= $lodash->unit ( 'border-color', '.jpb-nav > li > a', 'data.nav_border_color', '', false );
		$output .= $lodash->unit ( 'border-color', '.jpb-nav > li:hover > a', 'data.hover_tab_border_color', '', false );
		$output .= $lodash->unit ( 'border-color', '.jpb-nav > li.active > a', 'data.active_tab_border_color', '', false );
		$output .= '<# } #>';

		$output .= $lodash->unit ( "max-width", ".jpb-tab-content.jpb-tab-custom-content", "data.content_width", '%' );

		$output .= '<# Object.keys(mediaQueries).forEach(size => { #>';
		$output .= '<# if(_.isObject(navPosition)) { #>';
		$output .= '{{ mediaQueries[size] }} {
						#jpb-addon-{{data.id}} .jpb-tab-content {';
		$output .= '<# if (navPosition[size] === "nav-top") { #>';
		$output .= 'padding: {{ data.nav_gutter?.[size] || 0 }}px 0 0 0 !important;';

		$output .= '<# } else if (navPosition[size] === "nav-right") { #>';
		$output .= 'padding: 0 {{ data.nav_gutter?.[size] || 0 }}px 0 0 !important;';
		$output .= '<# } else if (navPosition[size] === "nav-bottom") { #>';
		$output .= 'padding: 0 0 {{ data.nav_gutter?.[size] || 0 }}px 0 !important;';
		$output .= '<# } else { #>';
		$output .= 'padding: 0 0 0 {{ data.nav_gutter?.[size] || 0 }}px !important; <# } #>  }';

		$output .= '<# if (navPosition[size] === "nav-top" && (_.isObject(data?.nav_justified) ? data?.nav_justified[size] : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100% !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition[size] === "nav-bottom" && (_.isObject(data?.nav_justified) ? data?.nav_justified[size] : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100% !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition[size] === "nav-top" && (_.isObject(data?.nav_justified) ? !data?.nav_justified[size] : !data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav {width: fit-content !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition[size] === "nav-bottom" && (_.isObject(data?.nav_justified) ? !data?.nav_justified[size] : !data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav {width: fit-content !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition[size] === "nav-left" && data?.style === "custom") { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav { width: {{ _.isObject(data.nav_width) ? data.nav_width[size] : data.nav_width }}% !important;}';
		$output .= '<# } #>';

		$output .= '<# if (navPosition[size] === "nav-right" && data?.style === "custom") { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav { width: {{ _.isObject(data.nav_width) ? data.nav_width[size] : data.nav_width }}% !important;}';
		$output .= '<# }  #> }';

		$output .= '<# } else { #>';

		$output .= '{{ mediaQueries[size] }} {
			#jpb-addon-{{data.id}} .jpb-tab-content {';
		$output .= '<# if (navPosition === "nav-top") { #>';
		$output .= 'padding: {{ data.nav_gutter }}px 0 0 0 !important;';

		$output .= '<# } else if (navPosition === "nav-right") { #>';
		$output .= 'padding: 0 {{ data.nav_gutter }}px 0 0 !important;';
		$output .= '<# } else if (navPosition === "nav-bottom") { #>';
		$output .= 'padding: 0 0 {{ data.nav_gutter }}px 0 !important;';
		$output .= '<# } else { #>';
		$output .= 'padding: 0 0 0 {{ data.nav_gutter }}px !important; } }';

		$output .= '<# if (navPosition[size] === "nav-top" && (_.isObject(data?.nav_justified) ? data?.nav_justified[size] : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100% !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition[size] === "nav-bottom" && (_.isObject(data?.nav_justified) ? data?.nav_justified[size] : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100% !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition[size] === "nav-top" && (_.isObject(data?.nav_justified) ? !data?.nav_justified[size] : !data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav {width: fit-content !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition[size] === "nav-bottom" && (_.isObject(data?.nav_justified) ? !data?.nav_justified[size] : !data?.nav_justified)) { #>';
		$output .= ' #jpb-addon-{{ data.id }} .jpb-nav {width: fit-content !important;}';
		$output .= '<# }  #>';

		$output .= '<# if (data?.style === "custom" && (navPosition[size] === "nav-left" || navPosition[size] === "nav-right")) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav { width: {{ _.isObject(data.nav_width) ? data.nav_width[size] : data.nav_width }}% !important;}';
		$output .= '<# } } #>';

		$output .= '<# } }) #>';
		$output .= '<# } #>';

		// for default device
		$output .= '<# if(_.isObject(navPosition)) { #>';
		$output .= '#jpb-addon-{{data.id}} .jpb-tab-content {';
		$output .= '<# if (navPosition?.xl === "nav-top") { #>';
		$output .= 'padding: {{ data.nav_gutter?.xl || 0 }}px 0 0 0;';
		$output .= '<# } else if (navPosition?.xl === "nav-right") { #>';
		$output .= 'padding: 0 {{ data.nav_gutter?.xl || 0 }}px 0 0;';
		$output .= '<# } else if (navPosition?.xl === "nav-bottom") { #>';
		$output .= 'padding: 0 0 {{ data.nav_gutter?.xl || 0 }}px 0;';
		$output .= '<# } else if(navPosition?.xl === "nav-left") { #>';
		$output .= 'padding: 0 0 0 {{ data.nav_gutter?.xl || 0 }}px; <# } #> }';

		$output .= '<# if (navPosition?.xl === "nav-top" && (_.isObject(data?.nav_justified) ? data?.nav_justified?.xl : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100%;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition?.xl === "nav-bottom" && (_.isObject(data?.nav_justified) ? data?.nav_justified?.xl : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100%;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition?.xl === "nav-top" && (_.isObject(data?.nav_justified) ? !data?.nav_justified?.xl : !data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav {width: fit-content;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition?.xl === "nav-bottom" && (_.isObject(data?.nav_justified) ? !data?.nav_justified?.xl : !data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav {width: fit-content;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition?.xl === "nav-left" && data?.style === "custom") { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav { width: {{ _.isObject(data.nav_width) ? data.nav_width?.xl : data.nav_width }}%;}';
		$output .= '<# } #>';

		$output .= '<# if (navPosition?.xl === "nav-right" && data?.style === "custom") { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav { width: {{ _.isObject(data.nav_width) ? data.nav_width?.xl : data.nav_width }}%;}';
		$output .= '<# }  #> }';

		$output .= '<# } else { #>';

		$output .= '#jpb-addon-{{data.id}} .jpb-tab-content {';

		$output .= '<# if (navPosition === "nav-top") { #>';
		$output .= 'padding-top: {{ data.nav_gutter }}px;';

		$output .= '<# } else if (navPosition === "nav-right") { #>';
		$output .= 'padding-right: {{ data.nav_gutter }}px;';
		$output .= '<# } else if (navPosition === "nav-bottom") { #>';
		$output .= 'padding-bottom: {{ data.nav_gutter }}px;';
		$output .= '<# } else { #>';
		$output .= 'padding-left: {{ data.nav_gutter }}px; }';

		$output .= '<# if (navPosition?.xl === "nav-top" && (_.isObject(data?.nav_justified) ? data?.nav_justified?.xl : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100%;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition?.xl === "nav-bottom" && (_.isObject(data?.nav_justified) ? data?.nav_justified?.xl : data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav > li {flex: 1 1 auto;} #jpb-addon-{{ data.id }} .jpb-nav {width: 100%;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition?.xl === "nav-top" && (_.isObject(data?.nav_justified) ? !data?.nav_justified?.xl : !data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav {width: fit-content;}';
		$output .= '<# }  #>';

		$output .= '<# if (navPosition?.xl === "nav-bottom" && (_.isObject(data?.nav_justified) ? !data?.nav_justified?.xl : !data?.nav_justified)) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav {width: fit-content;}';
		$output .= '<# }  #>';

		$output .= '<# if (data?.style === "custom" && (navPosition?.xl === "nav-left" || navPosition?.xl === "nav-right")) { #>';
		$output .= '#jpb-addon-{{ data.id }} .jpb-nav { width: {{ _.isObject(data.nav_width) ? data.nav_width?.xl : data.nav_width }}%;}';
		$output .= '<# } } #>';

		$output .= '<# } #>';

		// Nav
		$navItemTypographyFallbacks = [ 
				'font' => 'data.nav_font_family',
				'size' => 'data.nav_fontsize',
				'line_height' => 'data.nav_lineheight',
				'uppercase' => 'data.nav_font_style?.uppercase',
				'italic' => 'data.nav_font_style?.italic',
				'underline' => 'data.nav_font_style?.underline',
				'weight' => 'data.nav_font_style?.weight'
		];
		$output .= $lodash->spacing ( 'padding', '.jpb-nav > li > a', 'data.nav_padding' );
		$output .= $lodash->spacing ( 'padding', '.jpb-nav > li', 'data.nav_padding' );
		$output .= $lodash->spacing ( 'margin', '.jpb-nav > li > a', 'data.nav_margin' );
		$output .= $lodash->spacing ( 'margin', '.jpb-nav > li', 'data.nav_margin' );
		// $output .= $lodash->flexAlignment('.jpb-nav > li > a', 'data.nav_text_align');
		$output .= $lodash->typography ( '.jpb-nav > li > a', 'data.nav_typography', $navItemTypographyFallbacks );

		// Media
		$output .= $lodash->color ( 'color', '.jpb-nav > li > a .jpb-tab-icon', 'data.icon_color' );
		$output .= $lodash->color ( 'color', '.jpb-nav > li:hover > a .jpb-tab-icon', 'data.icon_color_hover' );
		$output .= $lodash->color ( 'color', '.jpb-nav > li.active > a .jpb-tab-icon', 'data.icon_color_active' );

		$output .= $lodash->unit ( 'font-size', '.jpb-tab-icon', 'data.icon_fontsize', 'px' );
		$output .= $lodash->spacing ( 'margin', '.jpb-tab-icon', 'data.icon_margin' );

		$output .= $lodash->unit ( 'width', '.jpb-tab-image', 'data.image_width', 'px' );
		$output .= $lodash->unit ( 'height', '.jpb-tab-image', 'data.image_height', 'px' );
		$output .= $lodash->spacing ( 'margin', '.jpb-tab-image', 'data.image_margin' );

		$output .= $lodash->spacing ( 'margin', '.jpb-tab-image', 'data.image_margin' );

		// Content
		$contentTypographyFallbacks = [ 
				'font' => 'data.content_font_family',
				'size' => 'data.content_fontsize',
				'line_height' => 'data.content_lineheight',
				'uppercase' => 'data.content_font_style?.uppercase',
				'italic' => 'data.content_font_style?.italic',
				'underline' => 'data.content_font_style?.underline',
				'weight' => 'data.content_font_style?.weight'
		];
		$output .= $lodash->typography ( '.jpb-tab-content', 'data.content_typography', $contentTypographyFallbacks );

		$output .= $lodash->spacing ( 'padding', '.jpb-tab-pane', 'data.content_padding' );
		$output .= $lodash->spacing ( 'margin', '.jpb-tab-pane', 'data.content_margin' );
		$output .= $lodash->unit ( 'border-radius', '.jpb-tab-pane', 'data.content_border_radius', 'px' );

		// box shadow
		$output .= '<# if (data.style == "custom") { #>';
		$output .= $lodash->boxShadow ( '.jpb-nav > li > a', 'box_shadow' );
		$output .= $lodash->boxShadow ( '.jpb-tab-pane', 'box_shadow' );
		$output .= '<# } #>';
		$output .= $lodash->generateTransformCss ( '', 'data.transform' );
		$output .= '
		</style>
		';

		return $output;
	}
}
