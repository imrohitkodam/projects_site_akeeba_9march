<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Component\ComponentHelper;

$options = $displayData ['options'];

$doc = Factory::getApplication ()->getDocument ();
$wa = $doc->getWebAssetManager ();

// Image lazy load
$config = ComponentHelper::getParams ( 'com_jpagebuilder' );
$lazyload = $config->get ( 'lazyloadimg', '0' );

$custom_class = (isset ( $options->class ) && ($options->class)) ? ' ' . $options->class : '';
$row_id = (isset ( $options->id ) && $options->id) ? $options->id : 'section-id-' . $options->dynamicId;
$fluid_row = (isset ( $options->fullscreen ) && $options->fullscreen) ? $options->fullscreen : 0;
$stretch_section = (isset ( $options->stretch_section ) && $options->stretch_section) ? $options->stretch_section : 0;
$row_class = (isset ( $options->no_gutter ) && $options->no_gutter) ? ' jpb-no-gutter' : '';
$row_class .= isset ( $options->isNestedRow ) && $options->isNestedRow ? ' jpb-nested-row' : '';

$cssHelper = new JpagebuilderCSSHelper ( $row_id );

if ($lazyload && isset ( $options->background_type ) && $options->background_type) {
	if ($options->background_type === 'image' || $options->background_type === 'video') {
		$custom_class .= ' jpb-element-lazy';
	}
}

if (isset ( $options->columns_content_alignment ) && ($options->columns_content_alignment === 'top' || $options->columns_content_alignment === 'start')) {
	$row_class .= (isset ( $options->columns_align_center ) && $options->columns_align_center) ? ' jpb-align-top' : '';
} elseif (isset ( $options->columns_content_alignment ) && ($options->columns_content_alignment === 'bottom' || $options->columns_content_alignment === 'end')) {
	$row_class .= (isset ( $options->columns_align_center ) && $options->columns_align_center) ? ' jpb-align-bottom' : '';
} else {
	$row_class .= (isset ( $options->columns_align_center ) && $options->columns_align_center) ? ' jpb-align-center' : '';
}

$external_video = (isset ( $options->background_external_video ) && $options->background_external_video) ? $options->background_external_video : '';
$background_parallax = (isset ( $options->background_parallax ) && $options->background_parallax) ? ( int ) $options->background_parallax : 0;

$sec_cont_center = '';

if (isset ( $options->columns_content_alignment ) && ($options->columns_content_alignment === 'top' || $options->columns_content_alignment === 'start')) {
	$sec_cont_center = (isset ( $options->columns_align_center ) && $options->columns_align_center) ? ' jpb-section-content-top' : '';
} elseif (isset ( $options->columns_content_alignment ) && ($options->columns_content_alignment === 'bottom' || $options->columns_content_alignment === 'end')) {
	$sec_cont_center = (isset ( $options->columns_align_center ) && $options->columns_align_center) ? ' jpb-section-content-bottom' : '';
} else {
	$sec_cont_center = (isset ( $options->columns_align_center ) && $options->columns_align_center) ? ' jpb-section-content-center' : '';
}

// Visibility
if (isset ( $options->hidden_xl ) && $options->hidden_xl) {
	$custom_class .= ' jpb-hidden-xl';
}

if (isset ( $options->hidden_lg ) && $options->hidden_lg) {
	$custom_class .= ' jpb-hidden-lg';
}

if (isset ( $options->hidden_md ) && $options->hidden_md) {
	$custom_class .= ' jpb-hidden-md';
}

if (isset ( $options->hidden_sm ) && $options->hidden_sm) {
	$custom_class .= ' jpb-hidden-sm';
}

if (isset ( $options->hidden_xs ) && $options->hidden_xs) {
	$custom_class .= ' jpb-hidden-xs';
}

$addon_attr = '';

// Animation
$hasEnableAnimationProperty = property_exists ( $options, 'enable_animation' );
$isAnimationEnabled = false;

if ($hasEnableAnimationProperty) {
	$isAnimationEnabled = ! empty ( $options->enable_animation ) && ! empty ( $options->animation );
} else {
	$isAnimationEnabled = ! empty ( $options->animation );
}

