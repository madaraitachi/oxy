<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://cdn2-reg2.mm.oxygen.id/hls/{$_GET['id']}/index.m3u8");
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'User-Agent: Oxygen TV/5.9.240112 (Linux;Android 10) ExoPlayerLib/2.18.1'
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
$chunks = explode(':', curl_exec($ch));
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
curl_close($ch);

if ($code !== 200)
{
  http_response_code($code);
}
else
{
  $url = substr($url, 0, strrpos($url, '/'));
  $enc_key = base64_decode('b3h5Z2VubXVsdGltZWRpYQ==');
  $enc_iv = base64_decode($chunks[1]);
  $cipher = base64_decode($chunks[0]);
  $decrypt = openssl_decrypt($cipher, 'AES-128-CBC', $enc_key, OPENSSL_RAW_DATA, $enc_iv);
  header('Content-Type: application/vnd.apple.mpegurl');
  echo preg_replace('/(\S+\.ts)/', "{$url}/$1", $decrypt);
}

?>
