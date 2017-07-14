<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Updraft_Abstract_Logger')) return ;

/**
 * Class Updraft_Abstract_Logger
 */
abstract class Updraft_Abstract_Logger implements Updraft_Logger_Interface {

    protected $enabled = true;

    /**
     * Updraft_Abstract_Logger constructor
     */
    public function __construct() {}

    /**
     * Returns true if logger is active
     * @return bool
     */
    public function is_enabled() {
        return $this->enabled;
    }

    /**
     * Enable logger
     */
    public function enable() {
        $this->enabled = true;
    }

    /**
     * Disable logger
     */
    public function disable() {
        $this->enabled = false;
    }

    /**
     * Returns logger description
     * @return mixed
     */
    abstract function get_description();

    /**
     * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
     * @param $message
     * @param array $context
     * @return string
     */
    protected function interpolate($message, array $context = array()) {
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be casted to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }
        return strtr($message, $replace);
    }

}