<?php

declare(strict_types=1);

require_once __DIR__ . '/src/functions.php';
require_once __DIR__ . '/src/handler.php';

$autoload = __DIR__ . '/vendor/autoload.php';
if (!is_file($autoload)) {
    exit('Run "php composer.phar install" in lab_7 to install Twig.');
}

require_once $autoload;

use Twig\Environment;
use Twig\Extra\Html\HtmlExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFilter;

$loader = new FilesystemLoader(__DIR__ . '/templates/twig');
$twig = new Environment($loader, [
    'cache' => false,
    'autoescape' => 'html',
]);

if (class_exists(HtmlExtension::class)) {
    $twig->addExtension(new HtmlExtension());
}

$twig->addFilter(new TwigFilter('format_sleep', 'format_sleep'));

echo $twig->render('home.html.twig', handle_mood_request(__DIR__ . '/data.json'));
