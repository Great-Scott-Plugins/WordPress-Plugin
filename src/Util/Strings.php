<?php

/**
 * Copyright (c) 2022 Great Scott Plugins
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
 * @copyright   Copyright (c) 2022 GreatScottPlugins. All rights reserved.
 **/

namespace GreatScottPlugins\WordPressPlugin\Util;

/**
 * Class Strings
 * @package GreatScottPlugins\Util
 */
class Strings
{
    /**
     * Get snake_case of string.
     *
     * @param string $str Input string.
     *
     * @return string Snake case output string.
     */
    public static function toSnakeCase(string $str): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $str));
    }
}
