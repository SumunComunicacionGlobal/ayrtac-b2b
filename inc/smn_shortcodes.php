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
    if ($field && $field['type'] === 'file') {
        $file_id = get_field($atts['field'], $term);
        if ($file_id) {
            return wp_get_attachment_image($file_id, 'full');
        }
    }

    return get_field($atts['field'], $term);
});