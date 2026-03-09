<?php
/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// No direct access.
defined ( '_JEXEC' ) or die ( 'Restricted access' );

use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
class JpagebuilderAddonAdvancedvideo extends JpagebuilderAddons {
	public function render() {
		$settings = $this->addon->settings;
		$title_addon = (isset ( $settings->title_addon ) && $settings->title_addon) ? $settings->title_addon : '';
		$title_style = (isset ( $settings->title_heading_style ) && $settings->title_heading_style) ? ' uk-' . $settings->title_heading_style : '';
		$title_style .= (isset ( $settings->title_heading_color ) && $settings->title_heading_color) ? ' uk-' . $settings->title_heading_color : '';
		$title_style .= (isset ( $settings->title_heading_margin ) && $settings->title_heading_margin) ? ' ' . $settings->title_heading_margin : '';
		$title_heading_decoration = (isset ( $settings->title_heading_decoration ) && $settings->title_heading_decoration) ? ' ' . $settings->title_heading_decoration : '';
		$title_heading_selector = (isset ( $settings->title_heading_selector ) && $settings->title_heading_selector) ? $settings->title_heading_selector : 'h3';

		$general = '';
		$addon_margin = (isset ( $settings->addon_margin ) && $settings->addon_margin) ? $settings->addon_margin : '';
		$general .= ($addon_margin) ? ' uk-margin' . (($addon_margin == 'default') ? '' : '-' . $addon_margin) : '';
		$general .= (isset ( $settings->visibility ) && $settings->visibility) ? ' ' . $settings->visibility : '';
		$general .= (isset ( $settings->class ) && $settings->class) ? ' ' . $settings->class : '';

		$max_width_cfg = (isset ( $settings->addon_max_width ) && $settings->addon_max_width) ? ' uk-width-' . $settings->addon_max_width : '';
		$addon_max_width_breakpoint = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_breakpoint ) && $settings->addon_max_width_breakpoint) ? '@' . $settings->addon_max_width_breakpoint : '') : '';
		$max_width_cfg_alg = ($max_width_cfg) ? ((isset ( $settings->addon_max_width_alignment ) && $settings->addon_max_width_alignment) ? ' uk-margin-' . $settings->addon_max_width_alignment : '') : '';
		$max_width_cfg .= $addon_max_width_breakpoint . $max_width_cfg_alg;

		$text_alignment = (isset ( $settings->alignment ) && $settings->alignment) ? ' ' . $settings->alignment : '';
		$text_breakpoint = ($text_alignment) ? ((isset ( $settings->text_breakpoint ) && $settings->text_breakpoint) ? '@' . $settings->text_breakpoint : '') : '';
		$text_alignment_fallback = ($text_alignment && $text_breakpoint) ? ((isset ( $settings->text_alignment_fallback ) && $settings->text_alignment_fallback) ? ' uk-text-' . $settings->text_alignment_fallback : '') : '';
		$text_alignment .= $text_breakpoint . $text_alignment_fallback;

		// Options.
		$url = (isset ( $settings->url ) && $settings->url) ? $settings->url : '';
		$url_mp4 = (isset ( $settings->url_mp4 ) && $settings->url_mp4) ? $settings->url_mp4 : '';
		$url_modal_mp4 = (isset ( $settings->url_modal_mp4 ) && $settings->url_modal_mp4) ? $settings->url_modal_mp4 : '';
		$image = (isset ( $settings->image ) && $settings->image) ? $settings->image : '';
		$image_src = isset ( $image->src ) ? $image->src : $image;
		if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
			$image_src = $image_src;
		} elseif ($image_src) {
			$image_src = Uri::base ( true ) . '/' . $image_src;
		}
		$alt_text = (isset ( $settings->alt_text ) && $settings->alt_text) ? $settings->alt_text : '';

		$show_control = (isset ( $settings->show_control ) && $settings->show_control) ? ' controls' : '';
		$loop_video = (isset ( $settings->loop_video ) && $settings->loop_video) ? ' loop' : '';
		$mute_video = (isset ( $settings->mute_video ) && $settings->mute_video) ? ' muted' : '';
		$play_inline = (isset ( $settings->play_inline ) && $settings->play_inline) ? ' playsinline' : '';
		$lazy_load = (isset ( $settings->lazy_load ) && $settings->lazy_load) ? ' preload="none"' : '';

		$autoplay = (isset ( $settings->autoplay ) && $settings->autoplay) ? $settings->autoplay : '';

		$autoplay_init = '';

		if ($autoplay == 'true') {
			$autoplay_init = ' autoplay';
		} elseif ($autoplay == 'inview') {
			$autoplay_init = ' uk-video="inview"';
		}

		$video_source = (isset ( $settings->video_source ) && $settings->video_source) ? $settings->video_source : '';

		$box_shadow = (isset ( $settings->box_shadow ) && $settings->box_shadow) ? ' ' . $settings->box_shadow : '';
		$video_modal_width = (isset ( $settings->width ) && $settings->width) ? ' width="' . $settings->width . '"' : '';

		$video_width = (isset ( $settings->video_width ) && $settings->video_width) ? $settings->video_width : '';
		$video_height = (isset ( $settings->video_height ) && $settings->video_height) ? $settings->video_height : '';

		$video_width_init = (isset ( $settings->video_width ) && $settings->video_width) ? ' width="' . $video_width . '"' : '';
		$video_height_init = (isset ( $settings->video_height ) && $settings->video_height) ? ' height="' . $video_height . '"' : '';

		if ($video_source == 'youtube-vimeo') {
			$video_width_init .= ($video_height && empty ( $video_width )) ? ' width="' . round ( (( int ) $video_height * 16) / 9 ) . '"' : '';
			$video_height_init .= ($video_width && empty ( $video_height )) ? ' height="' . round ( (( int ) $video_width * 9) / 16 ) . '"' : '';
		}

		// Parallax Animation.
		$horizontal_start = (isset ( $settings->horizontal_start ) && $settings->horizontal_start) ? $settings->horizontal_start : '0';
		$horizontal_end = (isset ( $settings->horizontal_end ) && $settings->horizontal_end) ? $settings->horizontal_end : '0';
		$horizontal = (! empty ( $horizontal_start ) || ! empty ( $horizontal_end )) ? 'x: ' . $horizontal_start . ',' . $horizontal_end . ';' : '';

		$vertical_start = (isset ( $settings->vertical_start ) && $settings->vertical_start) ? $settings->vertical_start : '0';
		$vertical_end = (isset ( $settings->vertical_end ) && $settings->vertical_end) ? $settings->vertical_end : '0';
		$vertical = (! empty ( $vertical_start ) || ! empty ( $vertical_end )) ? 'y: ' . $vertical_start . ',' . $vertical_end . ';' : '';

		$scale_start = (isset ( $settings->scale_start ) && $settings->scale_start) ? (( int ) $settings->scale_start / 100) : 1;
		$scale_end = (isset ( $settings->scale_end ) && $settings->scale_end) ? (( int ) $settings->scale_end / 100) : 1;
		$scale = (! empty ( $scale_start ) || ! empty ( $scale_end )) ? 'scale: ' . $scale_start . ',' . $scale_end . ';' : '';

		$rotate_start = (isset ( $settings->rotate_start ) && $settings->rotate_start) ? $settings->rotate_start : '0';
		$rotate_end = (isset ( $settings->rotate_end ) && $settings->rotate_end) ? $settings->rotate_end : '0';
		$rotate = (! empty ( $rotate_start ) || ! empty ( $rotate_end )) ? 'rotate: ' . $rotate_start . ',' . $rotate_end . ';' : '';

		$opacity_start = (isset ( $settings->opacity_start ) && $settings->opacity_start) ? (( int ) $settings->opacity_start / 100) : 1;
		$opacity_end = (isset ( $settings->opacity_end ) && $settings->opacity_end) ? (( int ) $settings->opacity_end / 100) : 1;
		$opacity = (! empty ( $opacity_start ) || ! empty ( $opacity_end )) ? 'opacity: ' . $opacity_start . ',' . $opacity_end . ';' : '';

		$easing = (isset ( $settings->easing ) && $settings->easing) ? (( int ) $settings->easing / 100) : '';
		$easing_cls = (! empty ( $easing )) ? 'easing:' . $easing . ';' : '';

		$breakpoint = (isset ( $settings->breakpoint ) && $settings->breakpoint) ? $settings->breakpoint : '';
		$breakpoint_cls = (! empty ( $breakpoint )) ? 'media: @' . $breakpoint . ';' : '';

		$viewport = (isset ( $settings->viewport ) && $settings->viewport) ? (( int ) $settings->viewport / 100) : '';
		$viewport_cls = (! empty ( $viewport )) ? 'viewport:' . $viewport . ';' : '';

		$overlay_styles = (isset ( $settings->overlay_styles ) && $settings->overlay_styles) ? ' uk-' . $settings->overlay_styles : '';
		$icon_width = (isset ( $settings->icon_width ) && $settings->icon_width) ? $settings->icon_width : '60';
		$icon_tooltip = (isset ( $settings->icon_tooltip ) && $settings->icon_tooltip) ? ' uk-tooltip=" ' . $settings->icon_tooltip . '"' : '';

		// Default Animation.

		$animation = (isset ( $settings->animation ) && $settings->animation) ? $settings->animation : '';
		$animation_repeat = ($animation) ? ((isset ( $settings->animation_repeat ) && $settings->animation_repeat) ? ' repeat: true;' : '') : '';

		if ($animation == 'parallax') {
			$animation = ' uk-parallax="' . $horizontal . $vertical . $scale . $rotate . $opacity . $easing_cls . $viewport_cls . $breakpoint_cls . '"';
		} elseif (! empty ( $animation )) {
			$animation = ' uk-scrollspy="cls: uk-animation-' . $animation . ';' . $animation_repeat . '"';
		}

		$addon_id = $this->addon->id;
		$output = '';

		$output .= '<div class="ui-video' . $text_alignment . $general . $max_width_cfg . '"' . $animation . '>';
		if ($title_addon) {
			$output .= '<' . $title_heading_selector . ' class="tm-title' . $title_style . $title_heading_decoration . '">';

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '<span>' : '';

			$output .= nl2br ( $title_addon );

			$output .= ($title_heading_decoration == ' uk-heading-line') ? '</span>' : '';

			$output .= '</' . $title_heading_selector . '>';
		}
		if ($video_source == 'youtube-vimeo') {
			if ($url) {

				$video = parse_url ( $url );

				switch ($video ['host']) {
					case 'youtu.be' :
						$id = trim ( $video ['path'], '/' );
						$src = '//www.youtube.com/embed/' . $id;
						break;

					case 'www.youtube.com' :
					case 'youtube.com' :
						parse_str ( $video ['query'], $query );
						$id = $query ['v'];
						$src = '//www.youtube.com/embed/' . $id;
						break;

					case 'vimeo.com' :
					case 'www.vimeo.com' :
						$id = trim ( $video ['path'], '/' );
						$src = '//player.vimeo.com/video/' . $id;
				}
				$output .= '<iframe class="tm-video' . $box_shadow . '"' . $video_width_init . $video_height_init . ' src="' . $src . '" frameborder="0" allowfullscreen uk-responsive></iframe>';
			}
		} elseif ($video_source == 'html5') {
			$output .= '<video class="html5-video' . $box_shadow . '"' . $video_width_init . $video_height_init . ' src="' . $url_mp4 . '"' . $show_control . $loop_video . $mute_video . $play_inline . $lazy_load . $autoplay_init . '></video>';
		} else {
			$output .= '<div class="uk-inline with-animation">';
			$output .= '<a href="#video-' . $addon_id . '" uk-toggle>';

			if ($image_src) {
				$output .= '<img class="img-video' . $box_shadow . '" src="' . $image_src . '" alt="' . $alt_text . '" title="' . $alt_text . '">';
			}

			$output .= '<div class="uk-position-cover uk-overlay' . $overlay_styles . ' uk-flex uk-flex-center uk-flex-middle"><div class="btn-play"' . $icon_tooltip . '><span class="uk-icon"><svg width="' . $icon_width . '" height="' . $icon_width . '" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" data-svg="play-circle"><polygon fill="none" stroke="#000" stroke-width="1.1" points="8.5 7 13.5 10 8.5 13"></polygon><circle fill="none" stroke="#000" stroke-width="1.1" cx="10" cy="10" r="9"></circle></svg></span></div></div>';
			$output .= '</a>';
			$output .= '</div>';
			$output .= '<div id="video-' . $addon_id . '" class="tm-video uk-flex-top" uk-modal="container: true">';
			$output .= '<div class="uk-modal-dialog uk-width-auto uk-margin-auto-vertical">';
			$output .= '<button class="uk-modal-close-outside" type="button" uk-close></button>'; // uk-video
			$output .= '<video class="html5-video' . $box_shadow . '"' . $video_modal_width . ' src="' . $url_modal_mp4 . '"' . $show_control . $loop_video . $mute_video . $play_inline . $lazy_load . $autoplay_init . '></video>';
			$output .= '</div>';
			$output .= '</div>';
		}

		$output .= '</div>';

		return $output;
	}
	public function scripts() {
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme.js', [ ], [
				'defer' => true
		] );
		HTMLHelper::_ ( 'script', 'components/com_jpagebuilder/assets/js/uitheme-icons.js', [ ], [
				'defer' => true
		] );
	}
	public function stylesheets() {
		$style_sheet = [
				'components/com_jpagebuilder/assets/css/uitheme.css'
		];
		
		return $style_sheet;
	}
	public function css() {
		$settings = $this->addon->settings;
		$image = (isset ( $settings->image ) && $settings->image) ? $settings->image : '';
		$image_src = isset ( $image->src ) ? $image->src : $image;
		if (strpos ( $image_src, 'http://' ) !== false || strpos ( $image_src, 'https://' ) !== false) {
			$image_src = $image_src;
		} elseif ($image_src) {
			$image_src = Uri::base ( true ) . '/' . $image_src;
		}
		$css = '';
		if ($image_src) {
			$css .= ".with-animation .btn-play:before,.with-animation.btn-play:after{content:'';border:1px solid;border-color:inherit;width:150%;height:150%;-webkit-border-radius:50px;border-radius:50px;position:absolute;left:-25%;top:-25%;opacity:1;-webkit-animation:1s videomodule-anim linear infinite;animation:1s videomodule-anim linear infinite}.with-animation .btn-play:before{-webkit-animation-delay:.5s;animation-delay:.5s}@-webkit-keyframes videomodule-anim{0%{-webkit-transform:scale(.68);transform:scale(.68)}100%{-webkit-transform:scale(1.2);transform:scale(1.2);opacity:0}}@keyframes videomodule-anim{0%{-webkit-transform:scale(.68);transform:scale(.68)}100%{-webkit-transform:scale(1.2);transform:scale(1.2);opacity:0}}.btn-play{position:relative}";
		}
		$css .= '.tm-video.uk-modal {background: rgba(248,248,248,0.95);}.tm-video .uk-modal-close-outside {color: #888;}';
		return $css;
	}
	/**
	 * Generate the lodash template string for the frontend editor.
	 *
	 * @return string The lodash template string.
	 * @since 1.0.0
	 */
	public static function getFrontendEditor() {
		$lodash = new JpagebuilderLodashlib ( "#jpb-addon-{{data.id}}" );
		$output = '
				
			<#
				let videoUrl 		  = data.url || ""
				let video_title		  = "";
				let show_rel_video 	  = (typeof data.show_rel_video !== "undefined" && data.show_rel_video) ? "&rel=0" : "&rel=1";
				let embedSrc 		  = ""
				let youtube_no_cookie = data.no_cookie ? "-nocookie" : ""
				let youtube_shorts 	  = data.youtube_shorts ? data.youtube_shorts : 0;
				let aspect_ratio	  = (data.aspect_ratio && youtube_shorts) ? data.aspect_ratio : "16by9";
				let mp4_enable 		  = (typeof data.mp4_enable == "undefined") ? 0 : data.mp4_enable;
				
				let vimeo_show_author 	   	   = data.vimeo_show_author ? "byline=1" : "byline=0";
				let vimeo_mute_video  	   	   = data.vimeo_mute_video ? "muted=1" : "muted=0";
				let vimeo_show_video_title 	   = data.vimeo_show_video_title ? "title=1" : "title=0";
				let vimeo_show_author_profile  = data.vimeo_show_author_profile ? "portrait=1" : "portrait=0";
				
				const embeddedParameter = [vimeo_show_author,vimeo_mute_video,vimeo_show_video_title,vimeo_show_author_profile];
				const separator 		= "&";
				let embeddedString      = "";
				
				let mp4_video = (!_.isEmpty(data.mp4_video) && data.mp4_video) ? data.mp4_video : "https://storejextensions.org/cdn/templatesvideos/sample_ocean_with_audio.mp4";
				
				if (typeof mp4_video !== "undefined" && typeof mp4_video.src !== "undefined") {
					mp4_video = data.mp4_video
				} else {
					mp4_video = {src: data.mp4_video}
				}
				
				let ogv_video = (!_.isEmpty(data.ogv_video) && data.ogv_video) ? data.ogv_video : "https://storejextensions.org/cdn/templatesvideos/sample_ocean_with_audio.mp4";
				
				if (typeof ogv_video !== "undefined" && typeof ogv_video.src !== "undefined") {
					ogv_video = data.ogv_video
				} else {
					ogv_video = {src: data.ogv_video}
				}
				
				let video_poster = (!_.isEmpty(data.video_poster) && data.video_poster) ? data.video_poster : "https://storejextensions.org/cdn/templatesvideos/poster-video.png";
				
				if (typeof data.video_poster !== "undefined" && typeof data.video_poster.src !== "undefined") {
					video_poster = data.video_poster
				} else {
					video_poster = {src: data.video_poster}
				}
				
				if ( videoUrl ) {
					let tempAchor = document.createElement("a")
						tempAchor.href = videoUrl
				
					let videoObj = {
						host    :   tempAchor.hostname,
						path    :   tempAchor.pathname,
						query   :   tempAchor.search.substr(tempAchor.search.indexOf("?") + 1)
					}
				
					switch( videoObj.host ){
						case "youtu.be":
							var videoId = videoObj.path.trim();
							embedSrc 	= "//www.youtube"+youtube_no_cookie+".com/embed"+ videoId + "?iv_load_policy=3"+ show_rel_video
							video_title = data.video_title ? data.video_title : Joomla.Text._("COM_JPAGEBUILDER_ADDON_VIDEO_TITLE_DEFAULT_TEXT");
							break;
				
						case "www.youtube.com":
						case "youtube.com":
							var queryStr = (youtube_shorts) ? videoObj.path.split("/shorts/") : videoObj.query.split("=");
							if(videoObj.path === "/playlist") {
								embedSrc 	 = "//www.youtube.com/embed/?listType=playlist&list="+ videoObj.query.match(/\blist=([^&]+)/)?.[1]
							} else {
								embedSrc 	 = "//www.youtube"+youtube_no_cookie+".com/embed/"+ queryStr[1]+ "?iv_load_policy=3"+ show_rel_video
							}
							video_title  = data.video_title ? data.video_title : Joomla.Text._("COM_JPAGEBUILDER_ADDON_VIDEO_TITLE_DEFAULT_TEXT");
							break;
				
						case "www.vimeo.com":
						case "vimeo.com":
						case "player.vimeo.com":
							embedSrc = videoUrl;
				
							if(videoObj.host !== "player.vimeo.com") {
								var videoId  = videoObj.path.trim();
								embedSrc 	 = "//player.vimeo.com/video"+ videoId;
							}
				
							_.forEach(embeddedParameter, function(value,key){
								embeddedString += (key > 0) ? separator + value : value;
							});
				
							embedSrc = embedSrc + "?" + embeddedString;
							break;
					}
				}
			#>';
		$output .= '<style type="text/css">';
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
		$output .= $lodash->generateTransformCss ( '.jpb-addon-video', 'data.transform' );
		
		$output .= '
			</style>
	 		<div class="jpb-addon jpb-addon-video {{ data.class }}">
		 		<# if( !_.isEmpty( data.title ) ){ #><{{ data.heading_selector }} class="jpb-addon-title jp-inline-editable-element" data-id={{data.id}} data-fieldName="title" contenteditable="true">{{{ data.title }}}</{{ data.heading_selector }}><# } #>
				<# if(mp4_enable != 1){ #>
					<div class="jpb-iframe-drag-overlay"></div>
				
					<div class="jpb-video-block jpb-embed-responsive jpb-embed-responsive-{{ aspect_ratio }}">
						<# if(embedSrc){ #>
<div role="button" tabindex="0"><div class="jpb-addon-wrapper  " style="pointer-events: none;">
						<iframe class="jpb-embed-responsive-item" src=\'{{ embedSrc }}\' title= \'{{ video_title }}\' allow="accelerometer"; webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
</div></div>
					</div>
					<# } #>
				 <# } else {
					if(mp4_video.src || ogv_video.src){
						#>
						<div class="jpb-addon-video-local-video-wrap">
							<video class="jpb-addon-video-local-source"{{(data.video_loop != 0 ? " loop" : "")}}{{(data.autoplay_video != 0 ? " autoplay" : "")}}{{(data.show_control != 0 ? " controls" : "")}}{{(data.video_mute != 0 ? " muted" : "")}}
							<# if(!_.isEmpty(video_poster.src)){
							if(video_poster.src.indexOf("http://") == -1 && video_poster.src.indexOf("https://") == -1){ #>
								poster=\'{{ pagebuilder_base + video_poster.src }}\'
							<# } else { #>
								poster=\'{{ video_poster.src }}\'
							<# }
							} #>
							controlsList="nodownload">
							<# if(!_.isEmpty(mp4_video.src)){ #>
								<# if(mp4_video.src.indexOf("http://") == -1 && mp4_video.src.indexOf("https://") == -1){ #>
									<source src=\'{{ pagebuilder_base + mp4_video.src }}\' type="video/mp4">
								<# } else { #>
									<source src=\'{{ mp4_video.src }}\' type="video/mp4">
								<# } #>
							<# }
							if(!_.isEmpty(ogv_video.src)){
							#>
								<# if(ogv_video.src.indexOf("http://") == -1 && ogv_video.src.indexOf("https://") == -1){ #>
									<source src=\'{{ pagebuilder_base + ogv_video.src }}\' type="video/mp4">
								<# } else { #>
									<source src=\'{{ ogv_video.src }}\' type="video/mp4">
								<# } #>
							<# } #>
							</video>
						</div>
					<# } #>
				<# } #>
	 		</div>
		';
		
		return $output;
	}
}
