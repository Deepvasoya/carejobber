(() => {
	const handleAnimation = () => {
		if ( ! window.et_builder_version ) {
			const wrappers = document.querySelectorAll( ".difl-svg-animator-inner-wrapper" );

			wrappers.forEach( wrapper => {
				const parent = wrapper.parentNode;
				parent.style.opacity = "1";

				const config = JSON.parse( wrapper.dataset.config );

				const svg_id = "difl-svg-animator-" + config.svg_id;
				const svg_element = document.getElementById( svg_id );

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
					forceRender: /^(?!chrome|android).*(msie|edge|trident|safari)/i.test( window.navigator.userAgent )
				};

				const animate = ( iteration, once = false ) => {
					const animation = new Vivus( svg_id, vivus_config, () => {
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
					svg_element.addEventListener( [config.repeat_type], function () {
						animate( 1, true )
					} );
				}
			} );
		}
	}

	document.addEventListener( "DOMContentLoaded", handleAnimation );
})();