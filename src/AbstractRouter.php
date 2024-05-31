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
 * Abstract router class.
 * 
 * The `AbstractRouter` class serves as a foundation for implementing custom routers within 
 * the Omega CMS Routing Package. It provides a set of methods to add routes for different 
 * HTTP methods and defines the dispatching behavior, allowing derived classes to handle 
 * route matching and execution of associated handlers.
 * *
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
     * Routes array.
     * 
     * @var array $routes Holds an array of routes.
     */
    public array $routes = [];
    
    /**
     * @inheritdoc
     *
     * @param  string  $method  Holds the HTTP method for the route.
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return Route Return the added Route instance.
     */
    public function addRoute( string $method, string $path, mixed $handler, ?string $name = null ) : Route
    {
        return $this->routes[] = new Route( $method, $path, $handler, $name );
    }

    /**
     * Get method.
     * 
     * Adds a `GET` route to the router.
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
     * Post method.
     * 
     * Adds a `POST` route to the router.
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
     * Put method.
     * 
     * Adds a `PUT` route to the router.
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
     * Delete method.
     * 
     * Adds a `DELETE` route to the router.
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
     * Patch method.
     * 
     * Adds a `PATCH` route to the router.
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
     * Options method.
     * 
     * Adds a `OPTIONS` route to the router.
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
     * Any method.
     * 
     * Adds a `ANY` route to the router.
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
     * @inheritdoc
     *
     * @return mixed  The result of the route handler.
     */
    abstract public function dispatch(): mixed;
}
