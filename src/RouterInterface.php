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
 * RouterInterface represents the contract for a router implementation.
 *
 * @category    Omega
 * @package     Omega\Routing
 * @link        https://omegacms.github.com
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.com)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
interface RouterInterface
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
    public function addRoute(string $method, string $path, mixed $handler, ?string $name = null ): Route;

    /**
     * Adds a GET route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function get(string $path, mixed $handler, ?string $name = null ) : Route;

    /**
     * Adds a POST route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function post(string $path, mixed $handler, ?string $name = null ) : Route;

    /**
     * Adds a PUT route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function put(string $path, mixed $handler, ?string $name = null ) : Route;

    /**
     * Adds a DELETE route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function delete(string $path, mixed $handler, ?string $name = null ) : Route;

    /**
     * Adds a PATCH route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function patch(string $path, mixed $handler, ?string $name = null ) : Route;

    /**
     * Adds an OPTIONS route to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function options(string $path, mixed $handler, ?string $name = null ) : Route;

    /**
     * Adds a route that matches any HTTP method to the router.
     *
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function any(string $path, mixed $handler, ?string $name = null ) : Route;

    /**
     * Dispatches the router, matching the current request and executing the corresponding route handler.
     *
     * @return mixed  The result of the route handler.
     */
    public function dispatch(): mixed;
}
