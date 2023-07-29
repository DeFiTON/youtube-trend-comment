<?php
use Medoo\Medoo;
session_start();

$db_conf = [
    // required
    'database_type' => 'mysql',
    'database_name' => 'ТУТ УКАЖИТЕ ИМЯ БАЗЫ ДАННЫХ',
    'server' => 'localhost',
    'username' => 'ТУТ УКАЖИТЕ ЛОГИН ВЛАДЕЛЬЦА БАЗЫ',
    'password' => 'НУ И ПАРОЛЬ УКАЖИТЕ ТОЖЕ',
//    'charset' => 'мало ли нужно еще',
//    'collation' => 'будет что-то указать тут',
    'port' => 3306,

];

$youtube_key = 'ТУТ УКАЖИТЕ СВОЙ YOUTUBE КЛЮЧ';

$jap = [
    'api_key'=>'ТУТ УКАЖИТЕ СВОЙ API КЛЮЧ JAP',
    'service_id'=>3459,
];

define('TASK_COUNT', 10);

require_once __DIR__.'/Medoo.php';
require_once __DIR__.'/TrendComment.class.php';

try {
    $db = new Medoo($db_conf);
} catch (Exception $e) {
    die($e->getMessage());
}

$TC = new TrendComment($db, $youtube_key);


if (isset($_REQUEST['action']) && method_exists($TC, $_REQUEST['action'])) {
    $action = $_REQUEST['action'];
    try {
        $page_data = $TC->$action();
    } catch (Exception $e) {
        die($e->getMessage());
    }
}