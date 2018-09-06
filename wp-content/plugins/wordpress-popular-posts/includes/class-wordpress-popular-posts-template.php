<?php
/**
 * WordPress Popular Posts template tags for use in themes.
 */

/**
 * Template tag - gets views count.
 *
 * @since	2.0.3
 * @global	object	wpdb
 * @param	int		id
 * @param	string	range
 * @param	bool	number_format
 * @return	string
 */
function wpp_get_views($id = NULL, $range = NULL, $number_format = true) {

    // have we got an id?
    if ( empty($id) || is_null($id) || !is_numeric($id) ) {
        return "-1";
    } else {
        global $wpdb;

        $table_name = $wpdb->prefix . "popularposts";

        if ( !$range || 'all' == $range ) {
            $query = "SELECT pageviews FROM {$table_name}data WHERE postid = '{$id}'";
        } else {
            $interval = "";

            switch( $range ){
                case "last24hours":
                case "daily":
                    $interval = "24 HOUR";
                break;

                case "last7days":
                case "weekly":
                    $interval = "6 DAY";
                break;

                case "last30days":
                case "monthly":
                    $interval = "29 DAY";
                break;

                default:
                    $interval = "24 HOUR";
                break;
            }

            $now = current_time('mysql');

            $query = "SELECT SUM(pageviews) FROM {$table_name}summary WHERE postid = '{$id}' AND view_datetime > DATE_SUB('{$now}', INTERVAL {$interval}) LIMIT 1;";
        }

        $result = $wpdb->get_var($query);

        if ( !$result ) {
            return "0";
        }

        return ($number_format) ? number_format_i18n( intval($result) ) : $result;
    }

}

/**
 * Template tag - gets popular posts.
 *
 * @since	2.0.3
 * @param	mixed	args
 */
function wpp_get_mostpopular($args = NULL) {

    $shortcode = '[wpp';

    if ( is_null( $args ) ) {
        $shortcode .= ']';
    } else {
        if( is_array( $args ) ){
            $atts = '';
            foreach( $args as $key => $arg ){
                $atts .= ' ' . $key . '="' . htmlspecialchars($arg, ENT_QUOTES, $encoding = ini_get("default_charset"), false) . '"';
            }
        } else {
            $atts = trim( str_replace( "&", " ", $args  ) );
        }

        $shortcode .= ' ' . $atts . ' php=true]';
    }

    echo do_shortcode( $shortcode );

}

/**
 * Template tag - gets popular posts. Deprecated in 2.0.3, use wpp_get_mostpopular instead.
 *
 * @since	1.0
 * @param	mixed	args
 */
function get_mostpopular($args = NULL) {
    trigger_error( 'The get_mostpopular() template tag has been deprecated since 2.0.3. Please use wpp_get_mostpopular() instead.', E_USER_WARNING );
}
