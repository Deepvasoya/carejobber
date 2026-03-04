<?php

namespace DIFL\Feature;

class Contact_Form {
	const SLUG = 'difl_contact_form';

	const PREFIX = 'difl_cf';

	public function __construct() {
		add_action( 'admin_init', [ $this, 'handle_export' ] );

		add_action( 'edit_form_top', [ $this, 'rename_edit_title' ] );

		add_action( 'init', [ $this, 'register_post_type' ] );

		add_filter( 'post_row_actions', [ $this, 'disable_quick_edit_form_submission' ], 10, 2 );

		add_filter( "manage_{$this->slug}_posts_columns", [ $this, 'updated_columns' ] );

		add_action( "manage_{$this->slug}_posts_custom_column", [ $this, 'updated_columns_data' ], 10, 2 );

		add_action( 'et_pb_contact_form_submit', [ $this, 'populate_entry' ], 10, 3 );

		add_filter( 'et_pb_all_fields_unprocessed_et_pb_contact_form', [ $this, 'add_contact_form_setting' ] );

		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		add_action( "manage_posts_extra_tablenav", [ $this, 'export_button' ] );

	}

	public function __get( $name ) {
		if ( 'slug' === $name ) {
			return self::SLUG;
		}

		throw new \Exception( sprintf( 'Prop %s not found', $name ) );
	}

