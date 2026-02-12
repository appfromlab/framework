<?php
namespace AFL\Framework\Core;

/**
 * @since 0.0.1
 */
abstract class Service_Provider {

	protected $app;

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
