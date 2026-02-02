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

if (getenv('TRAVIS')) {
    $pdo = new PDO('mysql:dbname=fluentdb;host=localhost;charset=utf8', 'root');
} else {
    $pdo = new PDO('mysql:dbname=fluentdb;host=localhost;charset=utf8', 'vagrant', 'vagrant');
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
