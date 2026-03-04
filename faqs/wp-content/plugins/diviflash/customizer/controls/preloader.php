<?php

namespace DIFL\Customizer\Controls;

class Preloader extends \WP_Customize_Control {

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
		add_action( 'customize_controls_head', [ $this, 'enqueue_script' ] );
	}

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'difl-preloader-preset';

	/**
	 * Send to _js json.
	 *
	 * @return array
	 */
	public function json() {
		$json            = parent::json();
		$json['id']      = $this->id;
		$json['link']    = $this->get_link();
		$json['value']   = $this->value();
		$json['choices'] = $this->choices;

		return $json;
	}

	public function content_template() { ?>
        <#
        var inputId = _.uniqueId( 'customize-control-default-input-' );
        var descriptionId = _.uniqueId( 'customize-control-default-description-' );
        var describedByAttr = data.description ? ' aria-describedby="' + descriptionId + '" ' : '';
        #>

        <# if ( data.label ) { #>
        <label for='{{ inputId }}' class='customize-control-title'>
            {{ data.label }}
        </label>
        <# } #>
        <# if ( data.description ) { #>
        <span id='{{ descriptionId }}' class='description customize-control-description'>{{{ data.description }}}</span>
        <# } #>
        <div class='difl-loader-list'>

            <# _.each( data.choices, function( val, key ) { #>
            <#
            var value, text;
            if ( _.isObject( val ) ) {
            value = val.value;
            text = val.text;
            } else {
            value = key;
            text = val;
            }
            let settings = JSON.stringify({[key]:val})
            #>
            <div class='difl_preloader_radio' data-settings={{settings}}>
                <input
                        id="{{ inputId + '-' + value }}"
                        type='radio'
                        value='{{ value }}'
                        name='{{ inputId }}'
                        data-customize-setting-key-link='default'
                        {{{ describedByAttr }}}
                >
                <label for="{{ inputId + '-' + value }}">
                    <div class="difl_preloader_type_wrapper  {{key}}"></div>
                </label>
            </div>
            <# } ); #>
        </div>
		<?php

	}

	public function enqueue_script() { ?>
        <script>
            (function () {
                let found = false;
                const addPreloaderElement = () => {
                    const selectors = [...document.querySelectorAll('.difl_preloader_radio')];
                    if (!selectors.length) {
                        return;
                    }
                    if (selectors.length) {
                        found = true;
                    }

                    selectors.forEach(selector => {
                        const settings = JSON.parse(selector.dataset.settings)
                        const key = Object.keys(settings)[0];
                        const element = document.querySelector('.' + key)
                        element.insertAdjacentHTML('beforeend', '<div></div>'.repeat(settings[key]))
                    })
                }

                let intervalId = setInterval(() => {
                    addPreloaderElement();
                    if (found) {
                        clearInterval(intervalId)
                    }
                }, 500)

            })()
        </script>
		<?php
	}
}