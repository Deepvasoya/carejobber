<?php

namespace DIFL\Customizer\Controls;

class Text_Preloader extends \WP_Customize_Control {

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
	public $type = 'difl-preloader-text';

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
        <div class='difl-loader-list text-preloader'>

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
            <div class='difl_preloader_radio text_preloader' data-settings={{settings}}>
                <input
                        id="{{ inputId + '-' + value }}"
                        type='radio'
                        value='{{ value }}'
                        name='{{ inputId }}'
                        data-customize-setting-key-link='default'
                        {{{ describedByAttr }}}
                >
                <label class="difl_preloader_text_label" for="{{ inputId + '-' + value }}">
                    <div class='difl_preloader_type_wrapper  {{key}}'></div>
                </label>
            </div>
            <# } ); #>
        </div>
		<?php

	}

	public function enqueue_script() {?>
        <style>

            .difl-loader-list.text-preloader{
                grid-template-columns: 1fr;
            }

            .difl_preloader_radio.text_preloader .difl_preloader_text_label{
                width: 100%;
            }

            .difl_preloader_radio.text_preloader .difl_preloader_type_wrapper svg text {
                font-size: 36px;
                stroke-width: 2;
                fill: #626c60;
                stroke: #d0e7ca;
                letter-spacing: -3px;
                word-spacing: 1rem;
            }


            @keyframes difl-text-type-animate-preset-one {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 25%;
                    stroke-dasharray: 0 32%;
                }

                50% {
                    fill: transparent;
                }

                80%, 100% {
                    fill: #626c60;
                    stroke-dashoffset: -25%;
                    stroke-dasharray: 32% 0;
                }
            }

            @keyframes difl-text-type-animate-preset-two {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 20%;
                    stroke-dasharray: 0 40%;
                }

                30% {
                    fill: transparent;
                    stroke-dashoffset: 10%;
                    stroke-dasharray: 20% 20%;
                }

                60% {
                    fill: transparent;
                    stroke-dashoffset: -10%;
                    stroke-dasharray: 40% 0;
                }

                90%, 100% {
                    fill: #626c60;
                    stroke-dashoffset: -30%;
                    stroke-dasharray: 50% 0;
                }
            }

            @keyframes difl-text-type-animate-preset-three {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 30%;
                    stroke-dasharray: 0 50%;
                }

                40% {
                    fill: transparent;
                    stroke-dashoffset: 15%;
                    stroke-dasharray: 25% 25%;
                }

                70% {
                    fill: transparent;
                    stroke-dashoffset: 0;
                    stroke-dasharray: 50% 0;
                }

                100% {
                    fill: #626c60;
                    stroke: transparent;
                    stroke-dashoffset: 0;
                    stroke-dasharray: 50% 0;
                }
            }

            @keyframes difl-text-type-animate-preset-four {
                0% {
                    fill: transparent;
                    stroke-dashoffset: 40%;
                    stroke-dasharray: 0 60%;
                }

                25% {
                    fill: transparent;
                    stroke-dashoffset: 20%;
                    stroke-dasharray: 30% 30%;
                }

                50% {
                    fill: transparent;
                    stroke-dashoffset: 0;
                    stroke-dasharray: 60% 0;
                }

                75%, 100% {
                    fill: #626c60;
                    stroke-dashoffset: -20%;
                    stroke-dasharray: 60% 0;
                }
            }

        </style>

        <script>
            (function () {
                let found = false;
                const addPreloaderElement = () => {
                    const selectors = [...document.querySelectorAll('.difl_preloader_radio.text_preloader')];
                    if (!selectors.length) {
                        return;
                    }
                    if (selectors.length) {
                        found = true;
                    }
                    const svg = `<svg xmlns='http://www.w3.org/2000/svg'>
                            <text x='45%' y='40%' dy='.3em' text-anchor='middle' class='svg-text'>
								PRELOADER TEXT
                            </text>
                        </svg>`;
                    selectors.forEach(selector => {
                        const settings = JSON.parse(selector.dataset.settings);
                        const key = Object.keys(settings)[0];
                        const element = document.querySelector('.' + key);
                        element.insertAdjacentHTML('beforeend', svg);
                    })

                    const animations = [...document.querySelectorAll('.difl_preloader_radio.text_preloader .difl_preloader_type_wrapper svg text')]
                    animations.forEach(element=>{
                        const item = [...element.closest('div').classList][1];
                        element.style.animation = `4s infinite alternate difl-text-type-animate-preset-${item}`;
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