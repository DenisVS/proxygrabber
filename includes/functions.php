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

function SSScurlMultyProxyTest($link, $proxiesToCheck = false, $myIp = '', $directLink, $timeout = 20, $uaList) {
    for ($proxyCount = 0; $proxyCount < count($proxiesToCheck); $proxyCount++) { //
        $ch[$proxyCount] = curl_init($link . "?test_query");
        curl_setopt($ch[$proxyCount], CURLOPT_PROXY, $proxiesToCheck[$proxyCount]);
        curl_setopt($ch[$proxyCount], CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch[$proxyCount], CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch[$proxyCount], CURLOPT_POST, 0);
        //curl_setopt($ch[$proxyCount], CURLOPT_USERAGENT, randUa($uaList));
    }
    $mh = curl_multi_init();
    for ($proxyCount = 0; $proxyCount < count($proxiesToCheck); $proxyCount++) { //
        curl_multi_add_handle($mh, $ch[$proxyCount]);
    }
    $running = null;
    do { //execute the handles
        curl_multi_exec($mh, $running);
    } while ($running > 0);

    for ($proxyCount = 0; $proxyCount < count($proxiesToCheck); $proxyCount++) { //
        // GET RESULTS AND SEPARATE BY STRINGS 
        $testResult = curl_multi_getcontent($ch[$proxyCount]); //получаем результат запроса
        echo $testResult . "  !!\n"; //
        $testResult = explode("\n", $testResult); //По строкам бьём текцщий забор
        // PARSE EACH STRING OF CURRENT RESULT
        $tempResultString = null; //обнуляем индикатор вхождения HTTP_X_FORWARDED_FOR
        $tempResultString2 = null; //обнуляем индикатор вхождения [QUERY_STRING\]
        for ($numLineResult = 0; $numLineResult < count($testResult); $numLineResult++) { //по строкам текущего забора
            //echo $testResult[$numLineResult]."\n";
            if (preg_match('/\[HTTP_X_FORWARDED_FOR\]/', $testResult[$numLineResult])) {
                $tempResultString = $testResult[$numLineResult]; //в индикатор содержимое строки со вхождением, для обработки
            }
            if (preg_match('/\[QUERY_STRING\]/', $testResult[$numLineResult])) {
                $tempResultString2 = $testResult[$numLineResult]; //в индикатор содержимое строки со вхождением, для обработки
            }
        }


        // CHECK IF PARSE STRING IS PRESENT
        if ($tempResultString == null) { //если не встретилось вхождение (дохлый прокси)
            $proxiesFromCheck [$proxyCount]['ip'] = $proxiesToCheck[$proxyCount];
            $proxiesFromCheck [$proxyCount]['time'] = '1';
            echo "Timeout\n";
        }
        else {
            $proxiesFromCheck [$proxyCount]['time'] = '0'; //жив
            if (strpos($tempResultString, $myIp) > 1) { //Проверка на анонимность если в строке мой IP 
                $proxiesFromCheck [$proxyCount]['ip'] = $proxiesToCheck[$proxyCount];
                $proxiesFromCheck [$proxyCount]['anm'] = '0';
                echo "Неанонимный! ";
            }
            else {
                echo "Анонимный! ";
                $proxiesFromCheck [$proxyCount]['ip'] = $proxiesToCheck[$proxyCount];
                $proxiesFromCheck [$proxyCount]['anm'] = '1';
            }


            if (strpos($tempResultString2, 'test_query') > 1) { //Проверка на QUERY если в строке  
                $proxiesFromCheck [$proxyCount]['ip'] = $proxiesToCheck[$proxyCount];
                $proxiesFromCheck [$proxyCount]['query'] = '1';
                echo "query проходит\n";
            }
            else {
                echo "query не проходит\n";
                $proxiesFromCheck [$proxyCount]['ip'] = $proxiesToCheck[$proxyCount];
                $proxiesFromCheck [$proxyCount]['query'] = '0';
            }
        }
        curl_multi_remove_handle($mh, $ch[$proxyCount]);
    }
    curl_multi_close($mh);


    $proxiesToDirect = $proxiesFromCheck;
    $proxiesFromCheck = null;

    ////////////////////////



    for ($proxyCount = 0; $proxyCount < count($proxiesToDirect); $proxyCount++) { //
        if ($proxiesToDirect [$proxyCount]['time'] != '1') {
            $ch[$proxyCount] = curl_init($directLink);
            curl_setopt($ch[$proxyCount], CURLOPT_PROXY, $proxiesToDirect[$proxyCount]['ip']);
            curl_setopt($ch[$proxyCount], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch[$proxyCount], CURLOPT_TIMEOUT, $timeout);
            curl_setopt($ch[$proxyCount], CURLOPT_POST, 0);
            curl_setopt($ch[$proxyCount], CURLOPT_USERAGENT, randUa($uaList));
        }
    }
    $mh = curl_multi_init();
    for ($proxyCount = 0; $proxyCount < count($proxiesToDirect); $proxyCount++) { //
        curl_multi_add_handle($mh, $ch[$proxyCount]);
    }
    $running = null;
    do { //execute the handles
        curl_multi_exec($mh, $running);
    } while ($running > 0);

    for ($proxyCount = 0; $proxyCount < count($proxiesToDirect); $proxyCount++) { //
        $testResult = curl_multi_getcontent($ch[$proxyCount]); //получаем результат запроса
        $testResult = explode("\n", $testResult); //По строкам бьём текцщий забор
        $tempResultString = null; //обнуляем индикатор вхождения categories_type
        for ($numLineResult = 0; $numLineResult < count($testResult); $numLineResult++) { //по строкам текущего забора
            //echo $testResult[$numLineResult]."\n";
            if (preg_match('/categories_type/', $testResult[$numLineResult])) {
                $tempResultString = $testResult[$numLineResult]; //в индикатор содержимое строки со вхождением, для обработки
            }
        }
        if ($tempResultString != null && $proxiesToDirect[$proxyCount][anm] == 1) { //если встретилось вхождение
            $proxiesToDirect[$proxyCount]['ya_market'] = '1';
        }
        curl_multi_remove_handle($mh, $ch[$proxyCount]);
        echo " DIRECT " . $proxiesToDirect[$proxyCount]['ya_market'] . ", ANM " . $proxiesToDirect[$proxyCount]['anm'] . "\n";
    }
    //////////////////////


    return $proxiesToDirect;
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
    $text = preg_replace('/(.*?)<span(.*?)>(.*)/i', '$1 $3', $text);
    $text = strip_tags_smart($text);
    $proxiesNew = explode("\n", $text); //текст в массив
    for ($i = 0; $i < count($proxiesNew); $i++) { //перебираем, фильтруем IP
        $proxiesNew[$i] = trim($proxiesNew[$i]);
        //echo ("$proxiesNew[$i]\n");
                

echo $proxiesNew[$i];


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
                echo "query проходит\n";
            }
            else {
                echo "query не проходит\n";
                $checkingProxy[$currentProxyKey]['query'] = '0';
            }
        }
        else {
            $checkingProxy[$currentProxyKey]['time'] = '1';
            echo "Timeout\n";
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
//var_dump($checkingProxy);
//
//----------------- Checking google_serp
        $resultFrom_multiCurl = $person->multyProxyBulkConnect('https://google.com', $proxiesToCheckElit, $timeout, 'Mozilla Firefox 52.1 / Windows NT6.3', 1);
//var_dump($resultFrom_multiCurl);
// Determin if the google_serp is available
        $resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, 'rel="shortcut icon"><title>Google</title><script nonce=', 'content', 'google_serp', 'ya_market,proxy_ip,anm,query,time', true);

        $resultFrom_multiCurl = fieldToBoolean3D($resultFrom_multiCurl, 'google_serp');   // Now if the field "google_serp" consists entry, it will as 1, else 0.
