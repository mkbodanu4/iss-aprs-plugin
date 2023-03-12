<?php

$url = "https://live.ariss.org/iss.txt";

$handler = curl_init();
curl_setopt($handler, CURLOPT_URL, $url);
curl_setopt($handler, CURLOPT_HEADER, FALSE);
curl_setopt($handler, CURLINFO_HEADER_OUT, FALSE);
curl_setopt($handler, CURLOPT_FOLLOWLOCATION, TRUE);
curl_setopt($handler, CURLOPT_MAXREDIRS, 10);
curl_setopt($handler, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($handler, CURLOPT_TIMEOUT, 30);
curl_setopt($handler, CURLOPT_USERAGENT, "WordPress");
$result = curl_exec($handler);
$http_code = curl_getinfo($handler, CURLINFO_HTTP_CODE);
curl_close($handler);

if($http_code === 200 && $result) {
    file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . 'iss.txt', $result);
}