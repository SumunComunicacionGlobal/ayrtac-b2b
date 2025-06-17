<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

function fwp_add_facet_labels() {
  ?>
  <script>
    (function($) {
      $(document).on('facetwp-loaded', function() {
        $('.facetwp-facet').each(function() {
          var facet = $(this);
          var facet_name = facet.attr('data-name');
          var facet_type = facet.attr('data-type');
          var facet_label = FWP.settings.labels[facet_name];
          if ( ! ['pager','sort','reset'].includes( facet_type ) ) { // Add or remove excluded facet types to/from the array
            if (facet.closest('.facet-wrap').length < 1 && facet.closest('.facetwp-flyout').length < 1) {
              facet.wrap('<div class="facet-wrap"></div>');
              facet.before('<p class="facet-label">' + facet_label + '</p>');
            }
          }
        });
      });
    })(jQuery);
  </script>
  <?php
}
 
add_action( 'wp_head', 'fwp_add_facet_labels', 100 );

add_filter( 'facetwp_facet_html', function( $output, $params ) {
    $output = preg_replace( '/<span class="facetwp-counter">[^<]*<\/span>/', '', $output );
    if ( 'capacidad' == $params['facet']['name'] ) {
        $output = str_replace('</span>', '&nbsp;ml</span>', $output);
    }
    return $output;
}, 10, 2 );