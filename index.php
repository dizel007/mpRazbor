<?php

require_once "connect_db.php";
require_once "functions/db_function.php";
require_once "mp_functions/wb_api_functions.php";

//  если нет таблицы user_action, то создаем пустую тубалицу
if (!check_table_in_db($pdo, 'user_action', $db_user)){
  create_new_table_user_action_in_db($pdo);
  }

//  если нет таблицы с токенами, то создаем пустую тубалицу
if (!check_table_in_db($pdo, 'tokens', $db_user)){
  create_new_table_tokens_in_db ($pdo);
  header("Location: make_new_shop.php?uhash=".$userdata_temp[0]['user_hash']);
  die();
} else {
  $tokens = get_token_shop ($pdo);
  if (!$tokens) {
    header("Location: make_new_shop.php?uhash=".$userdata_temp[0]['user_hash']);
  die();
  } else {
    header("Location: check_shop.php");
    die();
  }
}
// если нет ни однго магазина, то уходим на создание нового магазина


echo "<pre>";
print_r($tokens);




die();

$tokens = get_token_shop ($pdo);
$token_wb = $tokens[0]['token_market'];

$table_name_nomenclatura = "wb_222BBB222";

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

$result = get_all_tovari ($pdo, $table_name_nomenclatura );
echo "<pre>" ;
var_dump ($result);


die();

