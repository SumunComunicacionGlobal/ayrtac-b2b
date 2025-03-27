<?php
/**
 * Register ACF Blocks
 *
 * @link https://www.advancedcustomfields.com/resources/acf_register_block_type/
 *
 * @return void
 */


function smn_register_blocks() {
    register_block_type( get_stylesheet_directory() . '/custom-blocks/marquee' );
    register_block_type( get_stylesheet_directory() . '/custom-blocks/carousel', [
        'render_callback' => ['Carousel_Slider_Block', 'render_carousel']
    ]);
    register_block_type( get_stylesheet_directory() . '/custom-blocks/slide' );
}

add_action( 'init', 'smn_register_blocks' );

/**
 * Register custom blocks script
 */
function smn_register_block_script() {
    wp_register_script( 'marquee-js', get_template_directory_uri() . '/custom-blocks/marquee/marquee.js', [ 'jquery', 'acf' ] );
    
}
 add_action( 'init', 'smn_register_block_script' );