<?php

function curl($link, $postfields = '', $cookie = '', $refer = '', $header = 1, $follow = 1, $usragent = 0, $proxy = false, $timeout = 10) {
    echo "\nUseragent: " . $usragent . "\n";

    $ch = curl_init($link);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    if ($header)
        curl_setopt($ch, CURLOPT_HEADER, 1);
    else
        curl_setopt($ch, CURLOPT_HEADER, 0);
    if ($follow)
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    else
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
    if ($usragent)
        curl_setopt($ch, CURLOPT_USERAGENT, $usragent);
    else
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1');
    if ($refer)
        curl_setopt($ch, CURLOPT_REFERER, $refer);
    if ($postfields) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
    }
    if ($cookie) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie);
    }
    if ($proxy) {
        curl_setopt($ch, CURLOPT_PROXY, $proxy);
    }
    if ($timeout) {
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    }

    $page = curl_exec($ch);

    curl_close($ch);

    if (empty($page)) {
        echo "<br/>Could not connect to host: <br/> $link <br/>";
    }
    else {
        return $page;
    }
}

//Случайная строка из файла
function randUa($file) {
    $uaFile = fopen($file, "r");  //открываем файл на чтение
    if (!$uaFile) {  //если файл не открывается
        echo "\nОшибка открытия файла!\n";
        exit(); //выходим
    }
    $numLineInFile = 0; //номер строки
    while (!feof($uaFile)) { //пока не дошли до конца файла 
        $buffDurty = fgets($uaFile, 1000);  //в буфер содержимое строки по 1000 символ, либо до конца строки
        $uaAll[$numLineInFile] = trim($buffDurty); //заносим в массив, обрубив пробелы
        $numLineInFile = $numLineInFile + 1; //следующий элемент массива
    }
    fclose($uaFile); //закрываем
    shuffle($uaAll); //перемешиваем
    return $uaAll[1];
}

//Из файла читаем строку по номеру
function getProxySite($file, $numLine = '1') {
    $proxySitesFile = fopen($file, "r");  //открываем файл на чтение
    if (!$proxySitesFile) {  //если файл не открывается
        echo "\nОшибка открытия файла!\n";
        exit(); //выходим
    }
    $numLineInFile = 0; //номер строки
    while (!feof($proxySitesFile)) { //пока не дошли до конца файла 
        $buffDurty = fgets($proxySitesFile, 1000);  //в буфер содержимое строки по 1000 символ, либо до конца строки
        $proxySitesAll[$numLineInFile] = trim($buffDurty); //заносим в массив, обрубив пробелы
        $numLineInFile = $numLineInFile + 1; //следующий элемент массива
    }
    fclose($proxySitesFile); //закрываем
    return $proxySitesAll[$numLine];
}

//Пишем в файл
function writeToFileArr($arrToFile, $file) {
    $outFile = fopen($file, "w");  //открываем файл на запись
    if (!$outFile) {  //если файл не открывается
        echo "\nОшибка открытия файла!\n";
        exit(); //выходим
    }
    echo "\nПишем!\n";
    for ($lineOutFile = 0; $lineOutFile < count($arrToFile); $lineOutFile++) { //выводим отфильтрованный и сортированный список прокси
        echo $arrToFile[$lineOutFile] . "\n";
        fwrite($outFile, $arrToFile[$lineOutFile] . "\n"); //выводим в файл
    }
    fclose($outFile); //закрываем
}

//Из файла читаем все строки
function readAllLines($file) {
    $currentFile = fopen($file, "x+");  //открываем файл или создаём
    fclose($currentFile); //закрываем
    $currentFile = fopen($file, "r");  //открываем файл на чтение
    if (!$currentFile) {  //если файл не открывается
        echo "\nОшибка открытия файла!\n";
        //exit();	//выходим
    }
    $numLineInFile = 0; //номер строки
    while (!feof($currentFile)) { //пока не дошли до конца файла 
        $buffDurty = fgets($currentFile, 1000);  //в буфер содержимое строки по 1000 символ, либо до конца строки
        $arrFromFile[$numLineInFile] = trim($buffDurty); //заносим в массив, обрубив пробелы
        $numLineInFile = $numLineInFile + 1; //следующий элемент массива
    }
    fclose($currentFile); //закрываем
    return $arrFromFile;
}

