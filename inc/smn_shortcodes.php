<?php 
// Shortcodes 

add_action( 'acf/init', 'set_acf_settings' );
function set_acf_settings() {
    acf_update_setting( 'enable_shortcode', true );
}

add_shortcode('current_term_field', function($atts) {
    $atts = shortcode_atts(array(
        'field' => '',
    ), $atts);

    if (empty($atts['field'])) {
        return '';
    }

    $term = get_queried_object();
    if (!$term || !isset($term->term_id)) {
        return '';
    }

    $field = get_field_object($atts['field'], $term);

    if ($field && $field['type'] === 'image') {
        $file_id = get_field($atts['field'], $term);
        if ($file_id) {
            return wp_get_attachment_image($file_id, 'full');
        }
    }

    return get_field($atts['field'], $term);
});

// New shortcode to return post title, term name, or false
add_shortcode('post_title', function() {
    if (is_singular()) {
        return get_the_title();
    } elseif (is_tax() || is_category() || is_tag()) {
        $term = get_queried_object();
        if ($term && isset($term->name)) {
            return $term->name;
        }
    }
    return false;
});

// Shortcode to display variation swatches for the 'pa_color' attribute
add_shortcode('loop_colores_producto', function() {

    global $product;
    
    if (!$product) {
        return '';
    }

    $colors = wp_get_post_terms($product->get_id(), 'pa_color', array('fields' => 'ids'));

    if (empty($colors)) {
        return '';
    }

    $output = '<div class="loop-product-colors">';
    foreach ($colors as $color) {
        $term = get_term($color);
        $swatch_id = get_term_meta($color, 'product_attribute_image', true);
        if ($swatch_id) {
            $output .= '<span class="loop-color-swatch" title="' . esc_attr($term->name) . '">' . wp_get_attachment_image( $swatch_id, 'thumbnail' ) . '</span>';
        }
    }
    $output .= '</div>';

    return $output;
});

// Shortcode to display a row with capacity, dimensions, and weight
add_shortcode('loop_atributos_producto', function() {
    global $product;
    
    if (!$product) {
        return '';
    }

    $output = '<div class="wp-block-group loop-product-attributes is-content-justification-space-between is-nowrap is-layout-flex">';

        $capacity = $product->get_attribute('pa_capacidad');
        $capacity = explode(', ', $capacity);
        sort($capacity, SORT_NUMERIC);
        // if ( count( $capacity) > 1 && ( min($capacity) != max($capacity) ) ) $capacity = [min($capacity) . '-' . max($capacity)];
        // $capacity = implode('-', $capacity);
        $capacity = implode(' · ', $capacity);


        if ( $product->is_type('variable') ) {
            $heights = [];
            $widths = [];
            $weight = [];
            $lengths = [];

            $variations = $product->get_available_variations();
            foreach ($variations as $variation) {
                $variation_id = $variation['variation_id'];
                $variation_product = wc_get_product($variation_id);

                if ($variation_product) {
                    $variation_dimensions = $variation_product->get_dimensions(false);
                    $variation_weight = $variation_product->get_weight();

                    if (!empty($variation_dimensions['height'])) {
                        $heights[] = $variation_dimensions['height'];
                    }
                    if (!empty($variation_dimensions['width'])) {
                        $widths[] = $variation_dimensions['width'];
                    }
                    if (!empty($variation_dimensions['length'])) {
                        $lengths[] = $variation_dimensions['length'];
                    }
                    if ($variation_weight) {
                        $weight[] = $variation_weight;
                    }
                }
            }

            $heights = array_unique(array_map('floatval', $heights), SORT_NUMERIC);
            $widths = array_unique(array_map('floatval', $widths), SORT_NUMERIC);
            $lengths = array_unique(array_map('floatval', $lengths), SORT_NUMERIC);
            $weight = array_unique(array_map('floatval', $weight), SORT_NUMERIC);

            if ( count( $heights) > 1 && ( min($heights) != max($heights) ) ) $heights = [min($heights) . '-' . max($heights)]; 
            if ( count( $widths) > 1 && ( min($widths) != max($widths) ) ) $widths = [min($widths) . '-' . max($widths)];
            if ( count( $lengths) > 1 && ( min($lengths) != max($lengths) ) ) $lengths = [min($lengths) . '-' . max($lengths)];
            if ( count( $weight) > 1 && ( min($weight) != max($weight) ) ) $weight = [min($weight) . '-' . max($weight)];

            $heights = implode('-', $heights);
            $widths = implode('-', $widths);
            $lengths = implode('-', $lengths);
            $weight = implode('-', $weight);

        } else {

            $dimensions = $product->get_dimensions(false);
            $heights = !empty($dimensions['height']) ? $dimensions['height'] : '';
            $widths = !empty($dimensions['width']) ? $dimensions['width'] : '';
            $lengths = !empty($dimensions['length']) ? $dimensions['length'] : '';
            $weight = $product->get_weight();
        }

        if ($capacity) {
            $output .= '<div class="loop-attribute-item"><span class="icon-capacity"></span> ' . esc_html($capacity) . '&nbsp;ml</div>';
        }
        if ($heights) {
            $output .= '<div class="loop-attribute-item"><span class="icon-height"></span> ' . esc_html($heights) . '&nbsp;' . get_option('woocommerce_dimension_unit') . '</div>';
        }
        if ($widths) {
            $output .= '<div class="loop-attribute-item"><span class="icon-width"></span> ' . esc_html($widths) . '&nbsp;' . get_option('woocommerce_dimension_unit') . '</div>';
        }
        if ($lengths) {
            $output .= '<div class="loop-attribute-item"><span class="icon-width"></span> ' . esc_html($lengths) . '&nbsp;' . get_option('woocommerce_dimension_unit') . '</div>';
        }
        if ($weight) {
            $output .= '<div class="loop-attribute-item"><span class="icon-weight"></span> ' . esc_html($weight) . '&nbsp;' . get_option('woocommerce_weight_unit') . '</div>';
        }
        
    $output .= '</div>';

    return $output;
});

