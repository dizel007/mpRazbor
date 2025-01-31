<?php

/****************************************************************************************
 *  РИСУЕМ форму по выводу всех заказов
 ***************************************************************************************/
function print_wb_order_table($name_market, $date_orders_select, $raw_arr_orders, $text_about_date)
{
  echo <<<HTML
<div class = "table-wrapper">
<table class = "fl-table">
<thead>
  <tr>
    <th class="big_text" colspan ="3" >Информация по $name_market </th>
  </tr>
</thead>
<tbody>
  <td >
      <form action="#" method="get">
        <label>Введите дату СОЗДАНИЯ ЗАКАЗА </label>
    <div id="up_input" class="LockOff"> 
      <input type="date" name="date_sbora_zakaza" value="$date_orders_select">
      <input hidden type="text" name="name_market" value="$name_market">
      <input type="submit" value="НАЙТИ ЗАКАЗЫ НА ВЫБРАННУЮ ДАТУ">
      </form>    
</div>
  </td>

 
</tbody>
</table>
</div>
HTML;


  if (isset($raw_arr_orders['orders'][0])) {
    // массив новых отправлений собранный по артикулу
    $full_price = 0;
    foreach ($raw_arr_orders['orders'] as $orders) {
      $new_arr_orders[$orders['article']][] = $orders;
      $full_price = $full_price + $orders['convertedPrice'] / 100;
    }
    $middle_price = 1;
    $all_count = count($raw_arr_orders['orders']);


    foreach ($new_arr_orders as $key => $orders) {
      $raw_price = 0;

      foreach ($orders as $order) {
        $raw_price = $raw_price + $order['convertedPrice'];
      }
      $middle_price = number_format(($raw_price / count($new_arr_orders[$key])) / 100, 2);
      $sum_arr_article[$key] = array(

        'count' => count($new_arr_orders[$key]),
        'nmId' => $order['nmId'],
        'price' => $middle_price
      );
    }


    $full_zakaz_wb_price = number_format($full_price, 2);

echo <<<HTML
<div class = "table-wrapper">
<form action="start_new_supplies.php" method="post">
<table class = "fl-table">
<thead>
  <tr>
    <th colspan ="3" >Информация по $name_market </th>
  </tr>
</thead>
<tbody>
<tr>
    <th colspan ="3" class="big_text" >$text_about_date</th>
 </tr>

<td><b>Количество заказов :<br> $all_count </b></td>
<td ><b>Сумма заказов :<br> $full_zakaz_wb_price </b></td>
  <td >
     
        <label for="wb">Введите номер заказа из 1С</label>
        <input hidden type="date" name="date_sbora_zakaza" value="$date_orders_select">
        <input hidden type="text" name="name_market" value="$name_market">

        <!--  БЛОК который пропаадет после нажатия кнопки -->
        <div id="down_input" class="LockOff">
          <input required type="number" name="Zakaz1cNumber" value="">
          <input type="submit" value="СОБРАТЬ"  onclick="alerting()">
        </div>

        <!--  БЛОК который появляется после нажатия кнопки -->
        <div id="OnLock_textLockPane" class="LockOn">
             Обрабатываем запрос.........
        </div> 
     

  </td>

 
</tbody>
</table>

</div>

HTML;
    //********************************** Таблица с товарами ****************************** */
    echo <<<HTML
	<div class = "table-wrapper">
		
	<table class = "fl-table">
		<thead>
			<tr>
				<th>пп</th>
				<th>Артикул</th>
				<th>Количество</th>
				<th>цена</th>
			</tr>
		</thead>
	<tbody>
	HTML;


    $i = 1;
    foreach ($sum_arr_article as $key => $item) {
      //  print_r($item);
      echo "<tr>";
      echo "<td>$i</td>
	<td>" . $key . "</td>
	<td><input name = \"$key\" type=\"number\" step=\"1\" min=\"0\" max =\"{$item['count']}\" value=\"{$item['count']}\"></td>
	<td>" . $item['price'] . "</td>";


      $i++;
    }
    echo "</tbody></table></div>";
    echo "</form> ";


    //******************************************************************************* */
    unset($raw_arr_orders);
    unset($new_arr_orders);
    unset($sum_arr_article);
  } else {
    echo <<<HTML

  <div class = "table-wrapper">
  <table class = "fl-table ">
     <thead>
        <tr>
          <th colspan ="3" >$name_market</th>
    </tr>
     </thead>
    <tbody>
      <td colspan ="3" ><b>НЕТ ЗАКАЗОВ</b></td>
    </tbody>
    </table>
  </div>
  
HTML;
  }
}



function get_name_item_by_sku($pdo, $sku)
{
  $table_nomenklatura  = 'wb_22223222';
  $stmt = $pdo->prepare("SELECT `mp_name` FROM '$table_nomenklatura' WHERE `sku` = '$sku'");
  $stmt->$pdo->execute([]);
  $item_name =  $stmt->fetchALL(PDO::FETCH_COLUMN);
  if (isset($item_name[0])) {
    return $item_name[0];
  } else {
    return false;
  }
}
