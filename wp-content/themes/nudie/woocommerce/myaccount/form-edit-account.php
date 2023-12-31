<?php
/**
 * Edit account form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $woocommerce;
?>

<?php wc_print_notices(); ?>

<div class="page-header">
	<h2 class="entry-title"><?php echo apply_filters( 'woocommerce_my_account_my_address_title', __( 'Account Details', 'woocommerce' ) ); ?></h2>
</div>

<?php do_action( 'woocommerce_before_edit_account_form' ); ?>

<form action="" class="edit-account" method="post">

	<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

	<div class="form-row form-row-first form-group">
		<label for="account_first_name"><?php _e( 'First name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="form-control" name="account_first_name" id="account_first_name" value="<?php echo esc_attr( $user->first_name ); ?>" />
	</div>
	<div class="form-row form-row-last form-group">
		<label for="account_last_name"><?php _e( 'Last name', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="text" class="form-control" name="account_last_name" id="account_last_name" value="<?php echo esc_attr( $user->last_name ); ?>" />
	</div>
	<div class="form-row form-row-wide form-group">
		<label for="account_email"><?php _e( 'Email address', 'woocommerce' ); ?> <span class="required">*</span></label>
		<input type="email" class="form-control" name="account_email" id="account_email" value="<?php echo esc_attr( $user->user_email ); ?>" />
	</div>

	<fieldset>
		<legend><?php _e( 'Password Change', 'woocommerce' ); ?></legend>

		<div class="form-row form-row-thirds form-group">
			<label for="password_current"><?php _e( 'Current Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="form-control"  name="password_current" id="password_current" />
		</div>
		<div class="form-row form-row-thirds form-group">
			<label for="password_1"><?php _e( 'New Password (leave blank to leave unchanged)', 'woocommerce' ); ?></label>
			<input type="password" class="form-control"  name="password_1" id="password_1" />
		</div>
		<div class="form-row form-row-thirds form-group">
			<label for="password_2"><?php _e( 'Confirm New Password', 'woocommerce' ); ?></label>
			<input type="password" class="form-control"  name="password_2" id="password_2" />
		</div>
	</fieldset>

	<div class="clear"></div>

	<?php do_action( 'woocommerce_edit_account_form' ); ?>

	<div class="form-group">
		<?php wp_nonce_field( 'save_account_details' ); ?>
		<input type="submit" class="button btn btn-primary" name="save_account_details" value="<?php esc_attr_e( 'Save changes', 'woocommerce' ); ?>" />
		<input type="hidden" name="action" value="save_account_details" />
	</div>

	<?php do_action( 'woocommerce_edit_account_form_end' ); ?>

</form>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>