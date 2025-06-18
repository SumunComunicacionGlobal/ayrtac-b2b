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

add_action( 'wp_head', function() {
  ?>
  <script>
    (function($) {
      $(document).on('facetwp-loaded', function() {
        if ( '' != FWP.buildQueryString() ) {
          $('html, body').animate({
            scrollTop: $('.facetwp-template').offset().top
          }, 500);
        }
      });
    })(jQuery);
  </script>
  <?php
}, 100 );

add_action( 'facetwp_scripts', function() {
  ?>
  <script>
    (function($) {
      document.addEventListener('facetwp-loaded', function() {
        $.each(FWP.settings.num_choices, function(key, val) {
 
          // assuming each facet is wrapped within a "facet-wrap" container element
          // this may need to change depending on your setup, for example:
          // change ".facet-wrap" to ".widget" if using WP text widgets
 
          var $facet = $('.facetwp-facet-' + key);
          var $wrap = $facet.closest('.facet-wrap');
          var $flyout = $facet.closest('.flyout-row');
          if ($wrap.length || $flyout.length) {
            var $which = $wrap.length ? $wrap : $flyout;
            (0 === val) ? $which.hide() : $which.show();
          }
        });
      });
    })(jQuery);
  </script>
  <?php
}, 100 );