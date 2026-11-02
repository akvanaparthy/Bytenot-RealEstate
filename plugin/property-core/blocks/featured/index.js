( function ( blocks, element, ServerSideRender, blockEditor, i18n ) {
	var el = element.createElement;
	blocks.registerBlockType( 'property-core/featured', {
		edit: function ( props ) {
			return el( 'div', blockEditor.useBlockProps(), [
				el( blockEditor.InspectorControls, { key: 'c' }, el( wp.components.PanelBody, { title: i18n.__( 'Featured', 'property-core' ) },
					el( wp.components.RangeControl, { label: i18n.__( 'Number of homes', 'property-core' ), min: 1, max: 6, value: props.attributes.count, onChange: function ( v ) { props.setAttributes( { count: v } ); } } )
				) ),
				el( ServerSideRender, { key: 'r', block: 'property-core/featured', attributes: props.attributes } ),
			] );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.i18n );
