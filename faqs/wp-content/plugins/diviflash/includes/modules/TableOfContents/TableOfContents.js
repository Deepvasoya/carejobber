import React, { Component } from 'react'
import utility from '../../../scripts/df_scripts/utilities'
import './style.css'

export default class TableOfContents extends Component {
	static slug = 'difl_table_of_contents'
	et_utils = window.ET_Builder.API.Utils

	constructor( props ) {
		super( props );
		this.wrapperRef = React.createRef();
	}

	static css( props, moduleInfo ) {
		const { generateStyles } = window.ET_Builder.API.Utils
		const additionalCss = []
		const icons = [
			{ key: 'expand_icon', selector: '%%order_class%% .heading_container .icon .expand_icon.et-pb-icon' },
			{ key: 'collapse_icon', selector: '%%order_class%% .heading_container .icon .collapse_icon.et-pb-icon' },
			{ key: 'single_icon', selector: '%%order_class%% .heading_container .icon.single_icon .et-pb-icon' },
		]
		const spaces = [ 'margin', 'padding' ]
		const styles = [
			{
				name: 'header_bg_color',
				selector: '%%order_class%% .heading_container',
				cssProperty: 'background-color',
			},
			{
				name: 'expand_icon_color',
				selector: '%%order_class%% .heading_container .expand_icon.et-pb-icon',
				cssProperty: 'color',
			},
			{
				name: 'collapse_icon_color',
				selector: '%%order_class%% .heading_container .collapse_icon.et-pb-icon',
				cssProperty: 'color',
			},
			{
				name: 'single_icon_color',
				selector: '%%order_class%% .heading_container .icon.single_icon .et-pb-icon',
				cssProperty: 'color',
			},
			{
				name: 'expand_icon_size',
				selector: '%%order_class%% .heading_container .expand_icon.et-pb-icon',
				cssProperty: 'font-size',
			},
			{
				name: 'collapse_icon_size',
				selector: '%%order_class%% .heading_container .collapse_icon.et-pb-icon',
				cssProperty: 'font-size',
			},
			{
				name: 'single_icon_size',
				selector: '%%order_class%% .heading_container .icon.single_icon .et-pb-icon',
				cssProperty: 'font-size',
			},
			{
				name: 'title_icon_heading_space',
				selector: '%%order_class%% .heading_container',
				cssProperty: 'gap',
			},
			{
				name: 'active_icon_marker_color',
				selector: '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul .toc-li-wrapper:has( > a.active) span',
				cssProperty: 'color',
				important: true,
			},
			{
				name: 'active_background_color',
				selector: '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li .toc-li-wrapper:has( > a.active)',
				cssProperty: 'background-color',
			},
			{
				name: 'active_link_color',
				selector: '%%order_class%% .difl_toc_main_container .body_container ul.difl--toc--ul li .toc-li-wrapper a.active',
				cssProperty: 'color',
				important: true,
			},
			{
				name: 'title_icon_gap',
				selector: '.difl_table_of_contents .difl_toc_main_container .heading_container',
				cssProperty: 'gap',
			},
			{
				name: 'body_bg_color',
				selector: '%%order_class%% .difl_toc_main_container .body_container',
				cssProperty: 'background-color',
			},
		];

		if ( 'on' === props.use_content_height ) {
			styles.push( {
				name: 'content_height',
				selector: '%%order_class%% .difl_toc_main_container .body_container',
				cssProperty: 'height',
				important: true,
			} );
		}
		const headings = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

		headings.forEach( heading => {

			const icon = `marker_icon_${ heading }`;
			const size = `marker_icon_size_${ heading }`;
			const color = `marker_icon_color_${ heading }`;
			const level = heading.replace( 'h', '' );
			const selector = `.difl_table_of_contents .body_container.icon .difl_heading_level_${ level } > li > .toc-li-wrapper > span.et-pb-icon`;
			styles.push( {
				name: size,
				selector,
				cssProperty: 'font-size',
			}, );
			styles.push( {
				name: color,
				selector,
				cssProperty: 'color',
			}, );

			icons.push( {
				key: icon,
				selector
			} );

			styles.push( {
				name: `marker_icon_space_heading_${ heading }`,
				selector: `.difl_table_of_contents .body_container.icon .difl_heading_level_${ level } > li > .toc-li-wrapper > a`,
				cssProperty: 'margin-left'
			} );

			styles.push( {
				name: `heading_bg_${ heading }`,
				selector: `.difl_table_of_contents .body_container ul.difl--toc--ul.difl_heading_level_${ level } > li > .toc-li-wrapper`,
				cssProperty: 'background-color'
			} );

			spaces.forEach( type => {
				const key = `heading_spacing_${ heading }_${ type }`;
				utility.process_margin_padding( {
					props,
					key,
					additionalCss,
					selector: `.difl_table_of_contents .body_container .difl_heading_level_${ level } > li`,
					type,
				} )
			} )
		} );

		styles.forEach( item => {
			const args = Object.assign( { attrs: props }, item )
			additionalCss.push( generateStyles( args ) )
		} )
		spaces.forEach( type => {
			const key = `header_spacing_${ type }`
			utility.process_margin_padding( {
				props,
				key,
				additionalCss,
				selector: '%%order_class%% .heading_container',
				type,
			} )
		} )
		spaces.forEach( type => {
			const key = `content_spacing_${ type }`
			utility.process_margin_padding( {
				props,
				key,
				additionalCss,
				selector: '%%order_class%% .body_container',
				type,
			} )
		} )
		spaces.forEach( type => {
			if ( type === 'margin' ) {
				return;
			}
			const key = `active_spacing_padding`
			utility.process_margin_padding( {
				props,
				key,
				additionalCss,
				selector: '%%order_class%% .body_container ul.difl--toc--ul li > .toc-li-wrapper:has( > a.active)',
				type,
			} )
		} )

		icons.forEach( ( { key, selector } ) => {
			utility.process_icon_font_style( {
				'props': props,
				'additionalCss': additionalCss,
				key,
				selector
			} );
		} )

		additionalCss.push( [ {
			selector: '%%order_class%% ul.difl--toc--ul',
			declaration: 'padding-bottom:0'
		} ] )

		return additionalCss;
	}

