<?php
namespace AFL\Framework\Core;

/**
 * @since 0.0.1
 */
class Application {

	protected $file_path;
	protected $container;
	protected $config_manager;
	protected $logger;
	protected $service_provider_manager;

	public function __construct() {

		$this->container                = new Container();
		$this->logger                   = new Logger();
		$this->config_manager           = new Config_Manager();
		$this->service_provider_manager = new Service_Provider_Manager( $this );
	}

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

	public function get_file_path() {
		return $this->file_path;
	}

	public function container() {

		return $this->container;
	}

	public function logger() {

		return $this->logger;
	}

	public function config_manager() {

		return $this->config_manager;
	}

	public function config( $file_slug = 'app' ) {

		return $this->config_manager->get( $file_slug );
	}

	public function service_provider_manager() {

		return $this->service_provider_manager;
	}

	public function get_version() {
		return $this->config()->get( 'version' );
	}
}
