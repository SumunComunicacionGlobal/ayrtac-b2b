<?php

// enable gutenberg for woocommerce
function activate_gutenberg_product( $can_edit, $post_type ) {
    if ( $post_type == 'product' ) {
        $can_edit = true;
    }
    return $can_edit;
}
// add_filter( 'use_block_editor_for_post_type', 'activate_gutenberg_product', 10, 2 );

add_filter( 'loop_shop_per_page', 'smn_redefine_products_per_page', 9999 );
function smn_redefine_products_per_page( $per_page ) {
   $per_page = 72;
   return $per_page;
}

// Replace WC breadcrumbs with rank math breadcrumbs
function woocommerce_breadcrumb() {
    if (function_exists('rank_math_the_breadcrumbs')) {
        rank_math_the_breadcrumbs();
    }
}

add_filter('rank_math/frontend/breadcrumb/items', function($crumbs) {

    foreach ($crumbs as $i => $crumb) {

       // Check if this crumb URL contains a path ("/catalogo/"), indicating a possible product_cat term
        if (isset($crumb[1]) && strpos($crumb[1], '/catalogo/') !== false) {

           
            // Try to extract the slug from the URL path
            $parts = parse_url($crumb[1]);

            if (!empty($parts['path'])) {
                $slug = basename(untrailingslashit($parts['path']));

                $term = get_term_by('slug', $slug, 'product_cat');
                $term_id = $term ? $term->term_id : 0;

            } else {
                $term_id = 0;
            }
            if ($term_id) {
                $pagina_asociada = get_field('pagina_asociada', 'product_cat_' . $term_id);
                if ($pagina_asociada) {
                    $crumbs[$i][0] = get_the_title($pagina_asociada);
                    $crumbs[$i][1] = get_permalink($pagina_asociada);
                }
            }
        }
    }
    return $crumbs;

}, 10, 1);

// enable taxonomy fields for woocommerce with gutenberg on
function enable_taxonomy_rest( $args ) {
    $args['show_in_rest'] = true;
    return $args;
}

// add_filter( 'woocommerce_taxonomy_args_product_cat', 'enable_taxonomy_rest' );
// add_filter( 'woocommerce_taxonomy_args_product_tag', 'enable_taxonomy_rest' );


// Make product category URLs not hierarchical
add_filter( 'woocommerce_taxonomy_args_product_cat', 'smn_modify_product_cat_taxonomy' );
function smn_modify_product_cat_taxonomy( $args ) {
    $args['rewrite']['hierarchical'] = false;
    return $args;
}

// Disable SKU display only in frontend
add_filter('wc_product_sku_enabled', 'disable_sku_in_frontend');
function disable_sku_in_frontend($enabled) {
    if (is_admin()) {
        return $enabled;
    }
    return false;
}

add_action( 'after_setup_theme', 'woocommerce_move_hooks', 99999 );
function woocommerce_move_hooks() {

    // Remove order select dropdown
    remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

}

add_action('woocommerce_before_shop_loop_item_title', function() {
    echo '<div class="product-title-wrapper">';
        echo '<div class="wp-block-group product-title-group is-content-justification-space-between is-layout-flex">';
}, 20);

add_action('woocommerce_after_shop_loop_item_title', function() {

            echo do_shortcode('[loop_colores_producto]');

        echo '</div>'; // Close wp-block-group

        echo do_shortcode('[loop_atributos_producto]');

    echo '</div>'; // Close product-title-wrapper

}, 5 );

// Disable the additional information tab
add_filter('woocommerce_product_tabs', 'remove_additional_information_tab', 98);
function remove_additional_information_tab($tabs) {
    unset($tabs['additional_information']);
    return $tabs;
}

// Hide prices
add_filter('woocommerce_get_price_html', 'hide_woocommerce_prices', 10, 2);
function hide_woocommerce_prices($price, $product) {
    return '';
}

// Hide clear button in variations form
add_filter('woocommerce_reset_variations_link', '__return_empty_string');

add_action('woocommerce_before_variations_form', function() {
    echo '<p class="before-variations-text info-tip mb-2">' . __('Juega con las combinaciones disponibles para ver sus dimensiones:', 'smn') . '</p>';
});

