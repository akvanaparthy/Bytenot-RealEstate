( function ( blocks, element, ServerSideRender, blockEditor, i18n ) {
	var el = element.createElement;
	blocks.registerBlockType( 'property-core/lead-form', {
		edit: function ( props ) {
			var variants = [ { label: i18n.__( 'Inquiry', 'property-core' ), value: 'inquiry' }, { label: i18n.__( 'Book a visit', 'property-core' ), value: 'visit' } ];
			return el( 'div', blockEditor.useBlockProps(), [
				el( blockEditor.InspectorControls, { key: 'c' }, el( wp.components.PanelBody, { title: i18n.__( 'Lead form', 'property-core' ) },
					el( wp.components.SelectControl, { label: i18n.__( 'Variant', 'property-core' ), value: props.attributes.variant, options: variants, onChange: function ( v ) { props.setAttributes( { variant: v } ); } } ),
					el( wp.components.TextControl, { label: i18n.__( 'Intro line', 'property-core' ), value: props.attributes.lede, onChange: function ( v ) { props.setAttributes( { lede: v } ); } } )
				) ),
				el( ServerSideRender, { key: 'r', block: 'property-core/lead-form', attributes: props.attributes } ),
			] );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.i18n );