	componentDidUpdate( prevProps, prevState, snapshot ) {
		this.get_generated_toc();
		this.add_active_class();
		// this.handle_scroll();
	}

	componentDidMount() {
		this.get_generated_toc();
		this.add_active_class();
		// this.handle_scroll();
	}

	add_active_class() {
		if ( 'off' === this.props.highlight_active_link ) {
			return;
		}
		const active_item = document.querySelectorAll( '.toc-li-wrapper' )[2];
		active_item.querySelector( 'a' ).classList.add( 'active' );
		const child_selector = active_item.parentElement.id;
		const li = document.getElementById( `${ child_selector }` );
		const wrapper = li.querySelector( '.toc-li-wrapper' );
		if ( 'on' === this.props.active_link_border_on_parent ) {
			const left = this.get_parent_distance( wrapper, '.body_container' );
			const right = this.get_parent_distance( wrapper, '.body_container', 'right' ) * -1;
			const margin_left = left + parseInt( window.getComputedStyle( wrapper ).marginLeft.replace( 'px', '' ) );
			const margin_right = right + parseInt( window.getComputedStyle( wrapper ).marginRight.replace( 'px', '' ) );
			const padding_left = Math.abs( margin_left ) + parseInt( this.get_padding_value().left );
			const padding_right = Math.abs( margin_right ) + parseInt( this.get_padding_value().right );

			wrapper.style.setProperty( 'margin-left', `${ margin_left }px`, 'important' );
			wrapper.style.setProperty( 'margin-right', `${ margin_right }px`, 'important' );
			wrapper.style.setProperty( 'padding-left', `${ padding_left }px`, 'important' );
			wrapper.style.setProperty( 'padding-right', `${ padding_right }px`, 'important' );
		} else {
			wrapper.style.setProperty( 'margin-left', 'initial' );
			wrapper.style.setProperty( 'margin-right', 'initial' );
			wrapper.style.setProperty( 'padding-left', 'initial' );
			wrapper.style.setProperty( 'padding-right', 'initial' );
		}
	}