add_action( 'woocommerce_before_single_variation', 'smn_echo_variation_info' );
function smn_echo_variation_info() {
   global $product;
   if ( $product->is_type( 'variable' ) ) {

    echo '<div class="var_info"></div>';
    wc_enqueue_js( "
        $(document).on('found_variation', 'form.cart', function( event, variation ) { 
                $('.var_info').empty();
                const before = '<div class=\"var_info__item';
                const after = '</div>';
                var variation_description = variation.variation_description;
                variation_description = variation_description.replace(/<\/?[^>]+(>|$)/g, \"\");
                $('.var_info').append(before + ' pa_weight' + '\">' + variation.weight_html + after );
                $('.var_info').append(before + ' pa_dimensions' + '\">' + variation.dimensions_html + after);
                $('.title-variation-description').html(variation_description);
                $('input[name=\"your-subject\"]').val(variation_description);
        });
    " );

   }
}


add_filter('woocommerce_product_get_attribute', function($value, $instance, $attribute_name) {
    if ($attribute_name === 'pa_capacidad') {
        $value .= '&nbsp;ml';
    }
    return $value;
}, 10, 3);

add_filter( 'woocommerce_display_product_attributes', 'smn_add_capacidad_ml', 10, 2 );
function smn_add_capacidad_ml( $product_attributes, $product ) {
    if ( ! is_product() ) {
        return $product_attributes;
    }

    if ( isset( $product_attributes['attribute_pa_capacidad'] ) ) :
        $unidades = '&nbsp;ml';
        if ( str_contains( $product_attributes['attribute_pa_capacidad']['value'], '</a>' ) ) {
            $product_attributes['attribute_pa_capacidad']['value'] = str_replace( '</a></p>', $unidades . '</a></p>', $product_attributes['attribute_pa_capacidad']['value'] );
        } else {
            $product_attributes['attribute_pa_capacidad']['value'] = str_replace( '</p>', $unidades . '</p>', $product_attributes['attribute_pa_capacidad']['value'] );
        }
    endif;

    if ( isset( $product_attributes['attribute_pa_color'] ) ) :
        $product_attributes['attribute_pa_color']['value'] = do_shortcode('[loop_colores_producto]');
    endif;

    return $product_attributes;
}



add_filter('render_block', function($block_content, $block) {

    if ( ! is_product() ) {
        return $block_content;
    }

    global $product;
    if ( ! $product->is_type( 'variable' ) ) {
        return $block_content;
    }

    if ($block['blockName'] === 'core/post-title') {
        if ( isset($block['attrs']['level']) && $block['attrs']['level'] === 1 ) {
            $block_content .= '<div class="title-variation-description"></div>';
        }
    }
    return $block_content;
}, 10, 2);

add_filter('render_block', function($block_content, $block) {

    if ( $block['blockName'] === 'core/post-excerpt' ) {
        if ( 
            isset( $block['attrs']['__woocommerceNamespace']) && 
            $block['attrs']['__woocommerceNamespace'] == 'woocommerce/product-collection/product-summary' 
        ) {
            $block_content = do_shortcode('[loop_atributos_producto]');
        } else {
            global $product;
            if ( $product->is_type( 'variable' ) ) {
                return $block_content;
            }
            ob_start();
            do_action( 'woocommerce_product_additional_information', $product );
            $block_content .= ob_get_clean();
        }
    }

    if (
        $block['blockName'] === 'core/post-title' && 
        isset( $block['attrs']['__woocommerceNamespace']) && 
        $block['attrs']['__woocommerceNamespace'] == 'woocommerce/product-collection/product-title' 
    ) {
        $block_content .= do_shortcode('[loop_colores_producto]');
    }

    return $block_content;
}, 10, 2);

add_filter( 'render_block', 'smn_change_categories_dropdown_title', 10, 2 );
function smn_change_categories_dropdown_title( $block_content, $block ) {
    if ( $block['blockName'] === 'woocommerce/product-categories' ) {
        $block_content = str_replace( esc_html__( 'Select a category', 'woocommerce' ), __( 'Ver otros catálogos', 'smn' ), $block_content );
    }
    return $block_content;
}

// add_filter('render_block', 'smn_intercalar_imagenes_en_listados_de_producto', 10, 2);
function smn_intercalar_imagenes_en_listados_de_producto( $block_content, $block ) {
    // Verificar si estamos en una página de categoría de producto
    if (!is_product_category() || $block['blockName'] !== 'woocommerce/product-collection') {
        return $block_content;
    }

    // si la url tiene un parámetro que empiece por filter_, anular acción
    if ( isset($_SERVER['QUERY_STRING']) && preg_match('/^filter_/', $_SERVER['QUERY_STRING']) ) {
        return $block_content;
    }

    $current_term = get_queried_object();
    $additional_images = get_field( 'additional_images', $current_term );

    if ( !$additional_images ) {
        return $block_content;
    }

    $offset = 2; // Número de productos antes de insertar la imagen adicional
    $gap = 7; // Número de productos entre cada imagen adicional

    // Dividir el contenido en productos individuales
    $products = explode('</li>', $block_content);

    // Crear el contenido del <li> adicional
    $additional_li_before = '<li class="imagen-adicional">';
    $additional_li_after = '</li>';

    // Insertar las imágenes adicionales en las posiciones definidas por el offset y el gap
    $position = $offset;
    $image_index = 0;

    while (isset($products[$position]) && isset($additional_images[$image_index])) {
        $products[$position] .= $additional_li_before;
        $products[$position] .= wp_get_attachment_image($additional_images[$image_index], 'medium_large');
        $products[$position] .= $additional_li_after;

        $position += $gap;
        $image_index++;
    }



    // Reconstruir el contenido del bloque
    $block_content = implode('</li>', $products);

    return $block_content;
}

add_filter('render_block', function($block_content, $block) {


    if (
        is_tax('product_cat') && 
        $block['blockName'] === 'core/cover' && 
        isset($block['attrs']['metadata']['patternName']) && 
        $block['attrs']['metadata']['patternName'] === 'ayrtac/hero-sector'
    ) {

        $term = get_queried_object();
        if ($term && isset($term->term_id)) {
            $thumbnail_id = get_term_meta($term->term_id, 'thumbnail_id', true);
            if ($thumbnail_id) {
                $img = wp_get_attachment_image( $thumbnail_id, 'full', false, ['class' => 'wp-block-cover__image-background'] );
                $block_content = str_replace( 'id="hero">', 'id="hero">' . $img, $block_content );
            }
        }

    }
    return $block_content;
}, 10, 2);

// Add ml to the attribute terms in the REST API response
add_filter('rest_prepare_pa_capacidad', function($response, $attribute, $request) {
    // Verificar si el atributo es "capacidad"
    if ($attribute->slug === 'pa_capacidad') {
        // Obtener los términos del atributo
        $terms = get_terms([
            'taxonomy' => 'pa_capacidad',
            'hide_empty' => false,
        ]);

        // Ordenar los términos por valor numérico
        usort($terms, function($a, $b) {
            return intval($a->name) - intval($b->name);
        });

        // Modificar los nombres de los términos para añadir " ml"
        foreach ($terms as $term) {
            $term->name .= ' ml';
        }

        // Reemplazar los términos en la respuesta
        $response->data['terms'] = $terms;
    }

    return $response;
}, 10, 3);


/*

// Make all products non-purchasable
add_filter('woocommerce_is_purchasable', '__return_false');
add_filter( 'woocommerce_variation_is_purchasable', '__return_false' );

// Remove add to cart button for variable products keeping attribute select fields
add_action( 'woocommerce_single_product_summary', 'remove_variable_add_to_cart_button', 1 );
function remove_variable_add_to_cart_button() {
    global $product;

    // For variable product types (keeping attribute select fields)
    if( $product->is_type( 'variable' ) && is_user_logged_in() ) {
        remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
    }
}

// Disable WooCommerce checkout and cart pages
add_action('template_redirect', 'disable_woocommerce_checkout_cart_pages');
function disable_woocommerce_checkout_cart_pages() {
    if (is_checkout() || is_cart()) {
        wp_redirect(home_url());
        exit;
    }
}




// Hide Select none option if only one option is available
add_filter('woocommerce_dropdown_variation_attribute_options_args', 'hide_single_variation_option_label');
function hide_single_variation_option_label($args) {
    // if (count($args['options']) === 1) {
        $args['show_option_none'] = false;
    // }
    return $args;
}

// Disable clear button in variations form
add_filter('woocommerce_reset_variations_link', '__return_empty_string');

// Hide full variation table row in variations form if only one option is available
add_action('wp_footer', 'hide_single_variation_table_row_js');
function hide_single_variation_table_row_js() {
    ?>
    <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('.variations_form table.variations select').each(function() {
                var $select = $(this);
                var $row = $select.closest('tr');
                if ($select.length && $select.find('option').length === 2) {
                    $row.hide();
                }
            });
        });
    </script>
    <?php
}

*/

/* add_action('woocommerce_single_product_summary', 'display_product_variations_table', 15);
function display_product_variations_table() {
    global $product;

    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        if (!empty($available_variations)) {
            // Obtener configuraciones de WooCommerce para unidades de dimensiones y peso
            $dimension_unit = get_option('woocommerce_dimension_unit');
            $weight_unit = get_option('woocommerce_weight_unit');

            // Obtener todas las etiquetas de atributos
            $attributes = $product->get_variation_attributes();
            $attribute_labels = array();
            foreach ($attributes as $attribute_name => $options) {
                $label = wc_attribute_label($attribute_name);
                if ( 'pa_capacidad' === $attribute_name ) {
                    $label .= ' (ml)';
                }
                $attribute_labels[$attribute_name] = $label;
            }

            // Initialize columns data
            $columns_data = array();
            $columns_data['description'] = array();
            foreach ($attribute_labels as $attribute_name => $label) {
                $columns_data[$attribute_name] = array();
            }
            $columns_data['height'] = array();
            $columns_data['width'] = array();
            $columns_data['weight'] = array();

            // Collect data for each variation
            foreach ($available_variations as $variation) {
                $variation_obj = new WC_Product_Variation($variation['variation_id']);
                $columns_data['description'][] = $variation_obj->get_description();
                foreach ($variation_obj->get_variation_attributes() as $attribute_name => $attribute_value) {
                    $attribute_name = str_replace('attribute_', '', $attribute_name);
                    $term = get_term_by('slug', $attribute_value, $attribute_name);
                    $columns_data[$attribute_name][] = $term ? $term->name : '';
                }
                $columns_data['height'][] = $variation_obj->get_height();
                $columns_data['width'][] = $variation_obj->get_width();
                $columns_data['weight'][] = $variation_obj->get_weight();
            }

            // Remove columns with all empty cells
            foreach ($columns_data as $column_name => $data) {
                if (empty(array_filter($data))) {
                    unset($columns_data[$column_name]);
                    unset($attribute_labels[$column_name]);
                }
            }

            echo '<table class="variations-table">';
            echo '<thead><tr>';
            echo '<th class="description">' . esc_html(__('Descripción', 'smn')) . '</th>';
            foreach ($attribute_labels as $attribute_name => $label) {
                echo '<th class="' . esc_attr($attribute_name) . '">' . esc_html($label) . '</th>';
            }
            if (isset($columns_data['height'])) {
                echo '<th class="height is-numeric">' . esc_html(__('Altura', 'smn')) . ' (' . esc_html($dimension_unit) . ')</th>';
            }
            if (isset($columns_data['width'])) {
                echo '<th class="width is-numeric">' . esc_html(__('ø/Anchura', 'smn')) . ' (' . esc_html($dimension_unit) . ')</th>';
            }
            if (isset($columns_data['weight'])) {
                echo '<th class="weight is-numeric">' . esc_html(__('Peso', 'smn')) . ' (' . esc_html($weight_unit) . ')</th>';
            }
            echo '</tr></thead>';
            echo '<tbody>';

            foreach ($available_variations as $variation) {
                $variation_obj = new WC_Product_Variation($variation['variation_id']);
                echo '<tr>';
                echo '<td class="description">' . esc_html($variation_obj->get_description()) . '</td>';
                foreach ($attribute_labels as $attribute_name => $label) {
                    $attribute_value = $variation_obj->get_attribute($attribute_name);
                    $term = get_term_by('slug', $attribute_value, $attribute_name);
                    echo '<td class="' . esc_attr($attribute_name) . '">' . esc_html($term ? $term->name : '') . '</td>';
                }
                if (isset($columns_data['height'])) {
                    echo '<td class="height is-numeric">' . esc_html($variation_obj->get_height()) . '</td>';
                }
                if (isset($columns_data['width'])) {
                    echo '<td class="width is-numeric">' . esc_html($variation_obj->get_width()) . '</td>';
                }
                if (isset($columns_data['weight'])) {
                    echo '<td class="weight is-numeric">' . esc_html($variation_obj->get_weight()) . '</td>';
                }
                echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
        }
    } elseif ($product->is_type('simple')) {

        // Obtener configuraciones de WooCommerce para unidades de dimensiones y peso
        $dimension_unit = get_option('woocommerce_dimension_unit');
        $weight_unit = get_option('woocommerce_weight_unit');

        // Obtener todas las etiquetas de atributos
        $attributes = $product->get_attributes();
        $attribute_labels = array();
        foreach ($attributes as $attribute_name => $attribute) {
            $label = wc_attribute_label($attribute_name);
            if ( 'pa_capacidad' === $attribute_name ) {
                $label .= ' (ml)';
            }
            $attribute_labels[$attribute_name] = $label;
        }

        // Initialize columns data
        $columns_data = array();
        $columns_data['description'] = array();
        foreach ($attribute_labels as $attribute_name => $label) {
            $columns_data[$attribute_name] = array();
        }
        $columns_data['height'] = array();
        $columns_data['width'] = array();
        $columns_data['weight'] = array();

        // Collect data for the product
        $columns_data['description'][] = $product->get_name();
        foreach ($attributes as $attribute_name => $attribute) {
            $terms = wc_get_product_terms($product->get_id(), $attribute_name, array('fields' => 'names'));
            $columns_data[$attribute_name] = $terms;
        }
        $columns_data['height'][] = $product->get_height();
        $columns_data['width'][] = $product->get_width();
        $columns_data['weight'][] = $product->get_weight();

        // Remove columns with all empty cells
        foreach ($columns_data as $column_name => $data) {
            if (empty(array_filter($data))) {
                unset($columns_data[$column_name]);
                unset($attribute_labels[$column_name]);
            }
        }

        echo '<table class="variations-table">';
        echo '<thead><tr>';
        echo '<th class="description">' . esc_html(__('Descripción', 'smn')) . '</th>';
        foreach ($attribute_labels as $attribute_name => $label) {
            echo '<th class="' . esc_attr($attribute_name) . '">' . esc_html($label) . '</th>';
        }
        if (isset($columns_data['height'])) {
            echo '<th class="height is-numeric">' . esc_html(__('Altura', 'smn')) . ' (' . esc_html($dimension_unit) . ')</th>';
        }
        if (isset($columns_data['width'])) {
            echo '<th class="width is-numeric">' . esc_html(__('ø/Anchura', 'smn')) . ' (' . esc_html($dimension_unit) . ')</th>';
        }
        if (isset($columns_data['weight'])) {
            echo '<th class="weight is-numeric">' . esc_html(__('Peso', 'smn')) . ' (' . esc_html($weight_unit) . ')</th>';
        }
        echo '</tr></thead>';
        echo '<tbody>';
        echo '<tr>';
        echo '<td class="description">' . esc_html($product->get_name()) . '</td>';
        foreach ($attribute_labels as $attribute_name => $label) {
            $terms = wc_get_product_terms($product->get_id(), $attribute_name, array('fields' => 'names'));
            echo '<td class="' . esc_attr($attribute_name) . '">' . esc_html(implode(', ', $terms)) . '</td>';
        }
        if (isset($columns_data['height'])) {
            echo '<td class="height is-numeric">' . esc_html($product->get_height()) . '</td>';
        }
        if (isset($columns_data['width'])) {
            echo '<td class="width is-numeric">' . esc_html($product->get_width()) . '</td>';
        }
        if (isset($columns_data['weight'])) {
            echo '<td class="weight is-numeric">' . esc_html($product->get_weight()) . '</td>';
        }
        echo '</tr>';
        echo '</tbody>';
        echo '</table>';
    }
}
*/

