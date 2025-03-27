<?php
/**
 * Hooks to add content above the navigation block.
 *
 * Get the navigation block content and insert the content of a template at /template-hooks/navigation.php
 *
 * @package Enfrascados
 */

function add_content_above_to_navigation_block($block_content, $block) {
	// Verificar si el bloque es un bloque de navegación
	if ('core/navigation' === $block['blockName']) {
		// Capturar la salida de la plantilla
		ob_start();
		get_template_part('template-hooks/navigation');
		$template_content = ob_get_clean();

		// Buscar el div con la clase wp-block-navigation__responsive-container-content
		$pattern = '/(<div class="wp-block-navigation__responsive-container-content"[^>]*>)/';
		preg_match($pattern, $block_content, $matches, PREG_OFFSET_CAPTURE);

		if (!empty($matches)) {
			// Insertar el contenido de la plantilla después de la etiqueta de apertura del div
			$start_pos = $matches[0][1] + strlen($matches[0][0]);
			$block_content = substr_replace($block_content, $template_content, $start_pos, 0);
		}
	}

	return $block_content;
}

add_filter('render_block', 'add_content_above_to_navigation_block', 10, 2);


// Change icon from Navigation Block
function icon_nav_render_block_core (string $block_content, array $block)
{
	if ( 
		'core/navigatio' === $block['blockName'] 
		&& !is_admin() 
		&& !wp_is_json_request()
    ) {
		
		$svg = '<svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="false" focusable="false"><path d="M2.37681 24.0011C8.7924 23.7792 15.208 23.8069 21.6236 23.8069C24.3898 23.8069 24.3898 19.2316 21.6236 19.2316C15.208 19.2316 8.7924 19.2038 2.37681 19.4257C-0.376434 19.5227 -0.389421 24.0981 2.37681 24.0011Z" fill="white"/>
        <path d="M21.7533 10.2883C18.4027 10.2883 15.065 10.0249 11.7274 9.81688C10.1559 9.71982 8.59749 9.62277 7.02606 9.67823C5.66243 9.71982 4.24684 9.85847 2.89619 9.60891C1.75333 9.38707 0.584503 9.94166 0.259828 11.2034C-0.0258866 12.3125 0.610477 13.7961 1.75333 14.0179C3.40269 14.3229 5.01307 14.3229 6.67541 14.2675C8.23385 14.212 9.77931 14.2675 11.3378 14.3784C14.8053 14.5864 18.2728 14.8637 21.7533 14.8637C24.5196 14.8637 24.5196 10.2883 21.7533 10.2883Z" fill="white"/>
        <path d="M2.06493 4.59045C5.90909 4.53499 9.77922 4.65977 13.6234 4.95093C16.4805 5.17277 19.6493 6.06012 22.4286 4.88161C23.5065 4.42407 24.2597 3.34262 23.9221 2.06705C23.6364 0.971731 22.3636 0.0150588 21.2857 0.472598C18.9221 1.47086 15.9221 0.528057 13.3766 0.361679C9.62338 0.112113 5.84416 -0.0404004 2.06493 0.00119403C-0.688312 0.0566533 -0.688312 4.63204 2.06493 4.59045Z" fill="white"/>
        </svg>';

		$block_content = preg_replace('/<svg.*<\/svg>/s', $svg, $block_content);

		return $block_content;
	}

	if ( 
		'core/search' === $block['blockName'] 
		&& !is_admin() 
		&& !wp_is_json_request()
    ) {
		
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" width="25" height="23" viewBox="0 0 25 23" fill="none">
		<path d="M5.32733 18.7204C9.43369 20.7869 14.2395 19.7057 17.0656 16.2394C18.5521 14.4159 20.0173 12.5167 20.2402 10.1605C20.4346 8.10963 19.5929 6.07886 18.2913 4.46495C16.3922 2.11318 13.239 0.526022 10.0857 0.474751C6.38949 0.416793 2.91615 2.61697 1.32529 5.71551C-0.946009 10.1337 0.832148 16.1547 5.32496 18.7204C6.88263 19.6099 8.30279 17.3138 6.74986 16.4289C1.27788 13.3058 2.2452 4.33789 8.87655 3.21439C11.2451 2.81314 13.7961 3.82518 15.4605 5.40789C16.2737 6.1814 16.9091 7.16446 17.2268 8.20994C17.8076 10.1136 17.0466 11.732 15.8777 13.2746C14.7705 14.7369 13.7866 16.0232 11.9516 16.712C10.1165 17.4008 8.49009 17.3027 6.74986 16.4266C5.14952 15.6197 3.72225 17.9112 5.32496 18.7182L5.32733 18.7204Z" fill="white"/>
		<path d="M15.086 16.8193C17.3478 18.5937 19.6072 20.3704 21.869 22.1448C23.2536 23.2326 25.2618 21.3646 23.8653 20.2678C21.6035 18.4934 19.344 16.7168 17.0822 14.9424C15.6976 13.8546 13.6895 15.7226 15.086 16.8193Z" fill="white"/>
		</svg>';

		$block_content = preg_replace('/<svg.*<\/svg>/s', $svg, $block_content);

		return $block_content;
	}

	return $block_content;
}

add_filter('render_block', 'icon_nav_render_block_core', null, 2);