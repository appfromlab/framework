<?php
namespace AFL\Framework\Core;

/**
 * @since 0.0.1
 */
class Service_Provider_Manager {

	protected $app;
	protected $service_providers = array();

	public function __construct( Application $app ) {

		$this->app = $app;
	}

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

	public function list() {
		return $this->service_providers;
	}

	public function get( $provider_class_name ) {

		if ( isset( $this->service_providers[ $provider_class_name ] ) ) {
			$this->service_providers[ $provider_class_name ];
		} else {
			return null;
		}
	}

	public function has( $provider_class_name ) {

		if ( isset( $this->service_providers[ $provider_class_name ] ) ) {
			return true;
		} else {
			return false;
		}
	}

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

	public function plugin_loaded() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->plugin_loaded();
			}
		}
	}

	public function register_hooks() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->register_hooks();
			}
		}
	}

	public function init() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->init();
			}
		}
	}

	public function admin_init() {

		$service_providers = $this->list();

		if ( ! empty( $service_providers ) ) {
			foreach ( $service_providers as $provider_obj ) {
				$provider_obj->admin_init();
			}
		}
	}
}
