<?php

// Agrega el video de fondo de la categoría de WooCommerce al bloque core/cover si tiene el campo cta_video
function custom_add_category_video_to_cover_block( $block_content, $block ) {
    // Verifica si estamos en una categoría de producto de WooCommerce y si el bloque es core/cover
    if ( is_product_category() && $block['blockName'] === 'core/cover' ) {
        $category = get_queried_object();
        if ( $category && isset( $category->term_id ) ) {
            $cta_video_id = get_term_meta( $category->term_id, 'cta_video', true );
            if ( $cta_video_id ) {
                $cta_video_url = wp_get_attachment_url( $cta_video_id );

                // Si el bloque tiene un video de fondo, reemplázalo con el video de la categoría
                if ( strpos( $block_content, '<video' ) !== false && $cta_video_url ) {

                    $block_content = preg_replace(
                        '/<video class="wp-block-cover__video-background[^"]*"[^>]*>.*?<\/video>/s',
                        '<video class="wp-block-cover__video-background intrinsic-ignore" autoplay loop muted playsinline>
                            <source src="' . esc_url( $cta_video_url ) . '" type="video/mp4">
                        </video>',
                        $block_content,
                        1
                    );
                }
            }
        }
    }
    return $block_content;
}
add_filter( 'render_block', 'custom_add_category_video_to_cover_block', 10, 2 );

// Muestra los campos personalizados clásicos cuando ACF está activo
add_filter( 'acf/settings/remove_wp_meta_box', '__return_false' );

// Muestra el personalizador de apariencia
add_action( 'customize_register', '__return_true' );

add_action( 'wp_footer', function() {

    ?>
    <script>
        jQuery(document).ready(function($) {
            var productName = $('h1.wp-block-post-title').text();
            if (productName) {
                $('input[name="your-subject"]').val(productName);
            }
        });
    </script>
    <?php

});

//add ACF rule
add_filter('acf/location/rule_values/post_type', 'acf_location_rule_values_Post');
function acf_location_rule_values_Post( $choices ) {
	$choices['product_variation'] = 'Product Variation';
    //print_r($choices);
    return $choices;
}

// Agrega un filtro para el bloque de consulta de WordPress
// que muestra los posts relacionados en la página de un post y los filtra por categorías
add_filter('render_block_data', function ($parsed_block) {
    if (
        is_single() &&
        isset($parsed_block['blockName']) &&
        $parsed_block['blockName'] === 'core/query' &&
        isset($parsed_block['attrs']['className']) &&
        strpos($parsed_block['attrs']['className'], 'is-style-is-related-posts') !== false
    ) {
        $category_ids = wp_get_post_categories(get_the_ID());

        if (!empty($category_ids)) {
            $parsed_block['attrs']['query']['categoryIds'] = $category_ids;
            $parsed_block['attrs']['query']['exclude'] = [get_the_ID()];
            $parsed_block['attrs']['query']['sticky'] = '';
            $parsed_block['attrs']['query']['perPage'] = 6;
        }
    }

    return $parsed_block;
});

add_filter('render_block', function($block_content, $block) {

    if (
        is_product() &&
        isset($block['blockName']) &&
        in_array($block['blockName'], ['core/post-excerpt', 'core/post-content'] )
    ) {
        if (!current_user_can('edit_posts')) {
            // Hide excerpt and content for users who can't manage options
            return '';
        }
    }
    return $block_content;
}, 10, 2);