<?php
// Get content width
$content_max_width       = absint( $this->get( 'content_max_width' ) );

// Get template colors
$muted_text_color        = '#a1a1a1';
$theme_color             = $this->get_customizer_setting( 'theme_color' );
$text_color              = $this->get_customizer_setting( 'text_color' );
$border_color            = $this->get_customizer_setting( 'border_color' );
$link_color              = $this->get_customizer_setting( 'link_color' );
$header_background_color = $this->get_customizer_setting( 'header_background_color' );
$header_color            = $this->get_customizer_setting( 'header_color' );
?>
/* Generic WP styling */

.alignright {
	float: right;
}

.alignleft {
	float: left;
}

.aligncenter {
	display: block;
	margin-left: auto;
	margin-right: auto;
}

.amp-wp-enforced-sizes {
	/** Our sizes fallback is 100vw, and we have a padding on the container; the max-width here prevents the element from overflowing. **/
	max-width: 100%;
	margin: 0 auto;
}

.amp-wp-unknown-size img {
	/** Worst case scenario when we can't figure out dimensions for an image. **/
	/** Force the image into a box of fixed dimensions and use object-fit to scale. **/
	object-fit: contain;
}

/* Template Styles */

.amp-wp-content,
.amp-wp-title-bar div {
	<?php if ( $content_max_width > 0 ) : ?>
	margin: 0 auto;
	max-width: <?php echo sprintf( '%dpx', $content_max_width ); ?>;
	<?php endif; ?>
}

html {
	background: <?php echo sanitize_hex_color( $header_background_color ); ?>;
}

body {
	background: <?php echo sanitize_hex_color( $theme_color ); ?>;
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
	font-family: "Roboto", "Ubuntu", "Helvetica Neue", sans-serif;
	font-weight: 300;
	line-height: 1.75em;
}

p,
ol,
ul,
figure {
	margin: 0 0 1em;
	padding: 0;
}

a,
a:visited {
	color: <?php echo sanitize_hex_color( $link_color ); ?>;
}

a:hover,
a:active,
a:focus {
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
}

address {
	font-style: normal;
}

table {
  font-size: .9em;
}

tbody th, tbody td {
	line-height: 1.5em;
  padding: .325em .35em ;
}

/* Quotes */

blockquote {
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
	background: rgba(127,127,127,.125);
	border-left: 2px solid <?php echo sanitize_hex_color( $link_color ); ?>;
	margin: 8px 0 24px 0;
	padding: 16px;
}

blockquote p:last-child {
	margin-bottom: 0;
}

.amp-hidden {
	display: none
}

/* Header */

.amp-wp-header {
	background-color: <?php echo sanitize_hex_color( $header_background_color ); ?>;
}

.amp-wp-header div {
	color: <?php echo sanitize_hex_color( $header_color ); ?>;
	font-size: 1em;
	font-weight: 300;
	margin: 0 auto;
	max-width: calc(840px - 32px);
	padding: .875em 16px;
	position: relative;
}

.amp-wp-header a {
	color: <?php echo sanitize_hex_color( $header_color ); ?>;
	text-decoration: none;
}

/* Site Icon */

.amp-wp-header .amp-wp-site-icon {
	/** site icon is 32px **/
	background-color: <?php echo sanitize_hex_color( $header_color ); ?>;
	border: 1px solid <?php echo sanitize_hex_color( $header_color ); ?>;
	border-radius: 50%;
	position: absolute;
	right: 18px;
	top: 10px;
}

.amp-header-meta {
	width: 100%;
}

/* Article */

.amp-wp-article {
	color: <?php echo sanitize_hex_color( $text_color ); ?>;
	font-weight: 300;
	margin: 0 auto;
	max-width: 840px;
	overflow-wrap: break-word;
	word-wrap: break-word;
}

/* Article Header */

.amp-wp-article-header {
	align-items: center;
	align-content: stretch;
	display: flex;
	flex-wrap: wrap;
	justify-content: space-between;
	margin: 1em 16px;
}

.amp-wp-title {
	color: #200d35;
	display: block;
	flex: 1 0 100%;
	font-weight: 900;
	margin: 0;
	width: 100%;
	font-size: 1.35em;
}

/* Article Meta */

.amp-wp-meta {
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	display: inline-block;
	width: 49%;
	font-size: .75em;
	line-height: 1.5em;
	margin: 0;
	padding: 0 0 .85em;
}

.amp-wp-posted-on {
	text-align: left;
}

.amp-wp-byline {
	display: none;
	text-align: right;
}

.amp-wp-byline amp-img,
.amp-wp-byline .amp-wp-author {
	display: inline-block;
	vertical-align: middle;
}

.amp-wp-byline amp-img {
	border: 1px solid <?php echo sanitize_hex_color( $link_color ); ?>;
	border-radius: 50%;
	position: relative;
	margin-right: 6px;
}

/* Featured image */

.amp-wp-article-featured-image {
	padding: 1em 16px 0;
}

.amp-wp-article-featured-image amp-img {
	margin: 0 auto;
}

.amp-wp-article-featured-image.wp-caption .wp-caption-text {
	margin: 0 18px;
}

/* Article Content */

.amp-wp-article-content {
	margin: 0 16px;
}

.amp-wp-article-content ul,
.amp-wp-article-content ol {
	margin-left: 1em;
}

.amp-wp-article-content amp-img {
	margin: 0 auto;
}

.amp-wp-article-content amp-img.alignright {
	margin: 0 0 1em 16px;
}

.amp-wp-article-content amp-img.alignleft {
	margin: 0 16px 1em 0;
}

/* Captions */

.wp-caption {
	padding: 0;
}

.wp-caption.alignleft {
	margin-right: 16px;
}

