import React, { Component } from 'react'
import utility from '../../../scripts/df_scripts/utilities'
import './style.css'

export default class PricingTableItem extends Component {
	static slug = 'difl_pricingtableitem'
	et_utils = window.ET_Builder.API.Utils

	static css( props, moduleInfo ) {
		const { generateStyles } = window.ET_Builder.API.Utils
		const additionalCss = []
		let selector = '%%order_class%% .item-button .button-badge'
		const bg = [
			{ key: 'button_badge_bg', selector },
			{ key: 'divider_color', selector: '%%order_class%% .item-divider' },
		]
		bg.forEach( item => {
			const { key, selector } = item
			utility.df_process_bg( {
				props,
				key,
				additionalCss,
				selector,
			} )
		} )

		const spaces = [ 'margin', 'padding' ]

		spaces.forEach( type => {
			const key = `button_badge_spacing_${ type }`
			utility.process_margin_padding( {
				props,
				key,
				additionalCss,
				selector,
				type,
			} )
		} )
		spaces.forEach( type => {
			const key = `ribbon_spacing_${ type }`
			utility.process_margin_padding( {
				props,
				key,
				additionalCss,
				selector: '%%order_class%% .item-ribbon .ribbon_text',
				type,
			} )
		} )
		spaces.forEach( type => {
			const key = `ribbon_icon_spacing_${ type }`
			utility.process_margin_padding( {
				props,
				key,
				additionalCss,
				selector: '%%order_class%% .item-ribbon .ribbon_icon',
				type,
			} )
		} )

		utility.process_margin_padding( {
			props,
			key: 'feature_icon_spacing_padding',
			additionalCss,
			selector: '%%order_class%% .item-feature .et-pb-icon',
			type: 'padding',
		} )
		utility.process_margin_padding( {
			props,
			key: 'feature_tooltip_spacing_padding',
			additionalCss,
			selector: '.tippy-box[data-theme~="%%order_class%%"]',
			type: 'padding',
		} )

		// core generator
		selector = '%%order_class%% .item-feature .et-pb-icon';
		const icon_selector = '%%order_class%% .item-icon .et-pb-icon';
		const ribbon_icon = '%%order_class%% .item-ribbon .et-pb-icon';
		const image_selector = '%%order_class%% .item-image img';
		const styles = [
			{ name: 'feature_icon_color', selector, cssProperty: 'color' },
			{
				name: 'feature_icon_bg_color',
				selector,
				cssProperty: 'background-color',
			},
			{ name: 'feature_icon_size', selector, cssProperty: 'font-size' },
			{
				name: 'image_icon_background_color',
				selector: icon_selector,
				cssProperty: 'background-color',
			},
			{
				name: 'image_icon_width',
				selector: icon_selector,
				cssProperty: 'font-size',
			},
			{
				name: 'icon_color',
				selector: icon_selector,
				cssProperty: 'color',
			},
			{
				name: 'image_icon_background_color',
				selector: image_selector,
				cssProperty: 'background-color',
			},
			{
				name: 'rating_icon_label_gap',
				selector: '%%order_class%% .item-rating',
				cssProperty: 'gap',
			},
			{
				name: 'rating_icon_gap',
				selector: '%%order_class%% .item-rating span.et-pb-icon+span.et-pb-icon',
				cssProperty: 'margin-inline-start',
			},
			{ name: 'ribbon_icon_color', selector: ribbon_icon, cssProperty: 'color' },
			{
				name: 'ribbon_icon_bg_color',
				selector: ribbon_icon,
				cssProperty: 'background-color',
			},
			{ name: 'ribbon_icon_size', selector: ribbon_icon, cssProperty: 'font-size' },
			{ name: 'price_gap', selector: '%%order_class%% .item-price', cssProperty: 'gap' },
			{ name: 'price_icon_gap', selector: '%%order_class%% .item-feature', cssProperty: 'gap' },
			{
				name: 'feature_tooltip_bg_color',
				selector: '.tippy-box[data-theme~="%%order_class%%"]',
				cssProperty: 'background-color',
			},
			{
				name: 'feature_tooltip_arrow_color',
				selector: '.tippy-box[data-theme~="%%order_class%%"] .tippy-arrow',
				cssProperty: 'color',
			},
		];

		styles.forEach( item => {
			const args = Object.assign( { attrs: props }, item )
			additionalCss.push( generateStyles( args ) )
		} )

		const is_custom = props.rating_enable_custom_icon === 'on';

		if ( is_custom ) {
			utility.process_icon_font_style( {
				'props': props,
				'additionalCss': additionalCss,
				'key': 'rating_icon',
				'selector': '%%order_class%% .item-rating .et-pb-icon'
			} )
			additionalCss.push( [ {
				selector: '%%order_class%% .item-rating .star span.df_rating_icon_fill::before',
				declaration: 'content: attr(data-icon) !important;',
			}
			] )
			additionalCss.push( [ {
				selector: '%%order_class%% .item-rating .star span.df_rating_icon_fill::after',
				declaration: 'display: none !important;',
			}
			] )
			const colors = [
				{
					name: 'rating_color',
					selector: '%%order_class%% .item-rating span.et-pb-icon:not(.df_rating_icon_empty), %%order_class%% .item-rating .df_rating_icon_fill::before',
					cssProperty: 'color',
				},
				{
					name: 'rating_color_inactive',
					selector: '%%order_class%% .item-rating .df_rating_icon_empty',
					cssProperty: 'color',
				},
		];
			colors.forEach( item => {
				const args = Object.assign( { attrs: props }, item )
				additionalCss.push( generateStyles( args ) )
			} )
		} else {
			const colors = [
				{
					name: 'rating_color',
					selector: '%%order_class%% .item-rating span.et-pb-icon:not(.df_rating_icon_empty), %%order_class%% .item-rating .df_rating_icon_fill::before',
					cssProperty: 'color',
				},
				{
					name: 'rating_color_inactive',
					selector: '%%order_class%% .item-rating .df_rating_icon_empty',
					cssProperty: 'color',
				},
			];
			colors.forEach( item => {
				const args = Object.assign( { attrs: props }, item )
				additionalCss.push( generateStyles( args ) )
			} )
		}

		additionalCss.push( [ {
			selector: '%%order_class%%, %%order_class%% .item-rating span.label',
			declaration: `text-align:${ undefined === props.default_text_align ? 'center' : props.default_text_align };`
		}
		] )
		additionalCss.push( [
			{
				selector: '%%order_class%% .item-rating',
				declaration: 'justify-content:center;'
			}
		] )
		if ( undefined === props.feature_tooltip_bg_color ){
			additionalCss.push( [ {
				selector: '.tippy-box[data-theme~="%%order_class%%"]',
				declaration: `background-color: ${ window.et_pb_custom.accent_color } !important;`,
			} ] )
		}

		if ( undefined === props.feature_tooltip_arrow_color ){
			additionalCss.push( [ {
				selector: '.tippy-box[data-theme~="%%order_class%%"] .tippy-arrow',
				declaration: `color: ${ window.et_pb_custom.accent_color } !important;`,
			} ] )
		}
		additionalCss.push( [ {
			selector: image_selector,
			declaration: `width: ${ undefined === props.image_icon_width ? '96px' : props.image_icon_width } !important;`,
		} ] )
		additionalCss.push( [ {
			selector: '%%order_class%% .item-ribbon .ribbon-image',
			declaration: `width: ${ undefined === props.ribbon_image_width ? '100px' : props.ribbon_image_width } !important;`,
		} ] )
		if ( undefined === props.icon_color ){
			additionalCss.push( [ {
				selector: icon_selector,
				declaration: `color: ${ window.et_pb_custom.accent_color };`,
			} ] )
		}

		if ( props.icon_color__hover_enabled && props.hover_enabled && props.hover_enabled === 1 && props.icon_color__hover && undefined !== props.icon_color__hover ){
			additionalCss.push( [ {
				selector: icon_selector,
				declaration: `color: ${ props.icon_color__hover } !important;`,
			} ] )
		}

		additionalCss.push( [ {
			selector: '%%order_class%% .item-rating .star span.et-pb-icon',
			declaration: `font-size: ${props.rating_icon_size } !important;`,
		} ] )

		additionalCss.push( [ {
			selector: '%%order_class%% .item-rating .star span.et-pb-icon',
			declaration: `font-size: ${props.rating_icon_size_tablet } !important;`,
			device: 'tablet',
		} ] )

		additionalCss.push( [ {
			selector: '%%order_class%% .item-rating .star span.et-pb-icon',
			declaration: `font-size: ${props.rating_icon_size_phone } !important;`,
			device: 'phone',
		} ] )

		additionalCss.push( [ {
			selector: '%%order_class%% .item-rating .star span.et-pb-icon',
			declaration: `font-size: ${props.rating_icon_size_tablet } !important;`,
			device: 'tablet',
		} ] )

		if ( undefined === props.rating_icon_size ){
			additionalCss.push( [ {
				selector: '%%order_class%% .item-rating .star span.et-pb-icon',
				declaration: `font-size: ${ undefined === props.rating_icon_size ? '16px' : props.rating_icon_size } !important;`,
			} ] )
		}

		additionalCss.push( [
			{
				selector: '%%order_class%% .item-divider',
				declaration: `height: ${ undefined === props.divider_height ? '3px' : props.divider_height } !important;`,
			},
		] )
		additionalCss.push( PricingTableItem.generate_ribbon_transform_style( props ) )
		utility.process_icon_font_style( {
			'props': props,
			'additionalCss': additionalCss,
			'key': 'item_icon',
			'selector': '%%order_class%% .item-icon .et-pb-icon'
		} )

		utility.process_icon_font_style( {
			'props': props,
			'additionalCss': additionalCss,
			'key': 'feature_icon',
			'selector': '%%order_class%% .item-feature .et-pb-icon'
		} )
		utility.process_icon_font_style( {
			'props': props,
			'additionalCss': additionalCss,
			'key': 'ribbon_icon',
			'selector': '%%order_class%% .item-ribbon .et-pb-icon'
		} )
		return additionalCss
	}

