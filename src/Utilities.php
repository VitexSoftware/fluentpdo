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

namespace Envms\FluentPDO;

/**
 * Class Utilities.
 */
class Utilities
{
    /**
     * Convert "camelCaseWord" to "CAMEL CASE WORD".
     *
     * @param string $string The camelCase string to convert
     *
     * @return string The converted upper case string with spaces
     */
    public static function toUpperWords(string $string): string
    {
        $regex = new Regex();

        return trim(strtoupper($regex->camelCaseSpaced($string)));
    }

    /**
     * Format SQL query by splitting clauses and removing line end whitespace.
     *
     * @param string $query The SQL query string to format
     *
     * @return string The formatted query string
     */
    public static function formatQuery(string $query): string
    {
        $regex = new Regex();

        $query = $regex->splitClauses($query);
        $query = $regex->splitSubClauses($query);

        return $regex->removeLineEndWhitespace($query);
    }

    /**
     * Converts columns from strings to types according to PDOStatement::columnMeta().
     *
     * @param \PDOStatement $statement The PDO statement to get column metadata from
     * @param mixed         $rows      Rows provided by PDOStatement::fetch with PDO::FETCH_ASSOC
     *
     * @return mixed The rows with numeric columns converted to proper types
     */
    public static function stringToNumeric(\PDOStatement $statement, mixed $rows): mixed
    {
        for ($i = 0; ($columnMeta = $statement->getColumnMeta($i)) !== false; ++$i) {
            $type = $columnMeta['native_type'];

            switch ($type) {
                case 'DECIMAL':
                case 'DOUBLE':
                case 'FLOAT':
                case 'INT24':
                case 'LONG':
                case 'LONGLONG':
                case 'NEWDECIMAL':
                case 'SHORT':
                case 'TINY':
                    if (isset($rows[$columnMeta['name']])) {
                        $rows[$columnMeta['name']] += 0;
                    } else {
                        if (\is_array($rows) || $rows instanceof \Traversable) {
                            foreach ($rows as &$row) {
                                if (isset($row[$columnMeta['name']])) {
                                    $row[$columnMeta['name']] += 0;
                                }
                            }

                            unset($row);
                        }
                    }

                    break;

                default:
                    // return as string
                    break;
            }
        }

        return $rows;
    }

    /**
     * Convert SQL write values, handling arrays and individual values.
     *
     * @param mixed $value The value or array of values to convert
     *
     * @return mixed The converted value(s)
     */
    public static function convertSqlWriteValues(mixed $value): mixed
    {
        if (\is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::convertValue($v);
            }
        } else {
            $value = self::convertValue($value);
        }

        return $value;
    }

    /**
     * Convert a single value for SQL writing (boolean to int, array to JSON).
     *
     * @param mixed $value The value to convert
     *
     * @return mixed The converted value
     */
    public static function convertValue(mixed $value): mixed
    {
        switch (\gettype($value)) {
            case 'boolean':
                $conversion = ($value) ? 1 : 0;

                break;
            case 'array':
                $conversion = json_encode($value);

                break;

            default:
                $conversion = $value;

                break;
        }

        return $conversion;
    }

    /**
     * Check if a subject is countable (array or implements Countable interface).
     *
     * @param mixed $subject The subject to check
     *
     * @return bool True if the subject is countable, false otherwise
     */
    public static function isCountable(mixed $subject): bool
    {
        return \is_array($subject) || ($subject instanceof \Countable);
    }

    /**
     * Convert null values to Literal('NULL') for SQL queries.
     *
     * @param mixed $value The value to check and convert
     *
     * @return mixed The original value or Literal('NULL') if value was null
     */
    public static function nullToLiteral(mixed $value): mixed
    {
        if ($value === null) {
            return new Literal('NULL');
        }

        return $value;
    }
}
