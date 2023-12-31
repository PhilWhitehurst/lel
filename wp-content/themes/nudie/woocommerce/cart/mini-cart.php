<?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

do_action( 'woocommerce_before_mini_cart' ); ?>

<div class="panel panel-info">

	<div class="panel-body">

		<ul class="cart_list product_list_widget <?php echo $args['list_class']; ?>">

			<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

				<?php 
				foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

					$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
					$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
 
					if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

						$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key );
						$thumbnail         = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
						$product_price     = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>

					<li>
						<?php
						echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
							'<a href="%s" class="remove" title="%s" data-product_id="%s" data-product_sku="%s"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>',
							esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
							__( 'Remove this item', 'woocommerce' ),
							esc_attr( $product_id ),
							esc_attr( $_product->get_sku() )
						), $cart_item_key );
						?>
						<?php if ( ! $_product->is_visible() ) { ?>
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>
						<?php } else { ?>
						<a href="<?php echo esc_url( $product_permalink ); ?>">
							<?php echo str_replace( array( 'http:', 'https:' ), '', $thumbnail ) . $product_name . '&nbsp;'; ?>
						</a>
						<?php } ?>

						<?php echo WC()->cart->get_item_data( $cart_item ); ?>

						<?php echo apply_filters( 'woocommerce_widget_cart_item_quantity', '<span class="quantity">' . sprintf( '%s &times; %s', $cart_item['quantity'], $product_price ) . '</span>', $cart_item, $cart_item_key ); ?>

					</li>
					<?php
				}
			}
		?>

			<?php else : ?>

				<li class="empty"><?php _e( 'No products in the cart.', 'woocommerce' ); ?></li>

			<?php endif; ?>

		</ul><!-- end product list -->

		<?php if ( sizeof( WC()->cart->get_cart() ) > 0 ) : ?>

			<p class="total"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?>:</strong> <?php echo WC()->cart->get_cart_subtotal(); ?></p>

			<?php do_action( 'woocommerce_widget_shopping_cart_before_buttons' ); ?>

			<p class="buttons">
				<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="btn btn-info"><?php _e( 'View Cart &rarr;', 'woocommerce' ); ?></a>
				<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary"><?php _e( 'Checkout &rarr;', 'woocommerce' ); ?></a>
			</p>

		<?php endif; ?>

	</div>

</div>

<?php do_action( 'woocommerce_after_mini_cart' ); ?>