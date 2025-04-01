<?php
/**
 * ayrtac functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package ayrtac
 */

if ( ! function_exists( 'smn_support' ) ) :

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 *
	 * @return void
	 */
	function smn_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

		// Add support for excerpts in pages.
		add_post_type_support( 'page', 'excerpt' );
	}

endif;

add_action( 'after_setup_theme', 'smn_support' );

if ( ! function_exists( 'smn_styles' ) ) :

	/**
	 * Enqueue styles.
	 *
	 * @return void
	 */
	function smn_styles() {
		// Register theme stylesheet.
		$theme_version = wp_get_theme()->get( 'Version' );

		$version_string = is_string( $theme_version ) ? $theme_version : false;
		wp_register_style(
			'smn-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$version_string
		);

		// Enqueue theme stylesheet.
		wp_enqueue_style( 'smn-style' );
	}

endif;

add_action( 'wp_enqueue_scripts', 'smn_styles' );

// Enqueue scripts
require get_template_directory() . '/inc/smn_enqueue-scripts.php';

// Hooks to add content above the navigation block
require get_template_directory() . '/inc/smn_nav.php';

// Register blocks
require get_template_directory() . '/inc/smn_register-blocks.php';

// Shortcodes
require get_template_directory() . '/inc/smn_shortcodes.php';

// Hooks
require get_template_directory() . '/inc/smn_hooks.php';

// WooCommerce
require get_template_directory() . '/inc/smn_woocommerce.php';


/* Quitar <p> y <br/> de Contact Form 7 */
// add_filter('wpcf7_autop_or_not', '__return_false');


/**
 * Obtiene las clases de términos de taxonomía.
 *
 * @param string|array $class Una clase o un array de clases adicionales.
 * @param int $term_id El ID del término.
 * @param string $taxonomy El nombre de la taxonomía.
 * @return array Array de clases.
 */
function get_term_class($class, $term_id = 0, $taxonomy = '') {
    $classes = is_array($class) ? $class : explode(' ', $class);
    $term_id = (int) $term_id;

    if (!$term_id) {
        $term = get_queried_object();
        if ($term instanceof WP_Term && !is_wp_error($term)) {
            $term_id = $term->term_id;
            $taxonomy = $term->taxonomy;
        }
    }

    $term = get_term($term_id, $taxonomy);

    if ($term instanceof WP_Term && !is_wp_error($term)) {
        $classes[] = $term->taxonomy . '-' . $term->slug;
    }

    return $classes;
}
 


