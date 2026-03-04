<?php
/**
 * Render Pods text field
 * Supported tag - br, em, strong, b, p, ul, ol, li
 * @param Array $settings
 * @return String
 */
 function df_pod_render_text_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = wp_kses($field_value, df_allowed_html_for_text_input());

    return $pod_field_data !== '' ?
        sprintf('%4$s%1$s%2$s%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings)
        ): '';
}

/**
 * Render Pods Number field
 * @param Array $settings
 * @return String
 */
function df_pod_render_number_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = wp_kses($field_value, df_allowed_html_for_text_input());

    return $pod_field_data !== '' ?
        sprintf('%4$s%1$s%2$s%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings)
        ) : '';
}

/**
 * Render Pods textarea field
 * @param Array
 * @return String
 */
function df_pod_render_textarea_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = wp_kses($field_value, df_allowed_html_for_text_input());

    return $pod_field_data !== '' ?
            sprintf('%4$s%1$s%2$s%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings)
        ) : '';
}

/**
 * Render Pods range field
 * @param Array
 * @return String
 */
function df_pod_render_range_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = wp_kses($field_value, df_allowed_html_for_text_input());

    return $pod_field_data !== '' ?
            sprintf('%4$s%1$s%2$s%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings)
        ) : '';
}

/**
 * Render Pods email field value
 * @param Array
 * @return String
 */
function df_pod_render_email_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = esc_attr($field_value);
    $email_text     = $settings['pod_email_text'] !== '' ? esc_html($settings['pod_email_text']) : $pod_field_data;

    if($pod_field_data !== '') {
        $pod_field_data = sprintf('%5$s%1$s<a href="mailto:%2$s">%4$s</a>%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            $email_text,
            df_cpt_render_icon_image($settings)
        );
    }

    return $pod_field_data;
}

/**
 * Render Pods URL field type
 * @param Array
 * @return String
 */
function df_pod_render_url_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = esc_attr($field_value);
    $url_text       = $settings['pod_url_text'] !== '' ? esc_html($settings['pod_url_text']) : $pod_field_data;
    $url_target     = $settings['pod_url_new_window'] === 'on' ? 'target="_blank"' : '';

    if($pod_field_data !== '') {
        $pod_field_data = sprintf('%6$s%1$s<a href="%2$s" %5$s>%4$s</a>%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            $url_text,
            $url_target,
            df_cpt_render_icon_image($settings)
        );
    }

    return $pod_field_data;
}

/**
 * Get AFC image url
 */
function df_get_pod_image_url( $data ) {
    if(is_array($data)) {
        return $data['guid'];
    } elseif( is_int($data) ) {
        return wp_get_attachment_url($data);
    }
    return $data;
}

function is_image_for_pod($url) {
    $image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'svg'];
    $extension        = strtolower(pathinfo($url, PATHINFO_EXTENSION));

    return in_array($extension, $image_extensions);
}

/**
 * Render POD Image field type
 * @param Array
 * @return String
 */
function df_pod_render_image_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_image_url  = df_get_pod_image_url($field_value);
    $image_alt_text =  df_image_alt_by_url($pod_image_url);

    if( !empty($pod_image_url) && is_image_for_pod($pod_image_url) ) {
        return sprintf('%4$s%1$s<img class="df-pod-image" alt="%5$s" src="%2$s" />%3$s',
            df_pod_before_after($settings)['before'],
            esc_attr($pod_image_url),
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings),
            esc_attr($image_alt_text)
        );
    }else{
        return 'Only image files are supported.';
    }
}
/**
 * Render Pods Select Field
 * @param Array
 * @return String
 */
function df_pod_render_select_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = wp_kses($field_value, df_allowed_html_for_text_input());

    return $pod_field_data !== '' ?
            sprintf('%4$s%1$s%2$s%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings)
        ) : '';
}

/**
 * Render POD Date Field
 * @param Array
 * @return String
 */
function df_pod_render_date_type($settings) {
    global $post;
    $pod            = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = wp_kses($field_value, df_allowed_html_for_text_input());

    return $pod_field_data !== '' ?
            sprintf('%4$s%1$s%2$s%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings)
        ) : '';
}

/**
 * Render Pods wysiwyg Field
 * @param Array
 * @return String
 */
