<?php

if (!defined('ABSPATH')) die('No direct access allowed');

class WP_Optimize_Database_Information {

	const UNKNOWN_DB = 'unknown';
	const MARIA_DB = 'MariaDB';
	const PERCONA_DB = 'Percona';
	// for some reason coding standard parser give error here WordPress.DB.RestrictedFunctions.mysql_mysql_db
	// @codingStandardsIgnoreLine
	const MYSQL_DB = 'MysqlDB';

	const MYISAM_ENGINE = 'MyISAM';
	const MEMORY_ENGINE = 'Memory';
	const INNODB_ENGINE = 'InnoDB';
	const ARCHIVE_ENGINE = 'ARCHIVE';
	const CSV_ENGINE = 'CSV';
	const NDB_ENGINE = 'NDB';
	const ARIA_ENGINE = 'Aria'; // MariaDB
	const VIEW = 'VIEW';

	/**
	 * Returns server type MySQL or MariaDB if mysql database or Unknown if not mysql.
	 *
	 * @return string
	 */
	public function get_server_type() {
		global $wpdb;
		static $server_type = null;

		if (!$wpdb->is_mysql) return self::UNKNOWN_DB;

		if (null !== $server_type) return $server_type;

		$server_type = self::MYSQL_DB;

		$variables = $wpdb->get_results('SHOW SESSION VARIABLES LIKE "version%"');

		if (!empty($variables)) {
			foreach ($variables as $variable) {
				if (preg_match('/mariadb/i', $variable->Value)) {
					$server_type = self::MARIA_DB;
				}
				if (preg_match('/percona/i', $variable->Value)) {
					$server_type = self::PERCONA_DB;
				}
			}
		}

		return $server_type;
	}

	/**
	 * Returns database server version
	 *
	 * @return string|bool
	 */
	public function get_version() {
		$version = $this->get_option_value('version');

		if (!empty($version)) {
			if (preg_match('/^(\d+)(\.\d+)+/', $version, $match)) {
				return $match[0];
			}
		}

		return false;
	}

	/**
	 * Return table type by $table_name.
	 *
	 * @param String $table_name Database table name.
	 * @return String|Boolean - returns false upon failure
	 */
	public function get_table_type($table_name) {
		$table_info = $this->get_table_status($table_name);

		if ($table_info) {
			if (!$table_info->Engine && $this->is_view($table_name)) return self::VIEW;

			return $table_info->Engine;
		}

		return false;
	}

	/**
	 * Returns information about database table.
	 *
	 * @param string $table_name
	 * @param bool   $update     if true, then force request to database and don't use cached values.
	 * @return bool|mixed
	 */
	public function get_table_status($table_name, $update = false) {
		global $wpdb;

		if (false == $update) {
			$tables_info = $this->get_show_table_status();

			foreach ($tables_info as $table_info) {
				if ($table_name == $table_info->Name) return $table_info;
			}
		} else {
			return $wpdb->get_row($wpdb->prepare('SHOW TABLE STATUS LIKE %s;', $table_name));
		}

		return false;
	}

	/**
	 * Returns result for query SHOW TABLE STATUS.
	 *
	 * @return array
	 */
	public function get_show_table_status() {
		global $wpdb;
		static $tables_info = array();

		if (empty($tables_info) || !is_array($tables_info)) {
			$tables_info = $wpdb->get_results('SHOW TABLE STATUS');
		}

		// If option innodb_file_per_table is disabled then Data_free column will have summary overhead value for all table.
		if (!empty($tables_info)) {
			foreach ($tables_info as $i => $table) {
				if (self::INNODB_ENGINE == $table->Engine && false == $this->is_option_enabled('innodb_file_per_table')) {
					$tables_info[$i]->Data_free = 0;
				}
			}
		}

		return $tables_info;
	}

	/**
	 * Returns result for query SHOW FULL TABLES as associative array [table_name] => table_type.
	 *
	 * @return array
	 */
	public function get_show_full_tables() {
		global $wpdb;

		static $tables_info = array();

		if (empty($tables_info) || !is_array($tables_info)) {
			$_tables_info = $wpdb->get_results('SHOW FULL TABLES', ARRAY_N);

			if (!empty($_tables_info)) {
				foreach ($_tables_info as $row) {
					$tables_info[$row[0]] = $row[1];
				}
			}
		}

		return $tables_info;
	}

	/**
	 * Checks if table is a VIEW.
	 *
	 * @param  string $table_name
	 * @return bool
	 */
	public function is_view($table_name) {
		$tables_info = $this->get_show_full_tables();

		if (!array_key_exists($table_name, $tables_info)) return false;

		return ('VIEW' == $tables_info[$table_name]);
	}

