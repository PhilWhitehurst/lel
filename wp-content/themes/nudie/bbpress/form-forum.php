<?php

/**
 * New/Edit Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php if ( bbp_is_forum_edit() ) : ?>

<div id="bbpress-forums">

	<?php bbp_breadcrumb(); ?>

	<?php bbp_single_forum_description( array( 'forum_id' => bbp_get_forum_id() ) ); ?>

<?php endif; ?>

<?php if ( bbp_current_user_can_access_create_forum_form() ) : ?>

	<div id="new-forum-<?php bbp_forum_id(); ?>" class="bbp-forum-form">

		<form id="new-post" name="new-post" method="post" action="<?php the_permalink(); ?>" role="form">

			<?php do_action( 'bbp_theme_before_forum_form' ); ?>

			<fieldset>

				<legend>

					<?php
						if ( bbp_is_forum_edit() )
							printf( __( 'Now Editing &ldquo;%s&rdquo;', 'bbpress' ), bbp_get_forum_title() );
						else
							bbp_is_single_forum() ? printf( __( 'Create New Forum in &ldquo;%s&rdquo;', 'bbpress' ), bbp_get_forum_title() ) : _e( 'Create New Forum', 'bbpress' );
					?>

				</legend>

				<?php do_action( 'bbp_theme_before_forum_form_notices' ); ?>

				<?php if ( !bbp_is_forum_edit() && bbp_is_forum_closed() ) : ?>

					<div class="alert alert-info">
						<?php _e( 'This forum is closed to new content, however your account still allows you to do so.', 'bbpress' ); ?>
					</div>

				<?php endif; ?>

				<?php if ( current_user_can( 'unfiltered_html' ) ) : ?>

					<div class="alert alert-danger">
						<?php _e( 'Your account has the ability to post unrestricted HTML content.', 'bbpress' ); ?>
					</div>

				<?php endif; ?>

				<?php do_action( 'bbp_template_notices' ); ?>

				<div>

					<?php do_action( 'bbp_theme_before_forum_form_title' ); ?>

					<div class="form-group">
						<label for="bbp_forum_title"><?php printf( __( 'Forum Name (Maximum Length: %d):', 'bbpress' ), bbp_get_title_max_length() ); ?></label><br />
						<input type="text" id="bbp_forum_title" class="form-control" value="<?php bbp_form_forum_title(); ?>" tabindex="<?php bbp_tab_index(); ?>" size="40" name="bbp_forum_title" maxlength="<?php bbp_title_max_length(); ?>" />
					</div>

					<?php do_action( 'bbp_theme_after_forum_form_title' ); ?>

					<?php do_action( 'bbp_theme_before_forum_form_content' ); ?>

					<?php bbp_the_content( array( 'context' => 'forum' ) ); ?>

					<?php do_action( 'bbp_theme_after_forum_form_content' ); ?>

					<?php if ( ! ( bbp_use_wp_editor() || current_user_can( 'unfiltered_html' ) ) ) : ?>

					<p class="form-allowed-tags">
						<label><?php _e( 'You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:','bbpress' ); ?></label><br />
						<code><?php bbp_allowed_tags(); ?></code>
					</p>

					<?php endif; ?>

					<?php do_action( 'bbp_theme_before_forum_form_type' ); ?>

					<div class="form-group">
						<label for="bbp_forum_type"><?php _e( 'Forum Type:', 'bbpress' ); ?></label><br />
						<?php bbp_form_forum_type_dropdown(); ?>
					</div>

					<?php do_action( 'bbp_theme_after_forum_form_type' ); ?>

					<?php do_action( 'bbp_theme_before_forum_form_status' ); ?>

					<div class="form-group">
						<label for="bbp_forum_status"><?php _e( 'Status:', 'bbpress' ); ?></label><br />
						<?php bbp_form_forum_status_dropdown(); ?>
					</div>

					<?php do_action( 'bbp_theme_after_forum_form_status' ); ?>

					<?php do_action( 'bbp_theme_before_forum_form_status' ); ?>

					<div class="form-group">
						<label for="bbp_forum_visibility"><?php _e( 'Visibility:', 'bbpress' ); ?></label><br />
						<?php bbp_form_forum_visibility_dropdown(); ?>
					</div>

					<?php do_action( 'bbp_theme_after_forum_visibility_status' ); ?>

					<?php do_action( 'bbp_theme_before_forum_form_parent' ); ?>

					<div class="form-group">
						<label for="bbp_forum_parent_id"><?php _e( 'Parent Forum:', 'bbpress' ); ?></label><br />

						<?php
							bbp_dropdown( array(
								'select_id' => 'bbp_forum_parent_id',
								'show_none' => __( '(No Parent)', 'bbpress' ),
								'selected'  => bbp_get_form_forum_parent(),
								'exclude'   => bbp_get_forum_id()
							) );
						?>
					</p>

					<?php do_action( 'bbp_theme_after_forum_form_parent' ); ?>

					<?php do_action( 'bbp_theme_before_forum_form_submit_wrapper' ); ?>

					<div class="form-group">

						<?php do_action( 'bbp_theme_before_forum_form_submit_button' ); ?>

						<button type="submit" tabindex="<?php bbp_tab_index(); ?>" id="bbp_forum_submit" name="bbp_forum_submit" class="btn btn-default"><?php _e( 'Submit', 'bbpress' ); ?></button>

						<?php do_action( 'bbp_theme_after_forum_form_submit_button' ); ?>

					</div>

					<?php do_action( 'bbp_theme_after_forum_form_submit_wrapper' ); ?>

				</div>

				<?php bbp_forum_form_fields(); ?>

			</fieldset>

			<?php do_action( 'bbp_theme_after_forum_form' ); ?>

		</form>

	</div>

<?php elseif ( bbp_is_forum_closed() ) : ?>

	<div id="no-forum-<?php bbp_forum_id(); ?>" class="bbp-no-forum">
		<div class="alert alert-warning">
			<?php printf( __( 'The forum &#8216;%s&#8217; is closed to new content.', 'bbpress' ), bbp_get_forum_title() ); ?>
		</div>
	</div>

<?php else : ?>

	<div id="no-forum-<?php bbp_forum_id(); ?>" class="bbp-no-forum">
		<div class="alert alert-warning">
			<?php is_user_logged_in() ? _e( 'You cannot create new forums.', 'bbpress' ) : _e( 'You must be logged in to create new forums.', 'bbpress' ); ?>
		</div>
	</div>

<?php endif; ?>

<?php if ( bbp_is_forum_edit() ) : ?>

</div>

<?php endif; ?>
