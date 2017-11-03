# laravel-markdown-wrapper

[![Build Status](https://travis-ci.org/tomakee/laravel-markdown-wrapper.svg?branch=master)](https://travis-ci.org/tomakee/laravel-markdown-wrapper)
[![Latest Stable Version](https://poser.pugx.org/tomakee/laravel-markdown-wrapper/v/stable)](https://packagist.org/packages/tomakee/laravel-markdown-wrapper)
[![License](https://poser.pugx.org/tomakee/laravel-markdown-wrapper/license)](https://packagist.org/packages/tomakee/laravel-markdown-wrapper)


Laravel用のMarkdownパーサーのラッパークラスです。

このパッケージはラッパークラスで、パーサーは[michelf/php-markdown](https://github.com/michelf/php-markdown) or [cebe/markdown](https://github.com/cebe/markdown)などを別途インストールしてください。


## 環境
- PHP >= 5.6
- [Laravel](https://laravel.com/) >= 5.4
- Markdownパーサー
    - [michelf/php-markdown](https://github.com/michelf/php-markdown)
    - [cebe/markdown](https://github.com/cebe/markdown)
    - 他.


## 機能
- Bladeディレクティブ: `@markdown`, `@endmarkdown`, `@markdownFile`.
- Laravelヘルパー: `markdown()`, `markdown_config()`, `markdown_file()`, `markdown_capture()`.
- Laravelファサード: `Markdown::parse()`, `Markdown::setConfig()`, `Markdown::file()`, `Markdown::start()`, `Markdown::end()`
- ラッパークラス: `Tomakee\Markdown\Parser`


## PHP Composerインストール
[php coomposer](https://getcomposer.org/)をインストール済みでない場合は、まず初めにインストールしてください。

```bash
#composerインストール (Linux or MacOS)
curl -sS https://getcomposer.org/installer | php

#composer.pharをパスの通った場所に移動 (例：/usr/local/bin)
mv composer.phar /usr/local/bin/composer
chmod 755 /usr/local/bin/composer
```


## Laravelプロジェクト作成
[php coomposer](https://getcomposer.org/)をインストールしたら、[Laravel](https://laravel.com/)プロジェクトを作成します。
既存のプロジェクトにこのパッケージを追加するだけであれば、この工程を飛ばしてください。

```bash
composer create-project --prefer-dist laravel/laravel LARAVEL_PROJECT_DIR

#またはバージョン付きで
composer create-project --prefer-dist laravel/laravel "5.4.*" LARAVEL_PROJECT_DIR
```


## インストール方法
下記のコマンドでインストールします。

```bash
cd LARAVEL_PROJECT_DIR
composer require tomakee/laravel-markddown-wrapper
php artisan vendor:publish
```

config/app.php のサービスプロバイダーにこのパッケージを追加します。

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

そして、最後に好みのMarkdownパーサーをインストールします。

```bash
cd LARAVEL_PROJECT_DIR
composer require michelf/php-markdown
#or
composer require cebe/markdown
```


## Bladeディレクティブ
Laravel ViewファイルにMarkdownディレクティブを混ぜて書くことができます。<br>
例:

#### 1行マークダウン

```
@markdown('マークダウンテキスト。')
```

#### 複数行マークダウン

```
@markdown
some markdown text.
[link text](/link/path)
@endmarkdown
```

#### マークダウンファイル読込

```
@markdownFile('path.to.markdownfile')  {{-- パスフォーマットはLaravel Viewと同じ --}}

{{--
設定ファイル以外のパスのマークダウンファイルを読み込みたい場合は、
下記のようにできます。
これは、一時的に読み込みたい場合に使用できるだけのものですので、
プロジェクト内で恒久的にこのパスを使用するのであれば、設定ファイル
（app/config/markdown.php）の ```'resouces' => []``` に追加するように
してください。
--}}

@markdownFile('path.to.markdownfile', [リソースパス]);
```



## Laravelヘルパー
コントローラーなどで、本ラッパークラスのインスタンスにアクセスできます。

#### markdown()

```php
//マークダウンパース
$html = markdown('マークダウンテキスト。');
```

#### markdown_config()

```php
//パーサー設定変更
$parser = markdown_config('hard_wrap', false);
$html = $parser->parse('マークダウンテキスト。');

//パーサー設定変更、マークダウンパース
$html = markdown_config(['hard_wrap' => false, 'code_class_prefix' => 'prefix-'])
        ->parse('マークダウンテキスト。');
```

#### markdown_file()

```php
//マークダウンファイルパース
$html = markdown_file('path.to.markdownfile');  //パスフォーマットはLaravel Viewと同じ

//設定ファイル以外のパスのマークダウンファイルを読み込みたい場合は、
//下記のようにできます。
//これは、一時的に読み込みたい場合に使用できるだけのものですので、
//プロジェクト内で恒久的にこのパスを使用するのであれば、設定ファイル
//（app/config/markdown.php）の ```'resouces' => []``` に追加するように
//してください。

$html = markdown_file('path.to.markdownfile', [リソースパス,,,,]);
```

#### markdown_capture()

```php
$html = markdown_capture(function () {
    echo 'マークダウンテキスト。';
});

//引数あり
$html = markdown_capture(function () use ($args1, $args2) {
    echo $args1 . $args2 . 'マークダウンテキスト。';
});
```


## Laravelファサード
コントローラーなどで、本ラッパークラスのインスタンスにLaravelのファサード機能を使ってアクセスできます。

#### Import markdown facade
まず、本パッケージのファサードをインポートします。<br>
クラスパス:

```php
use Tomakee\Markdown\Facades\Markdown;
```

#### Markdown::parse()

```php
//マークダウンパース
$html = Markdown::parse('マークダウンテキスト。');
```

#### Markdown::file()

```php
//マークダウンファイルパース
$html = Markdown::file('path.to.markdownfile');  //パスフォーマットはLaravel Viewと同じ

//設定ファイル以外のパスのマークダウンファイルを読み込みたい場合は、
//下記のようにできます。
//これは、一時的に読み込みたい場合に使用できるだけのものですので、
//プロジェクト内で恒久的にこのパスを使用するのであれば、設定ファイル
//（app/config/markdown.php）の ```'resouces' => []``` に追加するように
//してください。

$html = Markdown::file('path.to.markdownfile', [リソースパス,,,]);
```

#### Markdown::setConfig()

```php
//パーサー設定変更、マークダウンパース
$html = Markdown::setConfig('hard_wrap', false)
    ->parse('マークダウンテキスト。');

//パサー設定を一時的に変更
$html = Markdown::setConfig('hard_wrap', false)->parse('マークダウンテキスト。');
Markdown::setConfig('hard_wrap', true);
```

#### Markdown::PARSER_METHOD()
必要であれば、本パッケージのラッパークラスの```__call()```マジックメソッドを通して、元のパーサーのメソッドにアクセスすることができます。

```php
//直接アクセス
Markdown::パーサーメソッド();
```


## Laravelコンテナ
コントローラーなどで、Laravelアプリケーションコンテナにバインドしてあるインスタンスから、本ラッパークラスのインスタンスにアクセスできます。
バインドは下記のようにしてあります。<br>
[src/MarkdownServiceProvider.php](https://github.com/tomakee/laravel-markdown-wrapper/blob/master/src/MarkdownServiceProvider.php#L54).

```
Tomakee\Markdown\MarkdownServiceProvider::register()
```

#### app('markdown'), app('Tomakee\Markdown\Parser')

```php
//インスタンス取得
$instance = app('markdown');
//or
$instance = app('Tomakee\Markdown\Parser');
```

#### app('markdown')->parse()

```php
//マークダウンパース
$html = app('markdown')->parse('マークダウンテキスト。');
```

#### app('markdown')->file()

```php
//マークダウンファイルパース
$html = app('markdown')->file('path.to.markdownfile');  //パスフォーマットはLaravel Viewと同じ

//設定ファイル以外のパスのマークダウンファイルを読み込みたい場合は、
//下記のようにできます。
//これは、一時的に読み込みたい場合に使用できるだけのものですので、
//プロジェクト内で恒久的にこのパスを使用するのであれば、設定ファイル
//（app/config/markdown.php）の ```'resouces' => []``` に追加するように
//してください。

$html = app('markdown')->file('path.to.markdownfile', [リソースパス,,,]);
```

#### app('markdown')->setConfig()

```php
//パーサー設定変更、マークダウンパース
$html = app('markdown')->setConfig('hard_wrap', false)
    ->parse('マークダウンテキスト。');

//パサー設定を一時的に変更
$html = app('markdown')->setConfig('hard_wrap', false)
    ->parse('マークダウンテキスト。');
app('markdown')->setConfig('hard_wrap', true);
```

#### app('markdown')->PARSER_METHOD()
必要であれば、本パッケージのラッパークラスの```__call()```マジックメソッドを通して、元のパーサーのメソッドにアクセスすることができます。

```php
//直接アクセス
app('markdown')->パーサーメソッド();
```


## 設定
下記コマンドを実行すると、本パッケージの設定ファイルが```app/config/markdown.php```に作成されます。

```bash
cd LARAVEL_PROJECT_DIR
php artisan vendor:publish
```

### 設定例:

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
初期状態で自動的にロードする[パーサークラスID](#id)です。<br>
（*初期値：'michelf-extra'*）

### resources
マークダウンファイルのリソースパスです。
マークダウンファイルはこのリソースパスにおいてある必要があります。
パスが異なる場合は、すべてのパスをこの配列に設定してください。<br>
（*初期値：[resource_path('views')]*）

### extensions
マークダウンファイルの拡張配列です。<br>
（*初期値：['md', 'md.blade.php', 'blade.php', 'php']*）


### パーサー設定

- id　　　：パーサークラスのユニークIDを設定
- parser　：```\namespace\to\class::class```の形式でパーサークラスのフルパスを設定
- methods ：1行、複数行用のメソッド名を設定
    - single : 1行マークダウン用
    - multi  : 複数行マークダウン用
- config　：パーサークラスの設定プロパティの連想配列を設定、設定自体がない場合は```空（array()）```を設定
