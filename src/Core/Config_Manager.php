<?php
namespace AFL\Framework\Core;

/**
 * Configuration Manager
 *
 * Manages multiple configuration files and provides centralized access to application settings.
 *
 * @since 0.0.1
 */
class Config_Manager {

	/**
	 * List of loaded configuration objects
	 *
	 * @var array
	 */
	protected $list = array();

	/**
	 * Primary configuration folder path
	 *
	 * @var string
	 */
	private $primary_folder_path;

	/**
	 * Initialize the configuration manager
	 */
	public function __construct() {
	}

	/**
	 * Boot the configuration manager
	 *
	 * Loads default configuration files (app.php and providers.php).
	 *
	 * @param string $config_folder_path Path to the configuration folder
	 * @return void
	 */
	public function boot( $config_folder_path ) {

		$this->set_primary_folder_path( $config_folder_path );

		$file_list = array(
			'app'       => $this->get_primary_folder_path() . 'app.php',
			'providers' => $this->get_primary_folder_path() . 'providers.php',
		);

		foreach ( $file_list as $file_key => $file_path ) {
			if ( file_exists( $file_path ) ) {
				$this->load_from_file( $file_key, $file_path );
			}
		}
	}

	/**
	 * Set the primary configuration folder path
	 *
	 * Normalizes the path by removing trailing slashes/backslashes.
	 *
	 * @param string $folder_path The configuration folder path
	 * @return void
	 */
	public function set_primary_folder_path( $folder_path ) {

		$folder_path = rtrim( $folder_path, '/' );
		$folder_path = rtrim( $folder_path, '\\' );

		$this->primary_folder_path = $folder_path . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get the primary configuration folder path
	 *
	 * @return string
	 */
	public function get_primary_folder_path() {
		return $this->primary_folder_path;
	}

	/**
	 * Load a configuration file and register it
	 *
	 * @param string $key The configuration key/identifier
	 * @param string $file_path Path to the PHP configuration file
	 * @return void
	 */
	public function load_from_file( $key, $file_path ) {

		$config = new Config();
		$config->load_from_file( $file_path );

		$this->list[ $key ] = $config;
	}

	/**
	 * Get a configuration object by key
	 *
	 * @param string $key The configuration key
	 * @return Config|null The configuration object or null if not found
	 */
	public function get( $key ) {

		if ( isset( $this->list[ $key ] ) ) {
			return $this->list[ $key ];
		} else {
			return null;
		}
	}

	/**
	 * Set a configuration object
	 *
	 * @param string $key The configuration key
	 * @param Config $value The configuration object
	 * @return void
	 */
	public function set( $key, $value ) {

		if ( isset( $this->list[ $key ] ) ) {
			$this->list[ $key ] = $value;
		}
	}
}
