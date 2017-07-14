<?php

require_once('Postmatic_Social_Network_Authenticator.php');

class Postmatic_Social_Facebook_Authenticator extends Postmatic_Social_Network_Authenticator
{
    public $network = "facebook";

    private static $ENABLED = 'pms_enabled';
    private static $API_URL = 'pms_api_url';
    private static $CLIENT_ID = 'pms_client_id';
    private static $CLIENT_SECRET = 'pms_client_secret';

    public function __construct()
    {
        parent::__construct();
    }

    function get_default_settings()
    {
        return array("id" => "facebook",
            "title" => '<i class="fa fa-facebook"></i> ' . esc_html__( 'Facebook', 'postmatic-social' ),
            "fields" => array(
                Postmatic_Social_Facebook_Authenticator::$API_URL => array(
                    'title' => __( 'API URL (you probably will not need to change this)', 'postmatic-social' ),
                    'type' => 'text',
                    'default_value' => 'https://www.facebook.com/dialog/oauth'
                ),
                Postmatic_Social_Facebook_Authenticator::$CLIENT_ID => array(
                    'title' => __( 'App ID', 'postmatic-social' ),
                    'type' => 'text',
                    'default_value' => ''
                ),
                Postmatic_Social_Facebook_Authenticator::$CLIENT_SECRET => array(
                    'title' => __( 'App Secret', 'postmatic-social' ),
                    'type' => 'text',
                    'default_value' => ''
                ),
               Postmatic_Social_Facebook_Authenticator::$ENABLED => array(
                    'title' => __( 'Status', 'postmatic-social' ),
                    'type' => 'switch',
                    'default_value' => 'off',
                    'possible_values' => array(
                        'on' => __( 'Enabled', 'postmatic-social' ),
                        'off' => __( 'Disabled', 'postmatic-social' )
                    )
                )
            )
        );
    }

    function render_settings_admin_page()
     
    {
       $default_settings = $this->get_default_settings();
        $sc_id = $default_settings[ 'id' ];
        $settings = $this->get_settings();
        echo '<table class="form-table"><tbody>';

        echo '<tr>';
        echo '<th><label>' . esc_html__('Need help?', 'postmatic-social') . '</label></th>';
        echo '<td><a href="' . esc_url( POSTMATIC_SOCIAL_HELP_URL . '#' . $sc_id . '-config' ) . '" target="_blank">Videos and walkthroughs for configuring your Facebook app are available here'. '</a>.</td>';
        echo '</tr>';

        $oauth_callback = $this->get_oauth_callback();
        echo '<tr>';
        echo '<th><label>' . esc_html__('Redirection URL', 'postmatic-social') . '</label></th>';
        echo '<td><strong>' . esc_html( $oauth_callback ) . '</strong></td>';
        echo '</tr>';

        foreach ($default_settings[ "fields" ] as $field_id => $field_meta ) {
            $field_value = $settings[ $field_id ];
            $this->render_form_field( $field_id, $field_value, $field_meta );
        }

        echo '</tbody></table>';
    }

    protected function process_token_request()
    {
        $settings = $this->get_settings();
        $api_url = $settings[ Postmatic_Social_Facebook_Authenticator::$API_URL ];
        $client_id = $settings[ Postmatic_Social_Facebook_Authenticator::$CLIENT_ID ];

        $query_string = $this->to_query_string(array(
            'response_type' => 'code',
            'client_id' => $client_id,
            'redirect_uri' => $this->get_oauth_callback(),
            'scope' => 'user_about_me,email',
        ));
        $authorize_url = $api_url . '?' . $query_string;
        header('Location: ' . esc_url_raw( $authorize_url ) );
    }

    protected function process_access_token_request()
    {
        if (array_key_exists( 'code', $_REQUEST ) && array_key_exists( 'post_id', $_REQUEST ) ) {
            global $pms_post_protected;
            global $pms_session;
            $post_id = intval( $_REQUEST['post_id'] );
            $settings = $this->get_settings();
            $client_id = $settings[ Postmatic_Social_Facebook_Authenticator::$CLIENT_ID ];
            $client_secret = $settings[ Postmatic_Social_Facebook_Authenticator::$CLIENT_SECRET ];
            $request_token_url = "https://graph.facebook.com/v2.4/oauth/access_token";

            $query_string = $this->to_query_string( array(
                'client_id' => $client_id,
                'redirect_uri' => $this->get_oauth_callback(),
                'client_secret' => $client_secret,
                'code' => $_REQUEST[ 'code' ] ,
                // 'grant_type' => 'authorization_code'
            ));
            $response = wp_remote_get( esc_url_raw( $request_token_url . "?" . $query_string ) );
            if (is_wp_error($response)) {
                $error_string = $response->get_error_message();
                throw new Exception( $error_string );
            } else {
                $response_body = json_decode( $response['body'], true );
                if ( $response_body && is_array( $response_body ) && array_key_exists( 'access_token', $response_body ) ) {
                    $access_token = $response_body[ 'access_token' ];
                    $user_details = $this->get_user_details( $access_token );
                    $pms_session[ 'user' ] = $user_details;
                    $pms_post_protected = true;
                    $user_details['disconnect_content'] = $GLOBALS['postmatic-social']->disconnect_content(
                        $user_details,
                        $post_id
                    );
                    wp_send_json( $user_details );
                    die();
                } else {
                    throw new Exception( __('Missing the access_token parameter', 'postmatic-social' ) );
                }
            }
        } else {
            die();
        }
    }


    protected function get_user_details($access_token)
    {
        $settings = $this->get_settings();
        $user_details_url = "https://graph.facebook.com/me?fields=id,name,email,picture" ;
        $response = wp_remote_get( esc_url_raw( $user_details_url ),
            array('timeout' => 120,
                'headers' => array( 'Authorization' => 'Bearer ' . $access_token ),
                'sslverify' => false));
        if ( is_wp_error( $response ) ) {
            $error_string = $response->get_error_message();
            throw new Exception( $error_string );
        } else {
            $response_body = json_decode( $response[ 'body' ], true );
            
            if ( $response_body && is_array( $response_body ) ) {
                return array(
                    'network' => "Facebook",
                    'display_name' => isset( $response_body[ 'name' ] ) ? $response_body[ 'name' ] : '',
                    'username' => isset( $response_body[ 'id' ] ) ? $response_body[ 'id' ] : '',
                    'email' => isset( $response_body[ 'email' ] ) ? $response_body[ 'email' ] : '',
                    'avatar_url' => isset( $response_body[ 'picture' ][ 'url' ] ) ? $response_body[ 'picture' ][ 'url' ] : '',
                );
            } else {
                throw new Exception(__( 'Could not get the user details', 'postmatic-social' ) );
            }
        }
    }

    function get_auth_button($settings = array())
    {
        $default_settings = $this->get_default_settings();
        $website_url = admin_url( 'admin-ajax.php' ) . '?action=pms-facebook-request-token';
        $btn = '<a class="postmatic-sc-button postmatic-sc-facebook-button fa fa-facebook" title="Comment via Facebook" data-sc-id="' . esc_attr( $default_settings['id'] ) . '" data-post-id="' . esc_attr( get_the_ID() ) . '" name="Facebook" href="' . esc_url( $website_url ) . '"></a>';
        return $btn;
    }
}
