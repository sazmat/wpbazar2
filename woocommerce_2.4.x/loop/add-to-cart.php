<?php
/**
 * Loop Add to Cart
 *
 * @author        WooThemes
 * @package       WooCommerce/Templates
 * @version       2.1.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce;
//if ( ! $product->is_purchasable() ) return;

if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'yith-woocompare-view-table' || defined( 'DOING_AJAX' ) && DOING_AJAX ) {
    include $woocommerce->plugin_path() . '/templates/loop/add-to-cart.php';
    return;
}

$is_whislist = function_exists( 'yith_wcwl_is_wishlist' ) && yith_wcwl_is_wishlist();

$count_buttons = 5; // number of buttons to show
$details = sprintf( '<a href="%s" rel="nofollow" title="%s" class="details">%s</a>', get_permalink( $product->id ), apply_filters( 'yit_details_button', __( 'Details', 'yit' ) ), apply_filters( 'yit_details_button', __( 'Details', 'yit' ) ) );
$wishlist = do_shortcode( '[yith_wcwl_add_to_wishlist use_button_style="no"]' );
$compare = ( shortcode_exists( 'yith_compare_button' ) && ( get_option( 'yith_woocompare_compare_button_in_products_list' ) == 'yes' ) ) ? do_shortcode( '[yith_compare_button type="link"]' ) : '';
$is_wishlist = function_exists( 'yith_wcwl_is_wishlist' ) && yith_wcwl_is_wishlist();

$show_share = yit_get_option( 'shop-view-show-share' );
$sharethis_allowed = false;

$share = '';
if ( $show_share ) {

    if ( isset( $woocommerce->integrations->integrations['sharethis'] ) && $woocommerce->integrations->integrations['sharethis']->publisher_id ) {
        $sharethis_allowed = true;
        $share             = sprintf( '<a href="%s" rel="nofollow" title="%s" class="share" id="%s" onclick="return false;">%s</a>', '#', __( 'Share', 'yit' ), "share_$product->id", __( 'Share', 'yit' ) );
    }
    else {
        $share = sprintf( '<a href="#" class="yit_share share">' . __( 'Share', 'yit' ) . '</a>' );
    }

}

if ( ! yit_get_option( 'shop-view-show-details' ) ) {
    $details = '';
}
if ( ( ! ( yit_get_option( 'shop-view-show-wishlist' ) && get_option( 'yith_wcwl_enabled' ) == 'yes' ) ) || !function_exists( 'yith_wcwl_is_wishlist' ) ) {
    $wishlist = '';
}

$request_a_quote = yit_ywraq_print_button();

$buttons = array( $details, $wishlist, $compare, $share, $request_a_quote );
foreach ( array( 'details', 'wishlist', 'compare', 'share' , 'request_a_quote' ) as $var ) {
    if ( empty( ${$var} ) ) {
        $count_buttons --;
    }
}

if ( ! is_shop_enabled() || ! yit_get_option( 'shop-view-show-add-to-cart' ) || ! $product->is_purchasable() ) {
    $add_to_cart  = '';
    $out_of_stock = '';


    if ( $product->product_type == 'external' ) {
        $link        = apply_filters( 'external_add_to_cart_url', get_permalink( $product->id ) );
        $label       = apply_filters( 'external_add_to_cart_text', __( 'Read More', 'yit' ) );
        $add_to_cart = sprintf( '<a href="%s" rel="nofollow" class="view-options" title="%s">%s</a>', apply_filters( 'yit_external_add_to_cart_link_loop', $link, $product ), $label, $label );
    }

}
elseif ( ! $product->is_in_stock() ) {
    $add_to_cart = '';
    $label       = apply_filters( 'out_of_stock_add_to_cart_text', __( 'Out of stock', 'yit' ) );

    $out_of_stock_class = 'out-of-stock';
    if( yit_get_option( 'shop-layout', 'with-hover' ) == 'classic' ) {
        $out_of_stock_class .= ' button';
    }
    ?>

    <?php $out_of_stock = sprintf( '<a class="%s" title="%s">%s</a>', $out_of_stock_class, $label, $label ); ?>

<?php
}
else {
    ?>

    <?php

    $add_to_cart  = '';
    $out_of_stock = '';


    // plugin catalog mode fix
    if ( is_catalog_mode_installed() && ( !WC_Catalog_Restrictions_Filters::instance()->user_can_purchase( $product ) ) ) {

        global $wc_cvo;

        // add to cart alternative text
        $label = wptexturize($wc_cvo->setting('wc_cvo_atc_text'));
        if (!empty($label)) {

            $link = get_permalink($product->ID);

            $link_add = apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf( '<a href="%s" data-product_id="%s" class="button product_type_%s">%s</a>', $link, $product->id, $product->product_type, $label ), $product );

            $add_to_cart = $link_add;

        }
    }
    else {
        switch ( $product->product_type ) {
            case "variable" :
            case "variable-subscription" :
                $link        = apply_filters( 'variable_add_to_cart_url', get_permalink( $product->id ) );
                $label       = apply_filters( 'variable_add_to_cart_text', __( 'Select options', 'yit' ) );
                $add_to_cart = apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf( '<a href="%s" rel="nofollow" class="view-options" title="%s">%s</a>', $link, $label, $label ), $product );
                break;
            case "grouped" :
                $link        = apply_filters( 'grouped_add_to_cart_url', get_permalink( $product->id ) );
                $label       = apply_filters( 'grouped_add_to_cart_text', __( 'View options', 'yit' ) );
                $add_to_cart = apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf( '<a href="%s" rel="nofollow" class="view-options" title="%s">%s</a>', $link, $label, $label ), $product );
                break;
            case "subscription" :
                $link        = apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) );
                $woo_option  = get_option( 'woocommerce_subscriptions_add_to_cart_button_text' );
                $label       = $woo_option ? $woo_option : apply_filters( 'subscription_add_to_cart_text', __( 'Sign Up Now', 'yit' ) );
                $add_to_cart = apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-quantity="%s" class="add_to_cart_button button product_type_%s" title="%s">%s</a>', $link, $product->id, 1, $product->product_type, $label, $label ), $product );
                break;
            default :
                $link        = apply_filters( 'add_to_cart_url', esc_url( $product->add_to_cart_url() ) );
                $label       = apply_filters( 'add_to_cart_text', __( 'Add to cart', 'yit' ) );
                $quantity    = apply_filters( 'add_to_cart_quantity', ( get_post_meta( $product->id, 'minimum_allowed_quantity', true ) ? get_post_meta( $product->id, 'minimum_allowed_quantity', true ) : 1 ) );
                $add_to_cart = apply_filters( 'woocommerce_loop_add_to_cart_link', sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-quantity="%s" class="add_to_cart_button button product_type_%s" title="%s">%s</a>', $link, $product->id, $quantity, $product->product_type, $label, $label ), $product );
                break;
        }
    }

    ?>

<?php } ?>

<?php if ( ! empty( $add_to_cart ) || ! empty( $details ) ) : ?>
    <div class="product-actions-wrapper">
        <div class="product-actions">
            <?php if ( yit_get_option( 'shop-view-show-rating' ) && ! $is_wishlist ) {
                echo $product->get_rating_html( 'shop_loop' );
            } ?>
            <?php echo $out_of_stock;
            ?>
            <?php if ( $product->is_purchasable() || $product->product_type == 'external' ) {
                echo $add_to_cart;
            } ?>
            <?php

            if ( function_exists( 'YITH_WCQV_Frontend' ) && ! $is_whislist ) {

                $quick_view = YITH_WCQV_Frontend();

                $position = isset($quick_view->position) ? $quick_view->position : 'add-cart';

                if ( $position == 'add-cart' ) {
                    YITH_WCQV_Frontend()->yith_add_quick_view_button();
                }
            }

            ?>
            <?php if( ! $is_wishlist ): ?>
            <div class="buttons buttons_<?php echo $count_buttons ?> group">
                <?php echo implode( "\n", $buttons ) ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if ( $show_share ): ?>
    <?php if ( $sharethis_allowed ): ?>
        <?php yit_add_sharethis_button_js() ?>
        <?php else: ?>
        <div class="product-share"><?php echo do_shortcode( '[share title="' . __( 'Share on:', 'yit' ) . ' " icon_type="default" socials="facebook, twitter, google, pinterest"]' ); ?></div>
    <?php endif; ?>
<?php endif ?>

