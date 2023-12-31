<?php
/**
 * Variable subscription product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce-Subscriptions/Templates
 * @version 2.0.9
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$attribute_keys = array_keys( $attributes );
$user_id = get_current_user_id();

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart form-horizontal" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->id ); ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce-subscriptions' ); ?></p>
	<?php else : ?>
		<?php if ( ! $product->is_purchasable() && 0 != $user_id && 'no' != $product->limit_subscriptions && ( ( 'active' == $product->limit_subscriptions && wcs_user_has_subscription( $user_id, $product->id, 'on-hold' ) ) || $user_has_subscription = wcs_user_has_subscription( $user_id, $product->id, $product->limit_subscriptions ) ) ) : ?>
			<?php if ( 'any' == $product->limit_subscriptions && $user_has_subscription && ! wcs_user_has_subscription( $user_id, $product->id, 'active' ) && ! wcs_user_has_subscription( $user_id, $product->id, 'on-hold' ) ) : // customer has an inactive subscription, maybe offer the renewal button ?>
				<?php $resubscribe_link = wcs_get_users_resubscribe_link_for_product( $product->id ); ?>
				<?php if ( ! empty( $resubscribe_link ) ) : ?>
					<a href="<?php echo esc_url( $resubscribe_link ); ?>" class="button product-resubscribe-link"><?php esc_html_e( 'Resubscribe', 'woocommerce-subscriptions' ); ?></a>
				<?php endif; ?>
			<?php else : ?>
				<p class="limited-subscription-notice notice"><?php esc_html_e( 'You have an active subscription to this product already.', 'woocommerce-subscriptions' ); ?></p>
			<?php endif; ?>
		<?php else : ?>
			<div class="variations">
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<div class="form-group">
						<label class="col-sm-3 control-label" for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label>
						<div class="col-sm-9 value">
							<?php
							$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
							wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
							echo wp_kses( end( $attribute_keys ) === $attribute_name ? '<a class="reset_variations" href="#">' . __( 'Clear selection', 'woocommerce-subscriptions' ) . '</a>' : '', array( 'a' => array( 'class' => array(), 'href' => array() ) ) );
							?>
						</div>
					</div>
				<?php endforeach;?>
				</tbody>
			</table>
			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

			<div class="single_variation_wrap" style="display:none;">
				<?php
				/**
				 * woocommerce_before_single_variation Hook
				 */
				do_action( 'woocommerce_before_single_variation' );

				/**
				 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
				 * @since 2.4.0
				 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
				 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
				 */
				do_action( 'woocommerce_single_variation' );

				/**
				 * woocommerce_after_single_variation Hook
				 */
				do_action( 'woocommerce_after_single_variation' );
				?>
			</div>

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
