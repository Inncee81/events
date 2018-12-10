<?php
/**
 * @package WordPress
 * @subpackage Theme_Compat
 * @deprecated 3.0.0
 *
 * This file is here for backward compatibility with old themes and will be removed in a future version
 *
 */
?>

<!-- You can start editing here. -->

<?php if ( have_comments() ) : ?>

	<h3 id="comments">
		<?php printf( __( '%s reviews', 'visita' ),  get_the_title() );?>
	</h3>

	<ol class="commentlist	">
		<?php
			wp_list_comments(
				array(
					'avatar_size' => 32,
					'style'       => 'ol',
					'short_ping'  => true,
					'callback' 		=> 'visita_comment',
				)
			);
		?>
	</ol>

<?php endif; ?>
