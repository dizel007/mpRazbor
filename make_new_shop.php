<?php

echo "<br>MAKE_NEW_SHOP";


require_once "connect_db.php";

// если вернулись в созжание магазина с какой то ошибкой (выведем ее на экран)
$alarm_message = '';
if (isset($_GET['message'])) {
    if($_GET['message'] == 1) {
     $alarm_message = 'Магазин с таким названием уже существует';
    } elseif ($_GET['message'] == 2) {
        $alarm_message = 'Магазин с таким токем уже существует';
    } else {
        $alarm_message = 'Непредвиденная ошибка';

    }
}
// echo "<pre>";
// print_r($userdata_temp[0]);
// die();
$user_name = $userdata_temp[0]['login'];
echo <<<HTML
<div>$alarm_message </div>
<form action ="function_pdo/insert_new_shop.php" method="post">
    <input hidden type="text" name="user_name" value="$user_name">
    <br>
    <label>Название магазина</label>
    <input required type="text" name="shop_name" value="">
    <br>
    <label>Вставьте токен ВБ</label>
    <input required type="text" name="token" value="">
    <br>
    <input type="submit" value="СОБРАТЬ">

</form>
<div><a href ="check_shop.php"> к списку магазинов </div>


HTML;