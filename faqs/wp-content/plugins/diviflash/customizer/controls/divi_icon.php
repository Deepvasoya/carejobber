<?php
/**
 * Customizer Heading.
 *
 * @since   1.0.0
 * @package DIFL\Customizer\Controls
 */

namespace DIFL\Customizer\Controls;

/**
 * Divi Icon picker control
 */
class Divi_Icon extends \WP_Customize_Control {

	/**
	 * The control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'icon_picker';

	/**
	 * Send to _js json.
	 *
	 * @return array
	 */
	public function json() {
		$json         = parent::json();
		$json['id']   = $this->id;
		$json['link'] = $this->get_link();

		return $json;
	}


	public function render_content() {
		?>
        <label>
			<?php if ( ! empty( $this->label ) ) : ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			et_pb_font_icon_list(); ?>
            <input type="hidden" class="et_selected_icon" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
        </label>
		<?php
	}
}