	render_title_icon() {
		const props = this.props;
		const is_title_icon = props.title_icon === 'on';
		const is_collapsible = props.collapsible_toc === 'on';
		if ( ! is_title_icon || ! is_collapsible ) {
			return null;
		}
		const expand_icon = <span
			className="et-pb-icon expand_icon">{ this.et_utils.processFontIcon(
			props.expand_icon ) }</span>
		const collapse_icon = <span
			className="et-pb-icon collapse_icon">{ this.et_utils.processFontIcon(
			props.collapse_icon ) }</span>
		return (
			<div className="icon">{ expand_icon }{ collapse_icon }</div>
		)
	}

	get_all_settings() {
		const props = this.props;

		const allowedSettings = [
			'heading_tags',
			'minimum_number_of_headings',
			'scrolling_speed',
			'content_height',
			'default_collapse_state',
			'offset',
			'marker_type',
			'collapsible_with_sticky',
		];

		const headings = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ];

		headings.forEach( ( heading ) => {
			allowedSettings.push( `marker_icon_${ heading }` );
			allowedSettings.push( `marker_icon_size_${ heading }` );
			allowedSettings.push( `expand_icon_color_${ heading }` );
		} );

		const settings = {};

		allowedSettings.forEach( ( key ) => {
			settings[key] = props[key];
		} );

		settings['offset_tablet'] = props['offset_tablet'];
		settings['offset_phone'] = props['offset_phone'];