	static generate_ribbon_transform_style( props ) {
		const styles = []
		const ribbon_transform_x = undefined !== props.ribbon_transform_x ? props.ribbon_transform_x : 0
		const ribbon_transform_x_tablet = undefined !== props.ribbon_transform_x_tablet ? props.ribbon_transform_x_tablet : 0
		const ribbon_transform_x_phone = undefined !== props.ribbon_transform_x_phone ? props.ribbon_transform_x_phone : 0
		const ribbon_transform_y = undefined !== props.ribbon_transform_y ? props.ribbon_transform_y : 0
		const ribbon_transform_y_tablet = undefined !== props.ribbon_transform_y_tablet ? props.ribbon_transform_y_tablet : 0
		const ribbon_transform_y_phone = undefined !== props.ribbon_transform_y_phone ? props.ribbon_transform_y_phone : 0
		const desktop = `transform: translate(${ ribbon_transform_x },${ ribbon_transform_y }) !important`
		const tablet = `transform: translate(${ ribbon_transform_x_tablet },${ ribbon_transform_y_tablet }) !important`
		const phone = `transform: translate(${ ribbon_transform_x_phone },${ ribbon_transform_y_phone }) !important`
		styles.push( {
			selector: '%%order_class%%',
			declaration: desktop,
			device: 'desktop',
		} )
		styles.push( {
			selector: '%%order_class%%',
			declaration: tablet,
			device: 'tablet',
		} )
		styles.push( {
			selector: '%%order_class%%',
			declaration: phone,
			device: 'phone',
		} )

		return styles
	}

