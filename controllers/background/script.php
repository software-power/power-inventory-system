<?
try {
    include 'vars.php';

    $script_log_file = __DIR__ . "/script.log";

    $scripts = [
        //expire notifications
        'expire_notify' => [
            'name' => 'expire_notification.php',
            'autorun' => true,
        ],

        //expire notifications
        'stock_notify' => [
            'name' => 'stock_level_notification.php',
            'autorun' => true,
        ],

        //supplier payment notifications
        'supplier_notify' => [
            'name' => 'supplier_notification.php',
            'autorun' => true,
        ],

        //clear old data
        'clear' => [
            'name' => 'clear_old_data.php',
            'autorun' => true,
        ],
    ];

    logData("\t ---- SCRIPT START ----", $script_log_file);

    $args = $_SERVER['argv'];
//    sleep(20);
//    for ($i = 0; $i < 10000; $i++) {
//        $rand = rand(0, 1249817204981270491);
//        usleep(10);
//        logData($rand, 'output_test.log');
//    }

//    debug($scripts);

    if (count($args) < 2) {
        //has query been run today
        if (ScriptLogs::$class->countWhere(['date' => TODAY]) == 0) {
            logData("AUTO SCRIPT RUN", $script_log_file);
            //mark as run before query runs to avoid duplicates in multi user situation
            ScriptLogs::$class->insert(array('date' => TODAY));
            //run scripts
            foreach ($scripts as $s) {
                if ($s['autorun']) include $s['name'];
            }
        } else {
            throw new Exception("Script already run today");
        }
    } else {
        $extract = explode('=', $args[1]);
        if ($extract[0] != '--scripts') throw new Exception("Invalid argument '{$extract[0]}'");
        if (empty($extract[1])) throw new Exception("No script specified!");
        $selected_scripts = explode(',', $extract[1]);
        foreach ($selected_scripts as $s) {
            if (!in_array($s, array_keys($scripts))) throw new Exception("Script '$s' not found!");
        }
//        debug($selected_scripts);
        logData("MANUAL SCRIPT RUN", $script_log_file);

        foreach ($selected_scripts as $s) include $scripts[$s]['name'];
    }

} catch (Exception $e) {
    logData("Error: " . $e->getMessage(), $script_log_file);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()], JSON_PRETTY_PRINT);
}

logData("\t ---- SCRIPT END ----\n", $script_log_file);


exit();