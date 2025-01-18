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
 * Class Plugin
 * @package GreatScottPlugins
 */
abstract class Plugin extends Singleton
{
    use Hookable;

    /**
     * Load plugin.
     */
    public static function load()
    {
        self::instance();
    }

    /**
     * Gets the plugin directory path.
     *
     * @return string Plugin directory path.
     */
    public static function dir($path = ''): string
    {
        if (false === function_exists('\plugin_dir_path')) {
            return $path;
        }

        return \plugin_dir_path(self::file()) . $path;
    }

    /**
     * Gets the plugin file path.
     *
     * @return string Plugin file path.
     */
    public static function file(): string
    {
        try {
            $reflector = new \ReflectionClass(get_called_class());

            $directory = dirname($reflector->getFileName(), 2);
            $filename = basename($directory) . '.php';

            return $directory . DIRECTORY_SEPARATOR . $filename;
        } catch (\Exception $e) {
            return __FILE__;
        }
    }

    /**
     * Gets the plugin url.
     *
     * @return string Plugin url.
     */
    public static function url($path = ''): string
    {
        if (false === function_exists('\plugin_dir_url')) {
            return $path;
        }

        return \trailingslashit(\plugin_dir_url(self::file())) . $path;
    }
}
