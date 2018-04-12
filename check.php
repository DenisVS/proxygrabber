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
//выбираем имеющиеся рабочие ok, с самых старых по дате проверки>0, покуда их > $testSize/20
do {
    $proxiesToCheck = array();
    $resultLongChecked = $mysqli->query("SELECT `proxy_ip` FROM `ip_list_ok` WHERE ((`checked` - `worked`) < (('" . time() . "' - `checked`)*" . $kTime . ")) OR (('" . time() . "' - `checked`) > '" . $maxTimeNoCheckOk . "' ) ORDER BY `checked` ASC LIMIT " . $testSize);
    //var_dump($resultLongChecked);
    $countIp = $resultLongChecked->num_rows; // How many rows with limited fresh?
    if ($countIp == 0) {
        echo "Nothing!\n";
        break;
    }
    echo "For test are " . $countIp . " ok IP's\n";
    testAndDBWrite($resultLongChecked, $testUrl, $myIp, $yaMarketLink, $timeout, $uaList, $cond, $mysqli, $penaltyNewTime, 'ip_list_ok');
} while ($countIp > ($testSize / 20));

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
    testAndDBWrite($resultLongChecked, $testUrl, $myIp, $yaMarketLink, $timeout, $uaList, $cond, $mysqli, $penaltyNewTime, 'ip_list_new');
} while ($countIp > ($testSize / 2));
$mysqli->query("ALTER TABLE `ip_list_new` AUTO_INCREMENT = 1;");

//выбираем имеющиеся нерабочие, с самых старых по дате проверки>0, покуда их > $testSize/5
$countIteration = 0; //счётчик
do {
    echo $countIteration . " итераций\n";
    $proxiesToCheck = array();
    $resultLongChecked = mysql_query("SELECT proxy_ip FROM ip_list_time 
	WHERE ((`checked` - `not_worked`)*'" . $distrustTime . "') < ('" . time() . "' - `checked`)
	ORDER BY `checked` ASC limit " . $testSize);
    //var_dump($resultLongChecked);
    $countIp = $resultLongChecked->num_rows; // How many rows with limited fresh?
    if ($countIp == 0) {
        echo "Nothing!\n";
        break;
    }
    echo "For test are " . $countIp . " time IP's\n";
    testAndDBWrite($resultLongChecked, $testUrl, $myIp, $yaMarketLink, $timeout, $uaList, $cond, $mysqli, $penaltyNewTime, 'ip_list_time');
    $countIteration = $countIteration + 1; //счётчик
} while ($countIp > ($testSize / 5) && ($limitCheckOld / $testSize) > $countIteration);


/*
  while ($row = $resultLongChecked->fetch_assoc()) {
  $proxiesToCheck[]['proxy_ip'] = $row['proxy_ip']; //заносим результат в массив для проверки
  }
  //var_dump($proxiesToCheck);
  $proxiesFromCheck = curlMultyProxyTest($testUrl, $proxiesToCheck, $myIp, $yaMarketLink, $timeout, $uaList); //тестируем
  //echo 'Now we will list proxies from check:';
  $cond = alignmentConditions($cond, $proxiesFromCheck);
  //thorowg whole proxy list
  for ($i = 0; $i < count($proxiesFromCheck); $i++) {
  $proxiesFromCheck[$i] = fillEmptyCells($proxiesFromCheck[$i]);
  if (($proxiesFromCheck[$i]['time'] == 0) && ($proxiesFromCheck[$i]['anm'] == $cond['anm']) && ($proxiesFromCheck[$i]['query'] == $cond['query']) && ($proxiesFromCheck[$i]['ya_market'] == $cond['ya_market']) && ($proxiesFromCheck[$i]['google_serp'] == $cond['google_serp'])) {
  $mysqli->query("INSERT INTO `ip_list_ok` (`proxy_ip`, `checked`, `worked`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "')"); //заносим в ip_list_ok
  $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
  }
  // check condition for not OK list
  if (($proxiesFromCheck[$i]['time'] == 0) && (($proxiesFromCheck[$i]['anm'] != $cond['anm']) || ($proxiesFromCheck[$i]['ya_market'] != $cond['ya_market']) || ($proxiesFromCheck[$i]['query'] != $cond['query']) || ($proxiesFromCheck[$i]['google_serp'] != $cond['google_serp']))) {
  $mysqli->query("INSERT INTO `ip_list_substandard` (`proxy_ip`, `checked`, `worked`, `status`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "', '" . $proxiesFromCheck[$i]['anm'] . $proxiesFromCheck[$i]['query'] . $proxiesFromCheck[$i]['ya_market'] . $proxiesFromCheck[$i]['google_serp'] . "' )"); //заносим в ip_list_substandard
  $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
  }
  // check condition for dead list
  if ($proxiesFromCheck[$i]['time'] == 1) { //проверяем условие недоступности
  $mysqli->query("INSERT INTO `ip_list_time` (`proxy_ip`, `checked`, `not_worked`, `never`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . (time() - $penaltyNewTime) . "', true)"); //insert into ip_list_bad
  $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
  }
  }

 */
?>
