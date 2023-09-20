<?


trait Authorization
{

    static function isAllowed($mod = "", $act = "", $redirect = true)
    {
        if (IS_ADMIN) return true;

        global $module;
        global $action;
        $mod = $mod ?: $module;
        $act = $act ?: $action;
        $allowed = RoleRights::isAllowed($mod, $act, $_SESSION['member']['roleid']);
        if ($redirect) {
            if (!$allowed) redirect('authenticate', 'access_page');
        }

        return $allowed;
    }

    /**
     * if can perform the action
     *
     * @param $other_right_action
     *
     * @param bool $redirect redirects to access denied if fails
     * @return bool
     */
    static function can($other_right_action, $redirect = false)
    {
        $user = [];
        if (isset($_SESSION['member'])) {
            $user = $_SESSION['member'];
        } else if (defined('AUTH_USER')) {
            $user = AUTH_USER;
        }
        if (empty($user)) return false;
        if ($user['roleid'] == 1) return true;//admin
        $access = RoleRights::fromAction($other_right_action, $user['roleid']);
        if ($redirect && !$access) {
            $label = OtherRights::$otherRightClass->find(['action' => $other_right_action])[0]['label'];
//            debug($label);
            redirect('authenticate', 'access_page', ['right_action' => base64_encode($label)]);
        }
        return $access;
    }

    /**
     * if cannot perform the action
     *
     * @param $other_right_action
     *
     * @param bool $redirect redirects to access denied if fails
     * @return bool
     */
    static function cannot($other_right_action, $redirect = false)
    {
        $access = !self::can($other_right_action);
        if ($redirect && !$access) {
            $label = OtherRights::$otherRightClass->find(['action' => $other_right_action])['label'];
            redirect('authenticate', 'access_page', ['right_action' => base64_encode($label)]);
        }
        return $access;
    }

}