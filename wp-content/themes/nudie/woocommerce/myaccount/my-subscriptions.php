<?php
/**
 * My Subscriptions section on the My Account page
 *
 * @author 		Prospress
 * @category 	WooCommerce Subscriptions/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div class="woocommerce_account_subscriptions">

	<div class="page-header">
		<h2 class="entry-title"><?php esc_html_e( 'My Subscriptions', 'woocommerce-subscriptions' ); ?></h2>
	</div>

	<?php if ( ! empty( $subscriptions ) ) : ?>
	<table class="shop_table shop_table_responsive my_account_subscriptions my_account_orders table table-striped">

	<thead>
		<tr>
			<th class="subscription-id order-number"><span class="nobr"><?php esc_html_e( 'Subscription', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-status order-status"><span class="nobr"><?php esc_html_e( 'Status', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-next-payment order-date"><span class="nobr"><?php esc_html_e( 'Next Payment', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-total order-total"><span class="nobr"><?php esc_html_e( 'Total', 'woocommerce-subscriptions' ); ?></span></th>
			<th class="subscription-actions order-actions">&nbsp;</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach ( $subscriptions as $subscription_id => $subscription ) : ?>
		<tr class="order">
			<td class="subscription-id order-number" data-title="<?php esc_attr_e( 'ID', 'woocommerce-subscriptions' ); ?>">
				<a href="<?php echo esc_url( $subscription->get_view_order_url() ); ?>"><?php echo esc_html( $subscription->get_order_number() ); ?></a>
				<?php do_action( 'woocommerce_my_subscriptions_after_subscription_id', $subscription ); ?>
			</td>
			<td class="subscription-status order-status" style="text-align:left; white-space:nowrap;" data-title="<?php esc_attr_e( 'Status', 'woocommerce-subscriptions' ); ?>">
				<?php echo esc_attr( wcs_get_subscription_status_name( $subscription->get_status() ) ); ?>
			</td>
			<td class="subscription-next-payment order-date" data-title="<?php esc_attr_e( 'Next Payment', 'woocommerce-subscriptions' ); ?>">
				<?php echo esc_attr( $subscription->get_date_to_display( 'next_payment' ) ); ?>
				<?php if ( ! $subscription->is_manual() && $subscription->has_status( 'active' ) && $subscription->get_time( 'next_payment' ) > 0 ) : ?>
					<?php $payment_method_to_display = sprintf( __( 'Via %s', 'woocommerce-subscriptions' ), $subscription->get_payment_method_to_display() ); ?>
					<?php $payment_method_to_display = apply_filters( 'woocommerce_my_subscriptions_payment_method', $payment_method_to_display, $subscription ); ?>
				&nbsp;<small><?php echo esc_attr( $payment_method_to_display ); ?></small>
				<?php endif; ?>
			</td>
			<td class="subscription-total order-total">
				<?php echo wp_kses_post( $subscription->get_formatted_order_total() ); ?>
			</td>
			<td class="subscription-actions order-actions">
				<a href="<?php echo esc_url( $subscription->get_view_order_url() ) ?>" class="button btn btn-default btn-xs view"><?php esc_html_e( 'View', 'woocommerce-subscriptions' ); ?></a>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
	</table>
	<?php else : ?>

		<p class="no_subscriptions"><?php printf( esc_html__( 'You have no active subscriptions. Sign up for your first subscription %shere%s.', 'woocommerce-subscriptions' ), '<a href="' . esc_url( "\/plans\/" ) . '">', '</a>' ); ?></p>

	<?php endif; ?>

</div>

<?php
