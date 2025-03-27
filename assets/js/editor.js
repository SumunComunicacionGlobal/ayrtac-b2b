wp.domReady( () => {
	wp.blocks.registerBlockStyle( 'core/list', [
		{
			name: 'default',
			label: 'Default',
			isDefault: true,
		},
		{
			name: 'separator-list',
			label: 'Separador',
		},
		{
			name: 'arrow-list',
			label: 'Flecha simple',
		},
		{
			name: 'arrow-separator-list',
			label: 'Flecha con separador',
		},
		{
			name: 'arrow-mini-separator-list',
			label: 'Flecha mini con separador',
		},

		{
			name: 'check-separator-list',
			label: 'Check con separador',
		},

		{
			name: 'check-list',
			label: 'Check simple',
		},
	] );
	wp.blocks.registerBlockStyle( 'core/button', [
		{
			name: 'with-arrow',
			label: 'Con Flecha',
		},
		{
			name: 'outline-with-arrow',
			label: 'Contorno con Flecha',
		}
	] );
	wp.blocks.registerBlockStyle( 'core/paragraph', [
		{
			name: 'default',
			label: 'Default',
			isDefault: true,
		},
		{
			name: 'title-has-image',
			label: 'Imagen integrada',
		},
	] );

	wp.blocks.registerBlockStyle( 'core/cover', [
		{
			name: 'cover-contain-background',
			label: 'Contain Background',
		},
	] );

	wp.blocks.registerBlockStyle( 'core/group', [
		{
			name: 'group-horizontal-scroll',
			label: 'Horizontal scroll',
		},
	] );
	
	wp.blocks.registerBlockVariation( 'core/group', [
		{
			name: 'group-horizontal-scroll',
			title: 'Group -- Horizontal scroll',
			description: 'Items in Horizontal scroll',
			isDefault: false,
			atttibutes: {
				className: 'is-style-group-horizontal-scroll',
			},
		},
	] );
} );

/* ADD data space height to wp-block-spacer in gutenberg */
wp.data.subscribe( function() {
	setTimeout( function() {
		const spacerBlocks = document.querySelectorAll( '.wp-block.wp-block-spacer' );

		for ( let item = 0; item < spacerBlocks.length; item++ ) {
			const block = spacerBlocks[ item ];

			const style = getComputedStyle( block ),
				height = parseInt( style.height ) || 0;

			block.querySelector( '.components-resizable-box__container' ).setAttribute( 'data-spaceheight', height + 'px' );
		}
	}, 1 );
} );