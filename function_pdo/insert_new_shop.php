<?php
require_once "../connect_db.php";
require_once "../functions/db_function.php";
require_once "../functions/db_table_function.php";
$user_name = $userdata_temp[0]['login'];

// $pdo = checkConnectionToDB($host, $db_user, $user, $password) ;

$data['shop_name'] = $_POST['shop_name'];
$data['token'] = $_POST['token'];

// проверяем есть ли такой магазин уже

$tokens = get_token_shop ($pdo);
$priznak_sovpadenia = 0;
foreach ($tokens as $token) {
    if ($token['name_market'] == $data['shop_name']) {
        $priznak_sovpadenia = 1;
        }
    if ($token['token_market'] == $data['token']) {
        $priznak_sovpadenia = 2;
    }


}


if ( $priznak_sovpadenia !=0) {
    header("Location: ../make_new_shop.php?message=".$priznak_sovpadenia);
    die();
}



insert_new_shop_in_db($pdo, $data, $user_name);


header("Location: ../check_shop.php");
