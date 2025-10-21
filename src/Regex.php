<?php

declare(strict_types=1);

/**
 * This file is part of the EaseCore package.
 *
 * (c) Vítězslav Dvořák <info@vitexsoftware.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Envms\FluentPDO;

/**
 * Regex class.
 */
class Regex
{
    /**
     * @var string - All UTF-8 letter characters
     */
    public const ALPHA = '\p{L}';

    /**
     * @var string - All UTF-8 letter and number characters
     */
    public const ALNUM = '\p{L}\p{N}';

    /**
     * @var string - All valid SQL characters except the UTF-8 groupings with quotes and wildcards
     */
    public const SQLCHARS = '\p{L}\p{N}\p{Pc}\p{Pd}\p{Pf}\p{Pi}';

    /**
     * Replace "camelCaseMethod" with "camel Case Method".
     */
    public function camelCaseSpaced(string $subject): array|string|null
    {
        return preg_replace('/(.)([A-Z]+)/', '$1 $2', $subject);
    }

    /**
     * Replace "SELECT * FROM table WHERE column = ?" with
     * "SELECT *
     *  FROM table
     *  WHERE column = ?".
     */
    public function splitClauses(string $subject): array|string|null
    {
        return preg_replace(
            '/\b(WHERE|FROM|GROUP BY|HAVING|ORDER BY|LIMIT|OFFSET|UNION|ON DUPLICATE KEY UPDATE|VALUES|SET)\b/',
            "\n$0",
            $subject,
        );
    }

    /**
     * Replace SELECT t2.id FROM t1 LEFT JOIN t2 ON t2.id = t1.t2_id" with
     * "SELECT t2.id FROM t1
     *      LEFT JOIN t2 ON t2.id = t1.t2_id".
     */
    public function splitSubClauses(string $subject): array|string|null
    {
        return preg_replace(
            '/\b(INNER|OUTER|LEFT|RIGHT|FULL|CASE|WHEN|END|ELSE|AND|OR)\b/',
            "\n    $0",
            $subject,
        );
    }

    /**
     * Replace "WHERE column = ?  " with "WHERE column = ?".
     */
    public function removeLineEndWhitespace(string $subject): array|string|null
    {
        return preg_replace("/\\s+\n/", "\n", $subject);
    }

    /**
     * Replace the string "table1.table2:column" with "table2.column".
     */
    public function removeAdditionalJoins(string $subject): array|string|null
    {
        return preg_replace('/(?:[^\s]*[.:])?([^\s]+)[.:]([^\s]*)/u', '$1.$2', $subject);
    }

    /**
     * Match the first file outside of the Fluent source.
     */
    public function compareLocation(string $subject, mixed &$matches = null, ?string $directory = null): false|int
    {
        $directory = ($directory === null) ? preg_quote(__DIR__, '/') : preg_quote($directory, '/');

        return preg_match('/(^'.$directory.'(\\.php$|[\/\\\\]))/', $subject, $matches);
    }

    /**
     * Match the string "?" or ":param".
     */
    public function sqlParameter(string $subject, mixed &$matches = null): false|int
    {
        return preg_match('/(\?|:\w+)/', $subject, $matches);
    }

    /**
     * Match the UTF-8 string "table AS alias".
     */
    public function tableAlias(string $subject, mixed &$matches = null): false|int
    {
        return preg_match(
            '/`?(['.self::SQLCHARS.']+[.:]?['.self::SQLCHARS.'*]*)`?(\s+AS)?(\s+`?(['.self::SQLCHARS.']*)`?)?/ui',
            $subject,
            $matches,
        );
    }

    /**
     * Match the UTF-8 string "table" or "table.".
     */
    public function tableJoin(string $subject, mixed &$matches = null): false|int
    {
        return preg_match_all('/(['.self::SQLCHARS.']+[.:]?)/u', $subject, $matches);
    }

    /**
     * Match the UTF-8 string "table." or "table.column".
     */
    public function tableJoinFull(string $subject, mixed &$matches = null): false|int
    {
        return preg_match_all('/([^[:space:]()]+[.:])['.self::SQLCHARS.']*/u', $subject, $matches);
    }
}