//Сырой текст в IP:port
function textToIpList($text) {
    echo $text;
    $text = strip_tags_smart($text);
    $proxiesNew = explode("\n", $text); //текст в массив
    for ($i = 0; $i < count($proxiesNew); $i++) { //перебираем, фильтруем IP
        $proxiesNew[$i] = trim($proxiesNew[$i]);

        if (preg_match('/\b((([-.a-z0-9]*)\.(\w{2,5}))|(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])):(\d{2,5})\b/', $proxiesNew[$i])) {
            $proxiesNew[$i] = preg_replace('/(.*?)(\b((([-.a-z0-9]*)\.(\w{2,5}))|(?:(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])\.){3}(?:25[0-5]|2[0-4][0-9]|1[0-9][0-9]|[1-9]?[0-9])):(\d{2,5})\b)(.*)/', '$2', $proxiesNew[$i]);
        }
        else {
            $proxiesNew[$i] = false; //убиваем мусорные строки-элементы массива
        }
    }
    $proxiesNew = array_filter($proxiesNew); //удаляем пустоты
    $proxiesNew = array_unique($proxiesNew); //уникализируем
    return $proxiesNew;
}

//Проверка запущенного процесса
class Thread {

    function RegisterPID($pidFile) {
        if ($fp = fopen($pidFile, 'w')) {
            fwrite($fp, getmypid());
            fclose($fp);
            @chmod($pidFile, 0777); // на случай если скрипт может запускаться от разных пользователей, либо вручную создайте этот файл и дайте ему 0777
            return true;
        }
        return false;
    }

    function CheckPIDExistance($pidFile) {
        if ($PID = @file_get_contents($pidFile)) {
            if (posix_kill($PID, 0))
                return true;
        }
        return false;
    }

    function KillPid($pidFile) {
        if ($PID = @file_get_contents($PIDFile))
            if (posix_kill($PID, 0))
                exec("kill -9 {$PID}");
    }

}

/*
 * 
 * If the field "cell" of 3D array consists entry, it will as 1, else 0.
 *
 * */
function fieldToBoolean3D($in, $cell) {
    foreach ($in as $key => $currentProxyVal) {
        if (isset($in[$key][$cell])) {
            $in[$key][$cell] = 1;
        }
        else {
            $in[$key][$cell] = 0;
        }
    }
    return ($in);
}

/*
 * 
 * The field "cell" from 3D $from insert into 3D $to, accordingly 2D $template.
 *  $template: 4 => 1, 5 => 2, 8 => 3
 * */
function cellFromAnother3DArray($from, $to, $cell, $template) {
    foreach ($to as $key => $val) {
        if (isset($template[$key])) {
            $to[$key][$cell] = $from [$template[$key]][$cell];
        }
    }
    return($to);
}

//function curlMultyProxyTest($link, $proxiesToCheck = false, $myIp = '', $directLink, $timeout = 20, $uaList)
function curlMultyProxyTest($testScriptUrl, $checkingProxy, $myIp, $yaMarketLink, $timeout = 20, $ua) {
    unset($person);
    $person = new CurlMulti();

//----------------- Checking my control script on host
    $resultFrom_multiCurl = $person->multyProxyBulkConnect($testScriptUrl, $checkingProxy, $timeout, 'Mozilla Firefox 52.1 / Windows NT6.3');
    // var_dump($person); 
    //var_dump($resultFrom_multiCurl); 
    $resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, '[REMOTE_ADDR]', 'content', 'remote_addr', 'content,proxy_ip', true);
    $resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, '[HTTP_X_FORWARDED_FOR]', 'content', 'x_forwarded_for', 'content,proxy_ip,remote_addr', true);
    $resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, 'test_query', 'content', 'test_query', 'x_forwarded_for,content,proxy_ip,remote_addr', true);
