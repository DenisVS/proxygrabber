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
    $resultLongChecked = $mysqli->query("SELECT `proxy_ip` FROM `ip_list_ok` WHERE ((`checked` - `worked` + ". $minTimeCheck .") < (('" . time() . "' - `checked`)*" . $kTime . ")) OR (('" . time() . "' - `checked`) > '" . $maxTimeNoCheckOk . "' ) ORDER BY `checked` ASC LIMIT " . $testSize);
    //var_dump($resultLongChecked);
    $countIp = $resultLongChecked->num_rows; // How many rows with limited ok?
    if ($countIp == 0) {
        echo "Nothing ok!\n";
        break;
    }
    echo "For test are " . $countIp . " ok IP's\n"; //OK 10 10 10 10 10
    //var_dump($resultLongChecked);
    testAndDBWrite($resultLongChecked, $testUrl, $myIp, $yaMarketLink, $timeout, $uaList, $cond, $mysqli, $penaltyNewTime, '10');
} while ($countIp > ($testSize / 20));

////////////////
//*
//выбираем свежие, покуда их > $testSize/2
do {
    $proxiesToCheck = array();
    $resultLongChecked = $mysqli->query("SELECT proxy_ip FROM ip_list_new LIMIT " . $testSize);
    //var_dump($resultLongChecked);
    $countIp = $resultLongChecked->num_rows; // How many rows with limited fresh?
    if ($countIp == 0) {
        echo "Nothing new!\n";
        break;
    }
    echo "For test are " . $countIp . " new IP's\n"; //NEW 20 20 20 20 20 20
    testAndDBWrite($resultLongChecked, $testUrl, $myIp, $yaMarketLink, $timeout, $uaList, $cond, $mysqli, $penaltyNewTime, '20');
} while ($countIp > ($testSize / 2));
$mysqli->query("ALTER TABLE `ip_list_new` AUTO_INCREMENT = 1;");
//*/
/////////////////

//переносим переизбыток залежавшихся в never
echo "Может, мусор выкинем?\n";
$result = $mysqli->query("SELECT proxy_ip FROM `ip_list_time` WHERE `never` = true;");
$count = $result->num_rows; //сколько строк?
$filter = ($count-$limitNever);
if ($filter < 0) $filter = 0; 	//Чтобы меньше нуля не было
$resultNever = $mysqli->query("select * from `ip_list_time` WHERE `never` = true order by `not_worked` desc limit ".$filter.";");
while ($row = $resultNever->fetch_assoc()) {
	echo  $row['proxy_ip']." вставляем в never\n";
		$mysqli->query("INSERT INTO `ip_list_never` (`proxy_ip`, `moved`) VALUES ('".$row['proxy_ip']."', '".time()."');");	//заносим в ip_list_never
		$mysqli->query("DELETE FROM `ip_list_time` WHERE `proxy_ip` = '".$row['proxy_ip']."';");		
}

////////////////

//удаляем переизбыток залежавшихся
$result = $mysqli->query("SELECT proxy_ip FROM `ip_list_time` WHERE `never` = false;");
$count = $result->num_rows; //сколько строк?
$filter = ($count-$limitTime);
if ($filter < 0) $filter = 0; 	//Чтобы меньше нуля не было
$mysqli->query("delete from `ip_list_time` WHERE `never` = false order by `not_worked` desc limit ".$filter.";");

//////////

//удаляем переизбыток залежавшихся некондиционных. Вдруг мутировали?
$result = $mysqli->query("SELECT proxy_ip FROM `ip_list_substandard`;");
$count = $result->num_rows; //сколько строк?
$filter = ($count-$limitSubstandard);
if ($filter < 0) $filter = 0; 	//Чтобы меньше нуля не было
$mysqli->query("delete from `ip_list_substandard` order by `checked` desc limit ".$filter.";");

/////////


//выбираем имеющиеся нерабочие, с самых старых по дате проверки>0, покуда их > $testSize/5
$countIteration = 0; //счётчик
do {
    echo $countIteration . " итераций\n";
    $proxiesToCheck = array();
    $resultLongChecked = $mysqli->query("SELECT proxy_ip FROM ip_list_time 
	WHERE ((`checked` - `not_worked`)*'" . $distrustTime . "') < ('" . time() . "' - `checked`)
	ORDER BY `checked` ASC limit " . $testSize);
    //var_dump($resultLongChecked);
    $countIp = $resultLongChecked->num_rows; // How many rows with limited time?
    if ($countIp == 0) {
        echo "Nothing time!\n";
        break;
    }
    echo "For test are " . $countIp . " time IP's\n";
    testAndDBWrite($resultLongChecked, $testUrl, $myIp, $yaMarketLink, $timeout, $uaList, $cond, $mysqli, $penaltyNewTime, '30');
    $countIteration = $countIteration + 1; //счётчик
} while ($countIp > ($testSize / 5) && ($limitCheckOld / $testSize) > $countIteration);







?>
