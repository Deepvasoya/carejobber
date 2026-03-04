<?php
namespace DIFL\Customizer\Controls;

class Divi_Select extends \WP_Customize_Control {
	public $type = 'select';

	public function render_content() {
		?>
        <label>
			<?php if ( ! empty( $this->label ) ) : ?>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
                <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
			<?php endif; ?>

            <select <?php $this->link(); ?>>
				<?php
				foreach ( $this->choices as $value => $attributes ) {
					$data_output = '';

					if ( ! empty( $attributes['data'] ) ) {
						foreach( $attributes['data'] as $data_name => $data_value ) {
							if ( '' !== $data_value ) {
								$data_output .= sprintf( ' data-%1$s="%2$s"',
									esc_attr( $data_name ),
									esc_attr( $data_value )
								);
							}
						}
					}

					echo '<option value="' . esc_attr( $value ) . '"' . selected( $this->value(), $value, false ) . $data_output . '>' . esc_html( $attributes['label'] ) . '</option>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Value is escaped above.
				}
				?>
            </select>
        </label>
		<?php
	}
}