( function ( blocks, element, ServerSideRender, blockEditor, i18n ) {
	var el = element.createElement;
	blocks.registerBlockType( 'property-core/search', {
		edit: function ( props ) {
			var parts = [ { label: i18n.__( 'Form', 'property-core' ), value: 'form' }, { label: i18n.__( 'Quick-type tags', 'property-core' ), value: 'tags' } ];
			return el( 'div', blockEditor.useBlockProps(), [
				el( blockEditor.InspectorControls, { key: 'c' }, el( wp.components.PanelBody, { title: i18n.__( 'Search', 'property-core' ) },
					el( wp.components.SelectControl, { label: i18n.__( 'Part', 'property-core' ), value: props.attributes.part, options: parts, onChange: function ( v ) { props.setAttributes( { part: v } ); } } ),
					el( wp.components.TextControl, { label: i18n.__( 'Title', 'property-core' ), value: props.attributes.title, onChange: function ( v ) { props.setAttributes( { title: v } ); } } )
				) ),
				el( ServerSideRender, { key: 'r', block: 'property-core/search', attributes: props.attributes } ),
			] );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.i18n );