	generate_regular_style( name, selector, cssProperty ) {
		const { generateStyles } = window.ET_Builder.API.Utils
		const styles = PricingTableItem.styles
		const attrs = this.props
		styles.push( generateStyles( {
			attrs,
			name,
			selector,
			cssProperty,
		} ) )
	}

	render_item_Text() {
		const props = this.props;
		return props.dynamic.text_content.hasValue ?
			<div className="item-text">{ utility._renderDynamicContent( props, 'text_content' ) }</div> : null;
	}

	render_item_Price() {
		const props = this.props
		let original_price = '';
		if ( 'on' === props.enable_original_price ) {
			const prefix_placement = undefined === props.original_price_prefix_placement ? 'top' : props.original_price_prefix_placement;
			const prefix = <span className={ 'price_prefix ' + prefix_placement }>{ props.original_price_prefix }</span>
			const price = <span className="price">{ props.original_price }</span>
			const suffix_placement = undefined === props.original_price_suffix_placement ? 'bottom' : props.original_price_suffix_placement;
			const suffix = <span
				className={ 'price_suffix ' + suffix_placement }>{ props.original_price_suffix }</span>;
			original_price = <span
				className={ `original-price ${ undefined === props.original_price_placement ? 'back' : props.original_price_placement }` }>{ prefix }{ price }{ suffix }</span>
		}

		const prefix_placement = undefined === props.price_prefix_placement ? 'top' : props.price_prefix_placement;
		const prefix = <span className={ 'price_prefix ' + prefix_placement }>{ props.price_prefix }</span>

		const price = <span className="price">{ props.price }</span>
		const suffix_placement = undefined === props.price_suffix_placement ? 'bottom' : props.price_suffix_placement;
		const suffix = <span className={ 'price_suffix ' + suffix_placement }>{ props.price_suffix }</span>

		const sale_price = <span className="sale-price">{ prefix }{ price }{ suffix }</span>
		const alignment = undefined === props.price_alignemnt ? 'center' : props.price_alignemnt;
		return <div className={ 'item-price ' + alignment }>{ original_price }{ sale_price }</div>
	}

