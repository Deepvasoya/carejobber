import React, { Component } from 'react';
import Vivus from 'vivus'
import './style.css';

export default class SVGAnimator extends Component {
	static slug = 'difl_svganimator'

	static css( props, moduleInfo ) {
		const additionalCss = []

		const svg_width = props.svg_width;
		const svg_width_tablet = props.svg_width_tablet;
		const svg_width_phone = props.svg_width_phone;
		const svg_height = props.svg_height;
		const svg_height_tablet = props.svg_height_tablet;
		const svg_height_phone = props.svg_height_phone;
		const selector = '%%order_class%% .difl-svg-animator-container svg';

		additionalCss.push( [ {
			selector,
			declaration: `width:  ${ svg_width } !important;`,
		} ] );

		additionalCss.push( [ {
			selector,
			declaration: `width:  ${ svg_width_tablet } !important;`,
			device: 'tablet'
		} ] );

		additionalCss.push( [ {
			selector,
			declaration: `width:  ${ svg_width_phone } !important;`,
			device: 'phone'
		} ] );

		additionalCss.push( [ {
			selector,
			declaration: `height:  ${ svg_height } !important;`,
		} ] );

		additionalCss.push( [ {
			selector,
			declaration: `height:  ${ svg_height_tablet } !important;`,
			device: 'tablet'
		} ] );

		additionalCss.push( [ {
			selector,
			declaration: `height:  ${ svg_height_phone } !important;`,
			device: 'phone'

		} ] );

		const svg_color = props['svg_color'];
		const svg_weight = props['svg_weight'];

		additionalCss.push( [ {
			selector: '%%order_class%% .difl-svg-animator-container svg *',
			declaration: `stroke:  ${ svg_color } !important;`,
		} ] );
		additionalCss.push( [ {
			selector: '%%order_class%% .difl-svg-animator-container svg *',
			declaration: `stroke-width: ${ svg_weight } !important;`,
		}
		] );

		additionalCss.push( [ {
			selector: '%%order_class%% .difl-svg-animator-inner-wrapper',
			declaration: `justify-content: ${props.alignment } !important;`,
			device: 'desktop'
		} ] );

		additionalCss.push( [ {
			selector: '%%order_class%% .difl-svg-animator-inner-wrapper',
			declaration: `justify-content: ${props.alignment_tablet } !important;`,
			device: 'tablet'
		} ] );

		additionalCss.push( [ {
			selector: '%%order_class%% .difl-svg-animator-inner-wrapper',
			declaration: `justify-content: ${props.alignment_phone } !important;`,
			device: 'phone'
		} ] );



		return additionalCss;

	}

	componentDidMount() {
		this.handleAnimation();
	}

	componentDidUpdate( prevProps, prevState, snapshot ) {
		const settings = [ 'svg_src', 'svg_color', 'svg_width', 'svg_height', 'svg_weight', 'animation_type', 'delay_animation', 'duration_animation', 'path_timing_func', 'animation_timing_func', 'animation_start', 'repeat_type', 'replay_enable', 'iteration_number' ];
		settings.forEach( config => {
			if ( prevProps[config] !== this.props[config] ) {
				this.handleAnimation();
			}
		} )
	}

	handleAnimation = () => {
		const props = this.props;
		const order_class = props.moduleInfo.orderClassName.replaceAll('_', '-');
		const wrapper = document.querySelector( `div.difl-svg-animator-container[class*="${ order_class }"]` );
		if ( ! wrapper ) {
			return;
		}

		const config = {
			type: undefined === props.animation_type ? 'delayed' : props.animation_type,
			delay: undefined === props.animation_delay ? '0ms' : props.animation_delay,
			duration: undefined === props.duration_animation ? 200 : parseInt( props.duration_animation.replace( 'ms', '' ) ),
			start: undefined === props.animation_start ? 'autostart' : props.animation_start,
			pathTimingFunction: undefined === props.path_timing_func ? 'linear' : props.path_timing_func,
			animTimingFunction: undefined === props.animation_timing_func ? 'linear' : props.animation_timing_func,
		};

		const svg_element = wrapper.querySelector( '#difl-svg-animator-' );

		if ( ! svg_element ) {
			return;
		}


		const vivus_config = {
			type: config.type,
			delay: config.delay,
			duration: config.duration,
			start: config.start,
			pathTimingFunction: Vivus[config.pathTimingFunction],
			animTimingFunction: Vivus[config.animTimingFunction],
		};

		const animate = ( iteration, once = false ) => {
			const animation = new Vivus( svg_element, vivus_config, () => {
				const loop_enabled = 'on' === config.loop_enabled;
				const infinite = 'on' === config.loop_infinite;
				const iteration_number = config.iteration_number;
				let total_iteration = 1;
				if ( loop_enabled && infinite ) {
					total_iteration = Infinity;
				}

				if ( loop_enabled && ! infinite ) {
					total_iteration = iteration_number;
				}
				if ( ! once ) {
					if ( iteration < total_iteration ) {
						setTimeout( function () {
							animate( iteration + 1 )
						}, 100 )
					}
				}
			} )

			animation.reset();
		}

		const delay = 'delay' === config.type ? config.delay : 0;

		setTimeout( () => {
			animate( 1 );
		}, delay )

		if ( 'off' === config.loop_infinite && 'on' === config.replay_enable ) {
			svg_element.addEventListener( [ config.repeat_type ], function () {
				animate( 1, true )
			} );
		}

	}

	render() {
		const props = this.props
		return this.props.__svg_animator ?
			<div className={ "difl-svg-animator-container " + props.moduleInfo.orderClassName.replaceAll('_', '-') }
				 dangerouslySetInnerHTML={ { __html: props.__svg_animator } }/> : '';
	}
}