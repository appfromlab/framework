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
class Application {

	/**
	 * Plugin/Application file path
	 *
	 * @var string
	 */
	protected $file_path;

	/**
	 * Service container instance
	 *
	 * @var Container
	 */
	protected $container;

	/**
	 * Configuration manager instance
	 *
	 * @var Config_Manager
	 */
	protected $config_manager;

	/**
	 * Logger instance
	 *
	 * @var Logger
	 */
	protected $logger;

	/**
	 * Service provider manager instance
	 *
	 * @var Service_Provider_Manager
	 */
	protected $service_provider_manager;

	/**
	 * Initialize the application with core components
	 */
	public function __construct() {

		$this->container                = new Container();
		$this->logger                   = new Logger();
		$this->config_manager           = new Config_Manager();
		$this->service_provider_manager = new Service_Provider_Manager( $this );
	}

	/**
	 * Boot the application
	 *
	 * Initializes the logger, configuration manager, and service providers.
	 *
	 * @param string $file_path The main plugin/application file path
	 * @param string $config_folder_path Optional path to configuration folder
	 * @return void
	 */
	public function boot( $file_path, $config_folder_path = '' ) {

		if ( empty( $file_path ) ) {
			return;
		}

		$this->file_path = $file_path;

		if ( empty( $config_folder_path ) ) {
			$config_folder_path = trailingslashit( dirname( $file_path ) ) . 'config/';
		}

		$this->logger->boot();
		$this->config_manager->boot( $config_folder_path );
		$this->service_provider_manager->boot( $this->config( 'providers' )->get( 'providers' ) );
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
	 * Get the service container
	 *
	 * @return Container
	 */
	public function container() {

		return $this->container;
	}

	/**
	 * Get the logger instance
	 *
	 * @return Logger
	 */
	public function logger() {

		return $this->logger;
	}

	/**
	 * Get the configuration manager
	 *
	 * @return Config_Manager
	 */
	public function config_manager() {

		return $this->config_manager;
	}

	/**
	 * Get a configuration object
	 *
	 * @param string $file_slug The configuration file slug (e.g., 'app', 'providers')
	 * @return Config
	 */
	public function config( $file_slug = 'app' ) {

		return $this->config_manager->get( $file_slug );
	}

	/**
	 * Get the service provider manager
	 *
	 * @return Service_Provider_Manager
	 */
	public function service_provider_manager() {

		return $this->service_provider_manager;
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
