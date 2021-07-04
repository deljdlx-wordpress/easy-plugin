<?php
namespace EasyPlugin;

class Plugin
{

    protected $bootstrapFile;
    protected $namespace;

    protected $api;


    protected $customPostTypes = [];
    protected $customTaxonomies = [];

    public function __construct($bootstrapFile = null)
    {

        if(!$bootstrapFile) {
            $bootstrapFile = debug_backtrace()[0]['file'];
        }

        $this->bootstrapFile = $bootstrapFile;

        register_activation_hook(
            $this->bootstrapFile,
            [$this, 'activate']
         );

        register_deactivation_hook(
            $this->bootstrapFile,
            [$this, 'deactivate']
        );

        add_action(
            'init',
            [$this, 'initialize']
        );

        add_action(
            'admin_menu',
            [$this, 'disableAdminItem']
        );

        add_action(
            'acf/init',
            [$this, 'createCustomFields']
        );

        $reflector = new \ReflectionClass($this);
        $this->namespace = $reflector->getNamespaceName();
        $apiClassName = $this->namespace. '\Api';

        if(class_exists($apiClassName)) {
            $this->api = new Api($this);
        }
    }

    public function initialize()
    {

    }

    public function activate()
    {

    }

    public function deactivate()
    {

    }

    public function createCustomFields()
    {

    }

    public function disableAdminItem($item)
    {
        remove_menu_page('index.php');
        remove_menu_page('edit.php?post_type=page');
    }

    //===========================================================
    //
    //===========================================================

    public function createFieldGroupToType($customPostType, $name, $caption, $fields = [])
    {

        
        $defaultFieldOptions = [
            'key' => 'must-be-set',
            'label' => 'Must be set at createFieldGroupToType call ',
            'name' => 'must-be-set',
            'type' => 'text',
            'prefix' => '',
            'instructions' => '',
            'required' => 0,
            'conditional_logic' => 0,
            'wrapper' => array (
                'width' => '',
                'class' => '',
                'id' => '',
            ),
            'default_value' => '',
            'placeholder' => '',
            'prepend' => '',
            'append' => '',
            'maxlength' => '',
            'readonly' => 0,
            'disabled' => 0,
        ];

        foreach($fields as $key => &$field) {
            $field['key'] = $key;
            $field['name'] = $key;
            $field = array_merge($defaultFieldOptions, $field);
        }


        acf_add_local_field_group(array (
            'key' => $name,              //
            'title' => $caption, //
            'fields' => $fields,
            'location' => array (
                array (
                    array (
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => $customPostType,
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => '',
        ));
         
    }


       

    public function createRole($name, $caption)
    {
        return add_role(
            $name,
            $caption
        );
    }

    public function removeRole($name)
    {
        remove_role($name);
    }

    public function createTerm($name, $taxonomy)
    {
        return wp_insert_term($name, $taxonomy);
    }


    public function createPostType($name, $caption, $support = null)
    {
        if(!$support) {
            $support = [
                'title',
                'thumbnail',
                'editor',
                'author',
                'excerpt',
                'comments',
            ];
        }

        register_post_type(
            $name,
            [
                'label' => $caption,
                'public' => true,
                'hierarchical' => false,
                'supports' => $support,
                'map_meta_cap' => true,
                'show_in_rest' => true,
                // 'capability_type' => $name,
                // 'menu_icon' => 'dashicons-food',
            ]
        );
    }

    public function createTaxonomy($taxonomy, $name, $caption, $hierachical = false)
    {
        register_taxonomy(
            $name,
            (array) $taxonomy,
            [
                'label' => $caption,
                'hierarchical' => $hierachical,
                'public' => true,
                'show_in_rest' => true
            ]
        );
    }



}

