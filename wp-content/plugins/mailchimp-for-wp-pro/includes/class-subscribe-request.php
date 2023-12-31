<?php

// prevent direct file access
if( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}

class MC4WP_Subscribe_Request extends MC4WP_Request {

	/**
	 * @var array
	 */
	private $list_fields_map = array();

	/**
	 * @var array
	 */
	private $unmapped_fields = array();

	/**
	 * @var array
	 */
	private $global_fields = array();

	/**
	 * Prepare data for MailChimp API request
	 * @return bool
	 */
	public function prepare() {
		$this->guess_fields();
		$mapped = $this->map_data();
		return $mapped;
	}

	/**
	 * Try to guess the values of various fields, if not given.
	 */
	protected function guess_fields() {
		// add some data to the posted data, like FNAME and LNAME
		$this->user_data = MC4WP_Tools::guess_merge_vars( $this->user_data );
	}

	/**
	 * Maps the received data to MailChimp lists
	 *
	 * @return array
	 */
	protected function map_data() {

		$mapper = new MC4WP_Field_Mapper( $this->user_data, $this->get_lists() );

		if( $mapper->success ) {
			$this->list_fields_map = $mapper->get_list_fields_map();
			$this->global_fields = $mapper->get_global_fields();
			$this->unmapped_fields = $mapper->get_unmapped_fields();
		} else {
			$this->message_type = $mapper->get_error_code();
		}

		return $mapper->success;
	}

	/**
	 * @return bool
	 */
	public function process() {
		$api = mc4wp_get_api();

		do_action( 'mc4wp_before_subscribe', $this->user_data['EMAIL'], $this->user_data, 0 );

		$result = false;
		$email_type = $this->get_email_type();

		// loop through selected lists
		foreach ( $this->list_fields_map as $list_id => $list_field_data ) {

			// allow plugins to alter merge vars for each individual list
			$list_merge_vars = $this->get_list_merge_vars( $list_id, $list_field_data );

			// send a subscribe request to MailChimp for each list
			$result = $api->subscribe( $list_id, $this->user_data['EMAIL'], $list_merge_vars, $email_type, $this->form->settings['double_optin'], $this->form->settings['update_existing'], $this->form->settings['replace_interests'], $this->form->settings['send_welcome'] );
			do_action( 'mc4wp_subscribe', $this->user_data['EMAIL'], $list_id, $list_merge_vars, $result, 'form', 'form', $this->form->ID );
		}

		do_action( 'mc4wp_after_subscribe', $this->user_data['EMAIL'], $this->user_data, 0, $result );

		// did we succeed in subscribing with the parsed data?
		if( ! $result ) {
			$this->message_type = ( $api->get_error_code() === 214 ) ? 'already_subscribed' : 'error';
			$this->mailchimp_error = $api->get_error_message();
		} else {
			$this->message_type = 'subscribed';

			// store user email in a cookie
			$this->set_email_cookie( $this->user_data['EMAIL'] );

			// send an email copy if that is desired
			if( $this->form->settings['send_email_copy'] ) {
				$this->send_email();
			}
		}

		$this->success = $result;

		return $result;
	}


	/**
	 * Adds global fields like OPTIN_IP, MC_LANGUAGE, OPTIN_DATE, etc to the list of user-submitted field data.
	 *
	 * @param string $list_id
	 * @param array $list_field_data
	 * @return array
	 */
	protected function get_list_merge_vars( $list_id, $list_field_data ) {

		$merge_vars = array();

		// add OPTIN_IP, we do this here as the user shouldn't be allowed to set this
		$merge_vars['OPTIN_IP'] = MC4WP_tools::get_client_ip();

		// make sure MC_LANGUAGE matches the requested format. Useful when getting the language from WPML etc.
		if( isset( $this->global_fields['MC_LANGUAGE'] ) ) {
			$merge_vars['MC_LANGUAGE'] = strtolower( substr( $this->global_fields['MC_LANGUAGE'], 0, 2 ) );
		}

		$merge_vars = array_merge( $merge_vars, $list_field_data );

		/**
		 * @filter `mc4wp_merge_vars`
		 * @expects array
		 * @param int $form_id
		 * @param string $list_id
		 *
		 * Can be used to filter the merge variables sent to a given list
		 */
		$merge_vars = apply_filters( 'mc4wp_merge_vars', $merge_vars, 0, $list_id );

		return (array) $merge_vars;
	}

