<?php

class Postmatic_Social_Comments_Session implements ArrayAccess
{
    private static $COOKIE_SESSION_NAME = "_postmatic_social_sc_session";
    private $data = array();
    private $session_id;
    private $session_expire;
    private static $instance = false;

    public static function get_instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        if ( isset( $_COOKIE[Postmatic_Social_Comments_Session::$COOKIE_SESSION_NAME] ) ) {
            $this->session_id = stripslashes( $_COOKIE[Postmatic_Social_Comments_Session::$COOKIE_SESSION_NAME] );
        } else {
            $this->session_id = $this->generate_session_id();
        }
        $this->session_expire = intval( 24 * 60 );
        $this->load();
        setcookie( Postmatic_Social_Comments_Session::$COOKIE_SESSION_NAME, $this->session_id, time() + $this->session_expire, COOKIEPATH, COOKIE_DOMAIN );
        add_action('shutdown', array( $this, 'save' ) );
    }

    private function load()
    {
        $transient_id = $this->get_transient_id();
        $data = get_transient( $transient_id );
        if ( !$data || !is_array( $data ) ) {
            $data = array();
        }
        $this->data = $data;
        set_transient( $transient_id, $data, $this->session_expire );
    }

    public function save()
    {
        $transient_id = $this->get_transient_id();
        return set_transient( $transient_id, $this->data, $this->session_expire );
    }

    public function invalidate()
    {
        $transient_id = $this->get_transient_id();
        delete_transient( $transient_id );
        $this->data = array();
    }

    private function generate_session_id()
    {
        require_once( ABSPATH . 'wp-includes/class-phpass.php' );
        $hasher = new PasswordHash( 8, false );
        return md5($hasher->get_random_bytes( 32 ) );
    }

    public function regenerate_session_id( $delete_old = false )
    {
        if ( $delete_old ) {
            $old_transient_id = $this->get_transient_id();
            delete_transient( $old_transient_id );
        }
        $this->session_id = $this->generate_session_id();
        setcookie( Postmatic_Social_Comments_Session::$COOKIE_SESSION_NAME, $this->session_id, time() + $this->session_expire, COOKIEPATH, COOKIE_DOMAIN );
    }

    private function get_transient_id()
    {
        return '_pms_' . $this->session_id;
    }

    public function offsetSet( $offset, $value )
    {
        if ( is_null( $offset ) ) {
            $this->data[] = $value;
        } else {
            $this->data[ $offset ] = $value;
        }
    }

    public function offsetExists( $offset )
    {
        return isset($this->data[ $offset ]);
    }

    public function offsetUnset( $offset )
    {
        unset( $this->data[ $offset ] );
    }

    public function offsetGet( $offset )
    {
        return isset( $this->data[ $offset ] ) ? $this->data[ $offset ] : null;
    }
}