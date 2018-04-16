<?php
$whatsCheck = 20;


            switch ($whatsCheck) {
                case '10':  //OK
                case '20': //NEW
                case '30':
                    echo '3 INSERT INTO ip_list_substandard ' . 44 . "\n";
//                    $mysqli->query("INSERT INTO `ip_list_substandard` (`proxy_ip`, `checked`, `worked`, `status`) VALUES ('" . $proxiesFromCheck[$i]['proxy_ip'] . "', '" . time() . "', '" . time() . "', '" . $proxiesFromCheck[$i]['anm'] . $proxiesFromCheck[$i]['query'] . $proxiesFromCheck[$i]['ya_market'] . $proxiesFromCheck[$i]['google_serp'] . "' )"); //заносим в ip_list_substandard
                    //break;
                case '10':
  //                  $mysqli->query("DELETE FROM `ip_list_ok` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
                    break;
                case '20': //NEW
                    echo '4 DELETE FROM ip_list_new ' .  55 . "\n";
    //                $mysqli->query("DELETE FROM `ip_list_new` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
                    break;
                case '30':
      //              $mysqli->query("DELETE FROM `ip_list_time` WHERE `proxy_ip` = '" . $proxiesFromCheck[$i]['proxy_ip'] . "';");
                    break;
            }

/*

include "includes/config.php";
include "includes/functions.php";




spl_autoload_register(function ($class) {
    include 'includes/classes/' . $class . '.class.php';
});


$mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$resultLongChecked = $mysqli->query("SELECT `id`, `proxy_ip` FROM `ip_list_new`");


$resultLongChecked = $mysqli->query("SELECT proxy_ip FROM ip_list_new LIMIT " . $testSize);
$countIp = $resultLongChecked->num_rows; // How many rows with limited fresh?
echo "For test are " . $countIp . " new IP's\n";
while ($row = $resultLongChecked->fetch_assoc()) {
    $proxiesToCheckOwn[]['proxy_ip'] = $row['proxy_ip']; //заносим результат в массив для проверки
}







//----------------- Checking my control script on host
$person = new CurlMulti();
$resultFrom_multiCurl = $person->multyProxyBulkConnect('http://example.com/test_proxy.php?test_query', $proxiesToCheckOwn, $timeout, 'Mozilla Firefox 52.1 / Windows NT6.3');
$resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, '[REMOTE_ADDR]', 'content', 'remote_addr', 'content,proxy_ip', true);
$resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, '[HTTP_X_FORWARDED_FOR]', 'content', 'x_forwarded_for', 'content,proxy_ip,remote_addr', true);
$resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, 'test_query', 'content', 'test_query', 'x_forwarded_for,content,proxy_ip,remote_addr', true);
//var_dump($proxiesToCheckOwn);
foreach ($resultFrom_multiCurl as $currentProxyKey => $currentProxyVal) {
    // Is the proxy alive?
    if (isset($currentProxyVal['remote_addr'])) {
        $proxiesToCheckOwn[$currentProxyKey]['time'] = '0';  //Alive  
        if (isset($currentProxyVal['x_forwarded_for']) && strpos($currentProxyVal['x_forwarded_for'], $myIp) > 1) { //Проверка на анонимность если в строке мой IP 
            $proxiesToCheckOwn[$currentProxyKey]['anm'] = '0';  // "Неанонимный!
        }
        else {
            $proxiesToCheckOwn[$currentProxyKey]['anm'] = '1';  // "Анонимный!	
        }
        if (isset($currentProxyVal['test_query']) && strpos($currentProxyVal['test_query'], 'test_query') > 1) { //Проверка на QUERY если в строке  
            $proxiesToCheckOwn[$currentProxyKey]['query'] = '1';
            echo "query проходит\n";
        }
        else {
            echo "query не проходит\n";
            $proxiesToCheckOwn[$currentProxyKey]['query'] = '0';
        }
    }
    else {
        $proxiesToCheckOwn[$currentProxyKey]['time'] = '1';
        echo "Timeout\n";
    }
    unset($proxiesToCheckOwn[$currentProxyKey]['content']);
    unset($proxiesToCheckOwn[$currentProxyKey]['test_query']);
    unset($proxiesToCheckOwn[$currentProxyKey]['remote_addr']);
    unset($proxiesToCheckOwn[$currentProxyKey]['x_forwarded_for']);
}
// The current fields are "proxy_ip,anm,query,time"
var_dump($proxiesToCheckOwn);

//--------------------------- Collect only anonimous and query positive proxy. 
// We arrange new array of active proxy with new keys
// form "small cycle"
foreach ($proxiesToCheckOwn as $currentProxyKey => $currentProxyVal) {
    if (isset($currentProxyVal['anm']) && $currentProxyVal['anm'] == 1 && isset($currentProxyVal['query']) && $currentProxyVal['query'] == 1) {
        $proxiesToCheckElit[]['proxy_ip'] = $proxiesToCheckOwn[$currentProxyKey]['proxy_ip'];
        $controlKey[] = $currentProxyKey; // We create the control array for index and really keys.
    }
}
$controlKey = array_flip($controlKey); //Flip array. Now it looks like 4 => 1, 5 => 2, 8 => 3  This is template to insert new fields
//var_dump($proxiesToCheckElit);
//----------------- Checking ya_market

$resultFrom_multiCurl = $person->multyProxyBulkConnect($yaMarketLink, $proxiesToCheckElit, $timeout, 'Mozilla Firefox 52.1 / Windows NT6.3', 1);
// Determin if the ya_market is available
$resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, '<meta property="og:title"', 'content', 'ya_market', 'proxy_ip,anm,query,time', true);

$resultFrom_multiCurl = fieldToBoolean3D($resultFrom_multiCurl, 'ya_market');   // Now if the field "ya_market" consists entry, it will as 1, else 0.
//var_dump($resultFrom_multiCurl);

$proxiesToCheckOwn = cellFromAnother3DArray($resultFrom_multiCurl, $proxiesToCheckOwn, 'ya_market', $controlKey);   // Now we reinstate sequences of keys and implement the new "ya_market" data to the array wich consists ip.

// The current fields are "ya_market,proxy_ip,anm,query,time"
//var_dump($proxiesToCheckOwn);
//
//----------------- Checking google_serp
$resultFrom_multiCurl = $person->multyProxyBulkConnect('https://google.com', $proxiesToCheckElit, $timeout, 'Mozilla Firefox 52.1 / Windows NT6.3', 1);
//var_dump($resultFrom_multiCurl);
// Determin if the google_serp is available
$resultFrom_multiCurl = $person->parseRequestLineByLine($resultFrom_multiCurl, 'rel="shortcut icon"><title>Google</title><script nonce=', 'content', 'google_serp', 'ya_market,proxy_ip,anm,query,time', true);

$resultFrom_multiCurl = fieldToBoolean3D($resultFrom_multiCurl, 'google_serp');   // Now if the field "google_serp" consists entry, it will as 1, else 0.
//var_dump($resultFrom_multiCurl);
$proxiesToCheckOwn = cellFromAnother3DArray($resultFrom_multiCurl, $proxiesToCheckOwn, 'google_serp', $controlKey); // Now we reinstate sequences of keys and implement the new "google_serp" data to the array wich consists ip.
// The current fields are "google_serp,ya_market,proxy_ip,anm,query,time"

//var_dump($proxiesToCheckOwn);



?>
