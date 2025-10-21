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
 * Class Utilities.
 */
class Utilities
{
    /**
     * Convert "camelCaseWord" to "CAMEL CASE WORD".
     */
    public static function toUpperWords(string $string): string
    {
        $regex = new Regex();

        return trim(strtoupper($regex->camelCaseSpaced($string)));
    }

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
     * @param array|\Traversable $rows - provided by PDOStatement::fetch with PDO::FETCH_ASSOC
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

    public static function isCountable(mixed $subject): bool
    {
        return \is_array($subject) || ($subject instanceof \Countable);
    }

    public static function nullToLiteral(mixed $value): mixed
    {
        if ($value === null) {
            return new Literal('NULL');
        }

        return $value;
    }
}