function df_pod_render_wysiwyg_type($settings) {
    global $post;
    $pod = pods($post->post_type, $post->ID);
    $field_value    = $pod->field($settings['pod_field']);
    $pod_field_data = wp_kses_post($field_value);

    return $pod_field_data !== '' ?
            sprintf('%4$s%1$s%2$s%3$s',
            df_pod_before_after($settings)['before'],
            $pod_field_data,
            df_pod_before_after($settings)['after'],
            df_cpt_render_icon_image($settings)
        ) : '';
}

/**
 * Render Pods Fields before and after text
 * @param Array $settings
 * @return Array
 */
function df_pod_before_after($settings){
    $before_label = wp_kses($settings['pod_before_label'], df_allowed_html_for_text_input());
    $after_label  = wp_kses($settings['pod_after_label'], df_allowed_html_for_text_input());

    return array(
        'before' => $before_label !== '' ? sprintf('<span class="before-text">%1$s</span>', $before_label) : '',
        'after' => $after_label !== '' ? sprintf('<span class="after-text">%1$s</span>', $after_label) : ''
    );
}

 /**
 * Render markup for Pods field
 * Supported fields: 'text', 'number', 'paragraph', 'email', 'website', 'file/image('jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'tiff', 'svg').', 'datetime', 'date', 'time', 'wysiwyg'
 * @param Array $settings
 * @param Boolean
 * @return String
 */
 function df_pod_fields_function($settings, $builder = false){
    global $post;
	 if (!class_exists('Df_Pod_Fields')){
		 require_once( DIFL_MAIN_DIR . '/includes/classes/df-pod-fields.php' );
	 }
    // get pod data stored in this instance
    // If no instance found then create instance and store all fields data
    $fields_storage = Df_Pod_Fields::getInstance();
    if(!isset($settings['pod_field'])) return;

    $field_type    = !empty($fields_storage->pod_fields_type) && isset($fields_storage->pod_fields_type[$settings['pod_field']]) ?
    $fields_storage->pod_fields_type[$settings['pod_field']] : array();
    $module_class  = isset($settings['module_vb_class']) ? $settings['module_vb_class'] : '';
    $default_value = '';

    if( class_exists('Pods') ) {
        $default_value = "No PODs field selected";
    }
    ob_start();

    switch($field_type) {
        case 'text':
            echo et_core_esc_previously(df_pod_render_text_type($settings));
            break;
        case 'number':
            echo et_core_esc_previously(df_pod_render_number_type($settings));
            break;
        case 'paragraph':
            echo et_core_esc_previously(df_pod_render_textarea_type($settings));
            break;
        case 'range':
            echo et_core_esc_previously(df_pod_render_range_type($settings));
            break;
        case 'email':
            echo et_core_esc_previously(df_pod_render_email_type($settings));
            break;
        case 'website':
            echo et_core_esc_previously(df_pod_render_url_type($settings));
            break;
        case 'file':
            echo et_core_esc_previously(df_pod_render_image_type($settings));
            break;
        case 'select':
            echo et_core_esc_previously(df_pod_render_select_type($settings));
            break;
        case 'time':
            echo et_core_esc_previously(df_pod_render_date_type($settings));
            break;
        case 'date':
            echo et_core_esc_previously(df_pod_render_date_type($settings));
            break;
        case 'datetime':
            echo et_core_esc_previously(df_pod_render_date_type($settings));
            break;
        case 'wysiwyg':
            echo et_core_esc_previously(df_pod_render_wysiwyg_type($settings));
            break;
        default:
            echo esc_html($default_value);
    }
    
    $data = ob_get_clean();

    if( !empty($data) ) {
        echo sprintf('<div class="df-item-wrap df-item-pod %1$s %2$s">', esc_attr($settings['class']), esc_attr($module_class));
            echo et_core_esc_previously(df_render_pattern_or_mask_html($settings['background_enable_pattern_style'], 'pattern'));
            echo et_core_esc_previously(df_render_pattern_or_mask_html($settings['background_enable_mask_style'], 'mask'));
            echo '<div class="df-pod-field-inner">'. et_core_esc_previously($data) .'</div>';
        echo '</div>';
    }else{
	    echo sprintf('<span class="df-item-wrap df-item-pod df-empty-element %1$s %2$s"></span>',
		    esc_attr($settings['class']),
		    esc_attr($module_class)
	    );
    }

}

