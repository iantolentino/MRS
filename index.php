<?php
declare(strict_types=1);

const APP_NAME = 'Strata Room Scheduler';

require_once __DIR__ . '/database/Database.php';
require_once __DIR__ . '/authentication/Auth.php';
require_once __DIR__ . '/backend/helpers.php';

$p = db();
if (isset($_GET['api']) && $_GET['api'] !== '') {
    require __DIR__ . '/backend/api.php';
    exit;
}

$me = user();
$setup = (int)$p->query('SELECT COUNT(*) FROM users')->fetchColumn() === 0;
require __DIR__ . '/frontend/index.php';
