<?php

class Postmatic_Social_Pinterest_Authenticator extends Postmatic_Social_Network_Authenticator
{

    private static $ENABLED = 'pms_enabled';
    private static $API_URL = 'pms_api_url';

    public function __construct()
    {
        parent::__construct();
    }

    function get_default_settings()
    {
        return array("id" => "pinterest",
            "title" => '<i class="fa fa-pinterest"></i> ', esc_html__( 'Pinterest', 'postmatic-social' ),
            "fields" => array(
                Postmatic_Social_Pinterest_Authenticator::$ENABLED => array(
                    'title' => __( 'Status', 'postmatic-social' ),
                    'type' => 'select',
                    'default_value' => 'off',
                    'possible_values' => array(
                        'on' => __( 'Enabled', 'postmatic-social' ),
                        'off' => __( 'Disabled', 'postmatic-social' )
                    )
                ),
                Postmatic_Social_Pinterest_Authenticator::$API_URL => array(
                    'title' => __( 'API URL', 'postmatic-social' ),
                    'type' => 'text',
                    'default_value' => 'https://pinterest.com/'
                ),
            )
        );
    }

    function render_settings_admin_page()
    {
        // TODO: Implement render_settings_admin_page() method.
    }

    protected function process_token_request()
    {
        // TODO: Implement process_token_request() method.
    }

    protected function process_access_token_request()
    {
        // TODO: Implement process_access_token_request() method.
    }

    function get_auth_button($settings = array())
    {
        return '';
    }
}