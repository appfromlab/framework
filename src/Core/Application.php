<?php
namespace AFL\Framework\Core;

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
	 * Initialize the application with core components
	 */
	public function __construct() {

		$this->add( Logger::class, new Logger() );
		$this->add( Config_Manager::class, new Config_Manager() );
		$this->add( Service_Provider_Manager::class, new Service_Provider_Manager( $this ) );
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

		$this->file_path = $file_path;

		if ( empty( $config_folder_path ) ) {
			$config_folder_path = rtrim( ( dirname( $file_path ) ), '/\\' ) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
		}

		$this->get( Logger::class )->boot();
		$this->get( Config_Manager::class )->boot( $config_folder_path );
		$this->get( Service_Provider_Manager::class )->boot( $this->config( 'providers' )->get( 'providers' ) );
	}

	/**
	 * Get the plugin/application file path
	 *
	 * @return string
	 */
	public function get_file_path() {
		return $this->file_path;
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
