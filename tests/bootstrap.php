<?php
error_reporting(E_ALL | E_STRICT);
require __DIR__.'/../src/Parser.php';
require __DIR__.'/../src/MarkdownServiceProvider.php';
require __DIR__.'/../src/helpers.php';
require __DIR__.'/../src/Facade/Markdown.php';
require __DIR__.'/../src/Exceptions/InvalidParserException.php';
require __DIR__.'/../src/Exceptions/InvalidTagException.php';
require __DIR__.'/../bootstap/autoload.php';
