<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
use Joomla\CMS\Uri\Uri;

// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );
class JpagebuilderAddonGallery extends JpagebuilderAddons {
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
		$heading_selector = (isset ( $settings->heading_selector ) && $settings->heading_selector) ? $settings->heading_selector : 'h3';
		$item_alignment = (isset ( $settings->item_alignment ) && $settings->item_alignment) ? $settings->item_alignment : '';
		$item_alignment = JpagebuilderAddonUtils::parseDeviceData ( $item_alignment, JpagebuilderBase::$defaultDevice );

		$output = '<div class="jpb-addon jpb-addon-gallery ' . $class . '">';
		$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>' : '';
		$output .= '<div class="jpb-addon-content">';
		$output .= '<ul class="jpb-gallery clearfix gallery-item-' . $item_alignment . '">';

		if (isset ( $settings->jp_gallery_item ) && count ( ( array ) $settings->jp_gallery_item )) {
			foreach ( $settings->jp_gallery_item as $key => $value ) {
				$thumb_img = isset ( $value->thumb ) && $value->thumb ? $value->thumb : '';
				$thumb_src = isset ( $thumb_img->src ) ? $thumb_img->src : $thumb_img;
				$alt_text_fallback = isset ( $value->title ) ? $value->title : '';
				$alt_text = isset ( $thumb_img->alt ) ? $thumb_img->alt : $alt_text_fallback;
				$thumb_width = isset ( $thumb_img->width ) && $thumb_img->width ? $thumb_img->width : '';
				$thumb_height = isset ( $thumb_img->height ) && $thumb_img->height ? $thumb_img->height : '';

				$full_img = isset ( $value->full ) && $value->full ? $value->full : '';
				$full_src = isset ( $full_img->src ) ? $full_img->src : $full_img;

				if ($thumb_src) {
					if (strpos ( $thumb_src, "http://" ) !== false || strpos ( $thumb_src, "https://" ) !== false) {
						$thumb_src = $thumb_src;
					} else {
						$thumb_src = Uri::base ( true ) . '/' . $thumb_src;
					}

					$placeholder = $thumb_src == '' ? false : $this->get_image_placeholder ( $thumb_src );

					$output .= '<li>';
					$output .= ($full_src) ? '<a href="' . $full_src . '" class="jpb-gallery-btn">' : '';
					$output .= '<img class="jpb-img-responsive' . ($placeholder ? ' jpb-element-lazy' : '') . '" src="' . ($placeholder ? $placeholder : $thumb_src) . '" alt="' . $alt_text . '" ' . ($placeholder ? 'data-large="' . $thumb_src . '"' : '') . ' ' . ($thumb_width ? 'width="' . $thumb_width . '"' : '') . ' ' . ($thumb_height ? 'height="' . $thumb_height . '"' : '') . ' loading="lazy">';
					$output .= ($full_src) ? '</a>' : '';
					$output .= '</li>';
				}
			}
		}

