<?php
namespace AFL\Framework\Core;

/**
 * Dependency Injection Container
 *
 * A service container for registering and resolving dependencies.
 *
 * @since 0.0.1
 */
class Container implements \ArrayAccess {

	/**
	 * Service bindings and cached singletons
	 *
	 * @var array
	 */
	private $bindings = array();

	/**
	 * Service aliases
	 *
	 * @var array
	 */
	private $aliases = array();

	/**
	 * Services currently being resolved (circular dependency detection)
	 *
	 * @var array
	 */
	private $resolving = array();

	/**
	 * Resolution callbacks
	 *
	 * @var array
	 */
	private $callbacks = array();

	/**
	 * Register a binding in the container
	 *
	 * @param string $id The service identifier.
	 * @param mixed  $resolver The class name, callable, or instance.
	 * @return self
	 */
	public function bind( $id, $resolver ) {
		$this->bindings[ $id ] = $resolver;
		return $this;
	}

	/**
	 * Register a singleton binding in the container
	 *
	 * @param string $id The service identifier.
	 * @param mixed  $resolver The class name, callable, or instance.
	 * @return self
	 */
	public function singleton( $id, $resolver ) {
		$this->bindings[ $id ] = array(
			'resolver'  => $resolver,
			'singleton' => true,
		);
		return $this;
	}

	/**
	 * Add an instance directly to the container
	 *
	 * @param string $id The service identifier.
	 * @param mixed  $instance The instance to store.
	 * @return self
	 */
	public function add( $id, $instance ) {
		$this->bindings[ $id ] = $instance;
		return $this;
	}

	/**
	 * Create an alias for a service
	 *
	 * @param string $alias The alias name.
	 * @param string $id The service identifier to alias.
	 * @return self
	 */
	public function alias( $alias, $id ) {
		$this->aliases[ $alias ] = $id;
		return $this;
	}

	/**
	 * Register a callback to be called after a service is resolved
	 *
	 * @param string   $id The service identifier.
	 * @param callable $callback The callback function.
	 * @return self
	 */
	public function resolved( $id, callable $callback ) {
		if ( ! isset( $this->callbacks[ $id ] ) ) {
			$this->callbacks[ $id ] = array();
		}
		$this->callbacks[ $id ][] = $callback;
		return $this;
	}

	/**
	 * Resolve a binding from the container
	 *
	 * @template T
	 * @param class-string<T> $id The service identifier.
	 * @return T
	 * @throws \Exception If the binding is not found or circular dependency detected.
	 */
	public function get( $id ) {
		// Resolve alias.
		if ( isset( $this->aliases[ $id ] ) ) {
			$id = $this->aliases[ $id ];
		}

		// Check if binding exists.
		if ( ! isset( $this->bindings[ $id ] ) ) {
			throw new \Exception( "Service '{$id}' is not bound in the container." );
		}

		// Check for circular dependency.
		if ( isset( $this->resolving[ $id ] ) ) {
			throw new \Exception( "Circular dependency detected for service '{$id}'." );
		}

		$binding = $this->bindings[ $id ];

		// If binding is not an array with 'resolver', it's already resolved or cached.
		if ( ! is_array( $binding ) || ! isset( $binding['resolver'] ) ) {
			$this->call_callbacks( $id, $binding );
			return $binding;
		}

		// Mark as resolving to detect circular dependencies.
		$this->resolving[ $id ] = true;

		try {
			// Handle singleton: resolve and cache it.
			if ( isset( $binding['singleton'] ) && $binding['singleton'] ) {
				$resolver              = $binding['resolver'];
				$instance              = $this->resolve( $resolver );
				$this->bindings[ $id ] = $instance; // Cache the instance.
				unset( $this->resolving[ $id ] );
				$this->call_callbacks( $id, $instance );
				return $instance;
			}

			// Handle transient: resolve without caching.
			$instance = $this->resolve( $binding['resolver'] );
			unset( $this->resolving[ $id ] );
			$this->call_callbacks( $id, $instance );
			return $instance;
		} catch ( \Exception $e ) {
			unset( $this->resolving[ $id ] );
			throw $e;
		}
	}

	/**
	 * Check if a binding exists in the container
	 *
	 * @param string $id The service identifier.
	 * @return bool True if binding exists, false otherwise.
	 */
	public function has( $id ) {
		return isset( $this->bindings[ $id ] ) || isset( $this->aliases[ $id ] );
	}

	/**
	 * Remove a binding from the container
	 *
	 * @param string $id The service identifier.
	 * @return self
	 */
	public function remove( $id ) {
		unset( $this->bindings[ $id ], $this->callbacks[ $id ] );
		return $this;
	}

