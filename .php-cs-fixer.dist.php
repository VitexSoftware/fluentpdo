<?php

declare(strict_types=1);

/**
 * This file is part of the FluentPDO package.
 *
 * FluentPDO is a quick and light PHP library for rapid query building. It features a smart join builder, which automatically creates table joins.
 *
 * For more information see readme.md
 *
 * @link      https://github.com/VitexSoftware/fluentpdo
 * @author    Chris Bornhoft, start@env.ms
 * @copyright 2012-2020 envms - Chris Bornhoft, Marek Lichtner
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License, version 3.0
 *
 * (G) 2025-2026 Vítězslav Dvořák <info@vitexsoftware.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Ergebnis\PhpCsFixer\Config\Factory;
use Ergebnis\PhpCsFixer\Config\Rules;
use Ergebnis\PhpCsFixer\Config\RuleSet\Php81;

$header = <<<'HEADER'
This file is part of the FluentPDO package.

FluentPDO is a quick and light PHP library for rapid query building. It features a smart join builder, which automatically creates table joins.

For more information see readme.md

@link      https://github.com/VitexSoftware/fluentpdo
@author    Chris Bornhoft, start@env.ms
@copyright 2012-2020 envms - Chris Bornhoft, Marek Lichtner
@license   https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License, version 3.0

(G) 2025-2026 Vítězslav Dvořák <info@vitexsoftware.cz>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.
HEADER;

$ruleSet = Php81::create()->withHeader($header)->withRules(Rules::fromArray([
    'blank_line_before_statement' => [
        'statements' => [
            'break',
            'continue',
            'declare',
            'default',
            'do',
            'exit',
            'for',
            'foreach',
            'goto',
            'if',
            'include',
            'include_once',
            'require',
            'require_once',
            'return',
            'switch',
            'throw',
            'try',
            'while',
        ],
    ],
    'concat_space' => [
        'spacing' => 'none',
    ],
    'date_time_immutable' => false,
    'error_suppression' => false,
    'final_class' => false,
    'mb_str_functions' => false,
    'native_function_invocation' => [
        'exclude' => [
            'sprintf',
        ],
        'include' => [
            '@compiler_optimized',
        ],
        'scope' => 'all',
        'strict' => false,
    ],
    'php_unit_internal_class' => false,
    'php_unit_test_annotation' => [
        'style' => 'prefix',
    ],
    'php_unit_test_class_requires_covers' => false,
    'return_to_yield_from' => false,
    'phpdoc_array_type' => false,
    'phpdoc_list_type' => false,
    'attribute_empty_parentheses' => false,
    'final_public_method_for_abstract_class' => false,
    'class_attributes_separation' => [
        'elements' => [
            'const' => 'only_if_meta',
            'property' => 'only_if_meta',
            'trait_import' => 'none',
            'case' => 'none',
        ],
    ],
    'yoda_style' => false,
    'php_unit_test_case_static_method_calls' => false,
]));

$config = Factory::fromRuleSet($ruleSet)->setUnsupportedPhpVersionAllowed(true);

$config->getFinder()
    ->append([
        __DIR__.'/.php-cs-fixer.dist.php',
    ])
    ->append([
        __DIR__.'/rector.php',
    ])
    ->in('src')
    ->in('tests');

$config->setCacheFile(__DIR__.'/.build/php-cs-fixer/.php-cs-fixer.cache');

return $config;
