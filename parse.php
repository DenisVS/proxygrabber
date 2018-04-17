<?php

//+”:8080″ +”:3128″ +”:80″ filetype:txt
//http://getfoxyproxy.org/proxylists.html
include "includes/config.php";
include "includes/functions.php";
include "includes/strip_tags_smart.php";

$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);  //тормозит скрипт при каждой ошибке 

$resultNewIp = $mysqli->query("SELECT * FROM ip_list_new;");
$amountNewIp = $resultNewIp->num_rows; //сколько строк со свежатиной?
//var_dump($amountNewIp);
$resultAnmIp = $mysqli->query("SELECT * FROM ip_list_ok;");
$amountAnmIp = $resultAnmIp->num_rows; // How many rows in anonimous table?
//var_dump($amountAnmIp);

if (($amountNewIp < $minimumNew) && ($amountAnmIp < $minimumOk)) { //если мало, парсим
    $resultNumSite = $mysqli->query("SELECT value FROM settings WHERE `param` = 'site_num';"); // Current site index number in table
    $rowNumSite = $resultNumSite->fetch_assoc();
    echo 'Current site index number in table $rowNumSite["value"]:  ' . $rowNumSite['value'] . "\n";
    echo '$amountNewIp  ' . $amountNewIp . "\n";
    $resultAmountUrl = $mysqli->query("SELECT site_url FROM proxy_sites_list;");
    $amountUrl = $resultAmountUrl->num_rows; //количество URL
    echo '$amountUrl  ' . $amountUrl . "\n";
    if ($rowNumSite['value'] >= $amountUrl) { //если дошли до последнего
        $result = $mysqli->query("SELECT * FROM proxy_sites_list WHERE `id` = (SELECT MIN(id) FROM proxy_sites_list WHERE `id` > '0');"); //reset to zero
    } else {
        $result = $mysqli->query("SELECT * FROM proxy_sites_list WHERE `id` = (SELECT MIN(id) FROM proxy_sites_list WHERE `id` > '" . $rowNumSite['value'] . "');");
    }
    $row = $result->fetch_assoc(); // Get URL from array
    $mysqli->query("UPDATE settings SET value='" . $row['id'] . "' WHERE `param` = 'site_num';");
    echo 'Fetched site index number in table $rowNumSite["value"]:  ' . $row['id'] . "\n";
$row['site_url'] = 'https://premproxy.com/list/time-01.htm';
    $newProxies = array();
    //$text = curl ($row['site_url'],'','includes/cookies.txt','',0, 0, randUa($uaList));	//	Fetch URL
    $text = curl($row['site_url'], '', 'includes/cookies.txt', '', 0, 1, randUa($uaList)); //	Fetch URL
    $newProxies = textToIpList($text); //	Raw result for processing
    $newProxies = array_unique($newProxies); //уникализируем
    $newProxies = array_filter($newProxies); //удаляем пустоты
    sort($newProxies, SORT_NUMERIC); //сортируем по порядку
    echo count($newProxies) . " всего\n";
    echo $row['site_url'] . "\n";
    $plusIp = 0; //счётчик добавленных
    echo "count newProxies: ";
    var_dump(count($newProxies));
    for ($i = 0; ($i < count($newProxies) && ($plusIp < $limitParseNew)); $i++) { //перебираем надёрганные IP	
        $countIpAll = 0; //Суммарно во всех таблицах пока нету такого IP
        $resultCount = $mysqli->query("SELECT * FROM ip_list_new WHERE proxy_ip ='" . $newProxies[$i] . "'");
        //$countIp = $resultCount->fetch_assoc();
        $countIp = $resultCount->num_rows; //How much rows with such IP?
        //var_dump ($countIp);
//echo "countIpAll countIp". $countIpAll ." - ". $countIp . "\n";
        $countIpAll = ($countIpAll + $countIp);
        $resultCount = $mysqli->query("SELECT * FROM ip_list_ok WHERE proxy_ip ='" . $newProxies[$i] . "'");
        $countIp = $resultCount->num_rows; //сколько строк с таким IP  в ip_list_ok?
        //var_dump ($countIp);
        //var_dump ($countIpAll);
        $countIpAll = ($countIpAll + $countIp);
        $resultCount = $mysqli->query("SELECT * FROM ip_list_substandard WHERE proxy_ip ='" . $newProxies[$i] . "'");
        $countIp = $resultCount->num_rows; //сколько строк с таким IP  в ip_list_substandard?
        $countIpAll = ($countIpAll + $countIp);
        $resultCount = $mysqli->query("SELECT * FROM ip_list_time WHERE proxy_ip ='" . $newProxies[$i] . "'");
        $countIp = $resultCount->num_rows; //сколько строк с таким IP  в ip_list_time?
        $countIpAll = ($countIpAll + $countIp);
        $resultCount = $mysqli->query("SELECT * FROM ip_list_never WHERE proxy_ip ='" . $newProxies[$i] . "'");
        $countIp = $resultCount->num_rows; //сколько строк с таким IP  в ip_list_never? 
        $countIpAll = ($countIpAll + $countIp);
        //echo count($newProxies)." - ".$i." Количество имеющихся с IP ".$newProxies[$i].": ".$countIpAll."\n";
        if ($countIpAll == 0) { //если нету
            $resultCount = $mysqli->query("INSERT INTO ip_list_new (proxy_ip) VALUES ( '$newProxies[$i]')"); //заносим
            $plusIp = $plusIp + 1; //счётчик добавленных
        }
    }
} else {
    echo "Хватит пока, надо имеющееся проверить \n";
}
?>
