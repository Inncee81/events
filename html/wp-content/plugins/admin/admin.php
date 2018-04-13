<?php
/*
	Plugin Name: Admin Area
	Plugin URI: http://xparkmedia.com
	Description: Default Settings for all xpark media sites. Custom login, remove some categories form RSS, delay RSS, add ID colum, cache, gz compression, set email information.
	Version: 3.0.0
	Author: Xpark Media
	Author URI: http://xparkmedia.com/
	Copyright (C) 2015-2018 Hafid R. Trujillo - Xpark Media
	Text Domain: admin

  Copyright 2010-2018 by Hafid Trujillo http://www.xparkmedia.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Pusblic License as published by
  the Free Software Foundation; either version 2 of the License,or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not,write to the Free Software
  Foundation,Inc.,51 Franklin St,Fifth Floor,Boston,MA 02110-1301 USA
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access file directly
};


if ( ! class_exists( 'XparkMedia' ) ) {
	class XparkMedia {

		/**
		* Variables
		*
		* @param $domain plugin Gallery IDentifier
		* Make sure that new language(.mo ) files have 'ims-' as base name
		*/
		protected $styles 	= array();
		protected $version 	= '3.0.0';
		protected $domain 	= 'xparkmedia';


		/**
		* Constructor
		*
		* @return void
		* @since 0.5.0
		*/
		function __construct( ) {

			//basics
			add_action( 'init', array( $this, 'add_hooks' ) );
			add_action( 'init', array( $this, 'force_gzip_compresion' ), 1 );

			define( 'XPARKMEDIA_ABSPATH', str_replace( "\\", "/", dirname( __FILE__ ) ) );

			//
			add_action( 'init', array( $this, 'redirect_page_admin' ), -10 );
			add_action( 'widgets_init', array( $this, 'remove_default_wp_widgets' ), 100 );

			// completely disable xmlrpc for security reasons
			// only enable if there is a need to use mobile native apps
			add_filter( 'xmlrpc_enabled', '__return_false' );

			//
			add_filter( 'rest_authentication_errors', array( $this, 'json_basic_auth_handler'), 30 );
		}

		/**
		* Load what is needed where is needed
		*
		* @return void
		* @since 0.5.0
		*/
		function add_hooks( ) {

			//clean up
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wp_generator' );
      remove_action( 'wp_print_styles', 'print_emoji_styles' );
      remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

			//extend post types
			add_filter( 'widget_text', 'do_shortcode' );
			add_post_type_support( 'page', 'excerpt' );

			//branding
			add_action( 'login_head', array( $this, 'custom_login' ), 1 );
			add_filter( 'wp_mail_from', array( $this, 'mail_from' ), 100 );
			add_filter( 'wp_mail_from_name', array( $this, 'mail_from_name' ), 100 );

			//admin url modifications
			add_filter( 'site_url', array( $this, 'change_access_urls' ), 100, 2 );
			add_filter( 'network_site_url', array( $this, 'change_access_urls' ), 100, 2 );


			if ( is_admin( ) ) {

				add_filter( 'name_save_pre', array( $this, 'seo_slugs'), 100 );
				add_filter( 'tiny_mce_before_init', array( $this, 'extended_editor_valid_elements' ) );

				return;
			}

			// login screen
			add_action( 'login_head', array( $this, 'custom_login' ), 1 );
			add_filter( 'login_headerurl', array( $this, 'change_wp_login_url' ) );
			add_filter( 'login_headertitle', array( $this, 'change_wp_login_title' ) );

			//rss filters
			add_filter( 'rss2_ns', array( $this, 'add_image_ns_rss'), 100, 2 );
			add_filter( 'posts_where', array( $this, 'publish_later_on_feed' ) );
			add_filter( 'rss2_item', array( $this, 'add_image_to_rss_item'), 100, 2 );

			// frontend hooks
			add_filter( 'body_class', array( $this, 'admin_body_classes' ), 100 );
			add_filter( 'wp_get_attachment_image_attributes', array( $this, 'validate_image_atttributes'), 10, 2 );

			//commentsxx
			add_action( 'comment_form', array( $this, 'add_comment_nonce' ) );
			add_action( 'pre_comment_on_post', array( $this, 'check_comment_nonce' ) );

			if ( WP_CACHE == true ) {
				add_action( 'template_redirect', array( $this, 'compress_html_markup' ), 1 );
				
				add_filter( 'wp_get_attachment_url', array( $this, 'attachment_url'), 100 );
				add_filter( 'stylesheet_directory_uri', array( $this, 'attachment_url'), 50 );
      }
		}

		/**
		* Use the admin email for emails
		*/
		function mail_from( ) {
			return sanitize_email( get_bloginfo( 'admin_email' ) );
		}

		/**
		* Use the site name for emails
		*/
		function mail_from_name( ) {
			return esc_attr( get_bloginfo( 'name' ) );
		}

		/**
		* Chagnge login logo link from wordpress to stephens media
		*/
		function change_wp_login_url( ) {
			return esc_url( get_bloginfo( 'url' ) );
		}

		/**
		* Custom tile on stephens media logo ( before worpress logo )
		*/
		function change_wp_login_title( ) {
			return 'Powered by Xpark Media';
		}

		/**
		* Custom long in screen wp-admin ( css )
		*
		* @return void
		* @since 1.0.0
		*/
		function custom_login( ) {
			wp_enqueue_style( 'admin-login', plugins_url( 'css/login.css', __FILE__ ), NULL, $this->version );
		}

		/**
		* Add nonce to comment form
		*
		* @return void
		* @since 1.0.0
		*/
		function add_comment_nonce( ){
			wp_nonce_field( 'xm_comment_form' );
		}

		/**
		* Validate comment nonce
		*/
		function check_comment_nonce( ) {
			if ( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'xm_comment_form' ) ) {
				die( 'Security check failed' );
			}
		}

		/**
		* Add nonce to registration to avoid spam
		*/
		function add_resgistration_actions( ) {
			wp_nonce_field( 'xwewerhg-register', 'xm-register-nonce' );
		}

    /**
     * html markup compression
		 *
     * @return void
     * @since 0.1.0
     */
    function compress_html_markup( ) {
      ob_start( array( $this, 'html_minify_buffer') );
    }

		/**
		* Minimize html output
		*
		* @param string $html page markup
		* @return string
		* @since 0.1.0
		*/
		function html_minify_buffer( $html ) {

			//remove comments
			$html = preg_replace('/<!--([^(\[>|<)])(?:(?!-->).)*-->/s', '', $html);
			$html = preg_replace('/>\s+</', '><', $html);
			$html = str_ireplace('<p></p>', '', $html);

			return $html;
		}

		/**
		* Check registration nonce
		*
		* @return void
		* @since 0.1.0
		*/
		function check_registration_nonce( ) {
			if ( ! wp_verify_nonce( $_POST['xm-register-nonce'], 'xwewerhg-register' ) ) {
				wp_die( 'Security check failed' );
			}
		}

		/**
		* Restrict REST API to domain and authenticated users
		*
		* @return $user object
		* @since 3.0.0
		*/
		function json_basic_auth_handler( $error ) {

			if ( current_user_can( 'publish_posts' ) ) {
				return $error;
			}

			foreach ( array('HTTP_X_FORWARDED_FOR', 'REMOTE_HOST', 'HTTP_ORIGIN' ) as $HTTP_HOST ) {
				if ( isset($_SERVER[$HTTP_HOST]) && preg_replace('/^https?\:\/\//i', '', $_SERVER[$HTTP_HOST]) == $_SERVER['HTTP_HOST']) {
					return $error;
				}
			}

			// Check that we're trying to authenticate
			if ( ! isset( $_SERVER['PHP_AUTH_USER'] ) || ! isset( $_SERVER['PHP_AUTH_PW'] ) ) {
				return new WP_Error( 'authentication_failed', __( 'Authentication is required', 'admin' ), array( 'status' => 401 ) );
			}

			if ( is_wp_error( wp_authenticate( $_SERVER['PHP_AUTH_USER'], $_SERVER['PHP_AUTH_PW'] ) ) ) {
				return new WP_Error( 'authentication_failed', __( 'Invalid username or password', 'admin' ), array( 'status' => 403 ) );
			}

			return $error;
		}

		/**
		* Use title as alternative attribute if not availble
		*
		* @param $attributes array
		* @param $attachment WP_Post object
		* @return array
		* @since 0.1.7
		*/
		function validate_image_atttributes( $attributes, $attachment ) {

			if ( ! isset( $attributes['alt'] ) || '' === $attributes['alt'] ) {
				$attributes['alt'] = trim( strip_tags( get_the_title( $attachment->ID ) ) );
			}

			if ( ! empty( $attributes['sizes'] ) && empty( $attributes['srcset'] ) ){
				if ( $attachment_meta = (object) wp_get_attachment_metadata( $attachment->ID ) ) {
					$attributes['srcset'] = wp_get_attachment_image_url( $attachment->ID, 'full' ) . " {$attachment_meta->width}w";
				} else {
					unset($attributes['sizes']);
				}
			}

			return $attributes;
		}

		/**
		*  Add namespace for rss2 media tag
		*
		* @return void
		* @since 3.0.0
		*/
		function add_image_ns_rss( ) {
			echo 'xmlns:media="http://search.yahoo.com/mrss/"' . "\n";
		}

		/**
		* Delay the publication of RSS
		*/
		function publish_later_on_feed( $where ) {
			if ( ! is_feed( ) ) {
			 return $where;
			}

			global $wpdb; $where .= sprintf(
			 " AND ( TIMESTAMPDIFF( MINUTE, {$wpdb->posts}.post_date_gmt, '%s' ) > 10 )",
			 gmdate( 'Y-m-d H:i:s' )
			);

			return $where;
		}

		/**
		* Add media tag to each item in rss2 feed
		*
		* @return void
		* @since 0.2.7
		*/
		function add_image_to_rss_item( ) {

			if ( ! has_post_thumbnail() ) {
				return;
			}

			$data = false;
			$upload_dir = wp_upload_dir();
			$metadata = wp_get_attachment_metadata( get_post_thumbnail_id() );

			if ( $metadata && $upload_dir ) {

				$sizes = $metadata['sizes'];
				if ( $sizes['mobile'] ) $data = $sizes['mobile'];
				else if ( $sizes['medium'] ) $data = $sizes['medium'];

				if ($data) {
					printf("\t" .'<media:content medium="image" isDefault="true" width="%1$s" height="%2$s" lang="%3$s" url="%4$s"/>' . "\n\t",
						esc_attr( $data['width'] ),
						esc_attr( $data['height'] ),
						esc_attr( substr(get_locale(), 0, 2 ) ),
						esc_url( $upload_dir['baseurl'] . "/" . dirname($metadata['file']) . "/" . urlencode($data['file'] ) )
					);
				}
			}
		}

		/**
		 * Force GZip compression
		 */
		function force_gzip_compresion( ) {

			// Dont use on /wp-admin/post ( tinymce )
			if ( preg_match( '(/post.php|/post-new.php)i', $_SERVER['REQUEST_URI']) || ! WP_CACHE ) {
			  return;
			}

			global $compress_scripts, $concatenate_scripts, $compress_css;
			$compress_css = $compress_scripts =  $concatenate_scripts = 1;
		}

		/*
		*
		*/
		function redirect_page_admin( ) {

			add_rewrite_rule( 'access/?', 'wp-login.php', 'top' );
			add_rewrite_rule( 'register/?', 'wp-signup.php', 'top' );
			add_rewrite_rule( 'loggedout/?', 'wp-login.php?loggedout=true', 'top' );

			//login in page redirect
			if ( preg_match( '/wp-login.php/i', $_SERVER['REQUEST_URI'] ) && empty( $_POST['user_login'])  ) {
				wp_redirect( site_url( str_replace( array( 'wp-login.php' ), 'access', $_SERVER['REQUEST_URI'] ) ) );
				die();
			}
		}

		/*
		*
		*/
		function change_access_urls( $original_url, $path ) {

			if ( preg_match( '/wp-signup.php/i', $original_url ) )
				return site_url( 'signup/' );

			if ( ! preg_match( '/wp-login.php/i', $original_url ) )
				return $original_url;

			if ( preg_match( '/action=register/i', $original_url ) )
				return site_url( 'signup/' );

			if ( preg_match( '/loggedout=true/i', $original_url ) )
				return site_url( 'signedout/', 'login'  );

			return  site_url( str_replace( array('wp-login.php' ), 'access', $path ) );
		}

		function attachment_url($url) {
			return str_replace( array('http://'), 'http://s.', $url );
		}

		/*
		*
		*/
		function remove_default_wp_widgets( ) {
			$remove_widgets = array(
	      'WP_Widget_Pages', 'WP_Widget_Archives', 'WP_Widget_Media_Audio',
				'WP_Widget_Categories', 'WP_Widget_Recent_Comments', 'WP_Widget_RSS',
				'WP_Nav_Menu_Widget', 'WP_Widget_Tag_Cloud', 'WP_Widget_Media_Video',
				'WP_Widget_Meta', 'WP_Widget_Recent_Posts', 'WP_Widget_Calendar',
				'WP_Widget_Media_Image', 'WP_Widget_Search'
			);

			foreach ( $remove_widgets as $widgets ) {
				unregister_widget( $widgets );
			}
		}

		/**
		* Add custom classes to the body post-type page
		*/
		function admin_body_classes( $classes ){
			if ( ! is_post_type_archive( ) ) {
				return $classes;
			}

			array_push( $classes,
				'post-type-archive',
				'post-type-' . get_query_var('post_type' )
			);

			return $classes;
		}

		/**
     * Allow schema.org attributes and cleanup deprecated attributes
     *
     * @param $settings array
     * @return $settings array
     * @since 0.1.3
     */
    function extended_editor_valid_elements( $settings ) {
      /**
      *   Edit extended_valid_elements as needed. For syntax, see
      *   http://www.tinymce.com/wiki.php/Configuration:valid_elements
      *
      *   NOTE: Adding an element to extended_valid_elements will cause TinyMCE to ignore
      *   default attributes for that element.
      *   Eg. a[title] would remove href unless included in new rule: a[title|href]
      */
      if (empty($settings['extended_valid_elements'])) {
        $settings['extended_valid_elements'] = '';
      }

      $settings['extended_valid_elements'] .=
      '@[id|class|style|title|itemscope|itemtype|itemprop|datetime|rel|data-*]' .
      ',article,header,i,footer,div,dl,dt,dd,em,li,span,strong,bold,ul' .
      ',a[href|name|target|target|charset|lang|tabindex|accesskey|type|download]'.
      ',iframe[src|width|height|allowfullscreen|sandbox|name]' .
      ',img[src|alt|width|height|align|srcset|crossorigin]';

      $settings['paste_postprocess'] = 'function(pl, o){ o.node.innerHTML = o.node.innerHTML.replace(/(<(p|span)[^>]*>)\s?(<br\/?>|&nbsp;)/ig, "$1") }';
      return $settings;
    }

		/**
		* Remove words form url slug
		*/
		function seo_slugs( $slug ) {

			if ( isset( $_POST['post_title'] ) && ! $slug ) {
				$slug = sanitize_title( $_POST['post_title'] );
			}

			// Turn it to an array and strip common words by comparing against c.w. array
			$seo_slug_array = array_diff( explode( "-", strtolower( $slug ) ), array (
				"a", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "aint", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "a's", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "better", "between", "beyond", "both", "brief", "but", "by", "c", "came", "can", "cannot", "cant", "can't", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "c'mon", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "currently", "d", "de", "del", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "done", "don't", "down", "downwards", "during", "e", "es", "en", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "ellos", "ellas", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "here's", "hereupon", "hers", "herself", "he's", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "ie", "if", "ignored", "i'll", "i'm", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "its", "it's", "itself", "i've", "j", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "l", "los", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "neverless", "nevertheless", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "one's", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "past", "per", "por", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "thats", "that's", "that've", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "there'd", "therefore", "therein", "there'll", "there're", "theres", "there's", "thereupon", "there've", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "twice", "two", "u", "un", "under", "underneath", "undoing", "unfortunately", "un", "unos", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "welcome", "well", "we'll", "went", "were", "we're", "weren't", "we've", "what", "whatever", "what'll", "what's", "what've", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "where's", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "whoever", "whole", "who'll", "whom", "whomever", "who's", "whose", "why", "will", "willing", "wish", "with", "within", "without", "wonder", "won't", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "your", "you're", "yours", "yourself", "yourselves", "you've", "z", "zero"
			) );

			return trim( join ("-", $seo_slug_array ) );
		}
	}

	//do that thing that you do
	$XparkMedia = new XparkMedia( );
}