if ($isAnimationEnabled) {
	$custom_class .= ' jpb-wow ' . $options->animation;

	if (! empty ( $options->animationduration )) {
		$addon_attr .= ' data-jpb-wow-duration="' . $options->animationduration . 'ms"';
	}

	if (! empty ( $options->animationdelay )) {
		$addon_attr .= ' data-jpb-wow-delay="' . $options->animationdelay . 'ms"';
	}
}

if (! empty ( $external_video )) {
	$custom_class .= ' jpb-row-have-ext-bg';
}

// Top Shape CSS
$cssTop = "";
$cssTop .= $cssHelper->generateStyle ( ".jpb-shape-container.jpb-top-shape > svg", $options, [ 
		'shape_width' => [ 
				'width',
				'max-width'
		],
		'shape_height' => 'height'
], [ 
		'shape_width' => '%',
		'shape_height' => 'px'
] );

if (isset ( $options->show_top_shape ) && $options->show_top_shape && $cssTop) {
	$wa->addInlineStyle ( $cssTop );
}

$top_shape_path_css = (isset ( $options->shape_color ) && ! empty ( $options->shape_color )) ? 'fill:' . $options->shape_color . ';' : '';

if (isset ( $options->show_top_shape ) && $options->show_top_shape && ! empty ( $top_shape_path_css )) {
	$wa->addInlineStyle ( '#' . $row_id . ' .jpb-shape-container.jpb-top-shape > svg path, #' . $row_id . ' .jpb-shape-container.jpb-top-shape > svg polygon{' . $top_shape_path_css . '}' );
}

// Bottom Shape CSS
$css = $cssHelper->generateStyle ( ".jpb-shape-container.jpb-bottom-shape > svg", $options, [ 
		'bottom_shape_width' => [ 
				'width',
				'max-width'
		],
		'bottom_shape_height' => 'height'
], [ 
		'bottom_shape_width' => '%',
		'bottom_shape_height' => 'px'
] );

if (isset ( $options->show_bottom_shape ) && $options->show_bottom_shape && $css) {
	$wa->addInlineStyle ( $css );
}

$bottom_shape_path_css = (isset ( $options->bottom_shape_color ) && ! empty ( $options->bottom_shape_color )) ? 'fill:' . $options->bottom_shape_color . ';' : '';

if (isset ( $options->show_bottom_shape ) && $options->show_bottom_shape && ! empty ( $bottom_shape_path_css )) {
	$wa->addInlineStyle ( '#' . $row_id . ' .jpb-shape-container.jpb-bottom-shape > svg path, #' . $row_id . ' .jpb-shape-container.jpb-bottom-shape > svg polygon{' . $bottom_shape_path_css . '}' );
}

// Video
$video_loop = '';

if (isset ( $options->video_loop ) && $options->video_loop == true) {
	$video_loop = 'loop';
} else {
	$video_loop = '';
}

$video_params = '';
$video_poster = '';
$mp4_url = '';
$ogv_url = '';

$external_background_video = 0;

if (isset ( $options->external_background_video ) && $options->external_background_video) {
	$external_background_video = $options->external_background_video;
}

$background_image = (isset ( $options->background_image ) && $options->background_image) ? $options->background_image : '';
$background_image_src = isset ( $background_image->src ) ? $background_image->src : $background_image;

$background_video_mp4 = (isset ( $options->background_video_mp4 ) && $options->background_video_mp4) ? $options->background_video_mp4 : '';
$background_video_mp4_src = isset ( $background_video_mp4->src ) ? $background_video_mp4->src : $background_video_mp4;

$background_video_ogv = (isset ( $options->background_video_ogv ) && $options->background_video_ogv) ? $options->background_video_ogv : '';
$background_video_ogv_src = isset ( $background_video_ogv->src ) ? $background_video_ogv->src : $background_video_ogv;

