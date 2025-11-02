<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/minishlink/web-push/src/VAPID.php';

$keys = Minishlink\WebPush\VAPID::createVapidKeys();
echo json_encode($keys, JSON_PRETTY_PRINT), PHP_EOL;


