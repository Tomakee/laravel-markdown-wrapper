<?php
/**
 * Markdown helpers (for Laravel >= 5.4)
 *
 * @package   tomakee/laravel-markdown-wrapper
 * @link      https://github.com/tomakee/laravel-markdown-wrapper
 * @author    Tommy A. <tommy.azularvore@gmail.com>
 * @license   MIT
 */

if (! function_exists('markdown')) {
    /**
     * markdown
     * Helper function to parse markdown strings to Html.
     *
     * @see \Tomakee\Markdown\Parser::__call()
     *
     * @param  string  $markdown  markdown text strings.
     * @return string  parsed markdown to HTML strings.
     */
    function markdown ($markdown)
    {
        return app('markdown')->parse($markdown);
    }
}

if (! function_exists('markdown_config')) {
    /**
     * markdown_config
     * Helper function to configure markdown parser settings.
     *
     * @see \Tomakee\Markdown\Parser::set()
     *
     * @param  mixed  $key    markdown parser property name | array('property' => 'value').
     * @param  mixed  $value  property's value.
     * @return object \Tomakee\Markdown\Parser
     */
    function markdown_config ($key, $value = null)
    {
        return app('markdown')->setConfig($key, $value);
    }
}

if (! function_exists('markdown_file')) {
    /**
     * markdown_file
     * Helper function to parse markdown file to Html.
     *
     * @see \Tomakee\Markdown\Parser::file()
     * @see \Tomakee\Markdown\Parser::__call()
     *
     * @param  string  $path       File path or blade view format: path.to.markdown.
     * @param  array   $resources  View resources path.
     * @return string  parsed markdown to HTML strings.
     */
    function markdown_file ($path, array $resources = [])
    {
        return app('markdown')->file($path, $resources);
    }
}

if (! function_exists('markdown_capture')) {
    /**
     * markdown_capture
     * Helper function to return Html strings from markdown.
     *
     * @see \Tomakee\Markdown\Parser::start()
     * @see \Tomakee\Markdown\Parser::end()
     *
     * @param  Closure  $callback
     * @return string   parsed markdown to HTML strings.
     */
    function markdown_capture (Closure $callback)
    {
        $parser = app('markdown');
        $parser->start();
        $callback();
        return $parser->end();
    }
}