//var_dump($resultFrom_multiCurl);
    foreach ($resultFrom_multiCurl as $currentProxyKey => $currentProxyVal) {
        // Is the proxy alive?
        if (isset($currentProxyVal['remote_addr'])) {
            $checkingProxy[$currentProxyKey]['time'] = '0';  //Alive  
            if (isset($currentProxyVal['x_forwarded_for']) && strpos($currentProxyVal['x_forwarded_for'], $myIp) > 1) { //Проверка на анонимность если в строке мой IP 
                $checkingProxy[$currentProxyKey]['anm'] = '0';  // "Неанонимный!
            }
            else {
                $checkingProxy[$currentProxyKey]['anm'] = '1';  // "Анонимный!	
            }
            if (isset($currentProxyVal['test_query']) && strpos($currentProxyVal['test_query'], 'test_query') > 1) { //Проверка на QUERY если в строке  
                $checkingProxy[$currentProxyKey]['query'] = '1';
                echo $checkingProxy[$currentProxyKey]['proxy_ip']. " query проходит\n";
            }
            else {
                echo $checkingProxy[$currentProxyKey]['proxy_ip']. " query не проходит\n";
                $checkingProxy[$currentProxyKey]['query'] = '0';
            }
        }
        else {
            $checkingProxy[$currentProxyKey]['time'] = '1';
            echo $checkingProxy[$currentProxyKey]['proxy_ip']. " Timeout\n";
        }
        unset($checkingProxy[$currentProxyKey]['content']);
        unset($checkingProxy[$currentProxyKey]['test_query']);
        unset($checkingProxy[$currentProxyKey]['remote_addr']);
        unset($checkingProxy[$currentProxyKey]['x_forwarded_for']);
    }
// The current fields are "proxy_ip,anm,query,time"
//--------------------------- Collect only anonimous and query positive proxy. 
// We arrange new array of active proxy with new keys
// form "small cycle"
    echo "We are here! \n";
    //var_dump($checkingProxy);
    foreach ($checkingProxy as $currentProxyKey => $currentProxyVal) {
        if (isset($currentProxyVal['anm']) && $currentProxyVal['anm'] == 1 && isset($currentProxyVal['query']) && $currentProxyVal['query'] == 1) {
            $proxiesToCheckElit[]['proxy_ip'] = $checkingProxy[$currentProxyKey]['proxy_ip'];
            $controlKey[] = $currentProxyKey; // We create the control array for index and really keys.
        }
    }

    if (isset($controlKey) && isset($proxiesToCheckElit)) {
        $controlKey = array_flip($controlKey); //Flip array. Now it looks like 4 => 1, 5 => 2, 8 => 3  This is template to insert new fields
//var_dump($proxiesToCheckElit);
//----------------- Checking ya_market
        $resultFrom_multiCurl = $person->multyProxyBulkConnect($yaMarketLink, $proxiesToCheckElit, $timeout, 'Mozilla Firefox 52.1 / Windows NT6.3', 1);
// Determin if the ya_market is available
        $resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, '<meta property="og:title"', 'content', 'ya_market', 'proxy_ip,anm,query,time', true);

        $resultFrom_multiCurl = fieldToBoolean3D($resultFrom_multiCurl, 'ya_market');   // Now if the field "ya_market" consists entry, it will as 1, else 0.
//var_dump($resultFrom_multiCurl);

        $checkingProxy = cellFromAnother3DArray($resultFrom_multiCurl, $checkingProxy, 'ya_market', $controlKey);   // Now we reinstate sequences of keys and implement the new "ya_market" data to the array wich consists ip.
