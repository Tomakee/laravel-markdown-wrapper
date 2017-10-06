<?php
/**
 * Markdown Service Provider (for Laravel >= 5.4)
 *
 * @package   tomakee/laravel-markdown-wrapper
 * @link      https://github.com/tomakee/laravel-markdown-wrapper
 * @author    Tommy A. <tommy.azularvore@gmail.com>
 * @license   MIT
 */

namespace Tomakee\Markdwon;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class MarkdownServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot ()
    {
        $this->publishes([
                __DIR__.'/../config/markdown.php' => config_path('markdown.php'),
        ]);

        Blade::directive('markdown', function ($markdown)
        {
            if (! empty($markdown)) {
                $config = app('markdown')->config;

                $method = false !== strpos($markdown, "\n")
                    ? array_get($config, 'methods.multi', 'transform') : array_get($config, 'methods.single', 'transform');

                return "<?php echo app('markdown')->$method($markdown); ?>";
            }

            return "<?php app('markdown')->start() ?>";
        });

        Blade::directive('endmarkdown', function ()
        {
            return "<?php echo app('markdown')->end() ?>";
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register ()
    {
        $this->app->singleton(Parser::class, function ($app)
        {
            $markdown = config('markdown', []);
            $default  = array_get($markdown, 'default', 'michelf-extra');

            $markdown = array_column($markdown, null, 'id');
            $parser   = array_get($markdown, "$default.parser", '\\Michelf\\MarkdownExtra');
            $config   = array_get($markdown, "$default.config", []);

            $psr = class_exists($parser) ? new $parser : new \stdClass();

            foreach ($config as $k => $v) {
                $psr->$k = $v;
            }

            return new Parser($psr);
        });

        $this->app->bind('markdown', Parser::class);
    }
}
