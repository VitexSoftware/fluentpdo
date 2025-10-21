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

if (getenv('TRAVIS')) {
    $pdo = new PDO('mysql:dbname=fluentdb;host=localhost;charset=utf8', 'root');
} else {
    $pdo = new PDO('mysql:dbname=fluentdb;host=localhost;charset=utf8', 'vagrant', 'vagrant');
}

$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
$pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