	public function handle_export() {
		$data = wp_unslash( $_REQUEST ); // phpcs:disable WordPress.Security.NonceVerification.Recommended -- Hooking on admin_init
		if ( ! array_key_exists( 'difl_cf_export', $data ) ) {
			return;
		}

		if ( 'Export' !== $data['difl_cf_export'] ) {
			return;
		}

		$file_name = isset( $data['difl_cf_export_options']['file_name'] ) ? sanitize_text_field( $data['difl_cf_export_options']['file_name'] ) : __( 'Divi Contact Form', 'divi_flash' );
		$date_from = isset( $data['difl_cf_export_options']['date_from'] ) ? sanitize_text_field( $data['difl_cf_export_options']['date_from'] ) : '';
		$date_to   = isset( $data['difl_cf_export_options']['date_to'] ) ? sanitize_text_field( $data['difl_cf_export_options']['date_to'] ) : '';
		$unique_id = isset( $data['difl_cf_export_options']['unique_id'] ) ? sanitize_text_field( $data['difl_cf_export_options']['unique_id'] ) : '';

		$additional_fields = [
			'page_name'           => 'Page_Submitted_Name',
			'difl_cf_page_id'     => 'Page_ID',
			'page_url'            => 'Page_URL',
			'date_submitted'      => 'Date_Submitted',
			'difl_cf_read_status' => 'Read_Status',
			'difl_cf_read_date'   => 'Read_Date',
		];

		$additional_fields_required = [];

		foreach ( $additional_fields as $key => $value ) {
			if ( isset( $$key ) && $$key == '1' ) {
				$additional_fields_required[ $key ] = $value;
			}
		}

		$date_from = isset( $date_from ) ? sanitize_text_field( $date_from ) : '';
		$date_to   = isset( $date_to ) ? sanitize_text_field( $date_to ) : '';

		$args = [
			'post_type'      => $this->slug,
			'posts_per_page' => - 1,
			'fields'         => 'ids',
			'meta_key'       => 'difl_cf_contact_form_unique_id',
			'meta_value'     => $unique_id,
		];

		if ( ! empty( $date_from ) || ! empty( $date_to ) ) {
			$args['date_query'] = [];

			if ( ! empty( $date_from ) ) {
				$args['date_query'][] = [
					'after'     => gmdate( 'Y-m-d', strtotime( $date_from ) ),
					'inclusive' => true,
				];
			}

			if ( ! empty( $date_to ) ) {
				$args['date_query'][] = [
					'before'    => gmdate( 'Y-m-d', strtotime( $date_to . ' +1 day' ) ),
					'inclusive' => true,
				];
			}
		}

		if ( ! empty( $data['post'] ) ) {
			$args['post__in'] = $data['post'];
		}

		$posts       = new \WP_Query( $args );
		$all_keys    = [];
		$submissions = [];

		if ( $posts->have_posts() ) {
			$post_ids = $posts->posts;
			$meta_key = 'processed_fields_values';

			foreach ( $post_ids as $post_id ) {
				$submission                   = get_post_meta( $post_id, $meta_key, true );
				$submission_keys              = array_keys( $submission );
				$extra_keys                   = array_diff( $submission_keys, $all_keys );
				$all_keys                     = array_merge( $all_keys, $extra_keys );
				$submission                   = array_combine( array_keys( $submission ), array_column( $submission, 'value' ) );
				$additional_fields_submission = [];

				foreach ( $additional_fields_required as $key => $value ) {
					if ( in_array( $key, [ 'page_url', 'date_submitted', 'page_name' ] ) ) {
						$additional_details                     = get_post_meta( $post_id, 'additional_details', true );
						$additional_fields_submission[ $value ] = $additional_details[ $key ];
					} else {
						$additional_fields_submission[ $value ] = get_post_meta( $post_id, $key, true );
					}
				}

				$attachments = get_post_meta( $post_id, 'difl_cf_attachment', true );

				if ( $attachments ) {
					foreach ( $attachments as $key => $attachment ) {
						$additional_fields_required[ wp_get_attachment_url( $attachment ) ] = $key;
						$additional_fields_submission[ $key ]                               = wp_get_attachment_url( $attachment );
					}
				}

				$combined_submission = array_merge( $submission, $additional_fields_submission );

				array_push( $submissions, $combined_submission );
			}

		}

		$additional_fields_values = array_values( $additional_fields_required );
		$all_keys                 = array_merge( $all_keys, $additional_fields_values );
		$empty_headers            = array_fill_keys( $all_keys, '' );
		$filename                 = ! empty( $file_name ) ? $file_name . gmdate( 'Ymd_His' ) . '.csv' : gmdate( 'Ymd_His' ) . '.csv';
		$tmp_file                 = wp_tempnam();
		$fh                       = fopen( $tmp_file, 'w' );

		fprintf( $fh, chr( 0xEF ) . chr( 0xBB ) . chr( 0xBF ) );

		fputcsv( $fh, $all_keys );

		foreach ( $submissions as $submission ) {
			$combinedArray = wp_parse_args( $submission, $empty_headers );
			fputcsv( $fh, array_values( $combinedArray ) );
		}

		fclose( $fh );

		header( 'Content-Type: application/csv' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		header( 'Content-Length: ' . filesize( $tmp_file ) );
		readfile( $tmp_file );

		unlink( $tmp_file );

		exit;
	}

	public function rename_edit_title( $post ) {
		if ( $post->post_type == self::SLUG ) {
			echo '<h1>' . esc_html( get_the_title( $post->ID ) ) . '</h1>';
		}
	}

	public function register_post_type() {
		$args = [
			'post_type'      => self::SLUG,
			'posts_per_page' => - 1,
			'meta_query'     => [
				[
					'key'     => 'difl_cf_read_status',
					'value'   => false,
					'compare' => '=',
				],
			],
		];

		$query = new \WP_Query( $args );

		$count = $query->found_posts;

		$labels = [
			'name'               => _x( 'Divi Contact Form', 'post type general name', 'divi_flash' ),
			'singular_name'      => _x( 'Divi Form Submission', 'post type singular name', 'divi_flash' ),
			'add_new'            => __( 'Add New', 'divi_flash' ),
			'add_new_item'       => __( 'Add New Divi Form Submission', 'divi_flash' ),
			'edit_item'          => false,
			'new_item'           => __( 'New Divi Form Submission', 'divi_flash' ),
			'view_item'          => __( 'View Divi Form Submission', 'divi_flash' ),
			'search_items'       => __( 'Search Divi Form Submissions', 'divi_flash' ),
			'all_items'          => $count ? sprintf( __( 'Divi Contact Form <span class="menu-counter">%d</span>', 'divi_flash' ), $count ) : '',
			'not_found'          => __( 'No Divi form submissions found', 'divi_flash' ),
			'not_found_in_trash' => __( 'No Divi form submissions found in Trash', 'divi_flash' ),
			'parent_item_colon'  => '',
		];

		$args = [
			'labels'              => $labels,
			'menu_icon'           => 'dashicons-email',
			'public'              => false, // Only available to admins in the backend
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'show_ui'             => true,
			'show_in_nav_menus'   => false,
			'show_in_menu'        => false,
			'query_var'           => true,
			'rewrite'             => false,
			'capability_type'     => 'post',
			'capabilities'        => [
				'create_posts' => 'do_not_allow', // Disallow creating new posts
			],
			'map_meta_cap'        => true,
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 101,
			'supports'            => [ null ],
		];

		register_post_type( self::SLUG, $args );

	}

	public function disable_quick_edit_form_submission( $actions, $post ) {
		if ( $post->post_type !== self::SLUG || ! is_array( $actions ) ) {
			return $actions;
		}

		if ( array_key_exists( 'edit', $actions ) ) {
			return $actions;
		}

		$actions['edit'] = str_replace( 'Edit', __( 'View', 'divi_flash' ), $actions['edit'] );
		unset( $actions['inline hide-if-no-js'] );

		return $actions;
	}

	public function updated_columns( $columns ) {
		unset( $columns['date'] );
		$columns['read_status'] = __( 'Read', 'divi_flash' );
		$columns['page_name']   = __( 'Page Submitted', 'divi_flash' );
		$columns['email']       = __( 'Email', 'divi_flash' );
		$columns['date']        = __( 'Date Submitted', 'divi_flash' );

		return $columns;
	}

	public function updated_columns_data( $column, $post_id ) {
		$additional_details = get_post_meta( $post_id, 'additional_details', true );
		$submission_details = get_post_meta( $post_id, 'processed_fields_values', true );
		$read_status        = get_post_meta( $post_id, 'difl_cf_read_status', true );
		$read_date          = get_post_meta( $post_id, 'difl_cf_read_date', true );

		switch ( $column ) {
			case 'page_name':
				if ( isset( $additional_details['page_name'] ) ) {
					echo esc_html( $additional_details['page_name'] );
				}
				break;
			case 'email':
				if ( isset( $submission_details['email'] ) && isset( $submission_details['email']['value'] ) ) {
					echo esc_html( $submission_details['email']['value'] );
				}
				break;
			case 'date_submitted':
				if ( isset( $additional_details['date_submitted'] ) ) {
					echo esc_html( $additional_details['date_submitted'] );
				}
				break;
			case 'read_status':
				if ( isset( $read_status ) && ( $read_status == false ) ) {
					echo '<span class="dashicons dashicons-email-alt"></span>';
				} else {
					echo '<span>' . esc_html( isset( $read_date ) ? $read_date : '' ) . '</span>';
				}
				break;
			default:
				break;
		}

	}

	public function populate_entry( $processed_fields_values, $et_contact_error, $contact_form_info ) {
		if ( $et_contact_error == true ) {
			return;
		}

		$post_data = [
			'post_title'  => '',
			'post_type'   => self::SLUG,
			'post_status' => 'publish',
		];

		$post_id = wp_insert_post( $post_data );
		update_post_meta( $post_id, 'processed_fields_values', $processed_fields_values );

		$current_page_id = get_the_ID();

		$additional_details = [
			'page_id'         => $current_page_id,
			'page_name'       => get_the_title( $current_page_id ),
			'page_url'        => get_permalink( $current_page_id ),
			'date_submitted'  => current_time( 'mysql' ),
			'read_status'     => false,
			'read_date'       => null,
			'contact_form_id' => sanitize_text_field( $contact_form_info['contact_form_id'] ),
		];

		update_post_meta( $post_id, 'additional_details', $additional_details );

		update_post_meta( $post_id, 'difl_cf_contact_form_unique_id', $contact_form_info['contact_form_unique_id'] );

		update_post_meta( $post_id, 'difl_cf_page_id', $current_page_id );

		update_post_meta( $post_id, 'difl_cf_read_status', false );

		update_post_meta( $post_id, 'difl_cf_read_date', null );

		update_post_meta( $post_id, 'difl_attachments_read', false );
		$updated_post = [
			'ID'         => $post_id,
			'post_title' => 'Entry# ' . $post_id,
		];

		wp_update_post( $updated_post );
	}

	public function add_meta_boxes() {
		add_meta_box(
			'form-submission-details',
			__( 'Form Submission Details', 'divi_flash' ),
			[ $this, 'details_metabox' ],
			self::SLUG,
			'normal',
			'high'
		);

		add_meta_box(
			'form-submission-additional-details',
			__( 'Additional Details', 'divi_flash' ),
			[ $this, 'additional_metabox' ],
			self::SLUG,
			'normal',
			'high'
		);

	}

	public function add_form_submission_meta_boxes__free() {
		add_meta_box(
			'form-submission-details',
			__( 'Form Submission Details', 'divi_flash' ),
			[ $this, 'render_form_submission_meta_box__free' ],
			'lwp_form_submission',
			'normal',
			'high'
		);

	}

	public function details_metabox( $post ) {
		$post_id   = $post->ID;
		$post_meta = get_post_meta( $post_id );

		$additional_details = get_post_meta( $post_id, 'additional_details', true );
		$submission_details = get_post_meta( $post_id, 'processed_fields_values', true );
		$read_status        = get_post_meta( $post_id, 'difl_cf_read_status', true );
		$read_date          = get_post_meta( $post_id, 'difl_cf_read_date', true );

		if ( $read_status == false ) {
			update_post_meta( $post_id, 'difl_cf_read_status', true );
			update_post_meta( $post_id, 'difl_cf_read_date', current_time( 'mysql' ) );

		}

		?>

        <table class="wp-list-table widefat fixed striped" style="margin-bottom:10px;">
            <thead>
            <tr>
                <th scope="col"><?php esc_attr_e( 'Field Name', 'divi_flash' ); ?></th>
                <th scope="col"><?php esc_attr_e( 'Value', 'divi_flash' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php foreach ( $submission_details as $value ) : ?>
                <tr>
                    <td><strong><?php echo esc_html( $value['label'] ); ?>:</strong></td>
                    <td><?php echo esc_html( $value['value'] ); ?></td>
                </tr>
			<?php endforeach; ?>
            </tbody>
        </table>

		<?php if ( isset( $submission_details['email'] ) ): ?>
            <a class="button button-primary"
               href="mailto:<?php echo esc_attr( $submission_details['email']['value'] ); ?>"
               type="button"><?php echo esc_html__( 'Reply via Email', 'divi_flash' ); ?></a>
		<?php endif; ?>

		<?php
	}

	public function additional_metabox( $post ) {
		$post_meta = get_post_meta( $post->ID );

		$additional_details     = get_post_meta( $post->ID, 'additional_details', true );
		$read_date              = get_post_meta( $post->ID, 'difl_cf_read_date', true );
		$contact_form_unique_id = get_post_meta( $post->ID, 'difl_cf_contact_form_unique_id', true );
		$attachments            = get_post_meta( $post->ID, 'difl_cf_attachment', true );

		$page_id = 0;
		if ( isset( $additional_details['page_id'] ) && absint( $additional_details['page_id'] ) ) {
			$page_id = absint( $additional_details['page_id'] );
		}

		?>

        <table class="wp-list-table widefat fixed striped">
            <tr>
                <td><strong><?php echo esc_html( 'Page Submitted:', 'divi_flash' ) ?></strong></td>
                <td>
					<?php echo esc_html( $additional_details['page_name'] ); ?>
					<?php if ( 0 !== $page_id ) : ?>
                        <a href="<?php echo esc_url( get_edit_post_link( $page_id ) ); ?>"><?php esc_html_e( 'Edit', 'divi_flash' ); ?></a> |
                        <a href="<?php echo esc_url( get_permalink( $page_id ) ); ?>"><?php esc_html_e( 'View', 'divi_flash' ); ?></a>
					<?php endif; ?>
                </td>
            </tr>
            <tr>
                <td><strong><?php echo esc_html( 'Date Submitted:', 'divi_flash' ) ?></strong></td>
                <td><?php echo esc_html( $additional_details['date_submitted'] ); ?></td>
            </tr>
            <tr>
                <td><strong><?php echo esc_html( 'Read Date:', 'divi_flash' ) ?></strong></td>
                <td><?php echo esc_html( $read_date ); ?></td>
            </tr>
            <tr>
                <td><strong><?php echo esc_html( 'Contact Form Unique ID:', 'divi_flash' ) ?></strong></td>
                <td><?php echo esc_html( $contact_form_unique_id ); ?></td>
            </tr>

			<?php if ( $attachments ) { ?>
                <tr>
                    <td><strong><?php echo esc_html( 'Attachments', 'divi_flash' ) ?></strong></td>
                    <td><?php foreach ( $attachments as $label => $attachment ) {
							echo sprintf( '<a target="_blank" href="%1$s">%2$s</a></br>', esc_url( wp_get_attachment_url( $attachment ) ), esc_html( $label ) );
						} ?></td>
                </tr>
			<?php } ?>
        </table>

		<?php

	}

	public function render_form_submission_meta_box__free( $post ) {

		$submission_details = get_post_meta( $post->ID, 'processed_fields_values', true );
		$read_status        = get_post_meta( $post->ID, 'difl_cf_read_status', true );

		if ( false == $read_status ) {
			update_post_meta( $post->ID, 'difl_cf_read_status', true );
			update_post_meta( $post->ID, 'difl_cf_read_date', current_time( 'mysql' ) );
		}

		?>

        <table class="wp-list-table widefat fixed striped" style="margin-bottom:10px;">
            <thead>
            <tr>
                <th scope="col"><?php esc_attr_e( 'Field Name', 'divi_flash' ); ?></th>
                <th scope="col"><?php esc_attr_e( 'Value', 'divi_flash' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php if ( isset( $submission_details['name'] ) ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $submission_details['name']['label'] ); ?>:</strong></td>
                    <td><?php echo esc_html( $submission_details['name']['value'] ); ?></td>
                </tr>
			<?php endif; ?>
			<?php if ( isset( $submission_details['email'] ) ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $submission_details['email']['label'] ); ?>:</strong></td>
                    <td><?php echo esc_html( $submission_details['email']['value'] ); ?></td>
                </tr>
			<?php endif; ?>
			<?php if ( isset( $submission_details['message'] ) ): ?>
                <tr>
                    <td><strong><?php echo esc_html( $submission_details['message']['label'] ); ?>:</strong></td>
                    <td><?php echo esc_html( $submission_details['message']['value'] ); ?></td>
                </tr>
			<?php endif; ?>
            </tbody>
        </table>

		<?php if ( isset( $submission_details['email'] ) ): ?>
            <a class="button button-primary"
               href="mailto:<?php echo esc_attr( $submission_details['email']['value'] ); ?>"
               type="button"><?php echo esc_html__( 'Reply via Email', 'divi_flash' ); ?></a>
		<?php endif; ?>

		<?php
	}

	public function add_contact_form_setting( $fields_unprocessed ) {
		$fields_unprocessed['_unique_id'] = [
			'label'           => __( 'Unique ID', 'divi_flash' ),
			'type'            => 'text',
			'attributes'      => 'readonly',
			'option_category' => 'basic_option',
			'toggle_slug'     => 'main_content',
		];

		return $fields_unprocessed;
	}

	public function export_button() {
		if ( ! array_key_exists( 'post_type', $_REQUEST ) ) {
			return;
		}
		if ( self::SLUG !== $_REQUEST['post_type'] ) {
			return;
		}
		echo "<input type='submit' name='difl_cf_export' id='post-query-submit' class='button' value='Export' >";
	}

}

if ( get_option( 'df_general_cf_support' ) ) {
	new Contact_Form();
}
