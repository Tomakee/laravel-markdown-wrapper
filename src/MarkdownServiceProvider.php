<?php
/**
 * Markdown Service Provider (for Laravel >= 5.4)
 *
 * @package   tomakee/laravel-markdown-wrapper
 * @link      https://github.com/tomakee/laravel-markdown-wrapper
 * @author    Tommy A. <tommy.azularvore@gmail.com>
 * @license   MIT
 */

namespace Tomakee\Markdown;

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
        $configPath  = __DIR__.'/../config/markdown.php';
        $publishPath = function_exists('config_path') ? config_path('markdown.php') : base_path('config/markdown.php');

        // publish config file (config/markdown.php)
        $this->publishes([$configPath => $publishPath], 'config');

        // @markdown
        Blade::directive('markdown', function ($markdown)
        {
            if (! empty($markdown)) {
                return "<?php echo markdown($markdown); ?>";
            }

            return "<?php app('markdown')->start() ?>";
        });

        // @markdownFile
        Blade::directive('markdownFile', function ($path)
        {
            return "<?php echo markdown_file($path); ?>";
        });

        // @endmarkdown
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
