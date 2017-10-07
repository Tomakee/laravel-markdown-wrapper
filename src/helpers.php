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
     * @see \Tomakee\Markdown\Parser::parse()
     *
     * @param  string  $markdown  markdown text strings.
     * @return string  parsed markdown to HTML strings.
     */
    function markdown ($markdown)
    {
        return app('markdown')->parse($markdown);
    }
}

if (! function_exists('markdown_file')) {
    /**
     * markdown_file
     * Helper function to parse markdown file to Html.
     *
     * @see \Tomakee\Markdown\Parser::parseWith()
     * @see \Tomakee\Markdown\Parser::parse()
     *
     * @param  string  $path  File path or blade view format: path.to.markdown.
     * @return string  parsed markdown to HTML strings.
     */
    function markdown_file ($path)
    {
        return app('markdown')->parseWith($path);
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

