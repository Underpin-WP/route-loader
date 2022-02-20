<?php

namespace Underpin\Routes\Factories;

use Underpin\Traits\Instance_Setter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class Route extends \Underpin\Routes\Abstracts\Route {

	use Instance_Setter;

	protected $id_callback;

	public function __construct( $args ) {
		$this->set_values( $args );
	}

	public function get_id() {
		return $this->set_callable( $this->id_callback, $this->query_vars );
	}

}