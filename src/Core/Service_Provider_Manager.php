<?php
namespace AFL\Framework\Core;

/**
 * Service Provider Manager
 *
 * Manages the registration, initialization, and lifecycle hooks of service providers.
 * Service providers are responsible for registering and bootstrapping application services.
 *
 * @since 0.0.1
 */
class Service_Provider_Manager {

	/**
	 * Application instance
	 *
	 * @var Application
	 */
	protected $app;

	/**
	 * List of registered service providers
	 *
	 * @var array
	 */
	protected $service_providers = array();

	/**
	 * Initialize the service provider manager
	 *
	 * @param Application $app The application instance
	 */
	public function __construct( Application $app ) {

		$this->app = $app;
	}

	/**
	 * Boot the service provider manager
	 *
	 * Registers and boots all service providers in the list.
	 *
	 * @param array $service_providers List of service provider class names
	 * @return void
	 */
	public function boot( $service_providers = array() ) {

		if ( ! empty( $service_providers ) && is_array( $service_providers ) ) {

			foreach ( $service_providers as $provider_class_name ) {

				$this->add( $provider_class_name );
			}
		}

		foreach ( $this->list() as $provider ) {
			$provider->boot();
		}
	}

	/**
	 * Get list of registered service providers
	 *
	 * @return array
	 */
	public function list() {
		return $this->service_providers;
	}

	/**
	 * Get a service provider by class name
	 *
	 * @param string $provider_class_name The service provider class name
	 * @return Service_Provider|null
	 */
	public function get( $provider_class_name ) {

		if ( isset( $this->service_providers[ $provider_class_name ] ) ) {
			$this->service_providers[ $provider_class_name ];
		} else {
			return null;
		}
	}

	/**
	 * Check if a service provider is registered
	 *
	 * @param string $provider_class_name The service provider class name
	 * @return bool
	 */
	public function has( $provider_class_name ) {

		if ( isset( $this->service_providers[ $provider_class_name ] ) ) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Register and add a service provider
	 *
	 * @param string $provider_class_name The service provider class name
	 * @return void
	 */
	public function add( $provider_class_name ) {

		if ( ! class_exists( $provider_class_name ) && ! $this->has( $provider_class_name ) ) {
			return;
		}

		$provider_obj = new $provider_class_name( $this->app );

		if ( is_a( $provider_obj, Service_Provider::class ) ) {
			$provider_obj->register();
			$this->service_providers[ $provider_class_name ] = $provider_obj;
		}
	}

	/**
	 * Call plugin_loaded hook on all service providers
	 *
	 * @return void
	 */
	public function plugin_loaded() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->plugin_loaded();
			}
		}
	}

	/**
	 * Call register_hooks method on all service providers
	 *
	 * @return void
	 */
	public function register_hooks() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->register_hooks();
			}
		}
	}

	/**
	 * Call init method on all service providers
	 *
	 * @return void
	 */
	public function init() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->init();
			}
		}
	}

	/**
	 * Call admin_init method on all service providers
	 *
	 * @return void
	 */
	public function admin_init() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->admin_init();
			}
		}
	}
}
