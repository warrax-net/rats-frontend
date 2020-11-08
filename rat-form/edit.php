<?php
require_once __DIR__ . '/../constants.php';
require_once __DIR__ . '/../vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../view');
$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/../compile/cache',
]);
$template = $twig->load('rat_edit.twig');
$year = date("Y");
$years = [];
for ($i = 1998; $i < $year; $i++) {
    array_push($years, $i);
}
$success = !empty($_GET['success']) ? $_GET['success'] : '';
$postdata['birth_date_d'] = -1;
$postdata['birth_date_m'] = -1;
$postdata['birth_date_y'] = -1;
$postdata['death_date_d'] = -1;
$postdata['death_date_m'] = -1;
$postdata['death_date_y'] = -1;
$postdata['arrival_date_d'] = -1;
$postdata['arrival_date_m'] = -1;
$postdata['arrival_date_y'] = -1;
$post = !empty($_POST) ? $_POST : $postdata;
echo $template->render(['success' => $success, 'months' => MONTHS, 'days' => DAYS, 'years' => $years, 'post' => $post]);
