<?php

use Lemon\Cache;
use Lemon\Config;

include 'public/index.php';

Config::part('kernel')->set('mode', 'terminal');

if ($argc < 2) {
    return;
}

Cache::setMultiple([
    'name' => $argv[1],
    'password' => password_hash($argv[2], PASSWORD_ARGON2ID),
]);