if (isset ( $options->background_type )) {
	if ($options->background_type === 'video' && ! $external_background_video) {
		if ($background_image_src) {
			if (strpos ( $background_image_src, "http://" ) !== false || strpos ( $background_image_src, "https://" ) !== false) {
				$video_poster .= $background_image_src;
				$video_params .= $video_loop;
			} else {
				$video_poster .= Uri::base ( true ) . '/' . $background_image_src;
			}
		}

		if ($background_video_mp4_src) {
			$mp4_parsed = parse_url ( $background_video_mp4_src );
			$mp4_url = (isset ( $mp4_parsed ['host'] ) && $mp4_parsed ['host']) ? $background_video_mp4_src : Uri::base ( true ) . '/' . $background_video_mp4_src;
		}

		if ($background_video_ogv_src) {
			$ogv_parsed = parse_url ( $background_video_ogv_src );
			$ogv_url = (isset ( $ogv_parsed ['host'] ) && $ogv_parsed ['host']) ? $background_video_ogv_src : Uri::base ( true ) . '/' . $background_video_ogv_src;
		}
	}
} else {
	if (isset ( $options->background_video ) && $options->background_video && ! $external_background_video) {
		if ($background_image_src) {
			if (strpos ( $background_image_src, "http://" ) !== false || strpos ( $background_image_src, "https://" ) !== false) {
				$video_poster .= $background_image_src;
				$video_params .= $video_loop;
			} else {
				$video_poster .= Uri::base ( true ) . '/' . $background_image_src;
				$video_params .= $video_loop;
			}
		}

		if (isset ( $options->background_video_mp4 ) && $options->background_video_mp4) {
			$mp4_parsed = parse_url ( $options->background_video_mp4 );
			$mp4_url = (isset ( $mp4_parsed ['host'] ) && $mp4_parsed ['host']) ? $options->background_video_mp4 : Uri::base ( true ) . '/' . $options->background_video_mp4;
		}

		if (isset ( $options->background_video_ogv ) && $options->background_video_ogv) {
			$ogv_parsed = parse_url ( $options->background_video_ogv );
			$ogv_url = (isset ( $ogv_parsed ['host'] ) && $ogv_parsed ['host']) ? $options->background_video_ogv : Uri::base ( true ) . '/' . $options->background_video_ogv;
		}
	}
}

$parallax_params = '';

if ($background_parallax && $background_image_src) {
	$parallax_params = ' data-jpb-parallax="on"';
}

$video_content = '';

if (isset ( $options->background_type )) {
	if (! empty ( $external_video ) && $options->external_background_video && $options->background_type === 'video') {
		$video = parse_url ( $external_video );
		$src = '';
		$vidId = '';

		switch ($video ['host']) {
			case 'youtu.be' :
				$id = trim ( $video ['path'], '/' );
				$src = '//www.youtube.com/embed/' . $id . '?playlist=' . $id . '&iv_load_policy=3&enablejsapi=1&disablekb=1&autoplay=1&controls=0&showinfo=0&rel=0&loop=1&wmode=transparent&widgetid=1&mute=1';
				break;

			case 'www.youtube.com' :
			case 'youtube.com' :
				parse_str ( $video ['query'], $query );
				$id = $query ['v'];
				$src = '//www.youtube.com/embed/' . $id . '?playlist=' . $id . '&iv_load_policy=3&enablejsapi=1&disablekb=1&autoplay=1&controls=0&showinfo=0&rel=0&loop=1&wmode=transparent&widgetid=1&mute=1';
				break;
			case 'vimeo.com' :
			case 'www.vimeo.com' :
				$id = trim ( $video ['path'], '/' );
				$src = "//player.vimeo.com/video/{$id}?background=1&autoplay=1&loop=1&title=0&byline=0&portrait=0";
		}

		$video_content .= '<div class="jpb-youtube-video-bg hidden"><iframe class="jpb-youtube-iframe" ' . ($lazyload ? 'data-src="' . $src . '"' : 'src="' . $src . '"') . ' frameborder="0" allowfullscreen></iframe></div>';
	}
} else {
	if (! empty ( $external_video ) && $options->external_background_video && $options->background_video) {
		$video = parse_url ( $external_video );
		$src = '';

		switch ($video ['host']) {
			case 'youtu.be' :
				$id = trim ( $video ['path'], '/' );
				$src = '//www.youtube.com/embed/' . $id . '?playlist=' . $id . '&iv_load_policy=3&enablejsapi=1&disablekb=1&autoplay=1&controls=0&showinfo=0&rel=0&loop=1&wmode=transparent&widgetid=1&mute=1';
				break;

			case 'www.youtube.com' :
			case 'youtube.com' :
				parse_str ( $video ['query'], $query );
				$id = $query ['v'];
				$src = '//www.youtube.com/embed/' . $id . '?playlist=' . $id . '&iv_load_policy=3&enablejsapi=1&disablekb=1&autoplay=1&controls=0&showinfo=0&rel=0&loop=1&wmode=transparent&widgetid=1&mute=1';
				break;
			case 'vimeo.com' :
			case 'www.vimeo.com' :
				$id = trim ( $video ['path'], '/' );
				$src = "//player.vimeo.com/video/{$id}?background=1&autoplay=1&loop=1&title=0&byline=0&portrait=0";
				break;
		}

		$video_content .= '<div class="jpb-youtube-video-bg hidden"><iframe class="jpb-youtube-iframe" ' . ($lazyload ? 'data-src="' . $src . '"' : 'src="' . $src . '"') . ' frameborder="0" allowfullscreen></iframe></div>';
	}
}

