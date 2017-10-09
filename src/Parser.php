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

namespace Tomakee\Markdown;

use Tomakee\Markdown\Exceptions\InvalidParserException;
use Tomakee\Markdown\Exceptions\InvalidTagException;

class Parser
{
    /**
     * Markdown parser instance
     * @var object  (ex: \Michelf\MarkdownExtra)
     */
    protected $psr;

    /**
     * Markdown parser settings
     * @var string  loaded markdown parser config (config/markdown.php).
     */
    public $config = [];

    /**
     * Capturing flag
     * @var boolean  markdown lines capturing flag.
     */
    protected $capturing = false;


    /**
     * Constructor
     *
     * @param  object $parser  (ex: \Michelf\MarkdownExtra)
     * @throws InvalidParserException
     */
    public function __construct ($parser)
    {
        $config = array_column(config('markdown', []), null, 'id');

        if (! is_object($parser)) {
            throw new InvalidParserException("Markdown parser isn't an object.");
        }
        if (false === $key = array_search('\\'.get_class($parser), array_column($config, 'parser', 'id'))) {
            throw new InvalidParserException("This markdown parser isn't defined in config (@see config/markdown.php).");
        }

        $this->config = array_get($config, $key, []);
        $this->psr = $parser;
    }

    /**
     * Overwrite markdown parser settings
     *
     * @param  mixed  $key    markdown parser property name | array('property' => 'value').
     * @param  mixed  $value  property's value.
     * @return object \Tomakee\Markdown\Parser
     */
    public function setConfig ($key, $value = null)
    {
        if (empty($key)) {
            return $this;
        }
        if (is_string($key)) {
            $key = [$key => $value];
        }

        foreach ($key as $k => $v) {
            if (property_exists($this->psr, $k)) {
                $this->psr->$k = $v;
            }
        }

        return $this;
    }

    /**
     * Start capturing markdown string
     * Blade extended directive: @markdown.
     *
     * @return void
     */
    public function start ()
    {
        $this->capturing = true;
        ob_start();
    }

    /**
     * End capturing markdwon string
     * Blade extended directive: @endmarkdown.
     *
     * @throws InvalidTagException
     * @return string  parsed html strings.
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
     * Blade extended directive: @markdownFile().
     *
     * @param  string  $path  File path or blade view format: path.to.markdown. (File extension should be .md.blade.php)
     * @throws InvalidParserException
     * @return string  html strings
     */
    public function file ($path)
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
     * @return string  markdown file path.
     */
    protected function _finder ($path)
    {
        if (false !== strpos($path, '/')) {
            $path = str_replace('/', '.', $path);
        }

        return (new \Illuminate\View\FileViewFinder(
                app('Illuminate\Filesystem\Filesystem'),
                config('markdown.resources', [resource_path('views')]),
                config('markdown.extensions', ['md', 'md.blade.php', 'blade.php', 'php'])
        ))
        ->find($path);
    }

    /**
     * Call meothod
     *
     * @param  string $method  class method name.
     * @param  array  $args    method arguments.
     * @throws InvalidParserException
     * @return mixed  response class method, but if it isn't callable, return empty string.
     */
    public function __call ($method, array $args = [])
    {
        if (is_callable([$this->psr, $method], true)) {
            return $this->psr->$method(...$args);
        }

        throw new InvalidParserException("Markdown parser doesn't have called method: $method.");
    }
}
