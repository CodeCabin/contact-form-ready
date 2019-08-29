var el = wp.element.createElement,
	registerBlockType = wp.blocks.registerBlockType,
	ServerSideRender = wp.components.ServerSideRender,
	TextControl = wp.components.TextControl,
	SelectControl = wp.components.SelectControl,
	InspectorControls = wp.editor.InspectorControls,
	AlignmentToolbar = wp.editor.AlignmentToolbar,
	BlockControls = wp.editor.BlockControls;
	

/*
 * Registers the block in JavaScript.
 */
registerBlockType( 'contact-form-ready/cfr-gutenberg-block', {
	title: 'Contact Form Ready Block',
	icon: 'email-alt',
	category: 'common',
	
/*
*	Our block is renderred on the PHP side so here we are 
*   setting our controls and attributes.
*/
	edit: function( props ) {

		var alignment = props.attributes.alignment;

        function onChangeAlignment( newAlignment ) {
            props.setAttributes( { alignment: newAlignment === undefined ? 'none' : newAlignment } );
        }

		return [
			/*
			 *	Automatically calls our block renderer from our PHP when it needs
			 *  to get an updated view of the block
			 */
			el( ServerSideRender, {
				block: 'contact-form-ready/cfr-gutenberg-block',
				attributes: props.attributes,
			} ),

			/*
			 *	Adds our block controls, in this case we are simply adding
			 *  the ability to text align left, center and right using the 
			 *  Gutenberg controls
			 */
			el( BlockControls,{ 
				key: 'controls' },
                el(
                    AlignmentToolbar, {
                        value: alignment,
                        onChange: onChangeAlignment,
                    }
                )
            ),

			/*
			 * Adds inspector controls, right hand side of block/page
			 */
			el( InspectorControls, {},
				el( SelectControl, {
					label: 'Please Choose A Contact Form',
					value: props.attributes.cfid,
					onChange: ( value ) => { props.setAttributes( { cfid: value } ); },
					options: cfr_localized_forms,
				} )
			), 
		];
	},

	// We're going to be rendering in PHP, so save() can just return null.
	save: function() {
		return null;
	},
} );