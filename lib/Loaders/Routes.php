<?php
/**
 * Script Loader
 *
 * @since   1.0.0
 * @package Underpin\Registries\Loaders
 */


namespace Underpin\Routes\Loaders;

use Underpin\Abstracts\Registries\Object_Registry;
use Underpin\Routes\Abstracts\Route;
use Underpin\Routes\Factories;
use WP_Error;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Scripts
 * Loader for scripts
 *
 * @since   1.0.0
 * @package Underpin\Registries\Loaders
 */
class Routes extends Object_Registry {

	protected $abstraction_class = Route::class;
	protected $default_factory   = Factories\Route::class;

	public function __construct() {
		parent::__construct();
		$this->do_actions();
	}

	protected function do_actions() {
		add_filter( 'init', [ $this, 'setup_rewrite_rules' ] );
		add_filter( 'query_vars', [ $this, 'whitelist_rewrite_vars' ] );
	}

	function whitelist_rewrite_vars( $vars ) {
		$items = [];
		foreach ( (array) $this as $item ) {
			$items = array_merge( $items, array_keys( $item->query_vars ) );
		}
		return array_merge( $vars, array_unique( $items ) );
	}

	/**
	 * Sets up rewrite rules for registered routes.
	 *
	 * @return void
	 */
	public function setup_rewrite_rules() {
		/* @var Route $item */
		foreach ( (array) $this as $item ) {
			add_rewrite_rule( $item->route, $item->query_vars, $item->priority );
		}
	}

	/**
	 * @param $key
	 *
	 * @return Route|WP_Error
	 */
	public function get( $key ) {
		return parent::get( $key );
	}

	/**
	 * @param $route_id string The route ID
	 *
	 * @return boolean true if this is the current route, otherwise false.
	 */
	public function is_current_route( $route_id ) {

		$route = $this->find( [ 'id' => $route_id ] );

		if ( is_wp_error( $route ) ) {
			return false;
		}

		/* @var Route $route */
		return $route->is_current_route;
	}

	/**
	 * Attempts to retrieve the current route from routes registered against this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Route|WP_Error The route if found, otherwise WP_Error
	 */
	public function current() {
		return $this->find( [ 'is_current_route' => true ] );
	}

	/**
	 * Attempts to retrieve the current route from routes registered against this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Route|WP_Error The route if found, otherwise WP_Error
	 */
	public function get_by_route( $route ) {
		return $this->find( [ 'route' => $route ] );
	}

	protected function set_default_items() {
		// TODO: Implement set_default_items() method.
	}

}