	render_item_Feature() {
		const props = this.props
		const feature_icon = <span
			className="et-pb-icon feature_icon">{ this.et_utils.processFontIcon(
			props.feature_icon ) }</span>
		const placement = undefined === props.feature_icon_placement ?
			'left' :
			props.feature_icon_placement
		const feature_text = <span
			className="feature_text">{ props.feature_text }</span>
		return <div className={ 'item-feature  icon-' + placement }
					data-tooltip={ props.feature_text_tooltip ==='on' ? JSON.stringify(
						{ 'main_content': props.feature_text_tooltip_main_content } ) : '' }>{ feature_text }{ feature_icon }</div>

	}

	render_item_Icon() {

		const props = this.props;
		const icon = <span
			className="et-pb-icon item_icon">{ this.et_utils.processFontIcon(
			props.item_icon ) }</span>
		const alignment = undefined === props.image_icon_alignment ? 'center' : props.image_icon_alignment;

		return <div
			className={ 'item-icon ' + alignment }>{ icon }</div>
	}

	render_item_Image() {
		const props = this.props
		const img = <img src={ props.item_image } alt={ props.item_image_alt }/>
		const alignment = undefined === props.image_icon_alignment ? 'center' : props.image_icon_alignment;

		return <span
			className={ 'item-image ' + alignment }>{ img }</span>
	}

	render_item_Ribbon() {
		const props = this.props;
		let content = [];
		let animation = 'on' === props.ribbon_animation ? 'difl_bounce_in ' : '';
		const position = undefined === props.ribbon_position ? 'top_right' : props.ribbon_position;
		const ribbon_type = undefined === props.ribbon_type ? 'text_only' : props.ribbon_type;
		const icon_enable = 'text_icon' === ribbon_type || 'icon_only' === ribbon_type;
		const image_enable = 'image_only' === ribbon_type || 'text_image' === ribbon_type;
		const text_enable = 'text_icon' === ribbon_type || 'text_image' === ribbon_type || 'text_only' === ribbon_type;

		setTimeout( () => {
			animation = ''
		}, 1000 )
		let icon_placement = '';
		if ( icon_enable ) {
			icon_placement = 'icon-' + props.ribbon_icon_placement;
			content.push( <span
				className="et-pb-icon ribbon_icon">{ this.et_utils.processFontIcon(
				props.ribbon_icon ) }</span> );
		}

		if ( image_enable ) {
			let src = props.ribbon_image;
			if ( src ) {
				content.push( <img src={ src } className="ribbon-image"
								   alt={ props.ribbon_image_alt }/> )
			}

		}

		if ( text_enable ) {
			content.push( <span className={ 'ribbon_text ' +
				(undefined === props.ribbon_orientation
					? 'vertical'
					: props.ribbon_orientation) }>{ undefined === props.ribbon_text ? 'Ribbon Text' : props.ribbon_text }</span> )
		}

		return <div
			className={ 'item-ribbon ' + position + ' ' +
				animation + icon_placement }>{ content }</div>
	}