$shape_content = '';

if (isset ( $options->show_top_shape ) && $options->show_top_shape && isset ( $options->shape_name ) && $options->shape_name) {
	$shape_class = '';
	$shape_code = '';
	$inversionNotAllowed = [ 
			'clouds-opacity',
			'slope-opacity',
			'waves3-opacity',
			'paper-torn',
			'hill-wave',
			'line-wave',
			'swirl',
			'wavy-opacity',
			'zigzag-sharp',
			'brushed'
	];

	if (in_array ( $options->shape_name, $inversionNotAllowed )) {
		$shape_invert = 0;
	} else {
		$shape_invert = isset ( $options->shape_invert ) && $options->shape_invert ? 1 : 0;
	}

	if (class_exists ( 'JpagebuilderHelperSite' )) {
		$shape_code = JpagebuilderHelperSite::getSvgShapeCode ( $options->shape_name, $shape_invert );
	}

	if (isset ( $options->shape_flip ) && $options->shape_flip) {
		$shape_class .= ' jpb-shape-flip';
	}

	if (! empty ( $shape_invert ) && ! empty ( $shape_code )) {
		$shape_class .= ' jpb-shape-invert';
	}

	if (isset ( $options->shape_to_front ) && $options->shape_to_front) {
		$shape_class .= ' jpb-shape-to-front';
	}

	$shape_content .= '<div class="jpb-shape-container jpb-top-shape ' . $shape_class . '">';
	$shape_content .= $shape_code;

	$shape_content .= '</div>';
}

if (isset ( $options->show_bottom_shape ) && $options->show_bottom_shape && isset ( $options->bottom_shape_name ) && $options->bottom_shape_name) {
	$bottom_shape_class = '';
	$bottom_shape_code = '';
	$inversionNotAllowed = [ 
			'clouds-opacity',
			'slope-opacity',
			'waves3-opacity',
			'paper-torn',
			'hill-wave',
			'line-wave',
			'swirl',
			'wavy-opacity',
			'zigzag-sharp',
			'brushed'
	];

	if (isset ( $options->shape_name ) && in_array ( $options->shape_name, $inversionNotAllowed )) {
		$bottom_shape_invert = 0;
	} else {
		$bottom_shape_invert = isset ( $options->bottom_shape_invert ) && $options->bottom_shape_invert ? 1 : 0;
	}

	if (class_exists ( 'JpagebuilderHelperSite' ) && isset ( $options->bottom_shape_name )) {
		$bottom_shape_code = JpagebuilderHelperSite::getSvgShapeCode ( $options->bottom_shape_name, $bottom_shape_invert );
	}

	if (isset ( $options->bottom_shape_flip ) && $options->bottom_shape_flip) {
		$bottom_shape_class .= ' jpb-shape-flip';
	}

	if (! empty ( $bottom_shape_invert ) && ! empty ( $bottom_shape_code )) {
		$bottom_shape_class .= ' jpb-shape-invert';
	}

	if (isset ( $options->bottom_shape_to_front ) && $options->bottom_shape_to_front) {
		$bottom_shape_class .= ' jpb-shape-to-front';
	}

	$shape_content .= '<div class="jpb-shape-container jpb-bottom-shape ' . $bottom_shape_class . '">';
	$shape_content .= $bottom_shape_code;
	$shape_content .= '</div>';
}

$html = '';
$stretchSectionStyle = "max-width: 100vw;margin-right: calc(50% - 50vw);margin-left: calc(50% - 50vw);";

