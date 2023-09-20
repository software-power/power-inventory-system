<?

if (isset(LICENSE_MODULES['usr']) && LICENSE_MODULES['usr'] > 0 && Users::$userClass->countWhere(['status' => 'active']) > LICENSE_MODULES['usr']) {
//    debug(LICENSE_MODULES);
    //redirect to user selection screen
    if (($module != 'authenticate') &&(($module != 'users' && $module != 'user') || ($action != 'choose_active_user' && $action != 'save_active_user'))) redirect('users', 'choose_active_user');
}

