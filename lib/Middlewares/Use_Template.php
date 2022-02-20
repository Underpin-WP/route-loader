<?php

namespace Underpin\Routes\Middlewares;

use Underpin\Abstracts\Observer;
use Underpin\Abstracts\Storage;
use Underpin\Routes\Abstracts\Route;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Use_Template extends Observer {

	/**
	 * @var Route
	 */
	protected $route;

	protected $template = '';

	public function __construct( $template ) {
		$this->template = $template;
		parent::__construct( 'use_template' );
	}

	public function update( $instance, Storage $args ) {
		if ( $instance instanceof Route ) {
			$this->route = $instance;
			add_filter( 'template_include', [ $this, 'include_template' ], 10, 2 );
		}
	}

	public function include_template( $template ) {
		if ( $this->route->is_current_route ) {
			return $this->template;
		}

		return $template;
	}

}