<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$product_cat = get_field( 'product_cat' );
if ( ! $product_cat ) {
    return;
}

$args = array(
    'post_type' => 'product',
    'posts_per_page' => -1,
    'facetwp' => true, // Enable FacetWP filtering
    'tax_query' => array(
        array(
            'taxonomy' => 'product_cat',
            'field'    => 'term_id',
            'terms'    => $product_cat,
        ),
    ),
);

$products = new WP_Query( $args );

// Get WooCommerce columns setting (default to 4 if not set)
$columns = apply_filters('loop_shop_columns', 4);

if ( $products->have_posts() ) {

    // Support custom "anchor" values.
    $anchor = '';
    if ( ! empty( $block['anchor'] ) ) {
        $anchor = 'id="' . esc_attr( $block['anchor'] ) . '" ';
    }

    // Create class attribute allowing for custom "className" and "align" values.
    $class_name = '';
    if ( ! empty( $block['className'] ) ) {
        $class_name .= ' ' . $block['className'];
    }
    if ( ! empty( $block['align'] ) ) {
        $class_name .= ' align' . $block['align'];
    }
    ?>
    <div class="acf-block-products-grid <?php echo esc_attr( $class_name ); ?>" <?php echo $anchor; ?>>
        <div class="products-grid-filter-bar">

            <div class="wp-block-group is-content-justification-space-between is-nowrap is-layout-flex">
                <div class="wp-block-buttons is-layout-flex">
                    <div class="wp-block-button is-style-outline is-style-outline--1">
                        <a class="facetwp-flyout-open wp-block-button__link has-small-font-size has-custom-font-size" href="javascript:;" style="border-style:none;border-width:0px;padding-right:0rem;padding-left:0rem">
                            <img width="16" height="16" class="wp-image-1866" style="width: 16px;" src="https://envasesybotellasayrtac.com/wp-content/uploads/icono-filtro.svg" alt=""> 
                            <?php echo facetwp_i18n( __( 'Ver filtros', 'ayrtac' ) ); ?>
                        </a>

                        <?php echo do_shortcode( '[facetwp facet="contador_resultados"]' ); ?>
                        
                    </div>
                </div>
            </div>

        </div>

        <div class="woocommerce products-grid-wrapper">
            <?php if ( is_active_sidebar( 'filtro-productos' ) ) : ?>
                <div class="products-grid-sidebar">
                    <?php dynamic_sidebar( 'filtro-productos' ); ?>
                </div>
            <?php endif; ?>

            <ul class="products products-grid facetwp-template">
                <?php while ( $products->have_posts() ) : $products->the_post(); ?>
                    <?php wc_get_template_part( 'content', 'product' ); ?>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <?php

    wp_reset_postdata();
}