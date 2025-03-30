<?php

// Agrega la imagen de la categoría de WooCommerce al bloque core/cover si no tiene una imagen destacada
function custom_add_category_image_to_cover_block( $block_content, $block ) {
    // Verifica si estamos en una categoría de producto de WooCommerce y si el bloque es core/cover
    if ( is_product_category() && $block['blockName'] === 'core/cover' ) {
        $category = get_queried_object();
        if ( $category && isset( $category->term_id ) ) {
            $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
            if ( $thumbnail_id ) {
                $image_url = wp_get_attachment_image_url( $thumbnail_id, 'full' );

                // Si el bloque no tiene una imagen destacada, agrega la imagen de la categoría
                if ( strpos( $block_content, 'wp-block-cover__image-background' ) === false ) {
                    $block_content = preg_replace(
                        '/(<div class="wp-block-cover[^>]*>)/',
                        '${1}<img class="wp-block-cover__image-background" src="' . esc_url( $image_url ) . '" alt="" />',
                        $block_content,
                        1
                    );
                }
            }
        }
    }
    return $block_content;
}
add_filter( 'render_block', 'custom_add_category_image_to_cover_block', 10, 2 );