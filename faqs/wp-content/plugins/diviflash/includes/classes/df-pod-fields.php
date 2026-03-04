<?php
class Df_Pod_Fields{
    private static $instance = null;

    private $pod_extension;

    //Store the Pods field name with by custom post type.
    public $pod_fields_storage = [];

    //Store the field type with the pod field name
    public $pod_fields_type = [];

    //Supported pod field type. Other field will be skipped from $pod_fields_storage
    private $pod_supported_field_type = [
        'text',
        'number',
        'paragraph',
        'email',
        'website',
        'datetime',
        'date',
        'time',
        'file',
        'wysiwyg'
    ];

	/* POD Filter Declaration */
	private $pod_filter_supported_field_type = [
        'text',
		'number',
		'paragraph',
		'range',
    ];

	public $pod_fields_with_details = [];

    private function __construct(){
        $this->pod_extension = get_option('df_general_pod_field_support') === '1' ? 'on' : 'off';
        $this->process_all_posttype();
    }

    public static function getInstance(){
        if(self::$instance == null) {
            self::$instance = new Df_Pod_Fields();
        }
        return self:: $instance;
    }

    /**
     * Get all Pods field for by custom post type
     * and iterate over each post type 
     * 
     */
    private function process_all_posttype(){
        if('on' !== $this->pod_extension) {
            return array();
        }

        if(!class_exists('Pods')) {
            return array();
        }

        $post_types = df_get_registered_post_type_options(false, false , true);

        if(!empty($post_types)) {
            foreach($post_types as $post_type => $value) {
                $this->process_all_pod_groups($post_type);
                $this->process_field_data_for_pod_filter($post_type);
            }
        }
    }

    /**
     * Get all pod groups for a single post type
     * 
     * @param String $post_type
     */
    private function process_all_pod_groups($post_type) {

        $pod = pods($post_type);

        if($pod && is_array($pod->fields())) {
            foreach($pod->fields() as $field_key => $pod_field) {
                if(in_array($pod_field['type'],$this->pod_supported_field_type) && $pod_field['object_storage_type']==="post_type") {
                    $this->pod_fields_storage[$post_type][$pod_field['name']] = $pod_field['label'];
                    $this->pod_fields_type[$pod_field['name']] = $pod_field['type'];
                }
            }
        }
    }

	/* POD Filter Methods */
	private function process_field_data_for_pod_filter($post_type) {
        $pod = pods($post_type);

        
        if($pod && is_array($pod->fields())) {
            foreach($pod->fields() as $pod_field) {
                if ( in_array( $pod_field['type'], $this->pod_filter_supported_field_type ) && $pod_field['object_storage_type']==="post_type" ) {
                    $data_for_pod_filter = [
                        "id"          => $pod_field['ID'],
                        "key"         => $pod_field['key'],
                        "label"       => $pod_field['label'],
                        "name"        => $pod_field['name'],
                        "prefix"      => $pod_field['prefix'],
                        "type"        => $pod_field['type'],
                        "parent"      => $pod_field['parent'],
                        "prepend"     => ! empty( $pod_field['prepend'] ) ? $pod_field['prepend'] : "",
                        "append"      => ! empty( $pod_field['append'] ) ? $pod_field['append'] : "",
                        "placeholder" => ! empty( $pod_field['placeholder'] ) ? $pod_field['placeholder'] : ""
                    ];
                    if ( "select" === $pod_field['type'] ) {
                        $data_for_pod_filter['choices'] = $pod_field['choices'];
                    }
                    if ( "range" === $pod_field['type'] ) {
                        $data_for_pod_filter['min'] = ! empty( $pod_field['min'] ) ? $pod_field['min'] : 0;
                        $data_for_pod_filter['max'] = ! empty( $pod_field['max'] ) ? $pod_field['max'] : 100;
                    }
                    $this->pod_fields_with_details[ $post_type ][] = $data_for_pod_filter;
                }
            }
		}
	}

	public function processed_pod_fields_for_filter_options() {
		$processed_data = [];
		foreach ($this->pod_fields_with_details as $post_type=>$fields){
			foreach ($fields as $field){
				$processed_data[$post_type][$field['name']] = $field['label'];
			}
		}
		return $processed_data;
	}

	/* POD Filter Methods */

}
