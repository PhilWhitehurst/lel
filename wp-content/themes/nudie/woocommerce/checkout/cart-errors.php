<?php
/**
 * Cart errors page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

<?php wc_print_notices(); ?>

<div class="alert alert-warning"><?php _e( 'There are some issues with the items in your cart (shown above). Please go back to the cart page and resolve these issues before checking out.', 'woocommerce' ) ?></div>

<?php do_action('woocommerce_cart_has_errors'); ?>

<a class="btn btn-default wc-backward" href="<?php echo esc_url( wc_get_page_permalink('cart') ); ?>"><?php _e( '&larr; Return To Cart', 'woocommerce' ) ?></a>