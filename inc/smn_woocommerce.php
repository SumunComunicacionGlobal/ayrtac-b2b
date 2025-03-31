<?php

// enable gutenberg for woocommerce
function activate_gutenberg_product( $can_edit, $post_type ) {
    if ( $post_type == 'product' ) {
        $can_edit = true;
    }
    return $can_edit;
}

add_filter( 'use_block_editor_for_post_type', 'activate_gutenberg_product', 10, 2 );


// enable taxonomy fields for woocommerce with gutenberg on
function enable_taxonomy_rest( $args ) {
    $args['show_in_rest'] = true;
    return $args;
}

add_filter( 'woocommerce_taxonomy_args_product_cat', 'enable_taxonomy_rest' );
add_filter( 'woocommerce_taxonomy_args_product_tag', 'enable_taxonomy_rest' );


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

add_action('woocommerce_single_product_summary', 'display_product_variations_table', 15);
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

// Add inline styles for variations-table
add_action('wp_head', 'add_custom_styles');
function add_custom_styles() {
    echo '<style>
    </style>';
}