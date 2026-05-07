<?php

/**
 * @var string $page_title
 * @var string $currentView
 * @var string $template
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($page_title) ?></title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #ffffff;
            color: #222222;
        }

        .container {
            max-width: 980px;
            margin: 20px auto;
            padding: 0 16px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 12px;
        }

        .nav a {
            margin-left: 10px;
            color: #333333;
        }

        .card {
            border: 1px solid #dddddd;
            border-radius: 6px;
            padding: 18px;
            margin-bottom: 18px;
            background: #ffffff;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 12px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input,
        select,
        textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 8px;
            border: 1px solid #bbbbbb;
            border-radius: 4px;
            font-size: 14px;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .field {
            margin-bottom: 14px;
        }

        .checkbox label {
            display: inline-block;
            margin-right: 14px;
            font-weight: normal;
        }

        .checkbox input {
            width: auto;
            margin-right: 4px;
        }

        .error {
            color: #a30000;
            font-size: 13px;
            margin-top: 4px;
        }

        .success {
            margin-bottom: 14px;
            padding: 10px;
            border: 1px solid #9bbf9b;
            background: #f1faf1;
        }

        .button {
            padding: 9px 14px;
            border: 1px solid #999999;
            border-radius: 4px;
            background: #f4f4f4;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        th {
            background: #f5f5f5;
        }

        th a {
            color: #222222;
            text-decoration: none;
        }

        .muted {
            color: #666666;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <main class="container">
        <div class="topbar">
            <div>
                <h1><?= h($page_title) ?></h1>
                <div class="muted"><?= h($currentView) ?></div>
            </div>
            <nav class="nav">
                <a href="index.php">PHP templates</a>
                <a href="twig.php">Twig templates</a>
            </nav>
        </div>

        <?php require $template; ?>
    </main>
</body>
</html>