	render_item_Divider() {
		return <span className="item-divider"></span>
	}

	render_item_Button() {
		const props = this.props;
		let badge_content = ''
		const buttonIcon = this.et_utils.processFontIcon( props.button_icon );
		if ( 'on' === props.button_badge ) {
			const badge_animation = 'on' === props.button_badge_animation ? 'difl_bounce_in ' : '';
			const badge_position = undefined === props.button_badge_position ? 'right ' : props.button_badge_position;
			const vertical_position = undefined === props.button_badge_position_vertically ? 'top ' : props.button_badge_position_vertically;

			badge_content = <span
				className={ `button-badge ${ vertical_position } ${ badge_position } ${ badge_animation }` }>{ props.button_badge_text }</span>
		}

		const button_full_width = 'on' === props.button_full_width ? 'full-width' : '';
		const alignment = undefined === props.button_alignment ? 'center' : props.button_alignment;
		const alignment_phone = undefined === props.button_alignment_phone ? 'center' : 'phone-' + props.button_alignment_phone;
		const alignment_tablet = undefined === props.button_alignment_tablet ? 'center' : 'tablet-' + props.button_alignment_tablet;

		return <div className={ "item-button " + button_full_width + ' ' + alignment + ' ' + alignment_phone + ' ' + alignment_tablet }> { badge_content } <a
			href={ props.button_url } className="et_pb_button difl-pricingtableitem-button"
			target={ 'on' === props.button_url_new_window ?
				'target="_blank"' :
				'' }
			data-icon={ buttonIcon }
		>{ props.button_text ? props.button_text : 'Click Here' }</a>
		</div>
	}

	generate_style() {
		this.et_utils.generateStyles( [] )
	}

	generate_color( key, selector, type = 'color' ) {
		this.et_utils.generateStyles( {
			attrs: this.props,
			name: key,
			selector: selector,
			cssProperty: type,
		} )
	}

	render_item_Rating() {
		const props = this.props;
		const rating_number = undefined === props.rating_number ? 5 : props.rating_number;
		const rating_label = props.rating_label;
		const rating_alignment = props.rating_alignment;
		const dynamicIcon = utility.df_collect_dynamic_content( "rating_icon", this.props );
		const icon = props.rating_enable_custom_icon === "on" ? this.et_utils.processFontIcon( dynamicIcon ) : "☆";

		const generateRating = () => {
			let rating = []
			const get_float = typeof rating_number === "string" && rating_number.includes( "." ) ? rating_number.split( "." ) : parseInt( rating_number );
			let rating_active_class = '';
			for ( let i = 1; i <= 5; i++ ) {
				if ( typeof rating_number === "undefined" ) {
					rating_active_class = "";
				} else if ( [] !== get_float && i <= get_float ) {
					rating_active_class = "df_rating_icon_fill";
				} else if ( i <= parseInt( get_float[0] ) || (1 < parseInt( get_float[1] ) && parseInt( get_float[0] ) + parseInt( 1 ) == i) ) {
					if ( i <= parseInt( get_float[0] ) ) {
						rating_active_class = "df_rating_icon_fill";
					} else {
						rating_active_class = `df_rating_icon_fill df_rating_icon_empty df_fraction_reverse df_fill_${ get_float[1] }`;
					}
				} else {
					rating_active_class = "df_rating_icon_empty";
				}
				rating.push( <span className={ "et-pb-icon " + rating_active_class } key={ i } data-icon={ icon }>
          { icon }
        </span>
				);
			}
			return rating
		}

		return (
			<div className={ 'item-rating ' + rating_alignment }>
				<span className="star">{ generateRating() }</span>
				{ rating_label && <span className="label">{ rating_label }</span> }
			</div>
		)
	}

	render() {
		let type = this.props.item_type
		if ( type === undefined ) {
			type = 'Text';
		}
		const method = `render_item_${ type }`
		let output = this[method]()

		return <>{ output }</>
	}
}