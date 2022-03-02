<?php

use Underpin\Abstracts\Underpin;
use Underpin\Factories\Observers\Loader;
use Underpin\Routes\Loaders\Routes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Add this loader.
Underpin::attach( 'setup', new Loader( 'routes', [ 'class' => Routes::class ] ) );
