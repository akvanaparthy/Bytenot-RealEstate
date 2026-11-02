( function ( blocks, element, ServerSideRender, blockEditor, i18n ) {
	var el = element.createElement;
	blocks.registerBlockType( 'bytenot/search', {
		edit: function ( props ) {
			var parts = [ { label: i18n.__( 'Form', 'bytenot-core' ), value: 'form' }, { label: i18n.__( 'Quick-type tags', 'bytenot-core' ), value: 'tags' } ];
			return el( 'div', blockEditor.useBlockProps(), [
				el( blockEditor.InspectorControls, { key: 'c' }, el( wp.components.PanelBody, { title: i18n.__( 'Search', 'bytenot-core' ) },
					el( wp.components.SelectControl, { label: i18n.__( 'Part', 'bytenot-core' ), value: props.attributes.part, options: parts, onChange: function ( v ) { props.setAttributes( { part: v } ); } } ),
					el( wp.components.TextControl, { label: i18n.__( 'Title', 'bytenot-core' ), value: props.attributes.title, onChange: function ( v ) { props.setAttributes( { title: v } ); } } )
				) ),
				el( ServerSideRender, { key: 'r', block: 'bytenot/search', attributes: props.attributes } ),
			] );
		},
		save: function () { return null; },
	} );
} )( window.wp.blocks, window.wp.element, window.wp.serverSideRender, window.wp.blockEditor, window.wp.i18n );
