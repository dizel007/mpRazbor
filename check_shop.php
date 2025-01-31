<?php
echo "<br>CHECK SHOP";

require_once "connect_db.php";
require_once "functions/db_function.php";
require_once "functions/db_table_function.php";

// $pdo = checkConnectionToDB($host, $db_user, $user, $password) ;

$tokens = get_token_shop ($pdo);
// echo "<pre>";
// print_r($tokens);

// если нет магазинов
if (!$tokens) {
    echo "<br>Магазины в базе данных не найдены" ;
} else {
echo "<br>Существующие Магазины ";
echo "<br>";
foreach ($tokens as $token) {
echo "<a href =\"get_tovari_s_wb.php?name_market=".$token['name_market']."\">". $token['name_market']."</a><br>";
}
}
echo "<br>";
echo "<br>";

echo "<a href =\"make_new_shop.php\">". "Добавить новый магазин"."</a><br>";

die();