<?php
break;
$uaList ="includes/ua.txt";
$testUrl = 'http://example.com/test_proxy.php?test_query';
$myIp = '8.8.8.8';
$minimumNew = 150;	//больше какого порога непроверенных не грабить новые (оперативность, качество, частота проверки)
$minimumOk = 70;	//больше какого порога рабочих ОК не грабить новые (величина списка, оперативность, нагрузка процессора)
$testSize = 60;	//По сколько проверять за раз (нагрузка сети и процессора с памятью)
$limitParseNew = 200; //По сколько парсить, чтобы очередь не скапливалась 
$limitCheckOld = 300; //По сколько проверять старья, чтобы очередь не скапливалась 

$distrustTime = 3;	//во сколько раз увеличить задержки между проверками плохих по сравнению с хорошими (качество, стабильность. если выделывается, порверяем реже)
$kTime = 1.5; //во сколько раз время последней проверки больше времени работоспособности ОК(больше — проверяем чаще, меньше — снижаем качество)

$penaltyNewTime = 3000;	//если новый и сразу мёртвый, штраф в секундах (чтобы не дёргать его при следующих проверках, пусть стоит в углу)
$cond['anm'] = '1'; //анонимность (0 ­— нет, 1 — да, 2 — по барабану)
$cond['query'] = '1';	//прохождение запроса  (0 ­— нет, 1 — да, 2 — по барабану)
$cond['ya_market'] = '2';	//доступность сайта через прокси (0 ­— нет, 1 — да, 2 — по барабану)
$cond['google_serp'] = '2';	//доступность сайта через прокси (0 ­— нет, 1 — да, 2 — по барабану)
$yaMarketLink = 'http://market.yandex.ru/';	//какой сайт проверяем
$timeout = 15;	//таймаут для проверки cUrl рабочих и новых
$timeoutDistrust = 10;	//таймаут для проверки cUrl нерабочих

$maxTimeNoCheckOk = 86400; //предел непроверки рабочих в секундах (сутки)
$limitTime = 2000; //количественный лимит хранения когда-то живых, чтобы не чекать его, а просто сбросить
$limitNever = 1500;	//количественный лимит хранения никогда не работавшего хлама, чтобы перенести его в блэклист и забыть
$limitSubstandard = 20000; //количественный лимит хранения некондиции, чтобы удалить. Вдруг мутировали?

define ("DBHOST", "192.168.1.64");
define ("DBNAME", "proxygrabber");
define ("DBUSER", "proxygrabber");
define ("DBPASS", "proxygrabber");
define ("PID_FILE", "proxygrabber.pid");



require 'includes/classes/vendor/autoload.php';
?>
