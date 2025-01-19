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
 * ActionDecoratorHooks Trait
 *
 * @package GreatScottPlugins
 */
trait ActionDecoratorHooks
{
    /**
     * Add doc hooks.
     */
    public function addDocHooks()
    {
        // Get instanced class to relate the callback to.
        $object = static::$instances[static::class];

        // Start a reflector.
        $reflector = new \ReflectionObject($object);

        foreach ($reflector->getMethods() as $method) {
            $phpdoc    = $method->getDocComment();
            $arg_count = $method->getNumberOfParameters();

            // Handle hooks.
            if (false !== preg_match_all(
                    '#\* @(?P<type>filter|action|shortcode)\s+(?P<name>[a-z0-9\/\=\-\._]+)(?:,\s+(?P<priority>\d+))?#',
                    $phpdoc,
                    $matches,
                    PREG_SET_ORDER
                )) {
                foreach ($matches as $match) {
                    $type     = $match['type'];
                    $name     = $match['name'];
                    $priority = empty($match['priority']) ? 11 : intval($match['priority']);
                    $callback = [$this, $method->getName()];
                    call_user_func(
                        [
                            self::class,
                            'add' . ucfirst($type),
                        ],
                        $name,
                        $callback,
                        compact('priority', 'arg_count')
                    );
                }
            }

            // Handle CLI commands.
            if (false !== preg_match_all(
                    '#\* @(?P<type>command)\s+(?P<name>[a-z0-9\/\=\-\._\: ]+)?#',
                    $phpdoc,
                    $matches,
                    PREG_SET_ORDER
                )) {
                foreach ($matches as $match) {
                    $type     = $match['type'];
                    $name     = $match['name'];
                    $callback = [$this, $method->getName()];
                    call_user_func(["\\WP_CLI", 'add_' . $type], $name, $callback);
                }
            }

            // Ajax handler.
            if (false !== preg_match_all(
                    '#\* @(?P<type>ajax)\s+?(?P<name>[a-z0-9\/\=\-\._]+)?#',
                    $phpdoc,
                    $matches,
                    PREG_SET_ORDER
                )) {
                foreach ($matches as $match) {
                    $name     = $match['name'] ?? StringUtil::toSnakeCase($method->getName());
                    $priority = empty($match['priority']) ? 11 : intval($match['priority']);
                    $callback = [$this, $method->getName()];

                    foreach (['wp_ajax', 'wp_ajax_nopriv'] as $ajax_hook) {
                        call_user_func(
                            [
                                self::class,
                                'addAction',
                            ],
                            sprintf('%s_%s', $ajax_hook, $name),
                            $callback,
                            compact('priority', 'arg_count')
                        );
                    }
                }
            }
        }
    }

    /**
     * Hooks a function on to a specific action.
     *
     * @param string $name    The hook name.
     * @param array $callback The class object and method.
     * @param array $args     An array with priority and arg_count.
     *
     * @return mixed
     */
    public function addAction(
        $name,
        $callback,
        $args = []
    ) {
        // Merge defaults.
        $args = array_merge(
            [
                'priority'  => 10,
                'arg_count' => PHP_INT_MAX,
            ],
            $args
        );

        return $this->addHook('action', $name, $callback, $args);
    }

    /**
     * Hooks a function on to a specific filter.
     *
     * @param string $name    The hook name.
     * @param array $callback The class object and method.
     * @param array $args     An array with priority and arg_count.
     *
     * @return mixed
     */
    public function addFilter(
        $name,
        $callback,
        $args = []
    ) {
        // Merge defaults.
        $args = array_merge(
            [
                'priority'  => 10,
                'arg_count' => PHP_INT_MAX,
            ],
            $args
        );

        return $this->addHook('filter', $name, $callback, $args);
    }

    /**
     * Hooks a function on to a specific shortcode.
     *
     * @param string $name    The shortcode name.
     * @param array $callback The class object and method.
     *
     * @return mixed
     */
    public function addShortcode(
        $name,
        $callback
    ) {
        return $this->addHook('shortcode', $name, $callback);
    }

    /**
     * Hooks a function on to a specific action/filter.
     *
     * @param string $type    The hook type. Options are action/filter.
     * @param string $name    The hook name.
     * @param array $callback The class object and method.
     * @param array $args     An array with priority and arg_count.
     *
     * @return mixed
     */
    protected function addHook(
        $type,
        $name,
        $callback,
        $args = []
    ) {
        $priority  = $args['priority'] ?? 10;
        $arg_count = $args['arg_count'] ?? PHP_INT_MAX;
        $fn        = sprintf('\add_%s', $type);

        return \call_user_func($fn, $name, $callback, $priority, $arg_count);
    }
}
