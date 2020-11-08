<?php
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../vendor/autoload.php';
$url = SERVER_URL . '/api/rat/read.php';
// Perform GET from URL and authorization headers
$cURLConnection = curl_init();
curl_setopt($cURLConnection, CURLOPT_URL, $url);
curl_setopt($cURLConnection, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($cURLConnection);
$info = null;
if (!curl_errno($cURLConnection)) {
    $info = curl_getinfo($cURLConnection, CURLINFO_HTTP_CODE);
} else {
    return curl_error($cURLConnection);
}
curl_close($cURLConnection);

if ($info === 200) {
    // If response HTTP status code is 200 OK
    $responseArr = json_decode($response, true); // JSON decode response body
    $rats = [];
    if (isset($_GET['filter_is_alive'])) {
        foreach($responseArr['body'] as $item) {
            if ($item['is_alive'] == $_GET['filter_is_alive']) {
                array_push($rats, $item);
            }
        }
    } else {
        $rats = $responseArr['body'];
    }
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../view');
    $twig = new \Twig\Environment($loader, [
        'cache' => __DIR__ . '/../compile/cache',
    ]);
    $template = $twig->load('rat_index.twig');
    $year = date("Y");
    $years = [];
    for ($i = 1998; $i <= $year; $i++) {
        array_push($years, $i);
    }
    echo $template->render(['months' => MONTHS, 'days' => DAYS, 'years' => $years, 'rats' => $rats]);
} else {
    echo $url;
}