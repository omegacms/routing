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
use Omega\Helpers\App;

/**
 * Route class
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
     * Method.
     *
     * @var string $method Holds the method.
     */
    protected string $method;

    /**
     * Route path.
     *
     * @var string $path Holds the route path.
     */
    protected string $path;

    /**
     * Handler.
     *
     * @var mixed Holds the handler.
     */
    protected mixed $handler;

        /**
     * Parameters array.
     *
     * @var array $parameters Holds an array of parameters.
     */
    protected array $parameters = [];

    /**
     * Route name.
     *
     * @var ?string $name Holds route name or null.
     */
    protected ?string $name = null;

    /**
     * Route class constructor.
     *
     * @param  string  $method  Holds the method.
     * @param  string  $path    Holds the path.
     * @param  mixed   $handler Holds the handler.
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
     * Get route method.
     *
     * @return string Return the route method.
     */
    public function method() : string
    {
        return $this->method;
    }

    /**
     * Get route path.
     *
     * @return string Return the route path.
     */
    public function path() : string
    {
        return $this->path;
    }

    /**
     * Get route parameters.
     *
     * @return array Return an array of route parameters.
     */
    public function parameters() : array
    {
        return $this->parameters;
    }

    /**
     * Route name.
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
     * Normalise path.
     *
     * @param  string $path Holds the path.
     * @return string Return the normalised path.
     */
    private function normalisePath( string $path ) : string
    {
        $path = trim( $path, '/' );
        $path = "/{$path}/";

        return preg_replace( '/[\/]{2,}/', '/', $path );
    }


    public function dispatch(): mixed
    {
        return App::application()->call($this->handler);
    }
}