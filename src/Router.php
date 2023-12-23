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
 * @use
 */
use function array_push;
use function header;
use function preg_replace;
use function str_replace;
use Exception;
use Throwable;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Omega\Helpers\Alias;

/**
 * Router class.
 *
 * The `Router` class is responsible for managing routes and dispatching requests to the appropriate handlers.
 *
 *
 * @category    Omega
 * @package     Omega\Routing
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class Router extends AbstractRouter
{
        /**
     * Routes array.
     *
     * @var array $routes Holds an array of Routes.
     */
    protected array $routes = [];

    /**
     * Error handler.
     *
     * @var array $errorHandlers Holds an array of error handler.
     */
    protected array $errorHandlers = [];

    /**
     * Current route.
     *
     * @var Route $current Holds an instance of Route.
     */
    protected Route $current;

    /**
     * Adds an error handler for a specific HTTP error code.
     *
     * @param  int      $code    The HTTP error code.
     * @param  callable $handler The error handler.
     * @return void
     */
    public function errorHandler( int $code, callable $handler ) : void
    {
        $this->errorHandlers[ $code ] = $handler;
    }


    /**
     * @inheritdoc
     *
     * @return mixed
     * @throws Throwable
     */
    public function dispatch() : mixed
    {
        $paths         = $this->paths();
        $requestMethod = $_SERVER[ 'REQUEST_METHOD' ] ?? 'GET';
        $requestPath   = $_SERVER[ 'REQUEST_URI'    ] ?? '/';
        $matching      = $this->match( $requestMethod, $requestPath );

        if ( $matching ) {
            $this->current = $matching;

            try {
                return $matching->dispatch();
            } catch ( Throwable $e ) {
                if ( $handler = Alias::config( 'handlers.exceptions' ) ) {
                    $instance = new $handler();
                    if ( $result = $instance->showThrowable( $e ) ) {
                        return $result;
                    }
                }

                return $this->dispatchError();
            }
        }

        if ( in_array( $requestPath, $paths ) ) {
            return $this->dispatchNotAllowed();
        }

        return $this->dispatchNotFound();
    }

    /**
     * Gets an array of paths registered in the router.
     *
     * @return array Return an array of paths.
     */
    private function paths() : array
    {
        $paths = [];

        foreach ( $this->routes as $route ) {
            $paths[] = $route->path();
        }

        return $paths;
    }

   /**
     * Gets the current route being dispatched.
     *
     * @return ?Route Return an instance of Route or null if no route is matched.
     */
    public function current() : ?Route
    {
        return $this->current;
    }

    /**
     * Matches a route based on the HTTP method and path.
     *
     * @param  string $method The HTTP method.
     * @param  string $path   The path to match.
     * @return ?Route Return an instance of Route or null if no route is matched.
     */
    private function match( string $method, string $path ) : ?Route
    {
        foreach ( $this->routes as $route ) {
            if ( $route->matches( $method, $path ) ) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Dispatch a not allowed response.
     *
     * @return mixed
     */
    public function dispatchNotAllowed() : mixed
    {
        $this->errorHandlers[ 400 ] ??= fn() => 'not allowed';

        return $this->errorHandlers[ 400 ]();
    }

    /**
     * Dispatch a not found response.
     *
     * @return mixed
     */
    public function dispatchNotFound() : mixed
    {
        $this->errorHandlers[ 404 ] ??= fn() => 'not found';

        return $this->errorHandlers[ 404 ]();
    }

    /**
     * Dispatch a server error response.
     *
     * @return mixed
     */
    public function dispatchError() : mixed
    {
        $this->errorHandlers[ 500 ] ??= fn() => 'server error';

        return $this->errorHandlers[ 500 ]();
    }

    /**
     * Redirects the request to a new path.
     *
     * @param  string $path The path to redirect to.
     * @return void
     */
    public function redirect( string $path ) : void
    {
        header( "Location: {$path}", true, 301 );

        exit;
    }

    /**
     * Generates a URL for a named route.
     *
     * @param  string $name       The name of the route.
     * @param  array  $parameters An array of parameters to replace in the route path.
     * @return string The generated route URL.
     * @throws Exception
     */
    public function route( string $name, array $parameters = [] ) : string
    {
        foreach ( $this->routes as $route ) {
            if ( $route->name() === $name ) {
                $finds = [];
                $replaces = [];

                foreach ( $parameters as $key => $value ) {
                    array_push( $finds, "{{$key}}" );
                    array_push( $replaces, $value );
                    array_push( $finds, "{{$key}?}" );
                    array_push( $replaces, $value );
                }

                $path = $route->path();
                $path = str_replace( $finds, $replaces, $path );
                $path = preg_replace( '#{[^}]+}#', '', $path );

                return $path;
            }
        }

        throw new Exception(
            'No route with that name.'
        );
    }
}
