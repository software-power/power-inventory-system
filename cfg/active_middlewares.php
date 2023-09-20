<?

$middlewares = [
    'middlewares/user_control.php',
];

foreach ($middlewares as $path) require $path;

