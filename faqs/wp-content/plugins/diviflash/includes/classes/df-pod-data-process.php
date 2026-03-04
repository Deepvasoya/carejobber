<?php
trait Df_Pod_Data_Process {

    /**
     * Store all the custom post type
     * in array
     */
    public $df_pod_cpt_list = ['select'    => 'Select post type'];

    /**
     * Store the dashboard extension value
     * this will depend on dashboard settings
     * 
     */
    public $pod_extension;

    /**
     * Store all Pods field for each cpt
     * 
     */
    public $df_pod_fields = [];

	/* POD Filter Declaration */
	public $df_pod_fields_for_filter        = [];
	public $df_pod_field_details_for_filter = [];
	public $df_pod_filter_fields            = [];
	public $df_pod_filter_field_options     = [];
	public $df_multi_filter_pod_field_type  = [];
	public $selected_post_type_for_pod;
	public $selected_pod_filter;
	public $selected_pod_filter_fields;
	/* POD Filter Declaration */

    /**
     * Initialize the pod support for
     * dynamic modules
     *  
     */
    public function df_pod_init($post_include = false) {
        $this->pod_extension = get_option('df_general_pod_field_support') === '1' && class_exists('Pods') ? 'on' : 'off';

        if( 'on' !== $this->pod_extension ) return;
        if( !class_exists('Pods') ) return;
        if(!$post_include){
			$postType = df_get_registered_post_type_options(false, false);
            $this->df_pod_cpt_list = array_merge($this->df_pod_cpt_list, $postType);
        }else{
			$postType = df_get_registered_post_type_options(false, false , true);
            $this->df_pod_cpt_list = array_merge($this->df_pod_cpt_list, $postType);  
        }
        
        $this->df_get_all_pod_fields();
    }

    /**
     * Get all fields for post types
     * 
     * @param String | $post_type
     */
    public function df_get_all_pod_fields() {
        $fields_storage = Df_Pod_Fields::getInstance();

        foreach($fields_storage->pod_fields_storage as $post_type=>$options) {
            $this->df_pod_fields["pod-".$post_type] = $this->add_settings_with_pod_fields($post_type,$options);
        }

		/* POD Filter On */
		$this->df_pod_fields_for_filter        = $fields_storage->processed_pod_fields_for_filter_options();
		$this->df_pod_field_details_for_filter = $fields_storage->pod_fields_with_details;

		foreach ($this->df_pod_fields_for_filter as $post_type=>$options){

			$this->df_pod_filter_fields['pod_filter_'.$post_type] = $this->add_settings_for_pod_filter($post_type);
			$this->df_pod_filter_field_options['pod_filter_option_'.$post_type] = $this->add_settings_for_pod_filter_options($post_type,$options);
			
			foreach ($options as $name => $label){
				$this->df_multi_filter_pod_field_type['pod_filter_field_type_'.$post_type.'_'.$name] = $this->add_settings_for_pod_filter_field_type($post_type,$label);
			}

		}
	    /* POD Filter Off */
    }

    /**
     * Pods Field settings for module
     * 
     * @param String $post_type
     * @param Array $options
     * @return Array
     */
    public function add_settings_with_pod_fields($post_type, $options){
        $options = array_merge(array('df_select_option' => 'Select a field'), $options);

        $df_pod_settings = array(
            'label'       => esc_html__('Select Pods Field', 'divi_flash'),
            'type'        => 'select',
            'options'     => $options,
            'default'     => 'select_option',
            'tab_slug'    => 'general',
            'toggle_slug' => 'settings',
            'show_if'     => array(
                'post_type_for_pod'  => $post_type,
                'type'               => 'pod_fields'
            )
        );

        return $df_pod_settings;
    }