		return settings;
	}


	render() {
		const props = this.props;

		const marker_type = undefined === props.marker_type ? 'number' : props.marker_type;
		const height_enable = 'on' === props.use_content_height ? ' height_enable' : '';
		const hierarchical_view = 'off' === props.hierarchical_view ? ' non_hierarchical' : '';
		const collapse_sticky = 'on' === props.collapsible_toc && 'on' === props.collapsible_with_sticky ? 'collapse_sticky' : '';
		const full_width_header = 'on' === props.full_width_header && 'on' === props.full_width_header ? 'full_width_header' : '';
		const collapse_icon_only = 'on' === props.collapse_icon_only && 'on' === props.collapse_icon_only ? 'collapse_icon_only' : '';
		const collapse_state = props.default_collapse_state


		const TitleTag = undefined === props.title_tag ? 'div' : props.title_tag;
		const heading = props.heading_tags.split( '|' ).map( ( value, index ) => {
			const key = `h${ ++index }`;
			return { [key]: value };
		} ).filter( val => val[Object.keys( val )[0]] !== '' ).filter( value => Object.values( value )[0] === 'on' );

		const list = [];

		if ( heading.length ) {
			//class name difl_heading_level_${index} in builder keep it simple i.e static but the markup should same
			// Make it accessible thus it will get SEO impression i.e role=group, role=treeitem etc
			list.push( <ul className={ `difl--toc--ul difl_heading_level_1` }>
				<li  { ...('icon' === props.marker_type ? {
					'data-icon': this.et_utils.processFontIcon(
						props.marker_icon_h1 )
				} : {}) }>
					<a className="difl--toc--anchor" href="#">Heading Level One</a>
				</li>
				<li  { ...('icon' === props.marker_type ? {
					'data-icon': this.et_utils.processFontIcon(
						props.marker_icon_h1 )
				} : {}) }>
					<a className="difl--toc--anchor" href="#">Heading Level One</a>
				</li>
				<li  { ...('icon' === props.marker_type ? {
					'data-icon': this.et_utils.processFontIcon( props.marker_icon_h1 ),
					// 'data-fm': this.et_utils.processIconFontData( props.marker_icon_h1 ).iconFontFamily,
					// 'data-fw': this.et_utils.processIconFontData( props.marker_icon_h1 ).iconFontWeight
				} : {}) }>
					<a className="difl--toc--anchor" href="#">Heading One</a>
				</li>
				<ul className={ `difl_heading_level_2` }>
					<li>Heading Level Two</li>
					<li>Heading Two</li>
					<ul className={ `difl_heading_level_3` }>
						<li>Heading Level Two</li>
						<li>Heading Two</li>
					</ul>
				</ul>
			</ul> );
			list.push( <ul className={ `difl_heading_level_1` }>
				<li>Heading Level</li>
				<li>Heading One</li>
				<ul className={ `difl_heading_level_2` }>
					<li>Heading Level Two</li>
					<li>Heading Two</li>
					<ul className={ `difl_heading_level_3` }>
						<li>Heading Level Two</li>
						<li>Heading Two</li>
					</ul>
				</ul>
			</ul> )
		}

		const post_content = this.get_generated_toc();

		return (<div ref={ this.wrapperRef }
					 className={ `difl_toc_main_container ${ collapse_state } ${ full_width_header } ${ collapse_icon_only }` }>
				<div className="heading_container">
					<TitleTag
						className="title">{ props.dynamic.title.hasValue ? utility._renderDynamicContent( props, 'title' ) : null }</TitleTag>
					{ this.render_title_icon() }
				</div>
				{
					post_content === null ? <div
							className={ `body_container ${ marker_type }  ${ height_enable }  ${ hierarchical_view } ${ collapse_sticky }` }>{ list }</div> :
						<div
							className={ `body_container ${ marker_type }  ${ height_enable }  ${ hierarchical_view } ${ collapse_sticky }` }
							dangerouslySetInnerHTML={ { __html: post_content } }></div>
				}
			</div>
		)
	}

	create_toc( text, level, parent = undefined ) {
		return {
			text,
			level,
			id: undefined,
			parent,
			children: []
		};
	}

	parse_headings( elements ) {
		const toc = [];
		let current_level = 0;
		let current_parent = undefined;

		elements.forEach( ( element, index ) => {
			const level = parseInt( element.tagName.substring( 1 ) );
			const text = element.textContent.trim();

			if ( current_level < level ) {
				const entry = this.create_toc( text, level, current_parent );
				current_parent ? current_parent.children.push( entry ) : toc.push( entry );
				current_parent = entry;
			} else {
				let new_parent = this.create_toc( text, level );
				while ( current_parent && current_parent.level >= level ) {
					current_parent = current_parent.parent;
				}
				if ( current_parent ) {
					new_parent.parent = current_parent;
					current_parent.children.push( new_parent );
				} else {
					toc.push( new_parent );
				}
				current_parent = new_parent;
			}

			current_level = level;

			const id = 'difl-toc-' + text.replace( /[^\w\s-]/g, '' ).replace( /\s+/g, '-' ).toLowerCase() + '-' + index;

			if ( ! element && ! current_parent ) {
				return;
			}

			if ( current_parent.hasOwnProperty( 'classList' ) ) {
				current_parent.classList.add( 'difl-toc-item' );
			}

			element.classList.add( 'difl-toc-item' );
			if ( element.id ) {
				current_parent.id = element.id;
			} else {
				element.id = id;
				current_parent.id = id;
			}
		} )

		return toc;
	}

	generate_markup( toc ) {
		const is_icon_marker = 'icon' === this.get_settings( 'marker_type' );
		const is_number_with_dot = 'number_with_dot' === this.get_settings( 'marker_type' );
		const is_native_number = 'number' === this.get_settings( 'marker_type' );
		let icon_span = '';
		let level_stack = [ 1 ];
		let depth = 0;

		const build_list = ( entries, level = 1 ) => {
			let html = `<ul class="difl--toc--ul difl_heading_level_${ level }">`;
			const icon_settings = this.get_settings( `marker_icon_h${ level }` );

			entries.forEach( ( entry ) => {
				let numbering = level_stack.join( '.' );
				numbering = depth === 0 ? '0' + numbering + '.' : numbering;

				const classes = `difl--toc--li difl_heading_li_level_${ entry.level }`;
				const anchor = `<a class="difl--toc--anchor ${ classes }" href="#${ entry.id }">${ entry.text }</a>`;
				if ( is_icon_marker && undefined !== icon_settings ) {
					const icon = icon_settings.split( '||' )[0];
					icon_span = `<span class="et-pb-icon marker-icon">${ icon }</span>`;
				}

				if ( is_number_with_dot ) {
					icon_span = `<span class="number-with-dot">${ numbering }</span>`;
				}

				if ( is_native_number ) {
					let numbering = level_stack[depth];
					numbering = depth === 0 ? '0' + level_stack[0] : '0' + numbering;
					icon_span = `<span class="native-number">${ numbering }</span>`;
				}

				html += `<li id="${ entry.id }-toc-li"><div class="toc-li-wrapper">${ icon_span } ${ anchor }</div>`;
				if ( entry.children.length ) {
					level_stack.push( 1 );
					depth += 1;
					html += build_list( entry.children, level + 1 );
				}
				level_stack[depth] += 1;
				html += `</li>`;
			} );
			depth -= 1;
			level_stack.pop();
			return html + `</ul>`;
		}

		return build_list( toc );
	}

	get_offset() {
		const is_mobile = window.matchMedia( '(max-width: 767px)' ).matches;
		const is_tablet = window.matchMedia( '(min-width: 768px) and (max-width: 1024px)' ).matches;
		let offset = this.props.offset.replace( 'px', '' );

		if ( is_tablet ) {
			offset = this.props.offset_tablet;
		}

		if ( is_mobile ) {
			offset = this.props.offset_mobile;
		}

		return offset;
	}

	get_current_device() {
		if ( window.matchMedia( '(max-width: 767px)' ).matches ) {
			return 'mobile';
		} else if ( window.matchMedia( '(min-width: 768px) and (max-width: 1024px)' ).matches ) {
			return 'tablet';
		} else {
			return 'desktop';
		}
	}

	get_spacing_value( value ) {
		let top = 0, right = 0, bottom = 0, left = 0;
		if ( ! value ) {
			return { top, right, bottom, left };
		}
		value.split( ';' ).map( val => val.split( ':' ) ).forEach( item => {
			const side = item[0].replace( 'padding-', '' ).trim();
			const side_value = '' === item[1] ? 0 : parseInt( item[1] );

			if ( side === 'top' ) {
				top = side_value;
			} else if ( side === 'right' ) {
				right = side_value;
			} else if ( side === 'bottom' ) {
				bottom = side_value;
			} else if ( side === 'left' ) {
				left = side_value;
			}
		} );

		return { top, right, bottom, left };
	}

	get_padding_value() {
		const props = this.props;
		const current_device = this.get_current_device();
		const active_padding = props.active_spacing_padding;
		const active_padding_tablet = props.active_spacing_padding_tablet;
		const active_padding_phone = props.active_spacing_padding_phone;
		const active_padding_desktop_value = this.get_spacing_value( active_padding );
		const active_padding_tablet_value = this.get_spacing_value( active_padding_tablet );
		const active_padding_phone_value = this.get_spacing_value( active_padding_phone );


		if ( 'desktop' === current_device ) {
			return active_padding_desktop_value;
		}
		if ( 'tablet' === current_device ) {
			return active_padding_tablet_value;
		}
		return active_padding_phone_value;
	}

	get_parent_distance( wrapper, parent_selector, side = 'left' ) {
		const parent = wrapper.closest( parent_selector );

		const parent_rect = parent.getBoundingClientRect();
		const child_rect = wrapper.getBoundingClientRect();

		return parent_rect[side] - child_rect[side];
	}

	highlight_active() {
		const props = this.props;
		let active_id = null;
		const sections = document.querySelectorAll( '[class*="difl-toc-item"]' );
		const items = document.querySelectorAll( '.difl_toc_main_container .body_container a.difl--toc--li' );
		const is_border_on_parent = 'on' === props.active_link_border_on_parent;
		const offset = this.get_offset();

		sections.forEach( ( section ) => {
			const rect = section.getBoundingClientRect();

			if ( active_id !== null ) {
				return;
			}

			if ( rect.top >= 0 && rect.top < window.innerHeight - offset ) {
				active_id = section.getAttribute( 'id' );
			}
		} );

		items.forEach( ( item ) => {
			const href = item.getAttribute( 'href' ).substring( 1 );
			if ( href === active_id ) {
				if ( items[0] !== item ) {
					item.scrollIntoView( {
						behavior: 'smooth',
						block: 'center',
						inline: 'nearest'
					} );
				}

				if ( is_border_on_parent ) {
					const child_selector = `${ active_id }-toc-li`;
					const li = document.getElementById( `${ child_selector }` );
					const wrapper = li.querySelector( '.toc-li-wrapper' );
					const left = this.get_parent_distance( wrapper, '.body_container' );
					const right = this.get_parent_distance( wrapper, '.body_container', 'right' ) * -1;
					const margin_left = left + parseInt( getComputedStyle( wrapper ).marginLeft.replace( 'px', '' ) );
					const margin_right = right + parseInt( getComputedStyle( wrapper ).marginRight.replace( 'px', '' ) );
					const padding_left = Math.abs( margin_left ) + parseInt( this.get_padding_value().left );
					const padding_right = Math.abs( margin_right ) + parseInt( this.get_padding_value().right );

					wrapper.style.setProperty( 'margin-left', `${ margin_left }px`, 'important' );
					wrapper.style.setProperty( 'margin-right', `${ margin_right }px`, 'important' );
					wrapper.style.setProperty( 'padding-left', `${ padding_left }px`, 'important' );
					wrapper.style.setProperty( 'padding-right', `${ padding_right }px`, 'important' );
				}

				if ( ! item ) {
					return;
				}
				if ( this.props.highlight_active_link === 'on' ){
					item.classList.add( 'active' );
				}
				items.forEach( ( item_remove ) => {
					if ( item_remove !== item && item_remove.classList.contains( 'active' ) ) {
						item_remove.classList.remove( 'active' );

						if ( is_border_on_parent ) {
							item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'margin-left', 'initial' );
							item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'margin-right', 'initial' );
							item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'padding-left', 'initial' );
							item_remove.closest( '.toc-li-wrapper' ).style.setProperty( 'padding-right', 'initial' );
						}
					}
				} );
			}
		} );
	};

	get_settings( key = 'all' ) {
		const settings = this.get_all_settings();
		if ( 'all' === key ) {
			return settings;
		}

		return settings[key] || '';
	}

	is_closest( element, selectors ) {
		if ( ! selectors ) return false;
		return selectors.split( ',' ).some( ( selector ) => element.closest( selector ) );
	}

	is_matched( element, selectors ) {
		if ( ! selectors ) return false;
		return selectors.split( ',' ).some( ( selector ) => element.matches( selector ) );
	}

	is_hidden( element ) {
		const style = window.getComputedStyle( element );
		return style.display === 'none' || style.visibility === 'hidden';
	}

	get_generated_toc() {
		const settings = this.get_settings();
		const heading_tags = settings.heading_tags.split( '|' );
		const module_hide_number = settings.minimum_number_of_headings;
		const container_exclude = this.get_settings( 'container_exclude_by_class' );
		const heading_exclude = this.get_settings( 'headings_exclude_by_class' );
		let main_content = document.querySelector( '#main-content' );
		const allowed_tags = [ 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ].filter( ( tag, index ) => heading_tags[index] === 'on' );

		const templateString = `<div id="main-content">
				<div id="level-1">
				  <h1>Level 1 Heading</h1>
				  <div>
					<h2>Level 1 - H2</h2>
					<h3>Level 1 - H3</h3>
					<h4>Level 1 - H4</h4>
					<h5>Level 1 - H5</h5>
					<h6>Level 1 - H6</h6>
				  </div>
				</div>
				
				<div id="level-2">
				  <h1>Level 2 Heading</h1>
				  <div>
					<h2>Level 2 - H2</h2>
					<h3>Level 2 - H3</h3>
					<h4>Level 2 - H4</h4>
					<h5>Level 2 - H5</h5>
					<h6>Level 2 - H6</h6>
				  </div>
				</div>
				
				<div id="level-3">
				  <h1>Level 3 Heading</h1>
				</div>
				
				<div id="level-4">
				  <h1>Level 4 Heading</h1>
				  <div>
					<h2>Level 4 - H2</h2>
					<h3>Level 4 - H3</h3>
					<h4>Level 4 - H4</h4>
					<h5>Level 4 - H5</h5>
					<h6>Level 4 - H6</h6>
					</div>
				</div>
				</div>`;
		const parser = new DOMParser();
		const doc = parser.parseFromString( templateString, 'text/html' );
		if ( this.is_in_theme_builder() ) {
			main_content = doc.querySelector( '#main-content' );
		}

		if ( ! main_content ) return;


		if ( ! allowed_tags.length ) {
			return '';
		}

		let headings = [ ...main_content.querySelectorAll( allowed_tags.join( ',' ) ) ].filter( heading => {
			return ! (heading.closest( '.entry-title' ) || heading.closest( '.heading_container' ) || heading.closest( '#sidebar' ) || heading.closest( '#comment-wrap' ) || this.is_closest( heading, container_exclude ) || this.is_matched( heading, heading_exclude ) || this.is_hidden( heading ))
		} )

		if ( ! headings.length ) {
			main_content = doc.querySelector( '#main-content' );
			headings = [ ...main_content.querySelectorAll( allowed_tags.join( ',' ) ) ].filter( heading => {
				return ! (heading.closest( '.entry-title' ) || heading.closest( '.heading_container' ) || heading.closest( '#sidebar' ) || heading.closest( '#comment-wrap' ) || this.is_closest( heading, container_exclude ) || this.is_matched( heading, heading_exclude ) || this.is_hidden( heading ))
			} )
		}

		// if ( headings.length < module_hide_number ) {
		// 	this.toc_main.remove();
		// 	return;
		// }
		let parsed_toc = this.parse_headings( Array.from( headings ) );

		if ( parsed_toc.length === 0 ) {
			return null;
		}
		return this.generate_markup( parsed_toc );
	}

	is_in_theme_builder() {
		const url = new URL( window.location.href );
		const params = new URLSearchParams( url.search );
		return params.get( 'et_tb' );
	};

	is_in_builder() {
		const url = new URL( window.location.href );
		const params = new URLSearchParams( url.search );
		return params.get( 'et_fb' );
	};

	handle_scroll() {
		window.addEventListener( 'scroll', () => {
			const module = document.querySelector( '.difl_table_of_contents' );
			const container = document.querySelector( '.difl_toc_main_container' );
			const body = container.querySelector( '.body_container' );
			const scrolling_speed = this.props.scrolling_speed;
			const transition = `all ${ scrolling_speed }ms ease-in-out`;
			const height = getComputedStyle( body ).height;
			const padding = getComputedStyle( body ).padding;
			const sections = document.querySelectorAll( '[class*="difl-toc-item"]' );
			const items = document.querySelectorAll( '.difl_toc_main_container .body_container a.difl--toc--li' );
			const offset = this.get_offset();
			const active_link_color = this.props.active_link_color;
			if ( module.classList.contains( 'et_pb_sticky_module' ) ) {
				this.highlight_active();
			}

			const is_sticky_enabled = this.props.collapsible_with_sticky === 'on';

			if ( ! is_sticky_enabled ) {
				return;
			}

			if ( ! module.classList.contains( 'et_pb_sticky_module' ) ) {
				return;
			}

			if ( body.classList.contains( 'collapse_sticky' ) && module.classList.contains( 'et_pb_sticky' ) ) {
				body.style.setProperty( 'height', '0px', 'important' );
				body.style.setProperty( 'padding', '0px', 'important' );
				container.classList.add( 'collapsed' );
				container.classList.remove( 'expanded' );
			}

			if ( ! module.classList.contains( 'et_pb_sticky' ) ) {
				body.style.setProperty( 'height', height, 'important' );
				body.style.setProperty( 'padding', padding, 'important' );
				container.classList.add( 'expanded' );
				container.classList.remove( 'collapsed' );
			}
		} );
	}
}