//var_dump($resultFrom_multiCurl);
        $checkingProxy = cellFromAnother3DArray($resultFrom_multiCurl, $checkingProxy, 'google_serp', $controlKey); // Now we reinstate sequences of keys and implement the new "google_serp" data to the array wich consists ip.
// The current fields are "google_serp,ya_market,proxy_ip,anm,query,time"
    }
    return($checkingProxy);
}

function alignmentConditions($conditions, $data) {
    for ($i = 0; $i < count($data); $i++) {
        if (!isset($data[$i]['anm']))
            $data[$i]['anm'] = 0;
        if (!isset($data[$i]['query']))
            $data[$i]['query'] = 0;
        if (!isset($data[$i]['ya_market']))
            $data[$i]['ya_market'] = 0;
        if ($conditions['anm'] == 2)
            $conditions['anm'] = $data[$i]['anm']; //если условие = 2 (неважно), приравниваем ко входным данным
        if ($conditions['query'] == 2)
            $conditions['query'] = $data[$i]['query']; //если условие = 2 (неважно), приравниваем ко входным данным
        if ($conditions['ya_market'] == 2)
            $conditions['ya_market'] = $data[$i]['ya_market']; //если условие = 2 (неважно), приравниваем ко входным данным
        if ($conditions['google_serp'] == 2)
            $conditions['google_serp'] = $data[$i]['google_serp']; //если условие = 2 (неважно), приравниваем ко входным данным
    }
    //$result[$data] = $data;
    //$result[$conditions] = $conditions;
    return($conditions);
}
// Filling empty cells by zero values
function fillEmptyCells($index) {

    if (!isset($index['ya_market']))
        $index['ya_market'] = 0;
    if (!isset($index['google_serp']))
        $index['google_serp'] = 0;
    if (!isset($index['anm']))
        $index['anm'] = 0;
    if (!isset($index['query']))
        $index['query'] = 0;
    //echo "!\n";
    //var_dump($index);
    return($index);
}
?>

