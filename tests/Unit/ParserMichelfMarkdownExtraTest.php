<?php
namespace Tests\Unit;

use Tests\TestCase;
use Tomakee\Markdown\Parser;
use Tomakee\Markdown\Facades\Markdown;

class ParserMichelfMarkdownExtraTest extends TestCase
{
    /**
     * Markdown Parser
     * @var string PSR_CLASS
     */
    const PSR_CLASS = \Michelf\MarkdownExtra::class;

    /**
     * Markdown Parser id
     * @var string PSR_CLASS_ID
     */
    const PSR_CLASS_ID = 'michelf-extra';

    /**
     * Markdown wrapper prophecy instance
     * @var object $parser
     */
    private $parser;

    /**
     * Markdown wrapper class instance
     * @var object $parserIns  Tomakee\Markdown\Parser
     */
    private $parserIns;

    /**
     * Markdown wrapper config path
     * @var string
     */
    private static $parserConfigPath = '../../config/markdown.php';

    /**
     * Default markdown parser id
     * @var string
     */
    private static $defaultParserId = 'michelf-extra';

    /**
     * Markdown file resources path
     * @var array
     */
    private static $resourcesPath = [__DIR__.'/../resources/views'];


    /**
     * @beforeClass
     */
    public static function beforeTest ()
    {
        // markdwon.php config path
        $parserConfigPath = realpath(__DIR__.'/../../config/markdown.php');

        if (file_exists($parserConfigPath)) {
            static::$parserConfigPath = $parserConfigPath;
        }

        // default markdown parser id
        ob_start();
        passthru("sed -nr \"/'default' => / s/^.+ => '(.+)',/\\1/p\" ".static::$parserConfigPath);
        $defaultParserId = ob_get_clean();

        if (! empty($defaultParserId)) {
            static::$defaultParserId = trim($defaultParserId);
        }

        $parserId = self::PSR_CLASS_ID;

        // set default parser
        exec("sed -r \"/'default' => / s/=> '.+',/=> '$parserId',/\" -i ".static::$parserConfigPath);

        // markdown.md resources path
        $resourcesPath = realpath(__DIR__.'/../resources/views');

        if (file_exists($resourcesPath)) {
            static::$resourcesPath = [$resourcesPath];
        }
    }

    /**
     * @afterClass
     */
    public static function afterTest ()
    {
        $defaultParserId = static::$defaultParserId;

        // reset default parser
        exec("sed -r \"/'default' => / s/=> '.+',/=> '$defaultParserId',/\" -i ".static::$parserConfigPath);
    }

    /**
     * @before
     */
    public function prepareInstances ()
    {
        $class = self::PSR_CLASS;
        $psr   = new $class;

        // markdown parser default config
        $config = array_column(config('markdown', []), null, 'id');
        $key    = array_search(self::PSR_CLASS, array_column($config, 'parser', 'id'));
        $config = array_get($config, "$key.config", []);

        foreach ($config as $k => $v) {
            $psr->$k = $v;
        }

        $this->parser = $this->prophesize('Tomakee\Markdown\Parser')
            ->willBeConstructedWith([$psr]);

        $this->parserIns = new Parser($psr);
    }

    /*
     |--------------------------------------------------------------------------
     | Markdown wrapper class prophecy test
     |--------------------------------------------------------------------------
     |
     | $this->parser = $this->prophesize('Tomakee\Markdown\Parser')
     |
     | - $this->parser->parse()
     | - $this->parser->transform()
     | - $this->parser->file()
     | - $this->parser->start(), $this->parser->end()
     | - $this->parser->setConfig()
     |
     */

    /**
     * @test
     */
    public function markdownMultiLine ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= 'markdown text.';

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->parser->parse($markdown)
            ->willReturn($expected)
            ->shouldBeCalled();

        $this->assertEquals(
                $this->parser->reveal()->parse($markdown),
                $this->parserIns->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function markdownSingleLine ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->parser->parse($markdown)
            ->willReturn($expected)
            ->shouldBeCalled();

        $this->assertEquals(
                $this->parser->reveal()->parse($markdown),
                $this->parserIns->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function markdownMultiLineDirectCall ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= 'markdown text.';

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                $this->parserIns->transform($markdown)
        );
    }

