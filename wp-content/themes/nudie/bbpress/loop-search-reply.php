<?php

/**
 * Search Loop - Single Reply
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<tr id="post-<?php bbp_reply_id(); ?>" <?php bbp_reply_class(); ?>>

	<td>

		<div class="bbp-reply-author">

			<?php do_action( 'bbp_theme_before_reply_author_details' ); ?>

			<?php bbp_reply_author_link( array( 'sep' => '<br />', 'show_role' => true ) ); ?>

			<?php if ( bbp_is_user_keymaster() ) : ?>

				<?php do_action( 'bbp_theme_before_reply_author_admin_details' ); ?>

				<div class="bbp-reply-ip"><?php bbp_author_ip( bbp_get_reply_id() ); ?></div>

				<?php do_action( 'bbp_theme_after_reply_author_admin_details' ); ?>

			<?php endif; ?>

			<?php do_action( 'bbp_theme_after_reply_author_details' ); ?>

		</div><!-- .bbp-reply-author -->

	</td>

	<td>

		<div class="bbp-reply-title">

			<h3><?php _e( 'In reply to: ', 'bbpress' ); ?>
			<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink( bbp_get_reply_topic_id() ); ?>"><?php bbp_topic_title( bbp_get_reply_topic_id() ); ?></a></h3>

		</div><!-- .bbp-reply-title -->

		<div class="bbp-reply-content">

			<?php do_action( 'bbp_theme_before_reply_content' ); ?>

			<?php bbp_reply_content(); ?>

			<?php do_action( 'bbp_theme_after_reply_content' ); ?>

		</div><!-- .bbp-reply-content -->

	</td>

</tr>
