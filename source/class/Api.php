<?php

namespace EasyPlugin;

use WP_REST_Request;
use WP_User;

class Api
{
    /**
     * @var string
     */
    protected $baseURI;

    public function __construct()
    {
        $this->baseURI = dirname($_SERVER['SCRIPT_NAME']);
        add_action('rest_api_init', [$this, 'initialize']);

    }

    public function initialize()
    {

    }
}


