<?php
/**
 * Enqueue scripts and styles.
 */

 function smn_scripts() {

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'ayrtac-js', get_template_directory_uri() . '/assets/js/ayrtac.js', array(), true );
	
	if ( has_block( 'cb/carousel' ) ) {
        wp_enqueue_style( 'slick-css', get_template_directory_uri() . '/assets/slick/slick.min.css' );
        wp_enqueue_script( 'slick-js', get_template_directory_uri() . '/assets/slick/slick.min.js', array('jquery'), null, true );
        wp_enqueue_script( 'slick-init-js', get_template_directory_uri() . '/assets/slick/init.js', array('jquery'), null, true );
    }

}
add_action( 'wp_enqueue_scripts', 'smn_scripts' );

/** 
* Gutenberg scripts and styles
*/
function smn_gutenberg_scripts() {

	wp_enqueue_script(
		'be-editor', 
		get_stylesheet_directory_uri() . '/assets/js/editor.js', 
		array( 'wp-blocks', 'wp-dom', 'wp-dom-ready', 'wp-edit-post' ), 
		filemtime( get_stylesheet_directory() . '/assets/js/editor.js' ),
		true
	);
}
add_action( 'enqueue_block_editor_assets', 'smn_gutenberg_scripts' );
