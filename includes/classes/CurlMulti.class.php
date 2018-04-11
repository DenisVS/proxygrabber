<?php

class CurlMulti
{
    public $name;

    public function multyProxyBulkConnect($link, $proxies, $timeout = 20, $uaList, $follow = 1) {
      for($proxyCount=0; $proxyCount < count($proxies); $proxyCount++)  {	
        $ch[$proxyCount] = curl_init();
        curl_setopt($ch[$proxyCount], CURLOPT_URL, $link);
        curl_setopt($ch[$proxyCount], CURLOPT_PROXY, $proxies[$proxyCount]['proxy_ip']);
        curl_setopt($ch[$proxyCount], CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch[$proxyCount], CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch[$proxyCount], CURLOPT_POST, 0);
        curl_setopt($ch[$proxyCount], CURLOPT_USERAGENT, $uaList);
        curl_setopt($ch[$proxyCount], CURLOPT_FOLLOWLOCATION, $follow);
      }	
      $mh = curl_multi_init();
      for($proxyCount=0; $proxyCount < count($proxies); $proxyCount++)  {	
        curl_multi_add_handle($mh, $ch[$proxyCount]);
      }	
      $running=null;
      //execute the handles
      do {	
        curl_multi_exec($mh, $running);
      } while ($running > 0);  
      for($proxyCount=0; $proxyCount < count($proxies); $proxyCount++)  {	
        $result[$proxyCount]['content'] = curl_multi_getcontent  ($ch[$proxyCount]); 
        $result[$proxyCount]['proxy_ip'] = $proxies[$proxyCount]['proxy_ip'];
     		curl_multi_remove_handle($mh, $ch[$proxyCount]);
      }
      curl_multi_close($mh); 
      return($result);
    }
  ////////////////////////////////////////
  /**
    * Parsing multiline array element to search for entry
    *
    * @param array $in
    * @param string $string
    * @param string $whichParse -- which element to parse
    * @param string $whereKeep -- which element for keeping outturn
    * @param string $whichKeep -- which comma-separated elements to keep for further use without changing
    * @param string $keepUncond -- keep empty results to array with keeping elements
    * @return string
    */

  public function parseRequestLineByLine($in, $string, $whichParse, $whereKeep = false, $whichKeep, $keepUncond = true) {
    if ($whereKeep == false) $whereKeep = $whichParse;
    $whichKeep = explode (',', $whichKeep);
    for($count=0; $count < count($in); $count++) {	
      $lineIn[$count] = explode ("\n",$in[$count][$whichParse]);
      foreach($lineIn[$count] as $key => $lineContent){
          if(stristr( $lineContent, $string ))  {
            $out[$count][$whereKeep] = $lineContent;
            foreach($whichKeep as $whichKeepKey => $whichKeepVal){
                if (isset($in[$count][$whichKeep[$whichKeepKey]])) $out[$count][$whichKeep[$whichKeepKey]] = $in[$count][$whichKeep[$whichKeepKey]];
            }
         } elseif ($keepUncond == true) {
              foreach($whichKeep as $whichKeepKey => $whichKeepVal){
                  if (isset($in[$count][$whichKeep[$whichKeepKey]])) $out[$count][$whichKeep[$whichKeepKey]] = $in[$count][$whichKeep[$whichKeepKey]];
              }
         }
      }
    }	
    return($out);
  }
}



?>