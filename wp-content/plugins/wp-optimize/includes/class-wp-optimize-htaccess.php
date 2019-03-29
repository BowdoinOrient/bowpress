<?php

if (!defined('WPO_VERSION')) die('No direct access allowed');

class WP_Optimize_Htaccess {

	/**
	 * Full path to .htaccess file.
	 *
	 * @var string
	 */
	private $_htaccess_file = '';

	/**
	 * Structured content of .htaccess file.
	 *
	 * @var array
	 */
	private	$_file_tree = array();

	/**
	 * WP_Optimize_Htaccess constructor.
	 *
	 * @param string $htaccess_file Full path to .htaccess file.
	 */
	public function __construct($htaccess_file = '') {
		$this->_htaccess_file = ('' != $htaccess_file) ? $htaccess_file : get_home_path() . '.htaccess';
		// read .htaccess content into $_file_tree.
		$this->read_file();
	}

	/**
	 * Returns .htaccess filename.
	 *
	 * @return string
	 */
	public function get_filename() {
		return $this->_htaccess_file;
	}

	/**
	 * Checks if .htaccess file exists.
	 *
	 * @return bool
	 */
	public function is_exists() {
		return is_file($this->_htaccess_file);
	}

	/**
	 * Checks if .htaccess file is readaable.
	 *
	 * @return bool
	 */
	public function is_readable() {
		return is_readable($this->_htaccess_file);
	}

	/**
	 * Checks if .htaccess file is writable.
	 *
	 * @return bool
	 */
	public function is_writable() {
		return is_writable($this->_htaccess_file);
	}

	/**
	 * Read content of .htaccess file and store it as a tree in $_file_tree variable.
	 * For ex.
	 *	[0] => '# BEGIN WordPress',
	 *	[1] => [
	 * 		[0] => '<IfModule mod_rewrite.c>',
	 *		[1] => 'RewriteEngine On',
	 *		[2] => 'RewriteBase /',
	 *		[3] => 'RewriteRule ^index\.php$ - [L]',
	 *		[4] => 'RewriteCond %{REQUEST_FILENAME} !-f',
	 *		[5] => 'RewriteCond %{REQUEST_FILENAME} !-d',
	 *		[6] => 'RewriteRule . /index.php [L]',
	 *		[7] => '</IfModule>',
	 *	[2] => '# END WordPress'
	 *
	 * @return void
	 */
	public function read_file() {
		if (false == $this->is_exists() || false == $this->is_readable()) return;

		$content = file_get_contents($this->_htaccess_file);

		$content = explode("\n", $content);

		$content_tree = array();

		$section = array();
		$sections = array();

		foreach ($content as $line) {
			$line = trim($line);

			if (preg_match("/^\<\/(.+)(\s.*)?\>/", $line, $matches)) {
				$section[] = $line;

				// close section
				if (!empty($sections)) {
					$_section = $section;
					$section = array_pop($sections);
					$section[] = $_section;
				} else {
					$content_tree[] = $section;
					$section = array();
				}
			} elseif (preg_match('/^\<(.+)>/', $line, $matches)) {
				// open section
				if (!empty($section)) {
					$sections[] = $section;
				}

				$section = array();
				$section[] = $line;
			} elseif (!empty($section)) {
				$section[] = $line;
			} else {
				$content_tree[] = $line;
			}
		}

		$this->_file_tree = $content_tree;
	}

	/**
	 * Write current $_file_tree content into .htaccess file.
	 */
	public function write_file() {
		$content = implode(PHP_EOL, $this->get_flat_array($this->_file_tree));
		file_put_contents($this->_htaccess_file, $content);
	}

	/**
	 * Recursive function used to prepare data for output - build flat array from $_file_tree.
	 *
	 * @param array  $array
	 * @param string $prefix
	 *
	 * @return array
	 */
	public function get_flat_array($array, $prefix = '') {
		$flat_array = array();

		if (!empty($array)) {
			foreach ($array as $item) {
				if (is_array($item)) {
					$item = $this->get_flat_array($item, "\t");
					$flat_array = array_merge($flat_array, $item);
				} else {
					$flat_array[] = $item;
				}
			}
		}

		reset($flat_array);
		$first = key($flat_array);
		end($flat_array);
		$last = key($flat_array);

		foreach ($flat_array as $key => $value) {
			if ('' != $value && '#' == $value[0]) {
				// never add prefix for comment lines.
				$flat_array[$key] = $value;
			} else {
				$flat_array[$key] = ($key == $first || $key == $last) ? $value : $prefix . $value;
			}
		}

		return $flat_array;
	}

