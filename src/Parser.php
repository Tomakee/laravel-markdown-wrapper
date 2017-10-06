<?php
/**
 * Markdown Parser (for Laravel >= 5.4)
 *
 * Simple Laravel wrapper class for markdown parser
 * such as like cebe/markdown (https://github.com/cebe/markdown).
 *
 * @package   tomakee/laravel-markdown-wrapper
 * @link      https://github.com/tomakee/laravel-markdown-wrapper
 * @author    Tommy A. <tommy.azularvore@gmail.com>
 * @license   MIT
 */

namespace Tomakee\Markdwon;

use Tomakee\Markdwon\Exceptions\InvalidParserException;
use Tomakee\Markdwon\Exceptions\InvalidTagException;

class Parser
{
    /**
     * Markdown Paser
     * @var object  (exp: \Michelf\MarkdownExtra)
     */
    protected $psr;

    /**
     * Markdown Parser Settings
     * @var string  markdown parser config array (config/markdown.php).
     */
    public $config = [];

    /**
     * Markdown string
     * @var string  markdown text string.
     */
    public $markdown = '';

    /**
     * Capturing flag
     * @var boolean  markdown lines capturing flag.
     */
    protected $capturing = false;


    /**
     * Constructor
     *
     * @param object $parser  (exp: \Michelf\MarkdownExtra)
     */
    public function __construct ($parser)
    {
        $config = array_column(config('markdown', []), null, 'id');

        if (! is_object($parser)) {
            throw new InvalidParserException("Markdown parser isn't an object.");
        }
        if (false === $key = array_search('\\'.get_class($parser), array_column($config, 'parser', 'id'))) {
            throw new InvalidParserException("This markdown parser isn't defined in the config (config/markdown.php).");
        }

        $this->config = array_get($config, $key, []);
        $this->psr = $parser;
    }

    /**
     * Blade Start
     * Blade extended directive: @markdown.
     */
    public function start ()
    {
        $this->capturing = true;
        ob_start();
    }

    /**
     * Blade end
     * Blade extended directive: @endmarkdown.
     */
    public function end ()
    {
        if ($this->capturing === false) {
            throw new InvalidTagException("Markdown capturing should be started before calling this method.");
        }

        $this->capturing = false;
        $markdown = ob_get_clean();

        // parse method
        $method = false !== strpos($markdown, "\n")
            ? array_get($this->config, 'methods.multi', 'transform') : array_get($this->config, 'methods.single', 'transform');

        return $this->$method($markdown);
    }

    /**
     * Parse markdown file
     *
     * @param  string  $path  File path or blade view format: path.to.markdown. (File extension should be .md.blade.php)
     * @throws InvalidTagException
     * @return string  html strings
     */
    public function parseWith ($path)
    {
        if (false === strpos($path, '/') || strpos($path, '/') > 0) {
            $path = $this->_finder($path);
        }
        if (! file_exists($path) || ! is_readable($path)) {
            throw new InvalidParserException("Markdown file dosen't exist.");
        }

        // multiple line parse method
        $method = array_get($this->config, 'methods.multi', 'transform');

        return $this->$method(file_get_contents($path));
    }

    /**
     * Find markdown file path
     *
     * @param  string  $path  laravel view dotted format: path.to.markdown.
     * @return string
     */
    protected function _finder ($path)
    {
        if (false !== strpos($path, '/')) {
            $path = str_replace('/', '.', $path);
        }

        $finder = new \Illuminate\View\FileViewFinder(
                app('Illuminate\Filesystem\Filesystem'),
                config('markdown.resources', [resource_path('views')]),
                config('markdown.extensions', ['md', 'md.blade.php', 'blade.php', 'php'])
        );

        return $finder->find($path);
    }

    /**
     * Call meothod
     *
     * @param  string $method  class method name.
     * @param  array  $args    method arguments.
     * @return mixed  response class method, but if it isn't callable, return empty string.
     */
    public function __call ($method, array $args = [])
    {
        if (is_callable([$this->psr, $method], true)) {
            return $this->psr->$method(...$args);
        }

        return '';
    }
}
