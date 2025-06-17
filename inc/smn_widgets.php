<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ayrtac_register_sidebars() {

    register_sidebar( array(
        'name'          => __( 'CTA Contacto Ficha Producto', 'ayrtac' ),
        'id'            => 'cta-contacto-ficha-producto',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'CTA Personaliza tu producto en la Ficha Producto', 'ayrtac' ),
        'id'            => 'cta-personaliza-ficha-producto',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Filtro productos', 'ayrtac' ),
        'id'            => 'filtro-productos',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );


}
add_action( 'widgets_init', 'ayrtac_register_sidebars' );