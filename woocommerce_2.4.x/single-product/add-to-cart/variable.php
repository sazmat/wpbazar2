<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.0
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

global $product;

$attribute_keys = array_keys( $attributes );

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->id ); ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
    <?php do_action( 'woocommerce_before_variations_form' ); ?>

    <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
        <p class="stock out-of-stock"><?php _e( 'This product is currently out of stock and unavailable.', 'yit' ); ?></p>
    <?php else : ?>

        <div class="variations">
            <?php $loop = 0; foreach ( $attributes as $attribute_name => $options ) : $loop ++; ?>
                <label for="<?php echo sanitize_title( $attribute_name ); ?>"><?php echo wc_attribute_label( $attribute_name ); ?></label>
                <div class="select-wrapper">
                    <?php
                    $selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
                    wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
                    ?>
                </div>
            <?php endforeach;?>
            <?php
            if ( yit_product_form_position_is( 'in-sidebar' ) && sizeof( $attributes ) == $loop ) {
                echo '<a class="reset_variations" href="#reset">' . __( 'Clear selection', 'yit' ) . '</a>';
            }

            ?>
        </div>

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

    <?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<div class="clear"></div>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
