<?php
/**
 * Customizer panel type enforcement
 *
 * @package DIFL\Customizer\Types
 */

namespace DIFL\Customizer\Types;

/**
 * Class Panel
 *
 * @package DIFL\Customizer\Types
 */
class Panel {
	/**
	 * ID of panel
	 *
	 * @var string the control ID.
	 */
	public $id;

	/**
	 * Args for panel instance.
	 *
	 * @var array args passed into panel instance.
	 */
	public $args = [];

	/**
	 * Constructor.
	 *
	 * @param string $id the control id.
	 * @param array $args the panel args.
	 */
	public function __construct( $id, $args ) {
		$this->id   = $id;
		$this->args = $args;
	}
}