	/**
	 * Returns true if DDL supported.
	 *
	 * @return bool
	 */
	public function has_online_ddl() {
		if (self::MYSQL_DB == $this->get_server_type()) {
			if (version_compare($this->get_version(), '5.7', '>=')) {
				return true;
			} else {
				return false;
			}
		} elseif (self::MARIA_DB == $this->get_server_type()) {
			if (version_compare($this->get_version(), '10.0.0', '>=')) {
				return true;
			} else {
				return false;
			}
		}

		return false;
	}

	/**
	 * Returns database option variable
	 *
	 * @param string $option_name Name of database option.
	 * @return mixed|null
	 */
	public function get_option_value($option_name) {
		global $wpdb;
		static $options = array();

		if (array_key_exists($option_name, $options)) return $options[$option_name];

		$option = $wpdb->get_row(
			$wpdb->prepare('SHOW SESSION VARIABLES LIKE %s', $option_name)
		);

		if (!empty($option)) {
			$options[$option_name] = $option->Value;
			return $option->Value;
		}

		return null;
	}

	/**
	 * Returns true if database option $option_name
	 *
	 * @param string $option_name Name of database option name.
	 * @return bool
	 */
	public function is_option_enabled($option_name) {
		$option_value = $this->get_option_value($option_name);

		if ('ON' == strtoupper($option_value)) return true;
		return false;
	}

	/**
	 * Returns true if table $table_name is optimizable
	 *
	 * @param string $table_name Name of database table
	 * @return bool
	 */
	public function is_table_optimizable($table_name) {
		$server_type = $this->get_server_type();
		$server_version = $this->get_version();
		$table_type = $this->get_table_type($table_name);

		// return true if table is MyISAM.
		if (self::MYISAM_ENGINE == $table_type) return true;

		// return true if table is Archive or Aria.
		if (self::ARCHIVE_ENGINE == $table_type || self::ARIA_ENGINE == $table_type) return true;

		// if InnoDB then check if we can optimize.
		if (self::INNODB_ENGINE == $table_type) {
			// check for MysqlDB.
			if (self::MYSQL_DB == $server_type && $this->has_online_ddl()) {
				return true;
			}

			// check for MariaDB.
			if (self::MARIA_DB == $server_type) {
				// if innodb_file_per_table enabled or version not older than 10.1.1 and innodb_defragment enabled.
				if ($this->is_option_enabled('innodb_file_per_table') || (version_compare($server_version, '10.1.1', '>=') && $this->is_option_enabled('innodb_defragment'))) {
					return true;
				}
			}
		}

		// otherwise return false.
		return false;
	}

	/**
	 * Returns true if table type is supported for optimization.
	 *
	 * @param string $table_name Name of database table
	 * @return bool
	 */
	public function is_table_type_optimize_supported($table_name) {
		$table_type = $this->get_table_type($table_name);

		$supported_table_types = array(
			self::MYISAM_ENGINE,
			self::INNODB_ENGINE,
			self::ARCHIVE_ENGINE,
			self::ARIA_ENGINE,
		);

		return in_array($table_type, $supported_table_types);
	}

	/**
	 * Returns true if table type is supported for repair.
	 *
	 * @param string $table_name
	 * @return bool
	 */
	public function is_table_type_repair_supported($table_name) {
		$table_type = $this->get_table_type($table_name);

		$supported_table_types = array(
			self::MYISAM_ENGINE,
			self::ARCHIVE_ENGINE,
			self::CSV_ENGINE,
		);

		return in_array($table_type, $supported_table_types);
	}

	/**
	 * Run CHECK TABLE query and returns statuses for single or list of tables.
	 *
	 * @param array|string $table
	 */
	public function check_table($table) {
		global $wpdb;

		if (is_array($table)) {
			$table = join('`,`', $table);
		}

		$result = array();

		if (empty($table)) return $result;

		$query_result = $wpdb->get_results('CHECK TABLE `'.$table.'`;');

		if (empty($query_result)) return $result;

		foreach ($query_result as $row) {
			$table_name_parts = explode('.', rtrim($row->Table, ' .'));
			$table_name = array_pop($table_name_parts);

			if (!array_key_exists($table_name, $result)) {
				$result[$table_name] = array(
					'status' => '',
					'corrupted' => false,
				);
			}

			if ('error' == $row->Msg_type) {
				$result[$table_name]['status'] = $row->Msg_type;

				if (preg_match('/corrupt/i', $row->Msg_text)) {
					$result[$table_name]['corrupted'] = true;
				} else {
					$result[$table_name]['message'] = $row->Msg_text;
				}
			}

			if ('status' == $row->Msg_type) {
				$result[$table_name]['status'] = $row->Msg_text;
			}
		}

		return $result;
	}

