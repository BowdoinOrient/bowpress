<?php

require_once('Postmatic_Social_Comments_Tab.php');

class Postmatic_Social_Generic_Settings_Tab extends Postmatic_Social_Comments_Tab
{
    static $ID = 'settings';
    static $PLUGIN_STATUS = 'pms_plugin_status';
    static $POSTS_ID = 'pms_posts_id';

    public function __construct()
    {
        parent::__construct();
    }

    function get_default_settings()
    {
        return array("id" => Postmatic_Social_Generic_Settings_Tab::$ID,
            "title" => '<i class="fa fa-home"></i> ' . esc_html__("Introduction", 'postmatic-social'),
            "fields" => array()
        );
    }

    function render_settings_admin_page()
    {

        include_once( Postmatic_Social::get_plugin_dir( '/templates/settings-intro.php' ) );
        
        $default_settings = $this->get_default_settings();
        $settings = $this->get_settings();

        echo '<table class="form-table"><tbody>';

        echo '</tbody></table>';
    }

}