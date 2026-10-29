/**
 * "Page design" panel in the block editor sidebar: field colour per page,
 * plus the front-page quotation (pages only). No build step: WordPress globals.
 */
( function ( wp, config ) {
	if ( ! wp || ! wp.plugins || ! config ) {
		return;
	}

	const el = wp.element.createElement;
	const __ = wp.i18n.__;
	const PluginDocumentSettingPanel =
		( wp.editor && wp.editor.PluginDocumentSettingPanel ) ||
		( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
	const { SelectControl, TextareaControl, TextControl } = wp.components;
	const { useSelect } = wp.data;
	const { useEntityProp } = wp.coreData;

	function FieldPanel() {
		const postType = useSelect(
			( select ) => select( 'core/editor' ).getCurrentPostType(),
			[]
		);
		const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );

		if ( ! PluginDocumentSettingPanel || config.postTypes.indexOf( postType ) === -1 || ! meta ) {
			return null;
		}

		const options = [ { label: __( 'Template default', 'twentytwentyfive-child' ), value: '' } ].concat(
			Object.keys( config.fields ).map( ( slug ) => ( { label: config.fields[ slug ], value: slug } ) )
		);
		const update = ( key ) => ( value ) => setMeta( { ...meta, [ key ]: value } );

		const children = [
			el( SelectControl, {
				key: 'field',
				label: __( 'Background field', 'twentytwentyfive-child' ),
				help: __( 'One pastel per page. Text always stays ink.', 'twentytwentyfive-child' ),
				value: meta.cc_field || '',
				options,
				onChange: update( 'cc_field' ),
				__nextHasNoMarginBottom: true,
			} ),
		];

		if ( postType === 'page' ) {
			children.push(
				el( TextareaControl, {
					key: 'quote',
					label: __( 'Quotation (front page)', 'twentytwentyfive-child' ),
					help: __( 'One sentence from a book, in the original language.', 'twentytwentyfive-child' ),
					value: meta.cc_quote || '',
					onChange: update( 'cc_quote' ),
				} ),
				el( TextControl, {
					key: 'source',
					label: __( 'Quotation source', 'twentytwentyfive-child' ),
					value: meta.cc_quote_source || '',
					onChange: update( 'cc_quote_source' ),
				} ),
				el( TextControl, {
					key: 'lang',
					label: __( 'Quotation language code (es, en, fr)', 'twentytwentyfive-child' ),
					value: meta.cc_quote_lang || '',
					onChange: update( 'cc_quote_lang' ),
				} )
			);
		}

		return el(
			PluginDocumentSettingPanel,
			{ name: 'cc-page-design', title: __( 'Page design', 'twentytwentyfive-child' ) },
			children
		);
	}

	wp.plugins.registerPlugin( 'cc-page-design', { render: FieldPanel } );
} )( window.wp, window.ccFieldPanel );
