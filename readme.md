# Underpin Route Loader

Loader That assists with adding custom routes to a WordPress website.

## Installation

### Using Composer

`composer require underpin/route-loader`

### Manually

This plugin uses a built-in autoloader, so as long as it is required _before_
Underpin, it should work as-expected.

`require_once(__DIR__ . '/route-loader/bootstrap.php');`

## Setup

1. Install Underpin. See [Underpin Docs](https://www.github.com/underpin-wp/underpin)
1. Register new routes as-needed.

## Example

A very basic example could look something like this.

```php
plugin_name()->routes()->add( 'dashboard-home', [
	'name'        => 'Dashboard',
	'description' => "Route for dashboard screen",
    'id_callback' => function () { // Callback function to set the route ID. This should be unique.
		$pieces               = [ 'account' ];
		$account_screen       = get_query_var( 'account_screen', false );
		$account_child_screen = get_query_var( 'account_child_screen', false );

		if ( $account_screen ) {
			$pieces[] = $account_screen;
		}

		if ( $account_child_screen ) {
			$pieces[] = $account_child_screen;
		}

		return implode( '_', $pieces );
	},
	'priority'    => 'top', // Optional. sets the priority of add_rewrite_rule. Defaults to bottom. Can be "bottom" or "top"
	'route'       => '^account/?([A-Za-z0-9-]+)?/?([A-Za-z0-9-]+)?/?$', // Regex for route. See https://developer.wordpress.org/reference/functions/add_rewrite_rule/
	'query_vars'  => [ 'account_screen' => '$matches[1]', 'account_child_screen' => '$matches[2]' ], // Query vars for route. See https://developer.wordpress.org/reference/functions/add_rewrite_rule/
] );
```

Alternatively, you can extend `Route` and reference the extended class directly. This would allow you to use Underpin's
Template loader trait, as well as other more-advanced class-based utilities:

```php
plugin_name()->routes()->add('key','Namespace\To\Class');
```

## Middleware

Since there are many ways a route can be used, this loader simply _registers_ the route to use, ensures any custom query
params are whitelisted, and ensures that they are sorted to minimize collisions with routes. In-order to _do_ something
with your route, you need to register additional actions. To help facilitate common actions, this loader comes with
middleware that can extend the behavior of routes.

### Template Middleware

The `Use_Template` middleware allows you to render a custom template when this route is used.

```php
plugin_name()->routes()->add( 'dashboard-home', [
	'name'        => 'Dashboard',
	'description' => "Route for dashboard screen",
	'route'       => '^account/?([A-Za-z0-9-]+)?/?([A-Za-z0-9-]+)?/?$', // Regex for route. See https://developer.wordpress.org/reference/functions/add_rewrite_rule/
	'query_vars'  => [ 'account_screen' => '$matches[1]', 'account_child_screen' => '$matches[2]' ], // Query vars for route. See https://developer.wordpress.org/reference/functions/add_rewrite_rule/
    'id_callback' => function () { // Callback function to set the route ID. This should be unique.
		$pieces               = [ 'account' ];
		$account_screen       = get_query_var( 'account_screen', false );
		$account_child_screen = get_query_var( 'account_child_screen', false );

		if ( $account_screen ) {
			$pieces[] = $account_screen;
		}

		if ( $account_child_screen ) {
			$pieces[] = $account_child_screen;
		}

		return implode( '_', $pieces );
	},
	'middlewares' => [
	  new \Underpin\Routes\Middlewares\Use_Template('/path/to/template/file.php')
    ]
] );
```

### Prevent Query Middleware

By default, WordPress makes a database call on every page load to load a post object in the query. Sometimes, however,
this is not necessary on custom routes. However, Even if you don't specify a post, WordPress will load a default post
instead. This causes an additional query and can cause other unwanted behaviors, as well.

To circumvent this, use the `Prevent_Main_Query` middleware, like so:

```php
plugin_name()->routes()->add( 'dashboard-home', [
	'name'        => 'Dashboard',
	'description' => "Route for dashboard screen",
	'route'       => '^account/?([A-Za-z0-9-]+)?/?([A-Za-z0-9-]+)?/?$', // Regex for route. See https://developer.wordpress.org/reference/functions/add_rewrite_rule/
	'query_vars'  => [ 'account_screen' => '$matches[1]', 'account_child_screen' => '$matches[2]' ], // Query vars for route. See https://developer.wordpress.org/reference/functions/add_rewrite_rule/
    'id_callback' => function () { // Callback function to set the route ID. This should be unique.
		$pieces               = [ 'account' ];
		$account_screen       = get_query_var( 'account_screen', false );
		$account_child_screen = get_query_var( 'account_child_screen', false );

		if ( $account_screen ) {
			$pieces[] = $account_screen;
		}

		if ( $account_child_screen ) {
			$pieces[] = $account_child_screen;
		}

		return implode( '_', $pieces );
	},
	'middlewares' => [
	  new \Underpin\Routes\Middlewares\Prevent_Main_Query
    ]
] );
```

This middleware will stop the primary query from running, while leaving the global WP_Query otherwise intact.

## Working With Routes

### Testing for Current Route

Usually you'll need to do some kind-of dynamic logic to determine certain behaviors that only run when the current page
matches your route. This loader helps facilitate that with `is_current_route()`, which can be used like so:

```php
if( plugin_name()->routes()->is_current_route( 'route-id' ) ){
  // Do something specific to this route.
}
```

If you happen to have the `Route` object directly, you can access it like so:

```php
if( $route->is_current_route ){
  // Do something specific to this route.
}
```