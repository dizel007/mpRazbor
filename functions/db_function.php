<?php

/***************************************************************
 ***************  Проверяем соединение с БД ********************
  ***************************************************************/
// function checkConnectionToDB($host, $db_user, $user, $password)
// {
//     static $rep_connection = 0;
//     $rep_connection++;
//     try {
//         $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $db_user . ';charset=utf8', $user, $password);
//         $pdo->exec('SET NAMES utf8');
//         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//         return $pdo;
//     } catch (PDOException $e) {
//         $pdo_new = new PDO('mysql:host=' . $host, $user, $password);
//         print "Не удалось подключиться к базе: $db_user " . $e->getMessage() . "<br> Пробуем создать БД с таким названием  $rep_connection<br>";
//         makeNewDataBase($pdo_new, $db_user);
//         $pdo = checkConnectionToDB($host, $db_user, $user, $password);
//         return $pdo;
//     }
// }




/***********************************************
 ***************  Make new DB *******************
 **********************************************/

// function makeNewDataBase($pdo, $db_user)
// {

//     try {
//         // SQL-выражение для создания базы данных
//         $sql = "CREATE DATABASE $db_user";
//         // выполняем SQL-выражение
//         $pdo->exec($sql);
//         // echo "Database has been created";

//     } catch (PDOException $e) {
//         print "Has errors: " . $e->getMessage();
//         makeNewDataBase($pdo, $db_user);
//     }
// }

/*********************************************************************************************
 ***************  Проверяем существование таблицы в выбранной базе данных  ********************
 ********************************************************************************************/

function check_table_in_db($pdo, $table_name_nomenclatura, $db_user)
{

    $stmt = $pdo->prepare("SHOW TABLES FROM `$db_user` LIKE '$table_name_nomenclatura'");
    $stmt->execute([]);
    $result = $stmt->fetchALL(PDO::FETCH_COLUMN);

    if (isset($result[0])) {
        return $result[0];
    } else {
        return false;
    }
}
/*********************************************************************************************
 ***************  создаем новую таблицу токенс ********************
 ********************************************************************************************/
function create_new_table_tokens_in_db($pdo)
{
    // создаем таблицу
    $statements = "CREATE TABLE `tokens`( 
              id INT AUTO_INCREMENT,
              name_market  TEXT, 
              token_market TEXT, 
              date_token   DATE,
              user_update  TEXT,
              PRIMARY KEY(id)
            );";
    $pdo->exec($statements);
}

/*********************************************************************************************
 ***************  создаем новую таблицу юзер экшинс ********************
 ********************************************************************************************/
function create_new_table_user_action_in_db($pdo)
{
    // создаем таблицу
    $statements = "CREATE TABLE `user_action`( 
              id INT AUTO_INCREMENT,
              name_market  TEXT, 
              user_action  TEXT,
              date_action  DATE,
              user_update  TEXT,
              PRIMARY KEY(id)
            );";
    $pdo->exec($statements);
}

/*********************************************************************************************
 ***************  Поулчаем все токены магазинов  ********************
 ********************************************************************************************/

function get_token_shop($pdo)
{

    $stmt = $pdo->prepare("SELECT * FROM tokens");
    $stmt->execute([]);
    $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

    if (isset($result[0])) {
        return $result;
    } else {
        return false;
    }
}
/*********************************************************************************************
 ***************  Проверяем токен выбранного магазина  ********************
 ********************************************************************************************/

 function get_our_token_shop($pdo, $name_market)
 {
 
     $stmt = $pdo->prepare("SELECT * FROM `tokens` WHERE `name_market` = '$name_market'");
     $stmt->execute([]);
     $result = $stmt->fetchALL(PDO::FETCH_ASSOC);
 
     if (isset($result[0])) {
         return $result[0];
     } else {
         return false;
     }
 }

/***************************************************************
 ***************  Получаем все номенклатуру с сайта ВБ **************
 ***************************************************************/

function get_all_nomenclaturu($token_wb, $limit)
{

    // $limit = 4; // количесто товаров в запросе


    $link_wb = "https://content-api.wildberries.ru/content/v2/get/cards/list";
    $data = array(
        "settings" => array(
            "cursor" => array(
                "limit" => $limit
            ),
            "filter" => array(
                "withPhoto" => -1
            )
        )
    );

    $res = light_query_with_data($token_wb, $link_wb, $data);


    // print_r($res); 

    // die();
    $updatedAt = $res['cursor']['updatedAt'];
    $nmID = $res['cursor']['nmID'];
    $total = $res['cursor']['total'];
    $delta = 1;



    $i = 0;

    foreach ($res['cards'] as $card) {
        $all_cards[$i]['nmID'] = $card['nmID'];
        $all_cards[$i]['vendorCode'] = $card['vendorCode'];
        $all_cards[$i]['subjectName'] = $card['subjectName'];
        $all_cards[$i]['title'] = $card['title'];
        $all_cards[$i]['barcode'] = $card['sizes'][0]['skus'][0];
        $all_cards[$i]['dimensions'] = $card['dimensions'];
        // находим цвет бордюра
        foreach ($card['characteristics'] as $color) {
            if ($color['id'] == 14177449) {
                $all_cards[$i]['color'] = $color['value'][0];
            }
        }
        $i++;
    }



    while ($delta >= 0) {
        $data = array(
            "settings" => array(
                "cursor" => array(
                    "updatedAt" => $updatedAt,
                    "nmID" => $nmID,
                    "limit" => $limit
                ),
                "filter" => array(
                    "withPhoto" => -1
                )
            )
        );
        $res = light_query_with_data($token_wb, $link_wb, $data);

        (isset($res['cursor']['updatedAt'])) ? $updatedAt = $res['cursor']['updatedAt'] : $updatedAt = 0;
        $nmID = $res['cursor']['nmID'];
        $total = $res['cursor']['total'];

        // дельта показывает сколько товаров осталось в следующем запросе.
        $delta = $total - $limit;

        foreach ($res['cards'] as $card) {
            $all_cards[$i]['nmID'] = $card['nmID'];
            $all_cards[$i]['vendorCode'] = $card['vendorCode'];
            $all_cards[$i]['subjectName'] = $card['subjectName'];
            $all_cards[$i]['title'] = $card['title'];
            $all_cards[$i]['barcode'] = $card['sizes'][0]['skus'][0];
            $all_cards[$i]['dimensions'] = $card['dimensions'];
            // находим цвет бордюра
            foreach ($card['characteristics'] as $color) {
                if ($color['id'] == 14177449) {
                    $all_cards[$i]['color'] = $color['value'][0];
                }
            }
            $i++;
        }

        // echo "<pre>";
        // print_r($kkkk);

    };
    return $all_cards;
}
