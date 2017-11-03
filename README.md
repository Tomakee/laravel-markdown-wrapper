# laravel-markdown-wrapper

[![Build Status](https://travis-ci.org/tomakee/laravel-markdown-wrapper.svg?branch=master)](https://travis-ci.org/tomakee/laravel-markdown-wrapper)
[![Latest Stable Version](https://poser.pugx.org/tomakee/laravel-markdown-wrapper/v/stable)](https://packagist.org/packages/tomakee/laravel-markdown-wrapper)
[![License](https://poser.pugx.org/tomakee/laravel-markdown-wrapper/license)](https://packagist.org/packages/tomakee/laravel-markdown-wrapper)

[日本語ドキュメント](https://github.com/tomakee/laravel-markdown-wrapper/blob/master/README.ja.md)


Simple Laravel wrapper class for markdown parser such as [michelf/php-markdown](https://github.com/michelf/php-markdown) or [cebe/markdown](https://github.com/cebe/markdown), etc.

This package is just wrapper classes for Laravel, it won't parse markdown by itself, for that you need a actual parser.
Add your favorite Markdown Parser.


## Environment
- PHP >= 5.6
- [Laravel](https://laravel.com/) >= 5.4
- Markdown Parser
    - [michelf/php-markdown](https://github.com/michelf/php-markdown)
    - [cebe/markdown](https://github.com/cebe/markdown)
    - etc.


## Functions
- Blade directives: `@markdown`, `@endmarkdown`, `@markdownFile`.
- Laravel helpers: `markdown()`, `markdown_config()`, `markdown_file()`, `markdown_capture()`.
- Laravel facade: `Markdown::parse()`, `Markdown::setConfig()`, `Markdown::file()`, `Markdown::start()`, `Markdown::end()`
- Wrapper class main: `Tomakee\Markdown\Parser`


## Howto Install PHP Composer
You need to install [php coomposer](https://getcomposer.org/) first
if you don't have it in your system.

```bash
#install composer (Linux or MacOS)
curl -sS https://getcomposer.org/installer | php

#move composer.phar into somewhere accessable (such as /usr/local/bin)
mv composer.phar /usr/local/bin/composer
chmod 755 /usr/local/bin/composer
```


## Howto Create Laravel Project
After install php composer, you need to create a [Laravel](https://laravel.com/) project.<br>
You can skip this step if you simply add it in your project.

```bash
composer create-project --prefer-dist laravel/laravel LARAVEL_PROJECT_DIR

#or with version
composer create-project --prefer-dist laravel/laravel "5.4.*" LARAVEL_PROJECT_DIR
```


## Howto Install
After install [Laravel](https://laravel.com/), you can install this package with follow commands.

```bash
cd LARAVEL_PROJECT_DIR
composer require tomakee/laravel-markddown-wrapper
php artisan vendor:publish
```

Add Laravel Service Provider into config/app.php.

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

And add your favorite Markdown Parser such as:

```bash
cd LARAVEL_PROJECT_DIR
composer require michelf/php-markdown
#or
composer require cebe/markdown
```


## Blade Directives
You can create view mixed markdown and Blade.<br>
For example:

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

#### include markdown file

```
@markdownFile('path.to.markdownfile')  {{-- path format is same as Laravel view. --}}

{{--
You can set different resources path. 
But if your project always use different path from default, 
change the config setting (app/config/markdown.php).
--}}

@markdownFile('path.to.markdownfile', [resources path,,,]);
```



## Laravel Helpers
Anywhere Controller, etc., you can access to the wrapper class.

#### markdown()

```php
//parse markdown text
$html = markdown('some markdown text.');
```

#### markdown_config()

```php
//change parser config
$parser = markdown_config('hard_wrap', false);
$html = $parser->parse('some markdown text.');

//change parser config and parse markdown
$html = markdown_config(['hard_wrap' => false, 'code_class_prefix' => 'prefix-'])
        ->parse('some markdown text.');
```

#### markdown_file()

```php
//parse markdown file
$html = markdown_file('path.to.markdownfile');  //path format is same as Laravel view.

//You can set different resources path.
//But if your project always use different path from default,
//change the config setting (app/config/markdown.php).

$html = markdown_file('path.to.markdownfile', [resources path,,,,]);
```

#### markdown_capture()

```php
$html = markdown_capture(function () {
    echo 'some markdown text.';
});

//with params
$html = markdown_capture(function () use ($args1, $args2) {
    echo $args1 . $args2 . 'some markdown text.';
});
```


## Laravel Facade
In your Controller, etc., you can access to the wrapper class with Laravel Facade.

#### Import markdown facade
To use Laravel Facade, first, you need import Markdown Facade.<br>
Class path:

```php
use Tomakee\Markdown\Facades\Markdown;
```

#### Markdown::parse()

```php
//parse markdown text
$html = Markdown::parse('some markdown text.');
```

#### Markdown::file()

```php
//parse markdown file
$html = Markdown::file('path.to.markdownfile');  //path format is same as Laravel view.

//You can set different resources path.
//But if your project always use different path from default,
//change the config setting (app/config/markdown.php).

$html = Markdown::file('path.to.markdownfile', [resources path,,,]);
```

#### Markdown::setConfig()

```php
//change parser config
$html = Markdown::setConfig('hard_wrap', false)
    ->parse('some markdown text.');

//temporary change parser config
$html = Markdown::setConfig('hard_wrap', false)->parse('some markdown text.');
Markdown::setConfig('hard_wrap', true);
```

#### Markdown::PARSER_METHOD()
It's accessable to the original parser method directly through to ```__call()``` magic method:

```php
//direct access to the original parser methods if you need
Markdown::PARSER_METHOD();
```


## Laravel App Container
It can access wrapper class instance from binded application container.
See this php: [src/MarkdownServiceProvider.php](https://github.com/tomakee/laravel-markdown-wrapper/blob/master/src/MarkdownServiceProvider.php#L54).

```
Tomakee\Markdown\MarkdownServiceProvider::register()
```

#### app('markdown'), app('Tomakee\Markdown\Parser')

```php
//get instance
$instance = app('markdown');
//or
$instance = app('Tomakee\Markdown\Parser');
```

#### app('markdown')->parse()

```php
//parse markdown text
$html = app('markdown')->parse('some markdown text.');
```

#### app('markdown')->file()

```php
//parse markdown file
$html = app('markdown')->file('path.to.markdownfile');  //path format is same as Laravel view.

//You can set different resources path.
//But if your project always use different path from default,
//change the config setting (app/config/markdown.php).

$html = app('markdown')->file('path.to.markdownfile', [resources path,,,]);
```

#### app('markdown')->setConfig()

```php
//change parser config
$html = app('markdown')->setConfig('hard_wrap', false)
    ->parse('some markdown text.');

//temporary change parser config
$html = app('markdown')->setConfig('hard_wrap', false)
    ->parse('some markdown text.');
app('markdown')->setConfig('hard_wrap', true);
```

#### app('markdown')->PARSER_METHOD()
It's accessable to the original parser method directly through to ```__call()``` magic method:

```php
//direct access to the original parser methods if you need
app('markdown')->PARSER_METHOD();
```


## Markdown Parser Config
Markdown wrapper class config is placed at ```app/config/markdown.php``` after execute:

```bash
cd LARAVEL_PROJECT_DIR
php artisan vendor:publish
```

### Example:

```php
[
    'default'    => 'github',
    'resources'  => [resource_path('views')],
    'extensions' => ['md', 'md.blade.php', 'blade.php', 'php'],
    [
        'id'      => 'github',
        'parser'  => \cebe\markdown\GithubMarkdown::class,
        'methods' => [
                'single' => 'parseParagraph',
                'multi'  => 'parse',
        ],
        'config'  => [
                'html5'               => true,
                'enableNewlines'      => true,
                'keepListStartNumber' => false,
        ],
    ],
];
```

### default
Automatically loading parser class id (see section [Parser settings > id](#id) ).<br>
(*default value: 'michelf-extra'*)

### resources
Markdown file resources path. Markdown files will be finded in this path.
If they are placed in different pathes, then should be set all of pathes in this array().<br>
(*default value: [resource_path('views')]*)

### extensions
Markdown file extensions array.<br>
(*default value: ['md', 'md.blade.php', 'blade.php', 'php']*)


### Parser settings

- id : Unique id string for the parser class. If it's unique, anything is possible.
- parser : Full path string of the parser class such as ```\namespace\to\class::class```.
- methods : A single or multiple line to parse markdown method name. Array keys are "single" and "multi".
    - single : for single line markdown.
    - multi  : for multipule line markdown.
- config : Parser class config properties array. If there is no config, value must be empty array().