// Shortcode to display the featured image linked to the corresponding page
add_shortcode('imagen_megamenu', function($atts) {
    $atts = shortcode_atts(array(
        'post_id' => 0,
    ), $atts);

    $post_id = intval($atts['post_id']);
    if (!$post_id) {
        return '';
    }

    $featured_image = get_the_post_thumbnail($post_id, 'medium_larga');
    if (!$featured_image) {
        return '';
    }

    $post_url = get_permalink($post_id);
    if (!$post_url) {
        return '';
    }

    return '<a href="' . esc_url($post_url) . '">' . $featured_image . '</a>';
});

add_shortcode( 'contenido_adicional_producto', 'smn_contenido_adicional_producto' );
function smn_contenido_adicional_producto( $atts ) {
    ob_start();
    ?>
    <div class="contenido-adicional-producto">
        <?php
        $croquis_id = get_field( 'product_sketch' );
        if ( $croquis_id ) {
            echo '<div class="product-sketch">';
                echo wp_get_attachment_image( $croquis_id, 'medium_large' );
            echo '</div>';
        }
        ?>
        
        <?php 
        $pdf_id = get_field( 'product_pdf' );
        if ( $pdf_id ) {
            $pdf_url = wp_get_attachment_url( $pdf_id );
            echo '<div class="wp-block-buttons">';
                echo '<div class="wp-block-button is-style-outline is-style-outline--2">';
                    echo '<a href="' . esc_url( $pdf_url ) . '" target="_blank" class="wp-block-button__link has-primary-color has-text-color">';
                        echo __('Descargar ficha en PDF', 'smn' ); 
                    echo '</a>';
                echo '</div>';
            echo '</div>';
        }
        ?>
        
        <?php if ( is_active_sidebar( 'cta-contacto-ficha-producto' ) ) {
            dynamic_sidebar( 'cta-contacto-ficha-producto' );
        } ?>
    </div>
    <?php
    return ob_get_clean();
}

// Shortcode to display a sidebar by ID
add_shortcode('sidebar', function($atts) {
    $atts = shortcode_atts(array(
        'id' => '',
    ), $atts);

    if (empty($atts['id']) || !is_active_sidebar($atts['id'])) {
        return '';
    }

    ob_start();
    dynamic_sidebar($atts['id']);
    return ob_get_clean();
});

// Filter to modify block content before rendering
add_filter('render_block', function($block_content, $block) {
    // Example: Add a custom wrapper to all paragraph blocks
    if ( is_singular( 'product' ) && $block['blockName'] === 'core/media-text') {

        $product_categories = get_the_terms( get_the_ID(), 'product_cat' );
        if ( $product_categories && ! is_wp_error( $product_categories ) ) {
            $first_category = $product_categories[0];
            $composicion_envases_personalizados_id = get_field( 'composicion_envases_personalizados', $first_category );

            // if ($composicion_envases_personalizados_id) {
            //     $image_url = wp_get_attachment_url($composicion_envases_personalizados_id);
            //     if ($image_url) {
            //         $block['attrs']['mediaUrl'] = $image_url;
            //         if (isset($block['innerBlocks'][0]['attrs']['src'])) {
            //             $block['innerBlocks'][0]['attrs']['src'] = $image_url;
            //         }
            //     }
            // }

            // Re-render the block with updated attributes
            // $block_content = render_block($block);
        }


    }

    return $block_content;
}, 10, 2);

add_shortcode( 'pagina_asociada', 'smn_pagina_asociada' );
function smn_pagina_asociada() {

    if ( !is_product_category() ) return false;

    $current_term = get_queried_object();
    $page_id = get_field( 'pagina_asociada', $current_term );
    if ( !$page_id ) return false;

    $page = get_post( $page_id );
    if ( !$page ) return false;

    $content = apply_filters( 'the_content', $page->post_content );
    
    return $content;

}