// The current fields are "ya_market,proxy_ip,anm,query,time"
// 
//----------------- Checking google_serp
        $resultFrom_multiCurl = $person->multyProxyBulkConnect('https://google.com', $proxiesToCheckElit, $timeout, 'Mozilla Firefox 52.1 / Windows NT6.3', 1);
// Determin if the google_serp is available
        $resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, 'rel="shortcut icon"><title>Google</title><script nonce=', 'content', 'google_serp', 'ya_market,proxy_ip,anm,query,time', true);
        $resultFrom_multiCurl = fieldToBoolean3D($resultFrom_multiCurl, 'google_serp');   // Now if the field "google_serp" consists entry, it will as 1, else 0.
//var_dump($resultFrom_multiCurl);
        $checkingProxy = cellFromAnother3DArray($resultFrom_multiCurl, $checkingProxy, 'google_serp', $controlKey); // Now we reinstate sequences of keys and implement the new "google_serp" data to the array wich consists ip.
// The current fields are "google_serp,ya_market,proxy_ip,anm,query,time"
    }
    return($checkingProxy);
}

// Filling empty cells by zero values
function fillEmptyCells($data) {
    if (!isset($data['ya_market']))
        $data['ya_market'] = '0';
    if (!isset($data['google_serp']))
        $data['google_serp'] = '0';
    if (!isset($data['anm']))
        $data['anm'] = '0';
    if (!isset($data['query']))
        $data['query'] = '0';
    return($data);
}

// Filling empty cells by zero values
function alignmentConditions($data, $conditions) {
    if ($conditions['anm'] == 2)
        $conditions['anm'] = $data['anm']; //если условие = 2 (неважно), приравниваем ко входным данным
    if ($conditions['query'] == 2)
        $conditions['query'] = $data['query']; //если условие = 2 (неважно), приравниваем ко входным данным
    if ($conditions['ya_market'] == 2)
        $conditions['ya_market'] = $data['ya_market']; //если условие = 2 (неважно), приравниваем ко входным данным
    if ($conditions['google_serp'] == 2)
        $conditions['google_serp'] = $data['google_serp']; //если условие = 2 (неважно), приравниваем ко входным данным
    //return($data);
    return($conditions);
}

