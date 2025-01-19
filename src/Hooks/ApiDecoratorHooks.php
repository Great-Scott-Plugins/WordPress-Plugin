<?php
/**
 * Copyright (c) 2025 Great Scott Plugins
 *
 * GNU General Public License, Free Software Foundation <https://www.gnu.org/licenses/gpl-3.0.html>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package     GreatScottPlugins
 * @category    Core
 * @author      GreatScottPlugins
 * @copyright   Copyright (c) 2025 GreatScottPlugins. All rights reserved.
 **/

namespace GreatScottPlugins\WordPressPlugin\Hooks;

/**
 * ApiDecoratorHooks Trait
 *
 * @package GreatScottPlugins
 */
trait ApiDecoratorHooks
{
    /**
     * @var string $namespace The API namespace.
     */
    private static string $namespace = '';

    /**
     * @var array $routes The API routes
     */
    private static array $routes = [];

    /**
     * @var string $subspace The optional API subspace.
     */
    private static string $subspace = '';

    /**
     * @var string $version The API version.
     */
    private static string $version = '1';

    /**
     * Register REST API endpoints.
     *
     * @action rest_api_init
     */
    public static function registerEndpoints()
    {
        // Register each of the defined routes.
        foreach (self::getApiRoutes() as $base => $methods) {
            if (true === empty(self::getApiNamespace()) || true === empty(self::getApiVersion())) {
                continue;
            }

            if (false === empty(self::getApiSubspace())) {
                $route = sprintf('%s/v%s/%s', self::getApiNamespace(), self::getApiVersion(), self::getApiSubspace());
            } else {
                $route = sprintf('%s/v%s', self::getApiNamespace(), self::getApiVersion());
            }

            \register_rest_route(
                $route,
                $base,
                $methods
            );
        }
    }

    /**
     * Get API routes.
     *
     * @return array The API routes.
     */
    public static function getApiRoutes(): array
    {
        return self::$routes;
    }

    /**
     * Get API namespace.
     *
     * @return string The API namespace.
     */
    protected static function getApiNamespace(): string
    {
        return self::$namespace;
    }

    /**
     * Get API version.
     *
     * @return string The API version.
     */
    protected static function getApiVersion(): string
    {
        return self::$version;
    }

    /**
     * Get API subspace.
     *
     * @return string The API subspace.
     */
    protected static function getApiSubspace(): string
    {
        return self::$subspace;
    }

    /**
     * Set API routes.
     *
     * @param array $routes Array of routes.
     * @return void
     */
    public static function setApiRoutes(array $routes)
    {
        self::$routes = $routes;
    }

    /**
     * Set API version.
     *
     * @param string $version The API version.
     * @return void
     */
    protected static function setApiVersion(string $version)
    {
        self::$version = $version;
    }

    /**
     * Set API namespace.
     *
     * @param string $namespace The API namespace.
     * @return void
     */
    protected static function setApiNamespace(string $namespace)
    {
        self::$namespace = $namespace;
    }

    /**
     * Set API subspace.
     *
     * @param string $subspace The API subspace.
     * @return void
     */
    protected static function setApiSubspace(string $subspace)
    {
        self::$subspace = $subspace;
    }

    /**
     * Add doc hooks function.
     *
     * @action init
     */
    public function addApiHooks()
    {
        // Get instanced class to relate the callback to.
        $object = static::$instances[static::class];

        // Start a reflector.
        $reflector = new \ReflectionObject($object);

        if (false !== preg_match_all(
                '#\* @api-(?P<api_key>\S+)\s+(?P<api_value>.+)#',
                $reflector->getDocComment(),
                $matches,
                PREG_SET_ORDER
            )) {
            // Iterate over found @filter|action|shortcode tags.
            foreach ($matches as $match) {
                // Parse comment block data.
                $api_key   = $match['api_key'];
                $api_value = $match['api_value'];

                $method = 'setApi' . ucfirst($api_key);

                if (true === method_exists(self::class, $method)) {
                    call_user_func(
                        [self::class, $method],
                        $api_value
                    );
                }
            }
        }
    }
}