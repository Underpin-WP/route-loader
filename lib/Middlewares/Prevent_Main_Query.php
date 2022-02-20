<?php

namespace Underpin\Routes\Middlewares;

use Underpin\Abstracts\Observer;
use Underpin\Abstracts\Storage;
use Underpin\Routes\Abstracts\Route;
use WP_Query;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Prevent_Main_Query extends Observer {

	/**
	 * @var Route
	 */
	protected $route;

	public function __construct() {
		parent::__construct( 'prevent_main_query' );
	}

	public function update( $instance, Storage $args ) {
		if ( $instance instanceof Route ) {
			$this->route = $instance;
			add_filter( 'posts_request', [ $this, 'prevent_main_query' ], 10, 2 );
		}
	}

	/**
	 * @param          $sql
	 * @param WP_Query $query
	 *
	 * @return string|false The current query if this route should not be prevented, otherwise false.
	 */
	public function prevent_main_query( $sql, WP_Query $query ) {

		if ( $query->is_main_query() && true === $this->route->is_current_route ) {
			// prevent SELECT FOUND_ROWS() query
			$query->query_vars['no_found_rows'] = true;

			// prevent post term and meta cache update queries
			$query->query_vars['cache_results'] = false;

			return false;
		}
		return $sql;
	}

}