    /**
     * @test
     */
    public function markdownSingleLineDirectCall ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                $this->parserIns->transform($markdown)
        );
    }

    /**
     * @test
     */
    public function markdownFile ()
    {
         $expected  = "<h1>Hello Markdown</h1>\n\n";
         $expected .= "<p>This is from markdown file: \"markdown.md\".</p>\n";

         $this->parser->file('markdown', static::$resourcesPath)
             ->willReturn($expected)
             ->shouldBeCalled();

         $this->assertEquals(
                 $this->parser->reveal()->file('markdown', static::$resourcesPath),
                 $this->parserIns->file('markdown', static::$resourcesPath)
         );
    }

    /**
     * @test
     */
    public function markdownCapture ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= 'markdown text.';

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->parser->reveal()->start();
        $this->expectOutputString($markdown);
        echo $markdown;

        $this->parser->end()
           ->willReturn($expected)
           ->shouldBeCalled();

        $this->assertEquals(
                $expected,
                $this->parser->reveal()->end()
        );
    }

    /**
     * @test
     */
    public function markdownConfig ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $parserIns = $this->parserIns;

        $this->parser->setConfig('hard_wrap', false)
            ->will(function () use ($parserIns, $markdown) {
                return $parserIns->setConfig('hard_wrap', false)->parse($markdown);
            })
            ->shouldBeCalled();

        $this->assertEquals(
                $expected,
                $this->parser->reveal()->setConfig('hard_wrap', false)
        );
    }

    /**
     * @test
     */
    public function markdownConfigChange ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $parserIns = $this->parserIns;

        $this->assertEquals(
                $expected,
                $parserIns->setConfig('hard_wrap', false)->parse($markdown)
        );

        // hard_wrap: true
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown<br>\n";
        $expected .= "markdown text.</p>\n";

        $this->parser->setConfig('hard_wrap', true)
            ->will(function () use ($parserIns, $markdown) {
                return $parserIns->setConfig('hard_wrap', true)->parse($markdown);
            })
            ->shouldBeCalled();

        $this->assertEquals(
                $expected,
                $this->parser->reveal()->setConfig('hard_wrap', true)
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Laravel app service provider Singleton Binded test
     |--------------------------------------------------------------------------
     |
     | - app('Tomakee\Markdown\Parser')->parse()
     | - app('Tomakee\Markdown\Parser')->transform()
     | - app('Tomakee\Markdown\Parser')->file()
     | - app('Tomakee\Markdown\Parser')->start(), app('Tomakee\Markdown\Parser')->end()
     | - app('Tomakee\Markdown\Parser')->setConfig()
     |
     */

    /**
     * @test
     */
    public function appServiceProviderMarkdownMultiLine ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= "markdown text.\n";

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderMarkdownMultiLineDirectCall ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= "markdown text.\n";

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->transform($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderMarkdownSingleLine ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderMarkdownSingleLineDirectCall ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->transform($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderMarkdownFile ()
    {
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>This is from markdown file: \"markdown.md\".</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->file('markdown', static::$resourcesPath)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderMarkdownCapture ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= 'markdown text.';

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        app('Tomakee\Markdown\Parser')->start();
        echo $markdown;

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->end()
        );
    }

    /**
     * @test
     */
    public function appServiceProviderMarkdownConfig ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->setConfig('hard_wrap', false)->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderMarkdownConfigChange ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->setConfig('hard_wrap', false)->parse($markdown)
        );

        // hard_wrap: true
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown<br>\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('Tomakee\Markdown\Parser')->setConfig('hard_wrap', true)->parse($markdown)
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Laravel app service provider Binded test
     |--------------------------------------------------------------------------
     |
     | - app('markdown')->parse()
     | - app('markdown')->transform()
     | - app('markdown')->file()
     | - app('markdown')->start(), app('markdown')->end()
     | - app('markdown')->setConfig()
     |
     */

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownMultiLine ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= "markdown text.\n";

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownMultiLineDirectCall ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= "markdown text.\n";

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->transform($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownSingleLine ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownSingleLineDirectCall ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->transform($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownFile ()
    {
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>This is from markdown file: \"markdown.md\".</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->file('markdown', static::$resourcesPath)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownCapture ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= 'markdown text.';

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        app('markdown')->start();
        echo $markdown;

        $this->assertEquals(
                $expected,
                app('markdown')->end()
        );
    }

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownConfig ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->setConfig('hard_wrap', false)->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function appServiceProviderBindedMarkdownConfigChange ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->setConfig('hard_wrap', false)->parse($markdown)
        );

        // hard_wrap: true
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown<br>\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                app('markdown')->setConfig('hard_wrap', true)->parse($markdown)
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Laravel facade test
     |--------------------------------------------------------------------------
     |
     | - Markdown::parse()
     | - Markdown::transform()
     | - Markdown::file()
     | - Markdown::start(), Markdown::end()
     | - Markdown::setConfig()
     |
     */

    /**
     * @test
     */
    public function facadeMarkdownMultiLine ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= "markdown text.\n";

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::parse($markdown)
        );
    }

    /**
     * @test
     */
    public function facadeMarkdownSingleLine ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::parse($markdown)
        );
    }

    /**
     * @test
     */
    public function facadeMarkdownMultiLineDirectCall ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= "markdown text.\n";

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::transform($markdown)
        );
    }

    /**
     * @test
     */
    public function facadeMarkdownSingleLineDirectCall ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::transform($markdown)
        );
    }

    /**
     * @test
     */
    public function facadeMarkdownFile ()
    {
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>This is from markdown file: \"markdown.md\".</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::file('markdown', static::$resourcesPath)
        );
    }

    /**
     * @test
     */
    public function facadeMarkdownCapture ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= 'markdown text.';

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        Markdown::start();
        echo $markdown;

        $this->assertEquals(
                $expected,
                Markdown::end()
        );
    }

    /**
     * @test
     */
    public function facadeMarkdownConfig ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::setConfig('hard_wrap', false)->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function facadeMarkdownConfigChange ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::setConfig('hard_wrap', false)->parse($markdown)
        );

        // hard_wrap: true
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown<br>\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                Markdown::setConfig('hard_wrap', true)->parse($markdown)
        );
    }

    /*
     |--------------------------------------------------------------------------
     | Laravel helpers test
     |--------------------------------------------------------------------------
     |
     | - markdown()
     | - markdown_file()
     | - markdown_capture()
     | - markdown_config()
     |
     */

    /**
     * @test
     */
    public function helperMarkdownMultiLine ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= "markdown text.\n";

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                markdown($markdown)
        );
    }

    /**
     * @test
     */
    public function helperMarkdownSingleLine ()
    {
        $markdown = 'Here is inline **Hello Markdown**.';
        $expected = "<p>Here is inline <strong>Hello Markdown</strong>.</p>\n";

        $this->assertEquals(
                $expected,
                markdown($markdown)
        );
    }

    /**
     * @test
     */
    public function helperMarkdownFile ()
    {
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>This is from markdown file: \"markdown.md\".</p>\n";

        $this->assertEquals(
                $expected,
                markdown_file('markdown', static::$resourcesPath)
        );
    }

    /**
     * @test
     */
    public function helperMarkdownCapture ()
    {
        $markdown  = '#Hello Markdown'."\n";
        $markdown .= 'markdown text.';

        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>markdown text.</p>\n";

        $html = markdown_capture(function () use ($markdown) {
            echo $markdown;
        });

        $this->assertEquals($expected, $html);
    }

    /**
     * @test
     */
    public function helperMarkdownConfig ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                markdown_config('hard_wrap', false)->parse($markdown)
        );
    }

    /**
     * @test
     */
    public function helperMarkdownConfigChange ()
    {
        $markdown  = '#Hello Markdown'."\n\n";
        $markdown .= "Hello Markdown\n";
        $markdown .= "markdown text.";

        // hard_wrap: false
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                markdown_config('hard_wrap', false)->parse($markdown)
        );

        // hard_wrap: true
        $expected  = "<h1>Hello Markdown</h1>\n\n";
        $expected .= "<p>Hello Markdown<br>\n";
        $expected .= "markdown text.</p>\n";

        $this->assertEquals(
                $expected,
                markdown_config('hard_wrap', true)->parse($markdown)
        );
    }
}
