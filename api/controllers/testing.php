<?

if ($action == 'main_system_connection') {
    required_method('POST');
    try {
        $main_token = base64_decode($request_data['main']);
        $subsystem_token = base64_decode($request_data['system']);
        if (empty($main_token)) throw new Exception("Invalid main system token");
        if (empty($subsystem_token)) throw new Exception("Invalid system token");

        if (!CS_MAIN_SYSTEM) throw new Exception("Not a main system");

        $sub_system = SystemTokens::$staticClass->find(['token' => $subsystem_token]);
        if (empty($sub_system)) throw new Exception("This system is not registered by the main system");
        if (CS_SYSTEM_TOKEN === $main_token) {
            json_response(['status' => 'success', 'msg' => "Hello {$sub_system[0]['name']} \nConnection successfully"]);
        } else {
            throw new Exception("Token mismatch");
        }
    } catch (Exception $e) {
        json_response(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}

if ($action == 'sub_system_connection') {
    required_method('POST');
    try {
        $main_token = base64_decode($request_data['main']);
        $subsystem_token = base64_decode($request_data['sub_token']);
        if (empty($main_token)) throw new Exception("Invalid main system token");
        if (empty($subsystem_token)) throw new Exception("Invalid system token");

        if (CS_MAIN_SYSTEM) throw new Exception("Not a sub system");
        if(CS_MAIN_SYSTEM_TOKEN!==$main_token) throw new Exception("Main system token mismatch");
        if (CS_SYSTEM_TOKEN === $subsystem_token) {
            json_response(['status' => 'success', 'msg' => "Hello Main \nConnection successfully"]);
        } else {
            throw new Exception("Sub system token mismatch");
        }
    } catch (Exception $e) {
        json_response(['status' => 'error', 'msg' => $e->getMessage()]);
    }
}
