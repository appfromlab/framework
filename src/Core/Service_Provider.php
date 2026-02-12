<?php
namespace AFL\Framework\Core;

/**
 * Abstract Service Provider Base Class
 *
 * Base class for all service providers. Service providers are responsible for
 * registering and bootstrapping application services at various lifecycle stages.
 *
 * @since 0.0.1
 */
abstract class Service_Provider {

	/**
	 * Application instance
	 *
	 * @var Application
	 */
	protected $app;

	/**
	 * Initialize the service provider
	 *
	 * @param Application $app The application instance
	 */
	public function __construct( Application $app ) {

		$this->app = $app;
	}

	/**
	 * Register any application services.
	 *
	 * @link https://laravel.com/docs/11.x/providers#the-register-method
	 * @return void
	 */
	public function register() {
	}

	/**
	 * Run after registering all service providers.
	 *
	 * @return void
	 */
	public function boot() {
	}

	/**
	 * Run during WordPress plugin_loaded hook.
	 *
	 * @return void
	 */
	public function plugin_loaded() {
	}

	/**
	 * Register WordPress related hooks.
	 *
	 * @return void
	 */
	public function register_hooks() {
	}

	/**
	 * Run during WordPress init hook.
	 *
	 * @return void
	 */
	public function init() {
	}

	/**
	 * Run during WordPress admin init hook.
	 *
	 * @return void
	 */
	public function admin_init() {
	}
}
