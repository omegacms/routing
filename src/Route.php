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
use function array_combine;
use function array_fill;
use function array_push;
use function count;
use function preg_match_all;
use function preg_replace;
use function preg_replace_callback;
use function rtrim;
use function str_contains;
use function str_ends_with;
use function trim;
use function Omega\Helpers\app;

/**
 * Route class.
 *
 * The `Route` class represents an individual route in the routing system.
 *
 * @category    Omega
 * @package     Omega\Routing
 * @link        https://omegacms.github.io
 * @author      Adriano Giovannini <omegacms@outlook.com>
 * @copyright   Copyright (c) 2022 Adriano Giovannini. (https://omegacms.github.io)
 * @license     https://www.gnu.org/licenses/gpl-3.0-standalone.html     GPL V3.0+
 * @version     1.0.0
 */
class Route
{
    /**
     * HTTP method associated with the route.
     *
     * @var string $method Holds the route method.
     */
    protected string $method;

    /**
     * Path pattern for the route.
     *
     * @var string $path Holds the route path.
     */
    protected string $path;

    /**
     * Handler of the route.
     *
     * @var mixed $handler Holds the handler.
     */
    protected mixed $handler;

    /**
     * Array of route parameters.
     *
     * @var array $parameters Holds an array of route parameters.
     */
    protected array $parameters = [];

    /**
     * Name of the route.
     *
     * @var ?string $name Holds route name or null if not named.
     */
    protected ?string $name = null;

    /**
     * Route class constructor.
     *
     * @param  string  $method  Holds the HTTP method associated with the route.
     * @param  string  $path    Holds the path pattern for the route.
     * @param  mixed   $handler Holds the handler for the route.
     * @param  ?string $name    Holds the route name or null.
     * @return void
     */
    public function __construct(string $method, string $path, mixed $handler, ?string $name = null )
    {
        $this->method  = $method;
        $this->path    = $path;
        $this->handler = $handler;
        $this->name    = $name;
    }

    /**
     * Get the HTTP method associated with the route.
     *
     * @return string Returns the route method.
     */
    public function method() : string
    {
        return $this->method;
    }

    /**
     * Get the path pattern for the route.
     *
     * @return string Returns the route path.
     */
    public function path() : string
    {
        return $this->path;
    }

    /**
     * Get the parameters associated with the route.
     *
     * @return array Returns an array of route parameters.
     */
    public function parameters() : array
    {
        return $this->parameters;
    }

    /**
     * Get or set the name of the route.
     *
     * @param  ?string $name Holds the route name.
     * @return $this|string|null
     */
    public function name( string $name = null ) : static|string|null
    {
        if ( $name ) {
            $this->name = $name;
            return $this;
        }

        return $this->name;
    }

    /**
     * Checks if the route matches a given HTTP method and path.
     *
     * @param  string $method Holds the HTTP method to match.
     * @param  string $path   Holds the path to match.
     * @return bool Returns true if the route matches, false otherwise.
     */
    public function matches(string $method, string $path): bool
    {
        if (
            $this->method === $method
            && $this->path === $path
        ) {
            return true;
        }

        $parameterNames = [];

        $pattern = $this->normalisePath( $this->path );

        $pattern = preg_replace_callback( '#{([^}]+)}/#', function ( array $found ) use ( &$parameterNames ) {
            array_push( $parameterNames, rtrim( $found[ 1 ], '?' ) );

            if ( str_ends_with( $found[ 1 ], '?' ) ) {
                return '([^/]*)(?:/?)';
            }

            return '([^/]+)/';
        }, $pattern );

        if (
            ! str_contains( $pattern, '+' )
            && ! str_contains( $pattern, '*' )
        ) {
            return false;
        }

        preg_match_all( "#{$pattern}#", $this->normalisePath( $path ), $matches );

        $parameterValues = [];

        if ( count( $matches[ 1 ] ) > 0 ) {
            foreach ( $matches[ 1 ] as $value ) {
                if ( $value ) {
                    array_push( $parameterValues, $value );
                    continue;
                }

                array_push( $parameterValues, null );
            }

            $emptyValues = array_fill( 0, count( $parameterNames ), false );
            $parameterValues += $emptyValues;

            $this->parameters = array_combine( $parameterNames, $parameterValues );

            return true;
        }

        return false;
    }

    /**
     * Normalizes the path by removing leading and trailing slashes.
     *
     * @param  string $path Holds the path to normalize.
     * @return string Returns the normalized path.
     */
    private function normalisePath( string $path ) : string
    {
        $path = trim( $path, '/' );
        $path = "/{$path}/";

        return preg_replace( '/[\/]{2,}/', '/', $path );
    }

    /**
     * Dispatches the route handler.
     *
     * This method invokes the registered handler for the route, typically a controller action.
     *
     * @return mixed The result of the route handler.
     */
    public function dispatch(): mixed
    {
        return app()->call($this->handler);
    }
}
