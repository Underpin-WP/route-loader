<?php

namespace Underpin\Routes\Abstracts;

use Underpin\Traits\With_Middleware;

abstract class Route {

	use With_Middleware;

	protected $route            = '';
	protected $query_vars       = [];
	protected $is_current_route = null;
	protected $priority         = 'bottom';
	protected $path             = '';
	protected $id               = '';
	private const FALSE_HASH = '11b3a8b654edbdec28dd944c3b44f3921';

	protected function get_path() {
		return add_query_arg( $this->query_vars, 'index.php' );
	}

	abstract public function get_id();

	/**
	 * Determines if is route is the current route.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if this is the current route, otherwise false.
	 */
	protected function is_current_route() {
		foreach ( array_keys( $this->query_vars ) as $query_var ) {
			if ( get_query_var( $query_var, self::FALSE_HASH ) === self::FALSE_HASH ) {
				return false;
			}
		}

		return true;
	}

	public function __get( $key ) {

		if ( 'path' === $key && empty( $this->path ) ) {
			$this->path = $this->get_path();
			return $this->path;
		}

		if ( 'id' === $key && empty( $this->id ) ) {
			$this->id = $this->get_id();
			return $this->id;
		}

		if ( 'is_current_route' === $key && empty ( $this->is_current_route ) ) {
			$this->is_current_route = $this->is_current_route();
			return $this->is_current_route;
		}

		if ( isset( $this->$key ) ) {
			return $this->$key;
		} else {
			return new \WP_Error( 'route_param_not_set', 'The key ' . $key . ' could not be found.' );
		}
	}

}