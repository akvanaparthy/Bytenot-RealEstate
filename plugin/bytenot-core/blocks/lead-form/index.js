( function ( blocks, element, ServerSideRender, blockEditor, i18n ) {
	var el = element.createElement;
	blocks.registerBlockType( 'bytenot/lead-form', {
		edit: function ( props ) {
			var variants = [ { label: i18n.__( 'Inquiry', 'bytenot-core' ), value: 'inquiry' }, { label: i18n.__( 'Book a visit', 'bytenot-core' ), value: 'visit' } ];
			return el( 'div', blockEditor.useBlockProps(), [
				el( blockEditor.InspectorControls, { key: 'c' }, el( wp.components.PanelBody, { title: i18n.__( 'Lead form', 'bytenot-core' ) },
					el( wp.components.SelectControl, { label: i18n.__( 'Variant', 'bytenot-core' ), value: props.attributes.variant, options: variants, onChange: function ( v ) { props.setAttributes( { variant: v } ); } } ),
					el( wp.components.TextControl, { label: i18n.__( 'Intro line', 'bytenot-core' ), value: props.attributes.lede, onChange: function ( v ) { props.setAttributes( { lede: v } ); } } )
				) ),
				el( ServerSideRender, { key: 'r', block: 'bytenot/lead-form', attributes: props.attributes } ),
			] );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.i18n );
