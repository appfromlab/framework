<?php
namespace AFL\Framework\Core;

use AFL\Framework\Core\Container;
use AFL\Framework\Core\Config_Manager;
use AFL\Framework\Core\Service_Provider_Manager;
use AFL\Framework\Core\Logger;
use AFL\Framework\Core\Config;

/**
 * Main Application Class
 *
 * Bootstraps the framework and manages core components including configuration,
 * logging, dependency injection, and service providers.
 *
 * @since 0.0.1
 */
class Application extends Container {

	/**
	 * Plugin/Application file path
	 *
	 * @var string
	 */
	protected $file_path;

	/**
	 * Configuration folder path
	 *
	 * @var string
	 */
	protected $config_folder_path;

	/**
	 * Initialize the application with core components
	 */
	public function __construct() {
	}

	/**
	 * Boot the application
	 *
	 * Initializes the logger, configuration manager, and service providers.
	 *
	 * @param string $file_path The main plugin/application file path.
	 * @param string $config_folder_path Optional path to configuration folder.
	 * @return void
	 */
	public function boot( $file_path, $config_folder_path = '' ) {

		if ( empty( $file_path ) ) {
			return;
		}

		$this->set_file_path( $file_path );
		$this->set_config_folder_path( $config_folder_path );
		$this->register_base_services();
		$this->boot_base_services();
	}

	/**
	 * Set the application file path
	 *
	 * @param string $file_path The main application file path.
	 */
	public function set_file_path( $file_path ) {
		$this->file_path = $file_path;
	}

	/**
	 * Get the application file path
	 *
	 * @return string
	 */
	public function get_file_path() {
		return $this->file_path;
	}

	/**
	 * Set the configuration folder path
	 *
	 * @param string $path The configuration folder path.
	 */
	public function set_config_folder_path( $path ) {

		if ( empty( $path ) && ! is_dir( $path ) ) {
			return;
		}

		$this->config_folder_path = rtrim( $path, '/\\' ) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Get the configuration folder path
	 *
	 * @return string
	 */
	public function get_config_folder_path() {

		if ( empty( $this->config_folder_path ) ) {
			$this->config_folder_path = rtrim( ( dirname( $this->get_file_path() ) ), '/\\' ) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
		}

		return $this->config_folder_path;
	}

	/**
	 * Register base services in the container
	 *
	 * @return void
	 */
	protected function register_base_services() {

		$this->singleton(
			Logger::class,
			function () {
				return new Logger( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ? true : false );
			}
		);

		$this->singleton(
			Config_Manager::class,
			function ( $app ) {
				return new Config_Manager( $app->get_config_folder_path() );
			}
		);

		$this->singleton(
			Service_Provider_Manager::class,
			function ( $app ) {
				return new Service_Provider_Manager( $app, $app->config( 'providers' )->get( 'providers' ) );
			}
		);
	}

	/**
	 * Boot base services in the container
	 *
	 * @return void
	 */
	protected function boot_base_services() {

		$this->service_provider_manager()->boot();
	}

	/**
	 * Get the logger instance
	 *
	 * @return Logger
	 */
	public function logger() {

		return $this->get( Logger::class );
	}

	/**
	 * Get the configuration manager
	 *
	 * @return Config_Manager
	 */
	public function config_manager() {

		return $this->get( Config_Manager::class );
	}

	/**
	 * Get a configuration object
	 *
	 * @param string $file_slug The configuration file slug (e.g., 'app', 'providers').
	 * @return Config
	 */
	public function config( $file_slug = 'app' ) {

		return $this->get( Config_Manager::class )->get( $file_slug );
	}

	/**
	 * Get the service provider manager
	 *
	 * @return Service_Provider_Manager
	 */
	public function service_provider_manager() {

		return $this->get( Service_Provider_Manager::class );
	}

	/**
	 * Get the application version
	 *
	 * @return mixed
	 */
	public function get_version() {
		return $this->config()->get( 'version' );
	}
}
