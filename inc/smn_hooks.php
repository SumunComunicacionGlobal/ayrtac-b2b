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