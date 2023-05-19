<?php
/**
 * Copyright (c) 2023 Great Scott Plugins
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
 * @copyright   Copyright (c) 2023 GreatScottPlugins. All rights reserved.
 **/

namespace GreatScottPlugins\WordPressPlugin;

/**
 * Class View
 * @package GreatScottPlugins
 */
class View
{
    /**
     * The data of the view.
     *
     * @var array
     */
    protected $data;

    /**
     * The path of the template of the view.
     *
     * @var string
     */
    protected $path;

    /**
     * View constructor.
     *
     * @param string $path The path of the template.
     * @param array $data  The data for the template.
     */
    public function __construct(string $path, array $data = [])
    {
        $this->path = $path;
        $this->data = $data;
    }

    /**
     * Include a template without rendering it.
     *
     * @param string $path The path of the template.
     * @param array $data  The data for the template.
     */
    public static function includeTemplate(string $path, array $data = [])
    {
        (new static($path, $data))->include();
    }

    /**
     * Return a rendered template.
     *
     * @param string $path The path of the template.
     * @param array $data  The data for the template.
     *
     * @return string Rendered template string.
     */
    public static function template(string $path, array $data = [])
    {
        return (new static($path, $data))->render();
    }

    /**
     * Include a template file.
     */
    public function include()
    {
        load_template(sprintf("%s.php", $this->path), false, $this->data);
    }

    /**
     * Render a template.
     * @return string Rendered template string.
     */
    public function render(): string
    {
        ob_start();
        $this->include();
        $render = ob_get_contents();
        ob_end_clean();

        return $render;
    }

    /**
     * Returns a rendered shortcode.
     *
     * @param string $shortcode The shortcode name.
     * @param array  $atts      The shortcode attributes.
     *
     * @return string Rendered shortcode.
     */
    public static function shortcode(string $shortcode, array $atts): string
    {
        ob_start();
        the_widget($shortcode, $atts);
        $widget = ob_get_contents();
        ob_end_clean();

        return $widget;
    }
}
