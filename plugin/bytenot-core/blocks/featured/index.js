( function ( blocks, element, ServerSideRender, blockEditor, i18n ) {
	var el = element.createElement;
	blocks.registerBlockType( 'bytenot/featured', {
		edit: function ( props ) {
			return el( 'div', blockEditor.useBlockProps(), [
				el( blockEditor.InspectorControls, { key: 'c' }, el( wp.components.PanelBody, { title: i18n.__( 'Featured', 'bytenot-core' ) },
					el( wp.components.RangeControl, { label: i18n.__( 'Number of homes', 'bytenot-core' ), min: 1, max: 6, value: props.attributes.count, onChange: function ( v ) { props.setAttributes( { count: v } ); } } )
				) ),
				el( ServerSideRender, { key: 'r', block: 'bytenot/featured', attributes: props.attributes } ),
			] );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.i18n );
