<?php

require_once('Postmatic_Social_Network_Authenticator.php');

class Postmatic_Social_Wordpress_Authenticator extends Postmatic_Social_Network_Authenticator
{
    public $network = "wordpress";

    private static $ENABLED = 'pms_enabled';
    private static $API_URL = 'pms_api_url';
    private static $CLIENT_ID = 'pms_client_id';
    private static $CLIENT_SECRET = 'pms_client_secret';

    public function __construct()
    {
        parent::__construct();
    }

    protected function process_token_request()
    {
        $settings = $this->get_settings();
        $api_url = $settings[ Postmatic_Social_Wordpress_Authenticator::$API_URL ];
        $client_id = $settings[ Postmatic_Social_Wordpress_Authenticator::$CLIENT_ID ];

        $query_string = $this->to_query_string(array(
            'client_id' => $client_id,
            'redirect_uri' => $this->get_oauth_callback(),
            'response_type' => 'code',
            'scope' => 'auth',
        ));
        $authorize_url = $api_url . 'oauth2/authorize?' . $query_string;
        header('Location: ' . esc_url_raw( $authorize_url ) );
    }

    protected function process_access_token_request()
    {
        if (array_key_exists( 'code', $_REQUEST ) && array_key_exists( 'post_id', $_REQUEST ) ) {
            global $pms_post_protected;
            global $pms_session;
            $post_id = intval($_REQUEST['post_id']);
            $settings = $this->get_settings();
            $api_url = $settings[ Postmatic_Social_Wordpress_Authenticator::$API_URL ];
            $client_id = $settings[ Postmatic_Social_Wordpress_Authenticator::$CLIENT_ID ];
            $client_secret = $settings[ Postmatic_Social_Wordpress_Authenticator::$CLIENT_SECRET ];
            $request_token_url = $api_url . 'oauth2/token';

            $query_string = $this->to_query_string(array(
                'client_id' => $client_id,
                'redirect_uri' => $this->get_oauth_callback(),
                'client_secret' => $client_secret,
                'code' => $_REQUEST[ 'code' ],
                'grant_type' => 'authorization_code'
            ));
            $response = wp_remote_post( esc_url_raw( $request_token_url ), array(
                'body' => $query_string,
                'sslverify' => false));
            if ( is_wp_error( $response ) ) {
                $error_string = $response->get_error_message();
                throw new Exception( $error_string );
            } else {
                $response_body = json_decode( $response[ 'body' ], true );
                if ( $response_body && is_array( $response_body ) && array_key_exists( 'access_token', $response_body )
                ) {
                    $access_token = $response_body[ 'access_token' ];
                    $user_details = $this->get_user_details( $access_token );
                    $pms_session['user'] = $user_details;
                    $pms_post_protected = true;
                    $user_details['disconnect_content'] = $GLOBALS['postmatic-social']->disconnect_content(
                        $user_details,
                        $post_id
                    );
                    wp_send_json( $user_details );
                    die();
                } else {
                    throw new Exception(__( 'Missing the access_token parameter', 'postmatic-social' ) );
                }
            }
        } else {
            die();
        }
    }

    protected function get_user_details($access_token)
    {
        $settings = $this->get_settings();
        $api_url = $settings[ Postmatic_Social_Wordpress_Authenticator::$API_URL ];
        $user_details_url = $api_url . 'rest/v1/me/';
        $response = wp_remote_get( esc_url_raw( $user_details_url ),
            array('timeout' => 120,
                'headers' => array('Authorization' => 'Bearer ' . $access_token),
                'sslverify' => false ) );
        if ( is_wp_error( $response ) ) {
            $error_string = $response->get_error_message();
            throw new Exception( $error_string );
        } else {
            $response_body = json_decode( $response[ 'body' ], true );
            if ( $response_body && is_array( $response_body ) ) {
                return array(
                    'network' => "WordPress",
                    'display_name' => $response_body[ 'display_name' ],
                    'username' => $response_body[ 'username' ],
                    'email' => $response_body[ 'email' ],
                    'avatar_url' => $response_body[ 'avatar_URL' ],
                    'profile_url' => $response_body[ 'profile_URL' ]
                );
            } else {
                throw new Exception( __( 'Could not get the user details', 'postmatic-social' ) );
            }
        }
    }


    function get_default_settings()
    {
        return array("id" => "wordpress",
            "title" => '<i class="fa fa-wordpress"></i> ' . esc_html__( 'WordPress', 'postmatic-social' ),
            "fields" => array(
                Postmatic_Social_Wordpress_Authenticator::$API_URL => array(
                    'title' => __('API URL (you probably will not need to change this)', 'postmatic-social'),
                    'type' => 'text',
                    'default_value' => 'https://public-api.wordpress.com/'
                ),
                Postmatic_Social_Wordpress_Authenticator::$CLIENT_ID => array(
                    'title' => __('Client ID', 'postmatic-social'),
                    'type' => 'text',
                    'default_value' => ''
                ),
                Postmatic_Social_Wordpress_Authenticator::$CLIENT_SECRET => array(
                    'title' => __('Client Secret', 'postmatic-social'),
                    'type' => 'text',
                    'default_value' => ''
                ),
              Postmatic_Social_Wordpress_Authenticator::$ENABLED => array(
                    'title' => __('Status', 'postmatic-social'),
                    'type' => 'switch',
                    'default_value' => 'off'
                ),
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
        echo '<td><a href="' . esc_url( POSTMATIC_SOCIAL_HELP_URL . '#' . $sc_id . '-config' ) . '" target="_blank">Videos and walkthroughs for configuring your WordPress app are available here'. '</a>.</td>';
        echo '</tr>';

        $oauth_callback = $this->get_oauth_callback();
        echo '<tr>';
        echo '<th><label>' . esc_html__('Redirection URL', 'postmatic-social') . '</label></th>';
        echo '<td><strong>' . esc_html( $oauth_callback ) . '</strong></td>';
        echo '</tr>';

        foreach ($default_settings[ "fields" ] as $field_id => $field_meta) {
            $field_value = $settings[ $field_id ];
            $this->render_form_field( $field_id, $field_value, $field_meta );
        }

        echo '</tbody></table>';
    }

    function get_auth_button($settings = array())
    {
        $default_settings = $this->get_default_settings();
        $website_url = admin_url('admin-ajax.php') . '?action=pms-wordpress-request-token';
        $btn = '<a class="postmatic-sc-button postmatic-sc-wordpress-button fa fa-wordpress" title="Comment via WordPress.com" data-sc-id="' . esc_attr( $default_settings['id'] ) . '" data-post-id="' . esc_attr( get_the_ID() ) . '" name="WordPress" href="' . esc_url( $website_url ) . '"></a>';
        return $btn;
    }

}