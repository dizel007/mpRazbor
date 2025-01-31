<?php

function insert_new_shop_in_db($pdo, $data, $user_name)
{

    print_r($data);

    // die('ss');

    try {
        $insert = $pdo->prepare("INSERT INTO `tokens` SET 
                                              `name_market`  = :name_market, 
                                              `token_market` = :token_market, 
                                              `date_token`   = :date_token, 
                                              `user_update`  = :user_update
                                             ");

        $insert->execute(
            array(
                'name_market' => $data['shop_name'],
                'token_market' => $data['token'],
                'date_token' => date('Y-m-d'),
                'user_update' => $user_name

            )

        );
        $insert_id = $pdo->lastInsertId();
        echo $insert_id;
    } catch (PDOException $e) {
        die($e->getMessage());
    }

    return   $insert_id;
}



/*********************************************************************************************
 ***************  Проверяем номенклатуру из магазина  ********************
 ********************************************************************************************/

function get_all_tovari($pdo, $name_market)
{
    try {
        $stmt = $pdo->prepare("SELECT * FROM $name_market");
        $stmt->execute([]);
        $result = $stmt->fetchALL(PDO::FETCH_ASSOC);

        if (isset($result[0])) {
            return $result;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "<br> Не нашли такой маркет в БД";
    }
}


try {
    $pdo = new PDO('mysql:host=' . $host . ';dbname=' . $db_user . ';charset=utf8', $user, $password);
    $pdo->exec('SET NAMES utf8');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $pdo;
} catch (PDOException $e) {
    $pdo_new = new PDO('mysql:host=' . $host, $user, $password);
    print "Не удалось подключиться к базе: $db_user " . $e->getMessage() . "<br> Пробуем создать БД с таким названием  $rep_connection<br>";
    makeNewDataBase($pdo_new, $db_user);
    $pdo = checkConnectionToDB($host, $db_user, $user, $password);
    return $pdo;
}

/*********************************************************************************************
 ***************  создаем новую таблицу ********************
 ********************************************************************************************/
function create_new_table_in_db($pdo, $table_name_nomenclatura)
{
    // создаем таблицу
    $statements = "CREATE TABLE $table_name_nomenclatura( 
           id   INT AUTO_INCREMENT,
           main_article  TEXT, 
           mp_article TEXT, 
           sku   TEXT,
           barcode  TEXT,
           mp_name TEXT,
           mp_price INT(11),
           fbo   INT(11),
           fbs   INT(11),
           fake_count  INT(11),
           active_tovar INT(11),
           PRIMARY KEY(id)
         );";
    $pdo->exec($statements);
}


/*********************************************************************************************
 ***************  создаем новую таблицу и вставляем туда всю номенклатуру ********************
 ********************************************************************************************/
function insert_nomenclaruru_in_db($pdo, $table_name_nomenclatura, $nomenclatura)
{


    foreach ($nomenclatura as $item) {
        if (!isset($item['color'])) {
            $item['color'] = '';
        }
        $insert = $pdo->prepare("INSERT INTO `$table_name_nomenclatura` SET 
                                               `main_article` = :main_article, 
                                               `mp_article` = :mp_article, 
                                               `sku` = :sku, 
                                               `barcode` = :barcode, 
                                               `mp_name` = :mp_name, 
                                               `mp_price` = :mp_price, 
                                               `fbo` = :fbo, 
                                               `fbs` = :fbs, 
                                               `fake_count` = :fake_count, 
                                               `active_tovar` = :active_tovar");

        $insert->execute(
            array(
                'main_article' => $item['vendorCode'],
                'mp_article' => $item['vendorCode'],
                'sku' => $item['nmID'],
                'barcode' => $item['barcode'],
                'mp_name' => $item['title'] . "(" . $item['color'] . ")",
                'mp_price' => 0,
                'fbo' => 0,
                'fbs' => 100,
                'fake_count' => 0,
                'active_tovar' => 1
            )

        );

        // Получаем id вставленной записи
        echo $insert_id = $pdo->lastInsertId();
    }
}