	/**
	 * Update commented section in array $_file_tree, i.e. section wrapped with comments
	 * # BEGIN WP-Optimize Browser Cache
	 * ...
	 * # END WP-Optimize Browser Cache
	 *
	 * @param array  $content
	 * @param string $section
	 */
	public function update_commented_section($content, $section = 'WP-Optimize Browser Cache') {
		$section_begin = $this->get_section_begin_comment($section);
		$section_end = $this->get_section_end_comment($section);

		// add begin-end section comments.
		array_unshift($content, $section_begin);
		array_push($content, $section_end);

		$section_index = $this->search_commented_section($section);

		// check if section with cache settings already in the file.
		if (false === $section_index) {
			// no section in file then add it to the end of file.
			$this->_file_tree = array_merge($this->_file_tree, $content);
		} else {
			$remove_length = (false === $section_index['end']) ? null : ($section_index['end'] - $section_index['begin'] + 1);
			array_splice($this->_file_tree, $section_index['begin'], $remove_length, $content);
		}
	}

	/**
	 * Removes commented section in $_file_tree, i.e. section wrapped with comments
	 * # BEGIN WP-Optimize Browser Cache
	 *   ...
	 * # END WP-Optimize Browser Cache
	 *
	 * @param string $comment
	 *
	 * @return bool
	 */
	public function remove_commented_section($comment = 'WP-Optimize Browser Cache') {
		$section_index = $this->search_commented_section($comment);

		if (false === $section_index) return false;

		$remove_length = (false === $section_index['end']) ? null : ($section_index['end'] - $section_index['begin'] + 1);
		array_splice($this->_file_tree, $section_index['begin'], $remove_length);

		$this->_file_tree = array_values($this->_file_tree);

		return true;
	}

	/**
	 * Check if section exists wrapped by comments like
	 *
	 * # BEGIN WP-Optimize Browser Cache
	 * ...
	 * # END WP-Optimize Browser Cache
	 *
	 * @param string $section
	 * @return bool
	 */
	public function is_commented_section_exists($section = 'WP-Optimize Browser Cache') {
		$search = $this->search_commented_section($section);

		return (false === $search) ? false : true;
	}

	/**
	 * Search section in $_file_tree array wrapped by begin and end comments.
	 *
	 * @param string $section
	 * @return array|bool
	 */
	private function search_commented_section($section) {
		$section_begin = $this->get_section_begin_comment($section);
		$section_end = $this->get_section_end_comment($section);

		$section_begin_index = $section_end_index = false;

		$section_begin_normalized = $this->normalize_string($section_begin);
		$section_end_normalized = $this->normalize_string($section_end);

		foreach ($this->_file_tree as $i => $value) {
			// if it is subsection then we don't go in deep.
			if (is_array($value)) continue;

			$value = $this->normalize_string($value);

			if ($value == $section_begin_normalized) $section_begin_index = $i;
			if ($value == $section_end_normalized) $section_end_index = $i;
		}

		if (false == $section_begin_index) {
			return false;
		} else {
			return array(
				'begin' => $section_begin_index,
				'end' => $section_end_index,
			);
		}
	}

	/**
	 * Generate begin cache section comment.
	 *
	 * @param string $section
	 * @return string
	 */
	public function get_section_begin_comment($section = 'WP-Optimize Browser Cache') {
		return '# BEGIN ' . $section;
	}

	/**
	 * Generate end cache section comment.
	 *
	 * @param string $section
	 * @return string
	 */
	public function get_section_end_comment($section = 'WP-Optimize Browser Cache') {
		return '# END ' . $section;
	}

	/**
	 * Normalize string - make all letters lowercase and remove spaces.
	 *
	 * @param string $string
	 * @return string
	 */
	private function normalize_string($string) {
		return strtolower(str_replace(array("\n", "\r", ' '), '', $string));
	}
}
