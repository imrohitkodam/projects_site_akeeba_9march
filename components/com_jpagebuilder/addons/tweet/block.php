<?php

/**
 * @package JPageBuilder
 * @author Joomla! Extensions Store
 * @copyright (C) 2024 - Joomla! Extensions Store
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
 */
// no direct access
defined ( '_JEXEC' ) or die ( 'restricted access' );

use Joomla\CMS\Language\Text;
class JpagebuilderAddonTweet extends JpagebuilderAddons {
	/**
	 * The addon frontend render method.
	 * The returned HTML string will render to the frontend page.
	 *
	 * @return string The HTML string.
	 * @since 1.0.0
	 */
	public function render() {
		$setting = $this->addon->settings;

		$class = (isset ( $setting->class ) && $setting->class) ? $setting->class : '';
		$title = (isset ( $setting->title ) && $setting->title) ? $setting->title : '';
		$heading_selector = (isset ( $setting->heading_selector ) && $setting->heading_selector) ? $setting->heading_selector : 'h3';

		// Options
		$autoplay = (isset ( $setting->autoplay ) && $setting->autoplay) ? ' data-jpb-ride="jpb-carousel"' : '';
		$username = (isset ( $setting->username ) && $setting->username) ? $setting->username : 'storejoomla';
		$consumerkey = (isset ( $setting->consumerkey ) && $setting->consumerkey) ? $setting->consumerkey : '';
		$consumersecret = (isset ( $setting->consumersecret ) && $setting->consumersecret) ? $setting->consumersecret : '';
		$accesstoken = (isset ( $setting->accesstoken ) && $setting->accesstoken) ? $setting->accesstoken : '';
		$accesstokensecret = (isset ( $setting->accesstokensecret ) && $setting->accesstokensecret) ? $setting->accesstokensecret : '';
		$include_rts = (isset ( $setting->include_rts ) && $setting->include_rts) ? $setting->include_rts : '';
		$ignore_replies = (isset ( $setting->ignore_replies ) && $setting->ignore_replies) ? $setting->ignore_replies : '';
		$show_image = (isset ( $setting->show_image )) ? $setting->show_image : 1;
		$show_username = (isset ( $setting->show_username ) && $setting->show_username) ? $setting->show_username : '';
		$show_avatar = (isset ( $setting->show_avatar ) && $setting->show_avatar) ? $setting->show_avatar : '';
		$count = (isset ( $setting->count ) && $setting->count) ? $setting->count : '';

		// Warning
		if ($consumerkey == '')
			return '<div class="jpb-alert jpb-alert-danger"><strong>Error</strong><br>Insert consumer key for twitter feed slider addon</div>';
		if ($consumersecret == '')
			return '<div class="jpb-alert jpb-alert-danger"><strong>Error</strong><br>Insert consumer secrete key for twitter feed slider addon</div>';
		if ($accesstoken == '')
			return '<div class="jpb-alert jpb-alert-danger"><strong>Error</strong><br>Insert access token for twitter feed slider addon</div>';
		if ($accesstokensecret == '')
			return '<div class="jpb-alert jpb-alert-danger"><strong>Error</strong><br>Insert access token secrete key for twitter feed slider addon</div>';

		// Include tweet helper
		$tweet_helper = JPATH_ROOT . '/components/com_jpagebuilder/helpers/twitter/helper.php';
		if (! file_exists ( $tweet_helper )) {
			return '<p class="alert alert-danger">' . Text::_ ( 'COM_JPAGEBUILDER_ADDON_TWEET_HELPER_FILE_MISSING' ) . '</p>';
		} else {
			require_once $tweet_helper;
		}

		// Get Tweets
		$tweets = JpagebuilderAddonHelperTwitter::getTweets ( $username, $consumerkey, $consumersecret, $accesstoken, $accesstokensecret, $count, $ignore_replies, $include_rts );

		if (isset ( $tweets->errors ) && is_array ( $tweets->errors )) {
			$message = '';
			foreach ( $tweets->errors as $error ) {
				$message .= '<p class="jpb-alert jpb-alert-warning">' . $error->message . '</p>';
			}
			return $message;
		}

		// Output
		if (count ( ( array ) $tweets ) > 0) {
			$output = '<div class="jpb-addon jpb-addon-tweet jpb-text-center ' . $class . '">';
			$output .= ($title) ? '<' . $heading_selector . ' class="jpb-addon-title">' . $title . '</' . $heading_selector . '>' : '';
			$output .= ($show_avatar) ? '<a target="_blank" rel="noopener noreferrer" href="https://twitter.com/' . $tweets [0]->user->screen_name . '"><img class="jpb-img-circle jpb-tweet-avatar" src="' . $tweets [0]->user->profile_image_url_https . '" alt="' . $tweets [0]->user->name . '" loading="lazy"></a>' : '';
			$output .= ($show_username) ? '<span class="jpb-tweet-username"><a target="_blank" rel="noopener noreferrer" href="https://twitter.com/' . $tweets [0]->user->screen_name . '">' . $tweets [0]->user->name . '</a></span>' : '';
			$output .= '<div id="jpb-carousel-' . $this->addon->id . '" class="jpb-carousel jpb-tweet-slider jpb-slide" ' . $autoplay . '>';
			$output .= '<div class="jpb-carousel-inner">';

			foreach ( $tweets as $key => $tweet ) {
				$output .= '<div class="jpb-item' . (($key == 0) ? ' active' : '') . '">';
				$tweet->text = preg_replace ( "/((http)+(s)?:\/\/[^<>\s]+)/i", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $tweet->text );
				$tweet->text = preg_replace ( "/[@]+([A-Za-z0-9-_]+)/", "<a href=\"https://twitter.com/\\1\" target=\"_blank\">\\0</a>", $tweet->text );
				$tweet->text = preg_replace ( "/[#]+([A-Za-z0-9-_]+)/", "<a href=\"https://twitter.com/search?q=%23\\1\" target=\"_blank\">\\0</a>", $tweet->text );
				$output .= '<small class="jpb-tweet-created">' . JpagebuilderAddonHelperTwitter::timeago ( $tweet->created_at ) . '</small>';
				if ((isset ( $tweet->entities ) && $tweet->entities) && $show_image) {
					if (isset ( $tweet->entities->media ) && $tweet->entities->media) {
						foreach ( $tweet->entities->media as $media ) {
							if ($media->type == 'photo') {
								$img_src = (isset ( $media->sizes->small ) && $media->sizes->small) ? $media->media_url . ':thumb' : $media->media_url;
								$output .= '<div class="jpb-item-image">';
								$output .= ($media->url) ? '<a href="' . $media->url . '" target="_blank" rel="noopener noreferrer">' : '';
								$output .= '<img class="jpb-tweet-image" src="' . $img_src . '" alt="' . preg_replace ( '/<\/?a[^>]*>/', '', $tweet->text ) . '" loading="lazy">';
								$output .= ($media->url) ? '</a>' : '';
								$output .= '</div>';
							}
						}
					}
				}
				$output .= '<div class="jpb-tweet-text">' . $tweet->text . '</div>';
				$output .= '</div>';
			}

			$output .= '</div>';
			$output .= '<a href="#jpb-carousel-' . $this->addon->id . '" class="left jpb-carousel-control" role="button" data-slide="prev" aria-label="' . Text::_ ( 'COM_JPAGEBUILDER_ARIA_PREVIOUS' ) . '"><i class="fa fa-angle-left" aria-hidden="true"></i></a>';
			$output .= '<a href="#jpb-carousel-' . $this->addon->id . '" class="right jpb-carousel-control" role="button" data-slide="next" aria-label="' . Text::_ ( 'COM_JPAGEBUILDER_ARIA_NEXT' ) . '"><i class="fa fa-angle-right" aria-hidden="true"></i></a>';
			$output .= '</div>';
			$output .= '</div>';

			return $output;
		}

		return;
	}
}
