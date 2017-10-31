<?php
error_reporting(E_ALL | E_STRICT);
require __DIR__.'/../vendor/autoload.php';

$config = dirname(__DIR__).'/config/app.php';

exec('[ `grep Tomakee '.$config.'` ] || sed -r "/\'providers\' => /,/\],/ s/\],/    Tomakee\\\\\Markdown\\\\\MarkdownServiceProvider::class,\n    ],/" -i '.$config);

require __DIR__.'/../src/helpers.php';
