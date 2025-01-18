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

namespace GreatScottPlugins\WordPressPlugin;

/**
 * Class Singleton
 * @package GreatScottPlugins
 */
abstract class Singleton
{
    use Hookable;

    /**
     * @var self Reference to singleton instance.
     */
    protected static $instances = [];

    /**
     * Creates a new instance of a singleton class (via late static binding), accepting a variable-length argument list.
     *
     * @param mixed ...$params
     *
     * @return self
     */
    final public static function instance(...$params): Singleton
    {
        if (false === isset(static::$instances[static::class])) {
            static::$instances[static::class] = new static();

            // Call 'addDocHooks' to parse and fire object doc actions/filters.
            if (method_exists(self::$instances[static::class], 'addDocHooks')) {
                call_user_func_array([self::$instances[static::class], 'addDocHooks'], []);
            }

            // Call 'init' bootstrap method if it's defined in the inheriting class.
            if (method_exists(self::$instances[static::class], 'init')) {
                call_user_func_array([self::$instances[static::class], 'init'], func_get_args());
            }
        }

        return static::$instances[static::class];
    }

    /**
     * Prevents direct instantiation.
     *
     * @return void
     */
    final private function __construct()
    {
    }

    /**
     * Prevents cloning the singleton instance.
     *
     * @return void
     */
    final public function __clone()
    {
    }

    /**
     * Prevents unserializing the singleton instance.
     *
     * @return void
     */
    final public function __wakeup()
    {
    }
}
