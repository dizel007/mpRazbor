<?php
$offset = "../";
require_once $offset."connect_db.php";
require_once $offset."functions/db_function.php";
require_once "functions/functions.php";
require_once "get_zakaz_by_check_date.php"; // функция выбора заказоа с учетом выбранной даты
require_once "print_wb_table.php";

$shop_name = 'Вашему магазину';
$name_market = $_GET['name_market'];
$arr_token = get_our_token_shop($pdo, $_GET['name_market']);
$token_wb = $arr_token['token_market'];

// дата на которую нуэно собрать заказы (ПОКА ВРУЧНУЮ ИЗМЕНЯЕТСЯ В ФУНКЦИИ)
if (isset($_GET['date_sbora_zakaza'])) {
  $date_orders_select = $_GET['date_sbora_zakaza'];
} else {
  $date_orders_select = '';
}

if ($date_orders_select <> '') {
  $text_about_date = "Заказы созданные : $date_orders_select";
} else {
  $text_about_date = "Заказы созданные ЗА ВСЕ ВРЕМЯ";
}
/********************************************************************************************************
 * ******************** Вычитываем и выводи заказы для ВБ
 ********************************************************************************************************/

$raw_arr_orders = select_order_by_check_date($token_wb, $date_orders_select);

// echo "<pre>";
// print_r($raw_arr_orders );
// die();
/********************************************************************************************************
 * ******************** Вычитываем Заказы, которые ВБ не приняли
 ********************************************************************************************************/
// $find_old_orders = select_all_old_order($token_wb); // 

// echo "<pre>";
// print_r($raw_arr_orders);

// die(); 

echo <<<HTML
<html>


<head>
<link rel="stylesheet" href="css/main_wb.css">
<script type="text/javascript" src="../js/js_hide_button.js"></script>


</head>

<body>
    
HTML;

print_wb_order_table($name_market, $date_orders_select, $raw_arr_orders, $text_about_date);

// if ($find_old_orders) {
//   echo "<h2>Есть просроченные заказы</h2>";
// }
echo <<<HTML

</body>
</html>



HTML;