	/**
	 * Get all registered service IDs
	 *
	 * @return array Array of service identifiers.
	 */
	public function all() {
		return array_keys( $this->bindings );
	}

	/**
	 * ArrayAccess: Check if a binding exists
	 *
	 * @param mixed $offset The service identifier.
	 * @return bool
	 */
	#[\ReturnTypeWillChange]
	public function offsetExists( $offset ) {
		return $this->has( $offset );
	}

	/**
	 * ArrayAccess: Get a service
	 *
	 * @param mixed $offset The service identifier.
	 * @return mixed
	 */
	#[\ReturnTypeWillChange]
	public function offsetGet( $offset ) {
		return $this->get( $offset );
	}

	/**
	 * ArrayAccess: Register a binding
	 *
	 * @param mixed $offset The service identifier.
	 * @param mixed $value The resolver or instance.
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetSet( $offset, $value ) {
		$this->bind( $offset, $value );
	}

	/**
	 * ArrayAccess: Remove a binding
	 *
	 * @param mixed $offset The service identifier.
	 * @return void
	 */
	#[\ReturnTypeWillChange]
	public function offsetUnset( $offset ) {
		$this->remove( $offset );
	}

	/**
	 * Resolve a binding to an instance
	 *
	 * Handles class names, callables, and direct values.
	 *
	 * @param mixed $resolver The resolver (class name, callable, or value).
	 * @return mixed The resolved value or instance.
	 * @throws \Exception If unable to resolve the binding.
	 */
	private function resolve( $resolver ) {
		// If it's a callable, invoke it.
		if ( is_callable( $resolver ) ) {
			return call_user_func( $resolver, $this );
		}

		// If it's a string, assume it's a class name.
		if ( is_string( $resolver ) ) {
			return $this->make( $resolver );
		}

		// Return the value as-is.
		return $resolver;
	}

	/**
	 * Create an instance of a class with automatic dependency injection
	 *
	 * @param string $class_name The class name to instantiate.
	 * @return mixed The created instance.
	 * @throws \Exception If the class doesn't exist.
	 */
	private function make( $class_name ) {
		if ( ! class_exists( $class_name ) ) {
			throw new \Exception( "Class '{$class_name}' does not exist." );
		}

		$reflection = new \ReflectionClass( $class_name );

		// Check if the class can be instantiated.
		if ( ! $reflection->isInstantiable() ) {
			throw new \Exception( "Class '{$class_name}' cannot be instantiated." );
		}

		// Get the constructor method.
		$constructor = $reflection->getConstructor();

		// If there's no constructor, just instantiate the class.
		if ( is_null( $constructor ) ) {
			return new $class_name();
		}

		// Get constructor parameters.
		$parameters = $constructor->getParameters();

		// If there are no parameters, just instantiate.
		if ( empty( $parameters ) ) {
			return new $class_name();
		}

		// Resolve constructor dependencies.
		$resolved_params = array();
		foreach ( $parameters as $param ) {
			$resolved_params[] = $this->resolve_parameter( $param );
		}

		return $reflection->newInstanceArgs( $resolved_params );
	}

	/**
	 * Resolve a constructor parameter
	 *
	 * @param \ReflectionParameter $param The parameter to resolve.
	 * @return mixed The resolved parameter value.
	 * @throws \Exception If unable to resolve the parameter.
	 */
	private function resolve_parameter( \ReflectionParameter $param ) {
		$type = $param->getType();

		// If the parameter has a type hint, try to resolve it.
		if ( ! is_null( $type ) && ! $type->isBuiltin() ) {
			$class_name = $type->getName();

			// Try to get it from the container.
			if ( $this->has( $class_name ) ) {
				return $this->get( $class_name );
			}

			// Try to instantiate it.
			return $this->make( $class_name );
		}

		// If the parameter has a default value, use it.
		if ( $param->isDefaultValueAvailable() ) {
			return $param->getDefaultValue();
		}

		// Unable to resolve the parameter.
		throw new \Exception( "Unable to resolve parameter '{$param->getName()}'." );
	}

	/**
	 * Call registered callbacks for a service
	 *
	 * @param string $id The service identifier.
	 * @param mixed  $instance The resolved instance.
	 * @return void
	 */
	private function call_callbacks( $id, $instance ) {
		if ( isset( $this->callbacks[ $id ] ) ) {
			foreach ( $this->callbacks[ $id ] as $callback ) {
				call_user_func( $callback, $instance, $this );
			}
		}
	}
}
