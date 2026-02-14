<?php
namespace AFL\Framework\Core;

/**
 * Configuration Class
 *
 * Manages configuration data loaded from files using key-value storage.
 *
 * @since 0.0.1
 */
class Config {

	/**
	 * Configuration data array
	 *
	 * @var array
	 */
	private $config = array();

	/**
	 * Initialize the configuration object
	 */
	public function __construct() {
	}

	/**
	 * Load configuration from a PHP file
	 *
	 * @param string $file_path Path to the PHP configuration file.
	 * @return void
	 */
	public function load_from_file( $file_path ) {

		if ( file_exists( $file_path ) ) {
			$this->config = include $file_path;
		}
	}

	/**
	 * Get a configuration value by key
	 *
	 * @param string $key The configuration key.
	 * @return mixed The configuration value or null if not found
	 */
	public function get( $key ) {

		if ( isset( $this->config[ $key ] ) ) {
			return $this->config[ $key ];
		} else {
			return null;
		}
	}

	/**
	 * Get all configuration data
	 *
	 * @return array All configuration data
	 */
	public function get_all() {
		return $this->config;
	}

	/**
	 * Set a configuration value
	 *
	 * @param string $key The configuration key.
	 * @param mixed  $value The configuration value.
	 * @return void
	 */
	public function set( $key, $value ) {

		$this->config[ $key ] = $value;
	}
}
