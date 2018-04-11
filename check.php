<?php

/*
 * countIp -- rows with old filtered IP's (ip_list_ok )
 * 
 * 
 * */




include "includes/config.php";
include "includes/functions.php";
spl_autoload_register(function ($class) {
    include 'includes/classes/' . $class . '.class.php';
});

//Проверяем, запущен ли скрипт
$thread = new Thread();
if ($thread->CheckPIDExistance('/tmp' . '/' . PID_FILE))
    die("ERROR: Only one copy of the script could be executed at the same time\n");
if (!$thread->RegisterPID('/tmp' . '/' . PID_FILE))
    die("ERROR: Cannot register script's PID\n");

$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);


//$proxiesToCheck: [0]["proxy_ip"] => "110.77.206.77:42619"
/////////////////////////////////////////////////
//выбираем свежие, покуда их > $testSize/2
do {
    $proxiesToCheck = array();
    $resultLongChecked = $mysqli->query("SELECT proxy_ip FROM ip_list_new LIMIT " . $testSize);
    //var_dump($resultLongChecked);
    $countIp = $resultLongChecked->num_rows; // How many rows with limited fresh?
    if ($countIp == 0) {
        echo "Nothing!\n";
        break;
    }
    echo "For test are " . $countIp . " new IP's\n";
    while ($row = $resultLongChecked->fetch_assoc()) {
        $proxiesToCheck[]['proxy_ip'] = $row['proxy_ip']; //заносим результат в массив для проверки
    }
    //var_dump($proxiesToCheck);
    $proxiesFromCheck = curlMultyProxyTest($testUrl, $proxiesToCheck, $myIp, $yaMarketLink, $timeout, $uaList); //тестируем
    //echo 'Now we will list proxies from check:';
    //var_dump($proxiesFromCheck);
    var_dump($proxiesFromCheck);
    $cond = alignmentConditions($cond, $proxiesFromCheck);

    //thorowg whole proxy list
    for ($i = 0; $i < count($proxiesFromCheck); $i++) {
        //var_dump($proxiesFromCheck[$i]);
        $proxiesFromCheck[$i] = fillEmptyCells($proxiesFromCheck[$i]);
        
        // check condition for OK list
        if (($proxiesFromCheck[$i]['time'] == 0) && ($proxiesFromCheck[$i]['anm'] == $cond['anm']) && ($proxiesFromCheck[$i]['query'] == $cond['query']) && ($proxiesFromCheck[$i]['ya_market'] == $cond['ya_market']) && ($proxiesFromCheck[$i]['google_serp'] == $cond['google_serp'])) {
            echo "insert OK".$proxiesFromCheck[$i]['proxy_ip'];
            $mysqli->query("INSERT INTO `ip_list_ok` (`proxy_ip`, `checked`, `worked`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "')"); //заносим в ip_list_ok
            $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
        }
        // check condition for not OK list
        if (($proxiesFromCheck[$i]['time'] == 0) && (($proxiesFromCheck[$i]['anm'] != $cond['anm']) || ($proxiesFromCheck[$i]['ya_market'] != $cond['ya_market']) || ($proxiesFromCheck[$i]['query'] != $cond['query']) || ($proxiesFromCheck[$i]['google_serp'] != $cond['google_serp']))) {
            echo "insert not OK".$proxiesFromCheck[$i]['proxy_ip']."\n";
            echo "INSERT INTO `ip_list_substandard` (`proxy_ip`, `checked`, `worked`, `status`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "', '" . $proxiesFromCheck[$i]['anm'] . $proxiesFromCheck[$i]['query'] . $proxiesFromCheck[$i]['ya_market'] . "' )\n";
            
            $mysqli->query("INSERT INTO `ip_list_substandard` (`proxy_ip`, `checked`, `worked`, `status`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "', '" . $proxiesFromCheck[$i]['anm'] . $proxiesFromCheck[$i]['query'] . $proxiesFromCheck[$i]['ya_market'] . "' )"); //заносим в ip_list_substandard
            $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
        }
        // check condition for dead list
        if ($proxiesFromCheck[$i]['time'] == 1) { //проверяем условие недоступности
            echo "insert time".$proxiesFromCheck[$i]['proxy_ip'];
            $mysqli->query("INSERT INTO `ip_list_time` (`proxy_ip`, `checked`, `not_worked`, `never`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . (time() - $penaltyNewTime) . "', true)"); //insert into ip_list_bad
            $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
        }
    }
} while ($countIp > ($testSize / 2));
$mysqli->query("ALTER TABLE `ip_list_new` AUTO_INCREMENT = 1;");



?>
