<?php

require_once('Postmatic_Social_Comments_Tab.php');

abstract class Postmatic_Social_Network_Authenticator extends Postmatic_Social_Comments_Tab
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function init()
    {
        $sn_id = $this->get_id();
        if (!is_user_logged_in()) {
            add_action( 'wp_ajax_nopriv_pms-' . $sn_id . '-request-token', array( $this, 'request_token' ) );
            add_action( 'wp_ajax_nopriv_pms-' . $sn_id . '-access-token', array( $this, 'access_token' ) );
            add_action( 'wp_ajax_nopriv_pms-logout', array($this, 'logout' ) );
            add_action( 'wp_footer', array($this, 'custom_footer' ) );
        }
    }

    function logout()
    {
        $referrer = wp_get_referer();
        if ($referrer) {
            global $pms_session;
            $pms_session->invalidate();
            header('Location: ' . esc_url_raw( $referrer ) );
        }
    }

    protected function get_oauth_callback()
    {
        $sn_id = $this->get_id();
        return admin_url('admin-ajax.php?action=pms-' . $sn_id . '-access-token');
    }

    function request_token()
    {
        try {
            $this->process_token_request();
        } catch ( Exception $ex ) {
            die( $ex->getMessage() );
        }
    }

    function access_token()
    {
        try {
            $this->process_access_token_request();
        } catch ( Exception $ex ) {
            die( $ex->getMessage() );
        }
    }

    abstract protected function process_token_request();

    abstract protected function process_access_token_request();

    abstract function get_auth_button($settings = array());

    function custom_footer()
    {

    }

    protected function starts_with( $haystack, $needle )
    {
        $length = strlen( $needle );
        return ( substr( $haystack, 0, $length ) === $needle );
    }

    protected function ends_with( $haystack, $needle )
    {
        $length = strlen( $needle );
        if ($length == 0) {
            return true;
        }

        return ( substr( $haystack, -$length ) === $needle );
    }

    protected function to_query_string( $params )
    {
        $url_params = array();
        foreach ( $params as $key => $value ) {
            $url_params[] = $key . '=' . rawurlencode($value);
        }
        return implode( '&', $url_params );
    }

} 