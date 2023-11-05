<?php
/**
 * Part of Banco Omega CMS -  Routing Package
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
 * @category    Omega
 * @package     Omega\Routing
 * @link        https://omegacms.github.com
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.com)
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
     * Error handler method.
     *
     * @param  int      $code    Holds the error code.
     * @param  callable $handler Holds the callable handler.
     * @return void
     */
    public function errorHandler( int $code, callable $handler ) : void
    {
        $this->errorHandlers[ $code ] = $handler;
    }


    /**
     * Dispatch the route.
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
     * Get paths.
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
     * Get current route.
     *
     * @return ?Route Return an instance of Route or null.
     */
    public function current() : ?Route
    {
        return $this->current;
    }

    /**
     * Route match.
     *
     * @param  string $method Holds the method.
     * @param  string $path   Holds the path.
     * @return ?Route Return an instance of Route or null.
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
     * Dispatch not allowed method.
     *
     * @return mixed
     */
    public function dispatchNotAllowed() : mixed
    {
        $this->errorHandlers[ 400 ] ??= fn() => 'not allowed';

        return $this->errorHandlers[ 400 ]();
    }

    /**
     * Dispatch not found method.
     *
     * @return mixed
     */
    public function dispatchNotFound() : mixed
    {
        $this->errorHandlers[ 404 ] ??= fn() => 'not found';

        return $this->errorHandlers[ 404 ]();
    }

    /**
     * Dispatch error method.
     *
     * @return mixed
     */
    public function dispatchError() : mixed
    {
        $this->errorHandlers[ 500 ] ??= fn() => 'server error';

        return $this->errorHandlers[ 500 ]();
    }

    /**
     * Redirect method.
     *
     * @param  string $path Holds the redirect path.
     * @return void
     */
    public function redirect( string $path ) : void
    {
        header( "Location: {$path}", true, 301 );

        exit;
    }

    /**
     * Route method.
     *
     * @param  string $name       Holds the route name.
     * @param  array  $parameters Holds an array of parameters.
     * @return string Return the route string.
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
