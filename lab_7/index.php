<?php

declare(strict_types=1);

require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/handler.php';

$view = handle_mood_request(__DIR__ . '/data.json');

extract($view, EXTR_SKIP);

$template = __DIR__ . '/templates/php/home.php';
$currentView = 'Native PHP templates';

require __DIR__ . '/templates/php/layout.php';
