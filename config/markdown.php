<?php

return [
        /*
         |--------------------------------------------------------------------------
         | Default markdown parser class id
         |--------------------------------------------------------------------------
         |
         | Automatically loading this id parser which is set
         | in the below array (Markdown parser settings).
         |
         | default : string 'michelf-extra' (\\Michelf\\MarkdownExtra)
         |
         */

        'default' => 'michelf-extra',

        /*
         |--------------------------------------------------------------------------
         | Markdown files path
         |--------------------------------------------------------------------------
         |
         | Markdown file resources path. Markdown files will be finded in this path.
         | If they are placed in different pathes, then should be set all of pathes
         | in this array().
         |
         | default : array [/path/to/resources/views]
         |
         */

        'resources' => [resource_path('views')],

        /*
         |--------------------------------------------------------------------------
         | Markdown file extensions
         |--------------------------------------------------------------------------
         |
         | Markdown file extensions array.
         |
         | default : array ['md', 'md.blade.php', 'blade.php', 'php']
         |
         */

        'extensions' => ['md', 'md.blade.php', 'blade.php', 'php'],

        /*
         |--------------------------------------------------------------------------
         | Markdown parser settings
         |--------------------------------------------------------------------------
         |
         | - id :
         | Unique id string. If it's unique, anything is possible.
         |
         | - parser :
         | Full path string of parser class such as \namespace\to\class::class.
         |
         | - methods :
         | A single or multiple line to parse markdown method name.
         | Array keys are "single" and "multi".
         |
         | - config :
         | Parser class config properties array.
         | If there is no config, value must be empty array().
         |
         */

        [
                'id'      => 'traditional',
                'parser'  => \cebe\markdown\Markdown::class,
                'methods' => [
                        'single' => 'parseParagraph',
                        'multi'  => 'parse',
                ],
                'config'  => [
                        'html5'               => true,
                        'keepListStartNumber' => false,
                ],
        ],[
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
        ],[
                'id'      => 'extra',
                'parser'  => \cebe\markdown\MarkdownExtra::class,
                'methods' => [
                        'single' => 'parseParagraph',
                        'multi'  => 'parse',
                ],
                'config'  => [
                        'html5'               => true,
                        'keepListStartNumber' => false,
                        'codeAttributesOnPre' => true,
                ],
        ],[
                'id'      => 'michelf',
                'parser'  => \Michelf\Markdown::class,
                'methods' => [
                        'single' => 'transform',
                        'multi'  => 'transform',
                ],
                'config'  => [
                        'empty_element_suffix' => ' />',
                        'tab_width'            => 4,
                        'hard_wrap'            => true,
                        'no_markup'            => false,
                        'no_entities'          => false,
                        'predef_urls'          => [],
                        'predef_titles'        => [],
                        'url_filter_func'      => null,
                        'header_id_func'       => null,
                        'code_block_content_func' => null,
                        'code_span_content_func'  => null,
                        'enhanced_ordered_list'   => false,
                ],
        ],[
                'id'      => 'michelf-extra',
                'parser'  => \Michelf\MarkdownExtra::class,
                'methods' => [
                        'single' => 'transform',
                        'multi'  => 'transform',
                ],
                'config'  => [
                        'empty_element_suffix' => '>',
                        'tab_width'            => 4,
                        'hard_wrap'            => true,
                        'no_markup'            => false,
                        'no_entities'          => false,
                        'predef_urls'          => [],
                        'predef_titles'        => [],
                        'url_filter_func'      => null,
                        'header_id_func'       => null,
                        'code_block_content_func' => null,
                        'code_span_content_func'  => null,
                        'enhanced_ordered_list'   => true,
                        'fn_id_prefix'         => '',
                        'fn_link_title'        => '',
                        'fn_backlink_title'    => '',
                        'fn_link_class'        => '',
                        'fn_backlink_class'    => '',
                        'fn_backlink_html'     => '',
                        'code_class_prefix'    => '',
                        'code_attr_on_pre'     => false,
                        'table_align_class_tmpl' => '',
                        'predef_abbr'          => [],
                ],
        ],
];
