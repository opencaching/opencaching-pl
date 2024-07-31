<?php

use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__)
    ->path('src')
    ->notPath('src/Libs')
    ->notPath('src/Utils/Database/Updates/template.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setFinder($finder)
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR12' => true,
        '@PSR12:risky' => true,

        // To be enabled later
        'binary_operator_spaces' => false,
        'blank_line_after_namespace' => false,
        'blank_line_after_opening_tag' => false,
        'blank_lines_before_namespace' => false,
        'braces_position' => false,
        'class_definition' => false,
        'constant_case' => false,
        'control_structure_braces' => false,
        'control_structure_continuation_position' => false,
        'elseif' => false,
        'function_declaration' => false,
        'line_ending' => false,
        'lowercase_keywords' => false,
        'method_argument_space' => false,
        'new_with_parentheses' => false,
        'no_blank_lines_after_class_opening' => false,
        'no_break_comment' => false,
        'no_closing_tag' => false,
        'no_leading_import_slash' => false,
        'no_multiple_statements_per_line' => false,
        'no_spaces_after_function_name' => false,
        'no_trailing_whitespace_in_comment' => false,
        'no_whitespace_in_blank_line' => false,
        'return_type_declaration' => false,
        'short_scalar_cast' => false,
        'single_blank_line_at_eof' => false,
        'single_class_element_per_statement' => false,
        'single_line_after_imports' => false,
        'spaces_inside_parentheses' => false,
        'statement_indentation' => false,
        'switch_case_space' => false,
        'ternary_operator_spaces' => false,
        'unary_operator_spaces' => false,
        'visibility_required' => false,
    ]);
