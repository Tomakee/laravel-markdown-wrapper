# laravel-markdown-wrapper

[![Build Status](https://travis-ci.org/tomakee/laravel-markdown-wrapper.svg?branch=master)](https://travis-ci.org/tomakee/laravel-markdown-wrapper)

Simple Laravel wrapper class for markdown parser such as [michelf/php-markdown](https://github.com/michelf/php-markdown) or [cebe/markdown](https://github.com/cebe/markdown), etc.


## Environment
- PHP >= 5.6
- [Laravel](https://laravel.com/) >= 5.4
- Markdown Parser
    - [michelf/php-markdown](https://github.com/michelf/php-markdown)
    - [cebe/markdown](https://github.com/cebe/markdown)
    - etc.


## Functions
- Blade directives: `@markdown`, `@endmarkdown`, `@markdownFile`.
- Laravel helpers: `markdwon()`, `markdown_config()`, `markdown_file()`, `markdown_capture()`.
- Laravel facade: `Markdown::parse()`, `Markdown::setConfig()`, `Markdown::file()`, `Markdown::start()`, `Markdown::end()`
- Wrapper class main: `Tomakee\Markdown\Parser`


## Howto Install
After install Laravel, you can install by [php coomposer](https://getcomposer.org/) with follow command.

```bash
cd LARAVEL_PROJECT_DIR
composer require tomakee/laravel-markddown-wrapper
php artisan vendor:publish
```

And add Laravel Service Provider into config/app.php.

```php
//config/app.php
return [
    'providers' => [
        ...
        Tomakee\Markdown\MarkdownServiceProvider::class,
    ],
    ...
];
```

This package is just wrapper classes for Laravel, it won't parse markdown by itself, for that you need parser package.
Add your favorite Markdown Parser such as:

```bash
cd LARAVEL_PROJECT_DIR
composer require michelf/php-markdown
# OR
composer require cebe/markdown
```


## Blade Directives

#### single line
```
@markdown('some markdown text.')
```

#### multiple line
```
@markdown
some markdown text.
[link text](/link/path)
@endmarkdown
```

#### include markdwon file
```
@markdwonFile('path.to.markdownfile')  {{-- path format is same as Laravel view. --}}
```

You can set different resources path. But if your project always use different path from default, change the config setting (app/config/markdown.php).

```
@markdwonFile('path.to.markdownfile', [resources path]);
```



## Laravel Helpers

#### markdown()
```php
$html = markdwon('some markdwon text.');
```

#### markdown_config()
```php
//change parser config
$parser = markdwon_config('hard_wrap', false);
$html = $parser->parse('some markdown text.');

//change parser config and parse markdown
$html = markdwon_config(['hard_wrap' => false, 'code_class_prefix' => 'prefix-'])
        ->parse('some markdown text.');
```

#### markdown_file()
```php
//path format is same as Laravel view
$html = markdwon_file('path.to.markdownfile');  //path format is same as Laravel view.
```

You can set different resources path. But if your project always use different path from default, change the config setting (app/config/markdown.php).

```php
$html = markdwon_file('path.to.markdownfile', [resources path]);
```

#### markdown_capture()
```php
$html = markdwon_capture(function () {
    echo 'some markdown text.';
});
```


## Laravel Facade
```php
use Tomakee\Markdown\Facades\Markdown;

class ExampleClass
{
    //parse markdown text
    public function parse ()
    {
        return Markdown::parse('some markdown text.');
    }

    //parse markdwon file
    public function parseMarkdwonFile ()
    {
        return Markdown::file('path.to.markdownfile');  //path format is same as Laravel view.

        //You can set different resources path.
        //But if your project always use different path from default,
        //change the config setting (app/config/markdown.php).

        return Markdown::file('path.to.markdownfile', [resources path]);
    }

    //change parser config
    public function parseWithConfig ()
    {
        return Markdown::setConfig('hard_wrap', false)
            ->parse('some markdown text.');
    }

    //temporary change parser config
    public function parseWithConfigTemporary ()
    {
        $html = Markdown::setConfig('hard_wrap', false)->parse('some markdown text.');
        Markdown::setConfig('hard_wrap', true);
        return $html;
    }

    //direct access to parser method (except parsing single or multiple line)
    public function parserMethod ()
    {
        return Markdown::PARSER_METHOD();
    }
}
```


## Howto access markdown parser from binded instance
It's possible to access parser instance from binded application container.
See this php file: src/MarkdownServiceProvider.php.

```
Tomakee\Markdown\MarkdownServiceProvider::register()
```

```php
class ExampleClass
{
    public function getParserInstance ()
    {
        return $parserInstance = app('markdwon');
        // OR
        return $parserInstance = app('Tomakee\Markdown\Parser');
    }

    //parse markdown text
    public function parse ()
    {
        return app('markdwon')->parse('some markdown text.');
    }

    //parse markdwon file
    public function parseMarkdwonFile ()
    {
        return app('markdwon')->file('path.to.markdownfile');  //path format is same as Laravel view.
        //You can set different resources path.
        //But if your project always use different path from default,
        //change the config setting (app/config/markdown.php).

        return app('markdwon')->file('path.to.markdownfile', [resources path]);
    }

    //change parser config
    public function parseWithConfig ()
    {
        return app('markdwon')->setConfig('hard_wrap', false)
            ->parse('some markdown text.');
    }

    //temporary change parser config
    public function parseWithConfigTemporary ()
    {
      $html = app('markdwon')->setConfig('hard_wrap', false)->parse('some markdown text.');
      app('markdwon')->setConfig('hard_wrap', true);
      return $html;
    }

    //direct access to parser method (except parsing single or multiple line)
    public function parserMethod ()
    {
        return app('markdwon')->PARSER_METHOD();
    }
}
```




















