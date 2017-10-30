<?php
/**
 * Markdown Facade (for Laravel >= 5.4)
 *
 * @package   tomakee/laravel-markdown-wrapper
 * @link      https://github.com/tomakee/laravel-markdown-wrapper
 * @author    Tommy A. <tommy.azularvore@gmail.com>
 * @license   MIT
 */

namespace Tomakee\Markdown\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Markdown extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor ()
    {
        return 'markdown';
    }
}