	/**
	 * Stores the given email in a cookie for 30 days
	 *
	 * @param string $email
	 */
	protected function set_email_cookie( $email ) {

		/**
		 * @filter `mc4wp_cookie_expiration_time`
		 * @expects timestamp
		 * @default timestamp for 30 days from now
		 *
		 * Timestamp indicating when the email cookie expires, defaults to 30 days
		 */
		$expiration_time = apply_filters( 'mc4wp_cookie_expiration_time', strtotime( '+30 days' ) );

		setcookie( 'mc4wp_email', $email, $expiration_time, '/' );
	}


	/**
	 * Gets the email_type
	 *
	 * @return string The email type to use for subscription coming from this form
	 */
	protected function get_email_type( ) {

		$email_type = 'html';

		// get email type from form
		if( isset( $this->internal_data['email_type'] ) ) {
			$email_type = sanitize_text_field( $this->internal_data['email_type'] );
		}

		// allow plugins to override this email type
		$email_type = apply_filters( 'mc4wp_email_type', $email_type );

		return (string) $email_type;
	}

	/**
	 * Send an email with a subscription summary to a given email address
	 */
	protected function send_email() {

		// bail if receiver is empty
		if( '' === $this->form->settings['email_copy_receiver'] ) {
			return;
		}

		// email receiver
		$to = explode( ',', str_replace( ' ', '', $this->form->settings['email_copy_receiver'] ) );

		// email subject
		$subject = __( 'New MailChimp Sign-Up', 'mailchimp-for-wp' ) . ' - ' . get_bloginfo( 'name' );

		$mailchimp = new MC4WP_MailChimp();
		$referer = ( ! empty( $_SERVER['HTTP_REFERER'] ) ) ? $_SERVER['HTTP_REFERER'] : $_SERVER['REQUEST_URI'];

		// build email message
		ob_start();

		?>
		<h3>MailChimp for WordPress: <?php _e( 'New Sign-Up', 'mailchimp-for-wp' ); ?></h3>
		<p><?php printf( __( '<strong>%s</strong> signed-up at %s on %s using the form "%s".', 'mailchimp-for-wp' ), $this->user_data['EMAIL'], date( get_option( 'time_format' ) ), date( get_option( 'date_format' ) ), $this->form->name ); ?></p>
		<table cellspacing="0" cellpadding="10" border="0" style="border: 1px solid #EEEEEE;">
			<tbody>
			<?php foreach( $this->list_fields_map as $list_id => $field_data ) { ?>
				<tr>
					<td colspan="2"><h4 style="border-bottom: 1px solid #efefef; margin-bottom: 0; padding-bottom: 5px;"><?php echo __( 'List', 'mailchimp-for-wp' ) . ': ' . $mailchimp->get_list_name( $list_id ); ?></h4></td>
				</tr>
				<tr>
					<td><strong><?php _e( 'Email address', 'mailchimp-for-wp' ); ?>:</strong></td>
					<td><?php echo $this->user_data['EMAIL']; ?></td>
				</tr>
				<?php
				foreach( $field_data as $field_tag => $field_value ) {

					if( $field_tag === 'GROUPINGS' && is_array( $field_value ) ) {

						foreach( $field_value as $grouping ) {

							$groups = implode( ', ', $grouping['groups'] ); ?>
							<tr>
								<td><strong><?php echo $mailchimp->get_list_grouping_name( $list_id, $grouping['id'] ); ?>:</strong></td>
								<td><?php echo esc_html( $groups ); ?></td>
							</tr>
						<?php
						}

					} else {
						$field_name = $mailchimp->get_list_field_name_by_tag( $list_id, $field_tag );

						// convert array values to comma-separated string value
						if( is_array( $field_value ) ) {
							$field_value = implode( ', ', $field_value );
						}
						?>
						<tr>
							<td><strong><?php echo esc_html( $field_name ); ?>:</strong></td>
							<td><?php echo esc_html( $field_value ); ?></td>
						</tr>
					<?php
					}
				} ?>
			<?php } ?>

			<?php if( count( $this->unmapped_fields ) > 0 ) { ?>
				<tr>
					<td colspan="2"><h4 style="border-bottom: 1px solid #efefef; margin-bottom: 0; padding-bottom: 5px;"><?php _e( 'Other fields', 'mailchimp-for-wp' ); ?></h4></td>
				</tr>
				<?php
				foreach( $this->unmapped_fields as $field_tag => $field_value ) {

					// convert array values to comma-separated string value
					if( is_array( $field_value ) ) {
						$field_value = implode( ', ', $field_value );
					}
					?>
					<tr>
						<td><strong><?php echo esc_html( $field_tag ); ?>:</strong></td>
						<td><?php echo esc_html( $field_value ); ?></td>
					</tr>
					<?php
				} ?>
			<?php } ?>

			</tbody>
		</table>
		<br />

		<p><?php printf( __( 'User subscribed from %s from IP %s.', 'mailchimp-for-wp' ), esc_html( $referer ), MC4WP_Tools::get_client_ip() ); ?></p>

		<?php  if( $this->form->settings['double_optin'] ) { ?>
			<p style="color:#666;"><?php printf( __( 'Note that you\'ve enabled double opt-in for the "%s" form. The user won\'t be added to the selected MailChimp lists until they confirm their email address.', 'mailchimp-for-wp' ), $this->form->name ); ?></p>
		<?php } ?>
		<p style="color:#666;"><?php _e( 'This email was auto-sent by the MailChimp for WordPress plugin.', 'mailchimp-for-wp' ); ?></p>
		<?php
		$message = ob_get_contents();
		ob_end_clean();

		/**
		 * @filter mc4wp_email_summary_receiver
		 * @expects string|array String or array of emails
		 * @param   int     $form_id        The ID of the submitted form
		 * @param   string  $email          The email of the subscriber
		 * @param   array   $lists_data     Additional list fields, like FNAME etc (if any)
		 *
		 * Use to set email addresses to send the email summary to
		 */
		$receivers = apply_filters( 'mc4wp_email_summary_receiver', $to, $this->form->ID, $this->user_data['EMAIL'], $this->list_fields_map );

		/**
		 * @filter mc4wp_email_summary_subject
		 * @expects string|array String or array of emails
		 * @param   int     $form_id        The ID of the submitted form
		 * @param   string  $email          The email of the subscriber
		 * @param   array   $lists_data     Additional list fields, like FNAME etc (if any)
		 *
		 * Use to set subject of email summaries
		 */
		$subject = apply_filters( 'mc4wp_email_summary_subject', $subject, $this->form->ID, $this->user_data['EMAIL'], $this->list_fields_map );

		/**
		 * @filter mc4wp_email_summary_message
		 * @expects string|array String or array of emails
		 * @param   int     $form_id        The ID of the submitted form
		 * @param   string  $email          The email of the subscriber
		 * @param   array   $lists_data     Additional list fields, like FNAME etc (if any)
		 *
		 * Use to set or customize message of email summaries
		 */
		$message = apply_filters( 'mc4wp_email_summary_message', $message, $this->form->ID, $this->user_data['EMAIL'], $this->list_fields_map );


		// send email
		wp_mail( $receivers, $subject, $message, 'Content-Type: text/html' );
	}


}