.wp-caption.alignright {
	margin-left: 16px;
}

.wp-caption .wp-caption-text {
	border-bottom: 1px solid <?php echo sanitize_hex_color( $border_color ); ?>;
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	font-size: .875em;
	line-height: 1.5em;
	margin: 0;
	padding: .66em 10px .75em;
}

/* AMP Media */

amp-carousel {
	background: <?php echo sanitize_hex_color( $border_color ); ?>;
	margin: 0 -16px 1.5em;
}
amp-iframe,
amp-youtube,
amp-instagram,
amp-vine {
	background: <?php echo sanitize_hex_color( $border_color ); ?>;
	margin: 0 0 1.5em;
}

.amp-wp-article-content amp-carousel amp-img {
	border: none;
}

amp-carousel > amp-img > img {
	object-fit: contain;
}

.amp-wp-iframe-placeholder {
	background: <?php echo sanitize_hex_color( $border_color ); ?> url( <?php echo esc_url( $this->get( 'placeholder_image_url' ) ); ?> ) no-repeat center 40%;
	background-size: 48px 48px;
	min-height: 48px;
}

/* Article Footer Meta */

.amp-wp-article-footer .amp-wp-meta {
	display: block;
}

.amp-wp-tax-category,
.amp-wp-tax-tag {
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	font-size: .875em;
	line-height: 1.5em;
	margin: 1.5em 16px;
}

.amp-wp-comments-link {
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	font-size: .875em;
	line-height: 1.5em;
	text-align: center;
	margin: 2.25em 0 1.5em;
}

.amp-header-meta .location {
	line-height: 1.5em;
	font-size: .85em;
	padding-top: .5em;
}
.amp-header-meta .venue {
	font-weight: 500;
}
.amp-header-meta .price {
  color: #200d35;
  font-weight: 700;
	font-size: .875em;
}
.amp-header-meta .location a {
	color: #ababab;
	text-decoration: none;
}
.hidden {
	display: none
}

.amp-header-meta .location span {
	margin-right: 5px;
}

.taxonomy-links {
  font-size: .7em;
  padding-top: 1em;
}
.taxonomy-links a {
  color: #5f5f5f;
  display: inline-block;
  border: solid 1px #ddd;
  font-weight: 500;
  margin-left: -1px;
  padding: 6px;
	text-decoration: none;
  line-height: normal;
}

.amp-dates {
  padding: .5em 0 1.5em;
  margin: 1em 16px;
  border-bottom: 1px solid #e6e6e6;
}

.amp-dates .day a,
.amp-dates .price-action {
  background-color: #ffc000;
  border-top: 1px solid #fefefe;
  color: #5f5f5f;
  font-weight: 700;
  display: block;
  padding: .65em .75em;
  text-decoration: none;
  font-size: .9em;
	line-height: normal;
}

.amp-dates .day a:after,
.amp-dates .price-action:after {
  float: right;
  content: "\25B6";
}

.amp-dates .day a.inactive,
.amp-dates .price-action.inactive {
  color: #ababab;
  background-color: #f5f5f5;
}

/* AMP Footer */

ins, .ad {
	background: #f3f3f3;
}

.amp-wp-footer {
  background: #f5f5f5;
	border-top: 1px solid <?php echo sanitize_hex_color( $border_color ); ?>;
}

.amp-wp-footer div {
	margin: 0 auto;
	max-width: calc(840px - 32px);
	padding: 1.25em 16px 1.25em;
	position: relative;
}

.amp-wp-footer h2 {
	font-size: .85em;
	line-height: 1.375em;
	margin: 0 0 .5em;
}

.amp-wp-footer p {
	color: <?php echo sanitize_hex_color( $muted_text_color ); ?>;
	font-size: .8em;
	line-height: 1.5em;
	margin: 0 85px 0 0;
}

.amp-wp-footer a {
	text-decoration: none;
}

.back-to-top {
	bottom: 1.75em;
	font-size: .8em;
	font-weight: 600;
	line-height: 2em;
	position: absolute;
	right: 16px;
}

.amp-menu-btn {
	position: absolute;
	right: 1em;
	top: 1.1em;
	width: 22px;
	border-top: solid 2px #fff;
	border-bottom: solid 2px #fff;
	padding: 5px 0;
	cursor: pointer;
	margin: 0 .5em;
}

.amp-menu-btn span {
	text-indent: -999em;
	display: block;
	border-top: solid 2px #fff;
	line-height: 0px;
}

amp-sidebar {
	width: 200px;
	paddding: 1em;
}

amp-sidebar ul {
	padding: 0;
	margin: 0;
	list-style: none;
}

amp-sidebar ul li {
	font-weight: 500;
}

amp-sidebar a,
amp-sidebar a:visited {
	text-decoration: none;
	color: #303133;
	display: block;
	padding: .5em 1.25em;
}

.amp-sidebar-header {
	text-align: right;
	border-bottom: solid 1px #ddd;
}

.amp-sidebar-header a {
	display: block;
	padding: .35em 1.25em;
}


ul.event-list {
	padding: 0;
  margin: 0 0 2em;
	border-top: 2px solid #ddd
}

.event-list li {
  font-size: .85em;
  display: block;
  list-style: none;
}

.event-list li em {
  display: block;
}

.event-list li strong {
  padding: 0 5px;
}

.event-list li a {
  display: block;
  border-bottom: 2px solid #ddd;
  padding: 8px;
	text-decoration: none;
	line-height: 1.5em;
}

.single .no-events {
  color: #828282;
  text-align: center;
	background-color: #f5f5f5;
}
.single .inactive:after,
.single .no-events:after {
    content: "";
}

.amp-carousel .amp-wp-article-featured-image {
	display: none;
}
