<?php

/**
 * Search Loop
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php do_action( 'bbp_template_before_search_results_loop' ); ?>

<table id="bbp-search-results" class="forums bbp-search-results table table-striped">

	<thead>
		<tr>
			<th class="bbp-search-author"><?php  _e( 'Author',  'bbpress' ); ?></th><!-- .bbp-reply-author -->
			<th class="bbp-search-content"><?php _e( 'Search Results', 'bbpress' ); ?></th><!-- .bbp-search-content -->
		</tr>
	</thead>

	<tbody>

		<?php while ( bbp_search_results() ) : bbp_the_search_result(); ?>

			<?php bbp_get_template_part( 'loop', 'search-' . get_post_type() ); ?>

		<?php endwhile; ?>

	</tbody>

</table><!-- #bbp-search-results -->

<?php do_action( 'bbp_template_after_search_results_loop' ); ?>