if (! $fluid_row) {
	$html .= '<section id="' . $row_id . '" class="jpb-section' . $custom_class . '' . $sec_cont_center . '" ' . $addon_attr . $parallax_params;
	if ($stretch_section) {
		$html .= ' style="' . $stretchSectionStyle . '"';
	}
	$html .= '>';

	if ($mp4_url || $ogv_url) {
		$html .= '<div class="jpb-section-background-video">';
		$html .= '<video class="section-bg-video" autoplay muted playsinline ' . $video_loop . '' . $video_params . '' . ($lazyload ? ' data-poster="' . $video_poster . '"' : ' poster="' . $video_poster . '"') . '>';

		if ($mp4_url) {
			$html .= '<source ' . ($lazyload ? 'data-large="' . $mp4_url . '"' : 'src="' . $mp4_url . '"') . ' type="video/mp4">';
		}

		if ($ogv_url) {
			$html .= '<source ' . ($lazyload ? 'data-large="' . $ogv_url . '"' : 'src="' . $ogv_url . '"') . ' type="video/ogg">';
		}

		$html .= '</video>';
		$html .= '</div>';
	}
	// When there was no gradient or pattern overlay after adding those option need Backward Compatiblity for pervious color overlay
	if (isset ( $options->overlay ) && $options->overlay) {
		$options->overlay_type = 'overlay_color';
	}

	if ($shape_content) {
		$html .= $shape_content;
	}

	if ($video_content) {
		$html .= $video_content;
	}

	if (isset ( $options->overlay_type ) && $options->overlay_type !== 'overlay_none') {
		$html .= '<div class="jpb-row-overlay"></div>';
	}

	$html .= '<div class="jpb-row-container">';
} else {
	$html .= '<div id="' . $row_id . '" class="jpb-section' . $custom_class . '' . $sec_cont_center . '" ' . $addon_attr . $parallax_params;
	if ($stretch_section) {
		$html .= ' style="' . $stretchSectionStyle . '"';
	}
	$html .= '>';

	if ($mp4_url || $ogv_url) {
		$html .= '<div class="jpb-section-background-video">';
		$html .= '<video class="section-bg-video" autoplay muted playsinline ' . $video_loop . '' . $video_params . '' . ($lazyload ? ' data-poster="' . $video_poster . '"' : ' poster="' . $video_poster . '"') . '>';

		if ($mp4_url) {
			$html .= '<source ' . ($lazyload ? 'data-large="' . $mp4_url . '"' : 'src="' . $mp4_url . '"') . ' type="video/mp4">';
		}

		if ($ogv_url) {
			$html .= '<source ' . ($lazyload ? 'data-large="' . $ogv_url . '"' : 'src="' . $ogv_url . '"') . ' type="video/ogg">';
		}

		$html .= '</video>';
		$html .= '</div>';
	}

	// When there was no gradient or pattern overlay after adding those option need Backward Compatiblity for pervious color overlay
	if (isset ( $options->overlay ) && $options->overlay) {
		$options->overlay_type = 'overlay_color';
	}

	if ($shape_content) {
		$html .= $shape_content;
	}

	if ($video_content) {
		$html .= $video_content;
	}

	if (isset ( $options->overlay_type ) && $options->overlay_type !== 'overlay_none') {
		$html .= '<div class="jpb-row-overlay"></div>';
	}

	$html .= '<div class="jpb-container-inner">';
}

// Row Title
if ((isset ( $options->title ) && $options->title) || (isset ( $options->subtitle ) && $options->subtitle)) {
	$title_position = '';

	if (isset ( $options->title_position ) && $options->title_position) {
		$title_position = $options->title_position;
	}

	if ($fluid_row) {
		$html .= '<div class="jpb-container">';
	}

	$html .= '<div class="jpb-section-title ' . $title_position . '">';

	if (isset ( $options->title ) && $options->title) {
		$heading_selector = 'h2';

		if (isset ( $options->heading_selector ) && $options->heading_selector) {
			$heading_selector = $options->heading_selector;
		}

		$html .= '<' . $heading_selector . ' class="jpb-title-heading">' . $options->title . '</' . $heading_selector . '>';
	}

	if (isset ( $options->subtitle ) && $options->subtitle) {
		$html .= '<p class="jpb-title-subheading">' . $options->subtitle . '</p>';
	}

	$html .= '</div>';

	if ($fluid_row) {
		$html .= '</div>';
	}
}

$html .= '<div class="jpb-row' . $row_class . '">';

echo $html;
