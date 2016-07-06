<?php

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->exclude('vendor')
    ->exclude('resources')
    ->exclude('database')
    ->exclude('config')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->fixers([
        'duplicate_semicolon',
        'new_with_braces',
        //'phpdoc_params',
        'double_arrow_multiline_whitespaces',
        'remove_lines_between_uses',
        'standardize_not_equal',
        'empty_return',
        'multiline_array_trailing_comma',
        'object_operator',
        'remove_leading_slash_use',
        'return',
        'single_array_no_trailing_comma',
        'spaces_before_semicolon',
        'spaces_cast',
        'unused_use',
        'whitespacy_lines',
        'align_double_arrow',
        //'align_equals',
        'concat_with_spaces',
        'multiline_spaces_before_semicolon',
        'ordered_use',
        'short_array_syntax',
    ])
    ->finder($finder)
;