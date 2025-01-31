<?php
echo "<br>GET TOVARI FROM WB";
require_once "connect_db.php";
require_once "functions/db_function.php";
require_once "functions/db_table_function.php";
require_once "mp_functions/wb_api_functions.php";

// Достаем токен из БД
$name_market = $_GET['name_market'];
$token = get_our_token_shop($pdo, $_GET['name_market']);
$token_wb = $token ['token_market'];

// Пробуем достать товары из Базы данных
$table_name_nomenclatura = $_GET['name_market'];
$nomenclatura = get_all_tovari($pdo, $table_name_nomenclatura);

// ССылка на начало разбора товаров
echo "<br>";
echo "<a href =\"wb_razbor/index_wb.php?name_market=$name_market\">Начать разбор товаров</a>";


if (!$nomenclatura) {
  // получаем всю номенклатуру магазина 
$limit = 100; // количесто товаров в запросе (100 - максимум)
$nomenclatura = get_all_nomenclaturu($token_wb, $limit);

// Проверяем существование данной таблцы
$result = check_table_in_db ($pdo, $table_name_nomenclatura ,$db_user);
// если нет таблицы то создаем новую таблицу и вставляем туда всю номенклатуру
if (!$result) {
  create_new_table_in_db ($pdo, $table_name_nomenclatura);
  insert_nomenclaruru_in_db ($pdo, $table_name_nomenclatura ,$nomenclatura);
} 
// Пробуем достать товары из Базы данных
$nomenclatura = get_all_tovari ($pdo, $table_name_nomenclatura );

}

// echo "<pre>";
// print_r($nomenclatura); 

