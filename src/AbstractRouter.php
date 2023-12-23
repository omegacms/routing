<?php
/**
 * Part of Omega CMS -  Routing Package
 *
 * @link       https://omegacms.github.io
 * @author     Adriano Giovannini <omegacms@outlook.com>
 * @copyright  Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license    https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 */

/**
 * @declare
 */
declare( strict_types = 1 );

/**
 * @namespace
 */
namespace Omega\Routing;

/**
 * AbstractRouter class.
 *
 * @category    Omega
 * @package     Omega\Routing
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
abstract class AbstractRouter implements RouterInterface
{
    /**
     * Adds a route to the router.
     *
     * @param  string  $method  Holds the HTTP method for the route.
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function addRoute(string $method, string $path, mixed $handler, ?string $name = null ): Route
    {
        $route          = new Route( $method, $path, $handler, $name );
        $this->routes[] = $route;

        return $route;
    }

    /**
     * Adds a GET route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function get(string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->addRoute( 'GET', $path, $handler, $name );
    }

    /**
     * Adds a POST route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function post(string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->addRoute( 'POST', $path, $handler, $name );
    }

    /**
     * Adds a PUT route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function put(string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->addRoute( 'PUT', $path, $handler, $name );
    }

    /**
     * Adds a DELETE route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function delete(string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->addRoute( 'DELETE', $path, $handler, $name );
    }

    /**
     * Adds a PATCH route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function patch(string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->addRoute( 'PATCH', $path, $handler, $name );
    }

    /**
     * Adds an OPTIONS route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function options(string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->addRoute( 'OPTIONS', $path, $handler, $name );
    }

    /**
     * Adds a route that matches any HTTP method to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function any(string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->addRoute( 'GET|POST|PUT|DELETE|PATCH|OPTIONS', $path, $handler, $name );
    }

    /**
     * Dispatches the router, matching the current request and executing the corresponding route handler.
     *
     * @return mixed  The result of the route handler.
     */
    abstract public function dispatch(): mixed;
}