function testAndDBWrite($sample, $testUrl, $myIp, $yaMarketLink, $timeout, $uaList, $conditions, $mysqli, $penaltyNewTime, $whatsCheck) {
    while ($row = $sample->fetch_assoc()) {
        $proxiesToCheck[]['proxy_ip'] = $row['proxy_ip']; //заносим результат в массив для проверки
    }
    $proxiesFromCheck = curlMultyProxyTest($testUrl, $proxiesToCheck, $myIp, $yaMarketLink, $timeout, $uaList); //тестируем
    //thorowg whole proxy list
    for ($i = 0; $i < count($proxiesFromCheck); $i++) {
        $proxiesFromCheck[$i] = fillEmptyCells($proxiesFromCheck[$i]);
        $cond = alignmentConditions($proxiesFromCheck[$i], $conditions);
        var_dump($cond);
        var_dump($conditions);
        var_dump($proxiesFromCheck[$i]);
        echo "CASE: " . $whatsCheck . "\n";
        if (($proxiesFromCheck[$i]['time'] == 0) && ($proxiesFromCheck[$i]['anm'] == $cond['anm']) && ($proxiesFromCheck[$i]['query'] == $cond['query']) && ($proxiesFromCheck[$i]['ya_market'] == $cond['ya_market']) && ($proxiesFromCheck[$i]['google_serp'] == $cond['google_serp'])) {

            if ($whatsCheck == 10) {
                echo '1 UPDATE ip_list_ok ' . $proxiesFromCheck[$i]['proxy_ip'] . "\n";
                $mysqli->query("UPDATE `ip_list_ok` SET `checked` ='" . time() . "', `worked` ='" . time() . "', `status`='" . $proxiesFromCheck[$i]['anm'] . $proxiesFromCheck[$i]['query'] . $proxiesFromCheck[$i]['ya_market'] . $proxiesFromCheck[$i]['google_serp'] . "' WHERE proxy_ip='" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }

            if ($whatsCheck == 20 OR $whatsCheck == 30) {
                echo '2 INSERT INTO ip_list_ok ' . $proxiesFromCheck[$i]['proxy_ip'] . "\n";
                $mysqli->query("INSERT INTO `ip_list_ok` (`proxy_ip`, `checked`, `worked`, `status`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "', '" . $proxiesFromCheck[$i]['anm'] . $proxiesFromCheck[$i]['query'] . $proxiesFromCheck[$i]['ya_market'] . $proxiesFromCheck[$i]['google_serp'] . "')"); //заносим в ip_list_ok
            }

            if ($whatsCheck == 20) {
                $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }

            if ($whatsCheck == 30) {
                $mysqli->query("DELETE FROM `ip_list_time` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }
        }
        // check condition for not OK list
        if (($proxiesFromCheck[$i]['time'] == 0) && (($proxiesFromCheck[$i]['anm'] != $cond['anm']) || ($proxiesFromCheck[$i]['ya_market'] != $cond['ya_market']) || ($proxiesFromCheck[$i]['query'] != $cond['query']) || ($proxiesFromCheck[$i]['google_serp'] != $cond['google_serp']))) {

            //10  //OK         //20': //NEW
            if ($whatsCheck == 10 OR $whatsCheck == 20 OR $whatsCheck == 30) {
                echo '3 INSERT INTO ip_list_substandard ' . $proxiesFromCheck[$i]['proxy_ip'] . "\n";
                $mysqli->query("INSERT INTO `ip_list_substandard` (`proxy_ip`, `checked`, `worked`, `status`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "', '" . $proxiesFromCheck[$i]['anm'] . $proxiesFromCheck[$i]['query'] . $proxiesFromCheck[$i]['ya_market'] . $proxiesFromCheck[$i]['google_serp'] . "' )"); //заносим в ip_list_substandard
            }
            if ($whatsCheck == 10) {
                $mysqli->query("DELETE FROM `ip_list_ok` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }
            if ($whatsCheck == 20) {
                echo '4 DELETE FROM ip_list_new ' . $proxiesFromCheck[$i]['proxy_ip'] . "\n";
                $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }
            if ($whatsCheck == 30) {
                $mysqli->query("DELETE FROM `ip_list_time` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }
        }
        // check condition for dead list
        if ($proxiesFromCheck[$i]['time'] == 1) { //проверяем условие недоступности
            if ($whatsCheck == 10) {
                echo '5 INSERT INTO ip_list_time ' . $proxiesFromCheck[$i]['proxy_ip'] . "\n";
                $mysqli->query("INSERT INTO `ip_list_time` (`proxy_ip`, `checked`, `not_worked`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "')"); //заносим в ip_list_bad
                $mysqli->query("DELETE FROM `ip_list_ok` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }
            if ($whatsCheck == 20) {
                echo '6 INSERT INTO ip_list_time ' . $proxiesFromCheck[$i]['proxy_ip'] . "\n";
                $mysqli->query("INSERT INTO `ip_list_time` (`proxy_ip`, `checked`, `not_worked`, `never`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . (time() - $penaltyNewTime) . "', true)"); //insert into ip_list_bad
                $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
            }
            if ($whatsCheck == 30) {
                echo '7 UPDATE ip_list_time ' . $proxiesFromCheck[$i]['proxy_ip'] . "\n";
                $mysqli->query("UPDATE `ip_list_time` SET `checked` = '" . time() . "' WHERE proxy_ip='" . $proxiesFromCheck[$i]['proxy_ip'] . "';\r");
            }
            //if ($whatsCheck == ) {
            //}
        }
    }
}
?>