	/* POD Filter Methods */
	public function add_settings_for_pod_filter($post_type){
		$df_pod_filter_settings = array(
			'label'   => esc_html__('POD Filter', 'divi_flash'),
			'type'    => 'yes_no_button',
			'options' => array(
				'off' => esc_html__('Off', 'divi_flash'),
				'on'  => esc_html__('On', 'divi_flash'),
			),
			'default' => '',
			'tab_slug'=> 'general',
			'show_if' => array(
				'post_type'    => $post_type,
				'post_display' => 'multiple_filter'
            ),
			'toggle_slug' => 'multi_filter_pod'
		);
		return $df_pod_filter_settings;
	}
	public function add_settings_for_pod_filter_options($post_type, $options){

		$df_pod_filter_settings = array(
			'label'       => esc_html__('Select Pods Field', 'divi_flash'),
			'type'        => 'multiple_checkboxes',
			'options'     => $options,
			'default'     => '',
			'tab_slug'    => 'general',
			'toggle_slug' => 'multi_filter_pod',
			'show_if'     => array(
				'pod_filter_' . $post_type => 'on',
				'post_type'    => $post_type,
				'post_display' => 'multiple_filter'
			)
		);
		return $df_pod_filter_settings;
	}

	public function add_settings_for_pod_filter_field_type($post_type, $label){
		$df_pod_filter_field_type_settings = array(
			'label'       => esc_html__($label.' Field Type', 'divi_flash'),
			'type'        => 'select',
			'options'     => array(
				'checkbox'=> esc_html__('Checkbox', 'divi_flash'),
				'select'  => esc_html__('Dropdown', 'divi_flash'),
			),
			'default'     => 'select',
			'tab_slug'    => 'general',
			'toggle_slug' => 'multi_filter_field_type',
			'show_if'     => array(
				'post_type'    => $post_type,
				'post_display' => 'multiple_filter',
				'pod_filter_'.$post_type => 'on',
            ),
			'option_category' => 'configuration'
		);
		foreach ($this->df_pod_field_details_for_filter[$post_type] as $single_field){

			if($single_field['label'] === $label && in_array($single_field['type'],['number', 'range'])){
				$df_pod_filter_field_type_settings['options']['range'] = esc_html__('Range', 'divi_flash');
			}

			if($single_field['label'] === $label && 'range' === $single_field['type']){
				$df_pod_filter_field_type_settings['default'] = 'range';
			}

		}

		return $df_pod_filter_field_type_settings;
	}

	public function get_pod_filter_values(){
		$this->selected_post_type_for_pod = $this->props['post_type'];
		$this->selected_pod_filter        = isset($this->props['pod_filter_'.$this->selected_post_type_for_pod]) ? $this->props['pod_filter_'.$this->selected_post_type_for_pod]: 'off';

		if('on' === $this->selected_pod_filter){
			$podFilterOption = $this->props['pod_filter_option_'.$this->selected_post_type_for_pod];
			$this->selected_pod_filter_options = isset($podFilterOption) ? $podFilterOption: '';
		}else{
			$this->selected_pod_filter_options = '';
		}

		$this->selected_pod_filter_fields = $this->get_multi_filter_pod_fields($this->selected_pod_filter_options);
	}

	public function get_multi_filter_pod_fields($selected_pod_filter_options){

		if ( !isset( $this->df_pod_fields_for_filter[ $this->selected_post_type_for_pod ] ) || !is_array( $this->df_pod_fields_for_filter[ $this->selected_post_type_for_pod ] ) ) {
			return [];
		}

		$main_value     = array();
		$selected_multi = explode("|", $selected_pod_filter_options);
		$list_multi_key = array_keys($this->df_pod_fields_for_filter[$this->selected_post_type_for_pod]);
		$iMax           = count( $list_multi_key );

		for($i =0; $i < $iMax; $i++){
			if( isset($selected_multi[$i]) && $selected_multi[$i] === 'on' ){
				$main_value[] = $list_multi_key[ $i ];
			}
		}
		return $main_value;
	}
	/* POD Filter Methods */

}


