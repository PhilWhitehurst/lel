<?php
/**
 * Empty cart page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

wc_print_notices();
?>

<div class="alert alert-warning">
	<?php _e( 'Your cart is currently empty.', 'woocommerce' ) ?>
</div>

<?php do_action('woocommerce_cart_is_empty'); ?>

<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?>
<div class="return-to-shop">
	<a class="btn btn-default" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
		<?php _e( 'Return To Shop', 'woocommerce' ); ?>
	</a>
</div>
<?php endif; ?>