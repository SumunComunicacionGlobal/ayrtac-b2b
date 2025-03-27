<?php

// Añade un wrap con clase "wp-block-details--content" al bloque core/details. Ver estilos en details.scss
function wrap_details_content($block_content, $block) {
    
    if ($block['blockName'] === 'core/details') {
        // Usa DOMDocument para manipular el HTML
        $dom = new DOMDocument();
        @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $block_content, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Encuentra el elemento <details>
        $details = $dom->getElementsByTagName('details')->item(0);

        if ($details) {
            $div = $dom->createElement('div');
            $div->setAttribute('class', 'wp-block-details--content');

            // Mueve todos los hijos de <details> (excepto <summary>) al nuevo div
            while ($details->childNodes->length > 1) {
                $div->appendChild($details->childNodes->item(1));
            }

            // Añade el nuevo div al <details>
            $details->appendChild($div);
        }

        // Guarda el HTML modificado
        $block_content = $dom->saveHTML($details);
    }

    return $block_content;
}
add_filter('render_block', 'wrap_details_content', 10, 2);