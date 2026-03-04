import React, { Component } from 'react'
import '../../../public/js/lib/popper.min'
import '../../../public/js/lib/tippy-bundle.min'
import tippy from 'tippy.js'

export default class PricingTable extends Component {
	static slug = 'difl_pricingtable'

	componentDidMount() {
		this.handleAnimation();
		this.handleTooltip();
	}

	componentDidUpdate( prevProps, prevState, snapshot ) {
		const controls = [ 'enable_animation', 'difl_animation_types', 'difl_animation_delay', 'difl_animation_duration' ];
		controls.forEach( config => {
			if ( prevProps[config] !== this.props[config] ) {
				this.handleAnimation();
			}
		} )
		this.handleTooltip();
	}

	getSettings( enableKey, prefix ) {
		const props = this.props

		if ( props[enableKey] === 'off' ) {
			return JSON.stringify( '' )
		}

		const fields = Object.entries( props ).filter( ( [ key ] ) => key.startsWith( prefix ) )

		let settings = {}

		fields.forEach( ( [ key, tooltipOption ] ) => {
			settings[key.replace( prefix, '' )] = props[key]
		} )

		return JSON.stringify( settings )
	}

	handleTooltip() {
		const selectAll = selector => document.querySelectorAll( selector )
		const settingsAll = selectAll( '.difl_pricingtable .difl-pricing-table-wrapper' );

		[ ...settingsAll ].forEach( table => {
			const handleTooltip = () => {
				const tooltips = table.querySelectorAll(
					'.difl_pricingtableitem .item-feature' );
				[ ...tooltips ].forEach( ( tooltip, index ) => {
					const item_selector = tooltip.closest( '.difl_pricingtableitem' ).classList.value.split( ' ' ).filter( function ( class_name ) {
						return class_name.indexOf( 'difl_pricingtableitem_' ) !== -1
					} )[0]

					const tipper = tooltips[index].querySelector( '.feature_text' )

					// Check if table.dataset and tooltip.dataset exist before accessing them
					if ( table.dataset && table.dataset.tooltip ) {
						const tooltipSettings = JSON.parse( table.dataset.tooltip )
						if ( tooltip.dataset && tooltip.dataset.tooltip ) {
							const content = JSON.parse(
								tooltip.dataset.tooltip ).main_content

							if ( tooltipSettings && content ) {
								tipper.style.cursor = tooltipSettings.mouse_style

								const {
									animation,
									placement,
									trigger,
									duration,
									interactive
								} = tooltipSettings
								const options = {
									content,
									duration,
									animation,
									placement,
									trigger,
									interactive,
									allowHTML: true,
									maxWidth: parseInt( tooltipSettings.max_width ),
									offset: [ parseInt( tooltipSettings.offset_distance_vertical ), parseInt( tooltipSettings.offset_distance ) ],
									theme: `.${ item_selector }`,
								}

								tippy( tipper, options ) // Tooltip setup
							}
						}
					}
				} )
			}

			handleTooltip();
		} )
	}

	handleAnimation() {
		const selectAll = selector => document.querySelectorAll( selector )
		const settingsAll = selectAll( '.difl_pricingtable .difl-pricing-table-wrapper' );

		[ ...settingsAll ].forEach( table => {
			const handleAnimation = () => {
				if ( table.dataset && table.dataset.animation ) {
					const animation = JSON.parse( table.dataset.animation )
					if ( ! animation ) {
						return;
					}
					const { types, delay, duration } = animation;
					const childs = [ ...table.querySelectorAll( '.difl_pricingtableitem' ) ];
					childs.forEach( ( child, index = index + 1 ) => {
						child.style['animation-delay'] = (delay.replace( 'ms', '' ) * index) + 'ms';
						child.classList.add( 'animated' );
						child.classList.add( types );
						child.style['animation-duration'] = duration;
						if ( child.querySelector( '.item-ribbon' ) ) {
							setTimeout( () => {
								child.classList.remove( 'animated' );
								child.classList.remove( types );
								child.style.animationDuration = '';
								child.style.animationDelay = '';
							}, duration )
						}
					} )
				}
			}

			handleAnimation();
		} )
	}

	render() {
		const props = this.props
		return <div className="difl-pricing-table-wrapper"
					data-tooltip={ this.getSettings( 'enable_feature_tooltip',
						'feature_text_tooltip_' ) }
					data-animation={ this.getSettings( 'enable_animation', 'difl_animation_' ) }
		>{ 0 !== this.props.content.length
			? this.props.content
			: <h2 className="df-empty-notice">Please add new items to continue.</h2> }</div>
	}
}