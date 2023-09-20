<?

global $NO_AUTHORIZATION;
$NO_AUTHORIZATION = [
    // module   =>  [actions..]
    'testing' => [],
    'authenticate' => ['get_token'],

    'endpoints' => [
        'pos_display', 'ajax_searchProduct','print_barcode'
    ]
];

if (!in_array($module, array_keys($NO_AUTHORIZATION))) {
    include 'authorization.php';
} else {
    if (!empty($NO_AUTHORIZATION[$module]) && !in_array($action, $NO_AUTHORIZATION[$module])) {
        include 'authorization.php';
    }
}
//authorize
//    if ($module != 'testing' && ($module != 'authenticate' && $action != 'get_token')) include 'authorization.php';
