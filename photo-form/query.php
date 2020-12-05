<?php
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

$rat_ids = [];
foreach ($_POST['rat_ids'] as $rat_id) {
    array_push($rat_ids, $rat_id);
}
$json = [
    'all_alive' => isset($_POST['all_alive']) && $_POST['all_alive'] == '1' ? 1 : 0,
    'from_date_m' => $_POST['from_date_m'],
    'from_date_y' => $_POST['from_date_y'],
    'to_date_m' => $_POST['to_date_m'],
    'to_date_y' => $_POST['to_date_y'],
    'rat_ids' => $rat_ids,
];
$postdata = json_encode($json);
// echo $postdata;
// Set up URL
$url = SERVER_URL . '/api/photo/query.php';
// echo $url;
// Perform GET from URL and authorization headers
$headers  = [
    'Content-Type: text/plain'
];
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);           
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_URL, $url);

$response = curl_exec($ch);
$info = null;
if (!curl_errno($ch)) {
    $info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
} else {
    return curl_error($ch);
}
curl_close($ch);

if ($info === 200) {
    // If response HTTP status code is 200 OK
    $responseArr = json_decode($response, true); // JSON decode response body
    $loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../view');
    $twig = new \Twig\Environment($loader, [
        'cache' => __DIR__ . '/../compile/cache',
    ]);
    $template = $twig->load('photo_index.twig');
    $year = date("Y");
    $years = [];
    for ($i = 1998; $i <= $year; $i++) {
        array_push($years, $i);
    }
    echo $template->render(['months' => MONTHS, 'days' => DAYS, 'years' => $years, 'photos' => $responseArr['body']]);
} else {
    echo $url;
}