		$output .= '</ul>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Attach inline stylesheet.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function stylesheets() {
		return array (
				'components/com_jpagebuilder/assets/css/magnific-popup.css'
		);
	}

	/**
	 * Attach external scripts.
	 *
	 * @return array
	 * @since 1.0.0
	 */
	public function scripts() {
		return array (
				'components/com_jpagebuilder/assets/js/jquery.magnific-popup.min.js'
		);
	}

	/**
	 * Attach inline JavaScript.
	 *
	 * @return string The JS string.
	 * @since 1.0.0
	 */
	public function js() {
		$addon_id = '#jpb-addon-' . $this->addon->id;
		$js = 'jQuery(function($){
			$("' . $addon_id . ' ul li").magnificPopup({
				delegate: "a",
				type: "image",
				mainClass: "mfp-no-margins mfp-with-zoom",
				gallery:{
					enabled:true
				},
				image: {
					verticalFit: true
				},
				zoom: {
					enabled: true,
					duration: 300
				}
			});
		})';

		return $js;
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

		$css = '';

		$border_radius = (isset ( $settings->border_radius ) && $settings->border_radius) ? $settings->border_radius : 0;

		if ($border_radius) {
			$border_radius = explode ( " ", $settings->border_radius );
		}

		if (is_array ( $border_radius ) && (count ( $border_radius ) > 2)) {
			$galleryImageStyle = $cssHelper->generateStyle ( '.jpb-gallery img', $settings, [ 
					'width' => 'width',
					'height' => 'height',
					'border_radius' => 'border-radius'
			], [ 
					'border_radius' => false
			], [ 
					'border_radius' => 'spacing'
			] );
		} else {
			$galleryImageStyle = $cssHelper->generateStyle ( '.jpb-gallery img', $settings, [ 
					'width' => 'width',
					'height' => 'height',
					'border_radius' => 'border-radius'
			] );
		}

		$galleryStyle = $cssHelper->generateStyle ( '.jpb-gallery', $settings, [ 
				'item_gap' => 'margin: -%s',
				'item_alignment' => 'justify-content'
		], [ 
				'item_alignment' => false
		] );
		$galleryItemStyle = $cssHelper->generateStyle ( '.jpb-gallery li', $settings, [ 
				'item_gap' => 'margin'
		] );
		$transformCss = $cssHelper->generateTransformStyle ( '.jpb-gallery', $settings, 'transform' );

		$css .= $galleryStyle;
		$css .= $galleryItemStyle;
		$css .= $galleryImageStyle;
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

		$output = '<style type="text/css">';

		$output .= $lodash->alignment ( 'justify-content', '.jpb-gallery', 'data.item_alignment' );

		$output .= $lodash->unit ( 'width', '.jpb-gallery img', 'data.width', 'px' );
		$output .= $lodash->unit ( 'height', '.jpb-gallery img', 'data.height', 'px' );

		$output .= '<# if((data.border_radius + "").split(" ").length < 2) { #>';
		$output .= $lodash->unit ( 'border-radius', '.jpb-gallery img', 'data.border_radius', 'px' );
		$output .= '<# } else { #>';
		$output .= '.jpb-gallery img {
			{{window.getSplitRadius(data.border_radius)}}	
		}';
		$output .= '<# } #>';

		$output .= $lodash->unit ( 'margin', '.jpb-gallery li', 'data.item_gap', 'px' );
		$output .= $lodash->unit ( 'margin', '.jpb-gallery', 'data.item_gap', 'px', true, '-' );

		// Title
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

		$output .= $lodash->typography ( '.jpb-addon-title', 'data.title_typography', $titleTypographyFallbacks );
		$output .= $lodash->unit ( 'margin-top', '.jpb-addon-title', 'data.title_margin_top', 'px' );
		$output .= $lodash->unit ( 'margin-bottom', '.jpb-addon-title', 'data.title_margin_bottom', 'px' );
		$output .= $lodash->generateTransformCss ( '.jpb-gallery', 'data.transform' );

		$output .= '
        </style>
		<div class="jpb-addon jpb-addon-gallery {{ data.class }}">
			<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{ data.title }}</{{ data.heading_selector }}><# } #>
			<div class="jpb-addon-content">
                <ul class="jpb-gallery clearfix gallery-item-{{data.item_alignment}}">
                
                <#
                _.each(data.jp_gallery_item, function (value, key) {
                    var thumbImg = {}
                    var fullImg = {}
                    if (typeof value.thumb !== "undefined" && typeof value.thumb.src !== "undefined") {
                        thumbImg = value.thumb
                    } else {
                        thumbImg = {src: value.thumb}
                    }
                    if (typeof value.full !== "undefined" && typeof value.full.src !== "undefined") {
                        fullImg = value.full
                    } else {
                        fullImg = {src: value.full}
                    }
					if(thumbImg.src) {
                #>
						<li>
						<# if(fullImg.src && fullImg.src.indexOf("http://") == -1 && fullImg.src.indexOf("https://") == -1){ #>
							<a href=\'{{ pagebuilder_base + fullImg.src }}\' class="jpb-gallery-btn">
						<# } else if(fullImg.src){ #>
							<a href=\'{{ fullImg.src }}\' class="jpb-gallery-btn">
                        <# }
                        if(thumbImg.src && thumbImg.src.indexOf("http://") == -1 && thumbImg.src.indexOf("https://") == -1){
                        #>
								<img class="jpb-img-responsive" src=\'{{ pagebuilder_base + thumbImg.src }}\' alt="{{ value.thumb.alt ?? value.title }}">
							<# } else if(thumbImg.src){ #>
                                <img class="jpb-img-responsive" src=\'{{ thumbImg.src }}\' alt="{{ value.thumb.alt ?? value.title }}">
							<# } #>
						<# if(fullImg.src){ #>
							</a>
						<# } #>
						</li>
					<# } #>
				<# }); #>
				</ul>
			</div>
		</div>
		';

		return $output;
	}
}