	/**
	 * Check all supported for repair tables and return statuses for them.
	 *
	 * @return array
	 */
	public function check_all_tables() {
		static $result = null;

		if (null !== $result) return $result;

		$tables = $this->get_show_table_status();
		$supported_tables = array();

		foreach ($tables as $table) {
			if ('' == $table->Engine || $this->is_table_type_repair_supported($table->Name)) {
				$supported_tables[] = $table->Name;
			}
		}

		$result = $this->check_table($supported_tables);

		return $result;
	}

	/**
	 * Returns true if table needing repair.
	 *
	 * @param string $table_name Database table name.
	 */
	public function is_table_needing_repair($table_name) {
		$table_statuses = $this->check_all_tables();

		if (is_array($table_statuses) && array_key_exists($table_name, $table_statuses) && $table_statuses[$table_name]['corrupted']) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Check if $table using by any of installed plugins.
	 *
	 * @param string $table
	 * @return bool
	 */
	public function is_table_using_by_plugin($table) {
		$plugin_names = $this->get_table_plugin($table);

		// if we can't determine which plugin use $table then return true.
		if (false == $plugin_names) {
			return true;
		}

		// is WordPress core table or using by any of installed plugins then return true.
		foreach ($plugin_names as $plugin_name) {
			if (__('WordPress core', 'wp-optimize') == $plugin_name || in_array($plugin_name, $this->get_all_installed_plugins())) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get information about relations between tables and plugins. [ 'table' => ['plugin1', 'plugin2', ...], ... ].
	 *
	 * @return array
	 */
	private function get_all_plugin_tables_relationship() {
		static $plugin_tables;

		$wp_core_tables = array(
			'blogs',
			'blog_versions',
			'commentmeta',
			'comments',
			'links',
			'options',
			'postmeta',
			'posts',
			'registration_log',
			'signups',
			'term_relationships',
			'term_taxonomy',
			'termmeta',
			'terms',
			'usermeta',
			'users',
			'site',
			'sitemeta',
		);

		if (is_array($plugin_tables)) return $plugin_tables;

		$plugin_tables_json_file = WPO_PLUGIN_MAIN_PATH.'/plugin.json';
		$plugin_tables = array();

		if (is_file($plugin_tables_json_file) && is_readable($plugin_tables_json_file)) {
			// get data from plugin.json file.
			$plugin_tables = json_decode(file_get_contents($plugin_tables_json_file), true);
		}

		foreach ($wp_core_tables as $table) {
			$plugin_tables[$table][] = __('WordPress core', 'wp-optimize');
		}

		// add WP-Optimize tables.
		$plugin_tables['tm_taskmeta'][] = 'wp-optimize';
		$plugin_tables['tm_tasks'][] = 'wp-optimize';

		return $plugin_tables;
	}

	/**
	 * Try to get plugin name by table name and return it or return false if plugin is not defined.
	 *
	 * @param string $table
	 * @return array|bool - array with plugin slugs or false.
	 */
	public function get_table_plugin($table) {
		global $wpdb;

		// delete table prefix.
		$table = preg_replace('/^'.$wpdb->prefix.'([0-9]+_)?/', '', $table);
		$plugins_tables = $this->get_all_plugin_tables_relationship();

		if (array_key_exists($table, $plugins_tables)) {
			return $plugins_tables[$table];
		}

		return false;
	}

	/**
	 * Get all installed plugin slugs.
	 *
	 * @return array
	 */
	public function get_all_installed_plugins() {
		static $installed_plugins;

		if (is_array($installed_plugins)) return $installed_plugins;

		$installed_plugins = array();
		
		if (!function_exists('get_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
		
		$plugins = get_plugins();

		foreach ($plugins as $plugin_file => $plugin_data) {
			if ('' != $plugin_data['TextDomain']) {
				$installed_plugins[] = $plugin_data['TextDomain'];
			} else {
				$plugin_file_parts = explode('/', $plugin_file);
				$installed_plugins[]= $plugin_file_parts[0];
			}
		}

		return $installed_plugins;
	}

	/**
	 * Check current plugin status installed/not installed and active/inactive.
	 *
	 * @param string $plugin
	 * @return array - ['installed' => true|false, 'active' => true|false]
	 */
	public function get_plugin_status($plugin) {
	
		if (!function_exists('get_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
		$plugins = get_plugins();

		// return true for wp-optimize without checking.
		if ('wp-optimize' == $plugin) {
			return array(
				'installed' => true,
				'active' => true,
			);
		}

		$installed = false;
		$active = false;

		foreach ($plugins as $plugin_file => $plugin_data) {
			$plugin_file_parts = explode('/', $plugin_file);
			$plugin_slug = $plugin_file_parts[0];

			if ($plugin == $plugin_slug) {
				$installed = true;
				$active = is_plugin_active($plugin_file);
			}
		}

		return array(
			'installed' => $installed,
			'active' => $active,
		);
	}
}
