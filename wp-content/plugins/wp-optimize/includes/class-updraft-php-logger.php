<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Updraft_PHP_Logger')) return ;

/**
 * Class Updraft_PHP_Logger
 */
class Updraft_PHP_Logger extends Updraft_Abstract_Logger {

    /**
     * Updraft_PHP_Logger constructor
     */
    public function __construct() { }

    /**
     * Returns logger description
     * @return string|void
     */
    public function get_description() {
        return __('Log events into PHP error log', 'wp-optimize');
    }

    /**
     * Emergency message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function emergency($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::EMERGENCY, $message, $context);
    }

    /**
     * Alert message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function alert($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::ALERT, $message, $context);
    }

    /**
     * Critical message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function critical($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::CRITICAL, $message, $context);
    }

    /**
     * Error message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function error($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::ERROR, $message, $context);
    }

    /**
     * Warning message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function warning($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::WARNING, $message, $context);
    }

    /**
     * Notice message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function notice($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::NOTICE, $message, $context);
    }

    /**
     * Info message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function info($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::INFO, $message, $context);
    }

    /**
     * Debug message
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function debug($message, array $context = array()) {
        $this->log(Updraft_Log_Levels::DEBUG, $message, $context);
    }

    /**
     * Log message with any level
     * @param mixed $level
     * @param string $message
     * @param array $context
     * @return null|void
     */
    public function log($level, $message, array $context = array()) {

        if (!$this->is_enabled()) return false;

        $message = '['.Updraft_Log_Levels::toText($level).'] : '.$this->interpolate($message, $context);
        error_log($message);
    }
}